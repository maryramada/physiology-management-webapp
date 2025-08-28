<?php
include 'layouts/config.php';
include 'helpers/functions.php';
include 'layouts/header.php';
include 'navegacao.php';

// Iniciar sessão se ainda não tiver sido iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$erro = '';
$resultados = [];

try {
    // Estabelecer ligação com a base de dados
    $ligacao = new PDO("mysql:host=" . MYSQL_HOST . ";dbname=" . MYSQL_DATABASE . ";charset=utf8", MYSQL_USERNAME, MYSQL_PASSWORD);
    // Configurar modo de erro do PDO para exceções
    $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Verificar se as datas foram fornecidas
    if (isset($_GET['data_inicio']) && isset($_GET['data_fim']) && isset($_GET['fisiologista_id'])) {
        $data_inicio = $_GET['data_inicio'];
        $data_fim = $_GET['data_fim'];
        $fisiologista_id = $_GET['fisiologista_id'];

        // Execução da query com filtro de datas e ID do fisiologista
        $stmt = $ligacao->prepare("
            SELECT 
                customers.id_customers, 
                customers.first_name, 
                customers.last_name, 
                customers.data_avaliacao AS data_avaliacao, 
                customers.horario AS horario,
                customers.renovacao AS is_renovation,
                NULL AS date_time
            FROM 
                customers
            LEFT JOIN 
                assignments ON customers.id_customers = assignments.id_customer
            WHERE 
                customers.data_avaliacao BETWEEN :data_inicio AND :data_fim
                AND assignments.id_physiologist = :fisiologista_id

            UNION ALL

            SELECT 
                assessments.id_customers, 
                customers.first_name, 
                customers.last_name, 
                assessments.date_time AS data_avaliacao, 
                assessments.date_time AS horario,
                assessments.is_renovation, 
                assessments.date_time
            FROM 
                assessments
            LEFT JOIN 
                customers ON customers.id_customers = assessments.id_customers
            LEFT JOIN 
                assignments ON customers.id_customers = assignments.id_customer
            WHERE 
                assessments.date_time BETWEEN :data_inicio AND :data_fim
                AND assignments.id_physiologist = :fisiologista_id
        ");
        $stmt->bindParam(':data_inicio', $data_inicio);
        $stmt->bindParam(':data_fim', $data_fim);
        $stmt->bindParam(':fisiologista_id', $fisiologista_id);
        $stmt->execute();
        $resultados = $stmt->fetchAll(PDO::FETCH_OBJ);
    } else {
        // Mensagem de erro caso as datas ou o ID do fisiologista não sejam fornecidos
        $erro = "Datas e ID do fisiologista são obrigatórios.";
    }

    // Processar a exclusão se o ID for recebido via POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
        $id_avaliacao = $_POST['id'];

        try {
            // Query para atualizar os campos na tabela customers
            $stmt_delete = $ligacao->prepare("
                UPDATE customers
                SET data_avaliacao = NULL, horario = NULL, renovacao = NULL
                WHERE id_customers = :id_avaliacao
            ");
            $stmt_delete->bindParam(':id_avaliacao', $id_avaliacao);
            $stmt_delete->execute();

            // Verificar se a atualização foi bem-sucedida
            $exclusao_sucesso = $stmt_delete->rowCount() > 0;

            // Responder ao JavaScript com um JSON indicando o sucesso da operação
            echo json_encode(['success' => $exclusao_sucesso]);
            exit;
        } catch (PDOException $err) {
            // Em caso de erro, responder com um JSON indicando falha e mensagem de erro
            echo json_encode(['success' => false, 'message' => 'Erro ao eliminar: ' . $err->getMessage()]);
            exit;
        }
    }
} catch (PDOException $err) {
    $erro = "Aconteceu um erro na ligação: " . $err->getMessage();
}

// Fechar a ligação
$ligacao = null;
?>

<!-- Conteúdo principal -->
<div class="container mt-5">
    <h2>Avaliações</h2>
    <br>
    <?php if ($erro): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($erro) ?>
        </div>
    <?php endif; ?>
    <!-- Barra de pesquisa -->
    <div class="row">
        <div class="col-md-12">
            <input type="text" id="searchInput" class="form-control" placeholder="Pesquisar...">
        </div>
    </div>
    <br>
    <!-- Tabela de Avaliações -->
    <table class="table results-table mt-3">
        <thead>
            <tr>
                <th>Nome do Cliente</th>
                <th>Data de Avaliação</th>
                <th>Horário</th>
                <th>Renovação?</th>
                <th>Eliminar</th>
            </tr>
        </thead>
        <tbody id="tableBody">
            <?php foreach ($resultados as $resultado): ?>
                <tr data-id="<?= htmlspecialchars($resultado->id_customers) ?>">
                    <td><?= htmlspecialchars($resultado->first_name) ?> <?= htmlspecialchars($resultado->last_name) ?></td>
                    <td><?= htmlspecialchars($resultado->data_avaliacao ? date('Y-m-d', strtotime($resultado->data_avaliacao)) : '') ?></td>
                    <td><?= htmlspecialchars($resultado->horario ? date('H:i:s', strtotime($resultado->horario)) : '') ?></td>
                    <td><?= htmlspecialchars($resultado->is_renovation ? 'Sim' : 'Não') ?></td>
                    <td>
                        <img src="assets/imagens/caixote.png" alt="Ícone Caixote do Lixo" class="trash-icon" onclick="excluirLinha(this)">
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($resultados)): ?>
                <tr>
                    <td colspan="5">Nenhum resultado encontrado.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <!-- Número total de clientes na tabela -->
    <p class="total-clientes">Total de Clientes: <?= count($resultados) ?></p>
    <!-- Botões -->
    <br><br>
    <div class="button-container-consultas">
        <button class="btn btn-primary" onclick="exportToXLSX()">Exportar para XLSX</button>
        <a href="novaavaliacao0.php" class="btn btn-primary">Nova Avaliação</a>
        <a href="renovacao.php" class="btn btn-primary">Renovação</a>
        <!-- Novo botão "Sair" -->
        <a href="avaliação.php" class="btn btn-primary sair-button">Sair</a>
    </div>
</div>
<br><br>
<!-- Modal de Confirmação de Exclusão -->
<div class="modal fade" id="confirmarExclusao" tabindex="-1" role="dialog" aria-labelledby="confirmarExclusaoLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmarExclusaoLabel">Confirmar eliminação</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Tem certeza de que deseja eliminar esta avaliação?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" onclick="excluirLinhaConfirmada()">Confirmar</button>
            </div>
        </div>
    </div>
</div>

<?php
include 'rodape.php';
include 'layouts/footer.php';
?>

<!-- Scripts do Bootstrap -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdn.sheetjs.com/xlsx-0.16.9/package/dist/xlsx.full.min.js"></script>
<script>
    // Função para filtrar dinamicamente a tabela conforme o usuário digita
   // Função para filtrar dinamicamente a tabela conforme o usuário digita
$(document).ready(function() {
    $("#searchInput").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("#tableBody tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });
});

// Função para exportar para XLSX
function exportToXLSX() {
    const table = document.querySelector(".results-table");
    const workbook = XLSX.utils.table_to_book(table, { sheet: "Sheet1" });
    XLSX.writeFile(workbook, "avaliacoes.xlsx");
}

function excluirLinha(element) {
    const id = element.closest('tr').getAttribute('data-id');
    document.getElementById('confirmarExclusao').setAttribute('data-id', id);
    $('#confirmarExclusao').modal('show');
}

// Função para confirmar a exclusão
function excluirLinhaConfirmada() {
    const id = document.getElementById('confirmarExclusao').getAttribute('data-id');
    $('#confirmarExclusao').modal('hide');

    // Fazer a requisição AJAX para excluir a avaliação
    $.ajax({
        url: 'consultas.php',
        method: 'POST',
        data: { id: id },
        success: function(response) {
            const res = JSON.parse(response);
            if (res.success) {
                // Remover a linha da tabela
                const linhaRemover = document.querySelector(`tr[data-id="${id}"]`);
                if (linhaRemover) {
                    linhaRemover.remove();
                    // Atualizar o total de clientes
                    const totalClientesElem = document.querySelector('.total-clientes');
                    const totalClientes = parseInt(totalClientesElem.textContent.split(':')[1].trim()) - 1;
                    totalClientesElem.textContent = 'Total de Clientes: ' + totalClientes;
                } else {
                    alert('Erro ao encontrar a linha para remoção.');
                }
            } else {
                alert('Erro ao eliminar a avaliação: ' + res.message);
            }
        },
        error: function(xhr, status, error) {
            alert('Erro ao comunicar com o servidor.');
            console.error(error);
        }
    });
}

// Preencher automaticamente o campo do nome do utilizador
document.getElementById('fisiologista').value = localStorage.getItem('username');

// Preencher nome do utilizador
document.getElementById('username-display').textContent = localStorage.getItem('username') || 'Utilizador';

function logout() {
    localStorage.removeItem('username');
    window.location.href = 'clientes.html';
}

</script>

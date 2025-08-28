<?php
session_start(); // Iniciar sessão para acessar as variáveis de sessão
include 'layouts/config.php';
include 'helpers/functions.php';
include 'layouts/header.php';
include 'navegacao.php';
?>
<!-- Conteúdo principal -->
<div class="container mt-5">
    <h2>Histórico de Avaliações</h2>
    <br>
    <div class="form-group">
        <label for="cliente">Selecionar Cliente:</label>
        <select id="cliente" name="cliente" class="select-client" onchange="carregarAvaliacoes()">
            <option value="">Escolha o nome do cliente que deseja consultar</option> <!-- Opção padrão -->
            <?php
            try {
                // Conectar ao banco de dados
                $conn = new PDO("mysql:host=" . MYSQL_HOST . ";dbname=" . MYSQL_DATABASE . ";charset=utf8", MYSQL_USERNAME, MYSQL_PASSWORD);
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                // Preparar a consulta SQL para buscar clientes do fisiologista logado
                $stmt = $conn->prepare("SELECT c.id_customers, c.first_name, c.last_name 
                                       FROM customers c
                                       INNER JOIN assignments a ON c.id_customers = a.id_customer
                                       WHERE a.id_physiologist = :physiologist_id");
                $physiologist_id = $_SESSION['userId']; // ID do fisiologista logado
                $stmt->bindParam(':physiologist_id', $physiologist_id, PDO::PARAM_INT);
                $stmt->execute();
                $clientes = $stmt->fetchAll(PDO::FETCH_OBJ);

                foreach ($clientes as $cliente) {
                    echo "<option value=\"{$cliente->id_customers}\">{$cliente->first_name} {$cliente->last_name}</option>";
                }
            } catch (PDOException $err) {
                echo "<option value=\"\">Erro ao carregar clientes</option>"; // Opção de erro
            } finally {
                $conn = null; // Fechar conexão com o banco de dados
            }
            ?>
        </select>
    </div>
    <br>
    <div id="avaliacoes-container">
        <!-- Avaliações serão preenchidas dinamicamente com PHP -->
    </div>
    <br>
    <button id="consultar-evolucao" class="btn btn-primary d-none" onclick="consultarEvolucao()">Consultar Evolução</button>
</div>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<?php
include 'rodape.php';
include 'layouts/footer.php';
?>
<script>
    function carregarAvaliacoes() {
        const clienteId = document.getElementById('cliente').value;
        if (clienteId) {
            fetch(`carregar_avaliacoes.php?cliente_id=${clienteId}`)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('avaliacoes-container').innerHTML = data;
                    // Mostrar o botão de consultar evolução após carregar as avaliações
                    document.getElementById('consultar-evolucao').classList.remove('d-none');
                })
                .catch(error => console.error('Erro ao carregar avaliações:', error));
        } else {
            document.getElementById('avaliacoes-container').innerHTML = '';
            // Esconder o botão de consultar evolução se nenhum cliente estiver selecionado
            document.getElementById('consultar-evolucao').classList.add('d-none');
        }
    }

    function consultarEvolucao() {
        const clienteId = document.getElementById('cliente').value;
        if (clienteId) {
            window.location.href = `evolucao.php?cliente_id=${clienteId}`;
        }
    }

    // Preencher nome do utilizador
    document.getElementById('username-display').textContent = localStorage.getItem('username') || 'Utilizador';

    function logout() {
        localStorage.removeItem('username');
        window.location.href = 'clientes.html';
    }
</script>

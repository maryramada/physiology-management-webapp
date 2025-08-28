<?php
session_start(); // Iniciar a sessão para acessar as variáveis de sessão
include 'layouts/config.php';
include 'helpers/functions.php';
include 'layouts/header.php';
include 'navegacao.php';

$validation_errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verificar se data_inicio e data_fim foram enviadas
    if (empty($_POST['data_inicio']) || empty($_POST['data_fim'])) {
        $validation_errors[] = 'Por favor, selecione as datas.';
    } else {
        // Validar se data_inicio é anterior a data_fim
        $data_inicio = $_POST['data_inicio'];
        $data_fim = $_POST['data_fim'];

        if (strtotime($data_inicio) >= strtotime($data_fim)) {
            $validation_errors[] = 'A data de início deve ser anterior à data de fim.';
        }
    }

    // Se houver erros, exibir mensagens
    if (!empty($validation_errors)) {
        foreach ($validation_errors as $error) {
            echo '<div class="alert alert-danger">' . $error . '</div>';
        }
    } 
}
?>

<!-- Conteúdo principal -->
<div class="container mt-5">
    <h2>Selecione as datas que deseja consultar.</h2>
    <br>
    <form id="listarAvaliacoesForm" onsubmit="return validarFormulario()" action="consultas.php" method="get">
        <div class="form-group">
            <label for="data_inicio">Data de Início:</label>
            <input type="date" id="data_inicio" name="data_inicio" class="form-control">
        </div>
        <div class="form-group">
            <label for="data_fim">Data de Fim:</label>
            <input type="date" id="data_fim" name="data_fim" class="form-control">
        </div>
        <!-- Campo oculto para armazenar o ID do fisiologista -->
        <input type="hidden" id="fisiologista_id" name="fisiologista_id" value="<?= $_SESSION['userId'] ?>">
        <div class="form-group">
            <label for="fisiologista">Nome do Fisiologista:</label>
            <input type="text" id="fisiologista" name="fisiologista" class="form-control" value="<?= $_SESSION['username'] ?>" readonly>
        </div>
        <div class="button-container">
            <button type="submit" class="btn btn-primary">Consultar</button>
        </div>
    </form>
</div>
<!-- Rodapé -->
<?php
include 'rodape.php';
include 'layouts/footer.php';
?>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    // Função para validar o formulário antes de enviar
    function validarFormulario() {
        const dataInicio = document.getElementById('data_inicio').value;
        const dataFim = document.getElementById('data_fim').value;

        if (!dataInicio || !dataFim) {
            alert('Por favor, selecione as datas.');
            return false; // Impede o envio do formulário
        }

        if (dataInicio > dataFim) {
            alert('A data de início deve ser anterior à data de fim.');
            return false; // Impede o envio do formulário
        }

        return true; // Permite o envio do formulário
    }

    // Preencher nome do utilizador
    document.getElementById('username-display').textContent = localStorage.getItem('username') || 'Utilizador';

    function logout() {
        localStorage.removeItem('username');
        window.location.href = 'clientes.html';
    }
</script>

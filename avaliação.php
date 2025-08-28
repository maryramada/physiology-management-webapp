<?php
include 'layouts/config.php';
include 'helpers/functions.php';
include 'layouts/header.php';
include 'navegacao.php';

session_start(); // Inicia a sessão (se já não estiver iniciada)
require_once('conexao.php'); // Arquivo com dados de conexão ao banco de dados

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Valida o login
    $id_physiologist = validarLogin($username, $password);

    if ($id_physiologist !== false) {
        // Login válido, define a sessão
        $_SESSION['id_physiologist'] = $id_physiologist;

        // Redireciona para a página restrita
        header("Location: avaliação.php");
        exit();
    } else {
        // Login inválido, redireciona de volta para o login com mensagem de erro
        header("Location: clientes.html?erro=1");
        exit();
    }
}

// Se a sessão já estiver iniciada e o utilizador já estiver autenticado, exibe o conteúdo da página restrita
if (isset($_SESSION['id_physiologist'])) {
    // Obter informações do utilizador
    $id_physiologist = $_SESSION['id_physiologist'];

    // Consulta para obter o nome do fisiologista
    $sql = "SELECT name FROM physiologist WHERE id_physiologist = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_physiologist);
    $stmt->execute();
    $stmt->bind_result($username);

    if ($stmt->fetch()) {
        // Nome do utilizador encontrado na base de dados
        $_SESSION['username'] = $username;
    } else {
        // Caso não encontre, redireciona para o login
        header("Location: clientes.html");
        exit();
    }

    $stmt->close();
}
?>
<div class="container mt-5 avaliacoes-container">
    <div class="user-info">
        <div class="welcome-message" id="welcome-message-text"></div>
    </div>
    <div class="avaliacoes-caixas-container">
        <div class="avaliacoes-caixa" id="nova-avaliacao">
            <img src="assets/imagens/novaavaliacao.png" alt="Nova Avaliação Física" class="avaliacoes-imagem">
            <h3><strong>Nova Avaliação <br> Física</strong></h3>
        </div>
        <div class="avaliacoes-caixa" id="renovacao-avaliacao">
            <img src="assets/imagens/renovacao.png" alt="Renovação Avaliação Física" class="avaliacoes-imagem">
            <h3><strong>Renovação Avaliação Física</strong></h3>
        </div>
        <div class="avaliacoes-caixa" id="avaliacoes-diarias">
            <img src="assets/imagens/avaliacaodiaria.png" alt="Avaliações Diárias" class="avaliacoes-imagem">
            <h3><strong>Avaliações Diárias</strong></h3>
        </div>
        <div class="avaliacoes-caixa" id="historico-avaliacoes">
            <img src="assets/imagens/historico.png" alt="Histórico de Avaliações" class="avaliacoes-imagem">
            <h3><strong>Histórico de Avaliações</strong></h3>
        </div>
    </div>
</div>
<?php
include 'rodape.php';
include 'layouts/footer.php';
?>
<script>
    document.getElementById('nova-avaliacao').onclick = function() {
        window.location.href = 'novaavaliacao0.php';
    };
    document.getElementById('renovacao-avaliacao').onclick = function() {
        window.location.href = 'renovacao.php';
    };
    document.getElementById('avaliacoes-diarias').onclick = function() {
        window.location.href = 'listaravaliacao.php';
    };
    document.getElementById('historico-avaliacoes').onclick = function() {
        console.log('Clicou no histórico de avaliações');
        window.location.href = 'historico.php';
    };

    // Mostrar mensagem de boas-vindas
    const username = "<?php echo isset($_SESSION['username']) ? $_SESSION['username'] : ''; ?>";
    if (username) {
        document.getElementById('welcome-message-text').innerText = `Bem-vindo, ${username}!`;
        document.getElementById('username-display').innerText = username;
    }
</script>

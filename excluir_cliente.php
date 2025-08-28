<?php
include 'layouts/config.php';
include 'helpers/functions.php';

// Definir cabeçalho para resposta JSON
header('Content-Type: application/json');

// Verificar se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Decodificar o JSON recebido do corpo da requisição
    $input = json_decode(file_get_contents('php://input'), true);
    $avaliacaoId = $input['id'] ?? null;

    // Verificar se o ID da avaliação foi fornecido
    if ($avaliacaoId) {
        try {
            // Estabelecer ligação com o banco de dados
            $ligacao = new PDO("mysql:host=" . MYSQL_HOST . ";dbname=" . MYSQL_DATABASE . ";charset=utf8", MYSQL_USERNAME, MYSQL_PASSWORD);
            $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Preparar e executar a query para excluir a avaliação
            $stmt = $ligacao->prepare("DELETE FROM assessments WHERE id_customers = :id");
            $stmt->bindParam(':id', $avaliacaoId, PDO::PARAM_INT);
            $stmt->execute();

            // Verificar se a exclusão foi bem-sucedida
            $success = $stmt->rowCount() > 0;

            // Responder com um JSON indicando o sucesso da operação
            echo json_encode(['success' => $success]);
        } catch (PDOException $err) {
            // Em caso de erro, responder com um JSON indicando falha e mensagem de erro
            echo json_encode(['success' => false, 'message' => $err->getMessage()]);
        }
    } else {
        // Se o ID da avaliação não foi fornecido, responder com um JSON indicando falha e mensagem apropriada
        echo json_encode(['success' => false, 'message' => 'ID da avaliação não fornecido.']);
    }
} else {
    // Se não for uma requisição POST, responder com um JSON indicando método de solicitação inválido
    echo json_encode(['success' => false, 'message' => 'Método de solicitação inválido.']);
}
?>


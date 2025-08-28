<?php
session_start();
include('layouts/config.php');

header('Content-Type: application/json');
//var_dump($_SESSION);
//echo json_encode(['success' => true]);

$data = json_decode(file_get_contents('php://input'), true);
$currentPassword = $data['currentPassword'];
$newPassword = $data['newPassword'];
$userId = $_SESSION['userId'];  // Obtém o ID do fisiologista do corpo da requisição

// Conexão com o banco de dados utilizando as constantes definidas em config.php
$conn = new mysqli(MYSQL_HOST, MYSQL_USERNAME, MYSQL_PASSWORD, MYSQL_DATABASE);

// Verificar se a senha atual está correta
$sql = "SELECT * FROM physiologist WHERE id_physiologist = ? AND password = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('is', $userId, $currentPassword);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    // Atualizar a senha
    $sql_update = "UPDATE physiologist SET password = ? WHERE id_physiologist = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param('si', $newPassword, $userId);
    
    if ($stmt_update->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'incorrect_password']);
}

$stmt->close();
$stmt_update->close();
?>

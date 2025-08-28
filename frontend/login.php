<?php
session_start();
include('layouts/config.php'); 

// Conexão com o banco de dados utilizando as constantes definidas em config.php
$conn = new mysqli(MYSQL_HOST, MYSQL_USERNAME, MYSQL_PASSWORD, MYSQL_DATABASE);

// Verificar conexão
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

// Defina o cabeçalho do tipo de conteúdo para JSON
header('Content-Type: application/json');

// Receber dados do formulário via JSON
$data = json_decode(file_get_contents('php://input'), true);
$username = $data['username'];
$password = $data['password'];
$modulo = $data['modulo'];

// Consulta para verificar as credenciais do usuário
$sql = "SELECT id_physiologist AS id, first_name as name FROM physiologist WHERE first_name = ? AND password = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ss', $username, $password);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Usuário encontrado, iniciar sessão
    $user = $result->fetch_assoc();
    $_SESSION['username'] = $user['name'];
    $_SESSION['userId'] = $user['id'];
    echo json_encode(['success' => true, 'userId' => $user['id'], 'username' => $user['name']]);
    //var_dump($_SESSION);
} else {
    // Usuário não encontrado ou senha incorreta
    echo json_encode(['success' => false, 'message' => 'Utilizador não encontrado ou senha incorreta.']);
}

$stmt->close();
$conn->close();
//session_destroy();
?>

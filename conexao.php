<?php
require_once 'layouts/config.php'; // Inclui o arquivo de configuração com as constantes

// Conexão com o banco de dados
$conn = new mysqli(MYSQL_HOST, MYSQL_USERNAME, MYSQL_PASSWORD, MYSQL_DATABASE);

// Verifica a conexão
if ($conn->connect_error) {
    die("Falha na conexão com o banco de dados: " . $conn->connect_error);
}

// Função para validar o login
function validarLogin($username, $password) {
    global $conn;

    // Prepara a consulta SQL
    $sql = "SELECT id_physiologist FROM physiologist WHERE name = ? AND password = ?";

    // Prepara a declaração SQL
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        return false; // Erro na preparação da consulta
    }

    // Faz o bind dos parâmetros
    $stmt->bind_param('ss', $username, $password);

    // Executa a consulta
    $stmt->execute();

    // Armazena o resultado da consulta
    $stmt->store_result();

    // Verifica se encontrou um fisiologista com o usuário e senha fornecidos
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id_physiologist);
        $stmt->fetch();
        return $id_physiologist; // Retorna o ID do fisiologista
    } else {
        return false; // Login inválido
    }

    // Fecha a declaração
    $stmt->close();
}
?>

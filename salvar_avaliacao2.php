<?php

include 'layouts/config.php';
include 'helpers/functions.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

// Verifica se foi uma requisição POST tradicional
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Conexão com o banco de dados
    $conn = new mysqli(MYSQL_HOST, MYSQL_USERNAME, MYSQL_PASSWORD, MYSQL_DATABASE);

    if ($conn->connect_error) {
        die(json_encode(['success' => false, 'message' => 'Connection failed: ' . $conn->connect_error]));
    }

    // Iniciar transação
    $conn->begin_transaction();
    try {
        
        $id_customers = $data['id_customer'];
        $stmt_assessment_hgt = $conn->prepare('INSERT INTO assessment_hgt (id_customers, grip_strength_values, considerations) VALUES (?, ?, ?)');
        $stmt_assessment_hgt->bind_param(
            'iss',
            $id_customers,
            $data['grip_strength_values'],
            $data['considerations']
        );

        if ($stmt_assessment_hgt->execute()) {
            $success_assessment_hgt = true;
        } else {
            $success_assessment_hgt = false;
        }

        $stmt_assessment_hgt->close();

        // Verificar se todas as operações foram bem sucedidas
        //if ($success_customer && $success_first_serie && $success_second_serie_quality && $success_second_serie_resistance && $success_second_serie_load && $success_assessment_hgt) {
        if ($success_assessment_hgt) {
            // Finaliza a transação se todas as operações foram bem-sucedidas
            $conn->commit();
            echo json_encode(['success' => true, 'id_customers' => $id_customers]);
        } else {
            // Desfaz a transação se houve algum erro em alguma operação
            $conn->rollback();
            echo json_encode(['success' => false, 'message' => 'Failed to insert data']);
        }
    } catch (Exception $e) {
        // Em caso de exceção, desfaz a transação e retorna uma mensagem de erro
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => 'Exception occurred: ' . $e->getMessage()]);
    }

    // Fechar a conexão com o banco de dados
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

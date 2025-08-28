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
        $stmt_first_serie = $conn->prepare('INSERT INTO assessement_first_serie (id_customers, pull_down_disponibilidade, pull_down_consideracoes, leg_extension_disponibilidade, leg_extension_consideracoes, chest_press_disponibilidade, chest_press_consideracoes, leg_press_disponibilidade, leg_press_consideracoes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt_first_serie->bind_param(
            'issssssss',
            $id_customers,
            $data['pullDownDisponibilidade'],
            $data['pullDownConsideracoes'],
            $data['legExtensionDisponibilidade'],
            $data['legExtensionConsideracoes'],
            $data['chestPressDisponibilidade'],
            $data['chestPressConsideracoes'],
            $data['legPressDisponibilidade'],
            $data['legPressConsideracoes']
        );

        if ($stmt_first_serie->execute()) {
            $success_first_serie = true;
        } else {
            $success_first_serie = false;
        }

        $stmt_first_serie->close();

        // Verificar se todas as operações foram bem sucedidas
        //if ($success_customer && $success_first_serie && $success_second_serie_quality && $success_second_serie_resistance && $success_second_serie_load && $success_assessment_hgt) {
        if ($success_first_serie) {
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

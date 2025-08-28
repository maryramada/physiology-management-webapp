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
        $stmt_second_serie_quality = $conn->prepare('INSERT INTO assessment_second_serie_constraction_quality (id_customers, p1_pull_down, p1_leg_extension, p1_chest_press, p1_leg_press, p2_pull_down, p2_leg_extension, p2_chest_press, p2_leg_press, p3_pull_down, p3_leg_extension, p3_chest_press, p3_leg_press) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt_second_serie_quality->bind_param(
            'issssssssssss',
            $id_customers,
            $data['assessment_second_serie_constraction_quality']['p1_pull_down'],
            $data['assessment_second_serie_constraction_quality']['p1_leg_extension'],
            $data['assessment_second_serie_constraction_quality']['p1_chest_press'],
            $data['assessment_second_serie_constraction_quality']['p1_leg_press'],
            $data['assessment_second_serie_constraction_quality']['p2_pull_down'],
            $data['assessment_second_serie_constraction_quality']['p2_leg_extension'],
            $data['assessment_second_serie_constraction_quality']['p2_chest_press'],
            $data['assessment_second_serie_constraction_quality']['p2_leg_press'],
            $data['assessment_second_serie_constraction_quality']['p3_pull_down'],
            $data['assessment_second_serie_constraction_quality']['p3_leg_extension'],
            $data['assessment_second_serie_constraction_quality']['p3_chest_press'],
            $data['assessment_second_serie_constraction_quality']['p3_leg_press']
        );

        if ($stmt_second_serie_quality->execute()) {
            $success_second_serie_quality = true;
        } else {
            $success_second_serie_quality = false;
        }

        $stmt_second_serie_quality->close();

        // Segunda série de avaliação - resistência muscular
        $stmt_second_serie_resistance = $conn->prepare('INSERT INTO assessment_second_serie_muscular_resistance (id_customers, p1_pull_down, p1_leg_extension, p1_chest_press, p1_leg_press, p2_pull_down, p2_leg_extension, p2_chest_press, p2_leg_press, p3_pull_down, p3_leg_extension, p3_chest_press, p3_leg_press) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt_second_serie_resistance->bind_param(
            'issssssssssss',
            $id_customers,
            $data['assessment_second_serie_muscular_resistance']['p1_pull_down'],
            $data['assessment_second_serie_muscular_resistance']['p1_leg_extension'],
            $data['assessment_second_serie_muscular_resistance']['p1_chest_press'],
            $data['assessment_second_serie_muscular_resistance']['p1_leg_press'],
            $data['assessment_second_serie_muscular_resistance']['p2_pull_down'],
            $data['assessment_second_serie_muscular_resistance']['p2_leg_extension'],
            $data['assessment_second_serie_muscular_resistance']['p2_chest_press'],
            $data['assessment_second_serie_muscular_resistance']['p2_leg_press'],
            $data['assessment_second_serie_muscular_resistance']['p3_pull_down'],
            $data['assessment_second_serie_muscular_resistance']['p3_leg_extension'],
            $data['assessment_second_serie_muscular_resistance']['p3_chest_press'],
            $data['assessment_second_serie_muscular_resistance']['p3_leg_press']
        );

        if ($stmt_second_serie_resistance->execute()) {
            $success_second_serie_resistance = true;
        } else {
            $success_second_serie_resistance = false;
        }

        $stmt_second_serie_resistance->close();

        // Segunda série de avaliação - registro de carga
        $stmt_second_serie_load = $conn->prepare('INSERT INTO assessment_second_serie_load_register (id_customers, subjective_effort_perspective, scale_of_feeling) VALUES (?, ?, ?)');
        $stmt_second_serie_load->bind_param(
            'iss',
            $id_customers,
            $data['assessment_second_serie_load_register']['subjective_effort_perspective'],
            $data['assessment_second_serie_load_register']['scale_of_feeling']
        );

        if ($stmt_second_serie_load->execute()) {
            $success_second_serie_load = true;
        } else {
            $success_second_serie_load = false;
        }

        $stmt_second_serie_load->close();

        // Verificar se todas as operações foram bem sucedidas
        //if ($success_customer && $success_first_serie && $success_second_serie_quality && $success_second_serie_resistance && $success_second_serie_load && $success_assessment_hgt) {
        if ($success_second_serie_quality && $success_second_serie_resistance && $success_second_serie_load) {
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

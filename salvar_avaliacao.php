<?php
session_start();

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
        // Inserir dados do cliente na tabela customers
        $stmt = $conn->prepare('INSERT INTO customers (first_name, last_name, email, gender, date_of_birth, data_avaliacao, horario, renovacao) VALUES (?, ?, ?, ?, ?, ?, ?, "não")');
        $stmt->bind_param(
            'sssssss',
            $data['first-name'],
            $data['last-name'],
            $data['email'],
            $data['gender'],
            $data['dob'],
            $data['data_avaliacao'],
            $data['horario']
        );

        if ($stmt->execute()) {
            // Obtém o id_customers gerado
            $id_customers = $stmt->insert_id;
            $stmt->close();

            // Inserir registro na tabela assignments
            $physiologist_id = $_SESSION['userId']; // ID do fisiologista logado
            $current_datetime = date('Y-m-d H:i:s');
            $stmt_assign = $conn->prepare('INSERT INTO assignments (id_customer, id_physiologist, date_time) VALUES (?, ?, ?)');
            $stmt_assign->bind_param('iis', $id_customers, $physiologist_id, $current_datetime);

            if ($stmt_assign->execute()) {
                $success_assignments = true;
                $stmt_assign->close();
            } else {
                $success_assignments = false;
            }

            // Verifica se ambos os inserts foram bem-sucedidos
            if ($success_assignments) {
                $conn->commit();
                echo json_encode(['success' => true, 'id_customers' => $id_customers]);
            } else {
                throw new Exception('Falha ao atribuir cliente ao fisiologista.');
            }
        } else {
            $conn->rollback();
            echo json_encode(['success' => false, 'message' => 'Falha ao salvar dados do cliente.']);
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
?>

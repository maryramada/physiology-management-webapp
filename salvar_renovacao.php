<?php
include 'layouts/config.php';
include 'helpers/functions.php';

// Criar conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Capturar dados do formulário
$customer_name = $_POST['nome-cliente'];
$date = $_POST['data'];
$a1_resultado = $_POST['a1-resultado'];
$a1_consideracoes = $_POST['a1-consideracoes'];
$a2_resultado = $_POST['a2-resultado'];
$a2_consideracoes = $_POST['a2-consideracoes'];
$a3_resultado = $_POST['a3-resultado'];
$a3_consideracoes = $_POST['a3-consideracoes'];
$a4_distancia = $_POST['a4-distancia'];
$a4_vo2 = $_POST['a4-vo2'];
$a4_fc_repouso = $_POST['a4-fc-repouso'];
$a4_fc_final = $_POST['a4-fc-final'];
$a4_fc_1min = $_POST['a4-fc-1min'];
$a4_consideracoes = $_POST['a4-consideracoes'];

// Extrair id do cliente baseado no nome
list($first_name, $last_name) = explode(" ", $customer_name);
$sql = "SELECT id_customers FROM customers WHERE first_name='$first_name' AND last_name='$last_name'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$id_customers = $row['id_customers'];

// Inserir dados na tabela
$sql = "INSERT INTO assessment_m2r (test_a1_result, test_a1_considerations, test_a2_result, test_a2_considerations, test_a3_result, test_a3_considerations, test_a4_distance, test_a4_vo2max, test_a4_resting_heart_rate, teste_a4_end_heart_race, test_a4_heart_race_after_1second, test_a4_considerations, id_customers, data) 
VALUES ('$a1_resultado', '$a1_consideracoes', '$a2_resultado', '$a2_consideracoes', '$a3_resultado', '$a3_consideracoes', '$a4_distancia', '$a4_vo2', '$a4_fc_repouso', '$a4_fc_final', '$a4_fc_1min', '$a4_consideracoes', '$id_customers', '$date')";

if ($conn->query($sql) === TRUE) {
    echo "Dados salvos com sucesso";
} else {
    echo "Erro: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>

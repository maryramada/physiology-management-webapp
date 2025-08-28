<?php
include 'layouts/config.php';
include 'helpers/functions.php';

// Criar conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

$sql = "SELECT first_name, last_name FROM customers";
$result = $conn->query($sql);

$customers = array();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $customers[] = $row['first_name'] . " " . $row['last_name'];
    }
}
$conn->close();

echo json_encode($customers);
?>

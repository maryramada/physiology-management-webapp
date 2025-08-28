<?php
include 'layouts/config.php';

header('Content-Type: application/json');

if (isset($_GET['cliente_id'])) {
    $cliente_id = $_GET['cliente_id'];

    try {
        // Conectar ao banco de dados
        $conn = new PDO("mysql:host=" . MYSQL_HOST . ";dbname=" . MYSQL_DATABASE . ";charset=utf8", MYSQL_USERNAME, MYSQL_PASSWORD);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Consultar a evolução das distâncias do teste A4
        $stmt = $conn->prepare("SELECT assessment_date, test_a4_distance FROM assessment_m2r WHERE id_customers = :cliente_id ORDER BY assessment_date ASC");
        $stmt->bindParam(':cliente_id', $cliente_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $labels = [];
        $distancias = [];

        foreach ($result as $row) {
            $labels[] = $row['assessment_date'];
            $distancias[] = $row['test_a4_distance'];
        }

        echo json_encode(['labels' => $labels, 'distancias' => $distancias]);
    } catch (PDOException $err) {
        echo json_encode(['error' => $err->getMessage()]);
    } finally {
        $conn = null; // Fechar conexão com o banco de dados
    }
} else {
    echo json_encode(['error' => 'Cliente não especificado']);
}
?>

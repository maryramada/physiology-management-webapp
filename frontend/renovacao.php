<?php
session_start();
include 'layouts/config.php';
include 'helpers/functions.php';
include 'layouts/header.php';
include 'navegacao.php';

// Conectar ao banco de dados
$conn = new mysqli(MYSQL_HOST, MYSQL_USERNAME, MYSQL_PASSWORD, MYSQL_DATABASE);

// Verificar conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Variável para armazenar mensagens de feedback ao usuário
$message = '';

// Verificar se o fisiologista está autenticado
if (!isset($_SESSION['userId'])) {
    header("Location: login.php"); // Redirecionar se não estiver autenticado
    exit();
}

// Processar o formulário se for submetido via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Capturar e sanitizar os dados do formulário
    $id_customers = $_POST['id_customers'];
    $date = $_POST['data'];
    $time = $_POST['hora'];
    $dateTime = $date . ' ' . $time;
    $a1_resultado = $_POST['a1_resultado'];
    $a1_consideracoes = $_POST['a1_consideracoes'];
    $a2_resultado = $_POST['a2_resultado'];
    $a2_consideracoes = $_POST['a2_consideracoes'];
    $a3_resultado = $_POST['a3_resultado'];
    $a3_consideracoes = $_POST['a3_consideracoes'];
    $a4_distancia = $_POST['a4_distancia'];
    $a4_vo2 = $_POST['a4_vo2'];
    $a4_fc_repouso = $_POST['a4_fc_repouso'];
    $a4_fc_final = $_POST['a4_fc_final'];
    $a4_fc_1min = $_POST['a4_fc_1min'];
    $a4_consideracoes = $_POST['a4_consideracoes'];
    $id_physiologist = $_SESSION['userId']; // ID do fisiologista logado
    $is_renovation = 'sim'; // Definir is_renovation como 'sim'

    // Inserir os dados na tabela assessment_m2r
    $sql1 = "INSERT INTO assessment_m2r (test_a1_result, test_a1_considerations, test_a2_result, test_a2_considerations, test_a3_result, test_a3_considerations, test_a4_distance, test_a4_vo2max, test_a4_resting_heart_rate, teste_a4_end_heart_race, test_a4_heart_race_after_1second, test_a4_considerations, id_customers, assessment_date) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    // Preparar a declaração SQL
    $stmt1 = $conn->prepare($sql1);
    $stmt1->bind_param('ssssssssssssss', $a1_resultado, $a1_consideracoes, $a2_resultado, $a2_consideracoes, $a3_resultado, $a3_consideracoes, $a4_distancia, $a4_vo2, $a4_fc_repouso, $a4_fc_final, $a4_fc_1min, $a4_consideracoes, $id_customers, $dateTime);

    // Inserir os dados na tabela assessments
    $sql2 = "INSERT INTO assessments (id_customers, date_time, is_renovation, id_physiologist) 
    VALUES (?, ?, ?, ?)";

    // Preparar a declaração SQL
    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param('sssi', $id_customers, $dateTime, $is_renovation, $id_physiologist);

    // Executar as declarações preparadas e verificar o sucesso
    if ($stmt1->execute() && $stmt2->execute()) {
        $message = "Dados salvos com sucesso";
        // Redirecionamento após o envio do formulário
        header("Location: avaliação.php");
        exit();
    } else {
        $message = "Erro ao salvar os dados: " . $conn->error;
    }

    // Fechar declarações preparadas
    $stmt1->close();
    $stmt2->close();
}

// Buscar os clientes atribuídos ao fisiologista logado para preencher o select
$sql_customers = "SELECT c.id_customers, c.first_name, c.last_name 
                 FROM customers c
                 INNER JOIN assignments a ON c.id_customers = a.id_customer
                 WHERE a.id_physiologist = ?";
$stmt_customers = $conn->prepare($sql_customers);
$stmt_customers->bind_param('i', $_SESSION['userId']);
$stmt_customers->execute();
$result_customers = $stmt_customers->get_result();

$customers = array();
if ($result_customers->num_rows > 0) {
    while ($row = $result_customers->fetch_assoc()) {
        $customers[] = array(
            'id' => $row['id_customers'],
            'name' => $row['first_name'] . " " . $row['last_name']
        );
    }
}

// Incluir o layout do formulário aqui

// Fechar conexão com o banco de dados
$conn->close();
?>

<div class="container mt-5">
    <h2>Formulário M2R</h2>
    <br>
    <?php if (!empty($message)): ?>
        <div class="alert alert-info"><?php echo $message; ?></div>
    <?php endif; ?>
    <form id="assessment-form" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <div class="form-group">
        <label for="nome-cliente">Nome do Cliente:</label>
        <select id="nome-cliente" name="id_customers" class="form-control">
            <option value="">Selecione o Cliente</option>
            <?php foreach ($customers as $customer): ?>
                <option value="<?php echo $customer['id']; ?>"><?php echo $customer['name']; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label for="data">Data:</label>
        <input type="date" id="data" name="data" class="form-control">
    </div>
    <div class="form-group">
        <label for="hora">Hora:</label>
        <select id="hora" name="hora" class="form-control">
            <option value="">Selecione a Hora</option>
            <?php
            for ($h = 7; $h <= 21; $h++) {
                for ($m = 0; $m <= 30; $m += 30) {
                    $hora = sprintf('%02d:%02d:00', $h, $m);
                    echo "<option value='$hora'>$hora</option>";
                }
            }
            ?>
        </select>
    </div>
    <br>
    <h3>Testes a realizar</h3>
    <br>
    <ul>
        <li>A1 - 30´´ Sit & Stand Test</li>
        <li>A2 - 2´ Step Test</li>
        <li>A3 - 6´ Walking Test</li>
        <li>A4 – 12’ Cooper Test</li>
    </ul>
    <br>
    <div class="row">
        <div class="col-md-12">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Teste</th>
                        <th>Resultado</th>
                        <th>Considerações</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>A1</td>
                        <td><input type="text" name="a1_resultado" class="form-control"></td>
                        <td><input type="text" name="a1_consideracoes" class="form-control"></td>
                    </tr>
                    <tr>
                        <td>A2</td>
                        <td><input type="text" name="a2_resultado" class="form-control"></td>
                        <td><input type="text" name="a2_consideracoes" class="form-control"></td>
                    </tr>
                    <tr>
                        <td>A3</td>
                        <td><input type="text" name="a3_resultado" class="form-control"></td>
                        <td><input type="text" name="a3_consideracoes" class="form-control"></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-12">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th></th>
                        <th>Distância (metros)</th>
                        <th>VO2 máx.</th>
                        <th>FC repouso</th>
                        <th>FC final do teste</th>
                        <th>FC após 1'</th>
                        <th>Considerações</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>A4</td>
                        <td><input type="text" name="a4_distancia" class="form-control"></td>
                        <td><input type="text" name="a4_vo2" class="form-control"></td>
                        <td><input type="text" name="a4_fc_repouso" class="form-control"></td>
                        <td><input type="text" name="a4_fc_final" class="form-control"></td>
                        <td><input type="text" name="a4_fc_1min" class="form-control"></td>
                        <td><input type="text" name="a4_consideracoes" class="form-control"></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <br>
    <button type="submit" class="btn btn-primary">Enviar</button>
</form>
</div>
<?php
include 'rodape.php';
include 'layouts/footer.php';
?>
<script>
    document.getElementById('username-display').textContent = localStorage.getItem('username') || 'Utilizador';

function logout() {
    localStorage.removeItem('username');
    window.location.href = 'clientes.html';
}
</script>



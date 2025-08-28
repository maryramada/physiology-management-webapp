<?php
include 'layouts/config.php';
include 'helpers/functions.php';
include 'layouts/header.php';
include 'navegacao.php';

$validation_errors = [];
$server_error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verificações de validação
    $required_fields = [
        'first-name' => 'Primeiro Nome',
        'last-name' => 'Último Nome',
        'email' => 'Email',
        'gender' => 'Gênero',
        'dob' => 'Data de Nascimento',
        'horario' => 'Horário'
    ];

    // Verificar campos obrigatórios
    foreach ($required_fields as $field_id => $field_name) {
        if (empty(trim($_POST[$field_id]))) {
            $validation_errors[] = 'Por favor, preencha o campo "' . $field_name . '".';
        }
    }

    // Se houver erros de validação, exibir mensagens de erro
    if (!empty($validation_errors)) {
        foreach ($validation_errors as $error) {
            echo '<p>' . $error . '</p>';
        }
    } else {
        // Processar os dados e salvar no banco de dados
        $first_name = $_POST['first-name'];
        $last_name = $_POST['last-name'];
        $email = $_POST['email'];
        $gender = $_POST['gender'];
        $dob = $_POST['dob'];
        $horario = $_POST['horario'] . ':00';

    }
}
?>
<div class="container mt-5">
    <h2><strong>Dados do Cliente</strong></h2>
    <br>
    <form id="customer-form">
        <input type="hidden" id="customer-id" name="customer-id" value="">
        <div class="form-group">
            <label for="first-name">Primeiro Nome</label>
            <input type="text" id="first-name" name="first-name" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="last-name">Último Nome</label>
            <input type="text" id="last-name" name="last-name" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="gender">Gênero</label>
            <select id="gender" name="gender" class="form-control" required>
                <option value="" selected></option>
                <option value="Feminino">Feminino</option>
                <option value="Masculino">Masculino</option>
                <option value="Outro">Outro</option>
            </select>
        </div>
        <div class="form-group">
            <label for="dob">Data de Nascimento</label>
            <input type="date" id="dob" name="dob" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="horario">Horário</label>
            <input type="time" id="horario" name="horario" step="1800" class="form-control" required>
        </div>
    </form>
</div>
<br>
<div class="button-container">
    <button onclick="validateAndSave()">Página Seguinte</button>
    <button onclick="logout()">Sair</button>
</div>
<?php
include 'rodape.php';
include 'layouts/footer.php';
?>
<script>
    document.getElementById('username-display').textContent = localStorage.getItem('username') || 'Utilizador';

    function logout() {
        localStorage.removeItem('username');
        document.getElementById('customer-form').reset();
        window.location.href = 'avaliação.php';
    }

    function saveCustomerData(data) {
        data['data_avaliacao'] = new Date().toISOString().slice(0, 10);

        return fetch('salvar_avaliacao.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data),
            })
            .then(response => response.json())
            .then(responseData => {
                if (responseData.success) {
                    console.log('Dados do cliente salvos com sucesso!');
                    // Redirecionar apenas se os dados foram salvos com sucesso
                    window.location.href = 'novaavaliacao.php?id=' + responseData.id_customers;
                } else {
                    console.error('Erro ao salvar dados do cliente:', responseData.message);
                    alert('Erro ao salvar dados do cliente. Por favor, tente novamente.');
                }
            })
            .catch(error => {
                console.error('Erro ao salvar dados do cliente:', error);
                alert('Erro ao salvar dados do cliente. Por favor, tente novamente.');
            });
    }

    function validateAndSave() {
        const firstName = document.getElementById('first-name').value;
        const lastName = document.getElementById('last-name').value;
        const email = document.getElementById('email').value;
        const gender = document.getElementById('gender').value;
        const dob = document.getElementById('dob').value;
        const horario = document.getElementById('horario').value + ':00';

        if (!firstName || !lastName || !email || !gender || !dob || !horario) {
            alert('Por favor, preencha todos os campos obrigatórios.');
            return;
        }

        const formData = {
            'first-name': firstName,
            'last-name': lastName,
            'email': email,
            'gender': gender,
            'dob': dob,
            'horario': horario,
            'customer-id': document.getElementById('customer-id').value
        };

        saveCustomerData(formData);
    }
</script>
</body>

</html>
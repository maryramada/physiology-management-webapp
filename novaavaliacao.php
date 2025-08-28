<?php
include 'layouts/config.php';
include 'helpers/functions.php';
include 'layouts/header.php';
include 'navegacao.php';

$validation_errors = [];
$server_error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verificações de validação
    if (empty($_POST['grip_strength_values'])) {
        $validation_errors[] = 'Por favor, preencha o campo "Força de Preensão".';
    }

    if (empty($_POST['considerations'])) {
        $validation_errors[] = 'Por favor, preencha o campo "Considerações".';
    }

    // Se não houver erros de validação, processar os dados
    if (empty($validation_errors)) {
        try {
            // Processamento dos dados e salvamento no banco de dados
            $grip_strength_values = $_POST['grip_strength_values'];
            $considerations = $_POST['considerations'];
            // Continue com o processamento dos dados aqui...

            // Após o processamento bem-sucedido, redirecionar ou fazer qualquer ação necessária
            header('Location: novaavaliacao2.php?id=' . $_GET['id']);
            exit;
        } catch (Exception $e) {
            $server_error = 'Ocorreu um erro ao processar os dados: ' . $e->getMessage();
        }
    }
}
?>
<!-- Conteúdo principal -->
<div class="container mt-5">
    <h2><strong>Formulário M2P</strong></h2>
    <br>
    <h3>1. Hand Grip Test</h3>
    <br>
    <form id="formHGT">

        <table class="form-table">
            <tr>
                <th>Resultados</th>
                <th></th>
                <th>Valores de Referência</th>
            </tr>
            <tr>
                <td>Força de Preensão</td>
                <td>
                    <div class="input-group">
                        <input type="number" id="grip_strength_values" name="grip_strength_values" class="form-control text-center" style="padding-right: 40px;" oninput="updateConsiderations()">
                        <div class="input-group-append">
                            <span class="input-group-text">kg</span>
                        </div>
                    </div>
                </td>
                <td>43-45 kg</td>
            </tr>
            <tr>
                <td>Considerações</td>
                <td colspan="2">
                    <textarea class="considerations-input form-control" id="considerations" name="considerations"></textarea>
                </td>
            </tr>
        </table>
    </form>
</div>
<!-- Botões de navegação -->
<div class="button-container">
    <button onclick="window.location.href='novaavaliacao0.php'">Página Anterior</button>
    <button onclick="saveFormData()">Página Seguinte</button>
    <button onclick="goToAvaliacao()">Sair</button>
</div>
<?php
include 'rodape.php';
include 'layouts/footer.php';
?>
<!-- Seção do seu HTML até antes de fechar a tag </body> -->
<script>
    document.getElementById('username-display').textContent = localStorage.getItem('username') || 'Utilizador';

    function logout() {
        localStorage.removeItem('username');
        window.location.href = 'clientes.html';
    }

    function saveFormData() {
        const formData = new FormData(document.getElementById('formHGT'));
        const data = {
            id_customer: <?php echo $_GET['id']; ?>,
            grip_strength_values: formData.get('grip_strength_values'),
            considerations: formData.get('considerations')
        };

        fetch('salvar_avaliacao2.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data),
            })
            .then(response => {
                if (response.ok) {
                    console.log('Dados salvos com sucesso!');
                    // Redirecionar para a próxima página após salvar
                    window.location.href = 'novaavaliacao2.php?id=' + <?php echo $_GET['id']; ?>;
                } else {
                    console.error('Erro ao salvar dados:', response.statusText);
                    // Tratar erro aqui, se necessário
                }
            })
            .catch(error => {
                console.error('Erro ao salvar dados:', error);
                // Tratar erro aqui, se necessário
            });
    }

    function goToAvaliacao() {
        window.location.href = 'avaliação.php';
    }

    function updateConsiderations() {
        const gripStrength = parseFloat(document.getElementById('grip_strength_values').value);
        const considerationsField = document.getElementById('considerations');

        if (gripStrength < 43) {
            considerationsField.value = 'Abaixo do valor de referência';
        } else if (gripStrength > 45) {
            considerationsField.value = 'Acima do valor de referência';
        } else {
            considerationsField.value = 'Nos valores de referência';
        }
    }
</script>
</body>

</html>
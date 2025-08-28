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
        'pull-down-disponibilidade' => 'Pull Down - Disponibilidade NMA',
        'leg-extension-disponibilidade' => 'Leg Extension - Disponibilidade NMA',
        'chest-press-disponibilidade' => 'Chest Press - Disponibilidade NMA',
        'leg-press-disponibilidade' => 'Leg Press - Disponibilidade NMA'
    ];

    foreach ($required_fields as $field_id => $field_name) {
        if (empty(trim($_POST[$field_id]))) {
            $validation_errors[] = 'Por favor, preencha o campo "' . $field_name . '".';
        }
    }

    // Processamento dos dados e salvamento no banco de dados
    if (empty($validation_errors)) {
        try {
            $pullDownDisponibilidade = $_POST['pull-down-disponibilidade'];
            $legExtensionDisponibilidade = $_POST['leg-extension-disponibilidade'];
            $chestPressDisponibilidade = $_POST['chest-press-disponibilidade'];
            $legPressDisponibilidade = $_POST['leg-press-disponibilidade'];

            // Considerações adicionais
            $pullDownConsideracoes = isset($_POST['pull-down-consideracoes']) ? $_POST['pull-down-consideracoes'] : '';
            $legExtensionConsideracoes = isset($_POST['leg-extension-consideracoes']) ? $_POST['leg-extension-consideracoes'] : '';
            $chestPressConsideracoes = isset($_POST['chest-press-consideracoes']) ? $_POST['chest-press-consideracoes'] : '';
            $legPressConsideracoes = isset($_POST['leg-press-consideracoes']) ? $_POST['leg-press-consideracoes'] : '';

            // Verifica se as considerações estão vazias e define um valor padrão se necessário
            if (empty($pullDownConsideracoes)) {
                $pullDownConsideracoes = 'Sem considerações adicionais';
            }
            if (empty($legExtensionConsideracoes)) {
                $legExtensionConsideracoes = 'Sem considerações adicionais';
            }
            if (empty($chestPressConsideracoes)) {
                $chestPressConsideracoes = 'Sem considerações adicionais';
            }
            if (empty($legPressConsideracoes)) {
                $legPressConsideracoes = 'Sem considerações adicionais';
            }
            
            header('Location: novaavaliacao3.php?id=' . $_GET['id']);
            exit;
        } catch (Exception $e) {
            $server_error = 'Ocorreu um erro ao processar os dados: ' . $e->getMessage();
        }
    }
}
?>
    <!-- Conteúdo principal -->
    <div class="container mt-5">
        <h2>Formulário M2P</h2>
        <br>
        <h3>2. Avaliação preparatória para o exercício (1º Série)</h3>
        <br>
        <table class="form-table">
            <tr>
                <th>Maquina</th>
                <th>Disponibilidade NMA</th>
                <th>Considerações</th>
            </tr>
            <tr>
                <!-- Linha 1: Pull Down -->
                <td><strong>Pull Down</strong></td>
                <td><input type="text" id="pull-down-disponibilidade" name="pull-down-disponibilidade"></td>
                <td><input type="text" id="pull-down-consideracoes" name="pull-down-consideracoes"></td>
            </tr>
            <tr>
                <!-- Linha 2: Leg Extension -->
                <td><strong>Leg Extension</strong></td>
                <td><input type="text" id="leg-extension-disponibilidade" name="leg-extension-disponibilidade"></td>
                <td><input type="text" id="leg-extension-consideracoes" name="leg-extension-consideracoes"></td>
            </tr>
            <tr>
                <!-- Linha 3: Chest Press -->
                <td><strong>Chest Press</strong></td>
                <td><input type="text" id="chest-press-disponibilidade" name="chest-press-disponibilidade"></td>
                <td><input type="text" id="chest-press-consideracoes" name="chest-press-consideracoes"></td>
            </tr>
            <tr>
                <!-- Linha 4: Leg Press -->
                <td><strong>Leg Press</strong></td>
                <td><input type="text" id="leg-press-disponibilidade" name="leg-press-disponibilidade"></td>
                <td><input type="text" id="leg-press-consideracoes" name="leg-press-consideracoes"></td>
            </tr>
        </table>
    </div>
    <!-- Botões de navegação -->
    <div class="button-container">
        <button id="anterior" onclick="window.location.href='novaavaliacao.html'">Página Anterior</button>
        <button id="seguinte">Página Seguinte</button>
        <button onclick="window.location.href='avaliação.php'">Sair</button>
    </div>
    <?php
    include 'rodape.php';
    include 'layouts/footer.php';
    ?>
    <script>
        // Preencher nome do utilizador
        document.getElementById('username-display').textContent = localStorage.getItem('username') || 'Utilizador';

        function logout() {
            localStorage.removeItem('username');
            window.location.href = 'clientes.html';
        }

        // Função para salvar os dados do formulário e redirecionar para a próxima página
        document.getElementById('seguinte').addEventListener('click', function() {
            // Coletar os valores dos campos
            const pullDownDisponibilidade = document.getElementById('pull-down-disponibilidade').value;
            const pullDownConsideracoes = document.getElementById('pull-down-consideracoes').value;
            const legExtensionDisponibilidade = document.getElementById('leg-extension-disponibilidade').value;
            const legExtensionConsideracoes = document.getElementById('leg-extension-consideracoes').value;
            const chestPressDisponibilidade = document.getElementById('chest-press-disponibilidade').value;
            const chestPressConsideracoes = document.getElementById('chest-press-consideracoes').value;
            const legPressDisponibilidade = document.getElementById('leg-press-disponibilidade').value;
            const legPressConsideracoes = document.getElementById('leg-press-consideracoes').value;

            // Estrutura dos dados a serem enviados
            const dados = {
                id_customer: <?php echo $_GET['id']; ?>,
                pullDownDisponibilidade: pullDownDisponibilidade,
                pullDownConsideracoes: pullDownConsideracoes,
                legExtensionDisponibilidade: legExtensionDisponibilidade,
                legExtensionConsideracoes: legExtensionConsideracoes,
                chestPressDisponibilidade: chestPressDisponibilidade,
                chestPressConsideracoes: chestPressConsideracoes,
                legPressDisponibilidade: legPressDisponibilidade,
                legPressConsideracoes: legPressConsideracoes
            };

            // Enviar os dados para o script PHP via fetch
            fetch('salvar_avaliacao3.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(dados)
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erro ao salvar os dados do formulário');
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Dados do formulário salvos com sucesso:', data);
                    window.location.href = 'novaavaliacao3.php?id=<?php echo $_GET['id']; ?>' // Redirecionar para a próxima página após salvar os dados
                })
                .catch(error => {
                    console.error('Erro:', error);
                });
        });
    </script>
</body>

</html>
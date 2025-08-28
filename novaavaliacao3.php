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
        'p1_pull_down' => 'Existe intermitência/trepidação durante a execução do movimento? - Pull Down',
        'p1_leg_extension' => 'Existe intermitência/trepidação durante a execução do movimento? - Leg Extension',
        'p1_chest_press' => 'Existe intermitência/trepidação durante a execução do movimento? - Chest Press',
        'p1_leg_press' => 'Existe intermitência/trepidação durante a execução do movimento? - Leg Press',
        'p2_pull_down' => 'Acelera e desacelera o movimento de forma adequada? - Pull Down',
        'p2_leg_extension' => 'Acelera e desacelera o movimento de forma adequada? - Leg Extension',
        'p2_chest_press' => 'Acelera e desacelera o movimento de forma adequada? - Chest Press',
        'p2_leg_press' => 'Acelera e desacelera o movimento de forma adequada? - Leg Press',
        'p3_pull_down' => 'É capaz de manter a cadência do movimento dentro do ritmo (60bpm) do metrónomo? - Pull Down',
        'p3_leg_extension' => 'É capaz de manter a cadência do movimento dentro do ritmo (60bpm) do metrónomo? - Leg Extension',
        'p3_chest_press' => 'É capaz de manter a cadência do movimento dentro do ritmo (60bpm) do metrónomo? - Chest Press',
        'p3_leg_press' => 'É capaz de manter a cadência do movimento dentro do ritmo (60bpm) do metrónomo? - Leg Press',
        'p1_resistance_pull_down' => 'É capaz de manter a amplitude pedida inicialmente? - Pull Down',
        'p1_resistance_leg_extension' => 'É capaz de manter a amplitude pedida inicialmente? - Leg Extension',
        'p1_resistance_chest_press' => 'É capaz de manter a amplitude pedida inicialmente? - Chest Press',
        'p1_resistance_leg_press' => 'É capaz de manter a amplitude pedida inicialmente? - Leg Press',
        'p2_resistance_pull_down' => 'A partir de que tempo é que se verifica perda de amplitude? - Pull Down',
        'p2_resistance_leg_extension' => 'A partir de que tempo é que se verifica perda de amplitude? - Leg Extension',
        'p2_resistance_chest_press' => 'A partir de que tempo é que se verifica perda de amplitude? - Chest Press',
        'p2_resistance_leg_press' => 'A partir de que tempo é que se verifica perda de amplitude? - Leg Press',
        'p3_resistance_motivo_pull_down' => 'Motivo - Pull Down',
        'p3_resistance_motivo_leg_extension' => 'Motivo - Leg Extension',
        'p3_resistance_motivo_chest_press' => 'Motivo - Chest Press',
        'p3_resistance_motivo_leg_press' => 'Motivo - Leg Press',
        'subjective_effort_perspective' => 'PSE* - Perceção subjetiva de Esforço',
        'scale_of_feeling' => 'ES** - Escala de sentimento',
    ];

    foreach ($required_fields as $field_id => $field_name) {
        if (empty($_POST[$field_id])) {
            $validation_errors[] = 'Por favor, preencha o campo "' . $field_name . '".';
        }
    }

    // Validar se algum radio button foi selecionado para cada grupo de perguntas
    $radio_groups = ['p1', 'p2', 'p3', 'p1_resistance', 'p2_resistance'];
    foreach ($radio_groups as $group) {
        $fields = [
            $group . '_pull_down',
            $group . '_leg_extension',
            $group . '_chest_press',
            $group . '_leg_press'
        ];

        $group_valid = false;
        foreach ($fields as $field) {
            if (isset($_POST[$field]) && ($_POST[$field] == 'sim' || $_POST[$field] == 'nao')) {
                $group_valid = true;
                break;
            }
        }

        if (!$group_valid) {
            $validation_errors[] = 'Por favor, selecione uma opção para todas as perguntas do grupo "' . strtoupper($group) . '".';
        }
    }

    // Validar campos de texto para motivo
    $motivo_fields = [
        'p3_resistance_motivo_pull_down',
        'p3_resistance_motivo_leg_extension',
        'p3_resistance_motivo_chest_press',
        'p3_resistance_motivo_leg_press',
    ];

    foreach ($motivo_fields as $field) {
        if (!isset($_POST[$field]) || empty(trim($_POST[$field]))) {
            $validation_errors[] = 'Por favor, preencha o motivo para todas as perguntas do campo "Motivo".';
            break;
        }
    }

    // Se não houver erros de validação, processar os dados
    if (empty($validation_errors)) {
        try {
            // Processamento dos dados e salvamento no banco de dados
            $formData = [
                'assessment_second_serie_constraction_quality' => [
                    'p1_pull_down' => $_POST['p1_pull_down'],
                    'p1_leg_extension' => $_POST['p1_leg_extension'],
                    'p1_chest_press' => $_POST['p1_chest_press'],
                    'p1_leg_press' => $_POST['p1_leg_press'],
                    'p2_pull_down' => $_POST['p2_pull_down'],
                    'p2_leg_extension' => $_POST['p2_leg_extension'],
                    'p2_chest_press' => $_POST['p2_chest_press'],
                    'p2_leg_press' => $_POST['p2_leg_press'],
                    'p3_pull_down' => $_POST['p3_pull_down'],
                    'p3_leg_extension' => $_POST['p3_leg_extension'],
                    'p3_chest_press' => $_POST['p3_chest_press'],
                    'p3_leg_press' => $_POST['p3_leg_press'],
                ],
                'assessment_second_serie_muscular_resistance' => [
                    'p1_pull_down' => $_POST['p1_resistance_pull_down'],
                    'p1_leg_extension' => $_POST['p1_resistance_leg_extension'],
                    'p1_chest_press' => $_POST['p1_resistance_chest_press'],
                    'p1_leg_press' => $_POST['p1_resistance_leg_press'],
                    'p2_pull_down' => $_POST['p2_resistance_pull_down'],
                    'p2_leg_extension' => $_POST['p2_resistance_leg_extension'],
                    'p2_chest_press' => $_POST['p2_resistance_chest_press'],
                    'p2_leg_press' => $_POST['p2_resistance_leg_press'],
                    'p3_pull_down' => $_POST['p3_resistance_motivo_pull_down'],
                    'p3_leg_extension' => $_POST['p3_resistance_motivo_leg_extension'],
                    'p3_chest_press' => $_POST['p3_resistance_motivo_chest_press'],
                    'p3_leg_press' => $_POST['p3_resistance_motivo_leg_press'],
                ],
                'assessment_second_serie_load_register' => [
                    'subjective_effort_perspective' => $_POST['subjective_effort_perspective'],
                    'scale_of_feeling' => $_POST['scale_of_feeling'],
                ],
                'id_customer' => $_GET['id'],
            ];

            // Continue com o processamento dos dados aqui...

        } catch (Exception $e) {
            $server_error = 'Erro ao processar os dados: ' . $e->getMessage();
        }
    }
}

?>
    <!-- Conteúdo principal -->
    <div class="container mt-5">
        <h2>Formulário M2P</h2>
        <br>
        <h3>3. Avaliação da Qualidade de Contração e Resistência Muscular (2º Série)</h3>
        <br>
        <br>
        <h4>Qualidade de Contração</h4>
        <table class="form-table">
            <tr>
                <th>Parâmetros</th>
                <th>Pull Down</th>
                <th>Leg Extension</th>
                <th>Chest Press</th>
                <th>Leg Press</th>
            </tr>
            <tr>
                <td>Existe intermitência/trepidação durante a execução do movimento?</td>
                <td><input type="radio" name="p1_pull_down" value="sim"> Sim <input type="radio" name="p1_pull_down" value="nao"> Não</td>
                <td><input type="radio" name="p1_leg_extension" value="sim"> Sim <input type="radio" name="p1_leg_extension" value="nao"> Não</td>
                <td><input type="radio" name="p1_chest_press" value="sim"> Sim <input type="radio" name="p1_chest_press" value="nao"> Não</td>
                <td><input type="radio" name="p1_leg_press" value="sim"> Sim <input type="radio" name="p1_leg_press" value="nao"> Não</td>
            </tr>
            <tr>
                <td>Acelera e desacelera o movimento de forma adequada?</td>
                <td><input type="radio" name="p2_pull_down" value="sim"> Sim <input type="radio" name="p2_pull_down" value="nao"> Não</td>
                <td><input type="radio" name="p2_leg_extension" value="sim"> Sim <input type="radio" name="p2_leg_extension" value="nao"> Não</td>
                <td><input type="radio" name="p2_chest_press" value="sim"> Sim <input type="radio" name="p2_chest_press" value="nao"> Não</td>
                <td><input type="radio" name="p2_leg_press" value="sim"> Sim <input type="radio" name="p2_leg_press" value="nao"> Não</td>
            </tr>
            <tr>
                <td>É capaz de manter a cadência do movimento dentro do ritmo (60bpm) do metrónomo?</td>
                <td><input type="radio" name="p3_pull_down" value="sim"> Sim <input type="radio" name="p3_pull_down" value="nao"> Não</td>
                <td><input type="radio" name="p3_leg_extension" value="sim"> Sim <input type="radio" name="p3_leg_extension" value="nao"> Não</td>
                <td><input type="radio" name="p3_chest_press" value="sim"> Sim <input type="radio" name="p3_chest_press" value="nao"> Não</td>
                <td><input type="radio" name="p3_leg_press" value="sim"> Sim <input type="radio" name="p3_leg_press" value="nao"> Não</td>
            </tr>
        </table>
        <br>
        <br>
        <h4>Resistência Muscular</h4>
        <table class="form-table">
            <tr>
                <th>Parâmetros</th>
                <th>Pull Down</th>
                <th>Leg Extension</th>
                <th>Chest Press</th>
                <th>Leg Press</th>
            </tr>
            <tr>
                <td>É capaz de manter a amplitude pedida inicialmente?</td>
                <td><input type="radio" name="p1_pull_down" value="sim"> Sim <input type="radio" name="p1_pull_down" value="nao"> Não</td>
                <td><input type="radio" name="p1_leg_extension" value="sim"> Sim <input type="radio" name="p1_leg_extension" value="nao"> Não</td>
                <td><input type="radio" name="p1_chest_press" value="sim"> Sim <input type="radio" name="p1_chest_press" value="nao"> Não</td>
                <td><input type="radio" name="p1_leg_press" value="sim"> Sim <input type="radio" name="p1_leg_press" value="nao"> Não</td>
            </tr>
            <tr>
                <td>A partir de que tempo é que se verifica perda de amplitude?</td>
                <td><input type="radio" name="p2_pull_down" value="sim"> Sim <input type="radio" name="p2_pull_down" value="nao"> Não</td>
                <td><input type="radio" name="p2_leg_extension" value="sim"> Sim <input type="radio" name="p2_leg_extension" value="nao"> Não</td>
                <td><input type="radio" name="p2_chest_press" value="sim"> Sim <input type="radio" name="p2_chest_press" value="nao"> Não</td>
                <td><input type="radio" name="p2_leg_press" value="sim"> Sim <input type="radio" name="p2_leg_press" value="nao"> Não</td>
            </tr>
            <tr>
                <td>Motivo</td>
                <td><input type="text" name="p3_pull_down"></td>
                <td><input type="text" name="p3_leg_extension"></td>
                <td><input type="text" name="p3_chest_press"></td>
                <td><input type="text" name="p3_leg_press"></td>
            </tr>
        </table>
        <br>
        <br>
        <h4>Registo de Carga</h4>
        <table class="form-table">
            <tr>
                <th>Registo de Carga</th>
                <th>2 kg</th>
            </tr>
            <tr>
                <td>PSE*</td>
                <td><input type="text" name="subjective_effort_perspective"></td>
            </tr>
            <tr>
                <td>ES**</td>
                <td><input type="text" name="scale_of_feeling"></td>
            </tr>
        </table>
        <br>
        <p>* PSE- Perceção subjetiva de Esforço</p>
        <p>** ES – Escala de sentimento</p>
    </div>
    <!-- Botões de navegação -->
    <div class="button-container">
        <button id="anterior" onclick="window.location.href='novaavaliacao2.html'">Página Anterior</button>
        <button onclick="window.location.href='avaliação.php'">Sair</button>
    </div>
    <div class="button-container">
        <button id="terminar" onclick="finalizarQuestionario()">Terminar</button>
    </div>
    <?php
include 'rodape.php';
include 'layouts/footer.php';
?>
    <script>
        function finalizarQuestionario() {
            const formData = {
                id_customer: <?php echo $_GET['id']; ?>,
                assessment_second_serie_constraction_quality: {
                    p1_pull_down: document.querySelector('input[name="p1_pull_down"]:checked')?.value || "",
                    p1_leg_extension: document.querySelector('input[name="p1_leg_extension"]:checked')?.value || "",
                    p1_chest_press: document.querySelector('input[name="p1_chest_press"]:checked')?.value || "",
                    p1_leg_press: document.querySelector('input[name="p1_leg_press"]:checked')?.value || "",
                    p2_pull_down: document.querySelector('input[name="p2_pull_down"]:checked')?.value || "",
                    p2_leg_extension: document.querySelector('input[name="p2_leg_extension"]:checked')?.value || "",
                    p2_chest_press: document.querySelector('input[name="p2_chest_press"]:checked')?.value || "",
                    p2_leg_press: document.querySelector('input[name="p2_leg_press"]:checked')?.value || "",
                    p3_pull_down: document.querySelector('input[name="p3_pull_down"]:checked')?.value || "",
                    p3_leg_extension: document.querySelector('input[name="p3_leg_extension"]:checked')?.value || "",
                    p3_chest_press: document.querySelector('input[name="p3_chest_press"]:checked')?.value || "",
                    p3_leg_press: document.querySelector('input[name="p3_leg_press"]:checked')?.value || "",
                },
                assessment_second_serie_muscular_resistance: {
                    p1_pull_down: document.querySelector('input[name="p1_pull_down"]:checked')?.value || "",
                    p1_leg_extension: document.querySelector('input[name="p1_leg_extension"]:checked')?.value || "",
                    p1_chest_press: document.querySelector('input[name="p1_chest_press"]:checked')?.value || "",
                    p1_leg_press: document.querySelector('input[name="p1_leg_press"]:checked')?.value || "",
                    p2_pull_down: document.querySelector('input[name="p2_pull_down"]:checked')?.value || "",
                    p2_leg_extension: document.querySelector('input[name="p2_leg_extension"]:checked')?.value || "",
                    p2_chest_press: document.querySelector('input[name="p2_chest_press"]:checked')?.value || "",
                    p2_leg_press: document.querySelector('input[name="p2_leg_press"]:checked')?.value || "",
                    p3_pull_down: document.querySelector('input[name="p3_pull_down"]')?.value || "",
                    p3_leg_extension: document.querySelector('input[name="p3_leg_extension"]')?.value || "",
                    p3_chest_press: document.querySelector('input[name="p3_chest_press"]')?.value || "",
                    p3_leg_press: document.querySelector('input[name="p3_leg_press"]')?.value || "",
                },
                assessment_second_serie_load_register: {
                    subjective_effort_perspective: document.querySelector('input[name="subjective_effort_perspective"]')?.value || "",
                    scale_of_feeling: document.querySelector('input[name="scale_of_feeling"]')?.value || "",
                }
            };

            fetch('salvar_avaliacao4.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formData),
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = 'avaliação.php';
                } else {
                    alert('Erro ao salvar os dados.');
                }
            })
            .catch((error) => {
                console.error('Error:', error);
                alert('Erro ao salvar os dados.');
            });
        }
        // Preencher nome do utilizador
        document.getElementById('username-display').textContent = localStorage.getItem('username') || 'Utilizador';

        function logout() {
            localStorage.removeItem('username');
            window.location.href = 'clientes.html';
        }
    </script>

</body>

</html>

<?php
include 'layouts/config.php';
include 'helpers/functions.php';

if (isset($_GET['cliente_id'])) {
    $cliente_id = $_GET['cliente_id'];

    try {
        $ligacao = new PDO("mysql:host=" . MYSQL_HOST . ";dbname=" . MYSQL_DATABASE . ";charset=utf8", MYSQL_USERNAME, MYSQL_PASSWORD);
        $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Consultar as tabelas do banco de dados
        $tables = [
            "assessment_hgt" => "SELECT * FROM assessment_hgt WHERE id_customers = :id_customers",
            "assessement_first_serie" => "SELECT * FROM assessement_first_serie WHERE id_customers = :id_customers",
            "assessment_second_serie" => "SELECT * FROM assessment_second_serie WHERE id_customers = :id_customers",
            "assessment_second_serie_constraction_quality" => "SELECT * FROM assessment_second_serie_constraction_quality WHERE id_customers = :id_customers",
            "assessment_second_serie_load_register" => "SELECT * FROM assessment_second_serie_load_register WHERE id_customers = :id_customers",
            "assessment_second_serie_muscular_resistance" => "SELECT * FROM assessment_second_serie_muscular_resistance WHERE id_customers = :id_customers",
            "assessment_m2r" => "SELECT * FROM assessment_m2r WHERE id_customers = :id_customers", // Adicionando a nova tabela
        ];

        $data = [];
        foreach ($tables as $key => $query) {
            $stmt = $ligacao->prepare($query);
            $stmt->bindParam(':id_customers', $cliente_id, PDO::PARAM_INT);
            $stmt->execute();
            $data[$key] = $stmt->fetchAll(PDO::FETCH_OBJ);
        }

        // Estilos de tabela
        echo "<style>
                table {
                    border-collapse: collapse;
                    width: 100%;
                    margin-top: 20px;
                    margin-bottom: 20px;
                }
                th, td {
                    border: 1px solid black;
                    text-align: center;
                    padding: 8px;
                }
                th {
                    background-color: #f2f2f2;
                }
              </style>";

        // Função para exibir tabela
        function exibirTabela($titulo, $dados, $campos, $cabecalhos) {
            if (!empty($dados)) {
                echo "<h3>{$titulo}</h3>";
                echo "<table>
                        <thead>
                            <tr>";
                foreach ($cabecalhos as $cabecalho) {
                    echo "<th>{$cabecalho}</th>";
                }
                echo "</tr>
                    </thead>
                    <tbody>";
                foreach ($dados as $item) {
                    echo "<tr>";
                    foreach ($campos as $campo) {
                        if (property_exists($item, $campo)) {
                            echo "<td>{$item->$campo}</td>";
                        } else {
                            echo "<td>N/A</td>";
                        }
                    }
                    echo "</tr>";
                }
                echo "</tbody></table>";
            }
        }

        // Definir os campos e cabeçalhos das tabelas
        $campos = [
            "assessment_hgt" => ["grip_strength_values", "considerations"],
            "assessement_first_serie" => ["machine_pull_down", "machine_leg_extension", "machine_chest_press", "machine_leg_press"],
            "assessment_second_serie" => ["contraction_quality", "muscular_resistance"],
            "assessment_second_serie_constraction_quality" => [
                "p1_pull_down", "p1_leg_extension", "p1_chest_press", "p1_leg_press",
                "p2_pull_down", "p2_leg_extension", "p2_chest_press", "p2_leg_press",
                "p3_pull_down", "p3_leg_extension", "p3_chest_press", "p3_leg_press"
            ],
            "assessment_second_serie_load_register" => ["subjective_effort_perspective", "scale_of_feeling"],
            "assessment_second_serie_muscular_resistance" => [
                "p1_pull_down", "p1_leg_extension", "p1_chest_press", "p1_leg_press",
                "p2_pull_down", "p2_leg_extension", "p2_chest_press", "p2_leg_press",
                "p3_pull_down", "p3_leg_extension", "p3_chest_press", "p3_leg_press"
            ],
            "assessment_m2r" => [
                "test_a1_result", "test_a1_considerations",
                "test_a2_result", "test_a2_considerations",
                "test_a3_result", "test_a3_considerations",
                "test_a4_distance", "test_a4_vo2max",
                "test_a4_resting_heart_rate", "teste_a4_end_heart_race",
                "test_a4_heart_race_after_1second", "test_a4_considerations"
            ]
        ];

        $cabecalhos = [
            "assessment_hgt" => ["Força de Preensão (kg)", "Considerações"],
            "assessement_first_serie" => ["Pull Down", "Leg Extension", "Chest Press", "Leg Press"],
            "assessment_second_serie" => ["Qualidade de Contração", "Resistêcia Muscular"],
            "assessment_second_serie_constraction_quality" => [
                "Pull Down P1", "Leg Extension P1", "Chest Press P1", "Leg Press P1",
                "Pull Down P2", "Leg Extension P2", "Chest Press P2", "Leg Press P2",
                "Pull Down P3", "Leg Extension P3", "Chest Press P3", "Leg Press P3"
            ],
            "assessment_second_serie_load_register" => ["Perceção subjetive de Esforço", "Escala de Sentimento"],
            "assessment_second_serie_muscular_resistance" => [
                "Pull Down P1", "Leg Extension P1", "Chest Press P1", "Leg Press P1",
                "Pull Down P2", "Leg Extension P2", "Chest Press P2", "Leg Press P2",
                "Pull Down P3", "Leg Extension P3", "Chest Press P3", "Leg Press P3"
            ],
            "assessment_m2r" => [
                "Teste A1 Resultadado", 
                "Teste A1 Considerações",
                "Teste A2 Resultado", 
                "Teste A2 Considerações",
                "Teste A3 Resultado", 
                "Teste A3 Considerações",
                "Teste A4 Distância", 
                "Teste A4 VO2max",
                "Teste A4 FC de repouso", 
                "Teste A4 Fc Final do teste",
                "Teste A4 FC após 1'", 
                "Teste A4 Considerações"
            ]
        ];

        // Exibir tabelas
        exibirTabela("Hand Grip Test", $data["assessment_hgt"], $campos["assessment_hgt"], $cabecalhos["assessment_hgt"]);
        exibirTabela("Avaliação Preparatória para o Exercício", $data["assessement_first_serie"], $campos["assessement_first_serie"], $cabecalhos["assessement_first_serie"]);
        exibirTabela("Segunda Série", $data["assessment_second_serie"], $campos["assessment_second_serie"], $cabecalhos["assessment_second_serie"]);
        exibirTabela("Qualidade de Contração", $data["assessment_second_serie_constraction_quality"], $campos["assessment_second_serie_constraction_quality"], $cabecalhos["assessment_second_serie_constraction_quality"]);
        exibirTabela("Registo de Carga", $data["assessment_second_serie_load_register"], $campos["assessment_second_serie_load_register"], $cabecalhos["assessment_second_serie_load_register"]);
        exibirTabela("Resistência Muscular", $data["assessment_second_serie_muscular_resistance"], $campos["assessment_second_serie_muscular_resistance"], $cabecalhos["assessment_second_serie_muscular_resistance"]);
        exibirTabela("Renovação- Avaliação M2R", $data["assessment_m2r"], $campos["assessment_m2r"], $cabecalhos["assessment_m2r"]);

        // Adicionar margem abaixo da última tabela
        echo "<div style='margin-bottom: 50px;'></div>";

    } catch (PDOException $err) {
        echo "Erro: " . $err->getMessage();
    }
} else {
    echo "ID do id_customers não identificado.";
}
?>

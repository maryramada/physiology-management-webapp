<?php
session_start();
include 'layouts/config.php';
include 'helpers/functions.php';
include 'layouts/header.php';
include 'navegacao.php';

if (!isset($_GET['cliente_id'])) {
    echo "Cliente não especificado.";
    exit();
}

$cliente_id = $_GET['cliente_id'];
?>
<!-- Conteúdo principal -->
<div class="container mt-5">
    <h2>Evolução do Teste A4 (Distância)</h2>
    <br>
    <canvas id="grafico-evolucao" style="max-width: 100%; height: 400px;"></canvas>
</div>
<?php
include 'rodape.php';
include 'layouts/footer.php';
?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const clienteId = <?php echo json_encode($cliente_id); ?>;
        if (clienteId) {
            fetch(`carregar_evolucao.php?cliente_id=${clienteId}`)
                .then(response => response.json())
                .then(data => {
                    const ctx = document.getElementById('grafico-evolucao').getContext('2d');
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: data.labels,
                            datasets: [{
                                label: 'Distância (metros)',
                                data: data.distancias,
                                borderColor: 'rgba(75, 192, 192, 1)',
                                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                fill: false,
                                tension: 0.1
                            }]
                        },
                        options: {
                            scales: {
                                x: {
                                    title: {
                                        display: true,
                                        text: 'Data'
                                    }
                                },
                                y: {
                                    title: {
                                        display: true,
                                        text: 'Distância (metros)'
                                    },
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                })
                .catch(error => console.error('Erro ao carregar evolução:', error));
        }
    });
</script>
</body>
</html>

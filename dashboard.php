<?php
require 'bd.php';

try {
    // Realizar las consultas para obtener los datos
    $stmt = $conexion->prepare("
        SELECT 
            COUNT(DISTINCT Dia) AS total_dias_trabajados, 
            SEC_TO_TIME(SUM(TIME_TO_SEC(`Horas Final`))) AS total_horas_trabajadas,
            SEC_TO_TIME(ROUND(AVG(TIME_TO_SEC(`Hora Ingreso`)))) AS promedio_hora_ingreso
        FROM horas
        WHERE YEAR(Dia) = YEAR(CURDATE()) 
        AND `Horas Final` IS NOT NULL 
        AND `Hora Ingreso` IS NOT NULL
        AND `Hora Salida` IS NOT NULL;
    ");

    // Ejecutar la consulta y obtener resultados
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    // Extraer valores de días y horas trabajadas
    $totalDiasTrabajados = $row['total_dias_trabajados'];
    $totalHorasTrabajadas = $row['total_horas_trabajadas'];
    $promedioHoraIngreso = $row['promedio_hora_ingreso'];

    // Convertir horas trabajadas y promedio de ingreso
    $horasTrabajadas = formatHoras($totalHorasTrabajadas);
    $PromedioIngreso = formatHorassimple($promedioHoraIngreso);

    // Obtener días laborables en el mes actual y horas estándar del mes
    $diasLaborablesMes = obtenerDiasLaborables($mes, $año);
    $horasEstándarMes = $diasLaborablesMes * 8;

    // Obtener horas trabajadas en el mes actual
    $stmt = $conexion->prepare("
        SELECT SEC_TO_TIME(SUM(TIME_TO_SEC(`Horas Final`))) AS total_horas_trabajadas 
        FROM horas 
        WHERE YEAR(Dia) = YEAR(CURDATE()) 
        AND MONTH(Dia) = MONTH(CURDATE()) 
        AND `Horas Final` IS NOT NULL;
    ");
    $stmt->execute();
    $totalHorasTrabajadasMes = $stmt->get_result()->fetch_assoc()['total_horas_trabajadas'] ?? "00:00:00";
    $stmt->close();

    // Convertir horas trabajadas en el mes a minutos
    list($horas, $minutos, $segundos) = explode(":", $totalHorasTrabajadasMes);
    $totalHorasTrabajadasEnMinutos = ($horas * 60) + $minutos;

    // Calcular productividad
    $productividad = round(($totalHorasTrabajadasEnMinutos / ($horasEstándarMes * 60)) * 100, 2) . "%";

    // Consultar los últimos 7 días de trabajo
    $stmt = $conexion->prepare("
        SELECT 
            Dia AS date,
            `Horas Final` AS hoursWorked,
            TIME_FORMAT(`Hora Ingreso`, '%H:%i') AS entryTime,
            TIME_FORMAT(`Hora Salida`, '%H:%i') AS exitTime
        FROM horas  
        ORDER BY `date` DESC
        LIMIT 5;
    ");
    $stmt->execute();
    $result = $stmt->get_result();

    // Crear un array con los últimos 7 días de trabajo
    $workData = [];
    while ($row = $result->fetch_assoc()) {
        $workData[] = [
            'date' => $row['date'],
            'hoursWorked' => $row['hoursWorked'],
            'entryTime' => $row['entryTime'],
            'exitTime' => $row['exitTime']
        ];
    }
    $stmt->close();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Horas de Trabajo</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <!-- Chart.js for visualizations -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            background-color: #f4f6f9;
        }

        .dashboard-card {
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .volver-btn {
            position: absolute;
            top: 10px;
            left: 10px;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s ease;
        }

        .volver-btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body class="bg-gray-100">
    <a href="./" class="volver-btn">Volver</a>
    <div class="container-fluid px-4 py-5">
        <h1 class="text-3xl font-bold mb-6 text-center text-gray-800">Panel de Análisis de Horas de Trabajo</h1>

        <div class="row g-4">
            <!-- Summary Cards -->
            <div class="col-md-3">
                <div class="bg-white rounded-lg p-4 dashboard-card">
                    <div class="d-flex align-items-center">
                        <div class="bg-blue-100 p-3 rounded-circle mr-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-blue-500">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                        </div>
                        <div>
                            <h5 class="text-gray-600">Total de Días Trabajados</h5>
                            <p id="totalDaysWorked" class="text-2xl font-bold text-gray-800"><?php echo $totalDiasTrabajados; ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="bg-white rounded-lg p-4 dashboard-card">
                    <div class="d-flex align-items-center">
                        <div class="bg-green-100 p-3 rounded-circle mr-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-green-500">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                        </div>
                        <div>
                            <h5 class="text-gray-600">Total de Horas Trabajadas</h5>
                            <p id="totalHoursWorked" class="text-2xl font-bold text-gray-800"><?php echo $horasTrabajadas; ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="bg-white rounded-lg p-4 dashboard-card">
                    <div class="d-flex align-items-center">
                        <div class="bg-yellow-100 p-3 rounded-circle mr-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-yellow-500">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="8" x2="12" y2="12"></line>
                                <line x1="12" y1="16" x2="12.01" y2="16"></line>
                            </svg>
                        </div>
                        <div>
                            <h5 class="text-gray-600">Hora Promedio de Ingreso</h5>
                            <p id="avgEntryTime" class="text-2xl font-bold text-gray-800"><?php echo $PromedioIngreso; ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="bg-white rounded-lg p-4 dashboard-card">
                    <div class="flex items-center">
                        <div class="bg-blue-100 p-3 rounded-full mr-4">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="miter" class="text-blue-500">
                                <rect x="3" y="13" width="4" height="8" rx="1" />
                                <rect x="10" y="9" width="4" height="12" rx="1" />
                                <rect x="17" y="5" width="4" height="16" rx="1" />
                            </svg>
                        </div>
                        <div>
                            <h5 class="text-gray-600 text-sm">Productividad en <?php echo $meses[$mesNumero];?></h5>
                            <p id="productivityScore" class="text-2xl font-bold text-gray-800"><?php echo $productividad; ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sección de Gráficos -->
            <div class="col-md-6">
                <div class="bg-white rounded-lg p-4 dashboard-card">
                    <h5 class="text-xl font-semibold mb-4 text-center">Horas Trabajadas por Día</h5>
                    <canvas id="hoursWorkedChart"></canvas>
                </div>
            </div>

            <div class="col-md-6">
                <div class="bg-white rounded-lg p-4 dashboard-card">
                    <h5 class="text-xl font-semibold mb-4 text-center">Variaciones en las Horas de Ingreso y Salida</h5>
                    <canvas id="entryTimeChart"></canvas>
                </div>
            </div>

        </div>
    </div>

    <!-- Bootstrap JS and Dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    <?php
    $workDataJson = json_encode($workData);

    // Imprimir la variable JSON dentro de un bloque de JavaScript
    echo "<script>";
    echo "const workData = $workDataJson;";
    echo "</script>";
    ?>
    <script>
        //  Ordenar de mas antiguo a mas actual
        //workData.sort((a, b) => new Date(a.date) - new Date(b.date));


        // Calculate Dashboard Metrics
        function calculateMetrics() {

            // Función para convertir el tiempo HH:MM:SS a horas decimales
            function convertToDecimal(time) {
                const [hours, minutes, seconds] = time.split(":").map(Number);
                return hours + (minutes / 60) + (seconds / 3600);
            }

            // Modificamos los datos para convertir horas trabajadas a formato decimal
            const formattedWorkData = workData.map(d => ({
                date: d.date,
                hoursWorked: convertToDecimal(d.hoursWorked) // Convertimos el tiempo a horas decimales
            }));

            const hoursWorkedChart = new Chart(document.getElementById('hoursWorkedChart'), {
                type: 'bar',
                data: {
                    labels: formattedWorkData.map(d => d.date), // Usamos las fechas como etiquetas
                    datasets: [{
                        label: 'Horas Trabajadas',
                        data: formattedWorkData.map(d => d.hoursWorked), // Usamos los valores de horas trabajadas en formato decimal
                        backgroundColor: 'rgba(75, 192, 192, 0.6)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Horas'
                            },
                            ticks: {
                                // Callback para mostrar el valor en formato "XX h YY m" en el eje Y
                                callback: function(value) {
                                    const hours = Math.floor(value); // Extraemos las horas enteras
                                    const minutes = Math.round((value - hours) * 60); // Calculamos los minutos restantes
                                    return `${hours} h`; // Formato "XX h YY m"
                                }
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                // Cambiar la forma en que se muestra el tooltip (anotación)
                                label: function(tooltipItem) {
                                    const value = tooltipItem.raw;
                                    const hours = Math.floor(value); // Extraemos las horas
                                    const minutes = Math.round((value - hours) * 60); // Calculamos los minutos
                                    return `${hours} h ${minutes} m`; // Muestra en formato "XX h YY m"
                                }
                            }
                        },
                        datalabels: {
                            anchor: 'end', // Posiciona las etiquetas de datos al final de la barra
                            align: 'top', // Ajusta la alineación en la parte superior de la barra
                            formatter: function(value) {
                                const hours = Math.floor(value); // Extraemos las horas
                                const minutes = Math.round((value - hours) * 60); // Calculamos los minutos
                                return `${hours} h ${minutes} m`; // Formato "XX h YY m"
                            }
                        }
                    }
                }
            });

            const entryTimeChart = new Chart(document.getElementById('entryTimeChart'), {
                type: 'line',
                data: {
                    labels: workData.map(d => d.date),
                    datasets: [{
                            label: 'Horas de Entrada',
                            data: workData.map(d => {
                                const [hours, minutes] = d.entryTime.split(':').map(Number);
                                return hours + minutes / 60; // Convertir la hora en un valor numérico decimal
                            }),
                            borderColor: 'rgba(255, 99, 132, 1)',
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            tension: 0.1
                        },
                        {
                            label: 'Horas de Salida',
                            data: workData.map(d => {
                                const [hours, minutes] = d.exitTime.split(':').map(Number);
                                return hours + minutes / 60; // Convertir la hora en un valor numérico decimal
                            }),
                            borderColor: 'rgba(54, 162, 235, 1)',
                            backgroundColor: 'rgba(54, 162, 235, 0.2)',
                            tension: 0.1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            title: {
                                display: true,
                                text: 'Horas'
                            },
                            ticks: {
                                // Formatear los valores del eje Y como HH:mm
                                callback: function(value) {
                                    const hours = Math.floor(value);
                                    const minutes = Math.round((value - hours) * 60);
                                    return `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}`;
                                }
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                // Formatear las etiquetas de las anotaciones de los puntos como HH:mm
                                label: function(tooltipItem) {
                                    const value = tooltipItem.raw; // El valor numérico del tooltip
                                    const hours = Math.floor(value);
                                    const minutes = Math.round((value - hours) * 60);
                                    return `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}`;
                                }
                            }
                        }
                    }
                }
            });



        }

        // Initialize metrics on page load
        document.addEventListener('DOMContentLoaded', calculateMetrics);
    </script>
</body>

</html>
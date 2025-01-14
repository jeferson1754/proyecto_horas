<?php

$usuario  = "root";
$password = "";
$servidor = "localhost";
$basededatos = "epiz_32740026_r_user";
$conexion = mysqli_connect($servidor, $usuario, $password) or die("No se ha podido conectar al Servidor");
mysqli_query($conexion, "SET SESSION collation_connection ='utf8_unicode_ci'");
$db = mysqli_select_db($conexion, $basededatos) or die("Upps! Error en conectar a la Base de Datos");

$conexion->set_charset("utf8");
//Linea para los caracteres �

if (!mysqli_set_charset($conexion, "utf8mb4")) {
    printf("Error loading character set utf8mb4: %s\n", mysqli_error($conexion));
    exit();
}


if (mysqli_connect_errno()) {
    die("No se pudo conectar a la base de datos: " . mysqli_connect_error());
}
/*
$max_queries_per_hour = 500;

$current_time = date("Y-m-d H:i:s", time());

// Consultamos el número de consultas realizadas en la última hora
$query = "SELECT COUNT(*) AS num_queries FROM consultas WHERE fecha > DATE_SUB(NOW(), INTERVAL 1 HOUR)";
$result = mysqli_query($conexion, $query);

// Si la consulta falla, lanzamos un error
if (!$result) {
    die("La consulta falló: " . mysqli_error($conexion));
}

$row = mysqli_fetch_assoc($result);
$num_queries_last_hour = $row["num_queries"];

// Liberamos el resultado de la consulta
mysqli_free_result($result);

// Si se han superado las consultas permitidas, lanzamos un error
if ($num_queries_last_hour >= $max_queries_per_hour) {
    mysqli_close($conexion); // Cerramos la conexión a la base de datos
    die("Lo siento, has superado el límite de consultas por hora.");
}

$query = "INSERT INTO consultas (fecha) VALUES ('$current_time')";
$result = mysqli_query($conexion, $query);

if (!$result) {
    die("La consulta falló: " . mysqli_error($conexion));
}

*/

date_default_timezone_set('America/Santiago');

$mes = date("m"); // Mes actual
$mesNumero = (int)$mes; // Esto convierte "01" a 1
$año = date("Y"); // Año actual
$hoy = date("Y-m-d");
$hora_actual = date("H:i");

$meses = [
    1 => 'Enero',
    2 => 'Febrero',
    3 => 'Marzo',
    4 => 'Abril',
    5 => 'Mayo',
    6 => 'Junio',
    7 => 'Julio',
    8 => 'Agosto',
    9 => 'Septiembre',
    10 => 'Octubre',
    11 => 'Noviembre',
    12 => 'Diciembre'
];


//FUNCIONES 
function formatearHora($hora)
{
    // Convertir la duración a un formato de horas y minutos
    $horaFormato = date('H:i', strtotime($hora));
    $horaParts = explode(":", $horaFormato);

    // Verificar si las horas son 00, para mostrar "mins" en lugar de "hrs"
    if ($horaParts[0] == "00") {
        return $horaParts[1] . " mins";  // Si es "00", solo mostrar los minutos
    } else {
        return $horaFormato . " hrs";  // Si tiene horas, mostrar en el formato "HH:mm hrs"
    }
}

// Función para convertir horas trabajadas a formato "HH h MM m"
function formatHoras($horasTrabajadas)
{
    list($horas, $minutos, $segundos) = explode(":", $horasTrabajadas);
    return "$horas h $minutos m";
}

function formatHorassimple($horasTrabajadas)
{
    list($horas, $minutos, $segundos) = explode(":", $horasTrabajadas);
    return "$horas:$minutos";
}

function formatearHoraAMPM($hora)
{
    // Verificar si la hora es 00:00, en ese caso no mostrar AM o PM
    if ($hora == "00:00" || $hora == "00:00:00") {
        return '<span style="color: red;">' . $hora . '</span>';
    }

    // Convertir la hora a formato 24 horas
    $horaFormato24 = date('H:i', strtotime($hora));

    // Verificar si la hora es AM o PM
    $horaAMPM = date('A', strtotime($hora));

    // Convertir a formato de 12 horas con am/pm
    if ($horaAMPM == "AM") {
        return $horaFormato24 . " am";  // Si es AM, retornar en formato 24 horas + " am"
    } else {
        return $horaFormato24 . " pm";  // Si es PM, retornar en formato 24 horas + " pm"
    }
}


function obtenerDiasLaborables($mes, $año)
{
    $primerDiaMes = strtotime("first day of $año-$mes");
    $ultimoDiaMes = strtotime("last day of $año-$mes");
    $diasLaborables = 0;

    // Recorremos todos los días del mes
    for ($dia = $primerDiaMes; $dia <= $ultimoDiaMes; $dia = strtotime("+1 day", $dia)) {
        $diaSemana = date("N", $dia); // Día de la semana (1 = lunes, 7 = domingo)

        // Si el día no es sábado (6) ni domingo (7), contamos como día laborable
        if ($diaSemana < 6) {
            $diasLaborables++;
        }
    }

    return $diasLaborables;
}

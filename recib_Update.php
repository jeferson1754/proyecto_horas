<?php
include 'bd.php';

// Validar y sanitizar las entradas
$idRegistros  = intval($_POST['id']);
$ingreso      = $_POST['ingreso'] ?? null;
$colacion     = $_POST['colacion'] ?? null;
$fin_colacion = $_POST['fin_colacion'] ?? null;
$salida       = $_POST['salida'] ?? null;

// Configurar la conexión para codificación UTF-8
$conexion->set_charset("utf8");

try {
    // Calcular Total Horas (Ingreso a Salida)
    if ($ingreso && $salida) {
        $segundosTotalHoras = strtotime($salida) - strtotime($ingreso);
        $totalHoras = gmdate("H:i:s", $segundosTotalHoras); // Convertir segundos a formato HH:mm:ss
    } else {
        $totalHoras = "00:00:00";
    }

    // Calcular Total Colación (Colación a Fin Colación)
    if ($colacion && $fin_colacion) {
        $segundosTotalColacion = strtotime($fin_colacion) - strtotime($colacion);
        $totalAlmuerzo = gmdate("H:i:s", $segundosTotalColacion); // Convertir segundos a formato HH:mm:ss
    } else {
        $totalAlmuerzo = "00:00:00";
    }

    // Calcular Horas Finales (Total Horas - Total Colación)
    $segundosHorasFinales = $segundosTotalHoras - $segundosTotalColacion;
    $horasFinales = gmdate("H:i:s", max(0, $segundosHorasFinales)); // Evitar valores negativos

    // Actualizar los valores en la base de datos
    $stmt = $conexion->prepare("
        UPDATE `horas` 
        SET `Hora Ingreso` = ?, 
            `Hora Colacion` = ?, 
            `Hora Fin Colacion` = ?, 
            `Total Colacion` = ?, 
            `Hora Salida` = ?, 
            `Total Horas` = ?, 
            `Horas Final` = ? 
        WHERE ID = ?
    ");
    $stmt->bind_param(
        "sssssssi",
        $ingreso,
        $colacion,
        $fin_colacion,
        $totalAlmuerzo,
        $salida,
        $totalHoras,
        $horasFinales,
        $idRegistros
    );
    $stmt->execute();

    // Redirigir al index
    header("Location: index.php");
    exit;
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

<?php
include 'bd.php';

// Validar y sanitizar las entradas
$idRegistros = isset($_POST['id']) ? intval($_POST['id']) : 0;

// Asegurarse de que se proporcionó un ID válido
if ($idRegistros <= 0) {
    echo "ID no válido";
    exit;
}

// Configurar la conexión para codificación UTF-8
$conexion->set_charset("utf8");

try {
    // Preparar y ejecutar la consulta para eliminar el registro
    $stmt = $conexion->prepare("DELETE FROM `horas` WHERE ID = ?");
    $stmt->bind_param("i", $idRegistros);

    if ($stmt->execute()) {
        // Redirigir al index
        header("Location: index.php");
        exit;
    } else {
        echo "Error al eliminar el registro";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

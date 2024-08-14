<?php
include('bd.php');
$fecha     = $_REQUEST['fecha'];
$hora      = $_REQUEST['hora'];
$accion    = $_REQUEST['accion'];


if ($accion == "Ingreso2") {
    try {
        $conn = new PDO("mysql:host=$servidor;dbname=$basededatos", $usuario, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql2 = "UPDATE horas SET `Hora Ingreso` ='" . $hora . "' WHERE Dia='" . $fecha . "';";
        $conn->exec($sql2);

        echo $sql2;

        echo "<br>";
        $conn = null;
    } catch (PDOException $e) {
        echo $sql2;
        $conn = null;
    }

} else {

    try {
        $conn = new PDO("mysql:host=$servidor;dbname=$basededatos", $usuario, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql2 = "INSERT INTO `horas`( Dia,
    `Hora Ingreso`)
    VALUES (
    '" . $fecha . "',
    '" . $hora . "')";
        $conn->exec($sql2);

        echo $sql2;

        echo "<br>";
        $conn = null;
    } catch (PDOException $e) {
        echo $sql2;
        $conn = null;
    }
}

header("location:index.php");

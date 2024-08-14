
<?php
include 'bd.php';


$idRegistros  = $_POST['id'];
//$idRegistros = 7;
$hora         = $_POST['hora'];
//$hora="16:07:00";
$accion       = $_POST['accion'];

$fecha       = $_POST['fecha'];


if ($accion == "Colacion") {
    $update = ("UPDATE horas SET `Hora Colacion` ='" . $hora . "' WHERE ID='" . $idRegistros . "';");

    $result_update = mysqli_query($conexion, $update);

    echo $update;
} else if ($accion == "Colacion2") {
    $update = ("UPDATE horas SET `Hora Colacion` ='" . $hora . "' WHERE Dia='" . $fecha . "';");

    $result_update = mysqli_query($conexion, $update);

    echo $update;
} else if ($accion == "Fin_Colacion") {
    $update = ("UPDATE
    horas 
    SET 
    `Hora Fin Colacion` ='" . $hora . "'
    WHERE ID='" . $idRegistros . "';
    ");

    $result_update = mysqli_query($conexion, $update);

    $sql = "SELECT `Hora Colacion`,`Hora Fin Colacion` FROM `horas` where ID='" . $idRegistros . "';";
    $result = mysqli_query($conexion, $sql);

    while ($valores = mysqli_fetch_array($result)) {
        $hora1 = $valores[0];
        $hora2 = $valores[1];
    }
    echo $sql;
    echo "<br>";
    echo $hora1;
    echo "<br>";
    echo $hora2;
    echo "<br>";
    $resultado = date("H:i:s", strtotime("00:00") + strtotime($hora2) - strtotime($hora1));
    echo $resultado;
    echo "<br>";
    /*
    $hora_actual = '18:33:00';
    $fin = '07:45:00';
    $resultado = date("H:i:s", strtotime("00:00") + strtotime($hora_actual) - strtotime($fin));
    echo $resultado;
    echo "<br>";
    echo $hora_actual;
    echo "<br>";
    echo $fin;
    echo "<br>";
    */

    $time = ("UPDATE `horas` SET `Total Colacion` ='" . $resultado . "'  where ID='" . $idRegistros . "';");

    $consulta = mysqli_query($conexion, $time);

    echo $time;
} else if ($accion == "Fin_Colacion2") {
    $update = ("UPDATE horas SET `Hora Fin Colacion` ='" . $hora . "' WHERE Dia='" . $fecha . "';");

    $result_update = mysqli_query($conexion, $update);

    echo $update;
    echo "<br>";

    $sql = "SELECT `Hora Colacion`,`Hora Fin Colacion` FROM `horas` where Dia='" . $fecha . "';";
    $result = mysqli_query($conexion, $sql);

    while ($valores = mysqli_fetch_array($result)) {
        $hora1 = $valores[0];
        $hora2 = $valores[1];
    }
    echo $sql;
    echo "<br>";
    echo $hora1;
    echo "<br>";
    echo $hora2;
    echo "<br>";
    $resultado = date("H:i:s", strtotime("00:00") + strtotime($hora2) - strtotime($hora1));
    echo $resultado;
    echo "<br>";

    $time = ("UPDATE `horas` SET `Total Colacion` ='" . $resultado . "'  where Dia='" . $fecha . "';");

    $consulta = mysqli_query($conexion, $time);

    echo $time;
} else if ($accion == "Salida") {

    $update = ("UPDATE
    horas 
    SET 
    `Hora Salida` ='" . $hora . "'
    WHERE ID='" . $idRegistros . "';
    ");

    $result_update = mysqli_query($conexion, $update);

    echo $update;

    $sql2 = "SELECT `Hora Ingreso`,`Hora Salida` FROM `horas` where ID='" . $idRegistros . "';";
    $result = mysqli_query($conexion, $sql2);

    while ($valores = mysqli_fetch_array($result)) {
        $hora1 = $valores[0];
        $hora2 = $valores[1];
    }

    echo "<br>";
    echo $hora1 . ' Hora Ingreso';
    echo "<br>";
    echo $hora2 . ' Hora Salida';;
    echo "<br>";
    $resultado = date("H:i:s", strtotime("00:00") + strtotime($hora2) - strtotime($hora1));
    echo $resultado . ' Total Horas';
    echo "<br>";

    $time = ("UPDATE `horas` SET `Total Horas` ='" . $resultado . "'  where ID='" . $idRegistros . "';");
    $consulta = mysqli_query($conexion, $time);

    $sql1 = "SELECT `Total Colacion`,`Total Horas` FROM `horas` where ID='" . $idRegistros . "';";
    $result1 = mysqli_query($conexion, $sql1);

    while ($valores = mysqli_fetch_array($result1)) {
        $hora3 = $valores[0];
        $hora4 = $valores[1];
    }
    echo $sql1;
    echo "<br>";
    echo $sql2;
    echo "<br>";
    echo $hora3 . ' Horas Total Colacion';
    echo "<br>";
    echo $hora4 . ' Horas Total Horas';
    echo "<br>";
    $result = date("H:i:s", strtotime("00:00") + strtotime($hora4) - strtotime($hora3));
    echo $result . ' Horas Final';
    echo "<br>";

    $day = ("UPDATE `horas` SET `Horas Final` ='" . $result . "'  where ID='" . $idRegistros . "';");


    $consulta2 = mysqli_query($conexion, $day);
    echo $time;
    echo "<br>";
    echo $day;
} else if ($accion == "Salida2") {

    $update = ("UPDATE horas SET `Hora Salida` ='" . $hora . "' WHERE Dia='" . $fecha . "';");

    $result_update = mysqli_query($conexion, $update);

    echo $update;

    $sql2 = "SELECT `Hora Ingreso`,`Hora Salida` FROM `horas` where Dia='" . $fecha . "';";
    $result = mysqli_query($conexion, $sql2);

    while ($valores = mysqli_fetch_array($result)) {
        $hora1 = $valores[0];
        $hora2 = $valores[1];
    }

    echo "<br>";
    echo $hora1 . ' Hora Ingreso';
    echo "<br>";
    echo $hora2 . ' Hora Salida';;
    echo "<br>";
    $resultado = date("H:i:s", strtotime("00:00") + strtotime($hora2) - strtotime($hora1));
    echo $resultado . ' Total Horas';
    echo "<br>";

    $time = ("UPDATE `horas` SET `Total Horas` ='" . $resultado . "'  where Dia='" . $fecha . "';");
    $consulta = mysqli_query($conexion, $time);

    $sql1 = "SELECT `Total Colacion`,`Total Horas` FROM `horas` where Dia='" . $fecha . "';";
    $result1 = mysqli_query($conexion, $sql1);

    while ($valores = mysqli_fetch_array($result1)) {
        $hora3 = $valores[0];
        $hora4 = $valores[1];
    }
    echo $sql1;
    echo "<br>";
    echo $sql2;
    echo "<br>";
    echo $hora3 . ' Horas Total Colacion';
    echo "<br>";
    echo $hora4 . ' Horas Total Horas';
    echo "<br>";
    $result = date("H:i:s", strtotime("00:00") + strtotime($hora4) - strtotime($hora3));
    echo $result . ' Horas Final';
    echo "<br>";

    $day = ("UPDATE `horas` SET `Horas Final` ='" . $result . "'  where Dia='" . $fecha . "';");

    $consulta2 = mysqli_query($conexion, $day);
    echo $time;
    echo "<br>";
    echo $day;
}

header("location:index.php");
?>

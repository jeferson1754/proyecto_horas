<?php

require 'bd.php';


$sql = "SELECT DATE_FORMAT(NOW( ), '%H:%i:%S' );";

$result = mysqli_query($conexion, $sql);

while ($valores = mysqli_fetch_array($result)) {
    $hora = $valores[0];
}

?>
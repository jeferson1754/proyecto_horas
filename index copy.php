<?php

require 'bd.php';

$hoy = date("Y-m-d");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css">
    <script src="js/bootstrap.bundle.min.js"></script>

    <title>Horas
    </title>
</head>
<style>
    .main-container {
        margin: 20px !important;
    }

    .boton {
        margin-top: 10px !important;
    }

    @media screen and (max-width: 600px) {

        .boton {
            height: 70px;
            width: 90%;
            margin-left: 5%;
            margin-right: 5%;
        }

        #mi-div {
            height: 100%;
        }

    }

    #mi-div {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 180px;
        width: 100%;
    }

    iframe {
        width: 100%;
        height: 175px;
    }
</style>

<body>
    <div class="col-sm">
        <!--- Formulario para registrar Cliente --->

        <button type="button" class="btn btn-primary boton" data-bs-toggle="modal" data-bs-target="#ingreso">
            Ingreso
        </button>
        <button type="button" class="btn btn-secondary boton" data-bs-toggle="modal" data-bs-target="#colacion">
            Inicio Colacion
        </button>
        <button type="button" class="btn btn-dark boton" data-bs-toggle="modal" data-bs-target="#fincolacion">
            Fin Colacion
        </button>
        <button type="button" class="btn btn-success boton" data-bs-toggle="modal" data-bs-target="#salida">
            Salida
        </button>

        <?php include('ModalIngreso.php');  ?>
        <?php include('ModalColacion.php');  ?>
        <?php include('ModalFin-Colacion.php');  ?>
        <?php include('ModalSalida.php');  ?>

        <button type="button" class="btn btn-outline-primary boton" data-bs-toggle="modal" data-bs-target="#ingreso2">
            Ingreso
        </button>
        <button type="button" class="btn btn-outline-secondary boton" data-bs-toggle="modal" data-bs-target="#colacion2">
            Inicio Colacion
        </button>
        <button type="button" class="btn btn-outline-dark boton" data-bs-toggle="modal" data-bs-target="#fincolacion2">
            Fin Colacion
        </button>
        <button type="button" class="btn btn-outline-success boton" data-bs-toggle="modal" data-bs-target="#salida2">
            Salida
        </button>




        <?php include('ModalIngreso copy.php');  ?>
        <?php include('ModalColacion copy.php');  ?>
        <?php include('ModalFin-Colacion copy.php');  ?>
        <?php include('ModalSalida copy.php');  ?>

    </div>
    <!--
    <div id="mi-div">
		<iframe src="http://inventarioncc.infinityfreeapp.com/Horas/hora%20real/1.php" frameborder="0"></iframe>
	</div>
    -->

    <?php include('./hora real/1.php');  ?>


    <div class="main-container">
        <table>
            <thead>
                <tr>
                    <th>Dia</th>
                    <th>Hora Ingreso</th>
                    <th>Hora Colacion</th>
                    <th>Hora Fin-Colacion</th>
                    <th>Hora Salida</th>
                    <th style="background-color:#004385;">Total Colacion</th>
                    <th style="background-color:#5C6784;">Horas Final</th>
                </tr>
            </thead>
            <?php

            $sql = "SELECT * FROM `horas` WHERE YEAR(`Dia`) = YEAR(CURDATE()) ORDER BY `horas`.`Dia` DESC";
            $result = mysqli_query($conexion, $sql);
            //echo $sql;

            while ($mostrar = mysqli_fetch_array($result)) {
            ?>
                <tr>
                    <td><?php echo $mostrar['Dia'] ?></td>
                    <td><?php echo $mostrar['Hora Ingreso'] ?></td>
                    <td><?php echo $mostrar['Hora Colacion'] ?></td>
                    <td><?php echo $mostrar['Hora Fin Colacion'] ?></td>
                    <td><?php echo $mostrar['Hora Salida'] ?></td>
                    <td><?php echo $mostrar['Total Colacion'] ?></td>
                    <td><?php echo $mostrar['Horas Final'] ?></td>
                </tr>
            <?php
            }
            ?>
        </table>
    </div>
</body>
<script src="js/jquery.min.js"></script>
<script src="js/popper.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="script.js"></script>

</html>
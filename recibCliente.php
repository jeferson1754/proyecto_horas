<header>
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
</header>

<?php

include('bd.php');

$fecha  = $_REQUEST['fecha'];
$hora   = $_REQUEST['hora'];

try {
    $conn = new PDO("mysql:host=$servidor;dbname=$basededatos", $usuario, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Consulta para verificar si ya existe el registro
    $sqlCheck = "SELECT COUNT(*) as count FROM horas WHERE Dia = :fecha AND `Hora Ingreso` = :hora";
    $stmt = $conn->prepare($sqlCheck);
    $stmt->bindParam(':fecha', $fecha);
    $stmt->bindParam(':hora', $hora);
    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result['count'] > 0) {
        // Ya existe el registro, mostrar mensaje de error con SweetAlert
        echo "
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Registro duplicado',
                text: 'Ya existe un ingreso con la misma fecha y hora.',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = 'index.php';
            });
        </script>";
    } else {
        // Insertar nuevo registro
        $sqlInsert = "INSERT INTO `horas` (Dia, `Hora Ingreso`) VALUES (:fecha, :hora)";
        $stmtInsert = $conn->prepare($sqlInsert);
        $stmtInsert->bindParam(':fecha', $fecha);
        $stmtInsert->bindParam(':hora', $hora);
        $stmtInsert->execute();

        echo "
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Registro agregado',
                text: 'El ingreso ha sido registrado correctamente.',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = 'index.php';
            });
        </script>";
    }

    $conn = null;
} catch (PDOException $e) {
    echo "

    <script>
        Swal.fire({
            icon: 'error',
            title: 'Error en el sistema',
            text: '" . $e->getMessage() . "',
            confirmButtonText: 'OK'
        }).then(() => {
            window.location.href = 'index.php';
        });
    </script>";
}

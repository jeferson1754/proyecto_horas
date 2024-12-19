<?php

require 'bd.php';

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SeguimientoHoras</title>

    <!-- TailwindCSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

    <!-- Bootstrap CSS (Consolida la versión de Bootstrap) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://kit.fontawesome.com/8846655159.js" crossorigin="anonymous"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Bootstrap Bundle JS (Bootstrap y Popper.js combinados) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</head>


<style>
    /* Custom responsive table styling */
    @media screen and (max-width: 768px) {
        .responsive-table tbody tr {
            display: block;
            margin-bottom: 1rem;
            border: 1px solid #ddd;
        }

        .responsive-table tbody td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem;
            border-bottom: 1px solid #eee;
        }

        .responsive-table tbody td::before {
            content: attr(data-label);
            font-weight: bold;
            margin-right: 10px;
        }
    }

    .modal-content {
        border: none;
    }

    .form-control:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }

    .input-group-text {
        border: 1px solid #ced4da;
    }

    .form-text {
        color: #6c757d;
        margin-top: 0.5rem;
        font-size: 0.875rem;
    }

    .btn-primary {
        background-color: #0d6efd;
        border: none;
        transition: all 0.2s;
    }

    .btn-primary:hover {
        background-color: #0b5ed7;
        transform: translateY(-1px);
    }

    .btn-light {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
    }

    .btn-light:hover {
        background-color: #e9ecef;
    }

    /* Estilos para campos deshabilitados */
    .form-control:disabled {
        background-color: #e9ecef;
        cursor: not-allowed;
    }

    /* Animación suave para transiciones */
    .form-control,
    .btn {
        transition: all 0.2s ease-in-out;
    }

    .modal-content {
        border: none;
    }

    .alert {
        border-radius: 0.5rem;
    }

    .card {
        border-radius: 0.5rem;
    }

    .btn-danger {
        background-color: #dc3545;
        border: none;
        transition: all 0.2s;
    }

    .btn-danger:hover {
        background-color: #bb2d3b;
        transform: translateY(-1px);
    }

    .btn-light {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
    }

    .btn-light:hover {
        background-color: #e9ecef;
    }

    /* Animación del modal */
    .modal.fade .modal-dialog {
        transition: transform 0.2s ease-out;
    }

    .modal.fade .modal-content {
        transform: scale(0.95);
        transition: transform 0.2s ease-out;
    }

    .modal.show .modal-content {
        transform: scale(1);
    }

    .modal-content {
        border: none;
    }

    .form-control:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }

    .btn-primary {
        background-color: #0d6efd;
        border: none;
        transition: all 0.2s;
    }

    .btn-primary:hover {
        background-color: #0b5ed7;
        transform: translateY(-1px);
    }

    .btn-light {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
    }

    .btn-light:hover {
        background-color: #e9ecef;
    }
</style>

<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <!-- Digital Clock and Stats Section -->
            <div class="relative">
                <div class="absolute top-2 left-2 z-10">

                    <button class="bg-gray-200 text-gray-700 p-2 rounded-full hover:bg-gray-300 transition duration-300 ease-in-out shadow-md">
                        <a href="dashboard.php">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                            </svg>
                        </a>
                    </button>
                </div>
                <div id="date" class="text-center mt-4 text-2xl md:text-4xl text-gray-600 p-4 md:p-6"></div>
            </div>


            <!-- Responsive Stats Grid -->
            <div class="bg-gradient-to-r from-blue-500 to-purple-600 text-white p-4 md:p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-center">
                    <div class="mb-4 md:mb-0">
                        <h2 class="text-base md:text-lg font-semibold">Horas Trabajadas</h2>
                        <p id="tiempo" class="text-2xl md:text-3xl font-bold">00:00:00</p>
                    </div>
                    <div class="mb-4 md:mb-0">
                        <h2 class="text-base md:text-lg font-semibold">Hora Actual</h2>
                        <p id="clock" class="text-2xl md:text-3xl font-bold">00:00:00</p>
                    </div>
                    <div>
                        <h2 class="text-base md:text-lg font-semibold">Horas Faltantes</h2>
                        <p id="countdown" class="text-2xl md:text-3xl font-bold">00:00:00</p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="p-4 bg-gray-50 grid grid-cols-1 md:grid-cols-1 gap-1 md:gap-2">
                <!-- Botón único para Entrada con ícono -->
                <button
                    class="btn btn-primary bg-blue-500 text-white p-2 md:p-3 rounded hover:bg-blue-600 text-sm md:text-base flex items-center justify-center"
                    data-bs-toggle="modal"
                    data-bs-target="#ingreso">

                    <!-- Ícono de Font Awesome para Entrada (reemplaza con el que prefieras) -->
                    <i class="fa fa-sign-in-alt mr-2"></i> <!-- Ícono de Entrada -->
                    Entrada
                </button>
            </div>


            <?php include('ModalIngreso.php'); ?>

            <!-- Responsive History Table -->
            <div class="p-4">
                <div class="overflow-x-auto">
                    <table class="w-full bg-white shadow-md rounded-lg responsive-table">
                        <thead class="bg-gray-200 text-gray-600 uppercase text-xs md:text-sm leading-normal hidden md:table-header-group">
                            <tr>
                                <th class="py-3 px-2 md:px-6 text-left">Fecha</th>
                                <th class="py-3 px-2 md:px-6 text-left">Entrada</th>
                                <th class="py-3 px-2 md:px-6 text-left">Inicio Almuerzo</th>
                                <th class="py-3 px-2 md:px-6 text-left">Fin Almuerzo</th>
                                <th class="py-3 px-2 md:px-6 text-left">Salida</th>
                                <th class="py-3 px-2 md:px-6 text-left bg-blue-100">Total Almuerzo</th>
                                <th class="py-3 px-2 md:px-6 text-left bg-black-300">Total Horas</th>
                                <th class="py-3 px-2 md:px-6 text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 text-xs md:text-sm font-light">
                            <?php

                            $sql = "SELECT * FROM `horas` ORDER BY `horas`.`Dia` DESC LIMIT 60;";
                            $result = mysqli_query($conexion, $sql);
                            //echo $sql;


                            while ($mostrar = mysqli_fetch_array($result)) {
                            ?>
                                <tr class="border-b border-gray-200 hover:bg-gray-100 flex flex-col md:table-row">
                                    <td class="py-2 px-2 md:px-6 flex justify-between md:table-cell" data-label="Fecha">

                                        <span><?php echo date('d-m-Y', strtotime($mostrar['Dia'])); ?></span>

                                    </td>
                                    <td class="py-2 px-2 md:px-6 flex justify-between md:table-cell" data-label="Entrada">

                                        <span><?php echo formatearHoraAMPM($mostrar['Hora Ingreso']) ?></span>
                                    </td>
                                    <td class="py-2 px-2 md:px-6 flex justify-between md:table-cell" data-label="Inicio Almuerzo">

                                        <span><?php echo formatearHoraAMPM($mostrar['Hora Colacion']) ?></span>
                                    </td>
                                    <td class="py-2 px-2 md:px-6 flex justify-between md:table-cell" data-label="Fin Almuerzo">

                                        <span><?php echo formatearHoraAMPM($mostrar['Hora Fin Colacion']) ?></span>
                                    </td>
                                    <td class="py-2 px-2 md:px-6 flex justify-between md:table-cell" data-label="Salida">

                                        <span><?php echo formatearHoraAMPM($mostrar['Hora Salida']) ?></span>
                                    </td>
                                    <td class="py-2 px-2 md:px-6 flex justify-between md:table-cell bg-blue-50" data-label="Total Almuerzo">
                                        <span><?php echo formatearHora($mostrar['Total Colacion']) ?></span>
                                    </td>
                                    <td class="py-2 px-2 md:px-6 flex justify-between md:table-cell bg-gray-10" data-label="Total Horas">

                                        <span><?php echo formatHoras($mostrar['Horas Final']) ?></span>
                                    </td>
                                    <td class="py-2 px-2 md:px-6 flex justify-center md:table-cell" data-label="Acciones">
                                        <div class="flex space-x-2">
                                            <button class="bg-blue-500 text-white p-1 md:p-2 rounded hover:bg-blue-600"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editar<?php echo $mostrar['ID'] ?>">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 md:h-4 md:w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
                                            <button class="bg-red-500 text-white p-1 md:p-2 rounded hover:bg-red-600" data-bs-toggle="modal"
                                                data-bs-target="#delete<?php echo $mostrar['ID'] ?>">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 md:h-4 md:w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php
                                include('ModalEditar.php');
                                include('ModalDelete.php');
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="./script.js"></script>
</body>

</html>
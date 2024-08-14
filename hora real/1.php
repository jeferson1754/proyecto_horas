<?php
// Obtén la hora deseada desde una variable
$hora_anterior = '07:45:00';
$hora_deseada = '17:15:00';
?>
<!DOCTYPE html>
<html>

<head>
    <title>Reloj digital con fecha en JavaScript</title>
    <style>
        body {
            /*
 border: 2px solid black;
  border-radius: 10px;
  */
            /*height:150px !important;*/
            height: 150px !important;

        }

        @media screen and (max-width: 600px) {

            #date {
                display: none;
            }


            .clock div {
                width: 100%;
            }

        }

        .clock {
            font-size: 60px;
            font-weight: bold;
            color: white;
            font-family: 'Montserrat', sans-serif;
            text-align: center;
            border: 2px solid white;
            border-radius: 10px;
            box-shadow: 0px 0px 10px 0px rgba(255, 255, 255, 0.5);
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            padding: 25px;
        }

        .clock h2 {
            font-size: 20px;
        }

        .contenedor div {
            margin-bottom: 0px;
            width: 48%;
        }


        #date {
            font-size: 50px;
            font-family: "Roboto", Arial, sans-serif;
            color: #666;
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <div id="date"></div>
    <!--
 <div class="clock">
   <h1>Título</h1>
    <p id="tiempo"></p>
    <p id="clock"></p>
    <p id="countdown"></p>

 </div>
 -->

    <div class="clock">
        <div>
            <h2>Horas Hechas</h2>
            <p id="tiempo"></p>
        </div>
        <div>
            <h2>Hora Actual</h2>
            <p id="clock"></p>
        </div>
        <div>
            <h2>Horas Faltantes</h2>
            <p id="countdown"></p>
        </div>
    </div>
    <script>
        function digitalClock() {
            // Obtenemos la hora actual
            var date = new Date();
            var hours = date.getHours();
            var minutes = date.getMinutes();
            var seconds = date.getSeconds();

            // Formateamos la hora para que siempre tenga dos dígitos
            hours = (hours < 10) ? "0" + hours : hours;
            minutes = (minutes < 10) ? "0" + minutes : minutes;
            seconds = (seconds < 10) ? "0" + seconds : seconds;

            // Mostramos la hora en la página
            document.getElementById("clock").innerHTML = hours + ":" + minutes + ":" + seconds;

            // Obtenemos la fecha actual y la mostramos en la página
            var days = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
            var months = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
            var day = days[date.getDay()];
            var month = months[date.getMonth()];
            var year = date.getFullYear();
            var dayNum = date.getDate();

            // Formateamos la fecha
            var dateFormat = day + " " + dayNum + " de " + month + " del " + year;

            // Mostramos la fecha en la página
            document.getElementById("date").innerHTML = dateFormat;
        }

        // Llamamos a la función cada segundo para actualizar el reloj
        setInterval(digitalClock, 1000);

        // Actualiza el tiempo transcurrido cada segundo
        setInterval(actualizarTiempoTranscurrido, 1000);

        // Función que actualiza el tiempo transcurrido
        function actualizarTiempoTranscurrido() {
            // Crea un objeto Date con las 09:00:00 de hoy
            const horaInicio = new Date();
            horaInicio.setHours(<?php echo substr($hora_anterior, 0, 2); ?>);
            horaInicio.setMinutes(<?php echo substr($hora_anterior, 3, 2); ?>);
            horaInicio.setSeconds(<?php echo substr($hora_anterior, 6, 2); ?>);

            // Crea un objeto Date con la hora actual
            const horaActual = new Date();

            // Calcula la diferencia entre las 09:00:00 y la hora actual en segundos
            const diferenciaSegundos = Math.floor((horaActual - horaInicio) / 1000);

            // Convierte la diferencia en segundos a horas, minutos y segundos
            const horas = Math.floor(diferenciaSegundos / 3600);
            const minutos = Math.floor((diferenciaSegundos % 3600) / 60);
            const segundos = diferenciaSegundos % 60;

            // Formatea la diferencia en formato "HH:MM:SS"
            const tiempoFormato = `${horas.toString().padStart(2, '0')}:${minutos.toString().padStart(2, '0')}:${segundos.toString().padStart(2, '0')}`;

            // Asigna el valor del tiempo transcurrido en formato "HH:MM:SS" al elemento HTML
            document.getElementById("tiempo").textContent = `${tiempoFormato}`;

            tiempoFormato

            if (tiempoFormato > "12:00:00") {
                clearInterval(tiempo);
                document.getElementById('tiempo').textContent = "--:--:--";
            }


        }

        const now = new Date();

        // Establece la hora objetivo en 18:30:00
        const targetTime = new Date(now);
        targetTime.setHours(<?php echo substr($hora_deseada, 0, 2); ?>);
        targetTime.setMinutes(<?php echo substr($hora_deseada, 3, 2); ?>);
        targetTime.setSeconds(<?php echo substr($hora_deseada, 6, 2); ?>);

        // Calcula la diferencia entre la hora actual y la hora objetivo
        let remainingTime = targetTime - now;

        // Convierte la diferencia en segundos
        remainingTime = Math.floor(remainingTime / 1000);

        // Crea una función para actualizar el temporizador
        function updateCountdown() {
            // Convierte el tiempo restante en horas, minutos y segundos
            const hours = Math.floor(remainingTime / 3600);
            const minutes = Math.floor((remainingTime % 3600) / 60);
            const seconds = remainingTime % 60;

            // Crea una cadena con el tiempo restante
            const timeString = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;

            // Actualiza el contenido del elemento <p> con el tiempo restante
            document.getElementById('countdown').textContent = timeString;

            // Resta un segundo del tiempo restante
            remainingTime--;

            // Si el tiempo restante llega a cero, detén el temporizador
            if (remainingTime < 0) {
                clearInterval(countdown);
                document.getElementById('countdown').textContent = "--:--:--";
            }
        }

        // Crea un temporizador que se actualizará cada segundo
        const countdown = setInterval(updateCountdown, 1000);

        // Actualiza el temporizador por primera vez
        updateCountdown();
    </script>
</body>

</html>
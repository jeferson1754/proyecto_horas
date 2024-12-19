function updateTime() {
    const now = new Date();
    const hours = now.getHours().toString().padStart(2, '0');
    const minutes = now.getMinutes().toString().padStart(2, '0');
    const seconds = now.getSeconds().toString().padStart(2, '0');
    const timeString = `${hours}:${minutes}:${seconds}`;
    document.getElementById("start-time-1").value = timeString;
    document.getElementById("end-time-1").value = timeString;
    document.getElementById("start-time-2").value = timeString;
    document.getElementById("end-time-2").value = timeString;
}

setInterval(updateTime, 1000);

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
    var days = ['Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado'];
    var months = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
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
    horaInicio.setHours(07);
    horaInicio.setMinutes(45);
    horaInicio.setSeconds(00);

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
  targetTime.setHours(17);
  targetTime.setMinutes(15);
  targetTime.setSeconds(00);

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


  // Add this to your existing script for mobile responsiveness
  function adjustTableForMobile() {
      const table = document.querySelector('.responsive-table');
      const isMobile = window.innerWidth <= 768;
      
      if (isMobile) {
          // Add mobile-specific attributes
          table.querySelectorAll('tbody tr').forEach(row => {
              row.classList.add('flex', 'flex-col');
              row.querySelectorAll('td').forEach(cell => {
                  cell.classList.add('flex', 'justify-between', 'items-center');
              });
          });
      } else {
          // Remove mobile-specific attributes
          table.querySelectorAll('tbody tr').forEach(row => {
              row.classList.remove('flex', 'flex-col');
              row.querySelectorAll('td').forEach(cell => {
                  cell.classList.remove('flex', 'justify-between', 'items-center');
              });
          });
      }
  }

  // Call on resize
  window.addEventListener('resize', adjustTableForMobile);
  
  // Initial call
  adjustTableForMobile();
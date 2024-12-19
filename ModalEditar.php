<!-- Modal -->
<div class="modal fade" id="editar<?php echo $mostrar['ID'] ?>" tabindex="-1" aria-labelledby="modalEdicion" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-3 shadow">
      <!-- Header -->
      <div class="modal-header border-bottom-0 bg-light">
        <h5 class="modal-title fw-bold" id="modalEdicion">
          <i class="bi bi-clock-history me-2"></i>Editar Registro de Horas
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <!-- Formulario -->
      <form method="POST" action="recib_Update.php" class="needs-validation">
        <div class="modal-body px-4 py-4">
          <input type="hidden" name="id" value="<?php echo $mostrar['ID'] ?>">

          <!-- Hora Ingreso -->
          <div class="mb-4">
            <label for="ingreso" class="form-label fw-semibold">
              <i class="bi bi-door-open me-2"></i>Hora de Ingreso
            </label>
            <div class="input-group">
              <input type="time"
                id="ingreso"
                name="ingreso"
                class="form-control form-control-lg"
                value="<?php echo $mostrar['Hora Ingreso'] ?>"
                required>
              <span class="input-group-text bg-light">
                <i class="fas fa-calendar-check"></i>

              </span>
            </div>
            <div class="form-text">Registra la hora de llegada al trabajo</div>
          </div>

          <!-- Inicio Almuerzo -->
          <div class="mb-4">
            <label for="colacion" class="form-label fw-semibold">
              <i class="bi bi-cup-hot me-2"></i>Inicio de Almuerzo
            </label>
            <div class="input-group">
              <input type="time"
                id="colacion"
                name="colacion"
                class="form-control form-control-lg"
                min="<?php echo $mostrar['Hora Ingreso'] ?>"
                value="<?php echo $mostrar['Hora Colacion'] ?>"
                required
                <?php echo (empty($mostrar['Hora Ingreso']) || $mostrar['Hora Ingreso'] === '00:00:00') ? 'disabled' : ''; ?>>
              <span class="input-group-text bg-light">
                <i class="fas fa-utensils"></i>

              </span>
            </div>
            <div class="form-text">Hora en que comienza tu pausa de almuerzo</div>
          </div>

          <!-- Fin Almuerzo -->
          <div class="mb-4">
            <label for="fin_colacion" class="form-label fw-semibold">
              <i class="bi bi-check2-circle me-2"></i>Fin de Almuerzo
            </label>
            <div class="input-group">
              <input type="time"
                id="fin_colacion"
                name="fin_colacion"
                class="form-control form-control-lg"
                min="<?php echo $mostrar['Hora Colacion'] ?>"
                value="<?php echo $mostrar['Hora Fin Colacion'] ?>"
                required
                <?php echo (empty($mostrar['Hora Colacion']) || $mostrar['Hora Colacion'] === '00:00:00') ? 'disabled' : ''; ?>>
              <span class="input-group-text bg-light">
                <i class="fas fa-play-circle"></i>
              </span>
            </div>
            <div class="form-text">Hora en que termina tu pausa de almuerzo</div>
          </div>

          <!-- Hora Salida -->
          <div class="mb-3">
            <label for="salida" class="form-label fw-semibold">
              <i class="bi bi-door-closed me-2"></i>Hora de Salida
            </label>
            <div class="input-group">
              <input type="time"
                id="salida"
                name="salida"
                class="form-control form-control-lg"
                min="<?php echo $mostrar['Hora Fin Colacion'] ?>"
                value="<?php echo $mostrar['Hora Salida'] ?>"
                required
                <?php echo (empty($mostrar['Hora Fin Colacion']) || $mostrar['Hora Fin Colacion'] === '00:00:00') ? 'disabled' : ''; ?>>
              <span class="input-group-text bg-light">
                <i class="fas fa-check-circle"></i>
              </span>
            </div>
            <div class="form-text">Registra la hora de término de tu jornada</div>
          </div>
        </div>

        <!-- Footer -->
        <div class="modal-footer border-top-0 px-4 pb-4">
          <button type="button"
            class="btn btn-light btn-lg px-4"
            data-bs-dismiss="modal">
            <i class="bi bi-x-lg me-2"></i>Cancelar
          </button>
          <button type="submit"
            class="btn btn-primary btn-lg px-4">
            <i class="bi bi-check-lg me-2"></i>Guardar Cambios
          </button>
        </div>
      </form>
    </div>
  </div>
</div>


<!-- Script para validación y mejora de UX -->

<script>
  /*
  document.addEventListener('DOMContentLoaded', function() {
    // Validación del formulario
    var forms = document.querySelectorAll('.needs-validation')
    Array.prototype.slice.call(forms)
      .forEach(function(form) {
        form.addEventListener('submit', function(event) {
          if (!form.checkValidity()) {
            event.preventDefault()
            event.stopPropagation()
          }
          form.classList.add('was-validated')
        }, false)
      })

    // Validación de secuencia de horas
    function validateTimeSequence() {
      const ingreso = document.getElementById('ingreso')
      const colacion = document.getElementById('colacion')
      const finColacion = document.getElementById('fin_colacion')
      const salida = document.getElementById('salida')

      if (ingreso.value) {
        colacion.disabled = false
        if (colacion.value) {
          finColacion.disabled = false
          if (finColacion.value) {
            salida.disabled = false
          }
        }
      }

      // Validar que las horas estén en orden cronológico
      const times = [ingreso, colacion, finColacion, salida]
      times.forEach((time, index) => {
        time.addEventListener('change', () => {
          if (index < times.length - 1 && time.value && times[index + 1].value) {
            if (time.value >= times[index + 1].value) {
              time.setCustomValidity('La hora debe ser anterior a la siguiente')
            } else {
              time.setCustomValidity('')
            }
          }
        })
      })
    }

    validateTimeSequence()
  })
  */
</script>
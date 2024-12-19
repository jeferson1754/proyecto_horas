<!-- Modal -->
<div class="modal fade" id="ingreso" tabindex="-1" aria-labelledby="modalIngreso" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-3 shadow">
      <!-- Header -->
      <div class="modal-header border-bottom-0 bg-light">
        <h5 class="modal-title fw-bold" id="modalIngreso">
          <i class="bi bi-calendar-check me-2"></i>Registro de Ingreso
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <!-- Formulario -->
      <form method="POST" action="recibCliente.php" class="needs-validation" novalidate>
        <div class="modal-body px-4 py-4">
          <!-- Campo Fecha -->
          <div class="mb-4">
            <label for="fecha" class="form-label fw-semibold">
              <i class="bi bi-calendar3 me-2"></i>Fecha
            </label>
            <input type="date"
              id="fecha"
              name="fecha"
              class="form-control form-control-lg"
              value="<?php echo $hoy ?>"
              required>
            <div class="invalid-feedback">
              Por favor seleccione una fecha
            </div>
          </div>

          <!-- Campo Hora -->
          <div class="mb-3">
            <label for="hora" class="form-label fw-semibold">
              <i class="bi bi-clock me-2"></i>Hora
            </label>
            <input type="time"
              id="hora"
              name="hora"
              class="form-control form-control-lg"
              value="<?php echo $hora_actual ?>"
              required>
            <div class="invalid-feedback">
              Por favor ingrese una hora válida
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div class="modal-footer border-top-0 px-4 pb-4">
          <button type="button"
            class="btn btn-light btn-lg px-4"
            data-bs-dismiss="modal">
            Cancelar
          </button>
          <button type="submit"
            class="btn btn-primary btn-lg px-4">
            <i class="bi bi-check2 me-2"></i>Guardar
          </button>
        </div>
      </form>

    </div>
  </div>
</div>

<!-- Script para validación -->
<script>
  // Ejemplo de validación de formulario
  (function() {
    'use strict'
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
  })()
</script>
<!-- Modal de Confirmación de Eliminación -->
<div class="modal fade" id="delete<?php echo $mostrar['ID'] ?>" tabindex="-1" aria-labelledby="modalDelete" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-3 shadow">
      <!-- Header -->
      <div class="modal-header border-bottom-0 bg-danger bg-opacity-10 px-4 py-3">
        <h5 class="modal-title text-danger fw-bold" id="modalDelete">
          <i class="bi bi-exclamation-triangle-fill me-2"></i>
          Confirmar Eliminación
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form method="POST" action="recib_Delete.php">
        <!-- Cuerpo del Modal -->
        <div class="modal-body text-center p-4">
          <input type="hidden" name="id" value="<?php echo $mostrar['ID'] ?>">

          <!-- Mensaje de Advertencia -->
          <div class="text-center mb-4">
            <div class="alert alert-warning border-0 bg-warning bg-opacity-10 mb-4">
              <p class="mb-0">¿Estás seguro de que deseas eliminar este registro? Esta acción no se puede deshacer.</p>
            </div>
          </div>

          <!-- Detalles del Registro -->
          <div class="card border-0 bg-light">
            <div class="card-body p-4">
              <!-- Fecha -->
              <div class="mb-3">
                <label class="text-muted small text-uppercase">Fecha del Registro</label>
                <p class="h5 mb-0">
                  <i class="bi bi-calendar3 me-2 text-primary"></i>
                  <?php echo date('d-m-Y', strtotime($mostrar['Dia'])); ?>
                </p>
              </div>

              <!-- Total Colación -->
              <div class="mb-3">
                <label class="text-muted small text-uppercase">Tiempo de Colación</label>
                <p class="h5 mb-0">
                  <i class="bi bi-clock-history me-2 text-primary"></i>
                  <?php
                  echo formatearHora($mostrar['Total Colacion']); // Salida: 40 mins
                  ?>
                </p>
              </div>

              <!-- Total Horas -->
              <div>
                <label class="text-muted small text-uppercase">Total Horas Trabajadas</label>
                <p class="h5 mb-0">
                  <i class="bi bi-clock me-2 text-primary"></i>
                  <?php
                  echo formatHoras($mostrar['Horas Final']); // Salida: 40 mins
                  ?>
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div class="modal-footer border-top-0 px-4 pb-4">
          <button type="button"
            class="btn btn-light btn-lg px-4"
            data-bs-dismiss="modal">
            <i class="bi bi-x-lg me-2"></i>
            Cancelar
          </button>
          <button type="submit"
            class="btn btn-danger btn-lg px-4">
            <i class="bi bi-trash3 me-2"></i>
            Eliminar Registro
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Estilos Adicionales -->

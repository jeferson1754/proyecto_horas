<div class="modal fade" id="fincolacion" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Fin Colacion</h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <?php


      $sql = "SELECT MAX(ID) FROM horas;";

      $result = mysqli_query($conexion, $sql);

      while ($valores = mysqli_fetch_array($result)) {
        $id = $valores[0];
      }

      ?>
      <form method="POST" action="recib_Update.php">
        <div class="modal-body">
          <div class="form-group">
            <input type="hidden" name="id" value="<?php echo $id ?>">
            <input type="hidden" name="accion" value="Fin_Colacion">
            <label for="recipient-name" class="col-form-label">Hora:</label>
            <input type="time" id="end-time-1" name="hora" class="form-control" required="true">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
          <button type="submit" class="btn btn-primary">Guardar</button>
        </div>
    </div>
    </form>
  </div>
</div>
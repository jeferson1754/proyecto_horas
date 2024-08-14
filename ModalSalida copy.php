<div class="modal fade" id="salida2" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Salida</h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <?php

      require 'bd.php';


      $sql = "SELECT DATE_FORMAT(NOW( ), '%H:%i:%S' );";

      $result = mysqli_query($conexion, $sql);

      while ($valores = mysqli_fetch_array($result)) {
        $hora1 = $valores[0];
      }

      ?>
      <form method="POST" action="recib_Update.php">
        <div class="modal-body">
          <div class="form-group">


            <div class="form-group">
              <label for="recipient-name" class="col-form-label">Fecha:</label>
              <input type="date" name="fecha" class="form-control" value="<?php echo $hoy ?>" required="true">
            </div>
            <div class="form-group">
              <label for="recipient-name" class="col-form-label">Hora:</label>
              <input type="time" name="hora" class="form-control" value="<?php echo $hora1 ?>" required="true">
            </div>
            <input type="hidden" name="accion" value="Salida2">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
          <button type="submit" class="btn btn-primary">Guardar</button>
        </div>
      </form>
    </div>
  </div>
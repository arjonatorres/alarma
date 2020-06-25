
        <div class="card text-white bg-info card-footer">
            <div class="card-header text-center">
                <button id="icono-estado-alarma" type="button" class="btn <?= $salida == '1'? $boton: 'btn-success' ?> btn-circle">
                    <i class="fas fa-lock"></i>
                </button>
                <img class="menu" style="width: 110px;" src="imagenes/home.png" onClick="casa()">
                
                <button type="button" class="btn btn-circle" style="right: 30px; position: absolute;">
                    <i class="fas fa-user"></i>
                </button>
            </div>
        </div>
      </div>
    </div>
  </body>
</html>
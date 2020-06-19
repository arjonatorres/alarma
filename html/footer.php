
        <div class="card text-white bg-info card-footer">
            <div class="card-header text-center">
                <button id="icono-estado-alarma" disabled type="button" class="btn <?= $salida == '1'? $boton: 'btn-success' ?> btn-circle">
                    <i class="fas fa-lock" style="margin-top: -8px;"></i>
                </button>
                <img class=menu style="width: 110px;margin-left: 160px;" src="imagenes/home.png" onClick="casa()">
                
                <img class=menu style="margin-right:50px;" align="right" width="100px" src="imagenes/refresh-icon.png" onClick="refresh()">
            </div>
        </div>
      </div>
    </div>
  </body>
</html>
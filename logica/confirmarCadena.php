<?php
//funcion para verificar que todos los valores en un array sean cadenas
function sonCadenas ($datos){
    foreach($datos as $dato){
      if (!is_string($dato)) {
          return false;
      }
    }
    return true;
}


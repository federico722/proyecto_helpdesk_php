<?php
//funcion para verificar que todos los valores en un array sean cadenas, valida que dentro de la cadena no incluya numeros
function sonCadenas ($datos){
    foreach($datos as $dato){
      if (!is_string($dato) ) {
          return false;
      }
    }
    return true;
}


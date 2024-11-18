<?php

//funcion para verificar que todos los valores en un array sean cadenas
function sonNumerico ($datos){
    foreach($datos as $dato){
      if (!is_numeric($dato)) {
          return false;
      }
    }
    return true;
}


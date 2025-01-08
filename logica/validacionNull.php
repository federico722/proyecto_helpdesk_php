<?php

function esCadenaValida($valores) {

    foreach($valores as $valor){
    // Verifica si es null o cadena vacía
      if ($valor === null || $valor === '') {
        continue;
      }

    // Verifica si es un string con números
    if (!is_string($valor)) {
        return false;
    }

    if (!preg_match('/[a-zA-Z]/', $valor)) {
        return false;
    }

    }

    return true;
 }
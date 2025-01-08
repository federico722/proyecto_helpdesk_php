<?php

function esNumericoNulo($valores) {

    foreach($valores as $valor){
    // Verifica si es null o cadena vacía
      if ($valor === null || $valor === '') {
        continue;
      }

    // Verifica si es numerico
    if (!ctype_digit(strval($valor) )) {
        return false;
    }

    }

    return true;
 }
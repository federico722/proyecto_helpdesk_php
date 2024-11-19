<?php

//funcion para validar el formato de la fecha
function validarFecha($fechas, $formato = 'Y-m-d'){
    foreach($fechas as $fecha){
        $d = DateTime::createFromFormat($formato, $fecha);
        if ($d && $d->format($formato) === $fecha) {
            continue;
        }else {
            return false;
        }
    }

    return true;
}


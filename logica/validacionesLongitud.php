<?php

function  validarArrayStrings($datos,$longitudMinima = 5 ,$longitudMaxima = 50){
    $errores = []; //almacenamos los errores registrados en las validaciones

    foreach($datos as $clave => $valor){
        // Elimina espacios en blanco al inicio y final
        $valorLimpio =  trim($valor);

        //validar longitud minima
        if (strlen($valorLimpio) < $longitudMinima) {
            $errores[] = "el campo '$clave' debe tener al menos $longitudMinima caracteres";
            continue;
        }

        //valida longitud maxima
        if (strlen($valorLimpio) > $longitudMaxima) {
            $errores[] = "El campo '$clave' no puede exceder $longitudMaxima caracteres";
            continue;
        }


        // Validar caracteres especiales
        if (!preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/', $valorLimpio)) {
            $errores[] = "El campo '$clave' solo puede contener letras y espacios";
            continue;
        }

        $datos[$clave] = $valorLimpio;
    }

    return [
        'valido' => empty($errores),
        'errores' => $errores,
        'datos' => $datos
    ];

}
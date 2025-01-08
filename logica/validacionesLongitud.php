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

function validarArrayStringsNumbers($datos,$longitudMinima = 5 ,$longitudMaxima = 50){
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
        if (!preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9\s]+$/', $valorLimpio)) {
            $errores[] = "El campo '$clave' solo puede contener letras espacios y numeros";
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

function validarArrayFlexible($datos, $longitudMinima = 5, $longitudMaxima = 50){
    $errores = [];

    foreach($datos as $clave => $valor){
        $valorLimpio = trim($valor);

        // Convertir a string para validación
        $valorLimpio = (string)$valorLimpio;

        // Validaciones más flexibles
        if (strlen($valorLimpio) < $longitudMinima) {
            $errores[] = "El campo '$clave' debe tener al menos $longitudMinima caracteres";
            continue;
        }

        if (strlen($valorLimpio) > $longitudMaxima) {
            $errores[] = "El campo '$clave' no puede exceder $longitudMaxima caracteres";
            continue;
        }

        // Regex más permisivo
        if (!preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9\s\-.:\/()@_,]+$/', $valorLimpio)) {
            $errores[] = "El campo '$clave' contiene caracteres no permitidos";
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
<?php

require_once '../../includes/verificando-sesion.class.php';
require_once '../../logica/formatoRespuesta.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //verifica si el token esta en el encabezado Authorization
    if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
        // Divide el contenido del encabezado
        //Authorization para obtener solo el token
        list($type, $token) = explode("", $_SERVER['HTTP_AUTHORIZATION'],2);

        if (strcasecmp($type, 'Bearer') === 0) {
        // llama a la funcion de verificacion con el token extraido
        Verificacion_sesion::verificacion($token);
        }else{
             sendResponse(400,
              ['Error' => "El tipo de token debe ser Bearer "]
        );
        }
    }else{
        sendResponse(401,
        ['Error'=> "Token de autorización no proporcionado"]
    );
    }
}else{
    sendResponse(405,
    ['error'=> "Método incorrecto"]
  );
}
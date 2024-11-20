<?php

require_once '..\..\includes\Ver-equipo.class.php';
require_once '../../logica/formatoRespuesta.php';


header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    // getallheaders() para obtener los encabezados
    $headers = getallheaders();

    //verifica el encabezado Authorization
    if (isset($headers['Authorization'])) {

        // Divide el contenido del encabezado
        list($type,$token)= explode(" ", $headers['Authorization'],2);

        if (strcasecmp($type, 'Bearer') === 0) {
            // Llama a la función de verificación con el token extraído
            View_equip::ver_equipo($token);
        }else {
            sendResponse(400, ['Error' => "El tipo de token debe ser Bearer"]);
        }
    }else{
        sendResponse(401,
        ['Error'=> "Token de autorizacion no proporcionado"]
    );
    }
}else{
    sendResponse(405,
    ['verificacion_tiempo_token'=> "funcionando correctamente"]
  );
}
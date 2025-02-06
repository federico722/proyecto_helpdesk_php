<?php

require_once '../../logica/formatoRespuesta.php';
require_once '..\..\includes\Eliminar_asociacion_cuenta.class.php';
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');  // Agregar OPTIONS
header('Access-Control-Allow-Credentials: true'); // Si es necesario para permitir credenciales

// Manejar solicitud OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Manejar solicitud OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {

    // getallheaders() para obtener los encabezados
    $headers = getallheaders();

    //verifica el encabezado Authorization
    if (isset($headers['Authorization'])) {

        // Divide el contenido del encabezado
        list($type,$token)= explode(" ", $headers['Authorization'],2);

        if (strcasecmp($type, 'Bearer') === 0) {
            // Llama a la función de verificación con el token extraído
            eliminar_asociaciones::delete_cuentas_asociadas($token,$_GET['id_equipo']);
        }else {
            sendResponse(400, ['Error' => "El tipo de token debe ser Bearer"]);
        }
    }else{
        sendResponse(401,
        ['Error'=> "Token de autorización no proporcionado"]
    );
    }
}else{
    sendResponse(405,
    ['Error'=> "Metodo incorrecto"]
);
}
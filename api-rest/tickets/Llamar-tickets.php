<?php

require '..\..\includes\View-tickets.class.php';
require_once '../../logica/formatoRespuesta.php';

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');  // Agregar OPTIONS
header('Access-Control-Allow-Credentials: true'); // Si es necesario para permitir credenciales

    // Manejar solicitud OPTIONS
     if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit();
    }



try {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {

        // getallheaders() para obtener los encabezados
        $headers = getallheaders();

        //verifica el encabezado Authorization
        if (isset($headers['Authorization'])) {

            // Divide el contenido del encabezado
            list($type,$token)= explode(" ", $headers['Authorization'],2);

            if (strcasecmp($type, 'Bearer') === 0) {
                 // Pasar estado como parámetro
                 $estado = $_GET['estado'] ?? null;
                 $nivel = $_GET['nivel'] ?? null;
                 $area = $_GET['area'] ?? null;
                 $desde = $_GET['desde'] ?? null;
                 $hasta = $_GET['hasta'] ?? null;
                // Llama a la función de verificación con el token extraído
                view_tickets::ver_tickets($token, $estado,$nivel, $area);
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
        ['Error'=> "metodo de consulta incorrecto"]
      );
    }
} catch (Exception $e) {
    sendResponse(500, [
        'error' => 'Error interno del servidor',
        'detalle' => $e->getMessage()
    ]);
}
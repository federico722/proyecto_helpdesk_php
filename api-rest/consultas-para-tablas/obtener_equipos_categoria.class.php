<?php
require_once '../../logica/formatoRespuesta.php';
require_once '..\..\includes\obtener-equipos-por-categoria.class.php';
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');  // Agregar OPTIONS
header('Access-Control-Allow-Credentials: true'); // Si es necesario para permitir credenciales

    // Manejar solicitud OPTIONS
     if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {

        // getallheaders() para obtener los encabezados
        $headers = getallheaders();
    
        //verifica el encabezado Authorization
        if (isset($headers['Authorization'])) {
    
            // Divide el contenido del encabezado
            list($type,$token)= explode(" ", $headers['Authorization'],2);
    
            if (strcasecmp($type, 'Bearer') === 0) {
                // Llama a la función de verificación con el token extraído
                Obteniendo_equipos_por_categoria::obtener_equipos_por_categoria($token, $_GET['fecha_actual']);
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


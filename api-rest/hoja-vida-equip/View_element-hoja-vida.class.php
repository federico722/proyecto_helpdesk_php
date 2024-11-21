<?php

require_once '..\..\includes\View-element-hoja-vida.class.php';
require_once '../../logica/formatoRespuesta.php';


header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Authorization');


try {
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id_equipo'])) {

        // getallheaders() para obtener los encabezados
        $headers = getallheaders();

        //verifica el encabezado Authorization
        if (isset($headers['Authorization'])) {

            // Divide el contenido del encabezado
            list($type,$token)= explode(" ", $headers['Authorization'],2);

            if (strcasecmp($type, 'Bearer') === 0) {
                // Llama a la función de verificación con el token extraído
                View_element_paper_life::ver_elementos_hoja_vida($token,$_GET['id_equipo']);
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
} catch (Exception $e) {
    sendResponse(500, [
        'error' => 'Error interno del servidor',
        'detalle' => $e->getMessage()
    ]);
}
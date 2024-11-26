<?php

require_once '../../includes/Create-user.class.php';
require_once '../../logica/formatoRespuesta.php';

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');  // Agregar OPTIONS
header('Access-Control-Allow-Credentials: true'); // Si es necesario para permitir credenciales

try {

    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        // Preflight request, se debe responder sin hacer nada más
        header('HTTP/1.1 200 OK');
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        Create_user::crear_usuario();
    }else {
        header('HTTP/1.1 405 Error');
        echo json_encode(["Error" => "Error metodo incorrecto"]);
    }
} catch (\Exception $e) {
    sendResponse(500, [
        'error' => 'Error interno del servidor',
        'detalle' => $e->getMessage()
    ]);
}

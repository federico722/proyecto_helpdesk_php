<?php
require_once '../../logica/formatoRespuesta.php';
require_once '..\..\includes\consultar_usuarios.class.php';
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
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        Obteniendo_los_agentes::obteniendo_agentes();
    }else{
        sendResponse(405,
        ['Error'=> "Metodo incorrecto"]
    );
    }

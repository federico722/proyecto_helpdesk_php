<?php

require '..\..\includes\Login-class.php';
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

  if ( $_SERVER['REQUEST_METHOD'] == 'POST') {
      Login::iniciar_sesion();
  }else {
      sendResponse(405,
      ['error'=> "Metodo incorrecto"]
    );
  }

} catch (\Exception $e) {
    sendResponse(500, [
      'error' => 'Error interno del servidor',
      'detalle' => $e->getMessage()
  ]);
}


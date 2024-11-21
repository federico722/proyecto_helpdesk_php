<?php

require_once '..\..\includes\Create-solicitud-cliente.class.php';
require_once '../../logica/formatoRespuesta.php';


header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Authorization');

try {

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    //verifica el encabezado Authorization
    Create_solicitud_cliente::crearSolicitudCliente();

    }else{
        sendResponse(405,
        ['Error'=> "Error en el metodo"]
      );
    }
} catch (Exception $e) {
    sendResponse(500, [
        'error' => 'Error interno del servidor',
        'detalle' => $e->getMessage()
    ]);
}
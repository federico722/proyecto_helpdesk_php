<?php

require '..\..\includes\Login-class.php';
require_once '../../logica/formatoRespuesta.php';

if ( $_SERVER['REQUEST_METHOD'] == 'POST') {
    Login::iniciar_sesion();
}else {
    sendResponse(405,
    ['error'=> "Metodo incorrecto"]
  );
}
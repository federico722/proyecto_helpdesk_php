<?php

require '..\..\includes\Delete-user.class.php';
require_once '../../logica/formatoRespuesta.php';

if($_SERVER['REQUEST_METHOD'] == 'DELETE' && isset($_GET['id_agente'])){
     Delete::delete_users($_GET['id_agente']);
}else {
    return sendResponse(405,
    ['error'=> "Metodo incorrecto"]
  );
}
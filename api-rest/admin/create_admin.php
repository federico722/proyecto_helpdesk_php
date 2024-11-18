<?php

require_once '../../includes/Create-admin.class.php';
require_once '../../logica/formatoRespuesta.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    Create_admin::crear_administrador();
}else {
  return sendResponse(405,
    ['error'=> "Metodo incorrecto"]
  );
}
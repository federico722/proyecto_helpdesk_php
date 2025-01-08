<?php

require_once '../../includes/Role.class.php';
require_once '../../logica/formatoRespuesta.php';

   if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_GET['role'])) {
       Role::create_role($_GET['role']);
   }else {
    return sendResponse(405,
    ['error'=> "Metodo incorrecto"]
  );
}

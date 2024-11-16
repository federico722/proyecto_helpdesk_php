<?php

require_once '../../includes/Create-user.class.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    Create_user::crear_usuario();
}else {
    header('HTTP/1.1 500 Error');
    echo json_encode(["Error" => "Error metodo incorrecto"]);
}
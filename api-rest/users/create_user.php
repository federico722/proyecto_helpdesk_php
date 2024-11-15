<?php

require_once '../../includes/Create-user.class.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $jwt = getallheaders();
    Login::iniciar_sesion($jwt);
}
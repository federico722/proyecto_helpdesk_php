<?php

require_once '..\..\includes\Login-class.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    Create_user::crear_usuario();
}
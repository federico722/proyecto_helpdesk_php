<?php

require_once '../../includes/Create-user.class.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_GET['usuario'])) {
    Create_user::crear_usuario($_GET['usuario'],);
}
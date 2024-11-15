<?php

require_once '../../includes/Create-admin.class.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    Create_admin::crear_administrador();
}
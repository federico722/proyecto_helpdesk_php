<?php
require_once 'Database.class.php';

$db = new Database();
if ($db->getConnection()) {
    echo "Conexión a la base de datos exitosa.";
} else {
    echo "Error en la conexión a la base de datos.";
}

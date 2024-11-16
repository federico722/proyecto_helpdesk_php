<?php

require_once __DIR__ . '..\..\includes\Database.class.php';

function consultarContraseña($usuario){
    try {
        $database = new Database();
        $conn = $database->getConnection();
        $stmt = $conn->prepare('SELECT contrasena_agente FROM AGENTES WHERE usuario_agente = :usuario');
        $stmt->bindParam(':usuario', $usuario);

        if ($stmt->execute()) {
            $resultado =  $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado ? $resultado['contrasena_agente'] : false ;
        }else {
            return false; // Error en la ejecución de la consulta
        }

    } catch (PDOException $e) {
        //manejo de errores en la base de datos
        error_log('Error en la consulta de la contraseña: ') . $e->getMessage();
        return false; // Error en la consulta
    }
}
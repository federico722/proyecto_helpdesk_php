<?php

require_once __DIR__ . "../../vendor/autoload.php";
require_once __DIR__ . '..\..\includes\Database.class.php';

function consultarRol2($usuario){
    try{

        $database = new Database();
        $conn = $database->getConnection();
        $stmt = $conn->prepare('SELECT a.usuario_agente, r.nombre_rol FROM agentes a JOIN roles r ON a.id_rol = r.id_rol WHERE a.usuario_agente = :usuario');
        $stmt->bindParam(':usuario', $usuario);

        if ($stmt->execute()) {
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Verificamos si se obtuvo un resultado
            if ($resultado) {
                $rol = $resultado['nombre_rol'];

                // Retornamos true si el rol es "admin", de lo contrario false
                return $rol === "admin";
            } else {
                return false; // Usuario no encontrado
            }
        } else {
            return false; // Error en la ejecución de la consulta
        }


    }catch(\Throwable $e){
    // Manejo de errores en la base de datos
    error_log('Error en la consulta: ' . $e->getMessage());
    return false; // Error en la consulta
    }
}
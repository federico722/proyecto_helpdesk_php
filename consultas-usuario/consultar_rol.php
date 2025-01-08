<?php


require_once __DIR__ . '..\..\includes\Database.class.php';

function consultarRol($usuario_agente){
    try {
        $database = new Database();
        $conn = $database->getConnection();
        $stmt = $conn->prepare('SELECT ROLES.nombre_rol, AGENTES.usuario_agente FROM AGENTES LEFT JOIN ROLES ON AGENTES.id_rol = ROLES.id_rol WHERE AGENTES.usuario_agente = :usuario_agente');
        $stmt->bindParam(':usuario_agente', $usuario_agente);

        if ($stmt->execute()) {
            $resultado =  $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado ? $resultado['nombre_rol'] : false;
        }else {
            return false; // Error en la ejecución de la consulta
        }


    } catch (PDOException $e) {
        //manejo de errores en la base de datos
        error_log('Error en la consulta de la contraseña: ') . $e->getMessage();
        return false; // Error en la consulta
    }

}
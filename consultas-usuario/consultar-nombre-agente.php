<?php

require_once __DIR__ . '..\..\includes\Database.class.php';

function consultarAgente($nombre_agente){

    try {

        $database = new Database();
        $conn = $database->getConnection();
        $stmt = $conn->prepare('SELECT id_agente FROM AGENTES WHERE usuario_agente = :nombre_agente');
        $stmt->bindParam(':nombre_agente', $nombre_agente);

        if ($stmt->execute()) {
            $resultado =  $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado ? $resultado['id_agente'] : false ;
        }else {
            return false; // Error en la ejecución de la consulta
        }
    } catch (\PDOException $e) {
          //manejo de errores en la base de datos
          error_log('Error en la consulta del agente: ') . $e->getMessage();
          return false; // Error en la consulta
    }


}
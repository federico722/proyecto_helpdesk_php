<?php

require_once __DIR__ . '..\..\includes\Database.class.php';

function consultarNombreArea($nombre_area,$id_area){
    try {
        $database = new Database();
        $conn = $database->getConnection();
        $stmt = $conn->prepare('SELECT nombre_area FROM AREAS WHERE id_area = :id_area AND nombre_area = :nombre_area');
        $stmt->bindParam(':id_area', $id_area);
        $stmt->bindParam(':nombre_area', $nombre_area);

        if ($stmt->execute()) {

            $resultado =  $stmt->fetch(PDO::FETCH_ASSOC);

            return $resultado ? false : true;
        }else {
            return false; // Error en la ejecución de la consulta
        }

    } catch (PDOException $e) {
        //manejo de errores en la base de datos
        error_log('Error en la consulta del area: ') . $e->getMessage();
        return false; // Error en la consulta
    }
}
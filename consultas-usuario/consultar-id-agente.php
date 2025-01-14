<?php

require_once __DIR__ . '..\..\includes\Database.class.php';

function consultarIdEquipo($nombre_equipo){

    try {

        $database = new Database();
        $conn = $database->getConnection();
        $stmt = $conn->prepare('SELECT id_equipo FROM EQUIPOS WHERE nombre_equipo = :nombre_equipo');
        $stmt->bindParam(':nombre_equipo', $nombre_equipo);

        if ($stmt->execute()) {
            $resultado =  $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado ? $resultado['id_equipo'] : false ;
        }else {
            return false; // Error en la ejecución de la consulta
        }
    } catch (\PDOException $e) {
          //manejo de errores en la base de datos
          error_log('Error en la consulta del id equipo: ') . $e->getMessage();
          return false; // Error en la consulta
    }


}
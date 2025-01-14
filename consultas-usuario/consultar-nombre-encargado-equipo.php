<?php

require_once __DIR__ . '..\..\includes\Database.class.php';

function consultarNombreEncargadoEquipo($nombre_encargado_equipo){

    try {

        $database = new Database();
        $conn = $database->getConnection();
        $stmt = $conn->prepare('SELECT id_equipo FROM EQUIPOS WHERE nombre_encargado_equipo = :nombre_encargado_equipo');
        $stmt->bindParam(':nombre_encargado_equipo', $nombre_encargado_equipo);

        if ($stmt->execute()) {
            $resultado =  $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado ? $resultado['id_equipo'] : false ;
        }else {
            return false; // Error en la ejecución de la consulta
        }
    } catch (\PDOException $e) {
          //manejo de errores en la base de datos
          error_log('Error en la consulta del agente: ') . $e->getMessage();
          return false; // Error en la consulta
    }


}
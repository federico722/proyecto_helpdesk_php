<?php

require_once __DIR__ . '..\..\includes\Database.class.php';

//funcion que analiza si hay licencias y servicios utilizando el nombre del equipo retorna true si no hay, y falso si hay licencias y servicios

function consultar_licencias_servicios($nombre_equipo){
    try {
        
        $database = new Database();
        $conn = $database->getConnection();
        $stmt = $conn->prepare('CALL ObtenerServiciosYLicenciasPorEquipo(:nombre_equipo)');
        $stmt->bindParam(':nombre_equipo', $nombre_equipo);

        if ($stmt->execute()) {

            $resultado =  $stmt->fetch(PDO::FETCH_ASSOC);

            return $resultado ? false : true;
        }else {
            return false; // Error en la ejecución de la consulta
        }
    } catch (\Throwable $e) {
     //manejo de errores en la base de datos
     error_log('Error en la consulta de la licencia y servicio: ') . $e->getMessage();
     return false; // Error en la consulta
    }
}
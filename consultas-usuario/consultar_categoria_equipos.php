<?php

require_once __DIR__ . '..\..\includes\Database.class.php';

//funcion que analiza si hay equipos en una categoria  retorna true si no hay, y falso si hay equipos
function consultar_equipos_categoria($nombre_categoria){
    try {
        $database = new Database();
        $conn = $database->getConnection();
        $stmt = $conn->prepare('CALL ObtenerEquiposDetalladoPorCategoria(:nombre_categoria)');
        $stmt->bindParam(':nombre_categoria', $nombre_categoria);

        if ($stmt->execute()) {

            $resultado =  $stmt->fetch(PDO::FETCH_ASSOC);

            return $resultado ? false : true;
        }else {
            return false; // Error en la ejecución de la consulta
        }
    } catch (\Throwable $e) {
     //manejo de errores en la base de datos
     error_log('Error en la consulta del area: ') . $e->getMessage();
     return false; // Error en la consulta
    }
}
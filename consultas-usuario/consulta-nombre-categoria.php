<?php

require_once __DIR__ . '..\..\includes\Database.class.php';

function consultarCategoriaNombre($nombre_categoria){

    try {

        $database = new Database();
        $conn = $database->getConnection();
        $stmt = $conn->prepare('SELECT id_categoria FROM CATEGORIAS WHERE nombre_categoria = :nombre_categoria');
        $stmt->bindParam(':nombre_categoria', $nombre_categoria);

        if ($stmt->execute()) {
            $resultado =  $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado ? true : false ;
        }else {
            return false; // Error en la ejecución de la consulta
        }
    } catch (\PDOException $e) {
          //manejo de errores en la base de datos
          error_log('Error en la consulta de la categoria: ') . $e->getMessage();
          return false; // Error en la consulta
    }


}
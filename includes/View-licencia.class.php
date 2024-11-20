<?php

require_once 'Database.class.php';
require_once __DIR__ . '..\..\logica\confirPassword.php';
require_once __DIR__ . '..\..\logica\confirmarCadena.php';
require_once __DIR__ . '..\..\consultas-usuario\Consultar_contrasena.php';
require_once __DIR__ . '..\..\logica\verificarContrasena.php';
require_once __DIR__ . '..\..\vendor/autoload.php';
require_once __DIR__ . '..\..\credentials\create-token.php';
require_once __DIR__ . '..\..\credentials\obtener-payload-token.php';
require_once __DIR__ . '..\..\logica\formatoRespuesta.php';
require_once __DIR__ . '..\..\logica\confirmarInt.php';
require_once __DIR__ . '..\..\credentials\verificar-token.php';
require_once __DIR__ . '..\..\logica\validacionesLongitud.php';
require_once __DIR__ . '..\..\logica\confirmarFecha.php';

class View_licencia{
    public static function ver_licencia($token, $id_equipo){

         // Verificar si los datos necesarios están presentes
         if (!isset($id_equipo)) {
            return sendResponse(400, ["Error" => "Faltan datos en la solicitud"]);
        }

        //verifica que el token no haya vencido
        if (!verificarToken($token)) {
            return sendResponse(400, ["Error" => "el token expiro"]);
        }


         if (!sonNumerico([$id_equipo])) {
            return sendResponse(400, [
            "Error" => "Datos invalidos",
            ]);
        }


        try {
            $database = new Database();
            $conn = $database->getConnection();
            $stmt = $conn->prepare('SELECT nombre_licencia FROM LICENCIAS WHERE id_equipo = :id_equipo');
            $stmt->bindParam(':id_equipo',$id_equipo);

            if($stmt->execute()){
                // Obtener todos los resultados
                $licencia = $stmt->fetchAll(PDO::FETCH_COLUMN);

                // Responder con los datos de categorías
            return sendResponse(200, [
                "licencias" => $licencia,
                "total" => count($licencia)
            ]);
               }else{
                   // Responder con error 500 si la inserción falla
                return sendResponse(500, ["error" => "No se pudo obtener los nombres de licencias"]);
              }
        } catch (\Throwable $th) {
            error_log('Error al obtener el nombre de las licencias: ' . $th->getMessage());
            return sendResponse(500, [
                "error" => "ocurrio un error interno del servidor",
                "detalles" => $th->getMessage()
         ]);
        }

    }
}
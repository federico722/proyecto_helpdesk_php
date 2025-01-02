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
        try {
        // Verificar si los datos necesarios están presentes
         if (!isset($id_equipo)) {
            return sendResponse(400, ["Error" => "Faltan datos en la solicitud"]);
        }

        // Verifica que el token no haya vencido
        $tokenValidation = validarTokenEnClase($token);

        if (!$tokenValidation ) {
            return sendResponse(400, ["Error" => "Token vencido"]);
        }


         if (!sonNumerico([$id_equipo])) {
            return sendResponse(400, [
            "Error" => "Datos invalidos",
            ]);
        }


            $database = new Database();
            $conn = $database->getConnection();
            $stmt = $conn->prepare('SELECT id_licencia, nombre_licencia FROM LICENCIAS WHERE id_equipo = :id_equipo');
            $stmt->bindParam(':id_equipo',$id_equipo);

            if($stmt->execute()){
                // Obtener todos los resultados
                $licencia = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
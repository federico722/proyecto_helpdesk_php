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

class view_category{
    public static function ver_categoria($token){

        try {
            //verifica que el token no haya vencido
            $tokenValidation = validarTokenEnClase($token);

            if (!$tokenValidation ) {
                return sendResponse(400, ["Error" => "Token vencido"]);
            }


            $database = new Database();
            $conn = $database->getConnection();
            $stmt = $conn->prepare('SELECT id_categoria, nombre_categoria FROM CATEGORIAS');

            if($stmt->execute()){
                // Obtener todos los resultados
                $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Responder con los datos de categorías
            return sendResponse(200, [
                "categorias" => $categorias,
                "total" => count($categorias)
            ]);
               }else{
                   // Responder con error 500 si la inserción falla
                return sendResponse(500, ["error" => "No se pudo obtener los nombres de categorias"]);
              }
        } catch (\Throwable $th) {
            error_log('Error al obtener las categorias: ' . $th->getMessage());
            return sendResponse(500, [
                "error" => "ocurrio un error interno del servidor",
                "detalles" => $th->getMessage()
         ]);
        }

    }
}
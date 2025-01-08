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

class Create_area{

    public static function crearArea($token){
        try {
             // Obtener el cuerpo de la solicitud en formato JSON
        $data = json_decode(file_get_contents("php://input"), true);

            //verifica que el token no haya vencido
            $tokenValidation = validarTokenEnClase($token);

            if (!$tokenValidation ) {
                return sendResponse(400, ["Error" => "Token vencido"]);
            }

        // Verificar si los datos necesarios están presentes
        if (!isset($data['nombre_area'])) {
        return sendResponse(400, ["Error" => "Faltan datos en la solicitud"]);
        }

        // Obtengo los datos del formato json
        $nombre_area = $data['nombre_area'];



        // verificar si son cadenas
        if (!sonCadenas([$nombre_area])) {
            return sendResponse(400, [
                "Error" => "Datos invalidos",
                ]);
        }


        $database = new Database();
        $conn = $database->getConnection();
        $stmt = $conn->prepare('INSERT INTO AREAS (nombre_area) VALUES(:nombre_area)');
        $stmt->bindParam(':nombre_area',$nombre_area);


        if($stmt->execute()){
        // Responder con éxito
        return sendResponse(200, ["success" => "Area guardada con exito."]);
        }else{
            // Responder con error 500 si la inserción falla
        return sendResponse(500, ["error" => "No se pudo guardar la Area"]);
        }

        } catch (\Throwable $th) {
                      error_log('Error al agregar el area: ' . $th->getMessage());
            return sendResponse(500, [
                "error" => "ocurrio un error interno del servidor",
                "detalles" => $th->getMessage()
         ]);
        }
    }

}

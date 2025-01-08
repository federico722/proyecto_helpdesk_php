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

class Create_level{

    public static function crearNivel($token){
        try {
             // Obtener el cuerpo de la solicitud en formato JSON
        $data = json_decode(file_get_contents("php://input"), true);

            //verifica que el token no haya vencido
            $tokenValidation = validarTokenEnClase($token);

            if (!$tokenValidation ) {
                return sendResponse(400, ["Error" => "Token vencido"]);
            }

        // Verificar si los datos necesarios están presentes
        if (!isset($data['nombre_nivel'], $data['descripcion_nivel'])) {
        return sendResponse(400, ["Error" => "Faltan datos en la solicitud"]);
        }

        // Obtengo los datos del formato json
        $nombre_nivel = $data['nombre_nivel'];
        $descripcion_nivel	= $data['descripcion_nivel'];



        // verificar si son cadenas
        if (!sonCadenas([$nombre_nivel, $descripcion_nivel])) {
            return sendResponse(400, [
                "Error" => "Datos invalidos",
                ]);
        }


        $database = new Database();
        $conn = $database->getConnection();
        $stmt = $conn->prepare('INSERT INTO NIVELES (nombre_nivel,descripcion_nivel ) VALUES(:nombre_nivel, :descripcion_nivel)');
        $stmt->bindParam(':nombre_nivel',$nombre_nivel);
        $stmt->bindParam(':descripcion_nivel',$descripcion_nivel);



        if($stmt->execute()){
        // Responder con éxito
        return sendResponse(200, ["success" => "Nivel guardada con exito."]);
        }else{
            // Responder con error 500 si la inserción falla
        return sendResponse(500, ["error" => "No se pudo guardar el nivel"]);
        }

        } catch (\Throwable $th) {
                      error_log('Error al agregar el nivel: ' . $th->getMessage());
            return sendResponse(500, [
                "error" => "ocurrio un error interno del servidor",
                "detalles" => $th->getMessage()
         ]);
        }
    }

}

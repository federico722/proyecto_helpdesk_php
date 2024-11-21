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

class Create_etiqueta{

    public static function crearEtiqueta($token){
        try {
             // Obtener el cuerpo de la solicitud en formato JSON
        $data = json_decode(file_get_contents("php://input"), true);

            //verifica que el token no haya vencido
            $tokenValidation = validarTokenEnClase($token);

            if (!$tokenValidation ) {
                return sendResponse(400, ["Error" => "Token vencido"]);
            }

        // Verificar si los datos necesarios están presentes
        if (!isset($data['nombre_etiqueta'], $data['descripcion_etiqueta'])) {
        return sendResponse(400, ["Error" => "Faltan datos en la solicitud"]);
        }

        // Obtengo los datos del formato json
        $nombre_etiqueta = $data['nombre_etiqueta'];
        $descripcion_etiqueta	= $data['descripcion_etiqueta'];



        // verificar si son cadenas
        if (!sonCadenas([$nombre_etiqueta, $descripcion_etiqueta])) {
            return sendResponse(400, [
                "Error" => "Datos invalidos",
                ]);
        }


        $database = new Database();
        $conn = $database->getConnection();
        $stmt = $conn->prepare('INSERT INTO ETIQUETAS (nombre_etiqueta,descripcion_etiqueta ) VALUES(:nombre_etiqueta, :descripcion_etiqueta)');
        $stmt->bindParam(':nombre_etiqueta',$nombre_etiqueta);
        $stmt->bindParam(':descripcion_etiqueta',$descripcion_etiqueta);



        if($stmt->execute()){
        // Responder con éxito
        return sendResponse(200, ["success" => "Etiqueta guardada con exito."]);
        }else{
            // Responder con error 500 si la inserción falla
        return sendResponse(500, ["error" => "No se pudo guardar la Etiqueta"]);
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

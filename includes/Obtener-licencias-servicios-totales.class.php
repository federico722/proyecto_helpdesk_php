<?php

require_once 'Database.class.php';
require_once __DIR__ . '..\..\logica\confirPassword.php';
require_once __DIR__ . '..\..\logica\confirmarCadena.php';
require_once __DIR__ . '..\..\vendor/autoload.php';
require_once __DIR__ . '..\..\logica\formatoRespuesta.php';
require_once __DIR__ . '..\..\logica\confirmarInt.php';
require_once __DIR__ . '..\..\credentials\verificar-token.php';
require_once __DIR__ . '..\..\logica\validacionesLongitud.php';
require_once __DIR__ . '..\..\logica\confirmarFecha.php';

class Obtener_servicios_licencias_totales{
    public static function obtener_licencias_servicios_totales($token){
        try {
        //verifica que el token no haya vencido
        $tokenValidation = validarTokenEnClase($token);

        if (!$tokenValidation ) {
             return sendResponse(400, ["ErrorToken" => "Token vencido"]);
        }

        $database = new Database();
        $conn = $database->getConnection();
        $stmt = $conn->prepare('CALL ObtenerEquiposLicenciasServicios()');

        if($stmt->execute()){
            // Obtener todos los resultados
            $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
          // Responder con los datos de categorías
        return sendResponse(200, [
          "resultado" => $resultado,
        ]);
         }else{
             // Responder con error 500 si la inserción falla
          return sendResponse(500, ["errorInterno" => "No se pudo obtener el total de las licencias y servicios"]);
        }

        } catch (\Throwable $th) {
            error_log('Error al obtener el total de las licencias y servicios: ' . $th->getMessage());
            return sendResponse(500, [
                "error" => "ocurrio un error interno del servidor",
                "detalles" => $th->getMessage()
            ]);
        }
        
    }
}
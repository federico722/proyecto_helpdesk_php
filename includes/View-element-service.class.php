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

class View_element_servicio{
    public static function ver_elementos_servicio($token, $nombre_servicio){
        try {
        // Verificar si los datos necesarios están presentes
         if (!isset($nombre_servicio)) {
            return sendResponse(400, ["Error" => "Faltan datos en la solicitud"]);
        }

        // Verifica que el token no haya vencido
        $tokenValidation = validarTokenEnClase($token);

        if (!$tokenValidation ) {
            return sendResponse(400, ["Error" => "Token vencido"]);
        }

        // verificar si son cadenas
        $camposValidar = [
            'nombre_servicio' => $nombre_servicio];

         //validar los campos
         $resultado = validarArrayFlexible($camposValidar, 1, 1000);

         if (!$resultado['valido']) {
            return sendResponse(400, [
            "Error" => "Datos invalidos",
            "detalles" => $resultado['errores']
            ]);
        }

        $nombre_servicio = $resultado['datos']['nombre_servicio'];

            $database = new Database();
            $conn = $database->getConnection();
            $stmt = $conn->prepare('SELECT * FROM SERVICIOS WHERE nombre_servicio = :nombre_servicio');
            $stmt->bindParam(':nombre_servicio',$nombre_servicio);

            if($stmt->execute()){
                // Obtener todos los resultados
                $elementos_equipo = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Responder con los datos de categorías
            return sendResponse(200, [
                "elementos_equipo" => $elementos_equipo
            ]);
               }else{
                   // Responder con error 500 si la inserción falla
                return sendResponse(500, ["error" => "No se pudo obtener los nombres de servicios"]);
              }
        } catch (\Throwable $th) {
            error_log('Error al obtener el nombre de las servicios: ' . $th->getMessage());
            return sendResponse(500, [
                "error" => "ocurrio un error interno del servidor",
                "detalles" => $th->getMessage()
         ]);
        }

    }
}
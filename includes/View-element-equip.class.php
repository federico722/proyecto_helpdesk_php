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

class view_element_equip{
    public static function ver_elementos_equipos($token,$nombre_equipo){

        try {

         // Verificar si los datos necesarios están presentes
         if (!isset($nombre_equipo)) {
            return sendResponse(400, ["Error" => "Faltan datos en la solicitud"]);
        }

        //verifica que el token no haya vencido
        $tokenValidation = validarTokenEnClase($token);

        if (!$tokenValidation ) {
            return sendResponse(400, ["Error" => "Token vencido"]);
        }


        // verificar si son cadenas
        $camposValidar = [
            'nombre_equipo' => $nombre_equipo,];

         //validar los campos
         $resultado = validarArrayFlexible($camposValidar, 1, 1000);

         if (!$resultado['valido']) {
            return sendResponse(400, [
            "Error" => "Datos invalidos",
            "detalles" => $resultado['errores']
            ]);
        }

        $nombre_equipo = $resultado['datos']['nombre_equipo'];


            $database = new Database();
            $conn = $database->getConnection();
            $stmt = $conn->prepare('SELECT EQUIPOS.*, AGENTES.usuario_agente FROM EQUIPOS LEFT JOIN AGENTES ON EQUIPOS.id_agente = AGENTES.id_agente WHERE EQUIPOS.nombre_equipo = :nombre_equipo');
            $stmt->bindParam(':nombre_equipo',$nombre_equipo);

            if($stmt->execute()){
                // Obtener todos los resultados
                $elementos_equipo = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Responder con los datos de categorías
            return sendResponse(200, [
                "elementos_equipo" => $elementos_equipo
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
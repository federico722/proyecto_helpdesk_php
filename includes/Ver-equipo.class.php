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

class View_equip{
    public static function ver_equipo($token, $categoria){

        try {
        //verifica que el token no haya vencido
        $tokenValidation = validarTokenEnClase($token);

        if (!$tokenValidation ) {
                return sendResponse(400, ["Error" => "Token vencido"]);
        }

        // Verificar si los datos necesarios están presentes
        if (!isset($categoria)) {
            return sendResponse(400, ["Error" => "Faltan datos en la solicitud"]);
        }



            // verificar si son cadenas
        $camposValidar = [
            'categoria' => $categoria];

         //validar los campos
         $resultado = validarArrayFlexible($camposValidar, 1, 1000);

         if (!$resultado['valido']) {
            return sendResponse(400, [
            "Error" => "Datos invalidos",
            "detalles" => $resultado['errores']
            ]);
        }

        $nombre_categoria = $resultado['datos']['categoria'];

            $database = new Database();
            $conn = $database->getConnection();
            $stmt = $conn->prepare('CALL ObtenerEquiposPorCategoria(:nombre_categoria)');
            $stmt->bindParam(':nombre_categoria',$nombre_categoria);

            if($stmt->execute()){
                // Obtener todos los resultados
                $equipos = $stmt->fetchAll(PDO::FETCH_COLUMN);

                // Responder con los datos de categorías
            return sendResponse(200, [
                "equipos" => $equipos,
                "total" => count($equipos)
            ]);
               }else{
                   // Responder con error 500 si la inserción falla
                return sendResponse(500, ["error" => "No se pudo obtener los nombres de equipos"]);
              }
        } catch (\Throwable $th) {
            error_log('Error al obtener los equipos: ' . $th->getMessage());
            return sendResponse(500, [
                "error" => "ocurrio un error interno del servidor",
                "detalles" => $th->getMessage()
         ]);
        }

    }
}
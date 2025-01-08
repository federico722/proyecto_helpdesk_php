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

class Obteniendo_equipos_por_categoria {
    //obtiene la fecha del front y devuelve la consulta que incluye las categorias
    public static function obtener_equipos_por_categoria($token, $fecha_actual){

    try {
           //verifica que el token no haya vencido
     $tokenValidation = validarTokenEnClase($token);

     if (!$tokenValidation ) {
             return sendResponse(400, ["ErrorToken" => "Token vencido"]);
     }

    // Verificar si los datos necesarios están presentes
    if (!isset($fecha_actual)) {
        return sendResponse(400, ["Error400FaltanDatos" => "Faltan datos en la solicitud"]);
      }

    $database = new Database();
    $conn = $database->getConnection();
    $stmt = $conn->prepare(' CALL ObtenerCategoriasConCantidadEquipos(:fecha_actual)');
    $stmt->bindParam(':fecha_actual',$fecha_actual);
    
    if($stmt->execute()){
        // Obtener todos los resultados
        $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
      // Responder con los datos de categorías
    return sendResponse(200, [
      "resultado" => $resultado,
    ]);
     }else{
         // Responder con error 500 si la inserción falla
      return sendResponse(500, ["errorInterno" => "No se pudo obtener las categorias y los equipos"]);
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
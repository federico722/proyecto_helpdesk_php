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
require_once __DIR__ . '..\..\consultas-usuario\consultar-nombre-encargado-equipo.php';
require_once __DIR__ . '..\..\consultas-usuario\consultar-id-agente.php';


class Editar_el_responsable_del_equipo{
    public static function editar_responsable($token){

    try {
    // Obtener el cuerpo de la solicitud en formato JSON
      $data = json_decode(file_get_contents("php://input"), true);

    //verifica que el token no haya vencido
     $tokenValidation = validarTokenEnClase($token);

     if (!$tokenValidation ) {
        return sendResponse(400, ["ErrorToken" => "Token vencido"]);
     }

    // Verificar si los datos necesarios están presentes
    if (!isset($data['nombre_equipo'], $data['nombre_encargado_equipo'] )) {
        return sendResponse(400, ["Error400FaltanDatos" => "Faltan datos en la solicitud"]);
    }
    
    $nombre_equipo = $data['nombre_equipo'];
    $nombre_encargado_equipo = $data['nombre_encargado_equipo'];

    $existeResponsable = consultarNombreEncargadoEquipo($nombre_encargado_equipo);
    if ($existeResponsable) {
        return sendResponse(400, ["ErrorExisteResponsable" => "El responsable esta asociado al equipo", "Error" => $existeResponsable ]);
    }


    $consultarElIdDelEquipo= consultarIdEquipo($nombre_equipo);
    if (!$consultarElIdDelEquipo) {
        return sendResponse(400, ["ErrorAlObtenerIdEquipo" => "No se pudo obtener el id equipo"]);
    }
    $id_equipo = $consultarElIdDelEquipo;

    $database = new Database();
    $conn = $database->getConnection();
    $stmt = $conn->prepare(' CALL CambiarEncargadoEquipo(:id_equipo, :nombre_encargado_equipo)');
    $stmt->bindParam(':id_equipo',$id_equipo);
    $stmt->bindParam(':nombre_encargado_equipo',$nombre_encargado_equipo);

    if($stmt->execute()){
        // Obtener todos los resultados
        $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
      // Responder con los datos de categorías
    return sendResponse(200, [
      "resultado" => $resultado,
    ]);
     }else{
         // Responder con error 500 si la inserción falla
      return sendResponse(500, ["errorInterno" => "No se pudo obtener las licencias y servicios del equipo"]);
    }
    } catch (\Throwable $th) {
        error_log('Error al actualizar el responsable ' . $th->getMessage());
        return sendResponse(500, [
            "error" => "ocurrio un error interno del servidor",
            "detalles" => $th->getMessage()
        ]);
    }

    }
}
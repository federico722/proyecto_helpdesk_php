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


class eliminar_asociaciones{
    public static function delete_cuentas_asociadas($token, $id_equipo){
        
    try {

    //verifica que el token no haya vencido
    $tokenValidation = validarTokenEnClase($token);

    if (!$tokenValidation ) {
        return sendResponse(400, ["ErrorToken" => "Token vencido"]);
    }

    if (!isset( $id_equipo)) {
        return sendResponse(400, ["Error" => "Faltan datos en la solicitud"]);
    }

    $database = new Database();
    $conn = $database->getConnection();
    $stmt = $conn->prepare('DELETE from asignacion_sitios WHERE id_equipo = :id_equipo');
    $stmt->bindParam(':id_equipo',$id_equipo);

    if($stmt->execute()){

    return sendResponse(200, [
      "succes" => 'Credenciales eliminadas',
    ]);

     }else{
         // Responder con error 500 si la inserción falla
      return sendResponse(500, ["errorInterno" => "No se pudo eliminar las credenciales"]);
    }


    } catch (\Throwable $th) {
        error_log('Error al obtener los datos de las credenciales: ' . $th->getMessage());
        return sendResponse(500, [
            "error" => "ocurrio un error interno del servidor",
            "detalles" => $th->getMessage()
        ]);
    }

    
    }
}
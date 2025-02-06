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

class Obteniendo_los_agentes{
    public static function obteniendo_agentes(){
    try {
    
   
    $database = new Database();
    $conn = $database->getConnection();
    $stmt = $conn->prepare('
        SELECT 
            agentes.id_agente, 
            agentes.usuario_agente, 
            agentes.contrasena_agente, 
            agentes.correo_agente, 
            agentes.id_rol, 
            agentes.reset_token, 
            roles.nombre_rol
        FROM 
            agentes
        JOIN 
            roles ON agentes.id_rol = roles.id_rol
        WHERE 
            roles.nombre_rol IN ("admin", "user")
   ');
   
    if($stmt->execute()){
        // Obtener todos los resultados
        $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
      // Responder con los datos de categorías
    return sendResponse(200, [
      "success" => $resultado,
    ]);
     }else{
         // Responder con error 500 si la inserción falla
      return sendResponse(500, ["errorInterno" => "No se pudo obtener los agentes"]);
    }

    } catch (\Throwable $th) {
        error_log('Error al obtener los agentes: ' . $th->getMessage());
        return sendResponse(500, [
            "error" => "ocurrio un error interno del servidor",
            "detalles" => $th->getMessage()
        ]);
    }
    }
}
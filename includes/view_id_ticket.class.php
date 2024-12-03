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

class view_ticket_id{
    public static function ver_ticket_id($token,$id ){

        try {
        //verifica que el token no haya vencido
        $tokenValidation = validarTokenEnClase($token);
        if (!$tokenValidation ) {
            return sendResponse(400, ["Error" => "Token vencido"]);
        }


        // Corrección: Usar los parámetros directamente
        $idTicket = $id;

        // Verificar si los datos necesarios están presentes
         if (!isset($idTicket)) {
             header('HTTP/1.1 400 Bad Request');
             echo json_encode(["error400" => "Faltan datos en la solicitud"]);
                    exit;
        }


            $database = new Database();
            $conn = $database->getConnection();

            $stmt = $conn->prepare('CALL ObtenerInformacionTicket(:idTicket)');

            $stmt->bindParam(':idTicket',$idTicket);


            if($stmt->execute()){
                // Obtener todos los resultados
                $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Responder con los datos de categorías
            return sendResponse(200, [
                "tickets" => $tickets,
                "idTicket" => $idTicket,
            ]);
               }else{
                   // Responder con error 500 si la inserción falla
                return sendResponse(500, ["error" => "No se pudo obtener los tickets"]);
              }
        } catch (\Throwable $th) {
            error_log('Error al obtener las tickets: ' . $th->getMessage());
            return sendResponse(500, [
                "error" => "ocurrio un error interno del servidor",
                "detalles" => $th->getMessage()
         ]);
        }

    }
}
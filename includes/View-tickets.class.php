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

class view_tickets{
    public static function ver_tickets($token,$estado = null, $nivel = null, $area = null, $desde = null, $hasta = null ){

        try {

        //verifica que el token no haya vencido
        $tokenValidation = validarTokenEnClase($token);

        if (!$tokenValidation ) {
            return sendResponse(400, ["Error" => "Token vencido"]);
        }

         // Obtener el cuerpo de la solicitud en formato JSON
         $data = json_decode(file_get_contents("php://input"), true);

         // Usar el estado del parámetro o del JSON
        $estadoTicket = $estado ?? ($data['estado'] ?? null);
        $nivelTicket = $nivel ?? ($data['nivel'] ?? null);
        $areaTicket = $area ?? ($data['area'] ?? null);
        $desdeTicket = $desde ?? ($data['desde'] ?? null);
        $hastaTicket = $hasta ?? ($hasta['hasta'] ?? null);

        // verificar si son cadenas
         if ($estadoTicket !== null && !sonCadenas([$estadoTicket])) {
             header('HTTP/1.1 404 No son string');
             echo json_encode(["errorString" => "Solo se permite string"]);
         exit;
        }

            $database = new Database();
            $conn = $database->getConnection();
            $stmt = $conn->prepare('CALL ObtenerTicketsPorEstado(:estadoTicket, :nivelTicket, :areaTicket, :desdeTicket, :hastaTicket)');
            $stmt->bindParam(':estadoTicket',$estadoTicket);
            $stmt->bindParam(':nivelTicket',$nivelTicket);
            $stmt->bindParam(':areaTicket',$areaTicket);
            $stmt->bindParam(':desdeTicket',$desdeTicket);
            $stmt->bindParam(':hastaTicket',$hastaTicket);


            if($stmt->execute()){
                // Obtener todos los resultados
                $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Responder con los datos de categorías
            return sendResponse(200, [
                "tickets" => $tickets,
                "estado" => $estadoTicket,
                "nivel" => $nivelTicket,
                "area" => $areaTicket,
                "desde" => $desdeTicket,
                "hasta" => $hastaTicket
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
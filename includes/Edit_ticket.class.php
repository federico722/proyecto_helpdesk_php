<?php

require_once 'Database.class.php';
require_once __DIR__ . '..\..\logica\confirPassword.php';
require_once __DIR__ . '..\..\logica\validacionIntNull.php';
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
require_once __DIR__ . '..\..\consultas-usuario\consultar-nombre-agente.php';

class Edit_ticket{
    public static function editarTicket($token){
        try {
        //verifica que el token no haya vencido
        $tokenValidation = validarTokenEnClase($token);
        if (!$tokenValidation ) {
            return sendResponse(400, ["Error" => "Token vencido"]);
        }

        // Obtener el cuerpo de la solicitud en formato JSON
        $data = json_decode(file_get_contents("php://input"), true);
        $id_ticket = $data['id_ticket'];
        $estado = $data['estado'];
        $fecha_ticket_abierto = $data['fecha_ticket_abierto'];
        $fecha_ticket_cerrado = $data['fecha_ticket_cerrado'];
        $id_agente  = $data['id_agente'];
        $id_solicitud  = $data['id_solicitud'];
        $id_area = $data['id_area'];
        $id_nivel = $data['id_nivel'];
        $id_plantilla = $data['id_plantilla'];
        $id_alerta = $data['id_alerta'];

        // verifica si son numeros
        if (!sonNumerico([$id_ticket])) {
            return sendResponse(400, [
             "Error" => "Datos invalidos, solo se permiten valores numericos"
             ]);
        }

        if (!esNumericoNulo([$id_agente, $id_solicitud, $id_area, $id_nivel, $id_plantilla, $id_alerta])) {
            return sendResponse(400, [
                "ErrorNumerico" => "Datos invalidos, solo se permiten valores numericos"
                ]);
        }

        $database = new Database();
        $conn = $database->getConnection();
        $stmt = $conn->prepare('CALL EditarTicket(:id_ticket, :estado, :fecha_ticket_abierto, :fecha_ticket_cerrado, :id_agente, :id_solicitud, :id_area, :id_nivel, :id_plantilla, :id_alerta )');

        $stmt->bindValue(':id_ticket', $id_ticket, PDO::PARAM_INT | PDO::PARAM_NULL);
        $stmt->bindValue(':estado', $estado, PDO::PARAM_STR | PDO::PARAM_NULL);
        $stmt->bindValue(':fecha_ticket_abierto', $fecha_ticket_abierto, PDO::PARAM_STR | PDO::PARAM_NULL);
        $stmt->bindValue(':fecha_ticket_cerrado', $fecha_ticket_cerrado, PDO::PARAM_STR | PDO::PARAM_NULL);
        $stmt->bindValue(':id_agente', $id_agente, PDO::PARAM_STR | PDO::PARAM_NULL);
        $stmt->bindValue(':id_solicitud', $id_solicitud, PDO::PARAM_STR | PDO::PARAM_NULL);
        $stmt->bindValue(':id_area', $id_area, PDO::PARAM_STR | PDO::PARAM_NULL);
        $stmt->bindValue(':id_nivel', $id_nivel, PDO::PARAM_STR | PDO::PARAM_NULL);
        $stmt->bindValue(':id_plantilla', $id_nivel, PDO::PARAM_STR | PDO::PARAM_NULL);
        $stmt->bindValue(':id_alerta', $id_nivel, PDO::PARAM_STR | PDO::PARAM_NULL);


        } catch (\Throwable $th) {
            error_log('Error al obtener las tickets: ' . $th->getMessage());
            return sendResponse(500, [
                "error" => "ocurrio un error interno del servidor",
                "detalles" => $th->getMessage()
         ]);
        }

    }
}
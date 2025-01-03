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

class Create_solicitud_cliente{

    public static function crearSolicitudCliente(){
        try {
             // Obtener el cuerpo de la solicitud en formato JSON
        $data = json_decode(file_get_contents("php://input"), true);

        // Verificar si los datos necesarios están presentes
        if (!isset($data['nombre_solicitante'],$data['asunto'],$data['fecha_solicitud'])) {
        return sendResponse(400, ["Error" => "Faltan datos en la solicitud"]);
        }


        // Obtengo los datos del formato json
        $nombre_solicitante = $data['nombre_solicitante'];
        $asunto = $data['asunto'];
        $fecha_solicitud = $data['fecha_solicitud'];


         // verifica si son fechas
         if (!validarFecha([$fecha_solicitud])) {
            return sendResponse(400,['Error' => "Datos invalidos, solo se permite la fecha en el formato Y-m-d"]);
        }

        // verificar si son cadenas
        $camposValidar = [
            'nombre_solicitante' => $nombre_solicitante, 'asunto' => $asunto];

        //validar los campos
        $resultado = validarArrayFlexible($camposValidar, 1, 1000);

        if (!$resultado['valido']) {
            return sendResponse(400, [
                "Error" => "Datos invalidos",
                "detalles" => $resultado['errores']
                ]);
        }

        $nombre_solicitante = $resultado['datos']['nombre_solicitante'];
        $asunto = $resultado['datos']['asunto'];

        //iniciar conexion a la base de datos
        $database = new Database();
        $conn = $database->getConnection();

         // Iniciar una transacción
         $conn->beginTransaction();

        $stmtSolicitud  = $conn->prepare('INSERT INTO SOLICITUD_CLIENTES (nombre_solicitante,asunto,fecha_solicitud) VALUES(:nombre_solicitante,:asunto,:fecha_solicitud)');
        $stmtSolicitud->bindParam(':nombre_solicitante',$nombre_solicitante);
        $stmtSolicitud->bindParam(':asunto',$asunto);
        $stmtSolicitud->bindParam(':fecha_solicitud',$fecha_solicitud);

        if($stmtSolicitud->execute()){
        // Obtener el ID de la solicitud recién insertada
        $idSolicitud = $conn->lastInsertId();

        //Insertar en tickets
        $stmtTicket = $conn->prepare(
            'INSERT INTO TICKETS (estado, id_solicitud,id_nivel) VALUES ("Abrir", :id_solicitud, 1 )'
        );
        $stmtTicket->bindParam(':id_solicitud', $idSolicitud);

        if ($stmtTicket->execute()) {
            $conn->commit();
            return sendResponse(200, ["success" => "Solicitud y ticket guardados con éxito."]);
        }else{
            $conn->rollBack();
            return sendResponse(500, ["No se pudo guardar el ticket"]);
        }
        }else{
        // Responder con error 500 si la inserción falla
          return sendResponse(500, ["error" => "No se pudo guardar la solicitud"]);
        }

        } catch (\Throwable $th) {
                      error_log('Error al agregar el servicio: ' . $th->getMessage());
            return sendResponse(500, [
                "error" => "ocurrio un error interno del servidor",
                "detalles" => $th->getMessage()
         ]);
        }
    }

}

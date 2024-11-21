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

class Create_servicio{

    public static function crearServicio($token){

        try {
        // Obtener el cuerpo de la solicitud en formato JSON
        $data = json_decode(file_get_contents("php://input"), true);

        // Verificar si los datos necesarios están presentes
        if (!isset($data['nombre_servicio'],$data['descripcion_servicio'],$data['fecha_inicio'],$data['frecuencia_facturacion'],$data['costo_servicio'],$data['proveedor_servicio'],$data['estado_servicio'],$data['url_acceso'],$data['tipo_servicio'],$data['id_equipo'])) {
        return sendResponse(400, ["Error" => "Faltan datos en la solicitud"]);
        }

        //verifica que el token no haya vencido
        $tokenValidation = validarTokenEnClase($token);

        if (!$tokenValidation ) {
            return sendResponse(400, ["Error" => "Token vencido"]);
        }

        // Obtengo los datos del formato json
        $nombre_servicio = $data['nombre_servicio'];
        $descripcion_servicio = $data['descripcion_servicio'];
        $fecha_inicio = $data['fecha_inicio'];
        $frecuencia_facturacion = $data['frecuencia_facturacion'];
        $costo_servicio = $data['costo_servicio'];
        $proveedor_servicio = $data['proveedor_servicio'];
        $estado_servicio = $data['estado_servicio'];
        $url_acceso = $data['url_acceso'];
        $tipo_servicio = $data['tipo_servicio'];
        $id_equipo = $data['id_equipo'];



        // verifica si son numeros
        if (!sonNumerico([$costo_servicio,$id_equipo])) {
        return sendResponse(400, [
            "Error" => "Datos invalidos, solo se permiten valores numericos"
            ]);
        }

        // verifica si son fechas
        if (!validarFecha([$fecha_inicio])) {
        return sendResponse(400,['Error' => "Datos invalidos, solo se permite la fecha en el formato Y-m-d"]);
        }

        // verificar si son cadenas
        $camposValidar = [
        'nombre_servicio' => $nombre_servicio, 'descripcion_servicio' => $descripcion_servicio,'frecuencia_facturacion' => $frecuencia_facturacion, 'proveedor_servicio' => $proveedor_servicio, 'estado_servicio' => $estado_servicio, 'url_acceso' => $url_acceso, 'tipo_servicio' => $tipo_servicio ];

        //validar los campos
        $resultado = validarArrayFlexible($camposValidar, 1, 1000);

        if (!$resultado['valido']) {
        return sendResponse(400, [
        "Error" => "Datos invalidos",
        "detalles" => $resultado['errores']
        ]);
        }

        $nombre_servicio = $resultado['datos']['nombre_servicio'];
        $descripcion_servicio = $resultado['datos']['descripcion_servicio'];
        $frecuencia_facturacion = $resultado['datos']['frecuencia_facturacion'];
        $proveedor_servicio = $resultado['datos']['proveedor_servicio'];
        $estado_servicio = $resultado['datos']['estado_servicio'];
        $url_acceso = $resultado['datos']['url_acceso'];
        $tipo_servicio = $resultado['datos']['tipo_servicio'];


            $database = new Database();
            $conn = $database->getConnection();
            $stmt = $conn->prepare('INSERT INTO SERVICIOS (nombre_servicio, descripcion_servicio,fecha_inicio, frecuencia_facturacion,costo_servicio ,proveedor_servicio, estado_servicio,  url_acceso, tipo_servicio, id_equipo) VALUES(:nombre_servicio, :descripcion_servicio,:fecha_inicio ,:frecuencia_facturacion, :costo_servicio ,:proveedor_servicio, :estado_servicio,:url_acceso, :tipo_servicio, :id_equipo)');
            $stmt->bindParam(':nombre_servicio',$nombre_servicio);
            $stmt->bindParam(':descripcion_servicio',$descripcion_servicio);
            $stmt->bindParam(':fecha_inicio',$fecha_inicio);
            $stmt->bindParam(':frecuencia_facturacion',$frecuencia_facturacion);
            $stmt->bindParam(':costo_servicio',$costo_servicio);
            $stmt->bindParam(':proveedor_servicio',$proveedor_servicio);
            $stmt->bindParam(':estado_servicio',$estado_servicio);
            $stmt->bindParam(':url_acceso',$url_acceso);
            $stmt->bindParam(':tipo_servicio',$tipo_servicio);
            $stmt->bindParam(':id_equipo',$id_equipo);


            if($stmt->execute()){
                // Responder con éxito
                return sendResponse(200, ["success" => "Servicio " .$nombre_servicio. " guardado con exito. "]);
               }else{
                   // Responder con error 500 si la inserción falla
                return sendResponse(500, ["error" => "No se pudo crear el servicio"]);
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
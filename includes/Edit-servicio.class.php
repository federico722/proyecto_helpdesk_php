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
require_once __DIR__ . '..\..\consultas-usuario\consultar-nombre-agente.php';

class Edit_servico{
    public static function Editar_servicio($token){

        try {

        // Obtener el cuerpo de la solicitud en formato JSON
        $data = json_decode(file_get_contents("php://input"), true);

        // Verificar si los datos necesarios están presentes
        if (!isset($data['id_servicio'],$data['descripcion_servicio'],$data['fecha_inicio'],$data['proveedor_servicio'],$data['fracuencia_facturacion'],$data['estado_servicio'],$data['url_acceso'],$data['tipo_servicio'],$data['costo_servicio'],$data['nombre_servicio'])) {
        return sendResponse(400, ["Error" => "Faltan datos en la solicitud"]);
        }

        //verifica que el token no haya vencido
        $tokenValidation = validarTokenEnClase($token);

        if (!$tokenValidation ) {
                return sendResponse(400, ["Error" => "Token vencido"]);
        }


       // Obtengo los datos del formato json
       $id_servicio = $data['id_servicio'];
       $descripcion_servicio = $data['descripcion_servicio'];
       $fecha_inicio = $data['fecha_inicio'];
       $proveedor_servicio = $data['proveedor_servicio'];
       $fracuencia_facturacion = $data['fracuencia_facturacion'];
       $estado_servicio = $data['estado_servicio'];
       $url_acceso = $data['url_acceso'];
       $tipo_servicio = $data['tipo_servicio'];
       $costo_servicio = $data['costo_servicio'];
       $nombre_servicio = $data['nombre_servicio'];



        // verifica si son fechas
        if (!validarFecha([$fecha_inicio])) {
            return sendResponse(400,['Error' => "Datos invalidos, solo se permite la fecha en el formato Y-m-d"]);
        }

        // verifica si son numeros
        if (!sonNumerico([$id_servicio, $costo_servicio])) {
        return sendResponse(400, [
            "Error" => "Datos invalidos, solo se permiten valores numericos"
            ]);
        }


        // verificar si son cadenas
        $camposValidar = [
            'descripcion_servicio' => $descripcion_servicio, 'proveedor_servicio' => $proveedor_servicio,'fracuencia_facturacion' => $fracuencia_facturacion, 'estado_servicio' => $estado_servicio, 'url_acceso' => $url_acceso, 'tipo_servicio' => $tipo_servicio, 'nombre_servicio' => $nombre_servicio
           ];

         //validar los campos
         $resultado = validarArrayFlexible($camposValidar, 1, 1000);

         $descripcion_servicio = $resultado['datos']['descripcion_servicio'];
         $proveedor_servicio = $resultado['datos']['proveedor_servicio'];
         $fracuencia_facturacion = $resultado['datos']['fracuencia_facturacion'];
         $estado_servicio = $resultado['datos']['estado_servicio'];
         $url_acceso = $resultado['datos']['url_acceso'];
         $tipo_servicio = $resultado['datos']['estado_licencia'];
         $nombre_servicio = $resultado['datos']['tipo_licencia'];

         if (!$resultado['valido']) {
            return sendResponse(400, [
            "Error" => "Datos invalidos",
            "detalles" => $resultado['errores']
            ]);
        }

        $database = new Database();
        $conn = $database->getConnection();
        $stmt = $conn->prepare('UPDATE Servicios SET descripcion_servicio = :descripcion_servicio ,
        fecha_inicio = :fecha_inicio ,
        proveedor_servicio = :proveedor_servicio,
        frecuencia_facturacion  = :frecuencia_facturacion,
        estado_servicio = :estado_servicio,
        url_acceso = :url_acceso,
        tipo_servicio = :tipo_servicio,
        nombre_servicio = :estado_licencia  WHERE  id_licencia = :id_licencia');
        $stmt->bindParam(':descripcion_servicio',$descripcion_servicio);
        $stmt->bindParam(':fecha_inicio',$fecha_inicio);
        $stmt->bindParam(':proveedor_servicio',$proveedor_servicio);
        $stmt->bindParam(':estado_servicio',$estado_servicio);
        $stmt->bindParam(':frecuencia_facturacion',$frecuencia_facturacion);
        $stmt->bindParam(':tipo_servicio',$tipo_servicio);
        $stmt->bindParam(':costo_servicio',$costo_servicio);
        $stmt->bindParam(':nombre_servicio',$nombre_servicio);


       if($stmt->execute()){

                // Responder con los datos de categorías
            return sendResponse(200, [
                "Success" => "Servicio actualizado con exito",
            ]);
               }else{
                   // Responder con error 500 si la inserción falla
                return sendResponse(500, ["error" => "No se pudo editar el servicio"]);
              }
        } catch (\Throwable $th) {
            error_log('Error al editar el servicio: ' . $th->getMessage());
            return sendResponse(500, [
                "error" => "ocurrio un error interno del servidor",
                "detalles" => $th->getMessage()
         ]);
        }

    }
}
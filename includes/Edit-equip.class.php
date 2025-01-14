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
require_once __DIR__ . '..\..\consultas-usuario\consultar-nombre-encargado-equipo.php';


class Edit_equip{
    public static function Editar_equipo($token){

        try {

        // Obtener el cuerpo de la solicitud en formato JSON
        $data = json_decode(file_get_contents("php://input"), true);

        // Verificar si los datos necesarios están presentes
        // ,$data['depreciacion_equipo']
        if (!isset(
            $data['nombre_equipo'],
            $data['caracteristicas_del_sistema'],
            $data['numero_serial'],
            $data['fecha_de_adquisicion'],
            $data['modelo_equipo'],
            $data['imagen_equipo'],
            $data['descripcion_equipo'],
            $data['estado_equipo'],
            $data['proveedor_equipo'],
            $data['ubicacion_equipo'], 
            $data['costo_adquisicion'], 
            $data['vida_util_equipo'],
            $data['valor_residual'], 
            $data['id_equipo'], 
            $data['placa_activo_fijo']
            )) {
        return sendResponse(400, ["Error400FaltanDatos" => "Faltan datos en la solicitud"]);
        }

        //$data['nombre_agente']

            //verifica que el token no haya vencido
            $tokenValidation = validarTokenEnClase($token);

            if (!$tokenValidation ) {
                return sendResponse(400, ["ErrorToken" => "Token vencido"]);
            }
       // Obtengo los datos del formato json
       $nombre_equipo = $data['nombre_equipo'];
       $caracteristicas_del_sistema = $data['caracteristicas_del_sistema'];
       $fecha_de_adquisicion = $data['fecha_de_adquisicion'];
       $costo_adquisicion = $data['costo_adquisicion'];
       $valor_residual = $data['valor_residual'];
       $imagen_equipo = $data['imagen_equipo'];
       $descripcion_equipo = $data['descripcion_equipo'];
       $modelo_equipo = $data['modelo_equipo'];
       $numero_serial = $data['numero_serial'];
       $proveedor_equipo = $data['proveedor_equipo'];
       //$nombre_agente = $data['nombre_agente'];
       $ubicacion_equipo =$data['ubicacion_equipo'];
       $vida_util_equipo =$data['vida_util_equipo'];
       $estado_equipo = $data['estado_equipo'];
       //$depreciacion_equipo = $data['depreciacion_equipo'];
       $id_equipo = $data['id_equipo'];
       $placa_activo_fijo = $data['placa_activo_fijo'];

        // verifica si son fechas
        if (!validarFecha([$fecha_de_adquisicion])) {
            return sendResponse(400,['ErrorFecha' => "Datos invalidos, solo se permite la fecha en el formato Y-m-d"]);
        }

        // verifica si son numeros
        if (!sonNumerico([$costo_adquisicion,$id_equipo])) {
        return sendResponse(400, [
            "ErrorNullNumerico" => "Datos invalidos, solo se permiten valores numericos"
            ]);
        }


        // verificar si son cadenas
        // 'imagen_equipo' => $imagen_equipo
        $camposValidar = [
            'nombre_equipo' => $nombre_equipo, 
            'caracteristicas_del_sistema' => $caracteristicas_del_sistema, 
            'descripcion_equipo' => $descripcion_equipo, 
            'modelo_equipo' => $modelo_equipo, 
            'numero_serial' => $numero_serial, 
            'proveedor_equipo' => $proveedor_equipo,
            'ubicacion_equipo' => $ubicacion_equipo, 
            'estado_equipo' => $estado_equipo, 
            'placa_activo_fijo'=> $placa_activo_fijo
           ];

           //'nombre_agente' => $nombre_agente

         //validar los campos
         $resultado = validarArrayFlexible($camposValidar, 1, 1000);

         $nombre_equipo = $resultado['datos']['nombre_equipo'];
         $caracteristicas_del_sistema = $resultado['datos']['caracteristicas_del_sistema'];
         //$imagen_equipo = $resultado['datos']['imagen_equipo'];
         $descripcion_equipo = $resultado['datos']['descripcion_equipo'];
         $modelo_equipo = $resultado['datos']['modelo_equipo'];
         $numero_serial = $resultado['datos']['numero_serial'];
         $proveedor_equipo = $resultado['datos']['proveedor_equipo'];
         $ubicacion_equipo = $resultado['datos']['ubicacion_equipo'];
         $estado_equipo = $resultado['datos']['estado_equipo'];
        // $nombre_agente = $resultado['datos']['nombre_agente'];
         $placa_activo_fijo = $resultado['datos']['placa_activo_fijo'];

         if (!$resultado['valido']) {
            return sendResponse(400, [
            "ErrorCampos" => "Datos invalidos",
            "detalles" => $resultado['errores']
            ]);
        }

        /*
        $id_agente = consultarAgente($nombre_agente);
        if ($id_agente ===  false) {
            return sendResponse(500, [
                "Error" => "Error en la base de datos al consultar el agente"
            ]);
        }else if($id_agente === null ){
            return sendResponse(400,[
                "Error" => "agente no encontrado"
            ]);
        }
        */

        // id_agente = :id_agente,
        //depreciacion_equipo = :depreciacion_equipo,

        $database = new Database();
        $conn = $database->getConnection();
        $stmt = $conn->prepare('UPDATE EQUIPOS SET
        nombre_equipo = :nombre_equipo,
        caracteristicas_del_sistema = :caracteristicas_del_sistema,
        fecha_de_adquisicion = :fecha_de_adquisicion,
        costo_adquisicion = :costo_adquisicion,
        valor_residual = :valor_residual,
        imagen_equipo = :imagen_equipo,
        descripcion_equipo = :descripcion_equipo,
        modelo_equipo = :modelo_equipo,
        numero_serial = :numero_serial,
        proveedor_equipo = :proveedor_equipo,
        ubicacion_equipo = :ubicacion_equipo,
        vida_util_equipo = :vida_util_equipo,
        estado_equipo = :estado_equipo,
        placa_activo_fijo = :placa_activo_fijo WHERE  id_equipo = :id_equipo');
        $stmt->bindParam(':nombre_equipo',$nombre_equipo);
        $stmt->bindParam(':caracteristicas_del_sistema',$caracteristicas_del_sistema);
        $stmt->bindParam(':fecha_de_adquisicion',$fecha_de_adquisicion);
        $stmt->bindParam(':costo_adquisicion', $costo_adquisicion);
        $stmt->bindParam(':valor_residual', $valor_residual);
        $stmt->bindParam(':imagen_equipo', $imagen_equipo);
        $stmt->bindParam(':descripcion_equipo', $descripcion_equipo);
        $stmt->bindParam(':modelo_equipo', $modelo_equipo);
        $stmt->bindParam(':numero_serial', $numero_serial);
        $stmt->bindParam(':proveedor_equipo', $proveedor_equipo);
       // $stmt->bindParam(':id_agente', $id_agente);
        $stmt->bindParam(':ubicacion_equipo', $ubicacion_equipo);
        $stmt->bindParam(':vida_util_equipo', $vida_util_equipo);
        $stmt->bindParam(':estado_equipo', $estado_equipo);
       // $stmt->bindParam(':depreciacion_equipo', $depreciacion_equipo);
        $stmt->bindParam(':placa_activo_fijo', $placa_activo_fijo);
        $stmt->bindParam(':id_equipo',$id_equipo);


            if($stmt->execute()){
                // Responder con los datos de categorías
            return sendResponse(200, [
                "success" => "Equipo actualizado con exito",
            ]);
               }else{
                   // Responder con error 500 si la inserción falla
                return sendResponse(500, ["errorInterno" => "No se pudo editar el equipo"]);
              }
        } catch (\Throwable $th) {
        error_log('Error al editar el equipo: ' . $th->getMessage());
            return sendResponse(500, [
                "error" => "ocurrio un error interno del servidor",
                "detalles" => $th->getMessage()
         ]);
        }

    }
}
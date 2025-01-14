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
require_once __DIR__ . '..\..\logica\obtenerFechaActual.php';


class Create_equipo{

    public static function crearEquipo($token){
        try {

        // Obtener el cuerpo de la solicitud en formato JSON
        $data = json_decode(file_get_contents("php://input"), true);

        //verifica si los datos necesarios estan presentes
          // Verificar si los datos necesarios están presentes
          if (!isset(
            $data['nombre_equipo'],
            $data['caracteristicas_del_sistema'],
            $data['fecha_de_adquisicion'],
            $data['costo_adquisicion'],
            $data['valor_residual'],
            $data['imagen_equipo'],
            $data['descripcion_equipo'],
            $data['modelo_equipo'],
            $data['numero_serial'],
            $data['placa_activo_fijo'],
            $data['proveedor_equipo'],
            $data['nombre_encargado_equipo'],
            $data['ubicacion_equipo'],
            $data['vida_util_equipo'],
            $data['id_categoria'],
            $data['estado_equipo']
            )) {
            return sendResponse(400, ["Error" => "Faltan datos en la solicitud",
        "data" => $data]);
        }

        //verifica que el token no haya vencido
        $tokenValidation = validarTokenEnClase($token);

        if (!$tokenValidation ) {
            return sendResponse(400, ["Error" => "Token vencido"]);
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
        $placa_activo_fijo = $data['placa_activo_fijo'];
        $proveedor_equipo = $data['proveedor_equipo'];
        $nombre_encargado_equipo = $data['nombre_encargado_equipo'];
        $ubicacion_equipo =$data['ubicacion_equipo'];
        $vida_util_equipo =$data['vida_util_equipo'];
        $id_categoria = $data['id_categoria'];
        $estado_equipo = $data['estado_equipo'];


        // verifica si son numeros
        if (!sonNumerico([$costo_adquisicion,$valor_residual,$id_categoria])) {
            return sendResponse(400, [
                "Error" => "Datos invalidos, solo se permiten valores numericos"
                ]);
        }

        // verifica si son fechas
        if (!validarFecha([$fecha_de_adquisicion])) {
            return sendResponse(400,['Error' => "Datos invalidos, solo se permite la fecha en el formato Y-m-d"]);
        }

        // verificar si son cadenas
        $camposValidar = [
            'nombre_equipo' => $nombre_equipo, 
            'caracteristicas_del_sistema' => $caracteristicas_del_sistema,
            'descripcion_equipo' => $descripcion_equipo, 
            'modelo_equipo' => $modelo_equipo, 
            'numero_serial' => $numero_serial, 
            'placa_activo_fijo' => $placa_activo_fijo,
            'proveedor_equipo' => $proveedor_equipo,
            'ubicacion_equipo' => $ubicacion_equipo, 
            'estado_equipo' => $estado_equipo, 
            'nombre_encargado_equipo' => $nombre_encargado_equipo, 
            'vida_util_equipo' => $vida_util_equipo
           ];

         //validar los campos
         $resultado = validarArrayFlexible($camposValidar, 1, 1000);

         if (!$resultado['valido']) {
            return sendResponse(400, [
            "Error" => "Datos invalidos",
            "detalles" => $resultado['errores']
            ]);
        }

        $fecha_actual = obtenerFechaActual();


        $nombre_equipo = $resultado['datos']['nombre_equipo'];
        $caracteristicas_del_sistema = $resultado['datos']['caracteristicas_del_sistema'];
        $descripcion_equipo = $resultado['datos']['descripcion_equipo'];
        $modelo_equipo = $resultado['datos']['modelo_equipo'];
        $numero_serial = $resultado['datos']['numero_serial'];
        $placa_activo_fijo = $resultado['datos']['placa_activo_fijo'];
        $proveedor_equipo = $resultado['datos']['proveedor_equipo'];
        $ubicacion_equipo = $resultado['datos']['ubicacion_equipo'];
        $estado_equipo = $resultado['datos']['estado_equipo'];
        $nombre_encargado_equipo = $resultado['datos']['nombre_encargado_equipo'];
        $vida_util_equipo = $resultado['datos']['vida_util_equipo'];

            $database = new Database();
            $conn = $database->getConnection();
            $stmt = $conn->prepare('INSERT INTO EQUIPOS (
            nombre_equipo,
            caracteristicas_del_sistema,
            fecha_de_adquisicion,
            costo_adquisicion,
            valor_residual,
            imagen_equipo,
            descripcion_equipo,
            modelo_equipo,
            numero_serial,
            placa_activo_fijo,
            proveedor_equipo,
            nombre_encargado_equipo,
            ubicacion_equipo,
            vida_util_equipo,
            id_categoria,
            estado_equipo,
            fecha_actual
            ) VALUES(:nombre_equipo,
            :caracteristicas_del_sistema,
            :fecha_de_adquisicion,
            :costo_adquisicion,
            :valor_residual,
            :imagen_equipo,
            :descripcion_equipo,
            :modelo_equipo,
            :numero_serial,
            :placa_activo_fijo
            :proveedor_equipo,
            :nombre_encargado_equipo,
            :ubicacion_equipo,
            :vida_util_equipo,
            :id_categoria,
            :estado_equipo,
            :fecha_actual)');
            $stmt->bindParam(':nombre_equipo',$nombre_equipo);
            $stmt->bindParam(':caracteristicas_del_sistema',$caracteristicas_del_sistema);
            $stmt->bindParam(':fecha_de_adquisicion',$fecha_de_adquisicion);
            $stmt->bindParam(':costo_adquisicion',$costo_adquisicion);
            $stmt->bindParam(':valor_residual',$valor_residual);
            $stmt->bindParam(':imagen_equipo',$imagen_equipo);
            $stmt->bindParam(':descripcion_equipo',$descripcion_equipo);
            $stmt->bindParam(':modelo_equipo',$modelo_equipo);
            $stmt->bindParam(':numero_serial',$numero_serial);
            $stmt->bindParam(':placa_activo_fijo',$placa_activo_fijo);
            $stmt->bindParam(':proveedor_equipo',$proveedor_equipo);
            $stmt->bindParam(':nombre_encargado_equipo',$nombre_encargado_equipo);
            $stmt->bindParam(':ubicacion_equipo',$ubicacion_equipo);
            $stmt->bindParam(':vida_util_equipo',$vida_util_equipo);
            $stmt->bindParam(':id_categoria',$id_categoria);
            $stmt->bindParam(':estado_equipo',$estado_equipo);
            $stmt->bindParam(':fecha_actual',$fecha_actual);

            if($stmt->execute()){
                // Responder con éxito
                return sendResponse(200, ["success" => "equipo " .$nombre_equipo. " guardado con exito. "]);
               }else{
                   // Responder con error 500 si la inserción falla
                return sendResponse(500, ["error" => "No se pudo crear el equipo"]);
              }

        } catch (\Throwable $th) {
            error_log('Error al agregar el equipo: ' . $th->getMessage());
            return sendResponse(500, [
                "error" => "ocurrio un error interno del servidor",
                "detalles" => $th->getMessage()
         ]);
        }
    }
}
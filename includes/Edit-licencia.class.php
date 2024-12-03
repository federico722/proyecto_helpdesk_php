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

class Edit_licencia{
    public static function Editar_licencia($token){

        try {

        // Obtener el cuerpo de la solicitud en formato JSON
        $data = json_decode(file_get_contents("php://input"), true);

        // Verificar si los datos necesarios están presentes
        if (!isset($data['id_licencia'],$data['nombre_licencia'],$data['descripcion_licencia'],$data['numero_licencia'],$data['fecha_adquisicion'],$data['duracion_licencia'],$data['costo_licencia'],$data['usuarios_permitidos'],$data['proveedor_licencia'],$data['estado_licencia'],$data['tipo_licencia'])) {
        return sendResponse(400, ["Error" => "Faltan datos en la solicitud"]);
        }

        //verifica que el token no haya vencido
        $tokenValidation = validarTokenEnClase($token);

        if (!$tokenValidation ) {
                return sendResponse(400, ["Error" => "Token vencido"]);
        }


       // Obtengo los datos del formato json
       $id_licencia = $data['id_licencia'];
       $nombre_licencia = $data['nombre_licencia'];
       $descripcion_licencia = $data['descripcion_licencia'];
       $numero_licencia = $data['numero_licencia'];
       $fecha_adquisicion = $data['fecha_adquisicion'];
       $duracion_licencia = $data['duracion_licencia'];
       $costo_licencia = $data['costo_licencia'];
       $usuarios_permitidos = $data['usuarios_permitidos'];
       $proveedor_licencia = $data['proveedor_licencia'];
       $estado_licencia = $data['estado_licencia'];
       $tipo_licencia = $data['tipo_licencia'];
       


        // verifica si son fechas
        if (!validarFecha([$fecha_adquisicion])) {
            return sendResponse(400,['Error' => "Datos invalidos, solo se permite la fecha en el formato Y-m-d"]);
        }

        // verifica si son numeros
        if (!sonNumerico([$costo_licencia,$id_licencia])) {
        return sendResponse(400, [
            "Error" => "Datos invalidos, solo se permiten valores numericos"
            ]);
        }


        // verificar si son cadenas
        $camposValidar = [
            'id_licencia' => $id_licencia, 'nombre_licencia' => $nombre_licencia,'descripcion_licencia' => $descripcion_licencia, 'numero_licencia' => $numero_licencia, 'usuarios_permitidos' => $usuarios_permitidos, 'proveedor_licencia' => $proveedor_licencia, 'estado_licencia' => $estado_licencia, 'tipo_licencia' => $tipo_licencia, 'duracion_licencia' => $duracion_licencia
           ];

         //validar los campos
         $resultado = validarArrayFlexible($camposValidar, 1, 1000);

         $nombre_licencia = $resultado['datos']['nombre_licencia'];
         $descripcion_licencia = $resultado['datos']['descripcion_licencia'];
         $numero_licencia = $resultado['datos']['numero_licencia'];
         $usuarios_permitidos = $resultado['datos']['usuarios_permitidos'];
         $proveedor_licencia = $resultado['datos']['proveedor_licencia'];
         $estado_licencia = $resultado['datos']['estado_licencia'];
         $tipo_licencia = $resultado['datos']['tipo_licencia'];

         if (!$resultado['valido']) {
            return sendResponse(400, [
            "Error" => "Datos invalidos",
            "detalles" => $resultado['errores']
            ]);
        }

        $database = new Database();
        $conn = $database->getConnection();
        $stmt = $conn->prepare('UPDATE Licencias SET nombre_licencia = :nombre_licencia ,
        descripcion_licencia = :descripcion_licencia ,
        numero_licencia = :numero_licencia,
        fecha_adquisicion  = :fecha_adquisicion ,
        duracion_licencia = :duracion_licencia,
        costo_licencia = :costo_licencia,
        usuarios_permitidos = :usuarios_permitidos,
        proveedor_licencia = :proveedor_licencia,
        estado_licencia = :estado_licencia,
        tipo_licencia = :tipo_licencia  WHERE  id_licencia = :id_licencia');
        $stmt->bindParam(':nombre_licencia',$nombre_licencia);
        $stmt->bindParam(':descripcion_licencia',$descripcion_licencia);
        $stmt->bindParam(':numero_licencia',$numero_licencia);
        $stmt->bindParam(':fecha_adquisicion',$fecha_adquisicion);
        $stmt->bindParam(':duracion_licencia',$duracion_licencia);
        $stmt->bindParam(':costo_licencia',$costo_licencia);
        $stmt->bindParam(':usuarios_permitidos',$usuarios_permitidos);
        $stmt->bindParam(':proveedor_licencia',$proveedor_licencia);
        $stmt->bindParam(':estado_licencia',$estado_licencia);
        $stmt->bindParam(':tipo_licencia',$tipo_licencia);
        $stmt->bindParam(':id_licencia',$id_licencia);

            if($stmt->execute()){
                // Responder con los datos de categorías
            return sendResponse(200, [
                "Success" => "Licencia actualizado con exito",
            ]);
               }else{
                   // Responder con error 500 si la inserción falla
                return sendResponse(500, ["error" => "No se pudo editar la licencia"]);
              }
        } catch (\Throwable $th) {
            error_log('Error al editar la licencia: ' . $th->getMessage());
            return sendResponse(500, [
                "error" => "ocurrio un error interno del servidor",
                "detalles" => $th->getMessage()
         ]);
        }

    }
}
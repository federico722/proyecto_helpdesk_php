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

class Create_licencia{

    public static function crearLicencia($token){
        // Obtener el cuerpo de la solicitud en formato JSON
        $data = json_decode(file_get_contents("php://input"), true);

          // Verificar si los datos necesarios están presentes
          if (!isset($data['fecha_anotacion'],$data['descripcion_anotaciones'],$data['id_equipo'],$data['author_anotacion'])) {
            return sendResponse(400, ["Error" => "Faltan datos en la solicitud"]);
        }

        //verifica que el token no haya vencido
        if (!verificarToken($token)) {
            return sendResponse(400, ["Error" => "el token expiro"]);
        }

        // Obtengo los datos del formato json
        $fecha_anotacion = $data['fecha_anotacion'];
        $descripcion_anotaciones = $data['descripcion_anotaciones'];
        $id_equipo = $data['id_equipo'];
        $author_anotacion = $data['author_anotacion'];


        // verifica si son numeros
        if (!sonNumerico([$id_equipo])) {
            return sendResponse(400, [
                "Error" => "Datos invalidos, solo se permiten valores numericos"
                ]);
        }

        // verifica si son fechas
        if (!validarFecha([$fecha_anotacion])) {
            return sendResponse(400,['Error' => "Datos invalidos, solo se permite la fecha en el formato Y-m-d"]);
        }

        // verificar si son cadenas
        $camposValidar = [
            'descripcion_anotaciones' => $$descripcion_anotaciones, 'author_anotacion' => $author_anotacion];

         //validar los campos
         $resultado = validarArrayFlexible($camposValidar, 1, 1000);

         if (!$resultado['valido']) {
            return sendResponse(400, [
            "Error" => "Datos invalidos",
            "detalles" => $resultado['errores']
            ]);
        }

        $descripcion_anotaciones = $resultado['datos']['descripcion_anotaciones'];
        $author_anotacion = $resultado['datos']['author_anotacion'];

        try {
            $database = new Database();
            $conn = $database->getConnection();
            $stmt = $conn->prepare('INSERT INTO LICENCIAS (nombre_licencia,descripcion_licencia, numero_licencia,fecha_adquisicion,tipo_licencia,costo_licencia, id_equipo, usuarios_permitidos, proveedor_licencia, estado_licencia, duracion_licencia) VALUES(:nombre_licencia, :descripcion_licencia,:numero_licencia ,:fecha_adquisicion, :tipo_licencia ,:costo_licencia, :id_equipo,:usuarios_permitidos, :proveedor_licencia, :estado_licencia, :duracion_licencia)');
            $stmt->bindParam(':nombre_licencia',$nombre_licencia);
            $stmt->bindParam(':descripcion_licencia',$descripcion_licencia);
            $stmt->bindParam(':numero_licencia',$numero_licencia);
            $stmt->bindParam(':fecha_adquisicion',$fecha_adquisicion);
            $stmt->bindParam(':tipo_licencia',$tipo_licencia);
            $stmt->bindParam(':costo_licencia',$costo_licencia);
            $stmt->bindParam(':id_equipo',$id_equipo);
            $stmt->bindParam(':usuarios_permitidos',$usuarios_permitidos);
            $stmt->bindParam(':proveedor_licencia',$proveedor_licencia);
            $stmt->bindParam(':estado_licencia',$estado_licencia);
            $stmt->bindParam(':duracion_licencia',$duracion_licencia);



            if($stmt->execute()){
                // Responder con éxito
                return sendResponse(200, ["success" => "licencia " .$nombre_licencia. " guardado con exito. "]);
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
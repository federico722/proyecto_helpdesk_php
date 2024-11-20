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

class Create_paper_life{

    public static function crearHojaVida($token){
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
            'descripcion_anotaciones' => $descripcion_anotaciones, 'author_anotacion' => $author_anotacion];

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
            $stmt = $conn->prepare('INSERT INTO HOJAS_VIDA (fecha_anotacion, descripcion_anotaciones,id_equipo,author_anotacion) VALUES(:fecha_anotacion,:descripcion_anotaciones,:id_equipo, :author_anotacion)');
            $stmt->bindParam(':fecha_anotacion',$fecha_anotacion);
            $stmt->bindParam(':descripcion_anotaciones',$descripcion_anotaciones);
            $stmt->bindParam(':id_equipo',$id_equipo);
            $stmt->bindParam(':author_anotacion',$author_anotacion);




            if($stmt->execute()){
                // Responder con éxito
                return sendResponse(200, ["success" => "hoja de vida guardada con exito. "]);
               }else{
                   // Responder con error 500 si la inserción falla
                return sendResponse(500, ["error" => "No se pudo guardar la hoja de vida"]);
              }

        } catch (\Throwable $th) {
            error_log('Error al agregar la hoja de vida: ' . $th->getMessage());
            return sendResponse(500, [
                "error" => "ocurrio un error interno del servidor",
                "detalles" => $th->getMessage()
         ]);
        }
    }
}
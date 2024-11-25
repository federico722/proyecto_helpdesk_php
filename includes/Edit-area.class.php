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
require_once __DIR__ . '..\..\consultas-usuario\consultar_area-usuario.php';

class Edit_area{
    public static function Editar_area($token){

        try {

        // Obtener el cuerpo de la solicitud en formato JSON
        $data = json_decode(file_get_contents("php://input"), true);

        // Verificar si los datos necesarios están presentes
        if (!isset($data['nombre_area'],$data['id_area'])) {
            return sendResponse(400, ["Error" => "Faltan datos en la solicitud"]);
        }

            //verifica que el token no haya vencido
            $tokenValidation = validarTokenEnClase($token);

            if (!$tokenValidation ) {
                return sendResponse(400, ["Error" => "Token vencido"]);
            }

       // Obtengo los datos del formato json
       $nombre_area = $data['nombre_area'];
       $id_area = $data['id_area'];

        // verifica si son numeros
        if (!sonNumerico([$id_area])) {
        return sendResponse(400, [
            "Error" => "Datos invalidos, solo se permiten valores numericos"
            ]);
        }


        // verificar si son cadenas
        $camposValidar = [
            'nombre_area' => $nombre_area ];

         //validar los campos
         $resultado = validarArrayFlexible($camposValidar, 1, 1000);

         $nombre_area = $resultado['datos']['nombre_area'];

         if (!$resultado['valido']) {
            return sendResponse(400, [
            "Error" => "Datos invalidos",
            "detalles" => $resultado['errores']
            ]);
        }

        //validar que no este el nuevo nombre de area
        if (!consultarNombreArea($nombre_area,$id_area)) {
            return sendResponse(400,["Error" => "Nombre repetido"]);
        }

        $database = new Database();
        $conn = $database->getConnection();
        $stmt = $conn->prepare('UPDATE AREAS SET nombre_area = :nombre_area WHERE  id_area = :id_area');
        $stmt->bindParam(':id_area',$id_area);
        $stmt->bindParam(':nombre_area',$nombre_area);

            if($stmt->execute()){
                // Responder con los datos de categorías
            return sendResponse(200, [
                "Success" => "nombre de area actualizado con exito",
            ]);
               }else{
                   // Responder con error 500 si la inserción falla
                return sendResponse(500, ["error" => "No se pudo editar el nombre de area"]);
              }
        } catch (\Throwable $th) {
            error_log('Error al editar el nombre de area: ' . $th->getMessage());
            return sendResponse(500, [
                "error" => "ocurrio un error interno del servidor",
                "detalles" => $th->getMessage()
         ]);
        }

    }
}
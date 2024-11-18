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

class Create_Category{
    public static function crearCategoriaEquipo($token){
        // Obtener el cuerpo de la solicitud en formato JSON
        $data = json_decode(file_get_contents("php://input"), true);

          // Verificar si los datos necesarios están presentes
        if (!isset($data['nombre_categoria'])) {
            return sendResponse(400, ["Error" => "Faltan datos en la solicitud"]);
        }

        $nombre_categoria = $data['nombre_categoria'];

        if (!verificarToken($token)) {
            return sendResponse(400, ["Error" => "el token expiro"]);
        }


        // verificar si son cadenas
        if (!sonCadenas([$nombre_categoria])) {
             return sendResponse(400, ["Error" => "formato incorrecto no se permite valores numericos"]);
        }

        $camposValidar = [
         'nombre_categoria' => $nombre_categoria
        ];

        //validar los campos
        $resultado = validarArrayStrings($camposValidar, 3, 50);


        if (!$resultado['valido']) {
            return sendResponse(400, [
            "Error" => "Datos invalidos",
            "detalles" => $resultado['errores']
            ]);
        }


         // Usar los datos validados y limpios
         $nombre_categoria = $resultado['datos']['nombre_categoria'];

        try {

            $database = new Database();
            $conn = $database->getConnection();
            $stmt = $conn->prepare('INSERT INTO CATEGORIAS (nombre_categoria) VALUES(:nombre_categoria)');
            $stmt->bindParam(':nombre_categoria',$nombre_categoria);

            if($stmt->execute()){
                // Responder con éxito
                return sendResponse(200, ["success" => "Categoria: " . $nombre_categoria . " creada con exito"]);
               }else{
                   // Responder con error 500 si la inserción falla
                return sendResponse(500, ["error" => "No se pudo crear la categoria"]);
              }
        } catch (\Throwable $th) {
            error_log('Error al validar el usuario: ' . $th->getMessage());
            return sendResponse(500, [
                "error" => "ocurrio un error interno del servidor",
                "detalles" => $th->getMessage()
         ]);
        }


    }
}

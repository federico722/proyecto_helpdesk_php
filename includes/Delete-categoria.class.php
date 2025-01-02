<?php

require_once 'Database.class.php';
require_once __DIR__ . '..\..\logica\confirPassword.php';
require_once __DIR__ . '..\..\logica\validacionIntNull.php';
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
require_once __DIR__ . '..\..\consultas-usuario\consulta-nombre-categoria.php';
require_once __DIR__ . '..\..\consultas-usuario\consultar_categoria_equipos.php';

class delete_categoria{
    public static function Delete_categoria($token){

        try {

        // Obtener el cuerpo de la solicitud en formato JSON
        $data = json_decode(file_get_contents("php://input"), true);


        // Verificar si los datos necesarios están presentes
        if (!isset($data['nombre_categoria'])) {
            return sendResponse(400, ["Error" => "Faltan datos en la solicitud"]);
        }

        //verifica que el token no haya vencido
        $tokenValidation = validarTokenEnClase($token);
        if (!$tokenValidation ) {
            return sendResponse(400, ["Error" => "Token vencido"]);
        }

         $nombre_categoria = $data['nombre_categoria'];

        // verificar si son cadenas
        $camposValidar = [
            'nombre_categoria' => $nombre_categoria
           ];

        //validar los campos
        $resultado = validarArrayFlexible($camposValidar, 1, 1000);

        if (!$resultado['valido']) {
            return sendResponse(400, [
            "Error" => "Datos invalidos",
            "detalles" => $resultado['errores']
            ]);
        }


        $resultadoBuscarEquipo = consultar_equipos_categoria($nombre_categoria);

        //validar que no haya datos en la categoria 
        if ($resultadoBuscarEquipo ) {
            return sendResponse(400, [
                "ErrorCode01x" => "No es posible eliminar la categoria ahi equipos ingresados",
            ]);
        }

        $nombre_categoria = $resultado['datos']['nombre_categoria'];

        $database = new Database();
        $conn = $database->getConnection();
        $stmt = $conn->prepare('DELETE FROM CATEGORIAS WHERE nombre_categoria = :nombre_categoria');

        $stmt->bindParam(':nombre_categoria',$nombre_categoria);

        if($stmt->execute()){
            // Responder con los datos de categorías
        return sendResponse(200, [
            "success" => "nombre de categoria eliminado con exito",
        ]);
           }else{
               // Responder con error 500 si la inserción falla
            return sendResponse(500, ["error" => "No se pudo eliminar el nombre de categoria"]);
          }
        } catch (\Throwable $th) {
            error_log('Error al eliminar el nombre de categoria: ' . $th->getMessage());
            return sendResponse(500, [
                "error" => "ocurrio un error interno del servidor",
                "detalles" => $th->getMessage()
         ]);
        }


    }
}
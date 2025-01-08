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

class delete_licencia{
    public static function Delete_licencia($token, $nombre_licencia){

        try {

        // Verificar si los datos necesarios están presentes
        if (!isset( $nombre_licencia )) {
            return sendResponse(400, ["Error400FaltanDatos" => "Faltan datos en la solicitud"]);
        }

         //verifica que el token no haya vencido
        $tokenValidation = validarTokenEnClase($token);
        if (!$tokenValidation ) {
            return sendResponse(400, ["ErrorToken" => "Token vencido"]);
        }

        // verificar si son cadenas
        $camposValidar = [
            'nombre_licencia' => $nombre_licencia
           ];

        //validar los campos
        $resultado = validarArrayFlexible($camposValidar, 1, 1000);

        if (!$resultado['valido']) {
            return sendResponse(400, [
            "ErrorDatos" => "Datos invalidos",
            "detalles" => $resultado['errores']
            ]);
        }


        $nombre_licencia = $resultado['datos']['nombre_licencia'];

        $database = new Database();
        $conn = $database->getConnection();
        $stmt = $conn->prepare('DELETE FROM LICENCIAS WHERE nombre_licencia = :nombre_licencia');

        $stmt->bindParam(':nombre_licencia',$nombre_licencia);

        if($stmt->execute()){
            // Responder con los datos de categorías
        return sendResponse(200, [
            "success" => "licencia eliminada con exito",
        ]);
           }else{
               // Responder con error 500 si la inserción falla
            return sendResponse(500, ["error" => "No se pudo eliminar la licencia"]);
          }
        } catch (\Throwable $th) {
            error_log('Error interno: ' . $th->getMessage());
            return sendResponse(500, [
                "error" => "ocurrio un error interno del servidor",
                "detalles" => $th->getMessage()
         ]);
        }


    }
}
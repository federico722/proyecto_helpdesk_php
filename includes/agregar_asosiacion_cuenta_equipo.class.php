<?php

use Respect\Validation\Rules\Length;

require_once 'Database.class.php';
require_once __DIR__ . '..\..\logica\confirPassword.php';
require_once __DIR__ . '..\..\logica\confirmarCadena.php';
require_once __DIR__ . '..\..\vendor/autoload.php';
require_once __DIR__ . '..\..\logica\formatoRespuesta.php';
require_once __DIR__ . '..\..\logica\confirmarInt.php';
require_once __DIR__ . '..\..\credentials\verificar-token.php';
require_once __DIR__ . '..\..\logica\validacionesLongitud.php';
require_once __DIR__ . '..\..\logica\confirmarFecha.php';

class agregar_asosiacion_cuentas {
    public static function Agregar_asosiacion_cuentas($token){
        try {

            // Obtener el cuerpo de la solicitud en formato JSON
            $data = json_decode(file_get_contents("php://input"), true);
            
            //verifica que el token no haya vencido
            $tokenValidation = validarTokenEnClase($token);

            if (!$tokenValidation ) {
                return sendResponse(400, ["ErrorToken" => "Token vencido"]);
            }

            if (!isset($data['aplicaciones_referenciadas'], $data['nombre_usuario'], $data['contrasenia_usuario'])) {
                return sendResponse(400, ["Error" => "Faltan datos en la solicitud"]);
            }

            $aplicaciones_referenciadas = $data['aplicaciones_referenciadas'];
            $nombre_usuario = $data['nombre_usuario'];
            $contrasenia_usuario = $data['contrasenia_usuario'];

            $camposValidar = [
                'nombre_usuario' => $nombre_usuario, 
                'contrasenia_usuario' => $contrasenia_usuario
               ];

          //validar los campos
            $resultado = validarArrayFlexible($camposValidar, 1, 1000);
       
        if (!$resultado['valido']) {
            return sendResponse(400, [
                "Error" => "Datos invalidos",
                "detalles" => $resultado['errores']
            ]);
        }

        $nombre_usuario = $resultado['datos']['nombre_usuario'];
        $contrasenia_usuario = $resultado['datos']['contrasenia_usuario'];
            
        $database = new Database();
        $conn = $database->getConnection();
        for ($i=0; $i < count($aplicaciones_referenciadas) ; $i++) { 
            $stmt = $conn->prepare('');
        }
        


        } catch (\Throwable $th) {
            error_log('Error al agregar las asociaciones a las cuentas: ' . $th->getMessage());
            return sendResponse(500, [
                "error" => "ocurrio un error interno del servidor",
                "detalles" => $th->getMessage()
         ]);
        }
    }
}
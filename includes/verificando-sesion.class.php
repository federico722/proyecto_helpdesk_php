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
require_once __DIR__ . '..\..\credentials\verificar-token.php';


class Verificacion_sesion{
    public static function verificacion($token){
          // verificando que el tiempo del token de la sesion no haya expirado
        try {
            //OBTENEMOS LA RESPUESTA DEL TIEMPO DE EXPIRACION DEL TOKEN
            $respuestaToken = verificarToken($token);
            if ($respuestaToken) {
                //OBTENEMOS EL CUERPO DEL TOKEN
                $payload_token = obtenerIdUserDelToken($token);
               return sendResponse(200, [
                    "verificacion exitosa" => "token vigente",
                    "Usuario" => $payload_token
                ]);

            }
                $payload_token = obtenerIdUserDelToken($token);
            if ($payload_token) {
                $renovadoToken = crearToken($payload_token);
                return sendResponse(200, [
                    "Renovacion token" => $renovadoToken
                ]);
            }

            return sendResponse(500, [
                "Error" => "No se pudo renovar el token, error interno del servidor"
            ]);
        } catch (\Throwable $th) {
           return sendResponse(500, [
                "error" => "ocurrio un error al validar el token",
                "detalles" => $th->getMessage()
        ]);
        }
    }
}
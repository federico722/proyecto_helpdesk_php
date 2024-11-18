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


class Login{
    public static function iniciar_sesion(){

         // Obtener el cuerpo de la solicitud en formato JSON
         $data = json_decode(file_get_contents("php://input"), true);



           // Verificar si los datos necesarios están presentes
            if (!isset($data['usuario'], $data['contrasena'], $data['confirmarContrasena'] )) {
                header('HTTP/1.1 400 Bad Request');
                echo json_encode(["error" => "Faltan datos en la solicitud"]);
                exit;
            }

             // Obtengo los datos del formato json
             $usuario = $data['usuario'];
             $contrasena = $data['contrasena'];
             $confirmarContrasena = $data['confirmarContrasena'];

             // compara las contraseñas
            if (!comparePassword($contrasena,$confirmarContrasena)) {
                header('HTTP/1.1 404 No coincide la contrasena');
                echo json_encode(["error" => "Las contrasenas no coinciden"]);
                exit;
            }

            // verificar si son cadenas
            if (!sonCadenas([$usuario,$contrasena])) {
                header('HTTP/1.1 404 No son string');
                echo json_encode(["error"=> "Solo se permite string y el correo debe ser valido"]);
                exit;
            }

            try {
                //obtenemos la contraseña del usuario (si existe) en la base
                $contrasenaObtenida = consultarContraseña($usuario);

                //verificamos que si obtenemos la contraseña
                if ($contrasenaObtenida === false) {
                    return sendResponse(404, ["error" => "Usuario no encontrado"]);
                }

                //verificamos la contraseña del usuario con la contraseña de la base de datos
                if (verificarContrasena($contrasena, $contrasenaObtenida)) {

                    $token = crearToken($usuario);
                   return sendResponse(200, [
                       "success" => "Usuario verificado con exito",
                       "login" => true,
                       "token" => $token
                    ]);

                }else{
                  return sendResponse(401, ["error" => "Contraseña incorrecta"]);
                }

            } catch (\Throwable $th) {
                error_log('Error al validar el usuario: ' . $th->getMessage());
                sendResponse(500, [
                    "error" => "ocurrio un error al validar el usuario",
                    "detalles" => $th->getMessage()
            ]);
            }


    }
}

<?php

require_once 'Database.class.php';
require_once __DIR__ . '..\..\logica\confirPassword.php';
require_once __DIR__ . '..\..\logica\confirmarCadena.php';
require_once __DIR__ . '..\..\consultas-usuario\Consultar_contrasena.php';
require_once __DIR__ . '..\..\logica\verificarContrasena.php';
require_once __DIR__ . '..\..\vendor/autoload.php';
require_once __DIR__ . '..\..\credentials\create-token.php';
require_once __DIR__ . '..\..\credentials\obtener-payload-token.php';

class Login{
    public static function iniciar_sesion($jwt){

         // Obtener el cuerpo de la solicitud en formato JSON
         $data = json_decode(file_get_contents("php://input"), true);

         if (!$jwt) {
            header('HTTP/1.1 400 Bad Request');
            echo json_encode(["error" => "Falta el header en la solicitud"]);
            exit;
         }


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

            if (!sonCadenas([$usuario,$contrasena])) {
                header('HTTP/1.1 404 No son string');
                echo json_encode(["error"=> "Solo se permite string y el correo debe ser valido"]);
                exit;
            }


    }
}

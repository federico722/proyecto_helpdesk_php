<?php

require_once 'Database.class.php';
require_once __DIR__ . '..\..\logica\confirPassword.php';
require_once __DIR__ . '..\..\logica\confirmarCadena.php';
require_once __DIR__ . '..\..\consultas-usuario\Consultar_contrasena.php';
require_once __DIR__ . '..\..\logica\verificarContrasena.php';
require_once __DIR__ . '..\..\consultas-usuario\consultar_rol.php';
require_once __DIR__ . '..\..\vendor/autoload.php';
require_once __DIR__ . '..\..\credentials\create-token.php';
require_once __DIR__ . '..\..\credentials\obtener-payload-token.php';
require_once __DIR__ . '..\..\logica\formatoRespuesta.php';
require_once __DIR__ . '..\..\logica\confirmarInt.php';
require_once __DIR__ . '..\..\credentials\verificar-token.php';
require_once __DIR__ . '..\..\logica\validacionesLongitud.php';
require_once __DIR__ . '..\..\logica\confirmarFecha.php';
require_once __DIR__ . '..\..\credentials\verificarRol.php';

class superAdminRecuperarContrasena{
    public static function superAdminRecuperarContrasenaUsuario(){
      try {
        // Obtener el cuerpo de la solicitud en formato JSON
       $data = json_decode(file_get_contents("php://input"), true);



       // Verificar si los datos necesarios están presentes
        if (!isset($data['usuario'], $data['contrasena'] )) {
            header('HTTP/1.1 400 Bad Request');
            echo json_encode(["error400" => "Faltan datos en la solicitud"]);
            exit;
        }

         // Obtengo los datos del formato json
         $usuario = $data['usuario'];
         $contrasena = $data['contrasena'];
        
         // verificar si son cadenas
         if (!sonCadenas([$usuario,$contrasena])) {
          header('HTTP/1.1 404 No son string');
          echo json_encode(["errorString" => "Solo se permite string y el correo debe ser valido"]);
          exit;
      }

      //obtenemos la contraseña del usuario (si existe) en la base
       $contrasenaObtenida = consultarContraseña($usuario);

      //verificamos que si obtenemos la contraseña
       if ($contrasenaObtenida === false) {
           return sendResponse(404, ["errorNoEncontrado" => "administrador no encontrado"]);
      }


         //verificamos el rol
         $rol = consultarRol($usuario);

       if ($rol !== "superAdmin") {
          header('HTTP/1.1 404 No coincide el rol');
          echo json_encode(["errorRol" => "El rol no es super administrador"]);
          exit;
       }

      //verificamos la contraseña del usuario con la contraseña de la base de datos
      if (verificarContrasena($contrasena, $contrasenaObtenida)) {

          $token = crearToken($usuario);

          return sendResponse(200, [
              "success" => "Usuario verificado con exito",
              "login" => true,
              "token" => $token,
              "rol" => $rol
          ]);

      }else{
           return sendResponse(401, ["errorContrasenaIncorrecta" => "Contraseña incorrecta"]);
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

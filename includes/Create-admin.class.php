<?php

require_once 'Database.class.php';
require_once __DIR__ . '..\..\logica\confirPassword.php';
require_once __DIR__ . '..\..\logica\validarFormatoCorreo.php';
require_once __DIR__ . '..\..\logica\confirmarCadena.php';
require_once __DIR__ . '..\..\consultas-usuario\Consultar_contrasena.php';
require_once __DIR__ . '..\..\logica\verificarContrasena.php';

class Create_admin{


    public static function crear_administrador(){
        try {

           // Obtener el cuerpo de la solicitud en formato JSON
           $data = json_decode(file_get_contents("php://input"), true);

           // Verificar si los datos necesarios están presentes
           if (!isset($data['usuario'], $data['contrasena'], $data['confirmarContrasena'] ,$data['correo'])) {
               header('HTTP/1.1 400 Bad Request');
               echo json_encode(["error" => "Faltan datos en la solicitud"]);
               exit;
           }


           // Obtengo los datos del formato json
           $usuario_agente = $data['usuario'];
           $contrasena_agente = $data['contrasena'];
           $confirmarContrasena = $data['confirmarContrasena'];
           $correo_agente = $data['correo'];


           // compara las contraseñas
           if (!comparePassword($contrasena_agente,$confirmarContrasena)) {
               header('HTTP/1.1 Password404 No coincide la contrasena');
               echo json_encode(["error" => "Las contrasenas no coinciden"]);
               exit;
           }

           if (!sonCadenas([$usuario_agente])) {
               header('HTTP/1.1 400 No son string');
               echo json_encode(["error"=> "Solo se permite string"]);
               exit;
           }

           //verifica si se obtuvo la contraseña de la base de datos
          // $contrasenaBD = consultarContraseña(($usuario));
/*
           if ($contrasenaBD) {
               if (!verificarContrasena($contrasena,$contrasenaBD)) {
                   header('HTTP/1.1 404 contrasena incorrecta');
                   echo json_encode(["error"=> "la contraseña no coincide"]);
                   exit;
               }
           }else{
               header('HTTP/1.1 500 No se obtuvo la contraseña');
               echo json_encode(["error"=> "Error al consultar la contrasena"]);
               exit;
           }*/

            $rol = 1;

            $bycripPassword = password_hash($contrasena_agente, PASSWORD_BCRYPT);

            $database = new Database();
            $conn = $database->getConnection();
            $stmt = $conn->prepare('INSERT INTO agentes (usuario_agente, contrasena_agente, correo_agente, id_rol) VALUES (:usuario_agente, :contrasena_agente, :correo_agente , 1)');
            $stmt->bindParam(':usuario_agente',$usuario_agente);
            $stmt->bindParam(':contrasena_agente',$bycripPassword);
            $stmt->bindParam(':correo_agente',$correo_agente);
            

            if($stmt->execute()){
             // Responder con éxito
             header('HTTP/1.1 200 OK');
             echo json_encode(["success" => "Administrador creado con exito"]);
            }else{
                // Responder con error 500 si la inserción falla
                header('HTTP/1.1 500 Error interno del servidor');
                echo json_encode(["error" => "No se pudo crear el administrador"]);
           }
        } catch (\Throwable $th) {
          // Manejar cualquier excepción y devolver un error genérico
          error_log('Error al crear administrador: ' . $th->getMessage());
          header('HTTP/1.1 500 Error interno del servidor');
          echo json_encode(["error" => "Ocurrio un error al crear el administrador", "detalles" => $th->getMessage()]);
        }

    }

}
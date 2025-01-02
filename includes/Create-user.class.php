<?php

require_once 'Database.class.php';
require_once __DIR__ . '..\..\logica\confirPassword.php';
require_once __DIR__ . '..\..\logica\validarFormatoCorreo.php';
require_once __DIR__ . '..\..\logica\confirmarCadena.php';

class Create_user{


    public static function crear_usuario(){

        try {

            // Obtener el cuerpo de la solicitud en formato JSON
            $data = json_decode(file_get_contents("php://input"), true);

            // Verificar si los datos necesarios están presentes
            if (!isset($data['usuario'], $data['contrasena'], $data['correo'], $data['confirmarContrasena'])) {
                header('HTTP/1.1 400 Bad Request');
                echo json_encode(["error" => "Faltan datos en la solicitud"]);
                exit;
            }

            // Obtengo los datos del formato json
            $usuario = $data['usuario'];
            $contrasena = $data['contrasena'];
            $correo = $data['correo'];
            $confirmarContrasena = $data['confirmarContrasena'];

            // compara las contraseñas
            if (!comparePassword($contrasena,$confirmarContrasena)) {
                header('HTTP/1.1 404 No coincide la contrasena');
                echo json_encode(["error" => "Las contrasenas no coinciden"]);
                exit;
            }

            // verifica si los datos son cadenas y si el correo tiene un formato valido
            if (!esCorreoValido($correo) || !sonCadenas([$usuario,$contrasena,$correo])) {
                header('HTTP/1.1 404 No son string');
                echo json_encode(["error"=> "Solo se permite string y el correo debe ser valido"]);
                exit;
            }


            // Encriptar la contraseña antes de guardarla
             $contrasenaHash = password_hash($contrasena, PASSWORD_BCRYPT);

            $rol = 2;

            $database = new Database();
            $conn = $database->getConnection();
            $stmt = $conn->prepare('INSERT INTO AGENTES (usuario_agente,contrasena_agente,correo_agente,id_rol) VALUES(:usuario,:contrasenaHash,:correo,:id_rol)');
            $stmt->bindParam(':usuario',$usuario);
            $stmt->bindParam(':contrasenaHash',$contrasenaHash);
            $stmt->bindParam(':correo',$correo);
            $stmt->bindParam(':id_rol',$rol); // Agregar esta línea
           // var_dump(['usuario' => $usuario, 'contrasena' => $contrasenaHash, 'correo' => $correo, 'id_rol' => $rol]);

            if($stmt->execute()){
             // Responder con éxito
             header('HTTP/1.1 200 OK');
             echo json_encode(["success" => "Usuario creado con exito", "Accept" => true]);
            }else{
                // Responder con error 500 si la inserción falla
                header('HTTP/1.1 500 Error interno del servidor');
                echo json_encode(["error" => "No se pudo crear el usuario"]);
           }
        } catch (\Throwable $th) {
          // Manejar cualquier excepción y devolver un error genérico
          error_log('Error al crear usuario: ' . $th->getMessage());
          header('HTTP/1.1 500 Error interno del servidor');
          echo json_encode(["error" => "Ocurrio un error al crear el usuario", "detalles" => $th->getMessage()]);
        }

    }

}
<?php

require_once 'Database.class.php';
require_once '../../logica/confirPassword.php';

class Create_user{


    public static function crear_usuario($usuario,$contraseña,$correo,$confirmarContraseña){

            // Obtener el cuerpo de la solicitud en formato JSON
            $data = json_decode(file_get_contents("php://input"), true);

            // Verificar si los datos necesarios están presentes
            if (!isset($data['usuario'], $data['contraseña'], $data['correo'], $data['confirmarContraseña'])) {
                header('HTTP/1.1 400 Bad Request');
                echo json_encode(["error" => "Faltan datos en la solicitud"]);
                exit;
            }

            // Obtengo los datos del formato json
            $usuario = $data['usuario'];
            $contraseña = $data['contraseña'];
            $correo = $data['correo'];
            $confirmarContraseña = $data['confirmarContraseña'];

            // compara las contraseñas
            if (!comparePassword($contraseña,$confirmarContraseña)) {
                header('HTTP/1.1 404 No coincide la contraseña');
                echo json_encode(["error" => "Las contraseñas no coinciden"]);
                exit;
            }

        try {
            // Encriptar la contraseña antes de guardarla
             $contraseñaHash = password_hash($contraseña, PASSWORD_BCRYPT);

            $rol = 1;

            $database = new Database();
            $conn = $database->getConnection();
            $stmt = $conn->prepare('INSERT INTO AGENTES (usuario_agente,contraseña_agente,correo_agente,id_rol) VALUES(:usuario,:contraseña,:correo,:id_rol)');
            $stmt->bindParam(':usuario',$usuario);
            $stmt->bindParam(':contraseña',$contraseña);
            $stmt->bindParam(':correo',$correo);
            $stmt->bindParam(':id_rol',$rol);

            if($stmt->execute()){
             // Responder con éxito
             header('HTTP/1.1 200 OK');
             echo json_encode(["success" => "Usuario creado con éxito"]);
            }else{
                // Responder con error 500 si la inserción falla
                header('HTTP/1.1 500 Error interno del servidor');
                echo json_encode(["error" => "No se pudo crear el usuario"]);
           }
        } catch (\Throwable $th) {
          // Manejar cualquier excepción y devolver un error genérico
          header('HTTP/1.1 500 Error interno del servidor');
          echo json_encode(["error" => "Ocurrió un error al crear el usuario", "detalles" => $th->getMessage()]);
        }

    }

}
<?php

require_once 'Database.class.php';
require_once __DIR__ . '..\..\vendor/autoload.php';
require_once __DIR__ . '..\..\logica\formatoRespuesta.php';

class ResetPassword {
    public static function cambiarContrasena(){
       try {
         // Obtener el cuerpo de la solicitud en formato JSON
        $data = json_decode(file_get_contents("php://input"), true);

        // Verificar si los datos necesarios están presentes
        if (!isset($data['id_agente'], $data['nuevaContrasena'])) {
         return sendResponse(400, ["Error" => "Faltan datos en la solicitud"]);
         }

         // Obtengo los datos del formato json
        $id_agente = $data['id_agente'];
        $nuevaContrasena = $data['nuevaContrasena'];

         // Conectar a la base de datos
         $database = new Database();
         $conn = $database->getConnection();


        // Encriptar la nueva contraseña
        $hashedPassword = password_hash($nuevaContrasena, PASSWORD_BCRYPT);

        // Actualizar la contraseña en la base de datos
        $stmt = $conn->prepare("UPDATE agentes SET contrasena_agente = :hashedPassword WHERE id_agente = :id_agente ");
        $stmt->bindParam(':hashedPassword',$hashedPassword); 
        $stmt->bindParam(':id_agente',$id_agente);
        $stmt->execute();

        return sendResponse(200, ["mensaje" => "Contraseña actualizada correctamente"]);

       } catch (Exception $e) {
          return sendResponse(500, ["error" => "Error interno", "detalles" => $e->getMessage()]);
       }
    }
}
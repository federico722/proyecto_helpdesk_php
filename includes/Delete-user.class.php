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
require_once __DIR__ . '..\..\logica\confirmarInt.php';

class Delete{
    public static function delete_users($id_agente){

        $id = $id_agente;

        // Verificar si los datos necesarios están presentes
        if (!isset($id)) {
           return sendResponse(400, ["Error" => "El id del usuario no fue enviado"]);
        }

          // verificar si son cadenas
          if (!sonNumerico([$id])) {
            return sendResponse(400, ["Error" => "formato incorrecto no ser permite valores no numericos"]);
        }

        try {
            $database = new Database();
            $conn = $database->getConnection();

            $stmt = $conn->prepare('DELETE FROM AGENTES WHERE id_agente=:id_agente');
            $stmt->bindParam(':id_agente', $id_agente);

            if($stmt->execute()){
                return sendResponse(200, [
                    "success" => "Usuario eliminado con exito",
                ]);
            }else{
            return sendResponse(500, ["500" => "Error interno del servidor"]);
            }

        } catch (\Throwable $th) {
            error_log('Error al eliminar el usuario: ' . $th->getMessage());
            sendResponse(500, [
                "error" => "ocurrio un error interno del servidor al eliminar el usuario",
                "detalles" => $th->getMessage()
        ]);
        }
    }
}
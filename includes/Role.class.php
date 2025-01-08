<?php
require_once 'Database.class.php';
require_once __DIR__ . '..\..\logica\formatoRespuesta.php';
require_once __DIR__ . '..\..\vendor/autoload.php';


class Role{
    public static function create_role($role){
        try {
            $database = new Database();
            $conn = $database->getConnection();
    
            $stmt = $conn->prepare('INSERT INTO ROLES (nombre_rol)VALUES(:role)');
            $stmt->bindParam(':role', $role);
    
            if($stmt->execute()){
                header('HTTP/1.1 200 Rol creado con exito');
            }else{
                header('HTTP/1.1 404 Rol no se ha creado correctamente');
            }
        } catch (\Throwable $th) {
            error_log('Error al crear el rol: ' . $th->getMessage());
            return sendResponse(500, [
                "error" => "ocurrio un error interno del servidor",
                "detalles" => $th->getMessage()
         ]);
        }


    }
}

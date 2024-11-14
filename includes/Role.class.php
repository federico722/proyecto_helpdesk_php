<?php
require_once 'Database.class.php';

class Role{
    public static function create_role($role){
        $database = new Database();
        $conn = $database->getConnection();

        $stmt = $conn->prepare('INSERT INTO ROLES (nombre_rol)VALUES(:role)');
        $stmt->bindParam(':role', $role);

        if($stmt->execute()){
            header('HTTP/1.1 200 Rol creado con exito');
        }else{
            header('HTTP/1.1 404 Rol no se ha creado correctamente');
        }

    }
}

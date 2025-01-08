<?php


require_once __DIR__ . '..\..\vendor/autoload.php';

use Firebase\JWT\Key;
use Firebase\JWT\JWT;
use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Función para obtener el id_user del token
function obtenerIdUserDelToken($jwt) {
    try {
        // Verifica si el JWT está presente
        if (empty($jwt)) {
            echo "El JWT está vacío o no se proporcionó.";
            return null;
        }

        // Obtener la clave secreta desde el archivo .env
        $secretKey = $_ENV['SECRET_KEY'];
        if (empty($secretKey)) {
            echo "La clave secreta no está definida.";
            return null;
        }

        // Decodificar el JWT y obtener el payload
        $decoded = JWT::decode($jwt, new Key($secretKey,'HS256'));  // Usamos la clase Key

        // Verificar el contenido del payload
        //print_r($decoded);  // Para ver el contenido del token decodificado

        // Extraer el id_user del payload
        if (isset($decoded->id_user)) {
            $idUser = $decoded->id_user;
           // echo "idUser: " . $idUser;
            return $idUser;
        } else {
           // echo "El token no contiene el campo id_user.";
            return null;
        }
    } catch (Exception $e) {
        // Si el token no es válido o ha expirado, capturamos el error
        echo "Error: " . $e->getMessage();  // Imprimir el mensaje del error
        return null;
    }
}
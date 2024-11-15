<?php

require_once __DIR__ . '..\..\vendor/autoload.php';


use Firebase\JWT\JWT;
use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$secred_key = $_ENV['SECRET_KEY'];


// Función para generar el token JWT
function crearToken($idUser) {
    // Obtener la clave secreta desde el archivo .env
    $secretKey = $_ENV['SECRET_KEY'];

    // Datos para el token
    $issuedAt = time(); // Fecha de emisión
    $expirationTime = $issuedAt + 18000;  // El token expirará en 1 hora (3600 segundos)

    // Estructura del payload (los datos que irán dentro del token)
    $payload = [
        'iat' => $issuedAt,         // Fecha de emisión
        'exp' => $expirationTime,   // Fecha de expiración
        'id_user' => $idUser,       // El id del usuario
    ];

     // Depurar el payload antes de crear el token
     //echo "Payload antes de codificar: ";
     //print_r($payload);  // Imprimir el contenido del payload

    // Crear el token JWT
    $jwt = JWT::encode($payload, $secretKey, 'HS256');

    return $jwt;
}
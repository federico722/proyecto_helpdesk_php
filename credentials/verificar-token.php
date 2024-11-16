<?php

// funcion para verificar el token

require_once __DIR__ . "../../vendor/autoload.php";

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . "/../");
$dotenv->load();

$secretKey = $_ENV['SECRET_KEY'];

//Funcion para verificar el token JWT
function verificarToken($jwt){
    global $secretKey;

    try {
       // Decodifica el token
       $decoded = JWT::decode($jwt, new Key($secretKey, 'HS256'));

       //Verifica si el token ha expirado
       $now = time();
       if ($decoded->exp < $now) {
           return [
             'success' => false,
             'message' => 'Token expirado'
           ];
       }

       //el token es valido
       return true;
    } catch (Exception $e) {
        //Error al decodificar o verificar el token
        return [
            'success' => false,
            'message' => 'token invalido: ' . $e->getMessage()
        ];
    }
}

<?php

// funcion para verificar el token

require_once __DIR__ . "../../vendor/autoload.php";
require_once __DIR__ . "../../logica/formatoRespuesta.php";

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . "/../");
$dotenv->load();

$secretKey = $_ENV['SECRET_KEY'];

//Funcion para verificar el token JWT
function verificarToken($jwt) {
    global $secretKey;

    try {
        // Decodifica el token
        $decoded = JWT::decode($jwt, new Key($secretKey, 'HS256'));

        // Verifica si el token ha expirado
        $now = time();
        if ($decoded->exp < $now) {
            return [
                'success' => false,
                'message' => 'Token expirado'
            ];
        }

        // Token válido
        return [
            'success' => true,
            'payload' => (array)$decoded
        ];
    } catch (Exception $e) {
        // Error al decodificar o verificar el token
        return [
            'success' => false,
            'message' => 'Token inválido: ' . $e->getMessage()
        ];
    }
}

function validarTokenEnClase($token) {
    $resultado = verificarToken($token);

    if (!$resultado['success']) {
        return false;
    }

    // Si el token es válido, devuelve null para continuar con el flujo normal
    return true;
}

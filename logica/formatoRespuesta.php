<?php

//funcion para centralizar la respuesta json

function sendResponse($statusCode, $data){
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}



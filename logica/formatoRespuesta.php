<?php

//funcion para centralizar la respuesta json

function sendResponse($statusCode, $response){
    header("Content-Type: application/json");
    http_response_code($statusCode);
    echo json_encode($response);
}



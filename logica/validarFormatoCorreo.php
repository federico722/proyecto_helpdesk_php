<?php

// Funcion para validar el formato de correo
function esCorreoValido($correo){
    return filter_var($correo , FILTER_VALIDATE_EMAIL) !==false;
}



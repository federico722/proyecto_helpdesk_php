<?php

//Funcion para validar la contraseña  hasheada
function verificarContrasena($userPassword,$bdPassword){
    if (password_verify($userPassword, $bdPassword)) {
        return true;
    }

    return false;
}

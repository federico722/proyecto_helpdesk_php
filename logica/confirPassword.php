
<?php
// comparar una contraseña con otra contraseña
function comparePassword($password, $copypassword) {
    // Verificamos si ambos son cadenas y si son iguales
    return is_string($password) && is_string($copypassword) && $password === $copypassword;
}

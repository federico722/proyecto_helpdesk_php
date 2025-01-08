<?php

require_once 'Database.class.php';
require_once __DIR__ . '..\..\logica\confirPassword.php';
require_once __DIR__ . '..\..\logica\validacionIntNull.php';
require_once __DIR__ . '..\..\logica\confirmarCadena.php';
require_once __DIR__ . '..\..\consultas-usuario\Consultar_contrasena.php';
require_once __DIR__ . '..\..\logica\verificarContrasena.php';
require_once __DIR__ . '..\..\vendor/autoload.php';
require_once __DIR__ . '..\..\credentials\create-token.php';
require_once __DIR__ . '..\..\credentials\obtener-payload-token.php';
require_once __DIR__ . '..\..\logica\formatoRespuesta.php';
require_once __DIR__ . '..\..\logica\confirmarInt.php';
require_once __DIR__ . '..\..\credentials\verificar-token.php';
require_once __DIR__ . '..\..\logica\validacionesLongitud.php';
require_once __DIR__ . '..\..\logica\confirmarFecha.php';
require_once __DIR__ . '..\..\consultas-usuario\consultar-nombre-agente.php';
require_once __DIR__ . '..\..\consultas-usuario\consulta-nombre-categoria.php';

class Edit_name_category{
    public static function edit_name_category($token){

        try {

        // Obtener el cuerpo de la solicitud en formato JSON
        $data = json_decode(file_get_contents("php://input"), true);


        // Verificar si los datos necesarios están presentes
        if (!isset($data['nombre_seleccionado_categoria'], $data['nombre_categoria'])) {
            return sendResponse(400, ["Error" => "Faltan datos en la solicitud"]);
        }
         //verifica que el token no haya vencido
        $tokenValidation = validarTokenEnClase($token);
        if (!$tokenValidation ) {
            return sendResponse(400, ["Error" => "Token vencido"]);
        }


         $nombre_seleccionado_categoria = $data['nombre_seleccionado_categoria'];
         $nombre_categoria = $data['nombre_categoria'];

        // verificar si son cadenas
        $camposValidar = [
            'nombre_seleccionado_categoria' => $nombre_seleccionado_categoria,
            'nombre_categoria' => $nombre_categoria
           ];

        //validar los campos
        $resultado = validarArrayFlexible($camposValidar, 1, 1000);

        if (!$resultado['valido']) {
            return sendResponse(400, [
            "Error" => "Datos invalidos",
            "detalles" => $resultado['errores']
            ]);
        }

        $resultadoCategoriaNombre = consultarCategoriaNombre($nombre_categoria);

        //valida que no se repita el nombre de categoria
        if ($resultadoCategoriaNombre ) {
            return sendResponse(400, [
                "ErrorCode01" => "Ya existe esta categoria",
            ]);
        }

        $nombre_seleccionado_categoria = $resultado['datos']['nombre_seleccionado_categoria'];
        $nombre_categoria = $resultado['datos']['nombre_categoria'];

        $database = new Database();
        $conn = $database->getConnection();
        $stmt = $conn->prepare('UPDATE CATEGORIAS SET nombre_categoria = :nombre_categoria WHERE nombre_categoria = :nombre_seleccionado_categoria ');

        $stmt->bindParam(':nombre_categoria',$nombre_categoria);
        $stmt->bindParam(':nombre_seleccionado_categoria',$nombre_seleccionado_categoria);

        if($stmt->execute()){
            // Responder con los datos de categorías
        return sendResponse(200, [
            "success" => "nombre de categoria actualizado con exito",
        ]);
           }else{
               // Responder con error 500 si la inserción falla
            return sendResponse(500, ["error" => "No se pudo editar el nombre de categoria"]);
          }
        } catch (\Throwable $th) {
            error_log('Error al editar el nombre de categoria: ' . $th->getMessage());
            return sendResponse(500, [
                "error" => "ocurrio un error interno del servidor",
                "detalles" => $th->getMessage()
         ]);
        }


    }
}
<?php

require_once 'Database.class.php';
require_once __DIR__ . '..\..\logica\confirPassword.php';
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

class Edit_area{
    public static function Editar_area($token){

        try {

        // Obtener el cuerpo de la solicitud en formato JSON
        $data = json_decode(file_get_contents("php://input"), true);

        // Verificar si los datos necesarios están presentes
        if (!isset($data['nombre_area'],$data['id_area'])) {

        return sendResponse(400, ["Error" => "Faltan datos en la solicitud"]);
        }

            //verifica que el token no haya vencido
            $tokenValidation = validarTokenEnClase($token);

            if (!$tokenValidation ) {
                return sendResponse(400, ["Error" => "Token vencido"]);
            }

       // Obtengo los datos del formato json
       $nombre_area = $data['nombre_area'];
       $id_area = $data['id_area'];

        // verifica si son numeros
        if (!sonNumerico([$id_area])) {
        return sendResponse(400, [
            "Error" => "Datos invalidos, solo se permiten valores numericos"
            ]);
        }


        // verificar si son cadenas
        $camposValidar = [
            'nombre_area' => $nombre_area ];

         //validar los campos
         $resultado = validarArrayFlexible($camposValidar, 1, 1000);

         $nombre_area = $resultado['datos']['nombre_area'];

         if (!$resultado['valido']) {
            return sendResponse(400, [
            "Error" => "Datos invalidos",
            "detalles" => $resultado['errores']
            ]);
        }



        $database = new Database();
        $conn = $database->getConnection();
        $stmt = $conn->prepare('UPDATE EQUIPOS SET nombre_equipo = :nombre_equipo ,
        caracteristicas_del_sistema = :caracteristicas_del_sistema ,
        fecha_de_adquisicion = :fecha_de_adquisicion,
        costo_adquisicion = :costo_adquisicion,
        valor_residual = :valor_residual,
        imagen_equipo = :imagen_equipo,
        descripcion_equipo = :descripcion_equipo,
        modelo_equipo = :modelo_equipo,
        numero_serial = :numero_serial,
        proveedor_equipo = :proveedor_equipo,
        id_agente = :id_agente,
        ubicacion_equipo = :ubicacion_equipo,
        vida_util_equipo = :vida_util_equipo,
        estado_equipo = :estado_equipo,
        depreciacion_equipo = :depreciacion_equipo WHERE  id_equipo = :id_equipo');
        $stmt->bindParam(':nombre_equipo',$nombre_equipo);
        $stmt->bindParam(':caracteristicas_del_sistema',$caracteristicas_del_sistema);
        $stmt->bindParam(':fecha_de_adquisicion',$fecha_de_adquisicion);
        $stmt->bindParam(':costo_adquisicion', $costo_adquisicion);
        $stmt->bindParam(':valor_residual', $valor_residual);
        $stmt->bindParam(':imagen_equipo', $imagen_equipo);
        $stmt->bindParam(':descripcion_equipo', $descripcion_equipo);
        $stmt->bindParam(':modelo_equipo', $modelo_equipo);
        $stmt->bindParam(':numero_serial', $numero_serial);
        $stmt->bindParam(':proveedor_equipo', $proveedor_equipo);
        $stmt->bindParam(':id_agente', $id_agente);
        $stmt->bindParam(':ubicacion_equipo', $ubicacion_equipo);
        $stmt->bindParam(':vida_util_equipo', $vida_util_equipo);
        $stmt->bindParam(':estado_equipo', $estado_equipo);
        $stmt->bindParam(':depreciacion_equipo', $depreciacion_equipo);
        $stmt->bindParam(':id_equipo',$id_equipo);


            if($stmt->execute()){
                // Responder con los datos de categorías
            return sendResponse(200, [
                "Success" => "Equipo actualizado con exito",
            ]);
               }else{
                   // Responder con error 500 si la inserción falla
                return sendResponse(500, ["error" => "No se pudo editar el equipo"]);
              }
        } catch (\Throwable $th) {
            error_log('Error al editar el equipo: ' . $th->getMessage());
            return sendResponse(500, [
                "error" => "ocurrio un error interno del servidor",
                "detalles" => $th->getMessage()
         ]);
        }

    }
}
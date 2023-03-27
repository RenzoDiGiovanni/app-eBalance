<?php

//include "conexion.php";
global $conexion; 

$dbhost = "localhost";
$dbuser = "root";
$dbpass = "";
$bd = "ebalance";
$conexion = mysqli_connect($dbhost, $dbuser, $dbpass, $bd);

if (isset($_SERVER['HTTP_ORIGIN'])) {
    // should do a check here to match $_SERVER['HTTP_ORIGIN'] to a
    // whitelist of safe domains
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');    // cache for 1 day
}
// Access-Control headers are received during OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");         

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

}

$tipo = $_SERVER["REQUEST_METHOD"];

switch ($tipo) {

    case "DELETE":
        parse_str(file_get_contents("php://input"), $deleteData);
        $idEliminar = $deleteData["idEliminado"];

        $consulta_eliminar = "DELETE FROM `datos` WHERE `datos`.`id` = " . $idEliminar;
        $resultado_consulta = mysqli_query($conexion, $consulta_eliminar);
        $dev = array();
        http_response_code(200);
        $dev["mensaje"] = "Seccion eliminada con exito";
        $dev["codigo"] = 200;
        echo json_encode($dev);

        break;

    case "GET":
        $accion = filter_input(INPUT_GET, "accion");
        $elegido = filter_input(INPUT_GET, "datoElegido");

        if ($accion === "ingresar") {
        $user = filter_input(INPUT_GET, "userData");
        $pass = filter_input(INPUT_GET, "passData");

        $dev = array();

        $consultaLogin = "SELECT * FROM `usuarios` WHERE `usuarios`.`usuario` LIKE '" . $user . "' AND `contrasenia` LIKE '" . $pass . "'";

        $resultadoLogin = mysqli_query($conexion, $consultaLogin);
        $cantidadResultados = mysqli_num_rows($resultadoLogin);

        if ($cantidadResultados === 1) {
            http_response_code(200);
            $usuario = mysqli_fetch_assoc($resultadoLogin);
            $dev["id"] = $usuario["id"];
            $dev["mensaje"] = "Ingreso con exito";
            $dev["codigo"] = 200;
        } else {
            http_response_code(404);
            $dev["mensaje"] = "No ingreso con exito";
            $dev["sql"] = $consulta_insertar;
            $dev["codigo"] = 404;
        }

        echo json_encode($dev);
    } else if ($accion === "datosReceta") {

        $consultaDatosReceta = "SELECT * FROM `recetas`";
        $resultadoDatosReceta = mysqli_query($conexion, $consultaDatosReceta);
        $dev = array();

        while ($fila = mysqli_fetch_assoc($resultadoDatosReceta)) {
            array_push($dev, $fila);
        }

        echo json_encode($dev);
        
    } else if ($accion === "ampliacionReceta") {

        $consultaAmpliacionReceta = "SELECT * FROM `recetas` WHERE `recetas`.`id` = " . $elegido;;
        $resultadoAmpliacionReceta = mysqli_query($conexion, $consultaAmpliacionReceta);
        $dev = array();

        while ($fila = mysqli_fetch_assoc($resultadoAmpliacionReceta)) {
            array_push($dev, $fila);
        }

        echo json_encode($dev);
        
    } else if ($accion === "datosRecipientes") {
        $consultaDatos = "SELECT * FROM `datos` WHERE `datos`.`idUsuario` = " . $elegido;
        $resultadoDatos = mysqli_query($conexion, $consultaDatos);
        $dev = array();

        while ($fila = mysqli_fetch_assoc($resultadoDatos)) {
            array_push($dev, $fila);
        }

        echo json_encode($dev);
        
    } 
    
    break;

    case "POST":
        $accion = filter_input(INPUT_POST, "accion");

        if ($accion === "nuevoRecipiente") {
            $idUsuario = filter_input(INPUT_POST, "idUsuarioData");
            $tamanio = filter_input(INPUT_POST, "tamanioData");
            $alimento = filter_input(INPUT_POST, "alimentoData");
            $pesoMaximo = filter_input(INPUT_POST, "pesoMaximoData");

            $consulta_insertar = "INSERT INTO `datos` (`id`, `idUsuario`, `tamanio`, `alimento`, `pesoMaximo`, `pesoActual`) VALUES (NULL, '" . $idUsuario . "', '" . $tamanio . "', '" . $alimento . "', '" . $pesoMaximo . "', '0');";
            $resultado_insertar = mysqli_query($conexion, $consulta_insertar);
            $dev = array();
            if ($resultado_insertar) {
                http_response_code(200);
                $dev["mensaje"] = "Registro insertado con éxito";
                $dev["codigo"] = 200;
            } else {
                http_response_code(404);
                $dev["mensaje"] = "Registro NO insertado con éxito";
                $dev["sql"] = $consulta_insertar;
                $dev["codigo"] = 404;
            }
            echo json_encode($dev);
        }  else if ($accion === "agregarReceta") {
            $nombre = filter_input(INPUT_POST, "nombreData");
            $descripcion = filter_input(INPUT_POST, "descripcionData");
            $creador = filter_input(INPUT_POST, "creadorData");
            $ingrediente1 = filter_input(INPUT_POST, "ingrediente1Data");
            $cantidad1 = filter_input(INPUT_POST, "cantidad1Data");
            $ingrediente2 = filter_input(INPUT_POST, "ingrediente2Data");
            $cantidad2 = filter_input(INPUT_POST, "cantidad2Data");
            $ingrediente3 = filter_input(INPUT_POST, "ingrediente3Data");
            $cantidad3 = filter_input(INPUT_POST, "cantidad3Data");
            $ingrediente4 = filter_input(INPUT_POST, "ingrediente4Data");
            $cantidad4 = filter_input(INPUT_POST, "cantidad4Data");
            $cantidadPersonas = filter_input(INPUT_POST, "cantidadPersonasData");
            $tiempoEstimado = filter_input(INPUT_POST, "tiempoEstimadoData");
            $paso1 = filter_input(INPUT_POST, "paso1Data");
            $paso2 = filter_input(INPUT_POST, "paso2Data");
            $paso3 = filter_input(INPUT_POST, "paso3Data");
            $paso4 = filter_input(INPUT_POST, "paso4Data");
            $paso5 = filter_input(INPUT_POST, "paso5Data");

            $_FILES['imagen'];
            var_dump($_FILES['imagen']);
            $nombreImg=$_FILES['imagen']['name'];
            $ubicacionTemporal= $_FILES['imagen']['tmp_name'];
            $ubicacionFinal="../img/".$nombreImg;

            move_uploaded_file($ubicacionTemporal, $ubicacionFinal);
        }
        break;

        case "PUT":
            parse_str(file_get_contents("php://input"), $updateData);
            $accion = $updateData["accion"];
    
            if ($accion === "datosAlimentos") {
                $tamanioRecipiente = $updateData["tamanio"];
                $alimentoRecipiente = $updateData["alimento"];
                $pesoMaximoRecipiente = $updateData["pesoMaximo"];
    
                $idRecipiente = $updateData["id"];

                $consulta_cambiar = "UPDATE `datos` SET `tamanio` = '" . $tamanioRecipiente . "' ,`alimento`= '" . $alimentoRecipiente .  "' ,`pesoMaximo`= '" . $pesoMaximoRecipiente . "' WHERE `datos`.`id` = " . $idRecipiente;
                $resultadoConsulta = mysqli_query($conexion, $consulta_cambiar);
                $dev = array();
                http_response_code(200);
                $dev["mensaje"] = "Hola hola";
                $dev["sql"] = $consulta_cambiar;
                $dev["codigo"] = 200;
                echo json_encode($dev);
            } break;
}
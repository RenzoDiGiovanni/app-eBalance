<?php

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
    
    case "POST":

            $usuario = filter_input(INPUT_POST, "usuario");
            $contrasenia = filter_input(INPUT_POST, "contra");

            $consulta_insertar = "INSERT INTO `usuarios` (`id`, `usuario`, `contrasenia`) VALUES (NULL, '" . $usuario . "', '" . $contrasenia . "');";
            $resultado_insertar = mysqli_query($conexion, $consulta_insertar);
            
            $dev = array();

                $dev["mensaje"] = "Registro insertado con éxito";
                $dev["codigo"] = 200;
                
            echo json_encode($dev);

    }
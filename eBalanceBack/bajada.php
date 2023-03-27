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


include "conexion.php";

$consultaSuper = "SELECT pesoActual FROM `datos`";
$resultadoSuper = mysqli_query($conexion, $consultaSuper);
$dev = array();

while ($fila = mysqli_fetch_assoc($resultadoSuper)) {
    array_push($dev, $fila);
}

header('Content-Type: application/json');
echo json_encode(reset($dev), JSON_FORCE_OBJECT | JSON_NUMERIC_CHECK);
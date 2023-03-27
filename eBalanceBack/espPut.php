<?php

global $conexion; 

$dbhost = "localhost";
$dbuser = "root";
$dbpass = "";
$bd = "ebalance";
$conexion = mysqli_connect($dbhost, $dbuser, $dbpass, $bd);


$pesoActual = filter_input(INPUT_GET, 'pesoActual', FILTER_SANITIZE_NUMBER_INT);

//echo $estado;
//echo $rojo;
//echo $verde;
//echo $azul;
//echo $intensidad;

$queryModificar = "UPDATE datos SET pesoActual='$pesoActual' WHERE id='31'";

echo $queryModificar;

$resultModificar = mysqli_query($conexion, $queryModificar);

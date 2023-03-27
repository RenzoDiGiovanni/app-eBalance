<?php
$host="localhost";
$user="root";
$password="";
$database="ebalance";
$link=mysqli_connect($host, $user, $password, $database);
mysqli_query($link, "SET NAMES 'utf8'");
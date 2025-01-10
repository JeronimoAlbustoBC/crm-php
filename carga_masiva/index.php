<?php
ini_set("display_errors",true);
require_once("/var/www/libs/Util.php");
$conexion = new Utiles('17','crm');
#echo "holaa";
#exit();



$query = "SELECT @@VERSION";

var_dump($conexion -> getArray2($query));
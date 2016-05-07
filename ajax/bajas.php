<?php
require_once("../engine/config.php");
requerir_class("tpl","querys","mysql","estructura");

$tabla = $_POST["tabla"];
$id = $_POST["id"];

requerir_class($tabla);

$clase = ucwords($tabla);
$obj = new $clase($id);

$rta = $obj->Baja();

echo $rta;

?>
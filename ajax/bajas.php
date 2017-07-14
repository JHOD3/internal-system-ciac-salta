<?php
require_once("../engine/config.php");
require_once("../engine/restringir_acceso.php");
requerir_class("tpl","querys","mysql","estructura");

$tabla = $_POST["tabla"];
$id = $_POST["id"];

requerir_class($tabla);

$clase = ucwords($tabla);
$obj = new $clase($id);

switch ($tabla) {
    case "especialidades":
    case "estudios":
    case "medicos":
        $rta = $obj->Inhabilitar();
        break;
    default:
        $rta = $obj->Baja();
        break;
}

echo $rta;

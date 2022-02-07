<?php
require_once("../engine/config.php");
require_once("../engine/restringir_acceso.php");
requerir_class("tpl","querys","mysql","estructura");
$this_db = new MySQL();

$tabla = $_POST["tabla"];
$estado = $_POST["estado"];
$id = $_POST["id"];

requerir_class($tabla);

$clase = ucwords($tabla);
$obj = new $clase($id);

switch ($tabla) {
    case "especialidades":
        $query_string = $obj->cambiar_estado($tabla,$estado,$id);
        $rta = $this_db->consulta($query_string);
       break;
}


echo $rta;
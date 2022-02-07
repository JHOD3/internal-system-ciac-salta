<?php
require_once("../engine/config.php");
require_once("../engine/restringir_acceso.php");
requerir_class("tpl","querys","mysql","estructura","json");

requerir_class ("medicos");
if (isset($_POST["id_medico"])){
	$ses_id_medico = $_POST["id_medico"];
	$desde = $_POST["desde"];
	$hasta = $_POST["hasta"];
	$d = implode("-", array_reverse(explode("/", $desde)));
	$h = implode("-", array_reverse(explode("/", $hasta)));
	$obj_medicos = new Medicos();
	$rta = $obj_medicos->EstadisticasSAS($ses_id_medico,$d,$h);
}

echo json_encode($rta);
/*$json = new Services_JSON();
$myjson = $json->encode($rta);
echo $myjson;*/
?>
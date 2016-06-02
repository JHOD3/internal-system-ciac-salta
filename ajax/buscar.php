<?php
	require_once("../engine/config.php");
	requerir_class("tpl","querys","mysql","estructura");

	requerir_class ("medicos");

	$obj_medicos = new Medicos();

	$term = trim(strip_tags($_GET['term']));

	$rta = $obj_medicos->Buscar($term);
	$myjson = json_encode($rta);
	echo $myjson;
?>

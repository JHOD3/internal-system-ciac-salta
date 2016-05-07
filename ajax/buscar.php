<?php
	require_once("../engine/config.php");
	requerir_class("tpl","querys","mysql","estructura","json");
	
	requerir_class ("medicos");

	$obj_medicos = new Medicos();
	
	$term = trim(strip_tags($_GET['term']));

	$rta = $obj_medicos->Buscar($term);
	
	$json = new Services_JSON();				
	$myjson = $json->encode($rta);
	echo $myjson;

?>
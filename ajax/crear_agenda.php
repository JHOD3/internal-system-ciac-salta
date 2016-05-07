<?php
	require_once("../engine/config.php");
	requerir_class("tpl","querys","mysql","estructura","json");
	
	$id_medico = $_POST["id_medico"];
	$id_especialidad = $_POST["id_especialidad"];
	
	requerir_class ("medicos");

	$obj_medicos = new Medicos($id_medico);
	
	$rta = $obj_medicos->DiasTrabajo($id_especialidad);
	
	echo $rta;
	
	
?>
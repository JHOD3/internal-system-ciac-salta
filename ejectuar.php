<?php
	require_once("engine/config.php");
	requerir_class("tpl","querys","mysql","estructura","json");
	
	requerir_class('turnos');
	
	$obj_turnos = new Turnos();
	
	//SI QUIERO ACTUALIZAR DE CONSULTA A ESTUDIO... BUSCO TODOS LOS TURNOS CON ESTUDIOS Y ACTUALIZO SU TIPO A 2 (ESTUDIOS)
	$id_turno_tipo = 2;
	$obj_turnos->ActualizarTipo($id_turno_tipo);


?>
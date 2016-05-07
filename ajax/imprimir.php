<?php
	require_once("../engine/config.php");
	requerir_class("tpl","querys","mysql","estructura","json");
	
	$id = $_POST["id"];
	$tipo = $_POST["tipo"];
	
	switch($tipo){
		case 'turno':
			requerir_class ('turnos');
			$obj_turno = new Turnos($id);
			$rta = $obj_turno->Detalle('imprimir');
		break;
		case 'turnos_todos':
		case 'cobros':
			$fecha = $_POST["fecha"];
			$id_especialidad = $_POST["id_especialidad"];
			
			requerir_class('medicos');
			$obj_medico = new Medicos($id);
			$rta = $obj_medico->ContenedorGrilla($id_especialidad, $fecha, $tipo);
		break;
	}
	
	echo $rta;
	
	
?>
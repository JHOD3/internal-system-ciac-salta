<?php
	require_once("../engine/config.php");
    require_once("../engine/restringir_acceso.php");
	requerir_class("tpl","querys","mysql","estructura","json");

	requerir_class ("pacientes");
	if (isset($_POST["dni"])){
		$dni = $_POST["dni"];
		$obj_pacientes = new Pacientes();
		$rta = $obj_pacientes->Buscar($dni);
	}else{
		$id= $_POST["id"];
		$obj_pacientes = new Pacientes($id);
		$rta = $obj_pacientes->Detalle("corto");
	}

	echo  $rta;
	/*$json = new Services_JSON();
	$myjson = $json->encode($rta);
	echo $myjson;*/

?>
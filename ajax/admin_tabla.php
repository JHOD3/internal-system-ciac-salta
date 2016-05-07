<?php
	require_once("../engine/config.php");
	requerir_class("tpl","querys","mysql","estructura","json");
	
	$tabla = $_POST["tabla"];
	
	requerir_class($tabla);
	$clase = ucwords($tabla);

	$obj = new $clase();
	
	if (isset($_POST["id_padre"]) && $_POST["id_padre"] != ""){
		$rta = $obj->PanelAdmin($_POST["id_padre"]);	
	}else{
		$rta = $obj->PanelAdmin();
	}
	/*$json = new Services_JSON();				
	$myjson = $json->encode($rta);*/
	echo $rta;

?>
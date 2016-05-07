<?php
	require_once("../engine/config.php");
	requerir_class("tpl","querys","mysql","estructura","json");
	
	
	$tipo = $_POST["tipo"];

	requerir_class ("mensajes");
	$obj_mensajes = new Mensajes();
		
	switch ($tipo){
		case "panel_mensajes":
			$id_receptor = $_POST["id_receptor"];
			$rta = $obj_mensajes->PanelMensajes($id_receptor);
		break;
		case "verificar_mensajes":
			//$id_receptor = $_POST["id_receptor"];
			$rta = $obj_mensajes->MensajesSinLeer($_SESSION['EMISOR'], 0);
		break;
	}
	
	echo $rta;

?>
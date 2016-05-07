<?php
require_once ("engine/config.php");

requerir_class("tpl","querys","mysql", "estructura");

$obj_estructura = new Estructura();
$tbl = $_GET["tbl"];

requerir_class($tbl);

$clase = ucwords($tbl);
$obj = new $clase();

$htm_gral = $obj_estructura->html("gral");
	$htm_form = $obj_estructura->html("forms");

	switch ($tbl){
		default:
			$htm_form->Asigna("FORM", $obj->FormAlta());
			$accion = "<span>Nuevo</span>";	
	}

	$htm_form->Asigna("ACCION", $obj->titulo_tabla." >> ".$accion);
	
$htm_gral->Asigna("CUERPO", $htm_form->Muestra());

CargarVariablesGrales($htm_gral, $tipo = "");

echo $htm_gral->Muestra();
?>
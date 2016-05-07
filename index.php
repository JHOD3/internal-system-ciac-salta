<?php
require_once ("engine/config.php");
require_once ("engine/restringir_acceso.php");
requerir_class("tpl","mysql","querys","estructura");

requerir_class("dias_semana");

$obj_estructura = new Estructura();
$obj_dias_semana = new Dias_semana();

$htm_gral = $obj_estructura->html("gral");
$htm_index = $obj_estructura->html("index");
$htm_menu_tablas = $obj_estructura->html("menu/tablas");

/*$htm_index->Asigna("FORM_HORARIOS", $obj_dias_semana->FormHorarios());*/

$htm_index->Asigna("MENU_TABLAS", $htm_menu_tablas->Muestra());

$htm_gral->Asigna("ACCION", "Bienvenido!");
$htm_gral->Asigna("SOLAPAS", "");

$htm_gral->Asigna("CUERPO", $htm_index->Muestra());

CargarVariablesGrales($htm_gral, $tipo = "");

echo utf8_decode($htm_gral->Muestra());
?>

<?php
require_once ("../engine/config.php");
require_once ("../engine/restringir_acceso.php");
requerir_class("tpl","mysql","querys","estructura");

requerir_class("medicos");
$obj_medico = new Medicos($_SESSION["ID_MEDICO"]);

$obj_estructura = new Estructura();

$htm_gral = $obj_estructura->html("sam/gral");
$htm_index = $obj_estructura->html("sam/index");
$htm_menu_tablas = $obj_estructura->html("menu/tablas_sam");

$htm_index->Asigna("DETALLE_MEDICO", $obj_medico->Detalle("corto", "", "sam"));

$htm_index->Asigna("MENU_TABLAS", $htm_menu_tablas->Muestra());

$htm_gral->Asigna("CUERPO", $htm_index->Muestra());

CargarVariablesGrales($htm_gral, $tipo = "");

include("estadisticas.php");

echo utf8_decode($htm_gral->Muestra());

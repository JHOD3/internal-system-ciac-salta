<?php
require_once ("../engine/config.php");
requerir_class("tpl","mysql","querys","estructura");

unset($_SESSION["USUARIO"]);
session_destroy();

$obj_estructura = new Estructura();
$htm_gral = $obj_estructura->html("gral_login");

$htm_login = $obj_estructura->html("sam/login");

$htm_gral->Asigna("TITULO_SISTEMA", "SAM - Sistema de Administraci&oacute;n de M&eacute;dicos");	
$htm_gral->Asigna("CUERPO", $htm_login->Muestra());

CargarVariablesGrales($htm_gral);

echo utf8_decode($htm_gral->Muestra());

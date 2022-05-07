<?php
require_once ("../engine/config.php");
requerir_class("tpl","mysql","querys","estructura","medicos");

unset($_SESSION["USUARIO"]);
if(!empty($_SESSION['ID_USUARIO'])) {
    $obj_usuario = new Medicos();
    $obj_usuario->logoutSessionState($_SESSION['ID_MEDICO']);
}
session_destroy();

$obj_estructura = new Estructura();
$htm_gral = $obj_estructura->html("gral_login");

$htm_login = $obj_estructura->html("sam/login");

switch ($_GET['err']) {
    case "1": $error = '<br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />La sesi&oacute;n ha expirado. Por favor identif&iacute;quese nuevamente. Muchas Gracias!'; break;
    default: $error = ''; break;
}
$htm_gral->Asigna("ERROR", $error);

$htm_gral->Asigna("TITULO_SISTEMA", "SAM - Sistema de Administraci&oacute;n de M&eacute;dicos");
$htm_gral->Asigna("CUERPO", $htm_login->Muestra());

CargarVariablesGrales($htm_gral);

echo utf8_decode($htm_gral->Muestra());

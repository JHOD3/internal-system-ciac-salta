<?php
require_once ("../engine/config.php");
require_once ("../engine/restringir_acceso.php");
requerir_class("tpl","mysql","querys","estructura");

//requerir_class("dias_semana");

$obj_estructura = new Estructura();
//$obj_dias_semana = new Dias_semana();

$htm_gral = $obj_estructura->html("sas/gral");
$htm_index = $obj_estructura->html("sas/index");
$htm_menu_tablas = $obj_estructura->html("menu/tablas_sas");

/*$htm_index->Asigna("FORM_HORARIOS", $obj_dias_semana->FormHorarios());*/

$startMonth = date("Y-m-01");
$start_month = date("01/m/Y")."\\n"."Hasta ".date("d/m/Y");
$data_turnos_otorgados = $obj_estructura->dataTurnosOtorgados($startMonth, $_SESSION['ID_USUARIO']);
$htm_index->Asigna("START_MONTH", $start_month);
$htm_index->Asigna("DATA_TURNOS_OTORGADOS", $data_turnos_otorgados);

$htm_index->Asigna("FECHA", utf8_encode(strftime("%A %d de %B del %Y")));
$htm_index->Asigna("USUARIO_APELLIDOS", utf8_encode($_SESSION['APELLIDOS']));
$htm_index->Asigna("USUARIO_NOMBRES", utf8_encode($_SESSION['NOMBRES']));
$htm_index->Asigna("MENU_TABLAS", $htm_menu_tablas->Muestra());
$htm_gral->Asigna("CUERPO", $htm_index->Muestra());

CargarVariablesGrales($htm_gral, $tipo = "");

echo $htm_gral->Muestra();

//CONVERTIR NUMEROS EN LETRAS
//echo $obj_estructura->NroLetras(1);
?>

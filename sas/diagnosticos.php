<?php
require_once ("../engine/config.php");
require_once ("../engine/restringir_acceso.php");
requerir_class("tpl","mysql","querys","estructura","menus");

//requerir_class("dias_semana");

$obj_estructura = new Estructura();
//$obj_dias_semana = new Dias_semana();

$htm_gral = $obj_estructura->html("sas/gral");
$htm_index = $obj_estructura->html("sas/diagnosticos");

//MENU
$obj_menu = new Menus();
$htm_menu_tablas = $obj_menu->armarMenu();

/*$htm_index->Asigna("FORM_HORARIOS", $obj_dias_semana->FormHorarios());*/

$desde = isset($_POST['desde']) ? $_POST['desde'] : null;
$d = DateTime::createFromFormat('Y-m-d', $desde);
if (!$d or $d->format($format) != $date or strlen($desde) != 10) {
    $desde = date('Y-m-d', strtotime('-1 month +1 day'));
}

$hasta = isset($_POST['hasta']) ? $_POST['hasta'] : null;
$d = DateTime::createFromFormat('Y-m-d', $hasta);
if (!$d or $d->format($format) != $date or strlen($hasta) != 10) {
    $hasta = date('Y-m-d');
}

$days = 2;
switch (date("w")) {
    case "6":
        $days = 3;
        break;
    case "0":
    case "1":
    case "2":
        $days = 4;
        break;
}

$htm_index->Asigna("DATE1", date("Y-m-d"));
$htm_index->Asigna("DATE2", date("Y-m-d"));
$htm_index->Asigna("ID_USUARIO", $_SESSION['ID_USUARIO']);

$htm_index->Asigna("FECHA", ucfirst(strftime("%A %d de ")).ucfirst(strftime("%B del %Y")));
$htm_index->Asigna("USUARIO_APELLIDOS", utf8_encode($_SESSION['APELLIDOS']));
$htm_index->Asigna("USUARIO_NOMBRES", utf8_encode($_SESSION['NOMBRES']));
$htm_index->Asigna("MENU_TABLAS", $htm_menu_tablas->Muestra());
$htm_gral->Asigna("CUERPO", $htm_index->Muestra());

CargarVariablesGrales($htm_gral, $tipo = "");

echo $htm_gral->Muestra();

//CONVERTIR NUMEROS EN LETRAS
//echo $obj_estructura->NroLetras(1);

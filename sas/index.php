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

$desde = $_POST['desde'];
$d = DateTime::createFromFormat('d/m/Y', $desde);
if (!$d or $d->format($format) != $date or strlen($desde) != 10) {
    $desde = date('d/m/Y', strtotime('-1 month +1 day'));
}

$hasta = $_POST['hasta'];
$d = DateTime::createFromFormat('d/m/Y', $hasta);
if (!$d or $d->format($format) != $date or strlen($hasta) != 10) {
    $hasta = date('d/m/Y');
}

$dataTOT = $obj_estructura->obtTurnosOtorgadosTotales($desde, $hasta, $_SESSION['ID_USUARIO']);
$dataTPM = $obj_estructura->obtTurnosPorMedicos($desde, $hasta);
$dataTOPD = $obj_estructura->obtTurnosOtorgadosPorDia($desde, $hasta, $_SESSION['ID_USUARIO']);
$htm_index->Asigna("DATE_DESDE", $desde);
$htm_index->Asigna("DATE_HASTA", $hasta);
$htm_index->Asigna("DATA_TURNOS_OTORGADOS_TOTALES", $dataTOT);
$htm_index->Asigna("DATA_TURNOS_POR_MEDICOS", $dataTPM);
$htm_index->Asigna("DATA_TURNOS_OTORGADOS_POR_DIA", $dataTOPD);

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

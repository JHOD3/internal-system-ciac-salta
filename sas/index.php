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
$d = DateTime::createFromFormat('Y-m-d', $desde);
if (!$d or $d->format($format) != $date or strlen($desde) != 10) {
    $desde = date('Y-m-d', strtotime('-1 month +1 day'));
}

$hasta = $_POST['hasta'];
$d = DateTime::createFromFormat('Y-m-d', $hasta);
if (!$d or $d->format($format) != $date or strlen($hasta) != 10) {
    $hasta = date('Y-m-d');
}

$dataTOT = $obj_estructura->obtTurnosOtorgadosTotales($desde, $hasta, $_SESSION['ID_USUARIO']);
$dataTPM = $obj_estructura->obtTurnosPorMedicos($desde, $hasta, $_SESSION['ID_USUARIO']);
$dataTPD = $obj_estructura->obtTurnosOtorgadosPorDia($desde, $hasta, $_SESSION['ID_USUARIO']);
$dataOST = $obj_estructura->obtTurnosOtorgadosPorOS($desde, $hasta, $_SESSION['ID_USUARIO']);
$dataEST = $obj_estructura->obtTurnosOtorgadosPorEST($desde, $hasta, $_SESSION['ID_USUARIO']);
$dataMOT = $obj_estructura->obtMotivosDeInhabilitaciones();

$htm_index->Asigna("DATE_DESDE", $desde);
$htm_index->Asigna("DATE_DESDE_TEXT", implode("/", array_reverse(explode("-", $desde))));
$htm_index->Asigna("DATE_HASTA", $hasta);
$htm_index->Asigna("DATE_HASTA_TEXT", implode("/", array_reverse(explode("-", $hasta))));
$htm_index->Asigna("TOT", $dataTOT[0]);
$htm_index->Asigna("TOT_NUMROWS", 100 + ($dataTOT[1] * 40));
$htm_index->Asigna("TPM", $dataTPM[0]);
$htm_index->Asigna("TPM_NUMROWS", 240);
$htm_index->Asigna("TPD", $dataTPD[0]);
$htm_index->Asigna("TPD_NUMROWS", 400 + ($dataTPD[1] * 100));
$htm_index->Asigna("OST", $dataOST[0]);
$htm_index->Asigna("OST_NUMROWS", 100 + ($dataOST[1] * 40));
$htm_index->Asigna("EST", $dataEST[0]);
$htm_index->Asigna("EST_NUMROWS", 100 + ($dataEST[1] * 40));
$htm_index->Asigna("MOTIVOS", $dataMOT);

$htm_index->Asigna("FECHA", ucfirst(strftime("%A %d de ")).ucfirst(strftime("%B del %Y")));
$htm_index->Asigna("USUARIO_APELLIDOS", utf8_encode($_SESSION['APELLIDOS']));
$htm_index->Asigna("USUARIO_NOMBRES", utf8_encode($_SESSION['NOMBRES']));
$htm_index->Asigna("MENU_TABLAS", $htm_menu_tablas->Muestra());
$htm_gral->Asigna("CUERPO", $htm_index->Muestra());

CargarVariablesGrales($htm_gral, $tipo = "");

echo $htm_gral->Muestra();

//CONVERTIR NUMEROS EN LETRAS
//echo $obj_estructura->NroLetras(1);
?>

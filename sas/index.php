<?php
require_once ("../engine/config.php");
require_once ("../engine/restringir_acceso.php");
requerir_class("tpl","mysql","querys","estructura","medicos");

//requerir_class("dias_semana");

$obj_estructura = new Estructura();
//$obj_dias_semana = new Dias_semana();

$htm_gral = $obj_estructura->html("sas/gral");
$htm_index = $obj_estructura->html("sas/index");
$htm_menu_tablas = $obj_estructura->html("menu/tablas_sas_{$_SESSION['SUPERUSER']}");

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

$d = implode("-", array_reverse(explode("/", $desde)));
$h = implode("-", array_reverse(explode("/", $hasta)));

if ($_SESSION['ID_USUARIO'] === '0') {
    $obj_medicos = new Medicos();
    $htm_index->Asigna(
        "DROP_MEDICOS",
        "Reporte: ".
        utf8_encode($obj_medicos->Drop("", $_GET['id_medicos'])).
        "<br /><br />"
    );
} else {
    $htm_index->Asigna("DROP_MEDICOS", "");
}
$htm_index->Asigna("DATE_TODAY", date("d/m/Y"));
$htm_index->Asigna("DATE_DESDE", $desde);
$htm_index->Asigna("DATE_HASTA", $hasta);

$dataMOT = $obj_estructura->obtMotivosDeInhabilitaciones();
$htm_index->Asigna("MOTIVOS", $dataMOT);

if ($_GET['id_medicos']) {
    $ses_id_medico = $_GET['id_medicos'];
    $this_db = new MySQL();
    $html_graph.= '<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>';
    $desde_text = $desde;
    $hasta_text = $hasta;
    $desde = $d;
    $hasta = $h;
    include("../sam/estadisticas.medicos.inc.php");
    $htm_index->Asigna("ESTADISTICAS_GRAPH", ($html_graph));
    $htm_index->Asigna("get_id_medicos", "?id_medicos={$_GET['id_medicos']}");

    $htm_index->Asigna("TOT_NUMROWS", 0);
    $htm_index->Asigna("TPM_NUMROWS", 0);
    $htm_index->Asigna("TPD_NUMROWS", 0);
    $htm_index->Asigna("OST_NUMROWS", 0);
    $htm_index->Asigna("EST_NUMROWS", 0);
} else {
    $dataTOT = $obj_estructura->obtTurnosOtorgadosTotales($d, $h, $_SESSION['ID_USUARIO']);
    $dataTPM = $obj_estructura->obtTurnosPorMedicos($d, $h, $_SESSION['ID_USUARIO']);
    $dataTPD = $obj_estructura->obtTurnosOtorgadosPorDia($d, $h, $_SESSION['ID_USUARIO']);
    $dataOST = $obj_estructura->obtTurnosOtorgadosPorOS($d, $h, $_SESSION['ID_USUARIO']);
    $dataEST = $obj_estructura->obtTurnosOtorgadosPorEST($d, $h, $_SESSION['ID_USUARIO']);
    $dataENC = $obj_estructura->obtTurnosOtorgadosPorENC($d, $h, $_SESSION['ID_USUARIO']);
    $htm_index->Asigna("TOT", $dataTOT[0]);
    $htm_index->Asigna("TPM", $dataTPM[0]);
    $htm_index->Asigna("TPD", $dataTPD[0]);
    $htm_index->Asigna("OST", $dataOST[0]);
    $htm_index->Asigna("EST", $dataEST[0]);
    $htm_index->Asigna("ENC", $dataENC[0]);
    $htm_index->Asigna("TOT_NUMROWS", 100 + ($dataTOT[1] * 40));
    $htm_index->Asigna("TPM_NUMROWS", 100 + ($dataOST[1] * 40));
    $htm_index->Asigna("TPD_NUMROWS", 100 + ($dataOST[1] * 40));
    $htm_index->Asigna("OST_NUMROWS", 100 + ($dataOST[1] * 40));
    $htm_index->Asigna("EST_NUMROWS", 100 + ($dataEST[1] * 40));
    $htm_index->Asigna("ENC_NUMROWS", 200);

    $htm_index->Asigna("ESTADISTICAS_GRAPH", '');
    $htm_index->Asigna("get_id_medicos", '');
}

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

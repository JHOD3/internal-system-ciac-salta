<?php
require_once("../engine/config.php");
require_once("../engine/restringir_acceso.php");
requerir_class("tpl","querys","mysql","estructura","json");

requerir_class ("medicos");

function upper($str)
{
    $arrAcentos = array('á', 'é', 'í', 'ó', 'ú', 'ñ', 'ü');
    $arrReemplz = array('Á', 'É', 'Í', 'Ó', 'Ú', 'Ñ', 'Ü');
    $str = str_replace($arrAcentos, $arrReemplz, $str);
    return strtoupper($str);
}

$id_medico = $_POST["id_medico"];
$id_especialidad = $_POST["id_especialidad"];
$id_dia = $_POST["dia"] + 1;
$fecha = $_POST["fecha"];

$obj_medico = new Medicos($id_medico);

switch ($id_medico){
	case 158: //ESTETICA
		$rta = $obj_medico->GrillaTurnosEstetica($id_medico, $id_especialidad, $id_dia, $fecha);
	break;
	default:
		$rta = $obj_medico->GrillaTurnos($id_medico, $id_especialidad, $id_dia, $fecha);
}
echo $rta;

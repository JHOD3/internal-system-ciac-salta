<?php
require_once("../engine/config.php");
requerir_class("tpl","querys","mysql","estructura","json");

$tipo = $_POST["tipo"];
$id_turno = $_POST["id_turno"];
$id_medico = $_POST["id_medico"];
$id_obra_social = $_POST["id_obra_social"];

requerir_class ("turnos_estudios");
$obj_turnos_estudios = new Turnos_estudios();
	
switch ($tipo){
	case "panelAlta":
		$rta = $obj_turnos_estudios->PanelGral($id_turno, $id_medico, $id_obra_social);
	break;
	case "panel_modificacion":
		$rta = $obj_turnos_estudios->PanelModificacion($id_turno, $id_medico, $id_obra_social);
	break;
}

echo utf8_encode($rta);

//EOF admin_turno_estudio.php

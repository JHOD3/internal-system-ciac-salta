<?php
require_once("../engine/config.php");
require_once("../engine/restringir_acceso.php");
requerir_class("tpl","querys","mysql","estructura","json");

$tipo = $_POST["tipo"];
$id_turno = $_POST["id_turno"];

requerir_class ("turnos");
$obj_turno = new Turnos($id_turno);

$hora_actual = date("H:i:s");
$fecha_actual = date('Y-m-d');

switch ($tipo){
	//ARMO EL PANEL GENERAL PARA ADMINISTRAR EL TURNO
	case "panel":
		$rta = $obj_turno->PanelGral('sas');
	break;
	case "panel_medico":
		$rta = $obj_turno->PanelGral('sam');
	break;
	case "cambiar_estado":
		parse_str($_POST["variables"]);
		$rta = $obj_turno->CambiarEstado($id_turno, $turnos_estados, $turno_estado_actual);

		requerir_class("turnos_cambios_estados");
		$obj_turnos_cambios_estados = new Turnos_cambios_estados();

		$columnas = "(
				id_turnos,
				id_usuarios,
				id_medicos,
				fecha,
				hora,
				id_turnos_estados_viejos,
				id_turnos_estados_nuevos,
				estado
				)";

		if ($_POST['tipo_sistema']=='sas'){
			$id_usuario = $_SESSION['ID_USUARIO'];
			$id_medico = 0;
		}else{
			$id_usuario = 0;
			$id_medico = $_SESSION['ID_MEDICO'];
		}

		$valores = "(
					".$id_turno.",
					".$id_usuario.",
					".$id_medico.",
					'".$fecha_actual."',
					'".$hora_actual."',
					".$turno_estado_actual.",
					".$turnos_estados.",
					1
					)";

		$query_string = $obj_turnos_cambios_estados->querys->Alta('turnos_cambios_estados', $columnas, $valores);
		//error_log($query_string);

		if ($obj_turnos_cambios_estados->db->consulta($query_string))
			$rta = $obj_turnos_cambios_estados->db->ultimo_id_insertado();
		else
			$rta = false;


		if ($turnos_estados == 4){//CANCELADO POR MEDICOS
			requerir_class('medicos_cancelaciones');
			$obj_medicos_cancelaciones = new Medicos_cancelaciones();
			$columnas = $obj_medicos_cancelaciones->NombreColumnas();
			$rta = $obj_medicos_cancelaciones->Alta($_POST["variables"],$columnas);
		}

	break;

	case 'baja_cobrosxmedico':
		parse_str($_POST["variables"]);

		requerir_class("cobros","turnos");
		$obj_cobros = new Cobros();

		$rta = $obj_cobros->BajaCobrosxMedicos($id_turno);

		$obj_turno = new Turnos($id_turno);
		$rta = $obj_turno->RestablecerOrdenesyPedidos();
	break;
}

echo $rta;

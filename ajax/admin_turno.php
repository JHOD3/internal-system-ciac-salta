<?php
#error_reporting(E_ALL); ini_set('display_errors', 1);
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

        if ($aviso_demora != 1) {
            $aviso_demora = 0;
        }
        if ($trae_orden != '0' and $trae_orden != '1') {
            $valor_orden = explode("|", $trae_orden);
            $valor_orden = $valor_orden[1];
            $trae_orden = 2;
        } elseif ($trae_orden == 1) {
            $valor_orden = $deposito_consulta ? $deposito_consulta : 0;
        } else {
            $valor_orden = 0;
        }
        $tmp = explode("|", $trae_pedido);
        if (count($tmp) == 2) {
            $trae_pedido = $tmp[0];
            $valor_pedido = $tmp[1];
        } else {
            $trae_pedido = 0;
            $valor_pedido = 0;
        }

        $query_string = "
            UPDATE
                turnos
            SET
                aviso_demora = '{$aviso_demora}',
                trae_orden = '{$trae_orden}',
                valor_orden = '{$valor_orden}',
                trae_pedido = '{$trae_pedido}',
                valor_pedido = '{$valor_pedido}',
                arancel_diferenciado = '{$arancel_diferenciado}'"
		;

		//Anadir fecha/hora, usuario que recepciono el paciente. (estado turno 2)
		if($turnos_estados != $turno_estado_actual && $turnos_estados==2){
			$query_string.= " , 
			id_usuarios_recepcion = '{$_SESSION['ID_USUARIO']}', 
			fecha_recepcion = '{$fecha_actual}', 
			hora_recepcion = '{$hora_actual}' "
			;
		}

		$query_string.= "
            WHERE
                id_turnos = '{$id_turno}'
            LIMIT 1;"
		;

		$obj_turno->db->consulta($query_string);

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
	case 'obtener_arancel_estudios':
		$query_string = "
			SELECT te.id_turnos, te.id_estudios, te.trajo_pedido, te.trajo_orden, te.trajo_arancel, te.deja_deposito, te.trajo_arancel_coseguro, e.nombre
			FROM turnos_estudios AS te
			INNER JOIN estudios AS e ON e.id_estudios = te.id_estudios
			WHERE te.id_turnos = '{$id_turno}' AND te.estado = 1
			;"
		;
		$query = $obj_turno->db->consulta($query_string);
		$rta = false;
		
		if ($obj_turno->db->num_rows($query) > 0){
			$suma = 0;
			while ($row = $obj_turno->db->fetch_array($query)){
				$ta = $row['trajo_arancel'] == null ? 0 : $row['trajo_arancel'];
				$dd = $row['deja_deposito'] == null ? 0 : $row['deja_deposito'];
				$tac = $row['trajo_arancel_coseguro'] == null ? 0 : $row['trajo_arancel_coseguro'];
				$nombre = utf8_encode($row['nombre']);
				$suma = $suma + $ta;
				$rta.="				
				<div>
				<div style='padding-top: 7px'><b>{$nombre}</b></div>
					<span style='white-space:nowrap'>TA: $ {$ta} &nbsp</span>
					<span style='white-space:nowrap'>DD: $ {$dd} &nbsp</span>
					<span style='white-space:nowrap'>Coseguro: $ {$tac} </span>
				</div>";
			}
			//$rta.= "<div><h4>Total: $ {$suma} </h4></div>";
		}
	break;
}

echo $rta;

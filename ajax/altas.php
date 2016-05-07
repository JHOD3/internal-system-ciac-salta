<?php
require_once("../engine/config.php");
requerir_class("tpl","querys","mysql","estructura");

$tabla = $_POST["tabla"];

if(isset($_POST['variables']))
	$datos = $_POST["variables"];

requerir_class($tabla);

$clase = ucwords($tabla);
$obj = new $clase();

$hora_actual = date("H:i:s");
$fecha_actual = date('Y-m-d');
		
switch ($tabla){
	case "pacientes":
		parse_str(stripslashes($datos));
		
		
		if((!isset($obras_sociales_planes)) || ($obras_sociales_planes == "") ){
			$obras_sociales_planes = 0;
		}
		$columnas = "(
					id_tipos_documentos,
					id_obras_sociales,
					apellidos,
					nombres,
					telefonos, 
					nro_documento,
					domicilio,
					email,
					estado,
					id_obras_sociales_planes
					)";
					
		$valores = "(
					".$tipos_documentos.",
					".$obras_sociales.",
					'".strtoupper(utf8_decode($apellidos))."',
					'".strtoupper(utf8_decode($nombres))."',
					'".$telefonos."',
					'".$nro_documento."',
					'".strtoupper(utf8_decode($domicilio))."',
					'".strtoupper($email)."',
					1,
					".$obras_sociales_planes."
					)";
		
		$query_string = $obj->querys->Alta($obj->nombre_tabla, $columnas, $valores);
		
		//error_log($query_string);
			
		if ($obj->db->consulta($query_string))
			$rta = $obj->db->ultimo_id_insertado();
		else
			$rta = false;
					
	break;
	case "medicos":
		parse_str(stripslashes($datos));
		
		$columnas = "(
					id_tipos_documentos,
					id_usuarios,
					apellidos,
					nombres,
					telefonos, 
					nro_documento,
					domicilio,
					email,
					estado,
					usuario,
					pass,
					particular_consulta,
					id_sectores,
					nro_sector,
					interno
					)";
		
		if (!isset($particular_consulta) || $particular_consulta == "")
			$particular_consulta = 0;
			
		if (!isset($sectores) || $sectores == "")
			$sectores = 0;
			
		if (!isset($nro_sector) || $nro_sector == "")
			$nro_sector = "-";
		
		if (!isset($interno) || $interno == "")
			$interno = 0;
						
		$valores = "(
					".$tipos_documentos.",
					".$_SESSION['ID_USUARIO'].",
					'".strtoupper(utf8_decode($apellidos))."',
					'".strtoupper(utf8_decode($nombres))."',
					'".$telefonos."',
					'".$nro_documento."',
					'".strtoupper(utf8_decode($domicilio))."',
					'".strtoupper($email)."',
					1,
					'".strtolower($obj->QuitarTildes($nombres[0].$apellidos))."',
					'".base64_encode($nro_documento)."',
					'".$particular_consulta."',
					".$sectores.",
					'".strtoupper(utf8_decode($nro_sector))."',
					".$interno."
					)";
		
		$query_string = $obj->querys->Alta($obj->nombre_tabla, $columnas, $valores);
			
		if ($obj->db->consulta($query_string))
			$rta = $obj->db->ultimo_id_insertado();
		else
			$rta = false;
					
	break;
	case "especialidades":
		parse_str(stripslashes($datos));
		
		$columnas = "(
					nombre,
					estado
					)";
					
		$valores = "(
					'".strtoupper(utf8_decode($nombre))."',
					1
					)";
		
		$query_string = $obj->querys->Alta($obj->nombre_tabla, $columnas, $valores);
			
		if ($obj->db->consulta($query_string))
			$rta = $obj->db->ultimo_id_insertado();
		else
			$rta = false;
					
	break;
	case "estudios":
		parse_str(stripslashes($datos));
		
		$columnas = "(
					nombre,
					importe,
					estado,
					requisitos
					)";
					
		$valores = "(
					'".strtoupper(utf8_decode($nombre))."',
					'".$importe."',
					1,
					'".strtoupper(utf8_decode($requisitos))."'
					)";
		
		$query_string = $obj->querys->Alta($obj->nombre_tabla, $columnas, $valores);
		//error_log($query_string);	
		if ($obj->db->consulta($query_string))
			$rta = $obj->db->ultimo_id_insertado();
		else
			$rta = false;
					
	break;
	case "obras_sociales":
		parse_str(stripslashes($datos));
		
		$columnas = "(
					nombre,
					abreviacion,
					importe_consulta,
					estado
					)";
					
		$valores = "(
					'".strtoupper(utf8_decode($nombre))."',
					'".strtoupper(utf8_decode($abreviacion))."',
					".$importe_consulta.",
					1
					)";
		
		$query_string = $obj->querys->Alta($obj->nombre_tabla, $columnas, $valores);
			
		if ($obj->db->consulta($query_string))
			$rta = $obj->db->ultimo_id_insertado();
		else
			$rta = false;
					
	break;
	
	case "medicos_especialidades":
		parse_str(stripslashes($datos));
		
		$columnas = "(
					id_medicos,
					id_especialidades,
					duracion_turno,
					es_medico_externo,
					estado
					)";
					
		$valores = "(
					".$id_medico.",
					".$especialidades.",
					'".$duracion_turno."',
					0,
					1
					)";
		
		$query_string = $obj->querys->Alta($obj->nombre_tabla, $columnas, $valores);
			
		if ($obj->db->consulta($query_string))
			$rta = $obj->db->ultimo_id_insertado();
		else
			$rta = false;
					
	break;
	case "medicos_horarios":
		parse_str(stripslashes($datos));
		
		$columnas = "(
					id_medicos,
					id_especialidades,
					id_dias_semana,
					desde,
					hasta,
					estado,
					id_turnos_tipos
					)";
					
		$valores = "(
					".$id_medico.",
					".$id_especialidad.",
					".$dias_semana.",
					'".$desde."',
					'".$hasta."',
					1,
					".$turnos_tipos."
					)";
		
		$query_string = $obj->querys->Alta($obj->nombre_tabla, $columnas, $valores);
			
		if ($obj->db->consulta($query_string))
			$rta = $obj->db->ultimo_id_insertado();
		else
			$rta = false;
					
	break;
	/*case "medicos_estudios":
		parse_str(stripslashes($datos));
		
		$columnas = "(
					id_medicos,
					id_estudios,
					estado
					)";
					
		$valores = "(
					".$id_medico.",
					".$estudios.",
					1
					)";
		
		$query_string = $obj->querys->Alta($obj->nombre_tabla, $columnas, $valores);
			
		if ($obj->db->consulta($query_string))
			$rta = $obj->db->ultimo_id_insertado();
		else
			$rta = false;
					
	break;*/	
	case "medicos_estudios":
		parse_str(stripslashes($datos));
		$columnas = "(id_medicos, id_estudios, particular, estado)";
		$ids_estudios =  rtrim($_POST["ids_estudios"], ", ");
		
		$id_estudiosv = explode(", ", $ids_estudios);
		
		//$obj->BajaxMedico($id_medico);
		$rta = false;
		
		foreach ($id_estudiosv as $clave => $valor) {
			$valorv = explode("-", $valor);
			
			$valores = "(
					".$id_medico.",
					".$valorv[0].",
					0,
					1)";
			$query_string = $obj->querys->Alta($obj->nombre_tabla, $columnas, $valores);
			
		
			if ($obj->db->consulta($query_string))
				$rta = $obj->db->ultimo_id_insertado();
			else
				$rta = false;
		}
	break;
	
	case "obras_sociales_estudios":
		parse_str(stripslashes($datos));
		
		$columnas = "(
					id_obras_sociales,
					id_estudios,
					importe,
					nomenclador,
					estado
					)";
					
		$valores = "(
					".$id_obra_social.",
					".$estudios.",
					'".$importe."',
					'".$nomenclador."',
					1
					)";
		
		$query_string = $obj->querys->Alta($obj->nombre_tabla, $columnas, $valores);
			
		if ($obj->db->consulta($query_string))
			$rta = $obj->db->ultimo_id_insertado();
		else
			$rta = false;
					
	break;
	case "obras_sociales_planes":
		parse_str(stripslashes($datos));
		
		$columnas = "(
					id_obras_sociales,
					nombre,
					estado
					)";
					
		$valores = "(
					".$id_obra_social.",
					'".strtoupper(utf8_decode($nombre))."',
					1
					)";
		
		$query_string = $obj->querys->Alta($obj->nombre_tabla, $columnas, $valores);
			
		if ($obj->db->consulta($query_string))
			$rta = $obj->db->ultimo_id_insertado();
		else
			$rta = false;
					
	break;
	
	case "turnos":
		/*$columnas = $obj->NombreColumnas();
		$resp = $obj->Alta($datos,$columnas);*/
		parse_str(stripslashes($datos));
		
		$columnas = "(id_medicos, id_especialidades, id_pacientes, id_turnos_estados, fecha, desde, hasta, trae_orden, trae_pedido, id_medicos_derivacion, id_especialidades_derivacion, es_derivacion_externa, id_turnos_tipos, estado, tipo_usuario, id_usuarios, fecha_alta, hora_alta, consultorio)";
		
		switch ($id_medico){
			case 158: //ESTETICA... CARGA HASTA TRES VECES PARA UN MISMO PERIODO DE TIEMPO
				switch($id_turno_tipo){
					case "consultas":
						$id_turno_tipo = 1;
					break;
					case "estudios":
						$id_turno_tipo = 2;
					break;	
				}
				
				$valores = "(
					".$id_medico.",
					".$id_especialidad.",
					".$id_paciente.",
					1,
					'".$fecha."',
					'".$desde."',
					'".$hasta."',
					0,
					0,
					0,
					0,
					0,
					".$id_turno_tipo.",
					1,
					'".$_SESSION['TIPO_USR']."',
					".$_SESSION['ID_USUARIO'].",
					'".$fecha_actual."',
					'".$hora_actual."',
					".$consultorio."
					)";
				
				$query_string = $obj->querys->Alta($obj->nombre_tabla, $columnas, $valores);
					
				if ($obj->db->consulta($query_string))
					$rta = $obj->db->ultimo_id_insertado();
				else
					$rta = false;
			break;
			default:
				if (!$obj->ExisteTurnoReservado($fecha, $desde, $hasta, $id_medico, $id_especialidad)){
					switch($id_turno_tipo){
						case "consultas":
							$id_turno_tipo = 1;
						break;
						case "estudios":
							$id_turno_tipo = 2;
						break;	
					}
					
					$valores = "(
						".$id_medico.",
						".$id_especialidad.",
						".$id_paciente.",
						1,
						'".$fecha."',
						'".$desde."',
						'".$hasta."',
						0,
						0,
						0,
						0,
						0,
						".$id_turno_tipo.",
						1,
						'".$_SESSION['TIPO_USR']."',
						".$_SESSION['ID_USUARIO'].",
						'".$fecha_actual."',
						'".$hora_actual."',
						".$consultorio."
						)";
					
					$query_string = $obj->querys->Alta($obj->nombre_tabla, $columnas, $valores);
						
					if ($obj->db->consulta($query_string))
						$rta = $obj->db->ultimo_id_insertado();
					else
						$rta = false;
				}else{
					error_log("EXISTE TURNO: ". $fecha." ".$desde." ".$hasta." ".$id_medico." ".$id_especialidad);
					$rta = 'existe_turno';	
				}	
			break;	
		}
		
		
		
	break;
	case "turnos_estudios":
		parse_str(stripslashes($datos));
		$columnas = "(id_turnos, id_estudios, estado)";
		$ids_estudios =  rtrim($_POST["ids_estudios"], ", ");
		
		$id_estudiov = explode(", ", $ids_estudios);
		
		$obj->BajaxTurno($id_turno);
		
		$rta = false;
		
		foreach ($id_estudiov as $clave => $valor) {
			$valores = "(
					".$id_turno.",
					".$valor.",
					1)";
			$query_string = $obj->querys->Alta($obj->nombre_tabla, $columnas, $valores);
			
		
			if ($obj->db->consulta($query_string))
				$rta = $obj->db->ultimo_id_insertado();
			else
				$rta = false;
		}
	break;
	
	case "medicos_obras_sociales":
		parse_str(stripslashes($datos));
		$columnas = "(id_medicos, id_obras_sociales, estado)";
		$ids_obras_sociales =  rtrim($_POST["ids_obras_sociales"], ", ");
		
		$id_obras_socialesv = explode(", ", $ids_obras_sociales);
		
		//$obj->BajaxMedico($id_medico);
		
		$rta = false;
		
		foreach ($id_obras_socialesv as $clave => $valor) {
			$valorv = explode("-", $valor);
			
			$valores = "(
					".$id_medico.",
					".$valorv[0].",
					1)";
			$query_string = $obj->querys->Alta($obj->nombre_tabla, $columnas, $valores);
			
		
			if ($obj->db->consulta($query_string))
				$rta = $obj->db->ultimo_id_insertado();
			else
				$rta = false;
		}
	break;
	case "cobros":
		
		$tipo = $_POST["tipo"];
		
		parse_str(stripslashes($datos));
		
		$trae_orden = 0;
		$trae_pedido = 0;
		
		switch ($tipo){
			case 'deposito_consulta':
				$id_cobros_conceptos = 2; //DEPOSITO POR NO TRAER ORDEN
				$importe = $deposito_consulta;
			break;
			case 'consulta':
				$id_cobros_conceptos = 1; //COBRO POR PLUS
				$importe = $valor_consulta;	
			break;
			case 'consulta_particular':
				$id_cobros_conceptos = 5; //COBRO PARTICULAR CONSULTA
				$importe = $orden_consulta;	
			break;
			
			case 'deposito_pedido':
				$id_cobros_conceptos = 4; //DEPOSITO POR NO TRAER PEDIDO
				$importe = $deposito_estudios_pedido;	
			break;
			case 'deposito_orden':
				$id_cobros_conceptos = 2; //DEPOSITO POR NO TRAER ORDEN
				$importe = $deposito_estudios_orden;	
			break;
			case 'estudios_particular':
				$id_cobros_conceptos = 3; //COBRO PARTICULAR ESTUDIOS
				$importe = $_POST['particular'];	
			break;
		}	
		
		$rta = false;
				
		if ($importe != 0){
			$columnas = "(id_cobros_conceptos, id_medicos, id_pacientes, id_turnos, id_usuarios, fecha, hora, nro_recibo, importe, importe_letras, concepto, estado)";
			
			requerir_class("cobros_conceptos");
			$obj_cobro_concepto = new Cobros_conceptos($id_cobros_conceptos);
		
			$valores = "(
				".$id_cobros_conceptos.",
				".$id_medico.",
				".$id_paciente.",
				".$id_turno.",
				".$_SESSION['ID_USUARIO'].",
				'".$fecha_actual."',
				'".$hora_actual."',
				0,
				'".$importe."',
				'".$obj->NroLetras($importe)."',
				'".$obj_cobro_concepto->nombre."',
				1)";
			
			$query_string = $obj->querys->Alta($obj->nombre_tabla, $columnas, $valores);
			
			//error_log($query_string);
				
			if ($obj->db->consulta($query_string))
				$rta = $obj->db->ultimo_id_insertado();
			else
				$rta = false;
		}
	break;
	case "egresos":
		$id_cobro = $_POST["id_cobro"];
		$columnas = "(id_egresos_conceptos, id_medicos, id_pacientes, id_turnos, id_usuarios, fecha, hora, nro_recibo, importe, importe_letras, concepto, estado)";
		
		requerir_class("cobros");
		$obj_cobro = new Cobros($id_cobro);
	
		$id_egreso_concepto = 1;
		
		requerir_class("egresos_conceptos");
		$obj_egreso_concepto = new Egresos_conceptos($id_egreso_concepto);
		
		$valores = "(
			".$id_egreso_concepto.",
			".$obj_cobro->id_medicos.",
			".$obj_cobro->id_pacientes.",
			".$obj_cobro->id_turnos.",
			".$_SESSION['ID_USUARIO'].",
			'".$fecha_actual."',
			'".$hora_actual."',
			0,
			'".$obj_cobro->importe."',
			'".$obj->NroLetras($obj_cobro->importe)."',
			'".$obj_egreso_concepto->nombre."',
			1)";
		
		$query_string = $obj->querys->Alta($obj->nombre_tabla, $columnas, $valores);
		
		$obj_cobro->ReintegroEfectuado();
		
		if ($obj->db->consulta($query_string))
			$rta = $obj->db->ultimo_id_insertado();
		else
			$rta = false;
	break;
	case "horarios_inhabilitados":
		parse_str(stripslashes($datos));
		
		$columnas = "(id_medicos, id_especialidades, fecha, desde, hasta, estado)";
		
		$valores = "(
			".$medico.",
			".$especialidad.",
			'".$fecha."',
			'".$desde."',
			'".$hasta."',
			1)";
		
		$query_string = $obj->querys->Alta($obj->nombre_tabla, $columnas, $valores);
		//error_log($query_string);	
		if ($obj->db->consulta($query_string)){
			$ultimo_id_insertado = $obj->db->ultimo_id_insertado();
			$rta = $ultimo_id_insertado;
		}else
			$rta = false;
	break;
	case "mensajes":
		parse_str(stripslashes($datos));
		
		$columnas = "(id_emisor, id_receptor, mensaje, hora, fecha, leido, estado)";
		
		$valores = "(
			'".$id_emisor."',
			'".$id_receptor."',
			'".$mensaje_chat."',
			'".$hora_actual."',
			'".$fecha_actual."',
			0,
			1)";
		
		$query_string = $obj->querys->Alta($obj->nombre_tabla, $columnas, $valores);
			
		if ($obj->db->consulta($query_string)){
			$ultimo_id_insertado = $obj->db->ultimo_id_insertado();
			$rta = $obj->PanelMensajes($id_receptor);
		}else
			$rta = false;
	break;
}
echo $rta;
?>
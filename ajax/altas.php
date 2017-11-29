<?php
require_once("../engine/config.php");
require_once("../engine/restringir_acceso.php");
requerir_class("tpl","querys","mysql","estructura");

#var_dump($_POST); die;

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
					id_sectores,
					id_subsectores,
					nro_sector,
					interno,
                    matricula,
                    saludo
					)";

		if (!isset($sectores) || $sectores == "")
			$sectores = 0;

		if (!isset($subsectores) || $subsectores == "")
			$subsectores = 0;

		if (!isset($nro_sector) || $nro_sector == "")
			$nro_sector = "-";

		if (!isset($interno) || $interno == "")
			$interno = 0;

		if (!isset($matricula) || $matricula == "")
			$matricula = 0;

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
					'".strtolower($obj->QuitarTildes(utf8_encode($nombres[0].$apellidos)))."',
					'".base64_encode($nro_documento)."',
					".$sectores.",
					".$subsectores.",
					'".strtoupper(utf8_decode($nro_sector))."',
					".$interno.",
					".$matricula.",
                    '".$saludo."'
					)";

		$query_string = $obj->querys->Alta($obj->nombre_tabla, $columnas, $valores);

		if ($obj->db->consulta($query_string))
			$rta = $obj->db->ultimo_id_insertado();
		else
			$rta = false;

	break;
	case "medicosext":
		parse_str(stripslashes($datos));

		$columnas = "(
			apellidos,
			nombres,
            matricula,
            saludo,
            estado
		)";

		if (!isset($matricula) || $matricula == "")
			$matricula = 0;

		$valores = "(
    		'".strtoupper(utf8_decode($apellidos))."',
    		'".strtoupper(utf8_decode($nombres))."',
    		".$matricula.",
            '".$saludo."',
            '1'
		)";

		print $query_string = $obj->querys->Alta($obj->nombre_tabla, $columnas, $valores);

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
                    arancel,
					estado,
					requisitos,
					codigopractica
					)";

		$valores = "(
					'".strtoupper(utf8_decode($nombre))."',
					'".$importe."',
					'".$arancel."',
					1,
					'".strtoupper(utf8_decode($requisitos))."',
					'".$codigopractica."'
					)";

		print $query_string = $obj->querys->Alta($obj->nombre_tabla, $columnas, $valores);
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
					#importe_consulta,
					estado
					)";

		$valores = "(
					'".strtoupper(utf8_decode($nombre))."',
					'".strtoupper(utf8_decode($abreviacion))."',
					#".$importe_consulta.",
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
					'".$_SESSION['TIPO_USR']."',".
                    (
                        ($_SESSION['TIPO_USR'] == 'M') ?
                            "'-1'," :
                            $_SESSION['ID_USUARIO'].","
                    ).
                    "'".$fecha_actual."',
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
    					'".$_SESSION['TIPO_USR']."',".
                        (
                            ($_SESSION['TIPO_USR'] == 'M') ?
                                "'-1'," :
                                $_SESSION['ID_USUARIO'].","
                        ).
                        "'".$fecha_actual."',
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
        if (trim($ids_estudios)) {
    		$id_estudiov = explode(", ", $ids_estudios);
            $where_del = "AND id_estudios NOT IN ({$ids_estudios})";
            $where_string = "AND id_estudios IN ({$ids_estudios})";
        } else {
    		$id_estudiov = array();
            $where_del = "";
            $where_string = "";
        }
		$rta = false;

        // DAR DE BAJA LOS QUE SE QUITARON
        $query_string_del = <<<SQL
            UPDATE
                turnos_estudios
            SET
                estado = 0
            WHERE
                id_turnos = '{$id_turno}'
                {$where_del}
SQL;
        #print "{$query_string_del}<br />";
        $obj->db->consulta($query_string_del);

        // DAR DE ALTA LOS QUE SE AGREGARON
        $query_string = <<<SQL
            SELECT id_estudios
            FROM turnos_estudios
            WHERE
                estado = 1 AND
                id_turnos = '{$id_turno}'
                {$where_string}
SQL;
        $query = $obj->db->consulta($query_string);
        $new_id_est = $id_estudiov;
        $key_del = array();
		while ($row = $obj->db->fetch_array($query)) {
            for ($i = 0; $i < count($new_id_est); $i++) {
                if ($new_id_est[$i] == $row['id_estudios']) {
                    $key_del[] = $i;
                }
            }
		}
        foreach ($key_del AS $itm_del) {
            unset($new_id_est[$itm_del]);
        }
        foreach ($new_id_est AS $itm_id_est) {
            $query_string_add = <<<SQL
                INSERT INTO turnos_estudios (
                    id_turnos,
                    id_estudios,
                    estado
                )
                VALUES (
                    '{$id_turno}',
                    '{$itm_id_est}',
                    '1'
                )
SQL;
            #print "{$query_string_add}<br />";
            $obj->db->consulta($query_string_add);
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

		$columnas = "(id_medicos, id_especialidades, fecha, desde, hasta, estado, id_horarios_inhabilitados_motivos, horarios_inhabilitados_motivos)";

        $fdesde = implode("-", array_reverse(explode("/", $fdesde)));
        $fhasta = implode("-", array_reverse(explode("/", $fhasta)));
        #print "{$fdesde} a {$fhasta}<br />";

        $query_string = <<<SQL
            SELECT
                id_dias_semana
            FROM
                medicos_horarios
            WHERE
                id_especialidades = '{$especialidad}' AND
                id_medicos = '{$medico}' AND
                estado = '1'
            GROUP BY
                id_dias_semana
            ORDER BY
                id_dias_semana
SQL;
        $query = $obj->db->consulta($query_string);
        $dds = array();
        while ($row = $obj->db->fetch_array($query)) {
            $dds[] = $row['id_dias_semana'];
        }

		$rta = false;
        for ($f = $fdesde; $f <= $fhasta; $f = date("Y-m-d", strtotime('+1 day', strtotime($f)))) {
            $id_dias_semana = date('w', strtotime($f));
            if ($id_dias_semana == 7) {
                $id_dias_semana = 1;
            } else {
                $id_dias_semana++;
            }
            if (in_array($id_dias_semana, $dds)) {
        		$valores = "(
        			".$medico.",
        			".$especialidad.",
        			'".$f."',
        			'".$desde."',
        			'".$hasta."',
        			1,
        			'".$id_horarios_inhabilitados_motivos."',
        			'".utf8_decode($horarios_inhabilitados_motivos)."'
                    )";

        		$query_string = $obj->querys->Alta($obj->nombre_tabla, $columnas, $valores);
        		#error_log($query_string);
        		if ($obj->db->consulta($query_string)){
        			$ultimo_id_insertado = $obj->db->ultimo_id_insertado();
        			$rta = $ultimo_id_insertado;
                }
            }
        }
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
	case "sectores":
		parse_str(stripslashes($datos));

		$columnas = "(
					nombre
					)";

		$valores = "(
					'".utf8_decode($nombre)."'
					)";

		$query_string = $obj->querys->Alta($obj->nombre_tabla, $columnas, $valores);

		if ($obj->db->consulta($query_string))
			$rta = $obj->db->ultimo_id_insertado();
		else
			$rta = false;

	break;
	case "subsectores":
		parse_str(stripslashes($datos));

		$columnas = "(
                    id_sectores,
					nombre
					)";

		$valores = "(
					'".utf8_decode($sectores)."',
					'".utf8_decode($nombre)."'
					)";

		$query_string = $obj->querys->Alta($obj->nombre_tabla, $columnas, $valores);

		if ($obj->db->consulta($query_string))
			$rta = $obj->db->ultimo_id_insertado();
		else
			$rta = false;

	break;
	case "agendas":
		parse_str(stripslashes($datos));

		$columnas = "(
					nombre,
                    apellido,
                    rubro,
                    celular,
                    telefono,
                    direccion,
                    id_agendas_tipos,
                    estado
					)";

		$valores = "(
					'".utf8_decode(upper($nombre))."',
					'".utf8_decode(upper($apellido))."',
					'".utf8_decode(upper($rubro))."',
					'".utf8_decode(upper($celular))."',
					'".utf8_decode(upper($telefono))."',
					'".utf8_decode(upper($direccion))."',
					'".utf8_decode($agendas_tipos)."',
                    1
					)";

		$query_string = $obj->querys->Alta($obj->nombre_tabla, $columnas, $valores);

		if ($obj->db->consulta($query_string))
			$rta = $obj->db->ultimo_id_insertado();
		else
			$rta = false;

	break;
	case "mantenimientos":
		parse_str(stripslashes($datos));

		$columnas = "(
					fecha,
                    id_sectores,
                    solicitador,
                    tarea,
                    especialista,
                    observaciones,
                    id_mantenimientos_estados,
                    estado,
                    id_usuarios
		)";

		if (!isset($sectores) || $sectores == "")
			$sectores = 0;

		$valores = "(
					'".date("Y-m-d H:i:s")."',
					'".$sectores."',
					'".utf8_decode(upper($solicitador))."',
					'".utf8_decode(upper($tarea))."',
					'".utf8_decode(upper($especialista))."',
					'".utf8_decode(upper($observaciones))."',
					'".$mantenimientos_estados."',
                    1,
                    '".$_SESSION['ID_USUARIO']."'
		)";

		$query_string = $obj->querys->Alta($obj->nombre_tabla, $columnas, $valores);

		if ($obj->db->consulta($query_string)) {
			$rta = $obj->db->ultimo_id_insertado();
    		$columnas = "(
                        id_mantenimientos,
    					fecha,
                        id_sectores,
                        solicitador,
                        tarea,
                        especialista,
                        observaciones,
                        id_mantenimientos_estados,
                        estado,
                        id_usuarios
			)";
    		$valores = "(
    					'".$rta."',
    					'".date("Y-m-d H:i:s")."',
    					'".$sectores."',
    					'".utf8_decode(upper($solicitador))."',
    					'".utf8_decode(upper($tarea))."',
    					'".utf8_decode(upper($especialista))."',
    					'".utf8_decode(upper($observaciones))."',
                        '".$mantenimientos_estados."',
                        1,
                        '".$_SESSION['ID_USUARIO']."'
			)";
    		$query_string2 = $obj->querys->Alta('mantenimhistoricos', $columnas, $valores);
    		$obj->db->consulta($query_string2);
        } else {
			$rta = false;
        }

	break;

}
echo $rta;
?>
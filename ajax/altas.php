<?php
require_once("../engine/config.php");
require_once("../engine/restringir_acceso.php");
requerir_class("tpl","querys","mysql","estructura");
$this_db = new MySQL();

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
	case "pacientes_observaciones":
		parse_str(stripslashes($datos));


		$columnas = "(
					id_pacientes,
					fechahora,
					observacion,
					estado,
					id_usuarios
					)";

		$valores = "(
					'".$id_pacientes."',
					'".date("Y-m-d H:i:s")."',
					'".str_replace("'", "\\'", utf8_decode($observacion))."',
					1,
					'{$_SESSION['ID_USUARIO']}'
					)";

		$query_string = $obj->querys->Alta($obj->nombre_tabla, $columnas, $valores);

		//error_log($query_string); die;

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
                    id_plantas,
                    matricula,
                    saludo,
                    fechanac
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

        if (!isset($plantas) || $plantas == "")
            $plantas = 0;

		$valores = "(
					'".$tipos_documentos."',
					'".$_SESSION['ID_USUARIO']."',
					'".strtoupper(utf8_decode($apellidos))."',
					'".strtoupper(utf8_decode($nombres))."',
					'".$telefonos."',
					'".$nro_documento."',
					'".strtoupper(utf8_decode($domicilio))."',
					'".strtoupper($email)."',
					'1',
					'".strtolower($obj->QuitarTildes(utf8_encode($nombres[0].$apellidos)))."',
					'".base64_encode($contrasena)."',
					'".$sectores."',
					'".$subsectores."',
					'".strtoupper(utf8_decode($nro_sector))."',
					'".$interno."',
                    '".$plantas."',
					'".$matricula."',
                    '".$saludo."',
                    '".implode("-", array_reverse(explode("/", $fechanac)))."'
					)";

		$query_string = $obj->querys->Alta($obj->nombre_tabla, $columnas, $valores);

		if ($obj->db->consulta($query_string)) {
			$rta = $obj->db->ultimo_id_insertado();

            /* INSERT ESPECIALIDADES ******************************************/
            requerir_class('medicos_especialidades');
            $clase_me = ucwords('medicos_especialidades');
            $obj_me = new $clase_me();

            $arr_especialidades = array();
            for ($i = 0; $i < count($fs_especialidades); $i++) {
                $arr_especialidades[$fs_especialidades[$i]] = $fs_duracion_turno[$i];
            }
            foreach ($arr_especialidades AS $key => $val) {
        		$columnas_me = "(
        					id_medicos,
        					id_especialidades,
        					duracion_turno,
        					es_medico_externo,
        					estado
        					)";

        		$valores_me = "(
        					".$rta.",
        					".$key.",
        					'".$val."',
        					0,
        					1
        					)";

        		$query_string_me = $obj_me->querys->Alta($obj_me->nombre_tabla, $columnas_me, $valores_me);

                $obj_me->db->consulta($query_string_me);
            }

            /* INSERT HORARIOS ************************************************/
            requerir_class('medicos_horarios');
            $clase_mh = ucwords('medicos_horarios');
            $obj_mh = new $clase_mh();

            for ($i = 0; $i < count($fs_especialidades); $i++) {
        		$columnas_mh = "(
        					id_medicos,
        					id_especialidades,
        					id_dias_semana,
        					desde,
        					hasta,
        					estado,
        					id_turnos_tipos,
                            id_plantas,
                            nro_consultorio
        					)";

        		$valores_mh = "(
        					".$rta.",
        					".$fs_especialidades[$i].",
        					".$fs_dias_semana[$i].",
        					'".$fs_desde[$i]."',
        					'".$fs_hasta[$i]."',
        					1,
        					'".$fs_turnos_tipos[$i]."',
        					'".$fs_plantas[$i]."',
                            '".$fs_nro_consultorio[$i]."'
        					)";

        		$query_string_mh = $obj_mh->querys->Alta($obj_mh->nombre_tabla, $columnas_mh, $valores_mh);

        		$obj_mh->db->consulta($query_string_mh);
            }

            /* INSERT OBRAS SOCIALES ARANCELES ********************************/
            requerir_class('medicos_obras_sociales');
            $clase_mos = ucwords('medicos_obras_sociales');
            $obj_mos = new $clase_mos();
            foreach ($obras_sociales AS $os) {
                $vpost = "obras_sociales_aranceles_{$os}";
                $vpost = $$vpost;

        		$columnas_mos = "(id_medicos, id_obras_sociales, arancel, estado)";

    			$valores_mos = "(
    					'".$rta."',
    					'".$os."',
    					'".$vpost."',
    					1)";
    			$query_string_mos = $obj_mos->querys->Alta($obj_mos->nombre_tabla, $columnas_mos, $valores_mos);

    			$obj_mos->db->consulta($query_string_mos);
            }

            /* INSERT ESTUDIOS ARANCELES **************************************/
            requerir_class('medicos_estudios');
            $clase_ms = ucwords('medicos_estudios');
            $obj_ms = new $clase_ms();
            foreach ($estudios AS $os) {
                $vpost = "estudios_aranceles_{$os}";
                $vpost = $$vpost;

        		$columnas_ms = "(id_medicos, id_estudios, particular, estado)";

    			$valores_ms = "(
    					'".$rta."',
    					'".$os."',
    					'".$vpost."',
    					1)";
    			$query_string_ms = $obj_ms->querys->Alta($obj_ms->nombre_tabla, $columnas_ms, $valores_ms);

    			$obj_ms->db->consulta($query_string_ms);
            }

		} else {
			$rta = false;
        }

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

		if (!isset($plantas) || $plantas == "")
			$plantas = 0;

		$columnas = "(
					id_medicos,
					id_especialidades,
					id_dias_semana,
					desde,
					hasta,
					duracion_turno,
					estado,
					id_turnos_tipos,
                    id_plantas,
                    nro_consultorio
					)";

		$valores = "(
					".$id_medico.",
					".$id_especialidad.",
					".$dias_semana.",
					'".$desde."',
					'".$hasta."',
					'".$duracion_turno."',
					1,
					'".$turnos_tipos."',
					'".$plantas."',
                    '".$nro_consultorio."'
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
        $columnas = "(id_medicos, id_especialidades, id_pacientes, id_turnos_estados, fecha, desde, hasta, trae_orden, trae_pedido, id_medicos_derivacion, id_especialidades_derivacion, es_derivacion_externa, id_turnos_tipos, estado, tipo_usuario, id_usuarios, fecha_alta, hora_alta, consultorio, arancel_diferenciado, aviso_demora)";

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
						".$consultorio.",
						0,
						0
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

		//Kcmnt Aqui podria agregar logica de ordenado para el orden de turnos Practica Medicas

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
        if (
            isset($medicos_especialidades) and
            $medicos_especialidades and
            trim($medicos_especialidades)
        ) {
            $medicos_especialidades = explode(
                ",",
                $medicos_especialidades
            );
            if (count($medicos_especialidades) > 0) {
                for ($i = 0; $i < count($medicos_especialidades); $i++) {
                    $medicos_especialidades[$i] = explode(
                        "|",
                        $medicos_especialidades[$i]
                    );
                }
            }
        }
        if (
            count($medicos_especialidades) == 1 and
            count($medicos_especialidades[0]) == 1 and
            !is_array($medicos_especialidades[0][0])
        ) {
            unset($medicos_especialidades);
        }

        if (isset($medicos_especialidades)) {
            for ($i = 0; $i < count($medicos_especialidades); $i++) {
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
                        id_especialidades = '{$medicos_especialidades[$i][1]}' AND
                        id_medicos = '{$medicos_especialidades[$i][0]}' AND
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
                			".$medicos_especialidades[$i][0].",
                			".$medicos_especialidades[$i][1].",
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
            }
        } else {
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
	case "novedades_diarias":
		parse_str(stripslashes($datos));

		$columnas = "(
					   fechahora,
                       titulo,
                       descripcion,
                       id_usuarios
					)";

        $valores = "(
					   '".date("Y-m-d H:i:s")."',
					   '".utf8_decode(mysql_real_escape_string($titulo))."',
                       '".utf8_decode(mysql_real_escape_string($descripcion))."',
                       '".$_SESSION['ID_USUARIO']."'
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
    case "turnos_tipos":
        parse_str(stripslashes($datos));

        $columnas = "(mensaje)";

        $valores = "('".utf8_decode(upper($mensaje))."')";

        $query_string = $obj->querys->Alta($obj->nombre_tabla, $columnas, $valores);

        if ($obj->db->consulta($query_string))
            $rta = $obj->db->ultimo_id_insertado();
        else
            $rta = false;

        break;
	case "mantenimientos":
		parse_str(stripslashes($datos));

		$columnas = "(
                    creado,
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
    		$query_string2 = "
                INSERT INTO
                    mantenimhistoricos
                SELECT
                    null,
                    id_mantenimientos,
					creado,
                    fecha,
                    id_sectores,
                    solicitador,
                    tarea,
                    especialista,
                    observaciones,
                    estado,
                    id_mantenimientos_estados,
                    id_usuarios
                FROM
                    mantenimientos
                WHERE
                    id_mantenimientos = '{$rta}'
            ";
    		$obj->db->consulta($query_string2);
        } else {
			$rta = false;
        }

	break;

	case "usuarios":
		parse_str(stripslashes($datos));

		$columnas = "(
					superuser,
                    nombres,
                    apellidos,
                    usuario,
                    pass,
                    fecha_alta,
                    fecha_baja,
                    estado,
                    fechanac,
                    agenda,
					comunicacion,
					comunicados_gerencia,
					novedades_diarias,
					notas_imprecion,
					encuestas,
					especialidades,
					estudios,
					mantenimiento,
					mantenimiento_reciente,
					mantenimiento_historico,
					medicos,
					medicos_ciac,
					medicos_externos,
					medicos_empresas,
					obras_sociales,
					pacientes,
					planes_contingencia,
					practicas_medicas,
					sectores,
					sectores_uno,
					subsectores,
					consultorios,
					disponibilidades,
					tareas,
					tareas_configuracion,
					tareas_pendientes,
					usuarios_permiso,
					cumples
					)";

		$valores = "(
					'".utf8_decode($roles)."',
					'".utf8_decode($nombres)."',
					'".utf8_decode($apellidos)."',
					'".utf8_decode(strtolower($usuario))."',
					'".base64_encode($pass)."',
					'".date("Y-m-d")."',
					NULL,
                    1,
                    '".implode("-", array_reverse(explode("/", $fechanac)))."',
                    '".utf8_decode($agenda)."',
                    '".utf8_decode($comunicacion)."',
                    '".utf8_decode($comunicados_gerencia)."',
                    '".utf8_decode($novedades_diarias)."',
                    '".utf8_decode($notas_imprecion)."',
                    '".utf8_decode($encuestas)."',
                    '".utf8_decode($especialidades)."',
                    '".utf8_decode($estudios)."',
                    '".utf8_decode($mantenimiento)."',
                    '".utf8_decode($mantenimiento_reciente)."',
                    '".utf8_decode($mantenimiento_historico)."',
                    '".utf8_decode($medicos)."',
                    '".utf8_decode($medicos_ciac)."',
                    '".utf8_decode($medicos_externos)."',
                    '".utf8_decode($medicos_empresas)."',
                    '".utf8_decode($obras_sociales)."',
                    '".utf8_decode($pacientes)."',
                    '".utf8_decode($planes_contingencia)."',
                    '".utf8_decode($practicas_medicas)."',
                    '".utf8_decode($sectores)."',
                    '".utf8_decode($sectores_uno)."',
                    '".utf8_decode($subsectores)."',
                    '".utf8_decode($consultorios)."',
                    '".utf8_decode($disponibilidades)."',
                    '".utf8_decode($tareas)."',
                    '".utf8_decode($tareas_configuracion)."',
                    '".utf8_decode($tareas_pendientes)."',
                    '".utf8_decode($cumples)."',
                    '".utf8_decode($usuarios_permiso)."'
					)";

		print $query_string = $obj->querys->Alta($obj->nombre_tabla, $columnas, $valores);

		if ($obj->db->consulta($query_string))
			$rta = $obj->db->ultimo_id_insertado();
		else
			$rta = false;

	break;
	case "notas_impresion":
		parse_str(stripslashes($datos));

		$columnas = "(
					nombre,
                    detalle,
                    estado
					)";

		$valores = "(
					'".utf8_decode($nombre)."',
					'".utf8_decode($detalle)."',
                    1
					)";

		$query_string = $obj->querys->Alta($obj->nombre_tabla, $columnas, $valores);

		if ($obj->db->consulta($query_string)) {
			$rta = $obj->db->ultimo_id_insertado();
            $query_string = <<<SQL
                DELETE FROM notas_impresion_estudios
                WHERE id_notas_impresion = '{$rta}';
SQL;
            $this_db->consulta($query_string);
            foreach ($id_estudios as $itm_est) {
                $query_string = <<<SQL
                    INSERT INTO notas_impresion_estudios
                    (id_notas_impresion, id_estudios, estado)
                    VALUES ('{$rta}', '{$itm_est}', 1);
SQL;
                $this_db->consulta($query_string);
            }
		} else {
			$rta = false;
        }

	break;
	case "planes_de_contingencia":
		parse_str(stripslashes($datos));

		$columnas = "(
					nombre,
                    descripcion,
                    estado
					)";

		$valores = "(
					'".str_replace("'", "\\'", utf8_decode($nombre))."',
                    '".str_replace("'", "\\'", utf8_decode($descripcion))."',
                    1
					)";

		$query_string = $obj->querys->Alta($obj->nombre_tabla, $columnas, $valores);

		if ($obj->db->consulta($query_string))
			$rta = $obj->db->ultimo_id_insertado();
		else
			$rta = false;

	break;
	case "tareas_configuracion":
		parse_str(stripslashes($datos));

		$columnas = "(
					nombre,
                    estado
					)";

		$valores = "(
					'".str_replace("'", "\\'", utf8_decode($nombre))."',
                    1
					)";

		$query_string = $obj->querys->Alta($obj->nombre_tabla, $columnas, $valores);

		if ($obj->db->consulta($query_string))
			$rta = $obj->db->ultimo_id_insertado();
		else
			$rta = false;

	break;
	case "tareas_requisitos":
		parse_str(stripslashes($datos));

        $nombre = (int)$nombre;
        if ($nombre < 1) $nombre = 1;

		$columnas = "(
                    id_tareas_configuracion,
					nombre,
                    descripcion,
                    estado
					)";

		$valores = "(
					'".$id_tareas_configuracion."',
					'".str_replace("'", "\\'", utf8_decode($nombre))."',
                    '".str_replace("'", "\\'", utf8_decode($descripcion))."',
                    1
					)";

		$query_string = $obj->querys->Alta($obj->nombre_tabla, $columnas, $valores);

        /**********************************************************************/
        $query_correr_ids = <<<SQL
            UPDATE tareas_requisitos
            SET nombre = nombre + 1
            WHERE id_tareas_configuracion = '{$id_tareas_configuracion}' AND nombre >= {$nombre}
SQL;
		$obj->db->consulta($query_correr_ids);
        /**********************************************************************/

        if ($obj->db->consulta($query_string)) {
			$rta = $obj->db->ultimo_id_insertado();
            /******************************************************************/
            $query_correr_ids = <<<SQL
                SELECT id_tareas_requisitos
                FROM tareas_requisitos
                WHERE id_tareas_configuracion = '{$id_tareas_configuracion}' and estado = 1
                ORDER BY nombre, id_tareas_requisitos
SQL;
    		$result = $obj->db->consulta($query_correr_ids);
            $i = 1;
            while ($row = $obj->db->fetch_assoc($result)) {
                $query_correr_ids = <<<SQL
                    UPDATE tareas_requisitos
                    SET nombre = {$i}
                    WHERE id_tareas_requisitos = '{$row['id_tareas_requisitos']}'
SQL;
        		$obj->db->consulta($query_correr_ids);
                $i++;
            }
            /******************************************************************/
		} else {
			$rta = false;
        }

	break;
	case "tareas_pedidos":
		parse_str(stripslashes($datos));

		$columnas = "(
                    id_tareas_configuracion,
					nombre,
                    descripcion,
                    estado
					)";

		$valores = "(
					'".$id_tareas_configuracion."',
					'".implode("-", array_reverse(explode("/", $nombre)))."',
                    '".str_replace("'", "\\'", utf8_decode($descripcion))."',
                    1
					)";

		$query_string = $obj->querys->Alta($obj->nombre_tabla, $columnas, $valores);

        if ($obj->db->consulta($query_string)) {
			$rta = $obj->db->ultimo_id_insertado();
            /******************************************************************/
            $query_traer_requisitos = <<<SQL
                SELECT *
                FROM tareas_requisitos
                WHERE id_tareas_configuracion = '{$id_tareas_configuracion}' and estado = 1
                ORDER BY nombre, id_tareas_requisitos
SQL;
    		$result = $obj->db->consulta($query_traer_requisitos);
            while ($row = $obj->db->fetch_assoc($result)) {
                $query_agregar_realizadas = <<<SQL
                    INSERT INTO tareas_realizadas
                        (
                            id_tareas_pedidos,
                            nombre,
                            descripcion,
                            estado,
                            status
                        )
                    VALUES
                        (
                            '{$rta}',
                            '{$row['nombre']}',
                            '{$row['descripcion']}',
                            '1',
                            '0'
                        )
SQL;
        		$obj->db->consulta($query_agregar_realizadas);
            }
            /******************************************************************/
		} else {
			$rta = false;
        }

	break;

}
echo $rta;

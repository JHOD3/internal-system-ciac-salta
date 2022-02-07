<?php
require_once("../engine/config.php");
require_once("../engine/restringir_acceso.php");
requerir_class("tpl","querys","mysql","estructura");
$this_db = new MySQL();

$tabla = $_POST["tabla"];
$datos = $_POST["variables"];

requerir_class($tabla);

$clase = ucwords($tabla);
$obj = new $clase();

$hora_actual = date("H:i:s");
$fecha_actual = date('Y-m-d');

switch ($tabla){
	case "medicos_especialidades":
		parse_str(stripslashes($datos));
		$ids = [$id_medico,$id_especialidad];
		$asignaciones = "duracion_turno = '".$duracion_turno."'";
		$query_string = $obj->querys->Modificaciones($obj->nombre_tabla, $asignaciones, $ids);

		if ($obj->db->consulta($query_string))
			$rta = true;
		else
			$rta = false;			
	break;
	case "pacientes":
		parse_str(stripslashes($datos));

		if((!isset($obras_sociales_planes)) || ($obras_sociales_planes == "") ){
			$obras_sociales_planes = 0;
		}

		$asignaciones = "";
        if ($_SESSION['SUPERUSER'] > 1) {
    		$asignaciones.= "
                bloqueado = '".$bloqueado."',
    		";
        }
		$asignaciones.= "
			id_tipos_documentos = ".$tipos_documentos.",
			id_obras_sociales = ".$obras_sociales.",
			apellidos = '".upper(utf8_decode($apellidos))."',
			nombres = '".upper(utf8_decode($nombres))."',
			telefonos = '".$telefonos."',
			nro_documento = '".$nro_documento."',
			domicilio = '".upper(utf8_decode($domicilio))."',
			email = '".upper($email)."',
			id_obras_sociales_planes = ".$obras_sociales_planes."
		";

		$query_string = $obj->querys->Modificaciones($obj->nombre_tabla, $asignaciones, $id);

		if ($obj->db->consulta($query_string))
			$rta = true;
		else
			$rta = false;

	break;
	case "pacientes_observaciones":
		parse_str(stripslashes($datos));

		$asignaciones = "
			observacion = '".str_replace("'", "\\'", utf8_decode($observacion))."',
			id_usuarios = '{$_SESSION['ID_USUARIO']}'
		";

		$query_string = $obj->querys->Modificaciones($obj->nombre_tabla, $asignaciones, $id);

		if ($obj->db->consulta($query_string))
			$rta = true;
		else
			$rta = false;

	break;
	case "medicos":
		parse_str(stripslashes($datos));

		if (!isset($sectores) || $sectores == "")
			$sectores = 0;

		if (!isset($subsectores) || $subsectores == "")
			$subsectores = 0;

		if (!isset($nro_sector) || $nro_sector == "")
			$nro_sector = "-";

		if (!isset($plantas) || $plantas == "")
			$plantas = 0;

		if (!isset($interno) || $interno == "")
			$interno = 0;

		if (!isset($matricula) || $matricula == "")
			$matricula = 0;

		$asignaciones = "
					id_tipos_documentos = ".$tipos_documentos.",
					apellidos = '".upper(utf8_decode($apellidos))."',
					nombres = '".upper(utf8_decode($nombres))."',
					telefonos = '".$telefonos."',
					nro_documento = '".$nro_documento."',
					domicilio = '".upper(utf8_decode($domicilio))."',
					email = '".upper($email)."',
					id_sectores = ".$sectores.",
					id_subsectores = ".$subsectores.",
					nro_sector = '".upper(utf8_decode($nro_sector))."',
					id_plantas = ".$plantas.",
					interno = ".$interno.",
					matricula = ".$matricula.",
                    saludo = '".$saludo."',
                    fechanac = '".implode("-", array_reverse(explode("/", $fechanac)))."'
					";
		if (!isset($contrasena) || trim($contrasena) != "")
		$asignaciones .= ", pass = '" . base64_encode($contrasena)."' ";
		
		#usuario = '".lower($obj->QuitarTildes($nombres[0].$apellidos))."',
		#pass = '".base64_encode($nro_documento)."',

		$query_string = $obj->querys->Modificaciones($obj->nombre_tabla, $asignaciones, $id);

		if ($obj->db->consulta($query_string))
			$rta = true;
		else
			$rta = false;

	break;
	case "medicosext":
		parse_str(stripslashes($datos));

		if (!isset($matricula) || $matricula == "")
			$matricula = 0;

		$asignaciones = "
			apellidos = '".upper(utf8_decode($apellidos))."',
			nombres = '".upper(utf8_decode($nombres))."',
			matricula = ".$matricula.",
            saludo = '".$saludo."',
            email = '".upper($email)."',
            domicilio = '".upper(utf8_decode($domicilio))."',
            telefonos = '".$telefonos."',
            fechanac = '".implode("-", array_reverse(explode("/", $fechanac)))."'
		";

		$query_string = $obj->querys->Modificaciones($obj->nombre_tabla, $asignaciones, $id);

		if ($obj->db->consulta($query_string))
			$rta = true;
		else
			$rta = false;

	break;
	case "medicos_horarios":
		parse_str(stripslashes($datos));

		if (!isset($plantas) || $plantas == "")
			$plantas = 0;

		$asignaciones = "
					desde = '".$desde."',
					hasta = '".$hasta."',
					duracion_turno = '".$duracion_turno."',
					id_turnos_tipos = '".$turnos_tipos."',
					id_plantas = '".$plantas."',
                    nro_consultorio = '".$nro_consultorio."'
					";

		$query_string = $obj->querys->Modificaciones($obj->nombre_tabla, $asignaciones, $id);
		//error_log($query_string);

		if ($obj->db->consulta($query_string))
			$rta = true;
		else
			$rta = false;

	break;
	case "especialidades":
		parse_str(stripslashes($datos));

		$asignaciones = "
					nombre = '".upper(utf8_decode($nombre))."'
					";

		$query_string = $obj->querys->Modificaciones($obj->nombre_tabla, $asignaciones, $id);

		if ($obj->db->consulta($query_string))
			$rta = true;
		else
			$rta = false;

	break;
	case "estudios":
		parse_str(stripslashes($datos));

		$asignaciones = "
					nombre = '".upper(utf8_decode($nombre))."',
					importe = '".$importe."',
					arancel = '".$arancel."',
					requisitos = '".utf8_decode($requisitos)."',
					codigopractica = '".$codigopractica."'
					";

		$query_string = $obj->querys->Modificaciones($obj->nombre_tabla, $asignaciones, $id);

		if ($obj->db->consulta($query_string))
			$rta = true;
		else
			$rta = false;

	break;
	case "obras_sociales":
		parse_str(stripslashes($datos));

		$asignaciones = "
					nombre = '".upper(utf8_decode($nombre))."',
					abreviacion = '".upper(utf8_decode($abreviacion))."',
					importe_consulta = '".$importe_consulta."'
					";

		$query_string = $obj->querys->Modificaciones($obj->nombre_tabla, $asignaciones, $id);

		if ($obj->db->consulta($query_string))
			$rta = true;
		else
			$rta = false;

	break;
	case "turnos":
		/*$columnas = $obj->NombreColumnas();
		$resp = $obj->Alta($datos,$columnas);*/
		parse_str(stripslashes($datos));

		if (isset($_POST['tipo'])){
			$trae_orden = 0;
			$trae_pedido = 0;
			if (!isset($arancel_diferenciado)) {
				$arancel_diferenciado = 0;
            }

			requerir_class("turnos");
			$obj_turno = new Turnos($id_turno);

			$obj_turno->OrdenesyPedidos($trae_orden, $trae_pedido, $arancel_diferenciado);

			$rta = true;
		}else{
			$columnas = "(id_medicos, id_especialidades, id_pacientes, id_turnos_estados, fecha, desde, hasta, trae_orden, trae_pedido, arancel_diferenciado, id_medicos_derivacion, id_especialidades_derivacion, es_derivacion_externa, id_turnos_tipos, estado)";

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
				0,
				".$id_turno_tipo.",
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
		$asignaciones = "
					id_obras_sociales = ".$id_obra_social.",
					id_estudios = ".$estudios.",
					importe = '".$importe."',
					nomenclador = '".$nomenclador."'
					";

		$query_string = $obj->querys->Modificaciones($obj->nombre_tabla, $asignaciones, $id);

		if ($obj->db->consulta($query_string))
			$rta = true;
		else
			$rta = false;
	break;
	case "obras_sociales_planes":
		parse_str(stripslashes($datos));
		$asignaciones = "
					nombre = '".$nombre."'
					";

		$query_string = $obj->querys->Modificaciones($obj->nombre_tabla, $asignaciones, $id);

		if ($obj->db->consulta($query_string))
			$rta = true;
		else
			$rta = false;
	break;
	case "medicos_obras_sociales":
		parse_str(stripslashes($datos));
		$asignaciones = "
					arancel = '".$arancel."'
					";

		$query_string = $obj->querys->Modificaciones($obj->nombre_tabla, $asignaciones, $id);

		if ($obj->db->consulta($query_string))
			$rta = true;
		else
			$rta = false;
	break;
	case "medicos_estudios":
		parse_str(stripslashes($datos));
		$asignaciones = "
					particular = '".$particular."',
					arancel = '".$arancel."'
					";

		$query_string = $obj->querys->Modificaciones($obj->nombre_tabla, $asignaciones, $id);

		if ($obj->db->consulta($query_string))
			$rta = true;
		else
			$rta = false;
	break;
	case "turnos_estudios":
		parse_str(stripslashes($datos));
		$columnas = "(id_turnos, id_estudios, estado)";
		$ids_estudios =  rtrim($_POST["ids_estudios"], ", ");

		$id_estudiov = explode(", ", $ids_estudios);

		$obj->BajaxTurno($id_turno);

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

	case "mensajes":
		parse_str(stripslashes($datos));

		$columnas = "(id_emisor, id_receptor, mensaje, hora, fecha, leido, estado)";

		$valores = "(
			'".$id_emisor."',
			'".$id_receptor."',
			'".$mensaje."',
			'".$hora_actual."',
			'".$fecha_actual."',
			0,
			1)";

		$query_string = $obj->querys->Alta($obj->nombre_tabla, $columnas, $valores);

		if ($obj->db->consulta($query_string))
			$rta = $obj->db->ultimo_id_insertado();
		else
			$rta = false;
	break;

	case "sectores":
		parse_str(stripslashes($datos));

		$asignaciones = "
					nombre = '".utf8_decode($nombre)."'
					";

        $query_string = $obj->querys->Modificaciones($obj->nombre_tabla, trim($asignaciones), $id);

		if ($obj->db->consulta($query_string))
			$rta = true;
		else
			$rta = false;

	break;

	case "subsectores":
		parse_str(stripslashes($datos));

		$asignaciones = "
					id_sectores = '".utf8_decode($sectores)."',
                    nombre = '".utf8_decode($nombre)."'
					";

        $query_string = $obj->querys->Modificaciones($obj->nombre_tabla, trim($asignaciones), $id);

		if ($obj->db->consulta($query_string))
			$rta = true;
		else
			$rta = false;

	break;

	case "novedades_diarias":
		parse_str(stripslashes($datos));

		$asignaciones = "
			titulo = '".utf8_decode(mysql_real_escape_string($titulo))."',
			descripcion = '".utf8_decode(mysql_real_escape_string($descripcion))."'
        ";

        $query_string = $obj->querys->Modificaciones($obj->nombre_tabla, trim($asignaciones), $id);

		if ($obj->db->consulta($query_string))
			$rta = true;
		else
			$rta = false;

	break;

	case "agendas":
		parse_str(stripslashes($datos));

		$asignaciones = "
					nombre = '".utf8_decode(upper($nombre))."',
					apellido = '".utf8_decode(upper($apellido))."',
					rubro = '".utf8_decode(upper($rubro))."',
					celular = '".utf8_decode(upper($celular))."',
					telefono = '".utf8_decode(upper($telefono))."',
					direccion = '".utf8_decode(upper($direccion))."',
					id_agendas_tipos = '".utf8_decode($agendas_tipos)."'
					";

        $query_string = $obj->querys->Modificaciones($obj->nombre_tabla, trim($asignaciones), $id);

		if ($obj->db->consulta($query_string))
			$rta = true;
		else
			$rta = false;

	break;
	case "mantenimientos":
		parse_str(stripslashes($datos));

		if (!isset($sectores) || $sectores == "")
			$sectores = 0;

		$asignaciones = "
                    fecha = '".date("Y-m-d H:i:s")."',
					id_sectores = '".$sectores."',
					solicitador = '".utf8_decode(upper($solicitador))."',
					tarea = '".utf8_decode(upper($tarea))."',
					especialista = '".utf8_decode(upper($especialista))."',
					observaciones = '".utf8_decode(upper($observaciones))."',
                    id_mantenimientos_estados = '".$mantenimientos_estados."',
                    id_usuarios = '".$_SESSION['ID_USUARIO']."'
					";

        $query_string = $obj->querys->Modificaciones($obj->nombre_tabla, trim($asignaciones), $id);

		if ($obj->db->consulta($query_string)) {
			$rta = true;
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
                    id_mantenimientos = '{$id}'
            ";
    		$obj->db->consulta($query_string2);
        } else {
			$rta = false;
        }

	break;

	case "usuarios":
		parse_str(stripslashes($datos));

		$asignaciones = "
			superuser = '".utf8_decode($roles)."',
			nombres = '".utf8_decode($nombres)."',
			apellidos = '".utf8_decode($apellidos)."',
			usuario = '".utf8_decode(strtolower($usuario))."',
            fechanac = '".implode("-", array_reverse(explode("/", $fechanac)))."',
            agenda = '".utf8_decode(strtolower($agenda))."',
			comunicacion = '".utf8_decode(strtolower($comunicacion))."',
			comunicados_gerencia = '".utf8_decode(strtolower($comunicados_gerencia))."',
			novedades_diarias = '".utf8_decode(strtolower($novedades_diarias))."',
			notas_imprecion = '".utf8_decode(strtolower($notas_imprecion))."',
			encuestas = '".utf8_decode(strtolower($encuestas))."',
			especialidades = '".utf8_decode(strtolower($especialidades))."',
			estudios = '".utf8_decode(strtolower($estudios))."',
			mantenimiento = '".utf8_decode(strtolower($mantenimiento))."',
			mantenimiento_reciente = '".utf8_decode(strtolower($mantenimiento_reciente))."',
			mantenimiento_historico = '".utf8_decode(strtolower($mantenimiento_historico))."',
			medicos = '".utf8_decode(strtolower($medicos))."',
			medicos_ciac = '".utf8_decode(strtolower($medicos_ciac))."',
			medicos_externos = '".utf8_decode(strtolower($medicos_externos))."',
			medicos_empresas = '".utf8_decode(strtolower($medicos_empresas))."',
			obras_sociales = '".utf8_decode(strtolower($obras_sociales))."',
			pacientes = '".utf8_decode(strtolower($pacientes))."',
			planes_contingencia = '".utf8_decode(strtolower($planes_contingencia))."',
			practicas_medicas = '".utf8_decode(strtolower($practicas_medicas))."',
			sectores = '".utf8_decode(strtolower($sectores))."',
			sectores_uno = '".utf8_decode(strtolower($sectores_uno))."',
			subsectores = '".utf8_decode(strtolower($subsectores))."',
			consultorios = '".utf8_decode(strtolower($consultorios))."',
			disponibilidades = '".utf8_decode(strtolower($disponibilidades))."',
			tareas = '".utf8_decode(strtolower($tareas))."',
			tareas_configuracion = '".utf8_decode(strtolower($tareas_configuracion))."',
			tareas_pendientes = '".utf8_decode(strtolower($tareas_pendientes))."',
			cumples = '".utf8_decode(strtolower($cumples))."',
			usuarios_permiso = '".utf8_decode(strtolower($usuarios_permiso))."'

        ";
        if (isset($pass) and trim($pass)) {
            $asignaciones.= ", pass = '".base64_encode($pass)."' ";
        }

        $query_string = $obj->querys->Modificaciones($obj->nombre_tabla, trim($asignaciones), $id);

		if ($obj->db->consulta($query_string))
			$rta = true;
		else
			$rta = false;

	break;

	case "notas_impresion":
		parse_str(stripslashes($datos));

		$asignaciones = "
					nombre = '".utf8_decode($nombre)."',
					detalle = '".utf8_decode($detalle)."'
					";

        $query_string = $obj->querys->Modificaciones($obj->nombre_tabla, trim($asignaciones), $id);

		if ($obj->db->consulta($query_string)) {
            $query_string = <<<SQL
                DELETE FROM notas_impresion_estudios
                WHERE id_notas_impresion = '{$id}';
SQL;
            $this_db->consulta($query_string);
            foreach ($id_estudios as $itm_est) {
                $query_string = <<<SQL
                    INSERT INTO notas_impresion_estudios
                    (id_notas_impresion, id_estudios, estado)
                    VALUES ('{$id}', '{$itm_est}', 1);
SQL;
                $this_db->consulta($query_string);
            }
			$rta = true;
		} else {
			$rta = false;
        }

	break;

	case "planes_de_contingencia":
		parse_str(stripslashes($datos));

		$asignaciones = "
					nombre = '".str_replace("'", "\\'", utf8_decode($nombre))."',
                    descripcion = '".str_replace("'", "\\'", utf8_decode($descripcion))."'
					";

        $query_string = $obj->querys->Modificaciones($obj->nombre_tabla, trim($asignaciones), $id);

		if ($obj->db->consulta($query_string))
			$rta = true;
		else
			$rta = false;

	break;

	case "tareas_configuracion":
		parse_str(stripslashes($datos));

		$asignaciones = "
					nombre = '".str_replace("'", "\\'", utf8_decode($nombre))."'
					";

        $query_string = $obj->querys->Modificaciones($obj->nombre_tabla, trim($asignaciones), $id);

		if ($obj->db->consulta($query_string))
			$rta = true;
		else
			$rta = false;

	break;

	case "tareas_requisitos":
		parse_str(stripslashes($datos));

        $nombre = (int)$nombre;
        if ($nombre < 1) $nombre = 1;

		$asignaciones = "
					nombre = '".str_replace("'", "\\'", utf8_decode($nombre))."',
                    descripcion = '".str_replace("'", "\\'", utf8_decode($descripcion))."'
					";

        $query_string = $obj->querys->Modificaciones($obj->nombre_tabla, trim($asignaciones), $id);

        /**********************************************************************/
        $query_correr_ids = <<<SQL
            UPDATE tareas_requisitos
            SET nombre = nombre + 1
            WHERE id_tareas_configuracion = '{$id_tareas_configuracion}' AND nombre >= {$nombre}
SQL;
		$obj->db->consulta($query_correr_ids);
        /**********************************************************************/

		if ($obj->db->consulta($query_string)) {
			$rta = true;
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

		$asignaciones = "
					nombre = '".implode("-", array_reverse(explode("/", $nombre)))."',
                    descripcion = '".str_replace("'", "\\'", utf8_decode($descripcion))."'
					";

        $query_string = $obj->querys->Modificaciones($obj->nombre_tabla, trim($asignaciones), $id);

		if ($obj->db->consulta($query_string))
			$rta = true;
		else
			$rta = false;

	break;

	case "tareas_realizadas":
		parse_str(stripslashes($datos));

		$asignaciones = "
			fechahora = '".date("Y-m-d H:i:s")."',
            id_usuarios = '{$_SESSION['ID_USUARIO']}',
            status = 1
		";

        $query_string = $obj->querys->Modificaciones($obj->nombre_tabla, trim($asignaciones), $_POST['id_tareas_realizadas']);

		if ($obj->db->consulta($query_string)) {
			$rta = true;
            /******************************************************************/
            $query_cerrar_tarea = <<<SQL
                SELECT
                    tr1.id_tareas_pedidos,
                    COUNT(tr1.id_tareas_pedidos) AS Total
                FROM tareas_realizadas AS tr1
                INNER JOIN tareas_realizadas AS tr2
                    ON tr1.id_tareas_pedidos = tr2.id_tareas_pedidos
                WHERE
                    tr1.id_tareas_realizadas = '{$_POST['id_tareas_realizadas']}' AND
                    tr1.estado = 1 AND
                    tr2.estado = 1 AND
                    tr2.status = 0
SQL;
    		$result = $obj->db->consulta($query_cerrar_tarea);
            if ($row = $obj->db->fetch_assoc($result)) {
                if ($row['Total'] == 0) {
                    $query_cerrar_tarea = <<<SQL
                        UPDATE tareas_pedidos
                        SET status = 1
                        WHERE id_tareas_pedidos = '{$row['id_tareas_pedidos']}'
SQL;
            		$obj->db->consulta($query_cerrar_tarea);
                }
            }
            /******************************************************************/
		} else {
			$rta = false;
        }

	break;

}
echo $rta;

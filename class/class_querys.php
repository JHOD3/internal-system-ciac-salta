<?php
interface iQuerys{

}

class Querys implements iQuerys{
	
	function __construct(){ 

    }
	
	function Registro($tabla,$id){
		switch ($tabla){
			default:
				$query = "SELECT * 
					FROM ".$tabla."
					WHERE id_".$tabla." = ".$id;	
		}
		//echo $query;
		return $query;
	}
	
	function TodosRegistros($tabla, $orden, $inicio = "", $cantidad = ""){
		
		switch($tabla){
			case 'medicos':
			case 'usuarios':
				$query = "SELECT * FROM ".$tabla." WHERE estado = 1 ORDER BY apellidos ".$orden;
			break;
			case 'obras_sociales':
			case 'estudios':
				$query = "SELECT * FROM ".$tabla." ORDER BY nombre ".$orden;
			break;
			default:
				$query = "SELECT * FROM ".$tabla." ORDER BY id_".$tabla." ".$orden;
		}
		
		if ($inicio != ""){
			$query .= " LIMIT $inicio, $cantidad";
			
		}
		return $query;	
		
	}
	
	function Registros($tabla, $atributo, $valor, $ordenar = "", $inicio = "", $final = "", $orden = "DESC"){
		if ($ordenar == "")
			$query = "SELECT * FROM ".$tabla." WHERE ".$atributo."=".$valor." ORDER BY id_".$tabla." ".$orden;
		else
			$query = "SELECT * FROM ".$tabla." WHERE ".$atributo."=".$valor." ORDER BY ".$ordenar." ".$orden;
		if (($inicio != "") && ($final != "")){
			$query .= " LIMIT ".$inicio.",".$final;	
		}
		return $query;
	}
	
	function RegistroXAtributo($tabla, $atributo, $valor, $tipo){
		if ($tipo == ""){
			$query = "SELECT * FROM ".$tabla." WHERE ".$atributo." = '".mysql_real_escape_string(utf8_decode($valor))."'";
		}else{
			$query = "SELECT * FROM ".$tabla." WHERE ".$atributo." LIKE '%".mysql_real_escape_string(utf8_decode($valor))."%'";
		}
		return $query;		
	}
	
	function ValidaLogueo($tabla, $usuario, $pass){
		$query = "SELECT * FROM ".$tabla." 
				  WHERE usuario ='". mysql_real_escape_string($usuario)."' AND pass = '". mysql_real_escape_string($pass) ."'";
		////error_log($query);
		return $query;		
	} 
	
	function CambiarEstado($tabla, $id, $id_estado){
		$query = "UPDATE ".$tabla." SET id_".$tabla."_estados = $id_estado WHERE id_".$tabla." = $id";
		return $query;
		
	}
	function Alta($tabla, $columnas, $valores){
		$hora_actual = date("H:i:s");
		$fecha_actual = date('Y-m-d');
		$fecha_hora_actual = date("Y-m-d H:i:s");
		
		switch ($tabla){
			case "turnos":
			case "turnos_estudios":
			case "mensajes":
			case "pacientes":
			case "medicos":
			case "cobros":
			case "egresos":
			case "especialidades":
			case "estudios":
			case "obras_sociales":
			case "medicos_especialidades":
			case "medicos_horarios":
			case 'medicos_estudios':
			case 'obras_sociales_estudios':
			case 'obras_sociales_planes':
			case 'medicos_obras_sociales':
			case 'horarios_inhabilitados':
			case "turnos_cambios_estados":
				$query = "INSERT INTO ".$tabla." ".$columnas." VALUES ".$valores;			
			break;
			case "medicos_cancelaciones":
				
				parse_str($valores, $datosv);
				
				$valores = 
					$datosv["id_turno"].",
					'".$fecha_actual."',
					'".$hora_actual."',
					'INHABILITACION DE HORARIO',".
					$_SESSION['ID_USUARIO'].",
					1"
				;
				$query = "INSERT INTO ".$tabla." (".$columnas.") VALUES (".$valores.")";
			break;
		}
		//echo ($query);
		return $query;
	}

	function Modificaciones($tabla, $asignacion, $id){
		$hora_actual = date("H:i:s");
		$fecha_actual = date('Y-m-d');
		$fecha_hora_actual = date("Y-m-d H:i:s");
		
		switch ($tabla){
			case "pacientes":
			case "medicos":
			case 'medicos_horarios':
			case 'medicos_estudios':
			case 'medicos_obras_sociales':
			case 'especialidades':
			case "estudios":
			case "obras_sociales":
			case 'obras_sociales_estudios':
			case 'obras_sociales_planes':
			
				$query = "UPDATE ".$tabla." SET ".$asignacion." WHERE id_".$tabla." = ".$id;			
			break;
		}
		return $query;
	}
	
	function Baja($tabla, $id){
		$query = "DELETE FROM ".$tabla." WHERE id_".$tabla." = ".$id;
		//echo $query;
		return $query;
	}

	function Inhabilitar($tabla, $id){
		$query = "UPDATE ".$tabla." SET estado = 0 WHERE id_".$tabla." = ".$id;
		//echo $query;
		return $query;
	}
	
	function BajaCobrosxMedicos($id_turno){
		$query = 'DELETE FROM cobros WHERE id_turnos = '.$id_turno;
		return $query;	
	}
	
	function DelDia($id, $tabla_padre){
		$fecha_actual = date('Y-m-d');
		/*$query = "SELECT DISTINCT R.id_recordatorios,  R.nombre, CA.id_casos, CA.carpeta_legajo, CA.responsable, CL.nombre as NOMBRE_CLIENTE 
		FROM recordatorios R 
		INNER JOIN casos  CA ON R.id_casos = CA.id_casos 
		INNER JOIN clientes CL ON CA.id_clientes = CL.id_clientes 
		WHERE (R.fecha_recordatorio = '".$fecha_actual."'";*/
		
		$query = "SELECT DISTINCT R.id_recordatorios, R.fecha_recordatorio,  R.nombre, CA.id_casos, CA.carpeta_legajo, CA.responsable, CL.nombre as NOMBRE_CLIENTE 
		FROM recordatorios R 
		INNER JOIN casos  CA ON R.id_casos = CA.id_casos 
		INNER JOIN clientes CL ON CA.id_clientes = CL.id_clientes 
		WHERE (R.estado = 0 AND R.fecha_recordatorio <= '".$fecha_actual."' ";
		
		
		
		switch ($tabla_padre){
			case "empresas":
				$query .= "AND CL.id_".$tabla_padre." = $id";
			break;
			
			case "clientes":	
				$query .= "AND CA.id_".$tabla_padre." = $id";
		}
		
		$query .= ") ORDER BY R.fecha_recordatorio";

		////error_log(print_r($query,1));
		return $query;
	}
	
	function ExisteCarpeta($carpeta_legajo, $id_cliente){
		$query = "SELECT id_casos FROM casos WHERE carpeta_legajo = '".$carpeta_legajo."' AND id_clientes= $id_cliente";
		return $query;	
	}
	
	function ConsultaPagosXClientes($id_cliente, $fecha_inicio, $fecha_fin){
		$query = "SELECT 
		CA.responsable AS responsable, 
		CA.carpeta_legajo AS carpeta_legajo, 
		CA.id_tipos_conceptos,
		P.*
		FROM pagos P INNER JOIN casos CA ON P.id_casos = CA.id_casos
		INNER JOIN clientes CL ON CA.id_clientes = CL.id_clientes
		WHERE CL.id_clientes = ".$id_cliente." AND P.fecha >= '".$fecha_inicio."'  AND P.fecha <= '".$fecha_fin."' ORDER BY P.fecha_pago DESC";
		return $query;
		
	}
	
	/*ESTE ANDA BIEN, PERO NO LE SIRVE A MACARENA... PORQUE TIENE EL RENDIDO O NO, Y YA LO DEJO DE USAR...
	function ConsultaPagosXClientes($id_cliente, $fecha_inicio, $fecha_fin){
		$query = "SELECT 
		CA.responsable AS responsable, 
		CA.carpeta_legajo AS carpeta_legajo, 
		CA.id_tipos_conceptos,
		P.*
		FROM pagos P INNER JOIN casos CA ON P.id_casos = CA.id_casos
		INNER JOIN clientes CL ON CA.id_clientes = CL.id_clientes
		WHERE CL.id_clientes = ".$id_cliente." AND P.fecha >= '".$fecha_inicio."'  AND P.fecha <= '".$fecha_fin."' AND P.rendido = 0 ORDER BY P.fecha_pago DESC";
		return $query;
		
	}*/
	
	function ConsultaGestionesXClientes($id_cliente, $fecha_inicio, $fecha_fin){
		$query = "SELECT 
		CA.responsable AS responsable, 
		CA.carpeta_legajo AS carpeta_legajo, 
		CA.id_tipos_conceptos,
		G.*
		FROM gestiones G INNER JOIN casos CA ON G.id_casos = CA.id_casos
		INNER JOIN clientes CL ON CA.id_clientes = CL.id_clientes
		WHERE CL.id_clientes = ".$id_cliente." AND G.fecha >= '".$fecha_inicio."'  AND G.fecha <= '".$fecha_fin."' ORDER BY CA.responsable, G.fecha_gestion DESC";
		return $query;
		
	}

	function ActualizarAccesos($tabla, $id, $datos){
		$query = "UPDATE ".$tabla." SET accesos = '".$datos."' WHERE id_".$tabla." = $id";
		return $query;
	}
	
	function ListadoReportes($tabla, $id, $tipos_conceptos){
		switch ($tabla){
			case "clientes":
				$query = "SELECT * FROM casos WHERE id_".$tabla." = $id AND id_tipos_conceptos = ".$tipos_conceptos;
			break;
			case "empresas":
				$query = "SELECT * 
						FROM casos CA INNER JOIN clientes CL ON CA.id_clientes = CL.id_clientes WHERE CL.id_".$tabla." = $id AND CA.id_tipos_conceptos = ".$tipos_conceptos;
			break;	
		}
		return $query;
	}
	
	function Buscar($tabla, $termino){
		switch ($tabla){
			case "pacientes":
				$query = "SELECT * FROM ".$tabla." WHERE estado = 1 AND nro_documento = '".$termino."'";
			break;
			case "medicos":
				$query = "SELECT * FROM ".$tabla." WHERE (nombres LIKE '%".$termino."%' OR apellidos LIKE '%".$termino."%') AND estado = 1";
			break;	
		}
		
		return $query;
		
	}
	
	function DiasTrabajo($id_medico, $id_especialidad){
		$query = "SELECT DISTINCT id_dias_semana FROM medicos_horarios WHERE id_medicos = $id_medico AND id_especialidades = $id_especialidad AND estado = 1";
		return $query;	
	}
	
	function GrillaTurnos($id_medico, $id_especialidad, $id_dia){
		/*$query = "SELECT * 
				FROM medicos_horarios MH 
				INNER JOIN medicos_especialidades ME 
				ON MH.id_medicos = ME.id_medicos 
				WHERE ME.id_medicos = $id_medico AND ME.id_especialidades = $id_especialidad AND MH.id_dias_semana = $id_dia AND MH.estado = 1 AND ME.estado = 1 AND ME.id_medicos_especialidades =(SELECT MAX(id_medicos_especialidades) FROM medicos_especialidades WHERE id_medicos = $id_medico AND id_especialidades = $id_especialidad AND id_dias_semana = $id_dia AND estado = 1 ) ORDER BY MH.desde ASC";*/
		$query = "SELECT * 
				FROM medicos_horarios MH  
				WHERE MH.id_medicos = $id_medico AND MH.id_especialidades = $id_especialidad AND MH.id_dias_semana = $id_dia AND MH.estado = 1 ORDER BY MH.desde ASC";		
		return $query;
		
	}
	
	function TurnosReservados($fecha, $id_medico, $id_especialidad, $id_dia){
		$query = "SELECT *, TE.nombre AS nombre_estado, OS.nombre AS nombre_os
				FROM turnos T
				INNER JOIN turnos_estados TE
				ON T.id_turnos_estados = TE.id_turnos_estados
				INNER JOIN pacientes P
				ON T.id_pacientes = P.id_pacientes
				LEFT JOIN obras_sociales OS
				ON P.id_obras_sociales = OS.id_obras_sociales
				WHERE T.id_medicos = $id_medico AND T.id_especialidades = $id_especialidad AND T.fecha = '".$fecha."' AND T.estado = 1 AND (T.id_turnos_estados = 1 OR T.id_turnos_estados = 2 OR T.id_turnos_estados = 4 OR T.id_turnos_estados = 7)
				ORDER BY T.desde ASC";
		return $query;
	}
	
	function GrillaTurnosPasados($id_medico, $id_especialidad, $fecha){
		$query = "SELECT *, OS.nombre AS nombre_os 
				FROM turnos T 
				INNER JOIN turnos_estados TE
				ON T.id_turnos_estados = TE.id_turnos_estados
				INNER JOIN pacientes P
				ON T.id_pacientes = P.id_pacientes
				INNER JOIN obras_sociales OS
				ON P.id_obras_sociales = OS.id_obras_sociales
				WHERE T.id_medicos = $id_medico AND T.id_especialidades = $id_especialidad AND T.fecha = '".$fecha."' AND T.estado = 1 ORDER BY T.desde ASC";
	
		return $query;
		
	}
	
	function GrillaTurnosImprimir($id_medico, $id_especialidad, $fecha, $horarios){
		$horariosv = explode("-",$horarios);
		
		$query = "SELECT DISTINCT T.desde,P.nombres, P.apellidos, T.id_turnos, OS.nombre AS nombre_os , OS.abreviacion
				FROM turnos T 
				INNER JOIN turnos_estados TE
				ON T.id_turnos_estados = TE.id_turnos_estados
				INNER JOIN pacientes P
				ON T.id_pacientes = P.id_pacientes
				INNER JOIN obras_sociales OS
				ON P.id_obras_sociales = OS.id_obras_sociales
				WHERE T.id_medicos = $id_medico AND T.id_especialidades = $id_especialidad AND T.fecha = '".$fecha."' AND T.estado = 1 AND (T.id_turnos_estados = 1 OR T.id_turnos_estados = 2 OR T.id_turnos_estados = 3) AND T.desde >= '".$horariosv[0]."' AND T.desde <= '".$horariosv[1]."' ORDER BY T.desde ASC";

		return $query;
		
	}
	
	function GrillaCobrosImprimir($id_medico, $id_especialidad, $fecha, $horarios){
		$horariosv = explode("-",$horarios);
		
		$query = "SELECT * 
				FROM turnos T
				INNER JOIN pacientes P
				ON T.id_pacientes = P.id_pacientes
				INNER JOIN obras_sociales OS
				ON P.id_obras_sociales = OS.id_obras_sociales
				WHERE T.id_medicos = $id_medico AND T.id_especialidades = $id_especialidad AND T.fecha = '".$fecha."' AND T.estado = 1 AND (T.id_turnos_estados = 2 OR T.id_turnos_estados = 7) AND T.desde >= '".$horariosv[0]."' AND T.desde <= '".$horariosv[1]."' ORDER BY T.desde ASC";

		return $query;
		
	}	
	
	
	function CobrosTurnos($id_turnos){
		$query = "SELECT nombre AS nombre_cobros, importe 
				FROM cobros C
				INNER JOIN cobros_conceptos CC
				ON C.id_cobros_conceptos = CC.id_cobros_conceptos
				WHERE C.id_turnos = $id_turnos";
		return $query;	
	}
	
	function Cobros($id_medico, $fecha){
		$query = "SELECT *, CC.nombre AS nombre_cobro_concepto
				FROM cobros C
				INNER JOIN pacientes P
				ON C.id_pacientes = P.id_pacientes
				INNER JOIN turnos T
				ON C.id_turnos = T.id_turnos
				INNER JOIN cobros_conceptos CC
				ON C.id_cobros_conceptos = CC.id_cobros_conceptos
				WHERE C.id_medicos = $id_medico AND T.fecha = '".$fecha."'
				ORDER BY P.apellidos";
		return $query;
		
	}
	
	function Arancel($id_medico, $fecha){
		$query = "SELECT * 
				FROM turnos T
				INNER JOIN pacientes P
				ON T.id_pacientes = P.id_pacientes
				WHERE T.id_medicos = $id_medico AND T.fecha = '".$fecha."' AND T.arancel_diferenciado <> 0
				ORDER BY P.apellidos";
		return $query;
		
	}
	
	function ExisteTurno($fecha, $inicio, $fin, $id_medico, $id_especialidad){
		/*$query = "SELECT *
				FROM turnos T
				INNER JOIN pacientes P
				ON T.id_pacientes = P.id_pacientes
				INNER JOIN turnos_estados TE
				ON T.id_turnos_estados = TE.id_turnos_estados
				WHERE T.fecha = '".$fecha."' AND T.desde = '".$inicio."' AND T.hasta = '".$fin."' AND T.id_medicos = $id_medico AND T.id_especialidades = $id_especialidad AND (T.id_turnos_estados = 1 OR T.id_turnos_estados = 2 OR T.id_turnos_estados = 3)";*/
		$query = "SELECT T.id_turnos, T.id_turnos_tipos, TE.id_turnos_estados, TE.color, TE.nombre AS nombre_estado, P.apellidos, P.nombres,  P.telefonos, OS.nombre AS nombre_os
				FROM turnos T
				LEFT JOIN turnos_estados TE
				ON T.id_turnos_estados = TE.id_turnos_estados
				LEFT JOIN pacientes P
				ON T.id_pacientes = P.id_pacientes
				LEFT JOIN obras_sociales OS
				ON P.id_obras_sociales = OS.id_obras_sociales
				WHERE T.fecha = '".$fecha."' AND T.desde = '".$inicio."' AND T.id_medicos = $id_medico AND T.id_especialidades = $id_especialidad AND (T.id_turnos_estados = 1 OR T.id_turnos_estados = 2 OR T.id_turnos_estados = 3 OR T.id_turnos_estados = 4 OR T.id_turnos_estados = 7)";
		return $query;
	}
	
	function _HorariosInhabilitados($fecha, $inicio, $fin, $id_medico, $id_especialidad){
		$query = "SELECT *
				FROM horarios_inhabilitados
				WHERE fecha = '".$fecha."' AND (desde <= '".$inicio."' AND hasta >= '".$fin."') AND id_medicos = $id_medico AND id_especialidades = $id_especialidad";
		return $query;
	}
	
	function HorariosInhabilitados($fecha, $id_medico, $id_especialidad){
		$query = "SELECT *
				FROM horarios_inhabilitados
				WHERE fecha = '".$fecha."' AND id_medicos = $id_medico AND id_especialidades = $id_especialidad";
		return $query;
	}
	
	function DropHorariosInhabilitados($id_medico, $id_especialidad, $fecha){
		$query = "SELECT *
				FROM horarios_inhabilitados
				WHERE fecha = '".$fecha."' AND id_medicos = $id_medico AND id_especialidades = $id_especialidad";
		return $query;
	}
	
	function TipoHorario($dia, $inicio, $fin, $id_medico, $id_especialidad){
		$query = "SELECT * 
				FROM medicos_horarios
				WHERE id_dias_semana = $dia AND desde <= '".$inicio."' AND hasta >= '".$fin."' AND id_medicos = $id_medico AND id_especialidades = $id_especialidad AND estado = 1";
		return $query;
	}
	
	function DropMedicosEspecialidades($id_medico){
		/*$query = "SELECT ME.*, M.*, E.nombre, E.id_especialidades 
				FROM medicos_especialidades ME 
				INNER JOIN medicos M
				ON ME.id_medicos = M.id_medicos
				INNER JOIN especialidades E
				ON ME.id_especialidades = E.id_especialidades
				WHERE ME.id_medicos = $id_medico AND ME.estado = 1 AND M.estado = 1 AND ME.id_medicos_especialidades =(SELECT MAX(id_medicos_especialidades) FROM medicos_especialidades WHERE id_medicos = $id_medico AND estado = 1 )
				ORDER BY ME.id_medicos_especialidades DESC"*/;
		$query = "SELECT  DISTINCT ME.id_especialidades, ME.id_medicos_especialidades, E.nombre, E.id_especialidades 
				FROM medicos_especialidades ME 
				INNER JOIN especialidades E
				ON ME.id_especialidades = E.id_especialidades
				WHERE ME.id_medicos = $id_medico AND ME.estado = 1 
				GROUP BY ME.id_especialidades
				ORDER BY ME.id_medicos_especialidades ASC";
		return $query;
	}
	
	function EstudiosSeleccion($id_obra_social){
		$query = "SELECT E.*, E.importe as IMPORTE_PARTICULAR, OSE.importe as IMPORTE_OS 
				FROM estudios E
				LEFT JOIN obras_sociales_estudios OSE
				ON E.id_estudios = OSE.id_estudios
				WHERE OSE.id_obras_sociales = $id_obra_social";
		return $query;
	}
	
	function EstudiosSeleccionMedicosOS($id_medico, $id_obra_social){
		/*$query = 'SELECT E.*, OSE.importe AS IMPORTE_OS, E.importe AS IMPORTE_PARTICULAR FROM estudios E
				INNER JOIN  medicos_estudios ME 
				ON E.id_estudios = ME.id_estudios
				INNER JOIN obras_sociales_estudios OSE
				ON E.id_estudios = OSE.id_estudios
				WHERE ME.id_medicos = '.$id_medico.' AND OSE.id_obras_sociales = '.$id_obra_social;*/
		$query = 'SELECT E.*, OSE.importe AS IMPORTE_OS, E.importe AS IMPORTE_PARTICULAR FROM estudios E
				INNER JOIN  medicos_estudios ME 
				ON E.id_estudios = ME.id_estudios
				LEFT JOIN obras_sociales_estudios OSE
				ON E.id_estudios = OSE.id_estudios
				WHERE ME.id_medicos = '.$id_medico;	
		//error_log($query);	
		return $query;

	}
	
	function EstudiosSeleccionados($id_turno, $id_medico){
		$query = "SELECT TE.id_estudios, ME.particular 
				FROM turnos_estudios TE
				INNER JOIN estudios E
				ON TE.id_estudios = E.id_estudios
				LEFT JOIN medicos_estudios ME
				ON TE.id_estudios = ME.id_estudios
				WHERE TE.id_turnos = $id_turno";
		return $query;
	}
	
	function ObrasSocialesPlanes(){
		$query = "SELECT CASE WHEN OSP.nombre is null THEN '' ELSE CONCAT('- ', OSP.nombre) END AS nombre_plan, CASE WHEN OSP.id_obras_sociales_planes is null THEN '0' ELSE OSP.id_obras_sociales_planes END AS id_obra_social_plan,  OS.*, OS.nombre AS nombre_os
				FROM obras_sociales OS 
				LEFT JOIN  obras_sociales_planes OSP  
				ON OS.id_obras_sociales = OSP.id_obras_sociales;";
		return $query;
	}
	
	function ObrasSocialesSeleccionadas($id_medico){
		$query = "SELECT * FROM medicos_obras_sociales WHERE id_medicos = $id_medico AND estado = 1";
		return $query;
	}
	
	function EstudiosSeleccionadosMedico($id_medico){
		$query = "SELECT * FROM medicos_estudios WHERE id_medicos = $id_medico";
		return $query;
	}
	
	function BajaxTurno($id_turno){
		$query = "DELETE FROM turnos_estudios WHERE id_turnos = ".$id_turno;
		return $query;
	}
	
	function BajaxMedico($id_medico){
		$query = "DELETE FROM medicos_obras_sociales WHERE id_medicos = ".$id_medico;
		return $query;
	}
	
	function Mensajes($id_emisor, $id_receptor){
		//$query = "SELECT * FROM mensajes WHERE (id_emisor = '".$id_emisor."' AND id_receptor = '".$id_receptor."') OR (id_emisor = '".$id_receptor."' AND id_receptor = '".$id_emisor."') ORDER BY fecha DESC, hora ASC";
		$query = "SELECT * FROM mensajes WHERE (id_emisor = '".$id_emisor."' AND id_receptor = '".$id_receptor."') OR (id_emisor = '".$id_receptor."' AND id_receptor = '".$id_emisor."') ORDER BY id_mensajes ASC";
		return $query;	
	}
	
	function Totales($id_turno, $id_obra_social){
		$query = 'SELECT SUM(ME.particular) as PARTICULAR
					FROM medicos_estudios ME
					INNER JOIN turnos_estudios TE
					ON ME.id_estudios = TE.id_estudios
					WHERE TE.id_turnos = '.$id_turno;
		/*$query = "SELECT SUM(E.importe) as PARTICULAR, SUM(OSE.importe) as OBRA_SOCIAL 
				FROM turnos_estudios TE 
				INNER JOIN estudios E 
				ON TE.id_estudios = E.id_estudios 
				INNER JOIN obras_sociales_estudios OSE 
				ON E.id_estudios = OSE.id_estudios 
				WHERE TE.id_turnos = $id_turno AND OSE.id_obras_sociales = $id_obra_social";*/
		return $query;	
	}
	
	function Atiende($id_medico, $id_obra_social, $id_obra_social_plan){
		$query = "SELECT *
				FROM medicos_obras_sociales
				WHERE id_medicos = $id_medico AND id_obras_sociales = $id_obra_social AND estado = 1";
				//error_log($query);
		/*$query = "SELECT *
				FROM medicos_obras_sociales
				WHERE id_medicos = $id_medico AND id_obras_sociales = $id_obra_social AND id_obras_sociales_planes = $id_obra_social_plan";*/
		return $query;
	}
	
	function MensajesSinLeer($id_actor, $id_secundario){
			$query = "SELECT * FROM mensajes WHERE id_receptor = '".$id_actor."' AND leido = 0";
			return $query;
		
	}
	
	function ActualizarTipo($id_turno, $id_turno_tipo){
		$query = "UPDATE turnos SET id_turnos_tipos = $id_turno_tipo WHERE id_turnos = $id_turno";
		////error_log($query);
		return $query;
	}
	
	function ReintegroEfectuado($id_cobro){
		$query = 'UPDATE cobros SET reintegro = 1 WHERE id_cobros = '. $id_cobro;
		return $query;	
	}
	
	function ArancelConsulta($id_medico, $id_obra_social){
		$query = 'SELECT * FROM medicos_obras_sociales WHERE id_medicos = '.$id_medico.' AND id_obras_sociales = '.$id_obra_social.' AND estado = 1';
		return $query;	
	}
	
	function EstudiosTurnos($id_turnos){
		$query = "SELECT DISTINCT E.id_estudios, E.nombre AS nombre_estudio 
				FROM turnos_estudios TE 
				INNER JOIN estudios E
				ON TE.id_estudios = E.id_estudios
				WHERE TE.id_turnos = $id_turnos";
		return $query;
		
	}
	
	function OrdenesyPedidos($id_turno, $trae_orden, $trae_pedido, $arancel_diferenciado){
		if ($trae_orden == 1){
			$query = 'UPDATE turnos SET trae_orden = 1, arancel_diferenciado = '.$arancel_diferenciado.' WHERE id_turnos = '.$id_turno;	
		}
		if ($trae_pedido == 1){
			$query = 'UPDATE turnos SET trae_pedido = 1, arancel_diferenciado = '.$arancel_diferenciado.' WHERE id_turnos = '.$id_turno;
		}
		if ($trae_orden == 1 && $trae_pedido == 1){
			$query = 'UPDATE turnos SET trae_orden = 1, trae_pedido = 1, arancel_diferenciado = '.$arancel_diferenciado.' WHERE id_turnos = '.$id_turno;	
		}
		
		if ($trae_orden == 0 && $trae_pedido == 0){
			$query = 'UPDATE turnos SET arancel_diferenciado = '.$arancel_diferenciado.' WHERE id_turnos = '.$id_turno;
		}
		return $query;
		
	}
	
	
	function ContadorOrdenesPedidos($id_medico, $fecha, $tipo){
		if ($tipo == 'pedidos'){
			$query = "SELECT id_turnos AS contador FROM turnos WHERE trae_pedido = 1 AND id_medicos = $id_medico AND fecha = '".$fecha."'";
		}else{
			$query = "SELECT id_turnos AS contador FROM turnos WHERE trae_orden = 1 AND id_medicos = $id_medico AND fecha = '".$fecha."'";
		}
		//echo($query);
		
		return $query;	
	}
	
	function RangoTurnosDia($id_medico, $id_especialidad, $dia_semana){
		$query = "SELECT desde, hasta
				FROM medicos_horarios
				WHERE id_medicos = $id_medico AND id_especialidades = $id_especialidad AND id_dias_semana = $dia_semana AND estado = 1";
		return $query;
	}
	
	function DuracionTurno($id_medico, $id_especialidad){
		$query = "SELECT * FROM medicos_especialidades WHERE id_medicos = $id_medico AND id_especialidades = $id_especialidad ORDER BY id_medicos_especialidades DESC LIMIT 0,1";
		return  $query;
	}
	
	function RestablecerOrdenesyPedidos($id_turno){
		$query = "UPDATE turnos SET trae_pedido = 0, trae_orden = 0, arancel_diferenciado = 0 WHERE id_turnos = $id_turno";
		return  $query;
	}
	
	function ExisteTurnoReservado($fecha, $desde, $hasta, $id_medico, $id_especialidad){
		/*$query = "SELECT id_turnos
				FROM turnos
				WHERE id_medicos = $id_medico AND id_especialidades = $id_especialidad AND fecha = '".$fecha."' AND desde = '".$desde."' AND hasta = '".$hasta."' AND id_turnos_estados = 1 AND estado = 1";*/
		$query = "SELECT id_turnos
				FROM turnos
				WHERE id_medicos = $id_medico AND id_especialidades = $id_especialidad AND fecha = '".$fecha."' AND desde = '".$desde."' AND (id_turnos_estados = 1 OR id_turnos_estados = 2 OR id_turnos_estados = 4 OR id_turnos_estados = 7) AND estado = 1";
		//error_log($query);
		return $query;	
	}
	
	function Duplicados($id_medico, $id_especialidad, $fecha){
		$query = "SELECT *, COUNT(desde) as TOTAL
				FROM turnos
				WHERE id_medicos = $id_medico AND id_especialidades = $id_especialidad AND fecha = '".$fecha."' AND estado = 1 AND (id_turnos_estados = 1 OR id_turnos_estados = 2 OR id_turnos_estados = 7)
				GROUP BY desde
				HAVING COUNT(desde) > 1	";
		return $query;	
	}
	
	function MedicosSAM(){
		$query = "SELECT  DISTINCT M.id_medicos, M.nombres, M.apellidos, E.nombre as especialidad, M.interno, M.id_sectores, M.nro_sector 
				FROM medicos M
				INNER JOIN medicos_especialidades ME 
				ON M.id_medicos = ME.id_medicos
				INNER JOIN especialidades E
				ON ME.id_especialidades = E.id_especialidades
				WHERE M.estado = 1 AND ME.estado = 1";
		return $query;	
	}
}
?>
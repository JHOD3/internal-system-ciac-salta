<?php
interface iMedicos{

}

class Medicos extends Estructura implements iMedicos{

	function __construct($id = ""){
		$this->nombre_tabla = "medicos";
		$this->titulo_tabla = "Médicos";
		$this->titulo_tabla_singular = "Médico";
		$this->tabla_padre = "";

		$this->drop_label_elija = "Elija un medico";

		parent::__construct($id);

		requerir_class("pacientes", "turnos_estados", "sectores", "subsectores", "plantas", "tipos_documentos");
	}

	function FormAlta(){
		$htm = $this->Html($this->nombre_tabla."/form_alta");

		requerir_class("tipos_documentos", "especialidades", "turnos_tipos", "dias_semana", "plantas", "obras_sociales", "estudios");

		$obj_tipos_documentos = new Tipos_Documentos();
		$htm->Asigna("DROP_TIPOS_DOCUMENTOS",$obj_tipos_documentos->Drop("", 1));

		$obj_sectores = new Sectores();
		$htm->Asigna("DROP_SECTORES", $obj_sectores->Drop());

		$obj_subsectores = new Subsectores();
		$htm->Asigna("DROP_SUBSECTORES", $obj_subsectores->Drop());

		$obj_plantas = new Plantas();
		$htm->Asigna("DROP_PLANTAS", $obj_plantas->Drop());

		$obj_especialidades = new Especialidades();
		$htm->Asigna("DROP_ESPECIALIDADES",$obj_especialidades->Drop());

		$obj_turnos_tipos = new Turnos_tipos();
		$htm->Asigna("DROP_TURNOS_TIPOS",$obj_turnos_tipos->Drop());

		$obj_dias_semana = new Dias_semana();
		$htm->Asigna("DROP_DIAS_SEMANA", $obj_dias_semana->Drop());

		$obj_plantas = new Plantas();
		$htm->Asigna("DROP_PLANTA", $obj_plantas->Drop());

		$obj_obras_sociales = new Obras_sociales();
		$htm->Asigna("DROP_OBRAS_SOCIALES", $obj_obras_sociales->Drop('nombre'));

		$obj_estudios = new Estudios();
		$htm->Asigna("DROP_ESTUDIOS",$obj_estudios->Drop());

		$htm->Asigna("TABLA",$this->nombre_tabla);

		CargarVariablesGrales($htm, $tipo = "");

		return utf8_encode($htm->Muestra());
	}

	function FormModificacion(){
		$htm = $this->Html($this->nombre_tabla."/form_modificacion");
		$row = $this->registro;
        $row['fechanac'] = date("d/m/Y", strtotime($row['fechanac']));


		$obj_tipos_documentos = new Tipos_Documentos();
		$row["DROP_TIPOS_DOCUMENTOS"] = $obj_tipos_documentos->Drop("DESC", $row["id_tipos_documentos"]);

		$obj_sectores = new Sectores();
		$htm->Asigna("DROP_SECTORES", $obj_sectores->Drop("", $row["id_sectores"]));

		$obj_subsectores = new Subsectores();
		$htm->Asigna("DROP_SUBSECTORES", $obj_subsectores->Drop("", $row["id_subsectores"]));

		$obj_plantas = new Plantas();
		$htm->Asigna("DROP_PLANTAS", $obj_plantas->Drop("", $row["id_plantas"]));

		$htm->Asigna("TABLA",$this->nombre_tabla);

		$htm->AsignaBloque('block_registros',$row);

		CargarVariablesGrales($htm, $tipo = "");

		return  utf8_encode($htm->Muestra());
	}

	function TablaAdmin(){
		$tabla = $this->html($this->nombre_tabla."/a_tabla_".$_SESSION['SISTEMA']);

		switch ($_SESSION['SISTEMA']){
			case "sam":
				$query_string = $this->querys->MedicosSAM();
				$query = $this->db->consulta($query_string);

				requerir_class("sectores");

				while ($row = $this->db->fetch_array($query)){

					$obj_sectores = new Sectores($row['id_sectores']);
					$row['SECTOR'] = $obj_sectores->nombre." ".$row['nro_sector'];

					$row['email'] = strtolower($row['email']);

					$tabla->AsignaBloque("block_".$this->nombre_tabla, $row);
				}

			break;
		}

		$tabla->Asigna("NOMBRE_TABLA",$this->nombre_tabla);

		$rta = utf8_encode($tabla->Muestra());

		return $rta;
	}

    function logoutSessionState($id_usuario){
        $this->db->consulta($this->querys->session_state_change($this->nombre_tabla, $id_usuario, 'null'));
        return true;
    }

    function cargar_medicos_activos()
    {
        $query = $this->db->consulta($this->querys->medicosLogueados());
        $cant_usr = $this->db->num_rows($query);
        $usuarios = [];
        if ($cant_usr > 0){
            while ($usr = $this->db->fetch_array($query))
            {
                $usuarios[] = array(
                        "id_medico" => $usr[0],
                        "nombre_completo" => utf8_encode($usr[1]),
                        "sistema" => 'sam',
                        "activo" => (!empty($usr[2]))?$usr[2]:false,
                        "id_usuario" => $_SESSION['ID_USUARIO'],
                        "count" => $usr[3],
                        "chat_id_usuario" => $usr[4]
                    );
            }
        }
        return $usuarios;
    }
    function eliminar_acentos($cadena){

        //Reemplazamos la A y a
        $cadena = str_replace(
            array('Á', 'À', 'Â', 'Ä', 'á', 'à', 'ä', 'â', 'ª'),
            array('A', 'A', 'A', 'A', 'a', 'a', 'a', 'a', 'a'),
            $cadena
        );

        //Reemplazamos la E y e
        $cadena = str_replace(
            array('É', 'È', 'Ê', 'Ë', 'é', 'è', 'ë', 'ê'),
            array('E', 'E', 'E', 'E', 'e', 'e', 'e', 'e'),
            $cadena );

        //Reemplazamos la I y i
        $cadena = str_replace(
            array('Í', 'Ì', 'Ï', 'Î', 'í', 'ì', 'ï', 'î'),
            array('I', 'I', 'I', 'I', 'i', 'i', 'i', 'i'),
            $cadena );

        //Reemplazamos la O y o
        $cadena = str_replace(
            array('Ó', 'Ò', 'Ö', 'Ô', 'ó', 'ò', 'ö', 'ô'),
            array('O', 'O', 'O', 'O', 'o', 'o', 'o', 'o'),
            $cadena );

        //Reemplazamos la U y u
        $cadena = str_replace(
            array('Ú', 'Ù', 'Û', 'Ü', 'ú', 'ù', 'ü', 'û'),
            array('U', 'U', 'U', 'U', 'u', 'u', 'u', 'u'),
            $cadena );

        //Reemplazamos la N, n, C y c
        $cadena = str_replace(
            array('Ñ', 'ñ', 'Ç', 'ç'),
            array('N', 'n', 'C', 'c'),
            $cadena
        );

        return $cadena;
    }

    function cargar_usuarios_chat_de_usuario($id_medico, $id_usuario)
    {
        $query = $this->db->consulta($this->querys->loadChat($id_medico, $id_usuario));
        $this->db->consulta("UPDATE chats SET view_usuario = NULL WHERE id_usuarios = ".$id_usuario." AND id_medicos = ".$id_medico);
        $cant_usr = $this->db->num_rows($query);
        $chats = [];
        if ($cant_usr > 0){
            while ($usr = $this->db->fetch_array($query))
            {
                $chats[] = [
                    'id_chats' => $usr[0],
                    'id_medico' => $usr[1],
                    'id_usuario' => $usr[2],
                    'mensaje' => $usr[3],
                    'fecha' =>  $usr[4],
                    'enviado_por' =>  $usr[6],
                ];
            }
        }
        return $chats;
    }

    function send_mensaje($id_medico, $id_usuario, $mensaje, $enviado_por){
        if ($enviado_por == 'usuario'){
            $query = $this->db->consulta('
            INSERT INTO chats (id_chats, id_medicos, id_usuarios, mensaje, fecha, estatus, enviado_por, view_medico, view_usuario) 
            VALUES  (null, '.$id_medico.', '.$id_usuario.', "'.$mensaje.'", "'.date("Y-m-d").'",1,"'.$enviado_por.'",1,null)');
        }else{
            $query = $this->db->consulta('
            INSERT INTO chats (id_chats, id_medicos, id_usuarios, mensaje, fecha, estatus, enviado_por, view_medico, view_usuario) 
            VALUES  (null, '.$id_medico.', '.$id_usuario.', "'.$mensaje.'", "'.date("Y-m-d").'",1,"'.$enviado_por.'",null,1)');
        }

        return true;
    }

	function ValidaLogueo($usuario, $pass){
		//Verifico si existe usuario con ese nombre y clave
		$query = $this->db->consulta($this->querys->ValidaLogueo($this->nombre_tabla, $usuario, base64_encode($pass)));

		$cant_usr = $this->db->num_rows($query);

		if ($cant_usr == 1){
			while ($usr = $this->db->fetch_array($query))
			{
                $this->db->consulta($this->querys->session_state_change($this->nombre_tabla, $usr[0], 'activo'));
				//variable para controlar tiempo que esta conectado
				$ultimo_acceso = date("Y-n-j H:i:s");

				$_SESSION['ID_USUARIO'] = (1000000 + $usr[0]);
				$_SESSION['ID_MEDICO'] = $usr[0];
				$_SESSION['TIPO_USR'] = "M";
				$_SESSION['APELLIDOS'] = $usr[1];
				$_SESSION['NOMBRES'] = $usr[2];
				$_SESSION['USUARIO'] = $usr[3];
				$_SESSION['SISTEMA'] = 'sam';
				$_SESSION['EMISOR'] = $_SESSION['TIPO_USR']."-".$_SESSION["ID_MEDICO"];
				$_SESSION['ULTIMO_ACCESO'] = $ultimo_acceso;
			}
			return ("1"); //Login Correcto
		}
		else
			return ("2"); //Login Incorrecto
	}



	function Detalle($tipo, $id_especialidad = "", $sistema = ""){
		if ($sistema != ""){
			$htm = $this->Html($sistema."/".$this->nombre_tabla."/detalle_".$tipo);
		}else{
			$htm = $this->Html($this->nombre_tabla."/detalle_".$tipo);
		}
		$row = $this->registro;

		switch ($sistema){
			case "sam":
				requerir_class("medicos_especialidades");
				$obj_medicos_especialidades = new Medicos_especialidades();

				$row["DROP_ESPECIALIDADES"] = $obj_medicos_especialidades->Drop("", "", $row["id_medicos"]);
			break;
		}

		switch ($tipo){
			case "corto_especialidad":
				requerir_class("especialidades");
				$obj_especialidades = new Especialidades($id_especialidad);

				$row["MEDICO_ESPECIALIDAD"] = $obj_especialidades->nombre;
			break;
		}
		$htm->AsignaBloque('block_registros',$row);

		$rta = $htm->Muestra();

		return $rta;
	}

	function Buscar($termino){
		$htm_form = $this->Html($this->nombre_tabla."/form_alta");

		$query_string = $this->querys->Buscar($this->nombre_tabla, $termino);
		$query = $this->db->consulta($query_string);
		$cant = $this->db->num_rows($query);


		$rta = Array();

		if ($cant != 0){
			while ($row = $this->db->fetch_array($query)){
				$elto = Array(
					"id" => utf8_encode($row["id_medicos"]),
					"saludo" => utf8_encode($row["saludo"]),
					"nombres" => utf8_encode($row["nombres"]),
					"apellidos" => utf8_encode($row["apellidos"]),
					"icon" => utf8_encode($row["email"])
				);
				$rta[] = $elto;
			}
		}

		return $rta;
	}

	/*function GrillaTurnos2 ($id_medico, $id_especialidad, $id_dia, $fecha){

		$fechav = new DateTime($fecha);
		$fecha = $fechav->format('y-m-d');

		$fechav1 = explode('-',$fecha);
		$diaSemana = $this->diaSemana($fechav1[0], $fechav1[1], $fechav1[2]);

		requerir_class("dias_semana");
		$obj_dia_semana = new Dias_semana($diaSemana);

		if ($fecha >= date('y-m-d')){

			$htm = $this->Html($this->nombre_tabla."/grilla_turnos_".$_SESSION['SISTEMA']);

			$query_string = $this->querys->GrillaTurnos($id_medico, $id_especialidad, $id_dia);
			$query = $this->db->consulta($query_string);
			$cant = $this->db->num_rows($query);

			$desdev = array();
			$hastav = array();
			if ($cant != 0){

				requerir_class('medicos_especialidades');
				$obj_medico_especialidad = new Medicos_especialidades();

				$duracion_turno = $obj_medico_especialidad->DuracionTurno($id_medico, $id_especialidad);

				while ($row = $this->db->fetch_array($query)){

					requerir_class("turnos");
					$obj_turnos = new Turnos();

					$suma = $row["desde"];
					while ($suma <= $row["hasta"]){
						$row1["DESDE"] = $suma;
						$desdev[] = $suma;

						$suma = $this->SumarHorasTime($suma,$duracion_turno);
						$row1["HASTA"] = $suma;
						$hastav[] = $suma;

						if(isset($row["id_turnos_tipos"]) && (2 - ($row["id_turnos_tipos"] % 2)) == 2){
							$tipo_turno = "estudios";
						}else{
							$tipo_turno	= "consultas";
						}

						$row1["ESTADO"] = $obj_turnos->ExisteTurno($fecha, $row1["DESDE"], $row1["HASTA"], $id_medico, $id_especialidad, $id_dia, $tipo_turno);



						$htm->AsignaBloque('block_registros',$row1);
					}

					/*$desdev["label_elija"] = "Horario Desde";
					$hastav["label_elija"] = "Horario Hasta";

					$htm->Asigna('DROP_DESDE', $this->DropArmado('desde', $desdev));
					$htm->Asigna('DROP_HASTA', $this->DropArmado('hasta', $hastav));
					$htm->Asigna('MJE',"");
				}
			}else{
				$htm->AsignaBloque('block_registros',"");
				$htm->Asigna('MJE',"Hoy no atiende este Profesional.");
			}
		}else{
			$htm = $this->Html($this->nombre_tabla."/grilla_turnos_pasado");
			$query_string = $this->querys->GrillaTurnosPasados($id_medico, $id_especialidad, $fecha);
			$query = $this->db->consulta($query_string);
			$cant = $this->db->num_rows($query);
			if ($cant != 0){
				while ($row = $this->db->fetch_array($query)){
					$row["PACIENTE"] =$row ["apellidos"].", ".$row['nombres'];

					$obj_turno_estado = new Turnos_estados($row["id_turnos_estados"]);
					$row["ESTADO"] = $obj_turno_estado->nombre;
					$htm->AsignaBloque('block_registros',$row);
				}
				$htm->Asigna('MJE',"");
			}else{
				$htm->AsignaBloque('block_registros',"");
				$htm->Asigna('MJE',"No se atendi&oacute; a ningun paciente");
			}
		}

		$drop_horarios_inhabilitado = $this->HorariosInhabilitadosPorFecha($id_medico, $id_especialidad, $fecha);

		$htm->Asigna("DROP_HORARIOS_INHABILITADOS", $drop_horarios_inhabilitado);

		$htm->Asigna("FECHA_CON_DIA", $obj_dia_semana->nombre.' '.$this->cambiaf_a_normal($fecha, "/"));

		$htm->Asigna("FECHA", $this->cambiaf_a_normal($fecha, "/"));

		$htm->Asigna("ID_MEDICO", $id_medico);
		$htm->Asigna("ID_ESPECIALIDAD", $id_especialidad);

		CargarVariablesGrales($htm, $tipo = "");

		return  utf8_encode($htm->Muestra());


	}*/

	function GrillaTurnos($id_medico, $id_especialidad, $id_dia, $fecha){
		$fechav = new DateTime($fecha);
		$fecha = $fechav->format('y-m-d');

		$fechav1 = explode('-',$fecha);
		$diaSemana = $this->diaSemana($fechav1[0], $fechav1[1], $fechav1[2]);

        $consultorio = array(
            'nro_consultorio' => '-',
            'interno' => '-'
        );

		requerir_class("dias_semana");
		$obj_dia_semana = new Dias_semana($diaSemana);

        if ($fecha >= date('y-m-d')){
			$htm = $this->Html($this->nombre_tabla."/grilla_turnos_".$_SESSION['SISTEMA']);

			//kcmnt aqui ve si el medico atiende en este dia
			//VERIFICO LOS HORARIOS QUE TIENE CARGADO EL MEDICO PARA UN DIA DETERMINADO
			$query_string = $this->querys->GrillaTurnos($id_medico, $id_especialidad, $id_dia);
			$query = $this->db->consulta($query_string);
			$cant = $this->db->num_rows($query);
			//VOY A ARMAR EL LISTADO DE HORARIOS DE CADA TURNO
            $aTurnos = array();
			while ($row2 = $this->db->fetch_array($query)){
			    $aTurnos[$row2['desde']] = array(
                    'desde' => $row2['desde'],
                    'hasta' => $row2['hasta'],
                    'id_turnos_tipos' => $row2['id_turnos_tipos']
                );
                $consultorio['nro_consultorio'] = $row2['nro_consultorio'];
                $consultorio['interno'] = $row2['interno'];
            }
            $keyTurnos = array_keys($aTurnos);
			$query = $this->db->consulta($query_string);

			//PREGUNTO SI TIENE CARGADO HORARIOS PARA UN DIA DADO TODO
			if (/*$cant > 0*/ true){

				//kcmnt Aqui tendria que agregar la nueva logica de Duracion de turno x dia y especialidad
				//VERIFICO LA DURACION DEL TURNO
				requerir_class('medicos_especialidades');
				$obj_medico_especialidad = new Medicos_especialidades();
				$duracion_turno = $obj_medico_especialidad->DuracionTurnoDia($id_medico, $id_especialidad, $diaSemana);

				//TRAIGO TODOS LOS TURNOS RESERVADOS
				$query_string2 = $this->querys->TurnosReservados($fecha, $id_medico, $id_especialidad, $id_dia);
				$query2 = $this->db->consulta($query_string2);
				$cant2 = $this->db->num_rows($query2);

                $init = date("Y-m-01", strtotime($fechav->format('y-m-d')));
                $end = date("Y-m-t", strtotime($fechav->format('y-m-d')));


                $query_string4 = $this->querys->TurnosReservadosEnMes($init, $end, $id_medico, $id_especialidad, $id_dia);
                $query4 = $this->db->consulta($query_string4);
				$cant4 = $this->db->num_rows($query4);

				$turnos_reservadosv = array();
                $turnos_reservadosEnMes = array();
				$turnos_libresv = array();
				$inhabilitadosv = array();
				$grillav = array();

                if($cant4 > 0){
                    while ($row = $this->db->fetch_array($query4)){
                        $turnos_reservadosEnMes[] = $row;
                    }
                }

				if ($cant2 > 0){
					while ($row = $this->db->fetch_array($query2)){
                        $clasm = ' sobreturno2';
                        for ($i = 0; $i < count($aTurnos); $i++) {
                            if (
                                $row['desde'] >= $aTurnos[$keyTurnos[$i]]['desde'] and
                                $row['desde'] <= $aTurnos[$keyTurnos[$i]]['hasta']
                            ) {
                                $clasm = '';
                            }
                        }


						//ES ESTUDIO O CONSULTA
						if(isset($row["id_turnos_tipos"]) && (2 - ($row["id_turnos_tipos"] % 2)) == 2){
							$tipo_turno = "estudios";
						}else{
							$tipo_turno	= "consultas";
						}

						//VOY CREANDO EL ARRAY CON LOS TURNOS RESERVADOS
						$turnos_reservadosv[] = $row;

						//ARMO EL LISTADO DE TURNOS RESERVADOS
						switch ($_SESSION['SISTEMA']){
							case 'sas':
								$query_string_estudios = $this->querys->EstudiosTurnos($row["id_turnos"]);
								$query_estudios = $this->db->consulta($query_string_estudios);
								$cant_estudios = $this->db->num_rows($query_estudios);

								$linea = " <span style='color:#".$row["color"]."' class='btn_estado_turno".$clasm."' data-id='".$row["id_turnos"]."' data-id_turnos_tipos='".$row["id_turnos_tipos"]."' data-id_turnos_estados='".$row["id_turnos_estados"]."' data-tipo='turno'>
									<div class='bloque'>
										<img src='".IMG."btns/tipo_".$tipo_turno.".png' /><span>".substr($row["desde"], 0, 5)." &raquo;</span>
										<div class='dat_paciente' style='display: inline-grid;'><div>".upper(trim($row["apellidos"])). ", ".upper(trim($row["nombres"]))."(".$row["nombre_estado"].")"
                                ;
                                $cnct = " ";
                                if ($tipo_turno	== "consultas" and $row['id_turnos_estados'] > 1) {
                                    if ($row['trae_orden'] == 0) {
                                        $linea.= " <sup>TO</sup>";
                                        $cnct = "<sup>+</sup>";
                                    } elseif ($row['trae_orden'] > 0 and $row['valor_orden'] > 0) {
                                        $linea.= " <sup>\${$row['valor_orden']}</sup>";
                                        $cnct = "<sup>+</sup>";
                                    }
                                    if ($row['trae_pedido'] == 1) {
                                        $linea.= "{$cnct}<sup>DD</sup><br />";
                                    } elseif ($row['trae_pedido'] == 2 and $row['arancel_diferenciado'] > 0) {
                                        $linea.= "{$cnct}<sup>\$d{$row['arancel_diferenciado']}</sup>";
                                    }
                                }
                                if ($row['aviso_demora'] == '1') {
                                    $linea .= " <sup style=\"color:#c00;\">DEMORADO</sup>";
                                }
                                $linea .= '</div>';
                                $linea .= '<div>';
								if ($cant_estudios > 0){
									while ($row_estudios = $this->db->fetch_array($query_estudios)){

										$linea .= "<div class='estudios'><small style='color:#000'>".$row_estudios['nombre_estudio']."</small></div>&nbsp;-&nbsp;";
									}
								}
                                
                                $linea .= "<small style='color:#000'>".$row["abreviacion"]."</small>";

                                $linea .= "<small style='color:#000'> - ".$row["telefonos"]."</small><br />";
								//Mostrar usuario y fecha/hora que registro el turno
								$usu_details = $row["uApellidos"].", ".$row['uNombres'];
								$linea .="
								<small style='color:#464646'><span title='". htmlentities($usu_details, 0, 'UTF-8') ."'>Otorgado: ".$row['uUsuario']."</span> - ".
									'<span>'.
										preg_replace('/\b\/\d{4}\b/', '',implode("/", array_reverse(explode("-", $row['fecha_alta'])))).
										' '.
										substr($row['hora_alta'], 0, 5).
									'</span>'.
								"</small>";
								if($row['id_usuarios_recepcion']){
									$usu_rec_details = $row["recApellidos"].", ".$row['recNombres'];
									//Mostrar usuario y fecha/hora que recepciono el paciente
									$linea .="
									<small style='color:#464646'><span title='". htmlentities($usu_rec_details, 0, 'UTF-8') ."'>Recepci&oacuten: ".$row['recUsuario']."</span> - ".
										'<span>'.
										preg_replace('/\b\/\d{4}\b/', '',implode("/", array_reverse(explode("-", $row['fecha_recepcion'])))).
											' '.
											substr($row['hora_recepcion'], 0, 5).
										'</span>'.
									"</small>";
								}
                                $linea .= '</div>';
								$linea .="</div></div></span>";
							break;
							case 'sam':
								//TRAIGO TODOS LOS TURNOS RESERVADOS
								$query_string_estudios = $this->querys->EstudiosTurnos($row["id_turnos"]);
								$query_estudios = $this->db->consulta($query_string_estudios);
								$cant_estudios = $this->db->num_rows($query_estudios);

								$linea = " <a href='#' style='color:#".$row["color"]."' class='btn_estado_turno".$clasm."' data-id='".$row["id_turnos"]."' data-id_turnos_tipos='".$row["id_turnos_tipos"]."' data-id_turnos_estados='".$row["id_turnos_estados"]."'>
									<div class='bloque'>
											<img src='".IMG."btns/tipo_".$tipo_turno.".png' /><span>".substr($row["desde"], 0, 5)." &raquo;</span>
											<div class='dat_paciente'>".
												upper(trim($row["apellidos"])). ", ".upper(trim($row["nombres"]))."
												(".$row["nombre_estado"].")";
                                if ($row['aviso_demora'] == '1') {
                                    $linea .= " <sup style=\"color:#c00;\">DEMORADO</sup>";
                                }
                                $linea .= "<br />
												<small style='color:#000'>".$row["abreviacion"]. " - ".$row["telefonos"]."</small>
											</div>";

								if ($cant_estudios > 0){
									while ($row_estudios = $this->db->fetch_array($query_estudios)){

										$linea .= "<br /><div class='estudios' style='color:#b0b0b0;'><small>".$row_estudios['nombre_estudio']."</small></div>";
									}
								}

								$linea .= "</div>
										</a>";
							break;


						}
                        $cnct = '';
                        while (isset($grillav[$row["desde"].$cnct])) {
                            $cnct.= '0';
                        }
						$grillav[$row["desde"].$cnct] = $linea;
					}
				}


				//TRAIGO LOS HORARIOS INHABILITADOS
				$query_string3 = $this->querys->HorariosInhabilitados($fecha, $id_medico, $id_especialidad);
				$query3 = $this->db->consulta($query_string3);
				$cant3 = $this->db->num_rows($query3);
				if ($cant3 > 0){
					while ($row3 = $this->db->fetch_array($query3)){
						$inhabilitadosv[] = $row3;
					}
				}

				//VOY A ARMAR EL LISTADO DE LIBRES
                while ($row2 = $this->db->fetch_array($query)){
				    //ES ESTUDIO O CONSULTA
					if(isset($row2["id_turnos_tipos"]) && (2 - ($row2["id_turnos_tipos"] % 2)) == 2){
						$tipo_turno = "estudios";
					}else{
						$tipo_turno	= "consultas";
					}

					$suma = $row2["desde"];
					$band = true; //BANDERA PARA INDICAR SI YA NO HAY TURNOS LIBRES

					while ($suma <= $row2["hasta"]){
						$inicio = $suma;
						$desdev[] = $suma;

						$suma = $this->SumarHorasTime($suma,$duracion_turno);

						$fin = $suma;
						$hastav[] = $suma;

						if (!$this->InMultiArray($inicio, $turnos_reservadosv, 'desde')){
							$es_inhabilitado = false;
							foreach ($inhabilitadosv as $clave => $valor){
								if (($inicio >= $valor['desde']) && ($inicio < $valor['hasta'])){
									$es_inhabilitado = true;
                                    if ($valor['motivo_descripcion'] != 'Otro') {
                                        $es_inhabilitado_mot = $valor['motivo_descripcion'];
                                    } else {
                                        $es_inhabilitado_mot = '';
                                    }
                                    $es_inhabilitado_txt = $valor['horarios_inhabilitados_motivos'];
								}
							}

							if ($es_inhabilitado){
								$linea = "<span class='c_rojo inhabilitado' data-desde='".$inicio."' data-hasta='".$fin."' data-fecha='".$fecha."' data-turnos_tipos='".$tipo_turno."'>
											<div class='bloque'>
												<img src='".IMG."btns/tipo_".$tipo_turno.".png' /><strong>".substr($inicio, 0, 5)." &raquo; Inhabilitado: ".$es_inhabilitado_mot." ".$es_inhabilitado_txt."</strong>
											</div>
										</span>";
							}else{
								switch ($_SESSION['SISTEMA']){
									case 'sas':
										$linea = "<span class='reservar libre' data-desde='".$inicio."' data-hasta='".$fin."' data-fecha='".$fecha."' data-turnos_tipos='".$tipo_turno."'>
											<div class='bloque'>
												<img src='".IMG."btns/tipo_".$tipo_turno.".png' />".substr($inicio, 0, 5)." &raquo; <strong>Libre</strong>
											</div>
										</span>";
									break;
									case 'sam':
										$linea = "<span href='#' class='reservar libre' data-desde='".$inicio."' data-hasta='".$fin."' data-fecha='".$fecha."' data-turnos_tipos='".$tipo_turno."'>
											<div class='bloque'>
												<img src='".IMG."btns/tipo_".$tipo_turno.".png' />".substr($inicio, 0, 5)." &raquo; <strong>Libre</strong>
											</div>
										</span>";
									break;

								}
							}

							$band = false;

							$grillav[$inicio] = $linea;
						}
						$htm->Asigna('MJE',"");
					}

				}

                $keysHorarios = array_keys($grillav);

                sort($keyTurnos);
                ksort($aTurnos);

                for ($i = 0; $i < count($aTurnos); $i++) {
                    $itmSTurno[$i] = $aTurnos[$keyTurnos[$i]]['hasta'];

                    while(in_array($itmSTurno[$i], $keysHorarios)):
                        if ($i + 1 < count($aTurnos)):
                            if ($this->SumarHorasTime($itmSTurno[$i], $duracion_turno) < $aTurnos[$keyTurnos[$i + 1]]['desde']):
                                $itmSTurno[$i] = $this->SumarHorasTime($itmSTurno[$i], $duracion_turno);
                            else:
                                $itmSTurno[$i] = null;
                            endif;
                        else:
                            $itmSTurno[$i] = $this->SumarHorasTime($itmSTurno[$i], $duracion_turno);
                        endif;
                    endwhile;

                    if ($itmSTurno[$i]):
                        $hor_normal = $itmSTurno[$i];
                        $hor_substr = substr($itmSTurno[$i], 0, 5);
                        $img = IMG;
                        if ($aTurnos[$keyTurnos[$i]]['id_turnos_tipos'] % 2 == 0) {
                            $aTurnos[$keyTurnos[$i]]['id_turnos_tipos'] = 2;
                        } else {
                            $aTurnos[$keyTurnos[$i]]['id_turnos_tipos'] = 1;
                        }
                        $grillav[$hor_normal] = <<<HTML
                            <span class='reservar sobreturno' data-desde='{$hor_normal}' data-hasta='{$hor_normal}' data-fecha='{$fecha}' data-turnos_tipos='{$aTurnos[$keyTurnos[$i]]['id_turnos_tipos']}'>
    							<div class='bloque'>
    								<img src='{$img}btns/tipo_{$tipo_turno}.png' />{$hor_substr} &raquo; <strong>ASIGNAR UN SOBRETURNO</strong>
    							</div>
    						</span>
HTML;
                    endif;
                }

				ksort($grillav);

				$listado = "";

				foreach ($grillav as $clave => $valor) {
					$listado .= $valor;
				}
                if (!empty($listado)){
                    $htm->Asigna('MJE',"");
                }else{
                    $htm->Asigna('MJE',"No hay turnos reservados para este dia.");
                }
			}else{
				$listado = '';
				$htm->Asigna('MJE',"Hoy no atiende este Profesional.");
			}
			$htm->Asigna("GRILLA_LISTADO", $listado);
		}else{
			//ARMA GRILLA DE TURNOS PASADOS
			$htm = $this->Html($this->nombre_tabla."/grilla_turnos_pasado");
			$query_string = $this->querys->GrillaTurnosPasados($id_medico, $id_especialidad, $fecha);
			$query = $this->db->consulta($query_string);
			$cant = $this->db->num_rows($query);
			if ($cant != 0){
				while ($row = $this->db->fetch_array($query)){
					$query_string_estudios = $this->querys->EstudiosTurnos($row["id_turnos"]);
					$query_estudios = $this->db->consulta($query_string_estudios);
					$cant_estudios = $this->db->num_rows($query_estudios);
				    $row["ESTUDIOS"] = "";
					if ($cant_estudios > 0){
						while ($row_estudios = $this->db->fetch_array($query_estudios)){
							$row["ESTUDIOS"] .= "&nbsp;-&nbsp;<div class='estudios'><small style='color:#b0b0b0'>".$row_estudios['nombre_estudio']."</small></div>";
						}
						$row["ESTUDIOS"] .= "<br />";
					}

					$row["PACIENTE"] = $row["apellidos"].", ".$row['nombres'];

					$usu_details = $row["uApellidos"].", ".$row['uNombres'];
					$recepcion ="
					<small style='color:#000'><span style='color:#b0b0b0;' title='". htmlentities($usu_details, 0, 'UTF-8') ."'>Otorgado: ".$row['uUsuario']."</span> - ".
						'<span style="color:#b0b0b0;">'.
							preg_replace('/\b\/\d{4}\b/', '',implode("/", array_reverse(explode("-", $row['fecha_alta'])))).
							' '.
							substr($row['hora_alta'], 0, 5).
						'</span>'.
					"</small>";
					if($row['id_usuarios_recepcion']){
						$usu_rec_details = $row["recApellidos"].", ".$row['recNombres'];
						//Mostrar usuario y fecha/hora que recepciono el paciente
						$recepcion .="
						<small style='color:#000'><span style='color:#b0b0b0;' title='". htmlentities($usu_rec_details, 0, 'UTF-8') ."'>Recepci&oacuten: ".$row['recUsuario']."</span> - ".
							'<span style="color:#b0b0b0;">'.
							preg_replace('/\b\/\d{4}\b/', '',implode("/", array_reverse(explode("-", $row['fecha_recepcion'])))).
								' '.
								substr($row['hora_recepcion'], 0, 5).
							'</span>'.
						"</small>";
					}
					$row["RECEPCION"] = $recepcion;
					$obj_turno_estado = new Turnos_estados($row["id_turnos_estados"]);
					$row["ESTADO"] = $obj_turno_estado->nombre;
                    $row["desde"] = substr($row["desde"], 0, 5);
                    $row["hasta"] = substr($row["hasta"], 0, 5);
					$htm->AsignaBloque('block_registros',$row);
				}
				$htm->Asigna('MJE',"");
			}else{
				$htm->AsignaBloque('block_registros',"");
				$htm->Asigna('MJE',"No se atendi&oacute; a ningun paciente");
			}
		}



		$drop_horarios_inhabilitado = $this->HorariosInhabilitadosPorFecha($id_medico, $id_especialidad, $fecha);

		$htm->Asigna("DROP_HORARIOS_INHABILITADOS", $drop_horarios_inhabilitado);

		$htm->Asigna("FECHA_CON_DIA", $obj_dia_semana->nombre.' '.$this->cambiaf_a_normal($fecha, "/"));

		$htm->Asigna("FECHA", $this->cambiaf_a_normal($fecha, "/"));

		$htm->Asigna("ID_MEDICO", $id_medico);
		$htm->Asigna("ID_ESPECIALIDAD", $id_especialidad);
        $htm->Asigna('CONSULTORIO', $consultorio['nro_consultorio']);
        $htm->Asigna('INTERNO', $consultorio['interno']);

		CargarVariablesGrales($htm, $tipo = "");

		$rta = $htm->Muestra();

		return  utf8_encode($rta);
	}

	function GrillaTurnosEstetica($id_medico, $id_especialidad, $id_dia, $fecha){
		$fechav = new DateTime($fecha);
		$fecha = $fechav->format('y-m-d');

		$fechav1 = explode('-',$fecha);
		$diaSemana = $this->diaSemana($fechav1[0], $fechav1[1], $fechav1[2]);

		requerir_class("dias_semana");
		$obj_dia_semana = new Dias_semana($diaSemana);

		if ($fecha >= date('y-m-d')){
			$htm = $this->Html($this->nombre_tabla."/grilla_turnos_".$_SESSION['SISTEMA']);

			//VERIFICO LOS HORARIOS QUE TIENE CARGADO EL MEDICO PARA UN DIA DETERMINADO
			$query_string = $this->querys->GrillaTurnos($id_medico, $id_especialidad, $id_dia);
			$query = $this->db->consulta($query_string);
			$cant = $this->db->num_rows($query);

			//PREGUNTO SI TIENE CARGADO HORARIOS PARA UN DIA DADO
			if ($cant > 0){

				//VERIFICO LA DURACION DEL TURNO
				requerir_class('medicos_especialidades');
				$obj_medico_especialidad = new Medicos_especialidades();
				$duracion_turno = $obj_medico_especialidad->DuracionTurno($id_medico, $id_especialidad);

				//TRAIGO TODOS LOS TURNOS RESERVADOS
				$query_string2 = $this->querys->TurnosReservados($fecha, $id_medico, $id_especialidad, $id_dia);
				$query2 = $this->db->consulta($query_string2);
				$cant2 = $this->db->num_rows($query2);

				$turnos_reservadosv = array();
				$turnos_libresv = array();
				$inhabilitadosv = array();
				$grillav = array();

				if ($cant2 > 0){
					$i = 1;
					while ($row = $this->db->fetch_array($query2)){


						//ES ESTUDIO O CONSULTA
						if(isset($row["id_turnos_tipos"]) && (2 - ($row["id_turnos_tipos"] % 2)) == 2){
							$tipo_turno = "estudios";
						}else{
							$tipo_turno	= "consultas";
						}


						//VOY CREANDO EL ARRAY CON LOS TURNOS RESERVADOS
						$turnos_reservadosv[] = $row;

						//ARMO EL LISTADO DE TURNOS RESERVADOS
						switch ($_SESSION['SISTEMA']){
							case 'sas':
								$linea = " <span style='color:#".$row["color"]."' class='btn_estado_turno' data-id='".$row["id_turnos"]."' data-id_turnos_tipos='".$row["id_turnos_tipos"]."' data-id_turnos_estados='".$row["id_turnos_estados"]."' data-tipo='turno'>
									<div class='bloque'>
										<img src='".IMG."btns/tipo_".$tipo_turno.".png' /><span>".$row["desde"].":</span>
										<div class='dat_paciente'>".
											$row["apellidos"]. ", ".$row["nombres"]."
											(".$row["nombre_estado"].")";
                                if ($row['aviso_demora'] == '1') {
                                    $linea .= " <sup style=\"color:#c00;\">DEMORADO</sup>";
                                }
                                $linea .= "<br />
											<small style='color:#000'>".$row["abreviacion"]. " - ".$row["telefonos"]."</small>
										</div>
									</div>
								</span>";
							break;
							case 'sam':
								//TRAIGO TODOS LOS TURNOS RESERVADOS
								$query_string_estudios = $this->querys->EstudiosTurnos($row["id_turnos"]);
								$query_estudios = $this->db->consulta($query_string_estudios);
								$cant_estudios = $this->db->num_rows($query_estudios);

								$linea = " <a href='#' style='color:#".$row["color"]."' class='btn_estado_turno' data-id='".$row["id_turnos"]."' data-id_turnos_tipos='".$row["id_turnos_tipos"]."' data-id_turnos_estados='".$row["id_turnos_estados"]."'>
									<div class='bloque'>
											<img src='".IMG."btns/tipo_".$tipo_turno.".png' /><span>".$row["desde"].":</span>
											<div class='dat_paciente'>".
												$row["apellidos"]. ", ".$row["nombres"]."
												(".$row["nombre_estado"].")";
                                if ($row['aviso_demora'] == '1') {
                                    $linea .= " <sup style=\"color:#c00;\">DEMORADO</sup>";
                                }
                                $linea .= "<br />
												<small style='color:#000'>".$row["abreviacion"]. " - ".$row["telefonos"]."</small>
											</div>";

								if ($cant_estudios > 0){
									while ($row_estudios = $this->db->fetch_array($query_estudios)){

										$linea .= "<br /><div class='estudios' style='color:#b0b0b0;'><small>".$row_estudios['nombre_estudio']."</small></div>";
									}
								}

								$linea .= "</div>
										</a>";
							break;


						}

						$grillav[$row["desde"].$i] = $linea;

						$i++;
					}
				}



				//TRAIGO LOS HORARIOS INHABILITADOS
				$query_string3 = $this->querys->HorariosInhabilitados($fecha, $id_medico, $id_especialidad);
				$query3 = $this->db->consulta($query_string3);
				$cant3 = $this->db->num_rows($query3);
				if ($cant3 > 0){
					while ($row3 = $this->db->fetch_array($query3)){
						$inhabilitadosv[] = $row3;
					}
				}

				//VOY A ARMAR EL LISTADO DE LIBRES
				while ($row2 = $this->db->fetch_array($query)){

					//ES ESTUDIO O CONSULTA
					if(isset($row2["id_turnos_tipos"]) && (2 - ($row2["id_turnos_tipos"] % 2)) == 2){
						$tipo_turno = "estudios";
					}else{
						$tipo_turno	= "consultas";
					}

					$suma = $row2["desde"];
					$band = true; //BANDERA PARA INDICAR SI YA NO HAY TURNOS LIBRES

					while ($suma <= $row2["hasta"]){
						$inicio = $suma;
						$desdev[] = $suma;

						$suma = $this->SumarHorasTime($suma,$duracion_turno);

						$fin = $suma;
						$hastav[] = $suma;

						$cantidad = $this->RepeticionTurnos($turnos_reservadosv, $inicio, 'reservados');

						if ($cantidad < 3){
						/////////////////
							$es_inhabilitado = false;
							foreach ($inhabilitadosv as $clave => $valor){
								if (($inicio >= $valor['desde']) && ($inicio < $valor['hasta'])){
									$es_inhabilitado = true;
                                    if ($valor['motivo_descripcion'] != 'Otro') {
                                        $es_inhabilitado_mot = $valor['motivo_descripcion'];
                                    } else {
                                        $es_inhabilitado_mot = '';
                                    }
                                    $es_inhabilitado_txt = $valor['horarios_inhabilitados_motivos'];
								}
							}

							if ($es_inhabilitado){
								$linea = "<span class='c_rojo inhabilitado' data-desde='".$inicio."' data-hasta='".$fin."' data-fecha='".$fecha."' data-turnos_tipos='".$tipo_turno."'>
											<div class='bloque'>
												<img src='".IMG."btns/tipo_".$tipo_turno.".png' /><strong>".substr($inicio, 0, 5)." &raquo; Inhabilitado: ".$es_inhabilitado_mot." ".$es_inhabilitado_txt."</strong>
											</div>

										</span>";
							}else{
								switch ($_SESSION['SISTEMA']){
									case 'sas':
										$linea_aux = "";
										for ($i =0; $i< 3 - $cantidad; $i++){
											$linea_aux .= "<span class='reservar libre' data-desde='".$inicio."' data-hasta='".$fin."' data-fecha='".$fecha."' data-turnos_tipos='".$tipo_turno."'>
												<div class='bloque'>
													<img src='".IMG."btns/tipo_".$tipo_turno.".png' />".substr($inicio, 0, 5)." &raquo; <strong>Libre</strong>
												</div>

											</span>";
										}

										$linea = $linea_aux;
									break;
									case 'sam':
										$linea = "<span href='#' class='reservar libre' data-desde='".$inicio."' data-hasta='".$fin."' data-fecha='".$fecha."' data-turnos_tipos='".$tipo_turno."'>
											<div class='bloque'>
												<img src='".IMG."btns/tipo_".$tipo_turno.".png' />".substr($inicio, 0, 5)." &raquo; <strong>Libre</strong>
											</div>

										</span>";
									break;

								}
							}

							$band = false;

							$cantidad_grilla = $this->RepeticionTurnos($grillav, $inicio, 'grilla');

							$grillav[$inicio.$cantidad_grilla] = $linea;
						}
						$htm->Asigna('MJE',"");

					}

				}

				ksort($grillav);

				$listado = "";

				foreach ($grillav as $clave => $valor) {
					$listado .= $valor;
				}

			}else{
				$listado = '';
				$htm->Asigna('MJE',"Hoy no atiende este Profesional.");
			}

			$htm->Asigna("GRILLA_LISTADO", $listado);
		}else{
			$htm = $this->Html($this->nombre_tabla."/grilla_turnos_pasado");
			$query_string = $this->querys->GrillaTurnosPasados($id_medico, $id_especialidad, $fecha);
			$query = $this->db->consulta($query_string);
			$cant = $this->db->num_rows($query);
			if ($cant != 0){
				while ($row = $this->db->fetch_array($query)){
					$row["PACIENTE"] =$row ["apellidos"].", ".$row['nombres'];

					$obj_turno_estado = new Turnos_estados($row["id_turnos_estados"]);
					$row["ESTADO"] = $obj_turno_estado->nombre;
					$htm->AsignaBloque('block_registros',$row);
				}
				$htm->Asigna('MJE',"");
			}else{
				$htm->AsignaBloque('block_registros',"");
				$htm->Asigna('MJE',"No se atendi&oacute; a ningun paciente");
			}
		}

		$drop_horarios_inhabilitado = $this->HorariosInhabilitadosPorFecha($id_medico, $id_especialidad, $fecha);

		$htm->Asigna("DROP_HORARIOS_INHABILITADOS", $drop_horarios_inhabilitado);

		$htm->Asigna("FECHA_CON_DIA", $obj_dia_semana->nombre.' '.$this->cambiaf_a_normal($fecha, "/"));

		$htm->Asigna("FECHA", $this->cambiaf_a_normal($fecha, "/"));

		$htm->Asigna("ID_MEDICO", $id_medico);
		$htm->Asigna("ID_ESPECIALIDAD", $id_especialidad);

		CargarVariablesGrales($htm, $tipo = "");

		$rta = $htm->Muestra();

		return  utf8_encode($rta);
	}

	function RepeticionTurnos($array, $desde, $tipo){
		$cant = 0;
		switch ($tipo){
			case "grilla":
				foreach ($array as $valor){
					if ($valor==$desde){
						$cant++;
					}
				}
			break;
			case "reservados":
				foreach ($array as $valor){
					if ($valor["desde"]==$desde){
						$cant++;
					}
				}
			break;
		}

		return $cant;
	}


	function InMultiArray($elem, $array,$field){
		$top = sizeof($array) - 1;
		$bottom = 0;
		while($bottom <= $top)
		{
			if($array[$bottom][$field] == $elem)
				return true;
			else
				if(is_array($array[$bottom][$field]))
					if(in_multiarray($elem, ($array[$bottom][$field])))
						return true;

			$bottom++;
		}
		return false;
	}

	function ContenedorGrilla($id_especialidad, $fecha, $tipo){

		$fechav = explode('/',$fecha);
		$diaSemana = $this->diaSemana($fechav[2], $fechav[1], $fechav[0]);
		/*requerir_class("dias_semana");
		$obj_dia_semana = new Dias_semana($diaSemana);
		echo $obj_dia_semana->nombre;*/

		$fecha = $this->cambiaf_a_mysql($fecha);

		$htm = $this->Html($this->nombre_tabla."/contenedor_grilla");

		$horariosv = $this->RangoTurnosDia($id_especialidad, $diaSemana);
        $anterior = '';
        for ($i = count($horariosv) - 2; $i >= 0; $i--) {
            $split = explode(" - ", $horariosv[$i]);
            if ($anterior != '') {
                $split[1] = date("H:i:s", strtotime ( '-1 minute' , strtotime($anterior)));
                $horariosv[$i] = implode(" - ", $split);
            }
            $anterior = $split[0];
        }
        $horariosv[count($horariosv) - 2] =
            substr($horariosv[count($horariosv) - 2], 0, 8).
            " - 23:59:00"
        ;

		$htm->Asigna('DROP_HORARIOS', $this->DropArmado("horarios",$horariosv));
		$htm->Asigna('MEDICO',$this->apellidos.", ".$this->nombres);
		$htm->Asigna('FECHA',$this->cambiaf_a_normal($fecha,'/'));
		$htm->Asigna('TIPO',$tipo);

		CargarVariablesGrales($htm, $tipo = "");
		return utf8_encode($htm->Muestra());
	}

	function GrillaTurnosImprimir($id_especialidad, $fecha, $horarios){

		$fecha = $this->cambiaf_a_mysql($fecha);

		$htm = $this->Html($this->nombre_tabla."/grilla_turnos_imprimir");
		$query_string = $this->querys->GrillaTurnosImprimir($this->id, $id_especialidad, $fecha, $horarios);
		$query = $this->db->consulta($query_string);
		$cant = $this->db->num_rows($query);

		//PARA EVITAR DUPLICADOS COMPARO CUANDO EL ATRIBUTO DESDE EES EL MISMO
		$desdev = array();
		$desdev[0] = 0;

		if ($cant != 0){
			$i = 1;
			while ($row = $this->db->fetch_array($query)){
				$desdev[$i] = $row["desde"];

				//echo ($i .' - '.$desdev[$i - 1]." - ".$desdev[$i]."<br/>");

				if ($desdev[$i - 1] != $desdev[$i]){
					$row["PACIENTE"] =$row ["apellidos"].", ".$row['nombres'];
					$row["OBRA_SOCIAL"] =$row["abreviacion"];
					$row["I"] =$i;



					$htm->AsignaBloque('block_registros',$row);

					$query_string2 = $this->querys->EstudiosTurnos($row['id_turnos']);
					$query2 = $this->db->consulta($query_string2);
					$cant2 = $this->db->num_rows($query2);
					if ($cant2 != 0){
						while ($row2 = $this->db->fetch_array($query2)){
							$htm->AsignaBloque('block_estudios_'.$row['id_turnos'],$row2);
						}
					}else{
						$htm->AsignaBloque('block_estudios_'.$row['id_turnos'],'');
					}
				}else{
					//$htm->AsignaBloque('block_estudios_'.$row['id_turnos'],'');
					//$htm->AsignaBloque('block_registros','');
				}
				$i++;

			}

			$htm->Asigna('MJE',"");
		}else{
			$htm->AsignaBloque('block_registros',"");
			$htm->Asigna('MJE',"No se atendi&oacute; a ningun paciente");
		}
		CargarVariablesGrales($htm, $tipo = "");
		return $htm->Muestra();
	}

	function RangoTurnosDia($id_especialidad, $dia_semana){
		$query_string = $this->querys->RangoTurnosDia($this->id, $id_especialidad, $dia_semana);
		$query = $this->db->consulta($query_string);
		$cant = $this->db->num_rows($query);
		if ($cant != 0){
			$horarios = array();
			$horarios["label_elija"] = "Elija horario del medico";
			while ($row = $this->db->fetch_array($query)){
				$horarios[] = $row["desde"] . " - " .$row["hasta"];
			}
		}
		return $horarios;
	}

	function GrillaCobrosImprimir($id_especialidad, $fecha, $horarios){

		$fecha = $this->cambiaf_a_mysql($fecha);

		$htm = $this->Html($this->nombre_tabla."/grilla_cobros_imprimir");
		$query_string = $this->querys->GrillaCobrosImprimir($this->id, $id_especialidad, $fecha, $horarios);
		$query = $this->db->consulta($query_string);
		$cant = $this->db->num_rows($query);

		$total = 0;
		$total_ordenes = 0;
		$total_pedidos = 0;

		if ($cant != 0){
			$i = 1;
			while ($row = $this->db->fetch_array($query)){
				$row["I"] =$i;
				$i++;

				switch ($row['trae_orden']) {
				    case '0':
    					$row['trae_orden'] = "SI";
                        break;
				    case '1':
				    case '2':
    					$row['trae_orden'] = "NO";
    					$total_ordenes += 1;
                        break;
				}

                switch ($row['trae_pedido']) {
                    case '0':
    					$row['trae_pedido'] = "NO";
                        break;
                    case '1':
    					$row['trae_pedido'] = "SI";
    					$total_pedidos += 1;
        				$row['arancel_diferenciado'] = $row['valor_pedido'];
        				$total += $row['arancel_diferenciado'];
                        break;
                    case '2':
    					$row['trae_pedido'] = "SI";
    					$total_pedidos += 1;
        				$total += $row['arancel_diferenciado'];
                        break;
                }

				$htm->AsignaBloque('block_registros',$row);

				$query_string2 = $this->querys->CobrosTurnos($row['id_turnos']);
				$query2 = $this->db->consulta($query_string2);
				$cant2 = $this->db->num_rows($query2);
				if ($cant2 != 0){
					while ($row2 = $this->db->fetch_array($query2)){
						$total += $row2['importe'];
						$htm->AsignaBloque('block_cobros_'.$row['id_turnos'],$row2);
					}
				}else{
					$htm->AsignaBloque('block_cobros_'.$row['id_turnos'],'');
				}
			}

			$htm->Asigna('CONTADOR_ORDENES',$total_ordenes);
			$htm->Asigna('CONTADOR_PEDIDOS',$total_pedidos);
			$htm->Asigna('MJE',"");
		}else{
			$htm->AsignaBloque('block_registros','');
			$htm->Asigna('CONTADOR_ORDENES',$total_ordenes);
			$htm->Asigna('CONTADOR_PEDIDOS',$total_pedidos);
			$htm->Asigna('MJE',"No se atendi&oacute; a ningun paciente");
		}
		$htm->Asigna('TOTAL',$total);
		$htm->Asigna('MEDICO',$this->apellidos.", ".$this->nombres);
		$htm->Asigna('FECHA',$this->cambiaf_a_normal($fecha,'/'));

		CargarVariablesGrales($htm, $tipo = "");
		return $htm->Muestra();
	}

	function Arancel($fecha,$i, $htm, $total){
		$query_string = $this->querys->Arancel($this->id, $fecha);
		$query = $this->db->consulta($query_string);
		$cant = $this->db->num_rows($query);
		if ($cant != 0){
			while ($row = $this->db->fetch_array($query)){
				$row["I"] =$i;
				$row["nombre_cobro_concepto"] = "ARANCEL DIFERENCIADO";
				$row["importe"] = $row["arancel_diferenciado"];
				$total += $row["importe"];
				$i++;

				$htm->AsignaBloque('block_registros2',$row);
			}
		}else{
			$htm->AsignaBloque('block_registros2',"");
		}
		$rta = array("htm"=>$htm, "total" => $total);
		return $rta;
	}

    function DiasTrabajo($id_especialidad, $id_medico, $fecha){

        $fechav = new DateTime($fecha);
        $htm = $this->Html($this->nombre_tabla."/agenda");

        $query_string = $this->querys->DiasTrabajo($this->id, $id_especialidad);
        $query = $this->db->consulta($query_string);
        $cant = $this->db->num_rows($query);

        $dias = "";
        if ($cant != 0){
            while ($row = $this->db->fetch_array($query)){
                $dia_jquery = $row["id_dias_semana"] - 1;
                $dias .= "day == ".$dia_jquery." || ";
            }
        }

        $init = date("Y-m-01", strtotime($fechav->format('y-m-d')));
        $end = date("Y-m-t", strtotime($fechav->format('y-m-d')));


        $query_string4 = $this->querys->TurnosReservadosEnMes($init, $end, $id_medico, $id_especialidad);
        $query4 = $this->db->consulta($query_string4);
        $cant4 = $this->db->num_rows($query4);

        $turnos_reservadosEnMes = 'false';
        if($cant4 > 0){
            $turnos_reservadosEnMes = '';
            while ($row = $this->db->fetch_array($query4)){
                $turnos_reservadosEnMes .= "fecha == '".$row['fecha']."' || ";
            }

        }

        $dias = trim($dias, ' || ');
        if ($turnos_reservadosEnMes != false){
            $turnos_reservadosEnMes = trim($turnos_reservadosEnMes, ' || ');
        }





        $htm->Asigna(
            "MEDICO",
            utf8_encode($this->saludo)." ".utf8_encode($this->apellidos).", ".utf8_encode($this->nombres)
        );

        $htm->Asigna("DIAS_TRABAJO", $dias);
        $htm->Asigna("FECHAS_TRABAJO", $turnos_reservadosEnMes);



        CargarVariablesGrales($htm);

        return $htm->Muestra();
    }

	function PanelAdmin(){
		$htm = $this->Html($this->nombre_tabla."/panel_admin_".$_SESSION['SISTEMA']);

		$htm->Asigna("LISTADO", $this->TablaAdmin());

		$htm->Asigna("TABLA", $this->nombre_tabla);
		$htm->Asigna("TITULO_TABLA", $this->titulo_tabla_singular);

		CargarVariablesGrales($htm, $tipo = "");

		echo ($htm->Muestra());

	}

	function ArancelConsulta($id_obra_social){
		$query_string = $this->querys->ArancelConsulta($this->id, $id_obra_social);
		$query = $this->db->consulta($query_string);
		$cant = $this->db->num_rows($query);

		$dias = "";
		if ($cant != 0){
			$row = $this->db->fetch_array($query);
			$rta = $row["arancel"];
		}else{
			$rta = "El medico no trabaja con la OS del Paciente.";
		}
		return $rta;
	}

	function ContadorOrdenesPedidos($id_medico, $fecha, $tipo){
		$query_string = $this->querys->ContadorOrdenesPedidos($id_medico, $fecha, $tipo);
		$query = $this->db->consulta($query_string);
		$rta = $this->db->num_rows($query);
		return $rta;
	}

	function HorariosInhabilitados($id_medico, $id_especialidad, $fecha){
		$htm = $this->Html("drop");
		$query_string = $this->querys->DropHorariosInhabilitados($id_medico, $id_especialidad, $fecha);
		$query = $this->db->consulta($query_string);
		$cant = $this->db->num_rows($query);
		if ($cant != 0){
			$htm->Asigna('NOMBRE_SELECT', 'horarios_inhabilitados');
			$htm->Asigna('LABEL_ELIJA', 'Seleccione Horario');
			while ($row = $this->db->fetch_array($query)){
                if ($row['motivo_descripcion'] != 'Otro') {
    				$row['TEXTO_OPTION'] = $row['desde']." - ".$row['hasta']." | ".$row['motivo_descripcion'];
                } else {
    				$row['TEXTO_OPTION'] = $row['desde']." - ".$row['hasta']." | ".$row['motivo_descripcion'].': '.$row['horarios_inhabilitados_motivos'];
                }
				$row['VALUE'] = $row['id_horarios_inhabilitados'];
				$htm->AsignaBloque('block_option',$row);
			}
			$rta = $htm->Muestra();
		}else{
			$rta = "Sin Horarios Inhabilitados";
		}
		return $rta;

	}


	function HorariosInhabilitadosPorFecha($id_medico, $id_especialidad){
		$htm = $this->Html("drop_uno");
		$query_string = $this->querys->DropHorariosInhabilitadosPorFecha($id_medico, $id_especialidad);
		$query = $this->db->consulta($query_string);
		$cant = $this->db->num_rows($query);
		if ($cant != 0){
			$htm->Asigna('NOMBRE_SELECT', 'horarios_inhabilitados');
			$htm->Asigna('LABEL_ELIJA', 'Seleccione Horario');
			$htm->Asigna('MULTIPLE', 'multiple');
			$htm->Asigna('SIZE', 'size="'.max(4, min(10, $cant)).'" style="width:100%;"');
			while ($row = $this->db->fetch_array($query)){
                $desde = $row['desde'];
                if (substr($desde, -3, 3) == ':00') {
                    $desde = substr($desde, 0, strlen($desde) -3);
                }
                if (substr($desde, -3, 3) == ':00') {
                    $desde = substr($desde, 0, strlen($desde) -3);
                }
                $hasta = $row['hasta'];
                if (substr($hasta, -3, 3) == ':00') {
                    $hasta = substr($hasta, 0, strlen($hasta) -3);
                }
                if (substr($hasta, -3, 3) == ':00') {
                    $hasta = substr($hasta, 0, strlen($hasta) -3);
                }
				$row['TEXTO_OPTION'] =
                    date("d/m", strtotime($row['fecha'])).
                    " | ".
                    $desde.
                    "-".
                    $hasta.
                    " | ".
                    $row['motivo_descripcion']
                ;
                if ($row['motivo_descripcion'] == 'Otro') {
    				$row['TEXTO_OPTION'].= ': '.$row['horarios_inhabilitados_motivos'];
                }
				$row['VALUE'] = $row['id_horarios_inhabilitados'];
        	    if (
                    $_SESSION['SISTEMA'] == 'sas' and
                    $_SESSION['SUPERUSER'] < 2 and
                    $row['bloqueo_superadmin'] == '1'
                ) {
    				$row['SELECTED'] = ' disabled="disabled"';
     	        } else {
    				$row['SELECTED'] = '';
     	        }
				$htm->AsignaBloque('block_option',$row);
			}
			$rta = $htm->Muestra();
		}else{
			$rta = "Sin Horarios Inhabilitados";
		}
		return $rta;

	}

    function upper($str)
    {
        $arrAcentos = array('á', 'é', 'í', 'ó', 'ú', 'ñ', 'ü');
        $arrReemplz = array('Á', 'É', 'Í', 'Ó', 'Ú', 'Ñ', 'Ü');
        $str = str_replace($arrAcentos, $arrReemplz, $str);
        return strtoupper($str);
    }

    function lower($str)
    {
        $arrAcentos = array('Á', 'É', 'Í', 'Ó', 'Ú', 'Ñ', 'Ü');
        $arrReemplz = array('á', 'é', 'í', 'ó', 'ú', 'ñ', 'ü');
        $str = str_replace($arrAcentos, $arrReemplz, $str);
        return strtolower($str);
    }

    function doSaludo($rsMedico, $prefix = true) {
        $str = "";
        if ($prefix) {
            switch ($this->lower($rsMedico['saludo'])) {
                case "dr.":
                    $str.= "el ";
                    break;
                case "dra.":
                    $str.= "la ";
                    break;
            }
        }
        $str.= ucwords($this->lower(trim($rsMedico['saludo'])));
        $str.= " ";
        $str.= $this->upper(trim($rsMedico['apellidos']));
        $str.= ", ";
        $str.= ucwords($this->lower(trim($rsMedico['nombres'])));
        return $str;
    }

    function horaMM($hora, $masm) {
        $aHora = explode(":", $hora);
        $aMasM = explode(":", $masm);
        $aSuma = array(
            $aHora[0] + $aMasM[0] + floor(($aHora[1] + $aMasM[1]) / 60),
            ($aHora[1] + $aMasM[1]) % 60,
            '00'
        );
        if ($aSuma[0] < 10) $aSuma[0] = '0'.$aSuma[0];
        if ($aSuma[1] < 10) $aSuma[1] = '0'.$aSuma[1];
        return implode(":", $aSuma);
    }

	function Consultorio($id_medicos){
		$htm = $this->Html("consultorios/form_detalle");

		$htm->Asigna("TABLA",$this->nombre_tabla);

		$query_string = $this->querys->DetalleMedicosConsultorios($id_medicos);
		$query = $this->db->consulta($query_string);

		$addRows = "";
        $cnct = "";
        $arrRows = array();
        while ($row = $this->db->fetch_array($query)) {
            $arrRows[] = $row;
            $desdeH = substr($row['desde'], 0, 2);
            $desdeM = substr($row['desde'], 3, 2);
            $hastaH = substr($this->horaMM($row['hasta'], $row['duracion_turno']), 0, 2);
            $hastaM = substr($this->horaMM($row['hasta'], $row['duracion_turno']), 3, 2);
            $addRows.= "{$cnct}['{$row['nombre']}', 'Consultorio {$row['nro_consultorio']} - {$row['especialidad']}', new Date(0,0,0,{$desdeH},{$desdeM},0), new Date(0,0,0,{$hastaH},{$hastaM},0)]";
            $cnct = ",\n";
        }

        if (!trim($addRows)) {
            $addRows = "]);}</script>No se encontraron datos.<script>nul=([";
        }
        $htm->Asigna("ADDROWS", utf8_encode($addRows));

        $dias = array('LUNES', 'MARTES', 'MIÉRCOLES', 'JUEVES', 'VIERNES');
        $addTableRows = "";
        $arrEspecialidades = array();
        for (
            $hora = '07:00:00';
            $hora <= '22:00:00';
            $hora = $this->SumarHorasTime($hora, '00:30:00')
        ) {
            $addTableRows.= "<tr><td>".substr($hora, 0, 5)."hs</td>";
            for ($j = 0; $j < 5; $j++) {
                $data = null;
                for ($k = 0; $k < count($arrRows); $k++) {
                    if (
                        $arrRows[$k]['nombre'] == utf8_decode($dias[$j]) and
                        $arrRows[$k]['desde'] <= $hora and
                        $this->SumarHorasTime($arrRows[$k]['hasta'], $arrRows[$k]['duracion_turno']) >= $hora
                    ) {
                        $data = $arrRows[$k];
                        if (!isset($arrEspecialidades[$data['especialidad']])) {
                            $arrEspecialidades[$data['especialidad']] = count($arrEspecialidades) + 1;
                        }
                    }
                }
                if ($data) {
                    $addTableRows.=
                        '<td class="ocu col'.
                        $arrEspecialidades[$data['especialidad']].
                        '">'.
                        $data['especialidad'].
                        '</td>'
                    ;
                } else {
                    $addTableRows.= '<td class="lib"></td>';
                }
            }
            $addTableRows.= "</tr>";
        }

        $htm->Asigna("ADDTABLEROWS", utf8_encode($addTableRows));

		CargarVariablesGrales($htm, $tipo = "");

		return ($htm->Muestra());
	}

	function EstadisticasSAS($ses_id_medico, $desde, $hasta){
		$estadistica = array();
		//GRAPH 1
		$sql = "
			SELECT
				DATE_FORMAT(t.fecha, '%d/%m') AS myfecha,
				COUNT(t.id_turnos) AS total
			FROM
				turnos AS t
			WHERE
				t.id_medicos = '{$ses_id_medico}' AND
				t.fecha BETWEEN '{$desde}' AND '{$hasta}'
			GROUP BY
				t.fecha
		";
		$query = $this->db->consulta($sql);

		$tEstadistica = array();
		while ($row = $this->db->fetch_array($query)){
			array_push($tEstadistica, array($row['myfecha'],(int)$row['total'],'#007FA6'));
		}
		array_push($estadistica,$tEstadistica);

		//GRAPH 2
		$sql = "
			SELECT
				o.abreviacion,
				COUNT(t.id_turnos) AS total
			FROM
				turnos AS t
			INNER JOIN
				pacientes AS p
				ON t.id_pacientes = p.id_pacientes
			INNER JOIN
				obras_sociales AS o
				ON p.id_obras_sociales = o.id_obras_sociales
			WHERE
				t.id_medicos = '{$ses_id_medico}' AND
				t.fecha BETWEEN '{$desde}' AND '{$hasta}'
			GROUP BY
				p.id_obras_sociales
			ORDER BY
				total DESC
		";
		$query = $this->db->consulta($sql);
		$tEstadistica = array();
		while ($row = $this->db->fetch_array($query)){
			array_push($tEstadistica, array(utf8_encode($row['abreviacion']),(int)$row['total'],'#007FA6'));
		}
		array_push($estadistica,$tEstadistica);

		//GRAPH 3
		$sql = "
			SELECT
				e.nombre,
				COUNT(t.id_turnos) AS total
			FROM
				turnos AS t
			INNER JOIN
				turnos_estados AS e
				ON e.id_turnos_estados = t.id_turnos_estados
			WHERE
				t.id_medicos = '{$ses_id_medico}' AND
				t.fecha BETWEEN '{$desde}' AND '{$hasta}'
			GROUP BY
				t.id_turnos_estados
			ORDER BY
				total DESC
		";
		$query = $this->db->consulta($sql);
		$tEstadistica = array();
		while ($row = $this->db->fetch_array($query)){
			array_push($tEstadistica, array(utf8_encode($row['nombre']),(int)$row['total'],'#007FA6'));

		}
		array_push($estadistica,$tEstadistica);

		// graph 4
		$sql = "
			SELECT
				e.nombre,
				COUNT(t.id_turnos) AS total
			FROM
				turnos AS t
			INNER JOIN
				turnos_estudios AS r
				ON r.id_turnos = t.id_turnos
			INNER JOIN
				estudios AS e
				ON r.id_estudios = e.id_estudios
			WHERE
				t.id_medicos = '{$ses_id_medico}' AND
				t.fecha BETWEEN '{$desde}' AND '{$hasta}'
			GROUP BY
				r.id_estudios
			ORDER BY
				total DESC
		";
		$query = $this->db->consulta($sql);
		$tEstadistica = array();
		while ($row = $this->db->fetch_array($query)){
			array_push($tEstadistica, array(utf8_encode($row['nombre']),(int)$row['total'],'#007FA6'));
		}
		array_push($estadistica,$tEstadistica);

		// graph 5
		$sql = "
			SELECT
				u.nombres,
				u.apellidos,
				COUNT(t.id_turnos) AS total
			FROM
				turnos AS t
			INNER JOIN usuarios AS u
				ON t.id_usuarios = u.id_usuarios
			WHERE
				t.id_medicos = '{$ses_id_medico}' AND
				t.fecha BETWEEN '{$desde}' AND '{$hasta}'
			GROUP BY
				t.id_usuarios
			ORDER BY
				COUNT(t.id_turnos) DESC
		";
		$query = $this->db->consulta($sql);
		$tEstadistica = array();
		while ($row = $this->db->fetch_array($query)){
			array_push($tEstadistica, array(utf8_encode($row['apellidos']),(int)$row['total'],'#007FA6'));
		}
		array_push($estadistica,$tEstadistica);

		return $estadistica;
	}
}

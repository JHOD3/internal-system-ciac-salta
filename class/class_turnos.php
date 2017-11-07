<?php
interface iTurnos{

}

class Turnos extends Estructura implements iTurnos{

	function __construct($id = ""){
		$this->nombre_tabla = "turnos";
		$this->titulo_tabla = "Turnos";
		$this->tabla_padre = "";

		$this->drop_label_elija = "Elija un Turno";

		parent::__construct($id);

		requerir_class("medicos", "obras_sociales", "obras_sociales", "turnos_estados");
	}

	function FormAlta(){
		$htm_form = $this->Html($this->nombre_tabla."/form_alta");

		return ($htm_form->Muestra());
	}

	function PanelGral($sistema = 'sas'){
		requerir_class("turnos_estudios", "obras_sociales_planes", 'medicos_obras_sociales');

		switch ($sistema){
			case "sas":
				$htm = $this->Html($this->nombre_tabla."/panel_gral");
			break;
			case "sam":
				$htm = $this->Html($this->nombre_tabla."/panel_gral_sam");
			break;
		}



		$row = $this->vector;

		$obj_medico = new Medicos($row["id_medicos"]);
		$row["MEDICO"] =
            '<strong style="color:#007FA6">'.
            $obj_medico->apellidos.
            ", ".
            $obj_medico->nombres.
            "</strong>"
        ;

		$obj_paciente = new Pacientes($row["id_pacientes"]);
		$row["PACIENTE"] =
            '<strong style="color:#008A47">'.
            strtoupper(trim($obj_paciente->apellidos)).
            ", ".
            strtoupper(trim($obj_paciente->nombres)).
            "</strong><br />"
        ;
        $row["PACIENTE"].=
            '<span style="font-size:0.9em;"><strong>DNI:</strong> '.
            number_format(trim($obj_paciente->nro_documento), 0, ",", ".").
            "</span><br />"
        ;
        if (trim($obj_paciente->telefonos)) {
            $row["PACIENTE"].=
                '<span style="font-size:0.9em;"><strong>Tel&eacute;f.:</strong> '.
                trim($obj_paciente->telefonos).
                "</span><br />"
            ;
        }
        if (trim($obj_paciente->domicilio)) {
            $row["PACIENTE"].=
                '<span style="font-size:0.9em;"><strong>Domic.:</strong> '.
                trim($obj_paciente->domicilio).
                "</span><br />"
            ;
        }
        if (trim($obj_paciente->email)) {
            $row["PACIENTE"].=
                '<span style="font-size:0.9em;"><strong>Email:</strong> '.
                trim($obj_paciente->email).
                "</span>"
            ;
        }

		$obj_obra_social = new Obras_sociales($obj_paciente->id_obras_sociales);
		$row["OBRA_SOCIAL"] =
            '<strong style="color:#007FA6;">'.
            $obj_obra_social->nombre.
            "</strong>"
        ;

		$obj_obra_social_plan = new Obras_sociales_planes($obj_paciente->id_obras_sociales_planes);
		$row["PLAN"] =
            '<strong style="color:#008A47;">'.
            $obj_obra_social_plan->nombre.
            "</strong>"
        ;



		switch($row["id_turnos_tipos"]){
			case 1://CONSULTAS
				$row["BTN_VER_ESTUDIO"] = "<a id='btn_estudios_asociados' data-id_turno='".$row["id_turnos"]."' data-id_obra_social='".$obj_paciente->id_obras_sociales."' data-id_medico='".$row["id_medicos"]."' class='btn' href='#'>Ver Estudios Asociados</a>";

				//$row["BTN_VER_ESTUDIO"] = "";

				$row["CLASS_CONSULTA"] = "";
				$row["CLASS_ESTUDIO"] = "oculto";

				$arancel_os = $obj_medico->ArancelConsulta($obj_paciente->id_obras_sociales);
				$particular_consulta = '0'; //$obj_medico->particular_consulta;

				$row['IMPORTE_ARANCEL_OS'] = $arancel_os;
				$row['IMPORTE_CONSULTA_PARTICULAR'] =  $particular_consulta;

				//VERIFICO SI EL PACIENTE TIENE OBRA SOCIAL O ES PARTICULAR
				//SI EL PACIENTE TIENE OS VERIFICO QUE EL MEDICO ATIENDA ESA OS...
				if ($obj_obra_social->nombre != "PARTICULAR"){
					$obj_medicos_obras_sociales = new Medicos_obras_sociales();
					$atiende_os = $obj_medicos_obras_sociales->Atiende($row['id_medicos'], $obj_paciente->id_obras_sociales, $obj_paciente->id_obras_sociales_planes);
					if ($atiende_os){
						$htm->Asigna("CHK_ARANCEL","checked");
						$htm->Asigna("CHK_PARTICULAR","");
						$valor_consulta = $arancel_os;
					}else{
						$htm->Asigna("CHK_ARANCEL","");
						$htm->Asigna("CHK_PARTICULAR","checked");
						$valor_consulta = $particular_consulta;
					}
				}else{
					$htm->Asigna("CHK_ARANCEL","");
					$htm->Asigna("CHK_PARTICULAR","checked");
					$valor_consulta = $particular_consulta;
				}
				$htm->Asigna("IMPORTE_CONSULTA",$valor_consulta);
			break;
			case 2://ESTUDIOS
				$row["BTN_VER_ESTUDIO"] = "<a id='btn_estudios_asociados' data-id_turno='".$row["id_turnos"]."' data-id_obra_social='".$obj_paciente->id_obras_sociales."' data-id_medico='".$row["id_medicos"]."' class='btn' href='#'>Ver Estudios Asociados</a>";
				$row["CLASS_CONSULTA"] = "oculto";
				$row["CLASS_ESTUDIO"] = "";

				$obj_turnos_estudios = new Turnos_estudios();
				$totalesv =  $obj_turnos_estudios->Totales($row['id_turnos'], $obj_paciente->id_obras_sociales);

				if ($totalesv['PARTICULAR'] == '')
					$totalesv['PARTICULAR'] = 0;

				$row['IMPORTE_ESTUDIOS_PARTICULAR'] =  $totalesv['PARTICULAR'];

				$row['VALOR_PARTICULAR_ESTUDIO'] = $obj_medico->particular_estudios;
			break;
		}

		if ($row['id_turnos_estados'] == 1){
			$btn_imprimir = "<a href='#' class='imprimir btn' data-tipo='turno' data-id='".$row["id_turnos"]."'>Imprimir Turno <img src='".IMG."btns/imprimir.png' width='20' /></a>";
		}else{
			$btn_imprimir = "";
		}
		$htm->Asigna('BTN_IMPRIMIR', $btn_imprimir);

		/*$obj_obra_social = new Obras_sociales($row["id_obras_sociales"]);
		$row["OBRA_SOCIAL"] =  $obj_obra_social->nombre;*/


		switch ($sistema){
			case "sas":
				$obj_turnos_estados = new Turnos_estados();
				$row["DROP_TURNOS_ESTADOS"] =  $obj_turnos_estados->Drop("",$row["id_turnos_estados"], null, null, null, null, 8);
                $row["DROP_TURNOS_ESTADOS"] = str_replace(
                    '<option value="">Elija un Estado de Turno</option>',
                    '',
                    $row["DROP_TURNOS_ESTADOS"]
                );
			break;
			case "sam":
				//$datos = array('label_elija' => 'Elija un Estado de Turno', '2' => 'LLEGO EL PACIENTE', '4' => 'CANCELADO POR EL MEDICO', '7' => 'YA ATENDIDO');
				$datos = array('label_elija' => 'Elija un Estado de Turno', '2' => 'LLEGO EL PACIENTE', '7' => 'YA ATENDIDO');
				$obj_turnos_estados = new Turnos_estados();
				$row["DROP_TURNOS_ESTADOS"] =  $obj_turnos_estados->DropArmado('turnos_estados',$datos, $row['id_turnos_estados']);
			break;
		}


		$htm->AsignaBloque('block_registros',$row);

		CargarVariablesGrales($htm);

		return utf8_encode($htm->Muestra());
	}

	function Detalle($tipo){
		$htm = $this->Html($this->nombre_tabla."/detalle_".$tipo);
		$row = $this->registro;

		requerir_class('pacientes', 'medicos', 'plantas');

		$obj_paciente = new Pacientes($row['id_pacientes']);
		$row['PACIENTE'] = $obj_paciente->apellidos.', '.$obj_paciente->nombres;

        $dia_nro = date('w', strtotime($row['fecha'])) + 1;
        if ($dia_nro == 8) {
            $dia_nro = 1;
        }

		$query_string = <<<SQL
            SELECT
                m.id_plantas AS id_plantas_1,
                mh.id_plantas AS id_plantas_2
            FROM
                turnos AS t
            INNER JOIN
                medicos AS m
                ON t.id_medicos = m.id_medicos
            INNER JOIN
                medicos_horarios AS mh
                ON
                    mh.id_medicos = m.id_medicos AND
                    mh.id_especialidades = t.id_especialidades
            WHERE
                t.id_turnos = '{$row['id_turnos']}' AND
                mh.id_dias_semana = '{$dia_nro}' AND
                t.desde BETWEEN mh.desde AND mh.hasta
            LIMIT 1
SQL;
		$query = $this->db->consulta($query_string);
        $row['PLANTA'] = '';
		while($plantas = $this->db->fetch_array($query)) {
            #var_dump($plantas);
            if (isset($plantas['id_plantas_2']) and $plantas['id_plantas_2']) {
                $id_plantas = $plantas['id_plantas_2'];
            } else {
                $id_plantas = $plantas['id_plantas_1'];
            }
            if ($id_plantas > 0) {
                $obj_planta = new Plantas($id_plantas);
                $row['PLANTA'].= <<<HTML
 / {$obj_planta->nombre}
HTML;
            } else {
    		  $row['PLANTA'].= '';
            }
        }

		$row['SECRETARIA'] = $_SESSION['APELLIDOS'].', '.$_SESSION['NOMBRES'];

		$row['FECHA'] = $this->cambiaf_a_normal($row['fecha'], '/');


		$obj_medico = new Medicos($row['id_medicos']);
		$row['MEDICO'] = $obj_medico->apellidos.', '.$obj_medico->nombres;

        $row['desde'] = substr($row['desde'], 0, 5);

		$htm->AsignaBloque('block_registros',$row);

		CargarVariablesGrales($htm);

		$rta = $htm->Muestra();

		return utf8_encode($rta);
	}

	function ExisteTurno($fecha, $inicio, $fin, $id_medico, $id_especialidad, $dia, $tipo_turno){
		$query_string = $this->querys->ExisteTurno($fecha, $inicio, $fin, $id_medico, $id_especialidad);
		$query = $this->db->consulta($query_string);

		$cant = $this->db->num_rows($query);

		if ($cant != 0){
			$turnov = $this->db->fetch_array($query);

			switch ($_SESSION['SISTEMA']){
				case 'sas':
					$rta = " <span style='color:#".$turnov["color"]."' class='btn_estado_turno' data-id='".$turnov["id_turnos"]."' data-id_turnos_tipos='".$turnov["id_turnos_tipos"]."' data-id_turnos_estados='".$turnov["id_turnos_estados"]."' data-tipo='turno'>
						<div class='bloque'>
								<img src='".IMG."btns/tipo_".$tipo_turno.".png' /><span>".$inicio.":</span>
								<div class='dat_paciente'>".
									$turnov["apellidos"]. ", ".$turnov["nombres"]."
									(".$turnov["nombre_estado"].")<br />
									<small style='color:#000'>".$turnov["nombre_os"]. " - ".$turnov["telefonos"]."</small>
								</div>";
					$rta .= "</div>
					</span>";
				break;
				case 'sam':
					$rta = " <a href='#' style='color:#".$turnov["color"]."' class='btn_estado_turno' data-id='".$turnov["id_turnos"]."' data-id_turnos_tipos='".$turnov["id_turnos_tipos"]."' data-id_turnos_estados='".$turnov["id_turnos_estados"]."'>
						<div class='bloque'>
								<img src='".IMG."btns/tipo_".$tipo_turno.".png' /><span>".$inicio.":</span>
								<div class='dat_paciente'>".
									$turnov["apellidos"]. ", ".$turnov["nombres"]."
									(".$turnov["nombre_estado"].")<br />
									<small style='color:#000'>".$turnov["nombre_os"]. " - ".$turnov["telefonos"]."</small>
								</div>";
					$rta .= "</div>
					</a>";
				break;
			}

		}else{
			$rta =  $this->HorariosInhabilitados($fecha, $inicio, $fin, $id_medico, $id_especialidad, $dia, $tipo_turno);
		}

		return $rta;
	}

	function ExisteTurnoReservado($fecha, $desde, $hasta, $id_medico, $id_especialidad){
		$query_string = $this->querys->ExisteTurnoReservado($fecha, $desde, $hasta, $id_medico, $id_especialidad);
		$query = $this->db->consulta($query_string);

		$cant = $this->db->num_rows($query);

		if ($cant != 0){
			$rta = true;
		}else{
			$rta = false;
		}
		return $rta;
	}

	function HorariosInhabilitados($fecha, $inicio, $fin, $id_medico, $id_especialidad, $dia, $tipo_turno){
		$query_string = $this->querys->HorariosInhabilitados($fecha, $inicio, $fin, $id_medico, $id_especialidad);
		$query = $this->db->consulta($query_string);

		$cant = $this->db->num_rows($query);

		if ($cant != 0){
			//$turnov = $this->db->fetch_array($query);
			$rta = $rta = "<a href='#' class='c_rojo' data-desde='".$inicio."' data-hasta='".$fin."' data-fecha='".$fecha."' data-turnos_tipos='".$tipo_turno."'>
						<div class='bloque'>
							<img src='".IMG."btns/tipo_".$tipo_turno.".png' /><strong>".$inicio.": No disponible - Inhabilitado</strong>
						</div>

					</a>";
		}else{
			$query_string = $this->querys->TipoHorario($dia, $inicio, $fin, $id_medico, $id_especialidad);
			$query = $this->db->consulta($query_string);

			$row = $this->db->fetch_array($query);



			switch ($_SESSION['SISTEMA']){
				case 'sas':
					$rta = "<span class='reservar libre' data-desde='".$inicio."' data-hasta='".$fin."' data-fecha='".$fecha."' data-turnos_tipos='".$tipo_turno."'>
						<div class='bloque'>
							<img src='".IMG."btns/tipo_".$tipo_turno.".png' />".$inicio.": <strong>Libre</strong>
						</div>

					</span>";
				break;
				case 'sam':
					$rta = "<span href='#' class='reservar libre' data-desde='".$inicio."' data-hasta='".$fin."' data-fecha='".$fecha."' data-turnos_tipos='".$tipo_turno."'>
						<div class='bloque'>
							<img src='".IMG."btns/tipo_".$tipo_turno.".png' />".$inicio.": <strong>Libre</strong>
						</div>

					</span>";
				break;
			}

		}

		return $rta;
	}

	function CambiarEstado($id, $id_estado_nuevo, $id_estado_actual){
		//VERIFICO SI CAMBIO O NO EL ESTADO... SOLO HAGO CUANDO CAMBIA ESTADO
		if ($id_estado_actual != $id_estado_nuevo){
			$query_string = $this->querys->CambiarEstado($this->nombre_tabla, $id, $id_estado_nuevo);
			if ($this->db->consulta($query_string))
				$rta = true;
			else
				$rta = false;
		}
		//GUARDAR CAMBIO DE ESTADO...
	}

	function TablaAdmin($id_padre = ""){
		$tabla = $this->html($this->nombre_tabla."/a_tabla");

		requerir_class('pacientes');

		if ($id_padre != ""){
			$tabla->Asigna("ARGS","tabla=".$this->nombre_tabla."&id=".$id_padre);

			$obj_paciente = new Pacientes($id_padre);
			$tabla->Asigna("DATOS_PACIENTE",$obj_paciente->Detalle("tabla"));
		}else{
			$tabla->Asigna("ARGS","tabla=".$this->nombre_tabla);
		}

		$tabla->Asigna("NOMBRE_TABLA",$this->nombre_tabla);



		$rta = utf8_encode($tabla->Muestra());

		return $rta;
	}

	function PanelAdmin($id_padre = ""){
		$htm = $this->Html($this->nombre_tabla."/panel_admin");

		$htm->Asigna("LISTADO", $this->TablaAdmin($id_padre));

		$htm->Asigna("ID_MEDICO", $id_padre);

		$htm->Asigna("TABLA", $this->nombre_tabla);
		$htm->Asigna("TITULO_TABLA", $this->titulo_tabla_singular);

		CargarVariablesGrales($htm, $tipo = "");

		echo ($htm->Muestra());
	}

	function ActualizarTipo($id_turno_tipo){
		$query_string = $this->querys->TodosRegistros('turnos_estudios',"DESC");
		$query = $this->db->consulta($query_string);

		$cant = $this->db->num_rows($query);

		if ($cant != 0){
			while ($row = $this->db->fetch_array($query)){
				$query_string2 = $this->querys->ActualizarTipo($row['id_turnos'], $id_turno_tipo);
				$query2 = $this->db->consulta($query_string2);
			}
		}

	}

	function OrdenesyPedidos($trae_orden, $trae_pedido, $arancel_diferenciado){
		$query_string = $this->querys->OrdenesyPedidos($this->id, $trae_orden, $trae_pedido, $arancel_diferenciado);
		if ($query_string != false){
			if ($this->db->consulta($query_string))
				$rta = true;
			else
				$rta = false;
		}else{
			$rta = false;
		}

	}

	function RestablecerOrdenesyPedidos(){
		$query_string = $this->querys->RestablecerOrdenesyPedidos($this->id_turnos);
		$query = $this->db->consulta($query_string);
		if ($this->db->consulta($query_string))
			$rta = true;
		else
			$rta = false;
		return $rta;
	}

	function EstudiosTurnos(){
		$htm = $this->Html($this->nombre_tabla."/grilla_estudios");
		$query_string = $this->querys->EstudiosTurnos($this->id);
		$query = $this->db->consulta($query_string);

		$cant = $this->db->num_rows($query);

		$i = 1;
		if ($cant != 0){
			while ($row = $this->db->fetch_array($query)){
				$row["I"] = $i;
                $row["nombre_estudio"] = utf8_encode($row["nombre_estudio"]);
                if (strlen($row["requisitos"]) < 2) {
                    $row["requisitos"] = '';
                } else {
                    $row["requisitos"] = "<br />".utf8_encode($row["requisitos"]);
                }
				$htm->AsignaBloque('block_registros',$row);
				$i += 1;
			}
			$rta = $htm->Muestra();
		}else{
			$rta = '<p>NO tiene estudios asociados</p>';
		}

		return $rta ;
	}

	function Duplicados($id_medico, $id_especialidad, $fecha){
		//$htm = $this->Html($this->nombre_tabla."/duplicados");
		$query_string = $this->querys->Duplicados($id_medico, $id_especialidad, $fecha);
		$query = $this->db->consulta($query_string);

		$cant = $this->db->num_rows($query);

		if ($cant != 0){
			$listado = "";
			while ($row = $this->db->fetch_array($query)){
				$listado .= "<p>".$row['desde']."</p>";
			}
			$rta = $listado;
		}else{
			$rta = 'Sin turnos duplicados ni superpuestos.';
		}
		return $rta;

	}


}
?>
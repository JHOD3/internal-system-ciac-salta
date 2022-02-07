<?php
interface iEstudios{

}

class Estudios extends Estructura implements iEstudios{

	function __construct($id = ""){
		$this->nombre_tabla = "estudios";
		$this->titulo_tabla = "Estudios";
		$this->titulo_tabla_singular = "Estudio";
		$this->tabla_padre = "";

		$this->drop_label_elija = "Elija un Estudio";

		parent::__construct($id);
	}

	function FormAlta(){
		$htm = $this->Html($this->nombre_tabla."/form_alta");

		$htm->Asigna("TABLA",$this->nombre_tabla);

		CargarVariablesGrales($htm, $tipo = "");

		return ($htm->Muestra());
	}

	function FormModificacion(){
		$htm = $this->Html($this->nombre_tabla."/form_modificacion");
		$row = $this->registro;

		$htm->Asigna("TABLA",$this->nombre_tabla);

		$htm->AsignaBloque('block_registros',$row);

		CargarVariablesGrales($htm, $tipo = "");

		return  utf8_encode($htm->Muestra());
	}

	function TablaAdmin(){
		$tabla = $this->html($this->nombre_tabla."/a_tabla");

		$tabla->Asigna("NOMBRE_TABLA",$this->nombre_tabla);

		$rta = utf8_encode($tabla->Muestra());

		return $rta;
	}

	/*function Listado($tipo, $id_obra_social = "", $id_turno = ""){
		$htm = $this->Html($this->nombre_tabla."/listado_".$tipo);
		switch ($tipo){
			case "todos":
				$query_string = $this->querys->TodosRegistros($this->nombre_tabla, "DESC");
			break;
			case "seleccion_modificacion":
				$id_estudios_seleccionadosv = $this->EstudiosSeleccionados($id_turno);
			case "seleccion":
				$query_string = $this->querys->EstudiosSeleccion($id_obra_social);
			break;
		}

		$query = $this->db->consulta($query_string);
		$cant = $this->db->num_rows($query);

		if ($cant > 0){
			while ($row = $this->db->fetch_array($query)){
				if ($tipo == "seleccion_modificacion"){
					if (in_array($row["id_estudios"], $id_estudios_seleccionadosv)){
						$row["SELECCIONADO"] = "selected=selected";
					}else{
						$row["SELECCIONADO"] = "";
					}
				}
				$htm->AsignaBloque('block_registros',$row);
			}
			$rta = $htm->Muestra();
		}else{

		}


		return $rta;

	}*/

	function Listado($tipo, $id_medico = '', $id_obra_social = '', $id_turno = ''){
		$htm = $this->Html($this->nombre_tabla."/listado_".$tipo);
		switch ($tipo){
			case "todos":
				$query_string = $this->querys->TodosRegistros($this->nombre_tabla, "ASC");
			break;
			case "seleccion_modificacion":
				$id_estudios_seleccionadosv = $this->EstudiosSeleccionados($id_turno, $id_medico);
			/* ESTO LO BORRE HASTA QUE SE PONGAN LAS PILAS Y CARGUEN LOS ESTUDIOS POR MEDICO */
			case "seleccion_medicos_os":
				$query_string = $this->querys->EstudiosSeleccionMedicosOS($id_medico, $id_obra_social);
			break;
			case "seleccion_medicos_os":
				$query_string = $this->querys->TodosRegistros($this->nombre_tabla, "ASC");
		}

		$query = $this->db->consulta($query_string);
		$cant = $this->db->num_rows($query);

		if ($cant > 0){
			while ($row = $this->db->fetch_array($query)){
				if ($tipo == "seleccion_modificacion"){
					if (in_array($row["id_estudios"], $id_estudios_seleccionadosv["id"])){
						$indice = array_search($row["id_estudios"], $id_estudios_seleccionadosv["id"]);
						$row['PARTICULAR'] = '($'.$id_estudios_seleccionadosv["particular"][$indice].')';
						$row["SELECCIONADO"] = "selected=selected";
					}else{
						$row["SELECCIONADO"] = "";
						$row['PARTICULAR'] = "";
					}
				}
				$htm->AsignaBloque('block_registros',$row);

			}
			$rta = $htm->Muestra();
		}else{
			$rta = "El medico no tiene cargado estudios para la OS del paciente";
		}
		return $rta;

	}


	function EstudiosSeleccionados($id_turno,  $id_medico){
		//Kcmnt Modificar el query, agregar un ORDER BY por TE.id_turnos_estudios
		$query_string = $this->querys->EstudiosSeleccionados($id_turno,  $id_medico);
		$query = $this->db->consulta($query_string);
		$cant = $this->db->num_rows($query);

		$id_estudios_seleccionadosv = array("id" => array(), "particular" => array());

		if ($cant > 0){
			while ($row = $this->db->fetch_array($query)){
				$id_estudios_seleccionadosv["id"][] = $row["id_estudios"];
				$id_estudios_seleccionadosv["particular"][] = $row["particular"];
			}
		}
		return $id_estudios_seleccionadosv;
	}


	function PanelAdmin(){
		$htm = $this->Html($this->nombre_tabla."/panel_admin");

		$htm->Asigna("LISTADO", $this->TablaAdmin());

		$htm->Asigna("TABLA", $this->nombre_tabla);
		$htm->Asigna("TITULO_TABLA", $this->titulo_tabla_singular);

		CargarVariablesGrales($htm, $tipo = "");

		echo ($htm->Muestra());

	}
}
?>
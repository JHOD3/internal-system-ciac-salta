<?php
interface iMedicos_estudios{

}

class Medicos_estudios extends Estructura implements iMedicos_estudios{

	function __construct($id = ""){
		$this->nombre_tabla = "medicos_estudios";
		$this->titulo_tabla = "Estudios de Médicos";
		$this->titulo_tabla_singular = "Estudio del Médico";
		$this->tabla_padre = "medicos";

		$this->drop_label_elija = "Elija un Estudio";

		parent::__construct($id);


	}

	/*function FormAlta($id_padre){
		$htm = $this->Html($this->nombre_tabla."/form_alta");

		requerir_class("estudios", "medicos");

		$obj_estudios = new Estudios();
		$htm->Asigna("DROP_ESTUDIOS",$obj_estudios->Drop());

		$obj_medico = new Medicos($id_padre);
		$htm->Asigna("MEDICO",$obj_medico->apellidos.', '.$obj_medico->nombres);

		$htm->Asigna("ID_MEDICO", $id_padre);
		$htm->Asigna("TABLA",$this->nombre_tabla);

		CargarVariablesGrales($htm, $tipo = "");

		return ($htm->Muestra());
	}*/

	function FormAlta($id_padre){

		return $this->Listado("seleccion_modificacion", $id_padre);
	}

	function FormModificacion(){
		$htm = $this->Html($this->nombre_tabla."/form_modificacion");
		$row = $this->registro;

		$htm->Asigna("TABLA",$this->nombre_tabla);

		$htm->AsignaBloque('block_registros',$row);

		CargarVariablesGrales($htm, $tipo = "");

		return  utf8_encode($htm->Muestra());
	}

	function TablaAdmin($id_padre = ""){
		$tabla = $this->html($this->nombre_tabla."/a_tabla");

		requerir_class("medicos");

		if ($id_padre != ""){
			$tabla->Asigna("ARGS","tabla=".$this->nombre_tabla."&id=".$id_padre);

			$obj_medico = new Medicos($id_padre);
			$tabla->Asigna("DATOS_MEDICO",$obj_medico->Detalle("corto"));
		}else{
			$tabla->Asigna("ARGS","tabla=".$this->nombre_tabla);
		}

		$tabla->Asigna("NOMBRE_TABLA",$this->nombre_tabla);



		$rta = utf8_encode($tabla->Muestra());

		return $rta;
	}

	function Listado($tipo, $id_medico = ''){
		$htm = $this->Html($this->nombre_tabla."/listado_".$tipo);

		CargarVariablesGrales($htm);

		switch ($tipo){
			case "todos":
				$query_string = $this->querys->TodosRegistros($this->nombre_tabla, "DESC");
			break;
			case "seleccion_modificacion":
				$id_estudios_seleccionadosv = $this->EstudiosSeleccionados($id_medico);
				$query_string = $this->querys->TodosRegistros("estudios", "DESC");
		}

		$query = $this->db->consulta($query_string);
		$cant = $this->db->num_rows($query);

		if ($cant > 0){
			while ($row = $this->db->fetch_array($query)){
				if ($tipo == "seleccion_modificacion"){
					if (in_array($row["id_estudios"], $id_estudios_seleccionadosv)){
						$row["SELECCIONADO"] = "disabled=disabled";
					}else{
						$row["SELECCIONADO"] = "";
					}
				}
				$htm->AsignaBloque('block_registros',$row);
			}

			$htm->Asigna('ID_MEDICO', $id_medico);
			$rta = $htm->Muestra();
		}else{
			$rta = "El medico no tiene cargados Estudios";
		}
		return $rta;

	}

	function EstudiosSeleccionados($id_medico){
		$query_string = $this->querys->EstudiosSeleccionadosMedico($id_medico);
		$query = $this->db->consulta($query_string);
		$cant = $this->db->num_rows($query);

		$id_estudios_seleccionadosv = array();

		if ($cant > 0){
			while ($row = $this->db->fetch_array($query)){
				$id_estudios_seleccionadosv[] = $row["id_estudios"];
			}
		}
		return $id_estudios_seleccionadosv;
	}

	function DuracionTurno($id_medico, $id_dia){
		$query_string = $this->querys->DuracionTurno($id_medico, $id_dia);
		$query = $this->db->consulta($query_string);
		$cant = $this->db->num_rows($query);

		$rta = $this->db->fetch_array($query);

		return $rta;
	}

	function PanelAdmin($id_padre = ""){
		$htm = $this->Html($this->nombre_tabla."/panel_admin_".$_SESSION['SISTEMA']);

		$htm->Asigna("LISTADO", $this->TablaAdmin($id_padre));

		$htm->Asigna("ID_MEDICO", $id_padre);

		$htm->Asigna("TABLA", $this->nombre_tabla);
		$htm->Asigna("TITULO_TABLA", $this->titulo_tabla_singular);

		CargarVariablesGrales($htm, $tipo = "");

		echo ($htm->Muestra());
	}
}
?>
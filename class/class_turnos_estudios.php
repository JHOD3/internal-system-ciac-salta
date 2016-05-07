<?php
interface iTurnos_estudios{

}

class Turnos_estudios extends Estructura implements iTurnos_estudios{
	
	function __construct($id = ""){ 
		$this->nombre_tabla = "turnos_estudios";
		$this->titulo_tabla = "Turnos_estudios";
		$this->tabla_padre = "";
		
		$this->drop_label_elija = "Elija un Estudio";
		
		parent::__construct($id);
	} 
	
	function FormAlta(){
		$htm_form = $this->Html($this->nombre_tabla."/form_alta");

		return ($htm_form->Muestra());
	}
	
	function TablaAdmin(){
		$tabla = $this->html($this->nombre_tabla."/a_tabla");
		
		$tabla->Asigna("NOMBRE_TABLA",$this->nombre_tabla);
		
		$rta = utf8_encode($tabla->Muestra());				
		
		return $rta;
	}

	function PanelAdmin(){
		$htm = $this->Html($this->nombre_tabla."/panel_admin");
		
		$htm->Asigna("FORM_ALTA", $this->FormAlta());
		$htm->Asigna("LISTADO", $this->TablaAdmin());
		
		CargarVariablesGrales($htm, $tipo = "");
		
		echo ($htm->Muestra());
		
	}	
	
	function PanelGral($id_turno, $id_medico, $id_obra_social){
		$htm = $this->Html($this->nombre_tabla."/panel_gral");
		
		requerir_class("estudios");
		$obj_estudios = new Estudios();
		$htm->Asigna("SELECCION_ESTUDIO", $obj_estudios->Listado("seleccion_medicos_os", $id_medico, $id_obra_social));
		
		$htm->Asigna("ID_TURNO", $id_turno);
		
		CargarVariablesGrales($htm);
		
		return ($htm->Muestra());
	}
	
	function PanelModificacion($id_turno, $id_medico, $id_obra_social){
		$htm = $this->Html($this->nombre_tabla."/panel_gral_modificacion");
		
		requerir_class("estudios");
		$obj_estudios = new Estudios();
		$htm->Asigna("SELECCION_ESTUDIO", $obj_estudios->Listado("seleccion_modificacion", $id_medico, $id_obra_social, $id_turno));
		
		$htm->Asigna("ID_TURNO", $id_turno);
		
		CargarVariablesGrales($htm);
		
		return ($htm->Muestra());
	}
	
	function BajaxTurno($id_turno){
		$query_string = $this->querys->BajaxTurno($id_turno);
		if ($this->db->consulta($query_string))
			$rta = "Baja Exitosa";
		else
			$rta = "No se pudo dar de bajo el registro. Intente de nuevo";
			
		return $rta;
	}
	
	function Totales($id_turno, $id_estudio){
		$query_string = $this->querys->Totales($id_turno, $id_estudio);
		$query = $this->db->consulta($query_string);
		
		$row = $this->db->fetch_array($query);
		
		return ($row);
	}
}
?>
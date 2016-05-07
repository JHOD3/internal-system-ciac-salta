<?php
interface iTurnos_estados{

}

class Turnos_estados extends Estructura implements iTurnos_estados{
	
	function __construct($id = ""){ 
		$this->nombre_tabla = "turnos_estados";
		$this->titulo_tabla = "Turnos_estados";
		$this->tabla_padre = "";
		
		$this->drop_label_elija = "Elija un Estado de Turno";
		
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
}
?>
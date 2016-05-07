<?php
interface iTurnos_cambios_estados{

}

class Turnos_cambios_estados extends Estructura implements iTurnos_cambios_estados{
	
	function __construct($id = ""){ 
		$this->nombre_tabla = "turnos_cambios_estados";
		$this->titulo_tabla = "Turnos_cambios_estados";
		$this->tabla_padre = "";
		
		$this->drop_label_elija = "Elija un Cambio de Estado";
		
		parent::__construct($id);
	} 
	
	function FormAlta(){
		$htm_form = $this->Html($this->nombre_tabla."/form_alta");

		return ($htm_form->Muestra());
	}
}
?>
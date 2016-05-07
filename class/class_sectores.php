<?php
interface iSectores{

}

class Sectores extends Estructura implements iSectores{
	
	function __construct($id = ""){ 
		$this->nombre_tabla = "sectores";
		$this->titulo_tabla = "Sectores";
		$this->tabla_padre = "";
		
		$this->drop_label_elija = "Elija un Sector";
		
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
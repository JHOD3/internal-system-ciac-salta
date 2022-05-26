<?php
interface iTurnos_tipos{

}

class Turnos_tipos extends Estructura implements iTurnos_tipos{
	
	function __construct($id = ""){ 
		$this->nombre_tabla = "turnos_tipos";
		$this->titulo_tabla = "Turnos_tipos";
		$this->tabla_padre = "";
		
		$this->drop_label_elija = "Elija un Tipo de Turno";
		
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

    function FormModificacion(){
        $htm = $this->Html($this->nombre_tabla."/form_modificacion");
        $row = $this->registro;

        $htm->Asigna("TABLA",$this->nombre_tabla);
        $htm->AsignaBloque('block_registros',$row);

        CargarVariablesGrales($htm, $tipo = "");

        return  utf8_encode($htm->Muestra());
    }
}
?>
<?php
interface iPlantas{

}

class Plantas extends Estructura implements iPlantas{

	function __construct($id = ""){
		$this->nombre_tabla = "plantas";
		$this->titulo_tabla = "Plantas";
		$this->tabla_padre = "";

		$this->drop_label_elija = "Elija una Planta";

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
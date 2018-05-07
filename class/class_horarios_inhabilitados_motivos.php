<?php
interface iHorarios_inhabilitados_motivos{

}

class Horarios_inhabilitados_motivos extends Estructura implements iHorarios_inhabilitados_motivos{

	function __construct($id = ""){
		$this->nombre_tabla = "horarios_inhabilitados_motivos";
		$this->titulo_tabla = "Motivos de Horarios Inhabilitados";
		$this->tabla_padre = "";

		$this->drop_label_elija = "Elija un Motivo de Horario Inhabilitado";

		parent::__construct($id);
	}

	function FormAlta(){
		$htm_form = $this->Html($this->nombre_tabla."/form_alta");

		return ($htm_form->Muestra());
	}

	function TablaAdmin(){
		$tabla = $this->html($this->nombre_tabla."/a_tabla");

		$tabla->Asigna("HOY",date("d/m/Y"));
		$tabla->Asigna("NOMBRE_TABLA",$this->nombre_tabla);

		$rta = utf8_encode($tabla->Muestra());

		return $rta;
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

<?php
interface iEncuestas{

}

class Encuestas extends Estructura implements iEncuestas{

	function __construct($id = ""){
		$this->nombre_tabla = "encuestas";
		$this->titulo_tabla = "Encuestas";
		$this->tabla_padre = "";

		$this->drop_label_elija = "Elija una Encuesta";

		parent::__construct($id);
	}

	function TablaAdmin(){
		$tabla = $this->html($this->nombre_tabla."/a_tabla");

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

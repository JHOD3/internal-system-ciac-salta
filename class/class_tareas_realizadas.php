<?php
interface iTareas_realizadas{

}

class Tareas_realizadas extends Estructura implements iTareas_realizadas{

	function __construct($id = ""){
		$this->nombre_tabla = "tareas_realizadas";
		$this->titulo_tabla = "Requisitos de Tareas";
        $this->titulo_tabla_singular = "Requisito de Tarea";
		$this->tabla_padre = "";

		$this->drop_label_elija = "Elija un Requisito de Tarea";

		parent::__construct($id);
	}

	function FormAlta($id_padre){
		$htm = $this->Html($this->nombre_tabla."/form_alta");

		$htm->Asigna("ID_TAREAS_CONFIGURACION", $id_padre);
        $htm->Asigna("TABLA",$this->nombre_tabla);

		CargarVariablesGrales($htm, $tipo = "");

		return ($htm->Muestra());
	}

	function FormModificacion($id_padre){
		$htm = $this->Html($this->nombre_tabla."/form_modificacion");
		$row = $this->registro;

		$htm->Asigna("ID_TAREAS_CONFIGURACION", $id_padre);
		$htm->Asigna("TABLA",$this->nombre_tabla);

		$htm->AsignaBloque('block_registros',$row);

		CargarVariablesGrales($htm, $tipo = "");

		return  utf8_encode($htm->Muestra());
	}

	function TablaAdmin($id_padre = ""){
		$tabla = $this->html($this->nombre_tabla."/a_tabla");

		if ($id_padre != "") {
			$tabla->Asigna("ARGS", "tabla=".$this->nombre_tabla."&id=".$id_padre);
		} else {
			$tabla->Asigna("ARGS", "tabla=".$this->nombre_tabla);
		}

		$tabla->Asigna("NOMBRE_TABLA",$this->nombre_tabla);

		$rta = utf8_encode($tabla->Muestra());

		return $rta;
	}

	function PanelAdmin($id_padre = ""){
		$htm = $this->Html($this->nombre_tabla."/panel_admin");

		$htm->Asigna("LISTADO", $this->TablaAdmin($id_padre));

		$htm->Asigna("ID_TAREAS_CONFIGURACION", $id_padre);
		$htm->Asigna("TABLA", $this->nombre_tabla);
		$htm->Asigna("TITULO_TABLA", $this->titulo_tabla_singular);

		CargarVariablesGrales($htm, $tipo = "");

		echo ($htm->Muestra());

	}
}

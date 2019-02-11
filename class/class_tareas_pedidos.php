<?php
interface iTareas_pedidos{

}

class Tareas_pedidos extends Estructura implements iTareas_pedidos{

	function __construct($id = ""){
		$this->nombre_tabla = "tareas_pedidos";
		$this->titulo_tabla = "Pedidos de Tareas";
        $this->titulo_tabla_singular = "Pedido de Tarea";
		$this->tabla_padre = "";

		$this->drop_label_elija = "Elija un Pedido de Tarea";

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
        $row['nombre'] = date("d/m/Y", strtotime($row['nombre']));

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

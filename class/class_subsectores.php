<?php
interface iSubsectores{

}

class Subsectores extends Estructura implements iSubsectores{

	function __construct($id = ""){
		$this->nombre_tabla = "subsectores";
		$this->titulo_tabla = "Subsectores";
        $this->titulo_tabla_singular = "Subsector";
		$this->tabla_padre = "";

		$this->drop_label_elija = "Elija un Subsectores";

		parent::__construct($id);

		requerir_class("sectores");
	}

	function FormAlta(){
		$htm = $this->Html($this->nombre_tabla."/form_alta");

		$obj_sectores = new Sectores();
		$htm->Asigna("DROP_SECTORES", $obj_sectores->Drop());

		$htm->Asigna("TABLA",$this->nombre_tabla);

		CargarVariablesGrales($htm, $tipo = "");

		return ($htm->Muestra());
	}

	function FormModificacion(){
		$htm = $this->Html($this->nombre_tabla."/form_modificacion");
		$row = $this->registro;

		$obj_sectores = new Sectores();
		$htm->Asigna("DROP_SECTORES", $obj_sectores->Drop("", $row["id_sectores"]));

		$htm->Asigna("TABLA",$this->nombre_tabla);

		$htm->AsignaBloque('block_registros',$row);

		CargarVariablesGrales($htm, $tipo = "");

		return  utf8_encode($htm->Muestra());
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

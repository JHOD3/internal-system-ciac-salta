<?php
interface iPacientes_Observaciones{

}

class Pacientes_Observaciones extends Estructura implements iPacientes_Observaciones{

	function __construct($id = ""){
		$this->nombre_tabla = "pacientes_observaciones";
		$this->titulo_tabla = "Observaciones de Pacientes";
        $this->titulo_tabla_singular = "Observación de Paciente";
		$this->tabla_padre = "";

		$this->drop_label_elija = "Elija una Observación de Paciente";

		parent::__construct($id);
	}

	function FormAlta($id_padre){
		$htm = $this->Html($this->nombre_tabla."/form_alta");

		requerir_class("pacientes");

		if ($id_padre != ""){
			$obj_paciente = new Pacientes($id_padre);
			$htm->Asigna("DATOS_PACIENTE",$obj_paciente->Detalle("tabla"));
		}else{
			$htm->Asigna("DATOS_PACIENTE");
		}

		$htm->Asigna("TABLA",$this->nombre_tabla);
		$htm->Asigna("id_padre",$id_padre);

		CargarVariablesGrales($htm, $tipo = "");

		return ($htm->Muestra());
	}

	function FormModificacion(){
		$htm = $this->Html($this->nombre_tabla."/form_modificacion");
		$row = $this->registro;

		$htm->Asigna("TABLA",$this->nombre_tabla);

		$htm->AsignaBloque('block_registros',$row);

		CargarVariablesGrales($htm, $tipo = "");

		return  utf8_encode($htm->Muestra());
	}

	function TablaAdmin($id_padre = ""){
		$tabla = $this->html($this->nombre_tabla."/a_tabla");

		requerir_class('pacientes');

		if ($id_padre != ""){
			$tabla->Asigna("ARGS","tabla=".$this->nombre_tabla."&id=".$id_padre);

			$obj_paciente = new Pacientes($id_padre);
			$tabla->Asigna("DATOS_PACIENTE",$obj_paciente->Detalle("tabla"));
		}else{
			$tabla->Asigna("ARGS","tabla=".$this->nombre_tabla);
		}

		$tabla->Asigna("NOMBRE_TABLA",$this->nombre_tabla);

		$rta = utf8_encode($tabla->Muestra());

		return $rta;
	}

	function PanelAdmin($id_padre = ""){
		$htm = $this->Html($this->nombre_tabla."/panel_admin");

		$htm->Asigna("LISTADO", $this->TablaAdmin($id_padre));

		$htm->Asigna("TABLA", $this->nombre_tabla);
		$htm->Asigna("TITULO_TABLA", $this->titulo_tabla_singular);
		$htm->Asigna("id_padre",$id_padre);

		CargarVariablesGrales($htm, $tipo = "");

		echo ($htm->Muestra());

	}
}

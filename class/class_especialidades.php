<?php
interface iEspecialidades{

}

class Especialidades extends Estructura implements iEspecialidades{
	
	function __construct($id = ""){ 
		$this->nombre_tabla = "especialidades";
		$this->titulo_tabla = "Especialidades";
		$this->titulo_tabla_singular = "Especialidad";
		$this->tabla_padre = "";
		
		$this->drop_label_elija = "Elija una Especialidad";
		
		parent::__construct($id);
	} 
	function cambiar_estado($tabla, $estado, $id)
	{
		$query = "UPDATE ".$tabla." SET estado = ".$estado." WHERE id_".$tabla." = ".$id;
		return $query;
	}
	
	function FormAlta(){
		$htm = $this->Html($this->nombre_tabla."/form_alta");
		
		$htm->Asigna("TABLA",$this->nombre_tabla);
		
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
?>
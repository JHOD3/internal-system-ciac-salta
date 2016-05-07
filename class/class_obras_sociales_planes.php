<?php
interface iObras_sociales_planes{

}

class Obras_sociales_planes extends Estructura implements iObras_sociales_planes{
	
	function __construct($id = ""){ 
		$this->nombre_tabla = "obras_sociales_planes";
		$this->titulo_tabla = "Planes de la Obra Social";
		$this->titulo_tabla_singular = "Plan Obra Social";
		$this->tabla_padre = "obras_sociales";
		
		$this->drop_label_elija = "Elija un Plan de Obra Social";
		
		parent::__construct($id);
	} 
	
	function FormAlta($id_padre){
		$htm = $this->Html($this->nombre_tabla."/form_alta");

		requerir_class("estudios", "obras_sociales");
		
		$htm->Asigna("ID_OBRA_SOCIAL", $id_padre);
		$htm->Asigna("TABLA",$this->nombre_tabla);
		
		CargarVariablesGrales($htm, $tipo = "");
		
		return ($htm->Muestra());
	}
	
	function FormModificacion($id_padre){
		$htm = $this->Html($this->nombre_tabla."/form_modificacion");
		$row = $this->registro;
		
		$htm->Asigna("TABLA",$this->nombre_tabla);
		$htm->Asigna("ID_OBRA_SOCIAL",$id_padre);
		
		$htm->AsignaBloque('block_registros',$row);
		
		CargarVariablesGrales($htm, $tipo = "");
		
		return  utf8_encode($htm->Muestra());
	}
	
	function TablaAdmin($id_padre = ""){
		$tabla = $this->html($this->nombre_tabla."/a_tabla");
		
		requerir_class("obras_sociales");
		
		if ($id_padre != ""){
			$tabla->Asigna("ARGS","tabla=".$this->nombre_tabla."&id=".$id_padre);	
			
			$obj_obra_social = new Obras_sociales($id_padre);
			$tabla->Asigna("DATOS_OBRA_SOCIAL",$obj_obra_social->Detalle("corto"));
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
		
		$htm->Asigna("ID_MEDICO", $id_padre);
		
		$htm->Asigna("TABLA", $this->nombre_tabla);
		$htm->Asigna("TITULO_TABLA", $this->titulo_tabla_singular);
		
		CargarVariablesGrales($htm, $tipo = "");
		
		echo ($htm->Muestra());
		
	}	
}
?>
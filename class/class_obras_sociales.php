<?php
interface iObras_sociales{

}

class Obras_sociales extends Estructura implements iObras_sociales{
	
	function __construct($id = ""){ 
		$this->nombre_tabla = "obras_sociales";
		$this->titulo_tabla = "Obras Sociales";
		$this->titulo_tabla_singular = "Obra Social";
		$this->tabla_padre = "";
		
		$this->drop_label_elija = "Elija una Obra Social";
		
		parent::__construct($id);
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
	
	function Detalle($tipo, $id_especialidad = "", $sistema = ""){
		if ($sistema != ""){
			$htm = $this->Html($sistema."/".$this->nombre_tabla."/detalle_".$tipo);
		}else{
			$htm = $this->Html($this->nombre_tabla."/detalle_".$tipo);
		}
		$row = $this->registro;
		
		switch ($sistema){
			case "sam":
				
			break;		
			case "":
			
			break;
		}
		
		$htm->AsignaBloque('block_registros',$row);
		
		$rta = $htm->Muestra();
		
		return $rta;
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
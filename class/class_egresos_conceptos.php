<?php
interface iEgresos_conceptos{

}

class Egresos_conceptos extends Estructura implements iEgresos_conceptos{
	
	function __construct($id = ""){ 
		$this->nombre_tabla = "egresos_conceptos";
		$this->titulo_tabla = "Egresos_conceptos";
		$this->tabla_padre = "";
		
		$this->drop_label_elija = "Elija un Concepto de Egreso";
		
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
		
		CargarVariablesGrales($htm, $tipo = "");
		
		echo ($htm->Muestra());
		
	}	
}
?>
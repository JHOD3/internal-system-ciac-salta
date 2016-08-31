<?php
interface iDias_semana{

}

class Dias_semana extends Estructura implements iDias_semana{
	
	function __construct($id = ""){ 
		$this->nombre_tabla = "dias_semana";
		$this->titulo_tabla = "Dias de la Semana";
		$this->tabla_padre = "";
		
		$this->drop_label_elija = "Elija un d&iacute;a";
		
		parent::__construct($id);
	} 
	
	function FormAlta(){
		$htm_form = $this->Html($this->nombre_tabla."/form_alta");

		return ($htm_form->Muestra());
	}
	
	function FormHorarios(){
		$htm = $this->Html($this->nombre_tabla."/form_horarios");

		$query_string = $this->querys->TodosRegistros($this->nombre_tabla, "DESC");
		$query = $this->db->consulta($query_string);
		$cant = $this->db->num_rows($query);

		if ($cant != 0){
			while ($row = $this->db->fetch_array($query)){
				$htm->AsignaBloque('block_registros',$row);
			}
		}
		
		return $htm->Muestra();
	}
	
}
?>
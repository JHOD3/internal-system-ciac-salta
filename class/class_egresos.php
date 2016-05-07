<?php
interface iEgresos{

}

class Egresos extends Estructura implements iEgresos{
	
	function __construct($id = ""){ 
		$this->nombre_tabla = "egresos";
		$this->titulo_tabla = "Egresos";
		$this->tabla_padre = "pacientes";
		
		$this->drop_label_elija = "Elija un egreso";
		
		parent::__construct($id);
	} 
	
	function FormAlta(){
		$htm_form = $this->Html($this->nombre_tabla."/form_alta");

		return ($htm_form->Muestra());
	}
	
	function TablaAdmin($id_padre = ""){
		$tabla = $this->html($this->nombre_tabla."/a_tabla");
		
		requerir_class("pacientes");
		
		if ($id_padre != ""){
			$tabla->Asigna("ARGS","tabla=".$this->nombre_tabla."&id=".$id_padre);	
			
			$obj_paciente = new Pacientes($id_padre);
			$tabla->Asigna("DATOS_MEDICO",$obj_paciente->Detalle("corto_cobro"));
		}else{
			$tabla->Asigna("ARGS","tabla=".$this->nombre_tabla);	
		}
		
		$tabla->Asigna("NOMBRE_TABLA",$this->nombre_tabla);
		
		
		
		$rta = utf8_encode($tabla->Muestra());				
		
		return $rta;
	}
	

	function Listado($tipo, $id_obra_social = "", $id_turno = ""){
		$htm = $this->Html($this->nombre_tabla."/listado_".$tipo);
		switch ($tipo){
			case "todos":
				$query_string = $this->querys->TodosRegistros($this->nombre_tabla, "DESC");	
			break;
			case "seleccion_modificacion":
				$id_estudios_seleccionadosv = $this->EstudiosSeleccionados($id_turno);
			case "seleccion":
				$query_string = $this->querys->EstudiosSeleccion($id_obra_social);
			break;	
		}
		
		$query = $this->db->consulta($query_string);
		$cant = $this->db->num_rows($query);
		
		if ($cant > 0){
			while ($row = $this->db->fetch_array($query)){
				if ($tipo == "seleccion_modificacion"){
					if (in_array($row["id_estudios"], $id_estudios_seleccionadosv)){
						$row["SELECCIONADO"] = "selected=selected";
					}else{
						$row["SELECCIONADO"] = "";
					}
				}
				$htm->AsignaBloque('block_registros',$row);
			}
			$rta = $htm->Muestra();
		}else{
			
		}
		
		
		return $rta;
		
	}
	
	function EstudiosSeleccionados($id_turno){
		$query_string = $this->querys->EstudiosSeleccionados($id_turno);
		$query = $this->db->consulta($query_string);
		$cant = $this->db->num_rows($query);
		
		$id_estudios_seleccionadosv = array();
		
		if ($cant > 0){
			while ($row = $this->db->fetch_array($query)){
				$id_estudios_seleccionadosv[] = $row["id_estudios"];	
			}
		}
		return $id_estudios_seleccionadosv;
	}
	
	function PanelAdmin($id_padre = ""){
		$htm = $this->Html($this->nombre_tabla."/panel_admin");

		$htm->Asigna("LISTADO", $this->TablaAdmin($id_padre));
		
		$htm->Asigna("ID_PACIENTE", $id_padre);
		
		$htm->Asigna("TABLA", $this->nombre_tabla);
		
		CargarVariablesGrales($htm, $tipo = "");
		
		echo ($htm->Muestra());
	}	
}
?>
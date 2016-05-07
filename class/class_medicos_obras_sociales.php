<?php
interface iMedicos_obras_sociales{

}

class Medicos_obras_sociales extends Estructura implements iMedicos_obras_sociales{
	
	function __construct($id = ""){ 
		$this->nombre_tabla = "medicos_obras_sociales";
		$this->titulo_tabla = "Obras Sociales de Médicos";
		$this->titulo_tabla_singular = "Obra Social Medico";
		$this->tabla_padre = "medicos";
		
		$this->drop_label_elija = "Elija un Obra Social";
		
		parent::__construct($id);
		
		
	} 
	
	function FormAlta($id_padre){
		
		return $this->Listado("seleccion_modificacion", $id_padre);
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
		
		requerir_class("medicos");
		
		if ($id_padre != ""){
			$tabla->Asigna("ARGS","tabla=".$this->nombre_tabla."&id=".$id_padre);	
			
			$obj_medico = new Medicos($id_padre);
			$tabla->Asigna("DATOS_MEDICO",$obj_medico->Detalle("corto"));
		}else{
			$tabla->Asigna("ARGS","tabla=".$this->nombre_tabla);	
		}
		
		$tabla->Asigna("NOMBRE_TABLA",$this->nombre_tabla);
		
		$rta = utf8_encode($tabla->Muestra());				
		
		return $rta;
	}
	
	function Listado($tipo, $id_medico = ''){
		$htm = $this->Html($this->nombre_tabla."/listado_".$tipo);
		
		CargarVariablesGrales($htm);
		
		switch ($tipo){
			case "todos":
				$query_string = $this->querys->TodosRegistros($this->nombre_tabla, "DESC");	
			break;
			case "seleccion_modificacion":
				$id_obras_sociales_seleccionadasv = $this->ObrasSocialesSeleccionadas($id_medico);
				$query_string = $this->querys->ObrasSocialesPlanes();	
		}
		
		$query = $this->db->consulta($query_string);
		$cant = $this->db->num_rows($query);
		
		if ($cant > 0){
			while ($row = $this->db->fetch_array($query)){
				if ($tipo == "seleccion_modificacion"){
					if (in_array($row["id_obras_sociales"], $id_obras_sociales_seleccionadasv)){
						$row["SELECCIONADO"] = "disabled=disabled";
					}else{
						$row["SELECCIONADO"] = "";
					}
				}
				$htm->AsignaBloque('block_registros',$row);
			}
			
			$htm->Asigna('ID_MEDICO', $id_medico);
			$rta = $htm->Muestra();
		}else{
			$rta = "El medico no tiene cargada Obras Sociales";
		}
		return $rta;
		
	}
	
	function ObrasSocialesSeleccionadas($id_medico){
		$query_string = $this->querys->ObrasSocialesSeleccionadas($id_medico);
		$query = $this->db->consulta($query_string);
		$cant = $this->db->num_rows($query);
		
		$id_obras_sociales_seleccionadasv = array();
		
		if ($cant > 0){
			while ($row = $this->db->fetch_array($query)){
				$id_obras_sociales_seleccionadasv[] = $row["id_obras_sociales"];	
			}
		}
		return $id_obras_sociales_seleccionadasv;
	}
	
	function DuracionTurno($id_medico, $id_dia){
		$query_string = $this->querys->DuracionTurno($id_medico, $id_dia);
		$query = $this->db->consulta($query_string);
		$cant = $this->db->num_rows($query);
	
		$rta = $this->db->fetch_array($query);
		
		return $rta;	
	}
	
	function BajaxMedico($id_medico){
		$query_string = $this->querys->BajaxMedico($id_medico);
		if ($this->db->consulta($query_string))
			$rta = "Baja Exitosa";
		else
			$rta = "No se pudo dar de bajo el registro. Intente de nuevo";
			
		return $rta;
	}
		
	function PanelAdmin($id_padre = ""){
		$htm = $this->Html($this->nombre_tabla."/panel_admin");

		$htm->Asigna("LISTADO", $this->TablaAdmin($id_padre));
		
		$htm->Asigna("ID_MEDICO", $id_padre);
		
		$htm->Asigna("TABLA", $this->nombre_tabla);
		
		CargarVariablesGrales($htm, $tipo = "");
		
		echo ($htm->Muestra());
	}
	
	function Atiende($id_medico, $id_obra_social, $id_obra_social_plan){
		$query_string = $this->querys->Atiende($id_medico, $id_obra_social, $id_obra_social_plan);
		$query = $this->db->consulta($query_string);
		error_log($query_string);
		$cant = $this->db->num_rows($query);
			
		if ($cant > 0){
			$rta = true;
		}else{
			$rta = false;
		}
		return $rta;	
	}

}
?>
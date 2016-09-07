<?php
interface iMedicos_horarios{

}

class Medicos_horarios extends Estructura implements iMedicos_horarios{
	
	function __construct($id = ""){ 
		$this->nombre_tabla = "medicos_horarios";
		$this->titulo_tabla = "Horarios de Médicos";
		$this->titulo_tabla_singular = "Horario del Médico";
		$this->tabla_padre = "";
		
		$this->drop_label_elija = "Elija un Horario";
		
		requerir_class ("medicos");
		
		parent::__construct($id);
	} 
	
	
	function FormAlta($id_padre){
		$htm = $this->Html($this->nombre_tabla."/form_alta");
		
		requerir_class("turnos_tipos", "dias_semana");
		
		$obj_turnos_tipos = new Turnos_tipos();
		$htm->Asigna("DROP_TURNOS_TIPOS",$obj_turnos_tipos->Drop());
		
		$obj_dias_semana = new Dias_semana();
		$htm->Asigna("DROP_DIAS_SEMANA", utf8_encode($obj_dias_semana->Drop()));	
		
		$idsv = explode("-", $id_padre);
		$id_medico = $idsv[0];
		$id_especialidad = $idsv[1];
		
		$htm->Asigna("ID_MEDICO", $id_medico);	
		$htm->Asigna("ID_ESPECIALIDAD", $id_especialidad);	
		
		$htm->Asigna("TABLA",$this->nombre_tabla);
			
		CargarVariablesGrales($htm, $tipo = "");
		
		return ($htm->Muestra());
	}
	
	function FormModificacion(){
		$htm = $this->Html($this->nombre_tabla."/form_modificacion");
		$row = $this->registro;
		
		requerir_class("turnos_tipos", "dias_semana");
		
		$obj_turnos_tipos = new Turnos_tipos();
		$htm->Asigna("DROP_TURNOS_TIPOS",$obj_turnos_tipos->Drop("DESC",$row['id_turnos_tipos']));
		
		$obj_dias_semana = new Dias_semana();
		$htm->Asigna("DROP_DIAS_SEMANA", utf8_encode($obj_dias_semana->Drop("DESC",$row['id_dias_semana'])));
		
		$htm->Asigna("TABLA",$this->nombre_tabla);

        $row['desde'] = substr($row['desde'], 0, 5);
        $row['hasta'] = substr($row['hasta'], 0, 5);

		$htm->AsignaBloque('block_registros',$row);
		
		CargarVariablesGrales($htm, $tipo = "");
		
		return  $htm->Muestra();
	}
	
	function TablaAdmin($id_padre = ""){
		$tabla = $this->html($this->nombre_tabla."/a_tabla");
		
		if ($id_padre != ""){
			$idsv = explode("-", $id_padre);
			$id_medico = $idsv[0];
			$id_especialidad = $idsv[1];
			
			$tabla->Asigna("ARGS","tabla=".$this->nombre_tabla."&id_medico=".$id_medico."&id_especialidad=".$id_especialidad);	
			
			$obj_medico = new Medicos($id_medico);
			$tabla->Asigna("DATOS_MEDICO",$obj_medico->Detalle("corto_especialidad", $id_especialidad));
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
		
		$htm->Asigna("ID_PADRE", $id_padre);	
		
		$htm->Asigna("TABLA", $this->nombre_tabla);	
		$htm->Asigna("TITULO_TABLA", $this->titulo_tabla_singular);
		
		CargarVariablesGrales($htm, $tipo = "");
		
		echo ($htm->Muestra());
		
	}
}
?>
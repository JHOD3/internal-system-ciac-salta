<?php
interface iPacientes{
	function FormAlta();
	function FormModificacion();
}

class Pacientes extends Estructura implements iPacientes{
	
	function __construct($id = ""){ 
		$this->nombre_tabla = "pacientes";
		$this->titulo_tabla = "Pacientes";
		$this->titulo_tabla_singular = "Paciente";
		$this->tabla_padre = "";
		
		$this->drop_label_elija = "Elija un Paciente";
		
		parent::__construct($id);
	} 
	
	function FormAlta(){
		$htm = $this->Html($this->nombre_tabla."/form_alta");
		
		requerir_class("tipos_documentos","obras_sociales", "obras_sociales_planes");
		
		$obj_tipos_documentos = new Tipos_Documentos();
		$htm->Asigna("DROP_TIPOS_DOCUMENTOS",$obj_tipos_documentos->Drop("", 1));		
		
		$obj_obras_sociales = new Obras_sociales();
		$htm->Asigna("DROP_OBRAS_SOCIALES", $obj_obras_sociales->Drop('nombre'));
		
		$obj_obras_sociales_planes = new Obras_sociales_planes();
		$htm->Asigna("DROP_OBRAS_SOCIALES_PLANES", $obj_obras_sociales_planes->DropVacio());		
		
		$htm->Asigna("TABLA",$this->nombre_tabla);
		
		CargarVariablesGrales($htm, $tipo = "");
		
		return ($htm->Muestra());
	}
	
	function FormModificacion(){
		$htm = $this->Html($this->nombre_tabla."/form_modificacion");
		$row = $this->registro;
		
		requerir_class("tipos_documentos","obras_sociales", "obras_sociales_planes");
		
		$obj_tipos_documentos = new Tipos_Documentos();
		$row["DROP_TIPOS_DOCUMENTOS"] = $obj_tipos_documentos->Drop("DESC", $row["id_tipos_documentos"]);		
		
		$obj_obras_sociales = new Obras_sociales();
		$row["DROP_OBRAS_SOCIALES"] = $obj_obras_sociales->Drop("DESC", $row["id_obras_sociales"]);		
		
		$obj_obras_sociales_planes = new Obras_sociales_planes();
		$row["DROP_OBRAS_SOCIALES_PLANES"] = $obj_obras_sociales_planes->Drop("DESC", $row["id_obras_sociales_planes"]);

        if ($_SESSION['ID_USUARIO'] != 0) {
            $row['bloqueado0'] = ' disabled="disabled"';
            $row['bloqueado1'] = ' disabled="disabled"';
            $row['bloqueado'.$row['bloqueado']] = ' checked="checked" disabled="disabled"';
        } else {                        
            $row['bloqueado0'] = '';
            $row['bloqueado1'] = '';
            $row['bloqueado'.$row['bloqueado']] = ' checked="checked"';
        }    

		$htm->Asigna("TABLA",$this->nombre_tabla);
		
		$htm->AsignaBloque('block_registros',$row);
		
		CargarVariablesGrales($htm, $tipo = "");
		
		return  utf8_encode($htm->Muestra());
	}
	
	function Detalle($tipo, $sistema = "sas"){
		$htm = $this->Html($this->nombre_tabla."/detalle_".$tipo);
		$row = $this->registro;
		
		requerir_class('obras_sociales','obras_sociales_planes');
			
		$obj_obra_social = new Obras_sociales($row['id_obras_sociales']);
		$row["OBRA_SOCIAL"] = $obj_obra_social->nombre;
		
		$obj_obra_social_plan = new Obras_sociales_planes($row['id_obras_sociales_planes']);
		$row["OBRA_SOCIAL_PLAN"] = $obj_obra_social_plan->nombre;
        $row['nro_documento'] = number_format($row['nro_documento'], 0, ",", ".");
		$htm->AsignaBloque('block_registros',$row);

		$rta = $htm->Muestra();
		
		return utf8_encode($rta);
	}
	
	
	
	function TablaAdmin(){
		$tabla = $this->html($this->nombre_tabla."/a_tabla");
		
		$tabla->Asigna("NOMBRE_TABLA",$this->nombre_tabla);
		
		$rta = utf8_encode($tabla->Muestra());				
		
		return $rta;
	}
	
	function Buscar($dni){
		$htm = $this->Html($this->nombre_tabla."/detalle_corto");

		$query_string = $this->querys->Buscar($this->nombre_tabla, $dni);
		$query = $this->db->consulta($query_string);
		$cant = $this->db->num_rows($query);
		
		if ($cant != 0){
			
			requerir_class('obras_sociales','obras_sociales_planes');
			
			while ($row = $this->db->fetch_array($query)){
				$obj_obra_social = new Obras_sociales($row['id_obras_sociales']);
				$row["OBRA_SOCIAL"] = $obj_obra_social->nombre;
				
				$obj_obra_social_plan = new Obras_sociales_planes($row['id_obras_sociales_planes']);
				$row["OBRA_SOCIAL_PLAN"] = $obj_obra_social_plan->nombre;
                $row['nro_documento'] = number_format($row['nro_documento'], 0, ",", ".");

				$htm->AsignaBloque('block_registros',$row);
			}
			
			$rta = $htm->Muestra();
		}else{
			$rta = "Paciente no encontrado";	
		}
		
		
		return utf8_encode($rta);
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
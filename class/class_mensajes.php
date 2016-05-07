<?php
interface iMensajes{

}

class Mensajes extends Estructura implements iMensajes{
	
	function __construct($id = ""){ 
		$this->nombre_tabla = "mensajes";
		$this->titulo_tabla = "Mensajes";
		$this->tabla_padre = "";
		
		$this->drop_label_elija = "Elija un Mensaje";
		
		parent::__construct($id);
	} 
	
	function FormAlta(){
		$htm_form = $this->Html($this->nombre_tabla."/form_alta");

		return ($htm_form->Muestra());
	}
	
	function PanelAdmin(){
		$htm = $this->Html($this->nombre_tabla."/panel_admin");
		
		$htm->Asigna("LISTADO_USUARIOS", $this->ListadoUsuarios(1));
		$htm->Asigna("TABLA",$this->nombre_tabla);
		
		CargarVariablesGrales($htm, $tipo = "");
		
		echo ($htm->Muestra());
		
	}
	
	function ListadoUsuarios($id_usuario_identificado){
		$htm = $this->Html($this->nombre_tabla."/listado_usuarios");
		
		$query_string = $this->querys->TodosRegistros("medicos", "ASC");
		$query = $this->db->consulta($query_string);
		$cant = $this->db->num_rows($query);
		
		if ($cant != 0){
			while ($row = $this->db->fetch_array($query)){
				$htm->AsignaBloque('block_medicos',$row);
			}
		}else{
			$rta = "M&eacute;dicos no encontrado";	
		}
		
		$query_string2 = $this->querys->TodosRegistros("usuarios", "ASC");
		$query2 = $this->db->consulta($query_string2);
		$cant2 = $this->db->num_rows($query2);
		
		if ($cant2 != 0){
			while ($row2 = $this->db->fetch_array($query2)){
				$htm->AsignaBloque('block_usuarios',$row2);
			}
			$rta = $htm->Muestra();
		}else{
			$rta = "M&eacute;dicos no encontrado";	
		}
		
		echo (utf8_encode($rta));
	}
	
	function PanelMensajes($id_receptor){
				
		$htm = $this->Html($this->nombre_tabla."/panel_mensajes");
		
		switch ($_SESSION['TIPO_USR']){
			case 'M':
				$id_emisor = $_SESSION['TIPO_USR']."-".$_SESSION["ID_MEDICO"];
			break;
			case 'U':
				$id_emisor = $_SESSION['TIPO_USR']."-".$_SESSION["ID_USUARIO"];
			break;
			case 'A':
				$id_emisor = $_SESSION['TIPO_USR']."-".$_SESSION["ID_ADMINISTRADOR"];
		}
		
		$htm->Asigna('ID_EMISOR',$id_emisor);
		$htm->Asigna('ID_RECEPTOR',$id_receptor);

		$htm->Asigna('LISTADO_MENSAJES', $this->ListadoMensajes($id_emisor, $id_receptor));
		
		$rta = $htm->Muestra();
		
		echo ($rta);
		
	}
	
	function MensajesSinLeer($id_actor, $id_secundario){
		$query_string = $this->querys->MensajesSinLeer($id_actor, $id_secundario);
		$query = $this->db->consulta($query_string);
		$cant = $this->db->num_rows($query);
		
		return $cant . " Mensajes Sin Leer";
	}
	
	function ListadoMensajes($id_emisor, $id_receptor){
		$htm = $this->Html($this->nombre_tabla."/listado_mensajes");

		$query_string = $this->querys->Mensajes($id_emisor, $id_receptor);
		$query = $this->db->consulta($query_string);
		$cant = $this->db->num_rows($query);
		
		if ($cant != 0){
			while ($row = $this->db->fetch_array($query)){
				if ($row["id_emisor"] == $id_emisor){
					$row["CLASS"] = "izq";
				}else{
					$row["CLASS"] = "der";
				}
				
				$htm->AsignaBloque('block_registros',$row);
			}
		
			CargarVariablesGrales($htm, $tipo = "");
			
			$rta = $htm->Muestra();
		}else{
			$rta = 'No tenes mensajes cargados';	
		}
		
		return $rta;
	}
	
	
}
?>
<?php
interface iUsuarios{

}

class Usuarios extends Estructura implements iUsuarios{
	
	function __construct($id = ""){ 
		$this->nombre_tabla = "usuarios";
		$this->titulo_tabla = "Usuarios";
		$this->tabla_padre = "";
		
		$this->drop_label_elija = "Elija un Usuario";
		
		parent::__construct($id);
	} 
	
	function ValidaLogueo($usuario, $pass){
		//Verifico si existe usuario con ese nombre y clave
		$query = $this->db->consulta($this->querys->ValidaLogueo($this->nombre_tabla, $usuario, base64_encode($pass)));
		
		$cant_usr = $this->db->num_rows($query); 
		
		if ($cant_usr == 1){
			while ($usr = $this->db->fetch_array($query))
			{
				//variable para controlar tiempo que esta conectado
				$ultimo_acceso = date("Y-n-j H:i:s");
				
				$_SESSION['ID_USUARIO'] = $usr[0];
				$_SESSION['TIPO_USR'] = "U";
				$_SESSION['APELLIDOS'] = $usr['apellidos'];
				$_SESSION['NOMBRES'] = $usr['nombres'];
				$_SESSION['USUARIO'] = $usr[3];
				$_SESSION['SISTEMA'] = 'sas';
				$_SESSION['EMISOR'] = $_SESSION['TIPO_USR']."-".$_SESSION["ID_USUARIO"];
				$_SESSION['ULTIMO_ACCESO'] = $ultimo_acceso;
			}
			return ("1"); //Login Correcto		
		}
		else
			return ("2"); //Login Incorrecto			
	}
	
	function FormAlta(){
		$htm_form = $this->Html($this->nombre_tabla."/form_alta");

		return ($htm_form->Muestra());
	}
	
	function Detalle($tipo){
		$htm_form = $this->Html($this->nombre_tabla."/detalle_".$tipo);
		
		return $htm_form->Muestra();
		
	}
	
	
	
}
?>
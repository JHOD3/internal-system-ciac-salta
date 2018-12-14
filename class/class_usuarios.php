<?php
interface iUsuarios{

}

class Usuarios extends Estructura implements iUsuarios{

	function __construct($id = ""){
		$this->nombre_tabla = "usuarios";
		$this->titulo_tabla = "Usuarios";
        $this->titulo_tabla_singular = "Usuario";
		$this->tabla_padre = "";

		$this->drop_label_elija = "Elija un Usuario";

		parent::__construct($id);
		requerir_class("roles");
	}

	function FormAlta(){
		$htm = $this->Html($this->nombre_tabla."/form_alta");

		$obj_roles = new Roles();
		$htm->Asigna("DROP_ROLES", $obj_roles->Drop());

		$htm->Asigna("TABLA",$this->nombre_tabla);

		CargarVariablesGrales($htm, $tipo = "");

		return ($htm->Muestra());
	}

	function FormModificacion(){
		$htm = $this->Html($this->nombre_tabla."/form_modificacion");
		$row = $this->registro;

		$obj_roles = new Roles();
		$htm->Asigna("DROP_ROLES", $obj_roles->Drop("", $row["superuser"]));

		$htm->Asigna("TABLA",$this->nombre_tabla);

		$htm->AsignaBloque('block_registros',$row);

		CargarVariablesGrales($htm, $tipo = "");

		return  utf8_encode($htm->Muestra());
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

	function ValidaLogueo($usuario, $pass){
		//Verifico si existe usuario con ese nombre y clave
		$query = $this->db->consulta($this->querys->ValidaLogueo($this->nombre_tabla, $usuario, base64_encode($pass)));

		$cant_usr = $this->db->num_rows($query);

		if ($cant_usr == 1){
			while ($usr = $this->db->fetch_array($query))
			{
                #print $_SERVER['HTTP_HOST'];
                if (
                    $_SERVER['HTTP_HOST'] != 'ciacsaltadb.ddns.net' or
                    $usr[8] > 1 or
                    $usr[1] == 'invitado'
                ) {
    				//variable para controlar tiempo que esta conectado
    				$ultimo_acceso = date("Y-n-j H:i:s");

    				$_SESSION['ID_USUARIO'] = $usr[0];
    				$_SESSION['TIPO_USR'] = "U";
    				$_SESSION['APELLIDOS'] = $usr['apellidos'];
    				$_SESSION['NOMBRES'] = $usr['nombres'];
    				$_SESSION['USUARIO'] = $usr[3];
    				$_SESSION['SUPERUSER'] = $usr[8];
    				$_SESSION['SISTEMA'] = 'sas';
    				$_SESSION['EMISOR'] = $_SESSION['TIPO_USR']."-".$_SESSION["ID_USUARIO"];
    				$_SESSION['ULTIMO_ACCESO'] = $ultimo_acceso;
                } else {
                    return ("4"); //No autorizado vía remota dyndns
                }
			}
			return ("1"); //Login Correcto
		}
		else
			return ("2"); //Login Incorrecto
	}
/*
	function FormAlta(){
		$htm_form = $this->Html($this->nombre_tabla."/form_alta");

		return ($htm_form->Muestra());
	}
*/
	function Detalle($tipo){
		$htm_form = $this->Html($this->nombre_tabla."/detalle_".$tipo);

		return $htm_form->Muestra();

	}



}
?>
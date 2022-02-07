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
        $row['fechanac'] = date("d/m/Y", strtotime($row['fechanac']));

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
                // #print $_SERVER['HTTP_HOST'];
                // if (
                //     $_SERVER['HTTP_HOST'] != 'ciacsaltadb.ddns.net' or
                //     $usr[8] > 1 or
                //     $usr[1] == 'invitado'
                // ) {
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
    				$_SESSION['PERMISO_AGENDA'] = $usr[10];
    				$_SESSION['PERMISO_COMUNICACION'] = $usr[11];
    				$_SESSION['PERMISO_COMUNICADOS_GERENCIA'] = $usr[12];
    				$_SESSION['PERMISO_NOVEDADES_DIARIAS'] = $usr[13];
    				$_SESSION['PERMISO_NOTAS_IMPRECION'] = $usr[14];
    				$_SESSION['PERMISO_ENCUESTAS'] = $usr[15];
    				$_SESSION['PERMISO_ESPECIALIDADES'] = $usr[16];
    				$_SESSION['PERMISO_ESTUDIOS'] = $usr[17];
    				$_SESSION['PERMISO_MANTENIMIENTO'] = $usr[18];
    				$_SESSION['PERMISO_MANTENIMIENTO_RECIENTE'] = $usr[19];
    				$_SESSION['PERMISO_MANTENIMIENTO_HISTORICO'] = $usr[20];
    				$_SESSION['PERMISO_MEDICOS'] = $usr[21];
    				$_SESSION['PERMISO_MEDICOS_CIAC'] = $usr[22];
    				$_SESSION['PERMISO_MEDICOS_EXTERNOS'] = $usr[23];
    				$_SESSION['PERMISO_MEDICOS_EMPRESAS'] = $usr[24];
    				$_SESSION['PERMISO_OBRAS_SOCIALES'] = $usr[25];
    				$_SESSION['PERMISO_PACIENTES'] = $usr[26];
    				$_SESSION['PERMISO_PLANES_CONTINGENCIA'] = $usr[27];
    				$_SESSION['PERMISO_PRACTICAS_MEDICAS'] = $usr[28];
    				$_SESSION['PERMISO_SECTORES'] = $usr[29];
    				$_SESSION['PERMISO_SECTORES_UNO'] = $usr[30];
    				$_SESSION['PERMISO_SUBSECTORES'] = $usr[31];
    				$_SESSION['PERMISO_CONSULTORIOS'] = $usr[32];
    				$_SESSION['PERMISO_DISPONIBILIDADES'] = $usr[33];
    				$_SESSION['PERMISO_TAREAS'] = $usr[34];
    				$_SESSION['PERMISO_TAREAS_CONFIGURACION'] = $usr[35];
    				$_SESSION['PERMISO_TAREAS_PENDIENTES'] = $usr[36];
    				$_SESSION['PERMISO_CUMPLES'] = $usr[37];
    				$_SESSION['PERMISO_USUARIOS_PERMISO'] = $usr[38];
                //} else {
                //    return ("4"); //No autorizado vía remota dyndns
                //}
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
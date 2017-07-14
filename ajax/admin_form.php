<?php
require_once("../engine/config.php");
require_once("../engine/restringir_acceso.php");
requerir_class("tpl","querys","mysql","estructura","json");

$tabla = $_POST["tabla"];
$tipo = $_POST["tipo"];
if (isset($_POST['tipo_btn']) and $_POST['tipo_btn'] == 'tabla_eme') {
    print '<input type="hidden" id="input_tipo_btn" value="tabla_eme" />'."\n";
}

requerir_class($tabla);
$clase = ucwords($tabla);

if ($tipo != "alta"){
	$id = $_POST["id"];
	$obj = new $clase($id);
}else{
	$obj = new $clase();
}

switch ($tipo){
	case "detalle":
		$rta = $obj->Detalle("completo");
	break;
	case "editar":
		switch($tabla){
			case "medicos_estudios":
			case "medicos_obras_sociales":
			case "obras_sociales_estudios":
			case "obras_sociales_planes":
			case "medicos_especialidades":
			//case "medicos_horarios":
				$id_padre = $_POST["id_padre"];
				$rta = $obj->FormModificacion($id_padre);
			break;
			default:
				$rta = $obj->FormModificacion();
		}
	break;
	case "alta":
		switch($tabla){
			case "medicos_estudios":
			case "medicos_obras_sociales":
			case "obras_sociales_estudios":
			case "obras_sociales_planes":
			case "medicos_especialidades":
			case "medicos_horarios":
				$id_padre = $_POST["id_padre"];
				$rta = $obj->FormAlta($id_padre);
			break;
			default:
				$rta = $obj->FormAlta();
		}

	break;
}

echo $rta;

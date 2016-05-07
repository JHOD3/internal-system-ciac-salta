<?php
require_once("../engine/config.php");
requerir_class("tpl","querys","mysql","estructura");

$tipo = $_POST["tipo"];
$tabla = $_POST["tabla"];
if (isset($_POST["valor"])){
	$valor = $_POST["valor"];
}

requerir_class($tabla);
$clase = ucwords($tabla);

switch ($tabla){
	case "obras_sociales_planes":
	case "turnos":
		$obj = new $clase($valor);
	break;
	default:
		$obj = new $clase();	
}

switch ($tipo){
	case "drop":
		if (isset($_POST["nombre_drop"])){
			$nombre = $_POST["nombre_drop"];
		}else
			$nombre="";

		$resp = $obj->Drop("nombre","",$valor,$nombre,"ASC");
		$resp = utf8_decode($resp);
	break;
	case "drop_vacio":
		$resp = $obj->DropVacio();
	break;
	case "detalle":
		$resp = $obj->Detalle();
	break;
	case "listado":
		parse_str(stripslashes($_POST["variables"]));
		$resp = $obj->ListadoMensajes($id_emisor, $id_receptor);
	break;
	case "form":
		$tipo_form = $_POST["tipo_form"];
		if ($tabla == "tipos_propiedades"){
			$nombre_tabla = "propiedades_".$obj->tabla;
			$clase = ucwords($nombre_tabla);
			requerir_class($nombre_tabla);
			$obj2 = new $clase;
			$resp = $obj2->FormAlta($tipo_form);
		}
	break;
	case "listado_imagenes":
		$imagenesv = $obj->ListadoImagenes("ajax");
		$resp = $imagenesv["listado"];
	break;
	case "reproductor_video":
		$reproductorv = $obj->ReproductorVideo("ajax","be");
		$resp = $reproductorv["reproductor"];
	break;
	case "cargar_recursos":
		$resp = $obj->FormCargarImagenes();
	break;
	case "turnos_todos":
		$obj = new $clase($valor);
	
		$id_especialidad = $_POST["id_especialidad"];
		$fecha = $_POST["fecha"];
		$horarios = $_POST["horarios"];
		$resp = $obj->GrillaTurnosImprimir($id_especialidad, $fecha, $horarios);
		$resp = utf8_encode($resp);
	break;
	case "cobros":
		$obj = new $clase($valor);
	
		$id_especialidad = $_POST["id_especialidad"];
		$fecha = $_POST["fecha"];
		$horarios = $_POST["horarios"];
		$resp = $obj->GrillaCobrosImprimir($id_especialidad, $fecha, $horarios);
		$resp = utf8_encode($resp);
	break;
	case 'estudios_turno':
		$resp = $obj-> EstudiosTurnos();
	break;
	
}

echo $resp;
?>
<?php
require_once ("../engine/config.php");
require_once("../engine/restringir_acceso.php");
requerir_class("tpl","querys","mysql","estructura");

$tabla = $_POST["tabla"];

requerir_class($tabla);
$clase = ucwords($tabla);

if (isset($_POST["id_paciente"]))
	$obj = new $clase($_POST["id_paciente"]);
else
	$obj = new $clase();

switch ($tabla){
	case 'pacientes':
		$nro_documento = $_POST["nro_documento"];

		if ($obj->nro_documento != $nro_documento){
			$query = $obj->RegistroXAtributo('nro_documento',$nro_documento);
			$cant = $obj->db->num_rows($query);
			//error_log($nro_documento.' - '.$cant);
			if ($cant > 0){
				$rta = 'false';
			}else{
				$rta = 'true';
			}
		}else{
			$rta = 'true';
		}
	break;
	default:
		$rta = 'false';
}


echo $rta


?>
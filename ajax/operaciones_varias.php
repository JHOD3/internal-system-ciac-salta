<?php
require_once("../engine/config.php");
require_once("../engine/restringir_acceso.php");
requerir_class("tpl","querys","mysql","estructura");

$tipo = $_POST["tipo"];

switch($tipo){
	case 'TrabajaConOS':
		requerir_class('medicos_obras_sociales');

		$id_medico = $_POST['id_medico'];

		if (isset($_POST['id_os']))
			$id_os = $_POST['id_os'];
		else
			$id_os = 0;

		if (isset($_POST['id_plan']))
			$id_plan = $_POST['id_plan'];
		else
			$id_plan = 0;

		$obj = new Medicos_obras_sociales();

		$rta = $obj->Atiende($id_medico, $id_os, $id_plan);


	break;

	case "habilitar":
		$id = $_POST['id'];
		requerir_class('horarios_inhabilitados');
        if (is_array($_POST['id'])) {
            foreach ($_POST['id'] AS $id) {
        		$obj = new Horarios_inhabilitados($id);
        		$rta = $obj->Baja();
            }
        } else {
    		$obj = new Horarios_inhabilitados($id);
    		$rta = $obj->Baja();
        }
	break;
	case 'duplicados':
		requerir_class('turnos');
		$obj_turnos = new Turnos();

		$id_medico = $_POST['id_medico'];
		$id_especialidad = $_POST['id_especialidad'];
		$fecha = $_POST['fecha'];

		$rta = $obj_turnos->Duplicados($id_medico, $id_especialidad, $fecha);
	break;
}

echo $rta;

?>
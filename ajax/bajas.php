<?php
require_once("../engine/config.php");
require_once("../engine/restringir_acceso.php");
requerir_class("tpl","querys","mysql","estructura");

$tabla = $_POST["tabla"];
$id = $_POST["id"];

requerir_class($tabla);

$clase = ucwords($tabla);
$obj = new $clase($id);

switch ($tabla) {
    case "especialidades":
    case "estudios":
    case "medicos":
    case "sectores":
    case "subsectores":
    case "agendas":
    case "mantenimientos":
        $rta = $obj->Inhabilitar();
        break;
    default:
        $rta = $obj->Baja();
        break;
}

#AFTER
switch ($tabla) {
    case "mantenimientos":
        $date = date("Y-m-d H:i:s");
		$query_string2 = "
            INSERT INTO mantenimhistoricos
            SELECT
                null,
                id_mantenimientos,
				'{$date}',
                id_sectores,
                solicitador,
                CONCAT(tarea, ' (ELIMINADO)'),
                especialista,
                observaciones,
                1
            FROM
                mantenimientos
            WHERE
                id_mantenimientos = '".$id."'
            LIMIT 1
        ";
		$obj->db->consulta($query_string2);
        break;
}

echo $rta;

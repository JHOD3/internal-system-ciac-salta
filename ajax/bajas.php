<?php
require_once("../engine/config.php");
require_once("../engine/restringir_acceso.php");
requerir_class("tpl","querys","mysql","estructura");
$this_db = new MySQL();

$tabla = $_POST["tabla"];
$id = $_POST["id"];

requerir_class($tabla);

$clase = ucwords($tabla);
$obj = new $clase($id);

switch ($tabla) {
    case "especialidades":
    case "estudios":
    case "medicos":
    case "agendas":
    case "mantenimientos":
    case "usuarios":
    case "pacientes_observaciones":
    case "planes_de_contingencia":
        $rta = $obj->Inhabilitar();
        break;
    case "notas_impresion":
        $rta = $obj->Inhabilitar();
        $query_string = <<<SQL
            UPDATE notas_impresion_estudios
            SET estado = 0
            WHERE id_notas_impresion = '{$id}';
SQL;
        $this_db->consulta($query_string);
        break;
    case "horarios_inhabilitados":
        if (trim($_POST['borrar_horarios_inhabilitados'])) {
            $query_string = <<<SQL
                UPDATE
                    {$tabla}
                SET
                    estado = 0
                WHERE
                    id_horarios_inhabilitados IN ({$_POST['borrar_horarios_inhabilitados']})
SQL;
    		$obj->db->consulta($query_string);
        }
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
                1,
                '".$_SESSION['ID_USUARIO']."'
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

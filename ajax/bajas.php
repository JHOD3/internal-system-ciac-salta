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
    case "tareas_configuracion":
    case "tareas_requisitos":
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
    case "tareas_requisitos":
        /******************************************************************/
        $query_correr_ids = <<<SQL
            SELECT id_tareas_configuracion, id_tareas_requisitos
            FROM tareas_requisitos
            WHERE estado = 1
            ORDER BY id_tareas_configuracion, nombre, id_tareas_requisitos
SQL;
		$result = $obj->db->consulta($query_correr_ids);
        $i = 1;
        $id_tareas_configuracion = null;
        while ($row = $obj->db->fetch_assoc($result)) {
            if ($row['id_tareas_configuracion'] != $id_tareas_configuracion) {
                $id_tareas_configuracion = $row['id_tareas_configuracion'];
                $i = 1;
            }
            $query_correr_ids = <<<SQL
                UPDATE tareas_requisitos
                SET nombre = {$i}
                WHERE id_tareas_requisitos = '{$row['id_tareas_requisitos']}'
SQL;
    		$obj->db->consulta($query_correr_ids);
            $i++;
        }
        /******************************************************************/
        break;
}

echo $rta;

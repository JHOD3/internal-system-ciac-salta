<?php
require_once("../engine/config.php");
require_once("../engine/restringir_acceso.php");
requerir_class("tpl","querys","mysql","estructura","json");
$this_db = new MySQL();

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
	case "consultorio":
		$rta = $obj->Consultorio($id);
	break;
	case "disponibles_libre":
		$rta = $obj->DiaLibre($id);
    break;
    case "disponibles_ocupado":
		$rta = $obj->DiaOcupado($id);
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
            case "pacientes_observaciones":
				$id_padre = $_POST["id_padre"];
				$rta = $obj->FormAlta($id_padre);
			break;
			default:
				$rta = $obj->FormAlta();
		}

	break;
}

switch ($tabla) {
    case "notas_impresion":
        $query_string = <<<SQL
            SELECT
                E.id_estudios,
                E.nombre
            FROM
                estudios AS E
            INNER JOIN
                medicos_estudios AS ME
                ON ME.id_estudios = E.id_estudios
            INNER JOIN
                medicos AS M
                ON ME.id_medicos = M.id_medicos
            INNER JOIN
                medicos_horarios AS MH
                ON MH.id_medicos = M.id_medicos
            INNER JOIN
                turnos_tipos AS TT
                ON MH.id_turnos_tipos = TT.id_turnos_tipos
            WHERE
                E.estado = 1 AND
                ME.estado = 1 AND
                M.estado = 1 AND
                MH.estado = 1 AND
                TT.estado = 1 AND
                TT.tipo = 'ESTUDIOS'
            GROUP BY
                E.nombre
            ORDER BY
                E.nombre
SQL;
        $result = $this_db->consulta($query_string);
        $rta.= "<script>\n$(document).ready(function(){\n";
        while ($row = $this_db->fetch_assoc($result)):
            $rta.= "$('#id_estudios').append('<option value=\"{$row['id_estudios']}\">".utf8_encode($row['nombre'])."</option>');\n";
        endwhile;
        switch ($tipo){
            case "editar":
                $query_string = <<<SQL
                    SELECT
                        id_estudios
                    FROM
                        notas_impresion_estudios
                    WHERE
                        estado = 1 AND
                        id_notas_impresion = '{$id}'
SQL;
                $result = $this_db->consulta($query_string);
                while ($row = $this_db->fetch_assoc($result)):
                    $rta.= "$('#id_estudios > option[value=\"{$row['id_estudios']}\"]').attr('selected', 'selected');\n";
                endwhile;
                break;
        }
        $rta.= "});\n</script>\n";
        break;
}

echo $rta;

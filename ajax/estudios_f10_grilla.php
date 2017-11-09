<?php
require_once("../engine/config.php");
require_once("../engine/restringir_acceso.php");
requerir_class("tpl","querys","mysql","estructura","json");

$this_db = new MySQL();

$where_est = array();
$where_osm = array();
$post_est = explode(" ", $_POST['tb_f10_estudio']);
$post_osm = explode(" ", $_POST['tb_f10_obrasocial']);
foreach ($post_est AS $est) {
    $where_est[]= "est.nombre LIKE '%{$est}%'";
}
foreach ($post_osm AS $osm) {
    $where_osm[]= "osm.abreviacion LIKE '%{$osm}%'";
}
$where_est = implode(" OR ", $where_est);
$where_osm = implode(" OR ", $where_osm);
$sql = "
    SELECT
        est.nombre AS est_nombre,
        est.importe AS est_importe,
        est.arancel AS est_arancel,
        relEstMed.particular AS estmed_particular,
        med.nombres AS med_nombres,
        med.apellidos AS med_apellidos,
        relMedOsm.arancel AS medosm_arancel,
        osm.abreviacion AS osm_abreviacion,
        osm.importe_consulta AS osm_importe_consulta
    FROM
        estudios AS est
    INNER JOIN
        medicos_estudios AS relEstMed
        ON relEstMed.id_estudios = est.id_estudios
    INNER JOIN
        medicos AS med
        ON relEstMed.id_medicos = med.id_medicos
    INNER JOIN
        medicos_obras_sociales AS relMedOsm
        ON relMedOsm.id_medicos = med.id_medicos
    INNER JOIN
        obras_sociales AS osm
        ON relMedOsm.id_obras_sociales = osm.id_obras_sociales
    WHERE
        est.estado = 1 AND
        relEstMed.estado = 1 AND
        med.estado = 1 AND
        relMedOsm.estado = 1 AND
        osm.estado = 1 AND
        ({$where_est}) AND
        ({$where_osm})
    ORDER BY
        est.nombre,
        med.nombres,
        med.apellidos
    LIMIT
        16
";
$estudios = $this_db->consulta($sql);
?>
<?php while ($est = $this_db->fetch_array($estudios)): ?>
    <tr class="odd">
        <td><?=utf8_encode($est['est_nombre'])?></td>
        <td style="text-align:right;">$&nbsp;<?=utf8_encode($est['est_importe'])?></td>
        <td style="text-align:right;">$&nbsp;<?=utf8_encode($est['est_arancel'])?></td>
        <td style="text-align:right;">$&nbsp;<?=utf8_encode($est['estmed_particular'])?></td>
        <td><?=utf8_encode($est['osm_abreviacion'])?></td>
        <td style="text-align:right;">$&nbsp;<?=utf8_encode($est['osm_importe_consulta'])?></td>
        <td><?=utf8_encode($est['med_apellidos'].", ".$est['med_nombres'])?></td>
        <td style="text-align:right;">$&nbsp;<?=utf8_encode($est['medosm_arancel'])?></td>
    </tr>
<?php endwhile; ?>

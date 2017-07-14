<?php
require_once("../engine/config.php");
require_once("../engine/restringir_acceso.php");
requerir_class("tpl","querys","mysql","estructura","json");

$this_db = new MySQL();

$fecha = date('Y-m-d');
$hora = date('H:i:s', strtotime('-60 second'));

if ($_GET['first'] == true) {
    $where = "";
    $limit = "LIMIT 3";
} else {
    $where = "tce.hora >= '{$hora}' AND";
    $limit = "";
}

$sql = "
    SELECT
        p.apellidos,
        p.nombres,
        te.nombre AS estado,
        LEFT(tce.hora, 5) AS hora
    FROM
        turnos_cambios_estados AS tce
    INNER JOIN
        turnos AS t
        ON tce.id_turnos = t.id_turnos
    INNER JOIN
        pacientes AS p
        ON t.id_pacientes = p.id_pacientes
    INNER JOIN
        turnos_estados AS te
        ON t.id_turnos_estados = te.id_turnos_estados
    WHERE
        {$where}
        tce.fecha = '{$fecha}' AND
        tce.estado = 1 AND
        tce.id_turnos_estados_nuevos = 2 AND
        tce.id_turnos_estados_viejos != 2 AND
        te.estado = 1 AND
        t.estado = 1 AND
        t.id_turnos_estados = 2 AND
        t.id_medicos = '{$_SESSION['ID_MEDICO']}'
    GROUP BY
        p.id_pacientes
    ORDER BY
        tce.fecha DESC,
        tce.hora DESC
    {$limit}
";

$notificaciones = $this_db->consulta($sql);
$html_notif = "";
$total = 0;
while ($not = $this_db->fetch_assoc($notificaciones)) {
    $total++;
    $html_notif =
        <<<HTML
            <tr>
                <td colspan="2"><label>{$not['estado']}&nbsp;&raquo;&nbsp;{$not['hora']}hs</label></td>
            </tr>
            <tr>
                <td class="border" rowspan="2"><img width="30" src="../files/img/btns/especialidades.png" alt="" /></td>
                <td class="bold border_right">{$not['apellidos']}</td>
            </tr>
            <tr>
                <td class="border_bottom border_right">{$not['nombres']}</td>
            </tr>
HTML
        .
        $html_notif
    ;
}
$html_notif = utf8_encode($html_notif);
if ($total > 0) {
    print "<table class=\"notLayerPac\">{$html_notif}</table>";
    print "<audio autoplay src=\"../files/mp3/ciac-notific.mp3\"></audio>";
}

//EOF notificaciones.php

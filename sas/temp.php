<?php
require_once ("../engine/config.php");
require_once ("../engine/restringir_acceso.php");
requerir_class("tpl","mysql","querys","estructura");

//requerir_class("dias_semana");

require_once("../engine/config.php");

$db = new MySQL();

$SQL = "
    SELECT t.*, u.apellidos, u.nombres, COUNT(t.id_turnos) AS `count`
    FROM turnos AS t
    LEFT JOIN usuarios AS u
        ON u.id_usuarios = t.id_usuarios
    WHERE
        t.id_pacientes IN (
            SELECT id_pacientes
            FROM pacientes
            GROUP BY nro_documento
            HAVING COUNT(id_pacientes) > 1
            ORDER BY COUNT(id_pacientes) DESC
        )
    GROUP BY
        u.id_usuarios
    ORDER BY
        COUNT(t.id_turnos) DESC
";
$result = $db->consulta($SQL);
print " (".$db->num_rows($result).")";
while ($row = $db->fetch_array($result)) {
    print utf8_encode($row['apellidos']." ".$row['nombres'].": ".$row['count']."<br />");
}

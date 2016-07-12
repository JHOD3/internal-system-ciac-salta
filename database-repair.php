<?php
require_once ("engine/config.php");
require_once ("engine/restringir_acceso.php");
requerir_class("tpl","mysql","querys","estructura");

//requerir_class("dias_semana");

require_once("engine/config.php");

$db = new MySQL();

print "<strong>DNI Duplicados</strong>";
$SQL1 = "
    SELECT nro_documento
    FROM pacientes
    WHERE estado = 1
    GROUP BY nro_documento
    HAVING COUNT(id_pacientes) > 1
    ORDER BY COUNT(id_pacientes) DESC
";
$result = $db->consulta($SQL1);
print " (".$db->num_rows($result).")";
$aPacientesId = array();
while ($row = $db->fetch_array($result)) {
    print ", ".$row['nro_documento'];
    $aPacientesId[] = $row['nro_documento'];
}

print "<br /><br /><strong>Fechas en que se dieron turnos con un DNI Duplicado</strong>";

$SQL2 = "
    SELECT *
    FROM turnos
    WHERE
        id_pacientes IN (
            SELECT id_pacientes
            FROM pacientes
            WHERE estado = 1
            GROUP BY nro_documento
            HAVING COUNT(id_pacientes) > 1
            ORDER BY COUNT(id_pacientes) DESC
        )
    ORDER BY
        fecha ASC
";
$result = $db->consulta($SQL2);
print " (".$db->num_rows($result).")";
while ($row = $db->fetch_array($result)) {
    print ", ".implode("/", array_reverse(explode("-", $row['fecha'])));
}

print "<br /><br /><strong>Pacientes duplicados que nunca pidieron turnos</strong>";
$SQL3 = "
    SELECT nro_documento
    FROM pacientes
    WHERE
        estado = 1 AND
        id_pacientes NOT IN (
            SELECT id_pacientes
            FROM turnos
        )
    GROUP BY nro_documento
    HAVING COUNT(id_pacientes) > 1
    ORDER BY COUNT(id_pacientes) DESC
";
$result = $db->consulta($SQL3);
print " (".$db->num_rows($result).")";
while ($row = $db->fetch_array($result)) {
    print ", ".$row['nro_documento'];
}

if (count($aPacientesId) > 0) {
    $SQL_update = "
        UPDATE pacientes
        SET estado = 0
        WHERE
            nro_documento IN (".implode(", ", $aPacientesId).")
    ";
    print "<pre>{$SQL_update}</pre>";
    $result = $db->consulta($SQL_update);
    var_dump($result);
}

<?php
require_once ("../engine/config.php");
require_once ("../engine/restringir_acceso.php");
requerir_class("tpl","mysql","querys","estructura","medicos","usuarios");
$this_db = new MySQL();

//requerir_class("dias_semana");

$obj_estructura = new Estructura();
//$obj_dias_semana = new Dias_semana();

$htm_gral = $obj_estructura->html("sas/gral");
$htm_index = $obj_estructura->html("sas/index");
$htm_menu_tablas = $obj_estructura->html("menu/tablas_sas_{$_SESSION['SUPERUSER']}");

/*$htm_index->Asigna("FORM_HORARIOS", $obj_dias_semana->FormHorarios());*/

$desde = $_POST['desde'];
$d = DateTime::createFromFormat('d/m/Y', $desde);
if (!$d or $d->format($format) != $date or strlen($desde) != 10) {
    $desde = date('d/m/Y', strtotime('-1 month +1 day'));
}

$hasta = $_POST['hasta'];
$d = DateTime::createFromFormat('d/m/Y', $hasta);
if (!$d or $d->format($format) != $date or strlen($hasta) != 10) {
    $hasta = date('d/m/Y');
}

$d = implode("-", array_reverse(explode("/", $desde)));
$h = implode("-", array_reverse(explode("/", $hasta)));

$date = date("m-d");
if ($_SESSION['SUPERUSER'] > 0) {
    $query_string = <<<SQL
        SELECT
            SUBSTR(U.fechanac, 6, 5) AS mesdia
        FROM
            usuarios U
        WHERE
            U.estado = 1 AND
            U.fechanac IS NOT NULL AND
            SUBSTR(U.fechanac, 6, 5) >= '{$date}'
        UNION SELECT
            SUBSTR(M.fechanac, 6, 5) AS mesdia
        FROM
            medicos M
        WHERE
            M.estado = 1 AND
            M.fechanac IS NOT NULL AND
            SUBSTR(M.fechanac, 6, 5) >= '{$date}'
        GROUP BY
            mesdia
        ORDER BY
            mesdia ASC
        LIMIT 4
SQL;
    $result = $this_db->consulta($query_string);
    if ($this_db->num_rows($result) > 0) {
        $aDates = array();
        while ($row = $this_db->fetch_assoc($result)) {
            $aDates[] = "'".$row['mesdia']."'";
        }
        $aDates = implode(",", $aDates);
        if (count($aDates) > 0) {
            $query_string = <<<SQL
                SELECT
                    CONCAT(U.nombres, ' ', U.apellidos) AS nombres,
                    SUBSTR(U.fechanac, 6, 5) AS mesdia
                FROM
                    usuarios U
                WHERE
                    U.estado = 1 AND
                    U.fechanac IS NOT NULL AND
                    SUBSTR(U.fechanac, 6, 5) IN ({$aDates})
                UNION SELECT
                    CONCAT(M.saludo, ' ', M.nombres, ' ', M.apellidos) AS nombres,
                    SUBSTR(M.fechanac, 6, 5) AS mesdia
                FROM
                    medicos M
                WHERE
                    M.estado = 1 AND
                    M.fechanac IS NOT NULL AND
                    SUBSTR(M.fechanac, 6, 5) IN ({$aDates})
                ORDER BY
                    mesdia ASC
SQL;
            $result = $this_db->consulta($query_string);
            if ($this_db->num_rows($result) > 0) {
                $aDates = "";
                while ($row = $this_db->fetch_assoc($result)) {
                    if ($row['mesdia'] == date("m-d")) {
                        $aDates.= '<strong style="color:red">';
                    }
                    $aDates.= '&raquo;&nbsp;';
                    $aDates.= date("d/m", strtotime(date("Y")."-".$row['mesdia']));
                    $aDates.= "&nbsp;-&nbsp;";
                    $aDates.= utf8_encode($row['nombres']);
                    if ($row['mesdia'] == date("m-d")) {
                        $aDates.= '</strong>';
                    }
                    $aDates.= "<br />\n";
                }
            }
            $cumples.= "<div style=\"color: white; width: 400px; min-height: 120px; background-size: 400px auto; background-repeat: no-repeat; padding: 20px; background-image:url(../files/img/1535146937-torta-cumpleaos-istock.jpg);\">";
            $cumples.= "<strong style=\"color: green;\">Próximos cumpleaños:</strong><br />\n";
            $cumples.= $aDates;
            $cumples.= "</div><br /><br />\n";
        } else {
            $cumples = "";
        }
    } else {
        $cumples = "";
    }
} else {
    $cumples = "";
}
if ($_SESSION['SUPERUSER'] == '3') {
    $obj_medicos = new Medicos();
    $htm_index->Asigna(
        "DROP_MEDICOS",
        $cumples.
        "Reporte: ".
        utf8_encode($obj_medicos->Drop("", $_GET['id_medicos'])).
        "<br /><br />"
    );
} else {
    $htm_index->Asigna("DROP_MEDICOS", $cumples);
}
$htm_index->Asigna("DATE_TODAY", date("d/m/Y"));
$htm_index->Asigna("DATE_DESDE", $desde);
$htm_index->Asigna("DATE_HASTA", $hasta);

$dataMOT = $obj_estructura->obtMotivosDeInhabilitaciones();
$htm_index->Asigna("MOTIVOS", $dataMOT);

if (in_array($_SESSION['SUPERUSER'], array(1, 2))) {
    list($dataNDMli, $dataNDMdiv) = $obj_estructura->obtNotificacionesDeMantenimientos();
    $htm_index->Asigna("dataNDMli", $dataNDMli);
    $htm_index->Asigna("dataNDMdiv", $dataNDMdiv);
} else {
    $htm_index->Asigna("dataNDMli", '');
    $htm_index->Asigna("dataNDMdiv", '');
}

if ($_GET['id_medicos']) {
    $ses_id_medico = $_GET['id_medicos'];
    $html_graph.= '<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>';
    $desde_text = $desde;
    $hasta_text = $hasta;
    $desde = $d;
    $hasta = $h;
    include("../sam/estadisticas.medicos.inc.php");
    $htm_index->Asigna("ESTADISTICAS_GRAPH", ($html_graph));
    $htm_index->Asigna("get_id_medicos", "?id_medicos={$_GET['id_medicos']}");

    $htm_index->Asigna("TOT_NUMROWS", 0);
    $htm_index->Asigna("TPM_NUMROWS", 0);
    $htm_index->Asigna("TPD_NUMROWS", 0);
    $htm_index->Asigna("OST_NUMROWS", 0);
    $htm_index->Asigna("EST_NUMROWS", 0);
} else {
    $dataTOT = $obj_estructura->obtTurnosOtorgadosTotales($d, $h, $_SESSION['ID_USUARIO']);
    $dataTPD = $obj_estructura->obtTurnosOtorgadosPorDia($d, $h, $_SESSION['ID_USUARIO']);
    $dataTPM = $obj_estructura->obtTurnosPorMedicos($d, $h, $_SESSION['ID_USUARIO']);
    $dataOST = $obj_estructura->obtTurnosOtorgadosPorOS($d, $h, $_SESSION['ID_USUARIO']);
    $dataEST = $obj_estructura->obtTurnosOtorgadosPorEST($d, $h, $_SESSION['ID_USUARIO']);
    $dataENC = $obj_estructura->obtTurnosOtorgadosPorENC($d, $h, $_SESSION['ID_USUARIO']);
    $dataDER = $obj_estructura->obtTurnosOtorgadosPorDER($d, $h, $_SESSION['ID_USUARIO']);
    $htm_index->Asigna("TOT", $dataTOT[0]);
    $htm_index->Asigna("TPD", $dataTPD[0]);
    $htm_index->Asigna("TPM", $dataTPM[0]);
    $htm_index->Asigna("OST", $dataOST[0]);
    $htm_index->Asigna("EST", $dataEST[0]);
    $htm_index->Asigna("ENC", $dataENC[0]);
    $htm_index->Asigna("DER", $dataDER[0]);
    $htm_index->Asigna("TOT_NUMROWS", 10 + ($dataTOT[1] * 41));
    $htm_index->Asigna("TPD_NUMROWS", 10 + (6 * 41));
    $htm_index->Asigna("TPM_NUMROWS", 10 + ($dataTPM[1] * 41));
    $htm_index->Asigna("OST_NUMROWS", 10 + ($dataOST[1] * 41));
    $htm_index->Asigna("EST_NUMROWS", 10 + ($dataEST[1] * 41));
    $htm_index->Asigna("ENC_NUMROWS", 10 + (4 * 41));
    $htm_index->Asigna("DER_NUMROWS", 10 + ($dataDER[1] * 41));

    $htm_index->Asigna("ESTADISTICAS_GRAPH", '');
    $htm_index->Asigna("get_id_medicos", '');
}

if (
    $_SESSION['SISTEMA'] == 'sas' and
    $_SESSION['SUPERUSER'] < 2
) {
    $BTN_MA = ' style="display: none;"';
} else {
    $BTN_MA = '';
}

$query_string = <<<SQL
    SELECT
        M.id_medicos,
        M.saludo,
        M.nombres,
        M.apellidos,
        ME.id_medicos_especialidades,
        E.nombre AS especialidad
    FROM
        medicos AS M
    INNER JOIN
        medicos_especialidades AS ME
        ON ME.id_medicos = M.id_medicos
    INNER JOIN
        especialidades AS E
        ON ME.id_especialidades = E.id_especialidades
    WHERE
        M.estado = 1 AND
        ME.estado = 1 AND (
            E.estado = 1 OR
            E.id_especialidades IN (60, 61)
		)
    ORDER BY
        M.nombres,
        M.apellidos,
        E.nombre
SQL;
$query = $this_db->consulta($query_string);
$data = "";
while ($row = $this_db->fetch_array($query)) {
    $data.=
        "sAT.push({id_medicos: '".
        utf8_encode($row['id_medicos']).
        "', id_medicos_especialidades: '".
        utf8_encode($row['id_medicos_especialidades']).
        "', saludo: '".
        utf8_encode($row['saludo']).
        "', nombres:'".
        utf8_encode($row['nombres']).
        "', apellidos:'".
        utf8_encode($row['apellidos']).
        "', especialidad:'".
        utf8_encode($row['especialidad']).
        "'});\n"
    ;
}

$htm_medicos_autocomplete = "<script>var sAT = new Array();{$data}</script>";

$htm_index->Asigna("BTN_MA", $BTN_MA);
$htm_index->Asigna("AGENDAS_OPTIONS", $obj_estructura->obtAGENDAS_OPTIONS());
$htm_index->Asigna("FECHA", ucfirst(strftime("%A %d de ")).ucfirst(strftime("%B del %Y")));
$htm_index->Asigna("USUARIO_APELLIDOS", utf8_encode($_SESSION['APELLIDOS']));
$htm_index->Asigna("USUARIO_NOMBRES", utf8_encode($_SESSION['NOMBRES']));

// FELICITACIONES DE FELIZ CUMPLEAÑOS
if (!$_SESSION['felicitado']) {
    $html_cumple = '';
    $cumple = false;
    switch ($_SESSION['TIPO_USR']) {
        case 'U':
            $obj_usuarios = new Usuarios($_SESSION['ID_USUARIO']);
            if (substr($obj_usuarios->fechanac, 5, 5) == date("m-d")) {
                $cumple = true;
            }
            break;
    }
    if ($cumple) {
        ob_start();
        ?>
        <script>
        $('body').prepend('<div class="imgHB"><a href="#"><img src="../files/img/bonita-tarjeta-cumpleanos-elementos_23-2147551587.jpg" alt="" style="position:absolute;z-index:999999;" /></a></div>');
        </script>
        <?php
        $html_cumple.= ob_get_clean();
    }
    include("../sas/salutaciones.php");
}

$htm_index->Asigna("MENU_TABLAS", $html_cumple.$htm_menu_tablas->Muestra().$htm_medicos_autocomplete);
$htm_gral->Asigna("CUERPO", $htm_index->Muestra());

CargarVariablesGrales($htm_gral, $tipo = "");

echo $htm_gral->Muestra();

//CONVERTIR NUMEROS EN LETRAS
//echo $obj_estructura->NroLetras(1);

<?php
require_once ("../engine/config.php");
require_once ("../engine/restringir_acceso.php");
requerir_class("tpl","mysql","querys","estructura","medicos","usuarios","menus");
$this_db = new MySQL();
//requerir_class("dias_semana");

$obj_estructura = new Estructura();
//$obj_dias_semana = new Dias_semana();

$htm_gral = $obj_estructura->html("sas/gral");
$htm_index = $obj_estructura->html("sas/index");
//$htm_menu_tablas = $obj_estructura->html("menu/tablas_sas_{$_SESSION['SUPERUSER']}");
/*$htm_index->Asigna("FORM_HORARIOS", $obj_dias_semana->FormHorarios());*/

//MENU
$obj_menu = new Menus();
$htm_menu_tablas = $obj_menu->armarMenu();

//CUMPLES
$desde = isset($_POST['desde']) ? $_POST['desde'] : null;
$d = DateTime::createFromFormat('d/m/Y', $desde);
if (!$d or $d->format($format) != $date or strlen($desde) != 10) {
    $desde = date('d/m/Y', strtotime('-1 month +1 day'));
}

$hasta = isset($_POST['hasta']) ? $_POST['hasta'] : null;
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
            $cumples= "<div style=\"color: white; width: 316px; text-align: left; min-height: 120px; background-size: 358px auto; background-repeat: no-repeat; padding: 20px; background-color: #d7e550; background-image:url(../files/img/1535146937-torta-cumpleaos-istock.png);\">";
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
//CUMPLES PASADOS



$date = date("m-d");
if ($_SESSION['SUPERUSER'] > 0) {
    $query_string_dos = <<<SQL
        SELECT
            SUBSTR(U.fechanac, 6, 5) AS mesdia
        FROM
            usuarios U
        WHERE
            U.estado = 1 AND
            U.fechanac IS NOT NULL AND
            SUBSTR(U.fechanac, 6, 5) <= '{$date}'
        UNION SELECT
            SUBSTR(M.fechanac, 6, 5) AS mesdia
        FROM
            medicos M
        WHERE
            M.estado = 1 AND
            M.fechanac IS NOT NULL AND
            SUBSTR(M.fechanac, 6, 5) <= '{$date}'
        GROUP BY
            mesdia
        ORDER BY
            mesdia DESC
        LIMIT 4
SQL;
    $result_dos = $this_db->consulta($query_string_dos);
    if ($this_db->num_rows($result) > 0) {
        $aDates_dos = array();
        while ($row_dos = $this_db->fetch_assoc($result_dos)) {
            $aDates_dos[] = "'".$row_dos['mesdia']."'";
        }
        $aDates_dos = implode(",", $aDates_dos);
        if (count($aDates_dos) > 0) {
            $query_string_dos = <<<SQL
                SELECT
                    CONCAT(U.nombres, ' ', U.apellidos) AS nombres,
                    SUBSTR(U.fechanac, 6, 5) AS mesdia
                FROM
                    usuarios U
                WHERE
                    U.estado = 1 AND
                    U.fechanac IS NOT NULL AND
                    SUBSTR(U.fechanac, 6, 5) IN ({$aDates_dos})
                UNION SELECT
                    CONCAT(M.saludo, ' ', M.nombres, ' ', M.apellidos) AS nombres,
                    SUBSTR(M.fechanac, 6, 5) AS mesdia
                FROM
                    medicos M
                WHERE
                    M.estado = 1 AND
                    M.fechanac IS NOT NULL AND
                    SUBSTR(M.fechanac, 6, 5) IN ({$aDates_dos})
                ORDER BY
                    mesdia ASC
SQL;
            $result_dos = $this_db->consulta($query_string_dos);
            if ($this_db->num_rows($result_dos) > 0) {
                $aDates_dos = "";
                while ($row_dos = $this_db->fetch_assoc($result_dos)) {
                    if ($row_dos['mesdia'] == date("m-d")) {
                        $aDates_dos.= '<strong style="color:red">';
                    }
                    $aDates_dos.= '&raquo;&nbsp;';
                    $aDates_dos.= date("d/m", strtotime(date("Y")."-".$row_dos['mesdia']));
                    $aDates_dos.= "&nbsp;-&nbsp;";
                    $aDates_dos.= utf8_encode($row_dos['nombres']);
                    if ($row_dos['mesdia'] == date("m-d")) {
                        $aDates_dos.= '</strong>';
                    }
                    $aDates_dos.= "<br />\n";
                }
            }
            $cumples_dos= "<div style=\"color: white; width: 316px;text-align: left; min-height: 120px; background-size: 358px auto; background-repeat: no-repeat; padding: 20px;background-color: #fa845e9e; background-image:url(../files/img/1535146937-torta-cumpleaos-istock.png);\">";
            $cumples_dos.= "<strong style=\"color: green;\">Cumpleaños pasados:</strong><br />\n";
            $cumples_dos.= $aDates_dos;
            $cumples_dos.= "</div><br /><br />\n";
        } else {
            $cumples_dos = "";
        }
    } else {
        $cumples_dos = "";
    }
} else {
    $cumples_dos = "";
}






//FIN CUMPLES PASADOS
if ($_SESSION['SUPERUSER'] == '3') {
    $obj_medicos = new Medicos();
    $htm_index->Asigna(
        "DROP_MEDICOS",
        $cumples.
       "<br /><br />"
    );
    $htm_index->Asigna(
        "DROP_MEDICOS_DOS",
        $cumples_dos.        
        "<br /><br />"
    );
    if(!isset($_GET['id_medicos'])){
        $_GET['id_medicos'] = null;
    }
    $htm_index->Asigna(
        "DROP_MEDICOS_TRES",
         utf8_encode($obj_medicos->Drop("", $_GET['id_medicos'])).        
        "<br /><br />"
    );

} else {
    $htm_index->Asigna("DROP_MEDICOS", $cumples);
    $htm_index->Asigna("DROP_MEDICOS_DOS", $cumples_dos);
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
    //Usuario 0 - jsuaina
    if ($_SESSION['ID_USUARIO']==0){
        $htm_estadistica_medico = $obj_estructura->html("sas/estadisticasMedicosSAS");
        $htm_index->Asigna("ESTADISTICASM_GRAPH", $htm_estadistica_medico->Muestra());
        $htm_index->Asigna("ESTADISTICA_MEDICOS_OPTIONS", $obj_estructura->obtMEDICOS_OPTIONS());
    }
    else{
        $htm_index->Asigna("ESTADISTICASM_GRAPH", '');
    }
    $dataTOT = $obj_estructura->obtTurnosOtorgadosTotales($d, $h, $_SESSION['ID_USUARIO']);
    $dataTPD = $obj_estructura->obtTurnosOtorgadosPorDia($d, $h, $_SESSION['ID_USUARIO']);
    $dataTPM = $obj_estructura->obtTurnosPorMedicos($d, $h, $_SESSION['ID_USUARIO']);
    $dataOST = $obj_estructura->obtTurnosOtorgadosPorOS($d, $h, $_SESSION['ID_USUARIO']);
    $dataEST = $obj_estructura->obtTurnosOtorgadosPorEST($d, $h, $_SESSION['ID_USUARIO']);
    $dataENC = $obj_estructura->obtTurnosOtorgadosPorENC($d, $h, $_SESSION['ID_USUARIO']);
    $dataDER = $obj_estructura->obtTurnosOtorgadosPorDER($d, $h, $_SESSION['ID_USUARIO']);

    $dataTOTTable = explode(',[',str_replace(array("]", "'"), '', $dataTOT[0]));
    $totalTurnosTOT = 0;
    $Tabla_TOT = '<table class="table table-striped datatableindex" id="table_TOT" width="100" style="display: none">';
        $Tabla_TOT .= '<thead>';
            $Tabla_TOT .= '<th>Gestor</th>';
            $Tabla_TOT .= '<th>Turnos</th>';
        $Tabla_TOT .= '</thead>';
        $Tabla_TOT .= '<tbody>';
            foreach ($dataTOTTable as $key => $TOT){
                $TOTExplode = explode(',',$TOT);
                if(!empty($TOTExplode[0])) {
                    $Tabla_TOT .= '<tr>';
                        $Tabla_TOT .= '<td>' . $TOTExplode[0] . '</td>';
                        $Tabla_TOT .= '<td>' . $TOTExplode[1] . '</td>';
                    $Tabla_TOT .= '</tr>';
                    $totalTurnosTOT = $totalTurnosTOT + $TOTExplode[1];
                }
            }
        $Tabla_TOT .= '<tbody>';
        $Tabla_TOT .= '<tfoot>';
            $Tabla_TOT .= '<tr>';
                $Tabla_TOT .= '<td><strong>Total Turnos</strong></td>';
                $Tabla_TOT .= '<td>' . $totalTurnosTOT . '</td>';
            $Tabla_TOT .= '</tr>';
        $Tabla_TOT .= '</tfoot>';
    $Tabla_TOT .= '</table>';
    $htm_index->Asigna("TOT_TABLE", $Tabla_TOT);

    ///////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////
    $dataTPDTable = explode(',[',str_replace(array("]", "'"), '', $dataTPD[0]));
    $totalTurnosTPD = 0;
    $Tabla_TPD = '<table class="table table-striped datatableindex" id="table_TPD" width="100" style="display: none">';
        $Tabla_TPD .= '<thead>';
            $Tabla_TPD .= '<th>DIA</th>';
            $Tabla_TPD .= '<th>Turnos</th>';
        $Tabla_TPD .= '</thead>';
        $Tabla_TPD .= '<tbody>';
            foreach ($dataTPDTable as $key => $TPD){
                $TPDExplode = explode(',',$TPD);
                if(!empty($TPDExplode[0])) {
                    $Tabla_TPD .= '<tr>';
                    $Tabla_TPD .= '<td>' . $TPDExplode[0] . '</td>';
                    $Tabla_TPD .= '<td>' . $TPDExplode[1] . '</td>';
                    $Tabla_TPD .= '</tr>';
                    $totalTurnosTPD = $totalTurnosTPD + $TPDExplode[1];
                }
            }
        $Tabla_TPD .= '<tbody>';
        $Tabla_TPD .= '<tfoot>';
            $Tabla_TPD .= '<tr>';
                $Tabla_TPD .= '<td><strong>Total Turnos</strong></td>';
                $Tabla_TPD .= '<td>' . $totalTurnosTPD . '</td>';
            $Tabla_TPD .= '</tr>';
        $Tabla_TPD .= '</tfoot>';
    $Tabla_TPD .= '</table>';
    $htm_index->Asigna("TPD_TABLE", $Tabla_TPD);

    ///////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////
    $dataTPMTable = explode(',[',str_replace(array("]", "'"), '', $dataTPM[0]));

    $totalTurnosTPM = 0;
    $Tabla_TPM = '<table class="table table-striped datatableindex" id="table_TPM" width="100" style="display: none">';
        $Tabla_TPM .= '<thead>';
            $Tabla_TPM .= '<th>DR.</th>';
            $Tabla_TPM .= '<th>Turnos</th>';
        $Tabla_TPM .= '</thead>';
        $Tabla_TPM .= '<tbody>';
            foreach ($dataTPMTable as $key => $TPM){
                $TPMExplode = explode(',',$TPM);
                if (!empty($TPMExplode[0])) {
                    $Tabla_TPM .= '<tr>';
                        $Tabla_TPM .= '<td>' . $TPMExplode[0] . '</td>';
                        $Tabla_TPM .= '<td>' . $TPMExplode[1] . '</td>';
                    $Tabla_TPM .= '</tr>';
                    $totalTurnosTPM = $totalTurnosTPM +$TPMExplode[1];
                }

            }
        $Tabla_TPM .= '<tbody>';
        $Tabla_TPM .= '<tfoot>';
            $Tabla_TPM .= '<tr>';
                $Tabla_TPM .= '<td><strong>Total Turnos</strong></td>';
                $Tabla_TPM .= '<td>' . $totalTurnosTPM . '</td>';
            $Tabla_TPM .= '</tr>';
        $Tabla_TPM .= '</tfoot>';
    $Tabla_TPM .= '</table>';
    $htm_index->Asigna("TPM_TABLE", $Tabla_TPM);

    ///////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////
    $dataOSTTable = explode(',[',str_replace(array("]", "'"), '', $dataOST[0]));

    $totalTurnosOST = 0;
    $Tabla_OST = '<table class="table table-striped datatableindex" id="table_OST" width="100" style="display: none">';
    $Tabla_OST .= '<thead>';
    $Tabla_OST .= '<th>Obra Social</th>';
    $Tabla_OST .= '<th>Turnos</th>';
    $Tabla_OST .= '</thead>';
    $Tabla_OST .= '<tbody>';
    foreach ($dataOSTTable as $key => $OST){
        $OSTExplode = explode(',',$OST);
        if (!empty($OSTExplode[0])) {
            $Tabla_OST .= '<tr>';
            $Tabla_OST .= '<td>' . $OSTExplode[0] . '</td>';
            $Tabla_OST .= '<td>' . $OSTExplode[1] . '</td>';
            $Tabla_OST .= '</tr>';
            $totalTurnosOST = $totalTurnosOST +$OSTExplode[1];
        }

    }
    $Tabla_OST .= '<tbody>';
    $Tabla_OST .= '<tfoot>';
    $Tabla_OST .= '<tr>';
    $Tabla_OST .= '<td><strong>Total Turnos</strong></td>';
    $Tabla_OST .= '<td>' . $totalTurnosOST . '</td>';
    $Tabla_OST .= '</tr>';
    $Tabla_OST .= '</tfoot>';
    $Tabla_OST .= '</table>';
    $htm_index->Asigna("OST_TABLE", $Tabla_OST);

    ///////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////
    $dataESTTable = explode(',[',str_replace(array("]", "'"), '', $dataEST[0]));

    $totalTurnosEST = 0;
    $Tabla_EST = '<table class="table table-striped datatableindex" id="table_EST" width="100" style="display: none">';
    $Tabla_EST .= '<thead>';
    $Tabla_EST .= '<th>Estudio</th>';
    $Tabla_EST .= '<th>Turnos</th>';
    $Tabla_EST .= '</thead>';
    $Tabla_EST .= '<tbody>';
    foreach ($dataESTTable as $key => $EST){
        $ESTExplode = explode(',',$EST);
        if (!empty($ESTExplode[0])) {
            $Tabla_EST .= '<tr>';
            $Tabla_EST .= '<td>' . $ESTExplode[0] . '</td>';
            $Tabla_EST .= '<td>' . $ESTExplode[1] . '</td>';
            $Tabla_EST .= '</tr>';
            $totalTurnosEST = $totalTurnosEST +$ESTExplode[1];
        }

    }
    $Tabla_EST .= '<tbody>';
    $Tabla_EST .= '<tfoot>';
    $Tabla_EST .= '<tr>';
    $Tabla_EST .= '<td><strong>Total Turnos</strong></td>';
    $Tabla_EST .= '<td>' . $totalTurnosEST . '</td>';
    $Tabla_EST .= '</tr>';
    $Tabla_EST .= '</tfoot>';
    $Tabla_EST .= '</table>';
    $htm_index->Asigna("EST_TABLE", $Tabla_EST);

    ///////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////
    $dataENCTable = explode(',[',str_replace(array("]", "'"), '', $dataENC[0]));

    $totalTurnosENC = 0;
    $Tabla_ENC = '<table class="table table-striped datatableindex" id="table_ENC" width="100" style="display: none">';
    $Tabla_ENC .= '<thead>';
    $Tabla_ENC .= '<th>Preguntas</th>';
    $Tabla_ENC .= '<th>Respuestas</th>';
    $Tabla_ENC .= '</thead>';
    $Tabla_ENC .= '<tbody>';
    foreach ($dataENCTable as $key => $ENC){
        $ENCExplode = explode(',',$ENC);
        if (!empty($ENCExplode[0])) {
            $Tabla_ENC .= '<tr>';
            $Tabla_ENC .= '<td>' . $ENCExplode[0] . '</td>';
            $Tabla_ENC .= '<td>' . $ENCExplode[1] . '</td>';
            $Tabla_ENC .= '</tr>';
            $totalTurnosENC = $totalTurnosENC +$ENCExplode[1];
        }

    }
    $Tabla_ENC .= '<tbody>';
    $Tabla_ENC .= '<tfoot>';
    $Tabla_ENC .= '<tr>';
    $Tabla_ENC .= '<td><strong>Total respuestas</strong></td>';
    $Tabla_ENC .= '<td>' . $totalTurnosENC . '</td>';
    $Tabla_ENC .= '</tr>';
    $Tabla_ENC .= '</tfoot>';
    $Tabla_ENC .= '</table>';
    $htm_index->Asigna("ENC_TABLE", $Tabla_ENC);

    ///////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////
    $dataDERTable = explode(',[',str_replace(array("]", "'"), '', $dataDER[0]));

    $totalTurnosDER = 0;
    $Tabla_DER = '<table class="table table-striped datatableindex" id="table_DER" width="100" style="display: none">';
    $Tabla_DER .= '<thead>';
    $Tabla_DER .= '<th>Dr</th>';
    $Tabla_DER .= '<th>Respuestas</th>';
    $Tabla_DER .= '</thead>';
    $Tabla_DER .= '<tbody>';
    foreach ($dataDERTable as $key => $DER){
        $DERExplode = explode(',',$DER);
        if (!empty($DERExplode[0])) {
            $Tabla_DER .= '<tr>';
            $Tabla_DER .= '<td>' . $DERExplode[0].' '.$DERExplode[1]. '</td>';
            $Tabla_DER .= '<td>' . $DERExplode[2] . '</td>';
            $Tabla_DER .= '</tr>';
            $totalTurnosDER = $totalTurnosDER +$DERExplode[2];
        }

    }
    $Tabla_DER .= '<tbody>';
    $Tabla_DER .= '<tfoot>';
    $Tabla_DER .= '<tr>';
    $Tabla_DER .= '<td><strong>Total respuestas</strong></td>';
    $Tabla_DER .= '<td>' . $totalTurnosDER . '</td>';
    $Tabla_DER .= '</tr>';
    $Tabla_DER .= '</tfoot>';
    $Tabla_DER .= '</table>';
    $htm_index->Asigna("DER_TABLE", $Tabla_DER);

    ///////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////

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


$html_cumple = '';
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
    include("salutaciones.php");
}

$htm_index->Asigna("MENU_TABLAS", $html_cumple.$htm_menu_tablas->Muestra().$htm_medicos_autocomplete);
$htm_gral->Asigna("CUERPO", $htm_index->Muestra());

CargarVariablesGrales($htm_gral, $tipo = "");

echo $htm_gral->Muestra();

//CONVERTIR NUMEROS EN LETRAS
//echo $obj_estructura->NroLetras(1);

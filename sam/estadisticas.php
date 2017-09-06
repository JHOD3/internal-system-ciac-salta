<?php

$desde = $_POST['desde'];
$d = DateTime::createFromFormat('Y-m-d', $desde);
if (!$d or $d->format($format) != $date or strlen($desde) != 10) {
    $desde = date('Y-m-d', strtotime('-1 month +1 day'));
}
$desde_text = date('d/m/Y', strtotime($desde));

$hasta = $_POST['hasta'];
$d = DateTime::createFromFormat('Y-m-d', $hasta);
if (!$d or $d->format($format) != $date or strlen($hasta) != 10) {
    $hasta = date('Y-m-d');
}
$hasta_text = date('d/m/Y', strtotime($hasta));

$html_graph = <<<EOT
    <form style="margin: 0 auto;width:480px;" action="index.php?show=estadisticas" method="post">
        Desde: <input type="text" name="desde" value="{$desde}" style="width:150px;" />
        Hasta: <input type="text" name="hasta" value="{$hasta}" style="width:150px;" />
        <input type="submit" value="ok"/>
    </form>
EOT;

$this_db = new MySQL();
$html_graph.= '<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>';
if ($_GET['show'] != 'estadisticas') {

    $html_button = <<<EOT
<a id="btnEst" href="index.php?show=estadisticas" class="btn" data-nombre="estadisticas" style="float:left!important;">
	<img src="../files/img/btns/estadisticas.png" width="30" />
    <span>Estad&iacute;sticas</span>
</a>
EOT;

} else {

    $html_graph.= "<script>\$(document).ready(function(){setTimeout(function(){\$('html, body').animate({scrollTop:460}, 920);}, 1000)});</script>";

    $html_button = <<<EOT
<a id="btnEst" href="index.php" class="btn" data-nombre="estadisticas" style="float:left!important;">
	<img src="../files/img/btns/estadisticas.png" width="30" />
    <span>Ocultar Estad.</span>
</a>
EOT;
    $ses_id_medico = $_SESSION['ID_MEDICO'];
    include("estadisticas.medicos.inc.php");
}

$htm_gral->Asigna("ESTADISTICAS_BUTTON", utf8_encode($html_button));
$htm_gral->Asigna("ESTADISTICAS_GRAPH", utf8_encode($html_graph));

<?php
require_once ("../engine/config.php");
require_once ("../engine/restringir_acceso.php");
requerir_class("tpl","mysql","querys","estructura");
$this_db = new MySQL();

requerir_class("medicos");
$obj_medico = new Medicos($_SESSION["ID_MEDICO"]);

$obj_estructura = new Estructura();

$htm_gral = $obj_estructura->html("sam/gral");
$htm_index = $obj_estructura->html("sam/index");
$htm_menu_tablas = $obj_estructura->html("menu/tablas_sam");

$htm_index->Asigna("DETALLE_MEDICO", $obj_medico->Detalle("corto", "", "sam"));

// FELICITACIONES DE FELIZ CUMPLEAÑOS
if (!$_SESSION['felicitado']) {
    $html_cumple = '';
    $cumple = false;
    switch ($_SESSION['TIPO_USR']) {
        case 'M':
            $obj_medicos = new Medicos($_SESSION['ID_MEDICO']);
            if (substr($obj_medicos->fechanac, 5, 5) == date("m-d")) {
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

$htm_index->Asigna("MENU_TABLAS", $html_cumple.$htm_menu_tablas->Muestra());
$htm_index->Asigna("DIV_HIDE_OPEN", "");
$htm_index->Asigna("DIV_HIDE_CLOSE", "");

$htm_gral->Asigna("CUERPO", $htm_index->Muestra());

CargarVariablesGrales($htm_gral, $tipo = "");

include("estadisticas.php");

echo utf8_decode($htm_gral->Muestra());

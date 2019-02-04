<?php
require_once ("../engine/config.php");
require_once ("../engine/restringir_acceso.php");
requerir_class("tpl","mysql","querys","estructura");

requerir_class("medicos");
$obj_medico = new Medicos($_SESSION["ID_MEDICO"]);

$obj_estructura = new Estructura();

$htm_gral = $obj_estructura->html("sam/gral");
$htm_index = $obj_estructura->html("sam/index");
$htm_menu_tablas = $obj_estructura->html("menu/tablas_sam");

$htm_index->Asigna("DETALLE_MEDICO", $obj_medico->Detalle("corto", "", "sam"));

// FELICITACIONES DE FELIZ CUMPLEAÑOS
$cumple = false;
switch ($_SESSION['TIPO_USR']) {
    case 'M':
        $obj_medicos = new Medicos($_SESSION['ID_MEDICO']);
        if (!$_SESSION['felicitado'] and substr($obj_medicos->fechanac, 5, 5) == date("m-d")) {
            $cumple = true;
            $_SESSION['felicitado'] = true;
        }
        break;
}
if ($cumple) {
    ob_start();
?>
<style>
div#imgHB > a{
    position: absolute;
    display: block;
    top: 50%;
    left: 50%;
    width: 626px;
    height: 626px;
    margin-left: -313px;
    margin-top: -313px;
    z-index:999999;
}
div#imgHB {
    position: absolute;
    display: block;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    background-color: rgba(0, 0, 0, 0.9);
    z-index:999998;
}
</style>
<script>
$('body').prepend('<div id="imgHB"><a href="#"><img src="../files/img/bonita-tarjeta-cumpleanos-elementos_23-2147551587.jpg" alt="" style="position:absolute;z-index:999999;" /></a></div>');
$('div#imgHB').click(function(event){
    event.preventDefault();
    $(this).remove();
    return false;
});
</script>
<?php
    $html_cumple = ob_get_clean();
} else {
    $html_cumple = '';
}

$htm_index->Asigna("MENU_TABLAS", $html_cumple.$htm_menu_tablas->Muestra());
$htm_index->Asigna("DIV_HIDE_OPEN", "");
$htm_index->Asigna("DIV_HIDE_CLOSE", "");

$htm_gral->Asigna("CUERPO", $htm_index->Muestra());

CargarVariablesGrales($htm_gral, $tipo = "");

include("estadisticas.php");

echo utf8_decode($htm_gral->Muestra());

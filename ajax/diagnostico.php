<?php
require_once ("../engine/config.php");
require_once ("../engine/restringir_acceso.php");
requerir_class("tpl","mysql","querys","estructura");

//requerir_class("dias_semana");
$this_db = new MySQL();

$SQL = <<<SQL
    SELECT
        *
    FROM
        turnos_estudios AS te
    WHERE
        te.estado = 1 AND
        te.id_turnos = '{$_POST['id_turno']}'
SQL;
$query = $this_db->consulta($SQL);
?>
<div>
    <form id="frm_diagnostico" method="post">
        <?php
        while ($row = $this_db->fetch_array($query)):
            print "id_turnos: {$row['id_turnos']}<br />";
            print "id_estudios: {$row['id_estudios']}<br />";
        endwhile;
        ?>
    </form>
</div>
<div class="botones">
    <a id="btn_modificar_diagnostico" class="btn" href="#">Aceptar</a>
    <a class="btn salir" href="#">Salir</a>
</div>
<script>
$(document).ready(function(){
    $('a#btn_modificar_diagnostico').click(function(event){
        event.preventDefault();
        ajxM = $.ajax({
            type: 'POST',
            url:'../ajax/diagnostico.save.php',
			data: $('#frm_diagnostico').serialize(),
            context: document.body
        }).done(function(data) {
        	IniciarVentana("ventana_diagnostico", "cerrar");
        	$(ventana_diagnostico).dialog('destroy').remove();
        });
    });
    $("a.salir").click(function(){
    	IniciarVentana("ventana_diagnostico", "cerrar");
    	$(ventana_diagnostico).dialog('destroy').remove();
    });
});
</script>

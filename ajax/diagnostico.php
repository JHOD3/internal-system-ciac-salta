<?php
require_once ("../engine/config.php");
require_once ("../engine/restringir_acceso.php");
requerir_class("tpl","mysql","querys","estructura");

//requerir_class("dias_semana");
$this_db = new MySQL();

$SQL_esp = <<<SQL
    SELECT
        t.*
    FROM
        turnos AS t
    INNER JOIN
        medicos_horarios AS mh
        ON mh.id_medicos = t.id_medicos
    INNER JOIN
        turnos_tipos AS tt
        ON mh.id_turnos_tipos = tt.id_turnos_tipos
    WHERE
        t.id_turnos = '{$_POST['id_turno']}' AND
        t.estado = 1 AND
        tt.estado = 1 AND
        tt.tipo = 'ESTUDIOS'
    LIMIT 1
SQL;
$query_esp = $this_db->consulta($SQL_esp);

if ($row_tur = $this_db->fetch_assoc($query_esp)) {
// IF NO ANIDADO

$SQL_med = <<<SQL
    SELECT
        m.*
    FROM
        medicos AS m
    INNER JOIN
        medicos_horarios AS mh
        ON mh.id_medicos = m.id_medicos
    INNER JOIN
        turnos_tipos AS tt
        ON mh.id_turnos_tipos = tt.id_turnos_tipos
    WHERE
        m.estado = 1 AND
        mh.estado = 1 AND
        tt.estado = 1 AND
        tt.tipo = 'ESTUDIOS'
    GROUP BY
        m.id_medicos
    ORDER BY
        m.apellidos,
        m.nombres
SQL;

$SQL_med_cm = <<<SQL
    SELECT
        m.apellidos,
        m.nombres,
        m.matricula,
        '0' AS externo
    FROM
        medicos AS m
    INNER JOIN
        medicos_especialidades AS me
        ON me.id_medicos = m.id_medicos
    WHERE
        m.estado = 1 AND
        m.matricula > 0 AND
        me.estado = 1
    GROUP BY
        m.id_medicos
    UNION
        SELECT
            dx.apellidos,
            dx.nombres,
            dx.matricula,
            '1' AS externo
        FROM
            medicosext AS dx
        WHERE
            dx.estado = 1
    ORDER BY
        apellidos,
        nombres
SQL;

$SQL_os = <<<SQL
    SELECT
        *
    FROM
        obras_sociales AS os
    WHERE
        os.estado = 1
    ORDER BY
        os.abreviacion
SQL;


$SQL_Estudios = <<<SQL
    SELECT
        te.*,
        e.nombre AS estudios,
        e.importe,
        e.arancel,
        t.id_medicos AS turnos_id_medicos
    FROM
        turnos_estudios AS te
    LEFT JOIN
        estudios AS e
        ON te.id_estudios = e.id_estudios
    LEFT JOIN
        turnos AS t
        ON te.id_turnos = t.id_turnos
    WHERE
        te.estado = 1 AND
        te.id_turnos = '{$_POST['id_turno']}'
    ORDER BY
        e.nombre
SQL;
$query = $this_db->consulta($SQL_Estudios);
?>
<?php if ($this_db->num_rows($query) > 0): ?>
    <div id="vntDiag">
        <form id="frm_diagnostico" method="post">
            <input type="hidden" name="id_turno" value="<?=$_POST['id_turno']?>" />
            <table>
                <thead>
                    <th>Estudio</th>
                    <th>Realizador</th>
                    <th>O.Social</th>
                    <th>Prestaci&oacute;n</th>
                    <th>#Orden</th>
                    <th>#Afiliado</th>
                    <th>Cant.</th>
                    <th>Tipo</th>
                    <th>TP</th>
                    <th>TO</th>
                    <th>($) TA</th>
                    <th>($) DD</th>
                    <th>Derivador</th>
                    <th>Observaciones</th>
                    <th>Acciones</th>
                </thead>
                <tbody>
                    <?php
                    while ($row = $this_db->fetch_array($query)):
                        $row_fecha_default = (isset($row['fecha_presentacion']) and $row['fecha_presentacion'] != '0000-00-00') ? $row['fecha_presentacion'] : '';
                        if ($row_fecha_default) {
                            $row_fecha_default = date("d/m/Y", strtotime($row_fecha_default));
                        }
                        $row_cantidad_default = isset($row['cantidad']) ? $row['cantidad'] : '1';
                        ?>
                        <tr id="id_medicos_tr_<?=$row['id_estudios']?>">
                            <td>
                                <input type="hidden" name="id_turnos_estudios[]" value="<?=$row['id_turnos_estudios']?>" />
                                <?=utf8_encode($row['estudios'])?>
                            </td>
                            <td>
                                <select name="id_medicos[]" style="width:120px;">
                                    <option value="">---</option>
                                    <?php
                                    $query_med = $this_db->consulta($SQL_med);
                                    ?>
                                    <?php while ($row_med = $this_db->fetch_array($query_med)): ?>
                                        <option
                                            value="<?=$row_med['id_medicos']?>"
                                            <?php
                                            if (
                                                (
                                                    $row['id_medicos'] != '' and
                                                    $row_med['id_medicos'] == $row['id_medicos']
                                                ) or (
                                                    $row['id_medicos'] == '' and
                                                    $row_med['id_medicos'] == $row['turnos_id_medicos']
                                                )
                                            ):
                                                ?>
                                                selected="selected"
                                                <?php
                                            endif;
                                            ?>
                                        ><?=doSaludo($row_med, false)?></option>
                                    <?php endwhile; ?>
                                </select>
                            </td>
                            <td>
                                <select name="id_obras_sociales[]" style="width:80px;">
                                    <option value="">---</option>
                                    <?php
                                    $query_os = $this_db->consulta($SQL_os);
                                    ?>
                                    <?php while ($row_os = $this_db->fetch_array($query_os)): ?>
                                        <option
                                            value="<?=$row_os['id_obras_sociales']?>"
                                            <?php if ($row_os['id_obras_sociales'] == $row['id_obras_sociales']): ?>
                                                selected="selected"
                                            <?php endif; ?>
                                            data-ta="<?=($row_os['id_obras_sociales'] == 51) ? $row['importe'] : $row['arancel']?>"
                                        ><?=utf8_encode($row_os['abreviacion'])?></option>
                                    <?php endwhile; ?>
                                </select>
                            </td>
                            <td><input type="text" name="fecha_presentacion[]" value="<?=$row_fecha_default?>" style="width:80px;" class="datepicker" /></td>
                            <td><input type="text" name="nro_orden[]" value="<?=$row['nro_orden']?>" style="width:70px;" /></td>
                            <td><input type="text" name="nro_afiliado[]" value="<?=$row['nro_afiliado']?>" style="width:70px;" /></td>
                            <td><input type="text" name="cantidad[]" value="<?=$row_cantidad_default?>" style="width:40px;text-align:right;" /></td>
                            <td>
                                <select name="tipo[]" style="width:50px;">
                                    <option value=""<?=!$row['tipo'] ? ' selected="selected"' : ''?>>---</option>
                                    <option value="1"<?=$row['tipo'] == '1' ? ' selected="selected"' : '' ?>>A</option>
                                    <option value="2"<?=$row['tipo'] == '2' ? ' selected="selected"' : '' ?>>I</option>
                                </select>
                            </td>
                            <td>
                                <select name="trajo_pedido[]" style="width:50px;">
                                    <option value=""<?=!$row['trajo_pedido'] ? ' selected="selected"' : ''?>>---</option>
                                    <option value="1"<?=$row['trajo_pedido'] == '1' ? ' selected="selected"' : ''?>>TP</option>
                                    <option value="2"<?=$row['trajo_pedido'] == '2' ? ' selected="selected"' : ''?>>No</option>
                                </select>
                            </td>
                            <td>
                                <select name="trajo_orden[]" style="width:50px;">
                                    <option value=""<?=!$row['trajo_orden'] ? ' selected="selected"' : ''?>>---</option>
                                    <option value="1"<?=$row['trajo_orden'] == '1' ? ' selected="selected"' : ''?>>TO</option>
                                    <option value="2"<?=$row['trajo_orden'] == '2' ? ' selected="selected"' : ''?>>No</option>
                                </select>
                            </td>
                            <td><input type="number" name="trajo_arancel[]" value="<?=$row['trajo_arancel']?>" alt="<?=$row['trajo_arancel'] ? 'Viejo valor: $'.$row['trajo_arancel'] : ''?>" title="<?=$row['trajo_arancel'] ? 'Viejo valor: $'.$row['trajo_arancel'] : ''?>" style="width:40px;text-align:right;" /></td>
                            <td><input type="number" name="deja_deposito[]" value="<?=$row['deja_deposito']?>" style="width:40px;text-align:right;" /></td>
                            <td><input type="text" name="matricula_derivacion[]" value="<?=$row['matricula_derivacion']?>" style="width:70px;text-align:right;" class="ac_matricula_derivacion" /></td>
                            <td><input type="text" name="observaciones[]" value="<?=$row['observaciones']?>" style="width:100px;" /></td>
                            <td>
                                <select class="copy_id_medicos" id="copy_id_medicos_<?=$row['id_estudios']?>" style="width:120px;">
                                    <option value="">---</option>
                                    <?php
                                    $query_estudios = $this_db->consulta($SQL_Estudios);
                                    ?>
                                    <?php while ($row_est = $this_db->fetch_array($query_estudios)): ?>
                                        <?php if ($row['id_estudios'] != $row_est['id_estudios']): ?>
                                            <option value="<?=$row_est['id_estudios']?>">Copiar desde <?=utf8_encode($row_est['estudios'])?></option>
                                        <?php endif; ?>
                                    <?php endwhile; ?>
                                </select>
                            </td>
                        </tr>
                        <?php
                    endwhile;
                    ?>
                </tbody>
            </table>
        </form>
    </div>
<?php else: ?>
    <div>no se ha seleccionado ningún estudio.</div>
<?php endif; ?>
<div class="botones">
    <?php if ($this_db->num_rows($query) > 0): ?>
        <div id="alert" style="float:left;"></div>
        <a id="btn_modificar_diagnostico" class="btn" href="#">Aceptar</a>
    <?php endif; ?>
    <a class="btn salir" href="#">Salir</a>
</div>
<script>
//$(document).ready(function(){
    $('a#btn_modificar_diagnostico').click(function(event){
        event.preventDefault();
        $('#ventana_diagnostico .botones #alert').html('');
        ajxM = $.ajax({
            type: 'POST',
            url:'../ajax/diagnostico.save.php',
			data: $('#frm_diagnostico').serialize(),
            context: document.body
        }).done(function(data) {
        	IniciarVentana("ventana_diagnostico", "cerrar");
        	$(ventana_diagnostico).dialog('destroy').remove();
        }).error(function(requestData) {
            $('#ventana_diagnostico .botones #alert').html('Ocurrió un error al intentar guardar, por favor intente nuevamente');
        });
    });
    $("a.salir").click(function(){
    	IniciarVentana("ventana_diagnostico", "cerrar");
    	$(ventana_diagnostico).dialog('destroy').remove();
    });
    <?php if ($this_db->num_rows($query) == 0): ?>
    	IniciarVentana("ventana_diagnostico", "cerrar");
    	$(ventana_diagnostico).dialog('destroy').remove();
    <?php endif; ?>
    $(".datepicker").datepicker();
    var tagsACMD = [
        <?php
        $query_med_cm = $this_db->consulta($SQL_med_cm);
        ?>
        <?php $cnct = ''; ?>
        <?php while ($row_med_cm = $this_db->fetch_array($query_med_cm)): ?>
            <?=$cnct?>{label: '<?=utf8_encode(trim($row_med_cm['apellidos']))?>, <?=utf8_encode(trim($row_med_cm['nombres']))?><?=($row_med_cm['externo'] == 1 ? ' (Externo)' : '')?> - <?=$row_med_cm['matricula']?>', value: '<?=$row_med_cm['matricula']?>'}
            <?php $cnct = ','; ?>
        <?php endwhile; ?>
    ];
    $(".ac_matricula_derivacion").autocomplete({
        source: tagsACMD
    });
    $('select.copy_id_medicos').change(function(){
        if ($(this).val() != '') {
            var trhs = 'tr#id_medicos_tr_' + $(this).attr('id').replace($(this).attr('class') + '_', '') + ' select';
            var trhi = 'tr#id_medicos_tr_' + $(this).attr('id').replace($(this).attr('class') + '_', '') + ' input';
            var trds = 'tr#id_medicos_tr_' + $(this).val() + ' select';
            var trdi = 'tr#id_medicos_tr_' + $(this).val() + ' input';
            $(trhs + '[name="id_medicos[]"]').val($(trds + '[name="id_medicos[]"]').val());
            $(trhs + '[name="id_obras_sociales[]"]').val($(trds + '[name="id_obras_sociales[]"]').val());
            $(trhi + '[name="fecha_presentacion[]"]').val($(trdi + '[name="fecha_presentacion[]"]').val());
            $(trhi + '[name="nro_orden[]"]').val($(trdi + '[name="nro_orden[]"]').val());
            $(trhi + '[name="nro_afiliado[]"]').val($(trdi + '[name="nro_afiliado[]"]').val());
            $(trhi + '[name="cantidad[]"]').val($(trdi + '[name="cantidad[]"]').val());
            $(trhs + '[name="tipo[]"]').val($(trds + '[name="tipo[]"]').val());
            $(trhs + '[name="trajo_pedido[]"]').val($(trds + '[name="trajo_pedido[]"]').val());
            $(trhs + '[name="trajo_orden[]"]').val($(trds + '[name="trajo_orden[]"]').val());
            $(trhi + '[name="trajo_arancel[]"]').val($(trdi + '[name="trajo_arancel[]"]').val());
            $(trhi + '[name="deja_deposito[]"]').val($(trdi + '[name="deja_deposito[]"]').val());
            $(trhi + '[name="matricula_derivacion[]"]').val($(trdi + '[name="matricula_derivacion[]"]').val());
            $(trhi + '[name="observaciones[]"]').val($(trdi + '[name="observaciones[]"]').val());
            $(this).val('');
        }
    });
    $('select[name="id_obras_sociales[]"]').change(function(){
        var new_value =
            parseInt($(this).find('option[value="'+$(this).val()+'"]').data('ta')) *
            parseInt($(this).parent().parent().find('input[name="cantidad[]"]').val())
        ;
        var ta_input = $(this).parent().parent().find('input[name="trajo_arancel[]"]');
        ta_input.val(new_value);
    });
    $('input[name="cantidad[]"]').change(function(){
        var new_value =
            parseInt($(this).parent().parent().find('select[name="id_obras_sociales[]"] > option[value="'+$(this).parent().parent().find('select[name="id_obras_sociales[]"]').val()+'"]').data('ta')) *
            parseInt($(this).val())
        ;
        var ta_input = $(this).parent().parent().find('input[name="trajo_arancel[]"]');
        ta_input.val(new_value);
    });
//});
</script>
<style>
#vntDiag input[type=number]::-webkit-outer-spin-button,
#vntDiag input[type=number]::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}
#vntDiag input[type=number] {
    -moz-appearance:textfield;
}
#vntDiag table tbody tr td{
    padding: 2px 4px 0 0;
}
#vntDiag table thead th{
    font-weight: bold;
    color: #007FA6;
    text-align: center;
}
#vntDiag table thead th:first-child{
    text-align: left;
}
.ui-datepicker {
    width:300px!important;
    z-index:848745;
    background-color: #fff;
}
</style>
<?php

// IF NO ANIDADO
}

//EOF diagnostico.php

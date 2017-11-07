<?php
$selected = ' selected="selected"';
$orderby_field = isset($orderby_field) ? $orderby_field : '1';
$orderby_order = isset($orderby_order) ? $orderby_order : 'ASC';
?>
<style>
.tAC {
    text-align: center;
}
.tAR {
    text-align: right;
}
.container{
    width: 98%;
    margin: 0 1%;
}
.tot {
    text-align: center!important;
    font-size: 14px!important;
    font-weight: bold!important;
}
.dOrder {
    cursor: pointer;
}
</style>
<h1>Prácticas Médicas <a class="dmBtnA" style="font-weight:normal;font-size:14px;" href="../tajax/index.php/<?=$this->router->fetch_class().'/agregar/'?>">Agregar Turnos</a></h1>
<?=$error_rol?>
<form id="frmInpSrcFilter" method="post">
    <table id="tblDxI" border="0" cellspacing="0" cellpadding="0">
        <tbody>
            <tr class="trDate">
                <td colspan="100%">
                    Desde:
                    <input type="text" id="date1" value="<?=date("d/m/Y", strtotime($date1))?>" class="datepicker" style="width: 74px;" />
                    <input type="text" id="hour1" name="hour1" value="<?=$hour1?>" class="formathour" style="width: 38px;" placeholder="__:__" />
                    &nbsp;
                    Hasta:
                    <input type="text" id="date2" value="<?=date("d/m/Y", strtotime($date2))?>" class="datepicker" style="width: 74px;" />
                    <input type="text" id="hour2" name="hour2" value="<?=$hour2?>" class="formathour" style="width: 38px;" placeholder="__:__" />
                    <input type="button" id="dateok" value="ok" />
                    <input type="button" id="dateexport" value="exportar" />
                    &nbsp;
                    Ordenar por:
                    <select id="orderby_field" name="orderby_field" style="width:120px;">
                        <option value="1"<?=$orderby_field == '1' ? $selected : ''?>>Turno</option>
                        <option value="2"<?=$orderby_field == '2' ? $selected : ''?>>Paciente</option>
                        <option value="3"<?=$orderby_field == '3' ? $selected : ''?>>Cód.Pra.</option>
                        <option value="4"<?=$orderby_field == '4' ? $selected : ''?>>Estudio</option>
                        <option value="5"<?=$orderby_field == '5' ? $selected : ''?>>Realizador</option>
                        <option value="6"<?=$orderby_field == '6' ? $selected : ''?>>O.Social</option>
                        <option value="7"<?=$orderby_field == '7' ? $selected : ''?>>Prestación</option>
                        <option value="8"<?=$orderby_field == '8' ? $selected : ''?>>Nro.Orden</option>
                        <option value="9"<?=$orderby_field == '9' ? $selected : ''?>>Nro.Afiliado</option>
                        <option value="10"<?=$orderby_field == '10' ? $selected : ''?>>Cant.</option>
                        <option value="11"<?=$orderby_field == '11' ? $selected : ''?>>Tipo</option>
                        <option value="12"<?=$orderby_field == '12' ? $selected : ''?>>TP</option>
                        <option value="13"<?=$orderby_field == '13' ? $selected : ''?>>TO</option>
                        <option value="14"<?=$orderby_field == '14' ? $selected : ''?>>TA</option>
                        <option value="15"<?=$orderby_field == '15' ? $selected : ''?>>DD</option>
                        <option value="16"<?=$orderby_field == '16' ? $selected : ''?>>Derivador</option>
                    </select>
                    <select id="orderby_order" name="orderby_order" style="width:90px;">
                        <option value="ASC"<?=$orderby_order == 'ASC' ? $selected : ''?>>Ascendiente</option>
                        <option value="DESC"<?=$orderby_order == 'DESC' ? $selected : ''?>>Descendiente</option>
                    </select>
                    &nbsp;
                    Total:&nbsp; <?=$listado_count?>
                </td>
            </tr>
            <tr class="inputSearch">
                <td>&nbsp;</td>
                <td><input id="spac" name="spac" type="text" value="<?=isset($spac) ? $spac : ''?>" /></td>
                <td><input id="sces" name="sces" type="text" value="<?=isset($sces) ? $sces : ''?>" /></td>
                <td><input id="sest" name="sest" type="text" value="<?=isset($sest) ? $sest : ''?>" /></td>
                <td>
                    <select id="srea" name="srea">
                        <option value=""></option>
                        <?php
                        for ($i = 0; $i < count($medicos); $i++):
                            $m = strtoupper(
                                trim($medicos[$i]['saludo'])." ".
                                trim($medicos[$i]['apellidos']).", ".
                                trim($medicos[$i]['nombres'])
                            );
                            ?>
                            <option value="<?=$medicos[$i]['id_medicos']?>"<?=(isset($srea) and $srea == $medicos[$i]['id_medicos']) ? $selected : ''?>><?=$m?></option>
                            <?php
                        endfor;
                        ?>
                    </select>
                </td>
                <td><input id="soso" name="soso" type="text" value="<?=isset($soso) ? $soso : ''?>" /></td>
                <td>&nbsp;</td>
                <td><input id="snor" name="snor" type="text" value="<?=isset($snor) ? $snor : ''?>" /></td>
                <td><input id="snaf" name="snaf" type="text" value="<?=isset($snaf) ? $snaf : ''?>" /></td>
                <td><input id="scan" name="scan" type="text" value="<?=isset($scan) ? $scan : ''?>" /></td>
                <td>&nbsp;</td>
                <td class="tot"><?=$cantidad_de_ordenes[1]?></td>
                <td class="tot"><?=$cantidad_de_ordenes[0]?></td>
                <td class="tot"><?=isset($deja_deposito_suma[0]) ? '$'.number_format($deja_deposito_suma[0], 0, "", ".") : ''?></td>
                <td class="tot"><?=isset($deja_deposito_suma[1]) ? '$'.number_format($deja_deposito_suma[1], 0, "", ".") : ''?></td>
                <td><input id="sder" name="sder" type="text" value="<?=isset($sder) ? $sder : ''?>" /></td>
                <td>
                    <select id="sden" name="sden">
                        <option value=""></option>
                        <?php
                        foreach ($medicos_mt AS $rs_mmt):
                            $m = strtoupper(
                                trim($rs_mmt['saludo'])." ".
                                trim($rs_mmt['apellidos']).", ".
                                trim($rs_mmt['nombres'])
                            );
                            ?>
                            <option value="<?=$rs_mmt['matricula']?>"<?=(isset($sden) and $sden == $rs_mmt['matricula']) ? $selected : ''?>><?=$m?></option>
                            <?php
                        endforeach;
                        ?>
                    </select>
                </td>
                <td>&nbsp;</td>
                <td colspan="2">
                    <select id="sche" name="sche">
                        <option value=""<?=(!isset($sche) or !in_array($sche, array('1', '2'))) ? $selected : ''?>>Todo</option>
                        <option value="1"<?=(isset($sche) and $sche == '1') ? $selected : ''?>>No chequeado</option>
                        <option value="2"<?=(isset($sche) and $sche == '2') ? $selected : ''?>>Chequeado</option>
                    </select>
                </td>
            </tr>
            <tr class="trHead">
                <td class="dOrder" data-order="1" style="width:36px;">Turno</td>
                <td class="dOrder" data-order="2">Paciente</td>
                <td class="dOrder" data-order="3" style="width:51px;" title="Código de P.M.">Cod.Pra.</td>
                <td class="dOrder" data-order="4">Estudio</td>
                <td class="dOrder" data-order="5">Realizador</td>
                <td class="dOrder" data-order="6" title="Obra Social">O.Social</td>
                <td class="dOrder" data-order="7" style="width:80px;">Prestación</td>
                <td class="dOrder" data-order="8" style="width:70px;">Nro.Orden</td>
                <td class="dOrder" data-order="9">Nro.Afiliado</td>
                <td class="dOrder" data-order="10" style="width:33px;">Cant.</td>
                <td class="dOrder" data-order="11" style="width:27px;">Tipo</td>
                <td class="dOrder" data-order="12" style="width:16px;" title="Trajo Pedido">TP</td>
                <td class="dOrder" data-order="13" style="width:16px;" title="Trajo Orden">TO</td>
                <td class="dOrder" data-order="14" style="width:32px;" title="Trajo Arancel">TA</td>
                <td class="dOrder" data-order="15" style="width:32px;" title="Deja Depósito">DD</td>
                <td class="dOrder" data-order="16" style="width:60px;">Derivador</td>
                <td style="width:120px;">Nombre</td>
                <td style="width:100px;">Observaciones</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <?php if ($SUPERUSER > 0): ?>
                    <td>&nbsp;</td>
                <?php endif; ?>
            </tr>
            <?php
            if (count($listado) > 0) :
                foreach($listado as $item):
                    $idmec = ' class="tdTab tAC" data-mth=';;
                    $idmer = ' class="tdTab tAR" data-mth=';;
                    $idme = ' class="tdTab" data-mth=';
?>
<tr class="tsEst<?=$item['estado']?>" data-id="<?=$item['id_turnos_estudios']?>" id="id_te_<?=$item['id_turnos_estudios']?>">
    <td style="text-align:center;"><?=date("d/m", strtotime($item['fecha']))?><br /><?=substr($item['desde'], 0, 5)?></td>
    <td><?=utf8_encode(ucwords(upper(trim(utf8_decode(str_replace(', ', ',<br />', $item['pacientes']))))))?></td>
    <td<?=$idme?>"codigoalternat<?=$item['codigoalternat'] > 0? '" style="color:#C66;' : ''?>"><?=$item['codigoalternat'] ? $item['codigoalternat'] : $item['codigopractica']?></td>
    <td<?=$idme?>"estudios"><?=trim($item['estudios']) ? utf8_encode(ucwords(upper(trim(utf8_decode($item['estudios']))))) : '---'?></td>
    <td<?=$idme?>"medicos"><?=trim($item['medicos']) ? utf8_encode(ucwords(upper(trim(utf8_decode($item['medicos']))))) : '---'?></td>
    <td<?=$idme?>"obras_sociales"><?=$item['obras_sociales'] ? $item['obras_sociales'] : '---'?></td>
    <td<?=$idme?>"fecha_presentacion"><?=$item['fecha_presentacion'] ? date("d/m/Y", strtotime($item['fecha_presentacion'])) : '---'?></td>
    <td<?=$idme?>"nro_orden"><?=$item['nro_orden'] ? $item['nro_orden'] : '---'?></td>
    <td<?=$idme?>"nro_afiliado"><?=$item['nro_afiliado'] ? $item['nro_afiliado'] : '---'?></td>
    <td<?=$idmec?>"cantidad"><?=$item['cantidad'] ? $item['cantidad'] : '---'?></td>
    <td<?=$idme?>"tipo"><?=$item['tipo'] == '1' ? 'A' : ($item['tipo'] == '2' ? 'I' : '---')?></td>
    <td<?=$idmec?>"trajo_pedido"><?=$item['trajo_pedido'] == '1' ? 'TP' : ($item['trajo_pedido'] == '2' ? 'No' : '---')?></td>
    <td<?=$idmec?>"trajo_orden"><?=$item['trajo_orden'] == '1' ? 'TO' : ($item['trajo_orden'] == '2' ? 'No' : '---')?></td>
    <td<?=$idmer?>"trajo_arancel"><?=$item['trajo_arancel'] > 0 ? "\$&nbsp;{$item['trajo_arancel']}" : '---'?></td>
    <td<?=$idmer?>"deja_deposito"><?=$item['deja_deposito'] > 0 ? "\$&nbsp;{$item['deja_deposito']}" : '---'?></td>
    <td<?=$idmer?>"matricula_derivacion"><?=$item['matricula_derivacion'] ? $item['matricula_derivacion'] : '---'?></td>
    <td data-mth="medicos_derivacion"><?=$item['medicos_derivacion'] ? $item['medicos_derivacion'] : $item['medicosext_derivacion']?></td>
    <td<?=$idmer?>"observaciones"><?=$item['observaciones']?></td>
    <?php if (isset($sche) and $sche != ''): ?>
        <td><input type="checkbox" class="checked"<?=$item['checked'] == '1' ? ' checked="checked"' : ''?> /></td>
    <?php else: ?>
        <td><?=$item['checked'] == '1' ? '✓' : '&nbsp;'?></td>
    <?php endif; ?>
    <td<?=$idmer?>"save"></td>
    <?php if ($SUPERUSER > 0): ?>
        <td<?=$idmer?>"dele"></td>
    <?php endif; ?>
</tr>
<?php
                endforeach;
            else:
                ?>
                <tr>
                    <td colspan="100%">No se encontró ningún turno</td>
                </tr>
                <?php
            endif;
            ?>
        </tbody>
    </table>
</form>

<div id="tab_codigoalternat" class="tab_hidden"><input type="text" name="codigoalternat" value="" style="width:70px;"<?=$SUPERUSER == '0' ? ' readonly="readonly"' : ''?> /></div>
<div id="tab_estudios" class="tab_hidden">
    <select name="id_estudios" style="width:80px;">
        <option value="">---</option>
        <?php foreach ($estudios AS $row_est): ?>
            <option
                value="<?=$row_est['id_estudios']?>"
            ><?=
                utf8_encode(ucwords(upper(trim(utf8_decode(
                    $row_est['nombre']
                )))))
            ?></option>
        <?php endforeach; ?>
    </select>
</div>
<div id="tab_medicos" class="tab_hidden">
    <select name="id_medicos" style="width:80px;">
        <option value="">---</option>
        <?php foreach ($medicos AS $row_med): ?>
            <option
                value="<?=$row_med['id_medicos']?>"
            ><?=
                utf8_encode(ucwords(upper(trim(utf8_decode(
                    $row_med['saludo'].' '.
                    $row_med['apellidos'].', '.
                    $row_med['nombres']
                )))))
            ?></option>
        <?php endforeach; ?>
    </select>
</div>
<div id="tab_obras_sociales" class="tab_hidden">
    <select name="id_obras_sociales" style="width:80px;">
        <option value="">---</option>
        <?php foreach ($obras_sociales AS $row_os): ?>
            <option
                value="<?=$row_os['id_obras_sociales']?>"
            ><?=
            utf8_encode(trim(utf8_decode(
                $row_os['abreviacion']
            )))
            ?></option>
        <?php endforeach; ?>
    </select>
</div>
<div id="tab_fecha_presentacion" class="tab_hidden"><input type="text" name="fecha_presentacion" value="" style="width:80px;" class="date_picker" /></div>
<div id="tab_nro_orden" class="tab_hidden"><input type="text" name="nro_orden" value="" style="width:70px;" /></div>
<div id="tab_nro_afiliado" class="tab_hidden"><input type="text" name="nro_afiliado" value="" style="width:70px;" /></div>
<div id="tab_cantidad" class="tab_hidden"><input type="text" name="cantidad" value="" style="width:40px;text-align:right;" /></div>
<div id="tab_tipo" class="tab_hidden">
    <select name="tipo" style="width:50px;">
        <option value="">---</option>
        <option value="1">A</option>
        <option value="2">I</option>
    </select>
</div>
<div id="tab_trajo_pedido" class="tab_hidden">
    <select name="trajo_pedido" style="width:50px;">
        <option value="">---</option>
        <option value="1">TP</option>
        <option value="2">No</option>
    </select>
</div>
<div id="tab_trajo_orden" class="tab_hidden">
    <select name="trajo_orden" style="width:50px;">
        <option value="">---</option>
        <option value="1">TO</option>
        <option value="2">No</option>
    </select>
</div>
<div id="tab_trajo_arancel" class="tab_hidden"><input type="number" name="trajo_arancel" value="" style="width:40px;text-align:right;" /></div>
<div id="tab_deja_deposito" class="tab_hidden"><input type="number" name="deja_deposito" value="" style="width:40px;text-align:right;" /></div>
<div id="tab_matricula_derivacion" class="tab_hidden"><input type="text" name="matricula_derivacion" value="" style="width:70px;text-align:right;" class="ac_matricula_derivacion" /></div>
<div id="tab_observaciones" class="tab_hidden"><input type="text" name="observaciones" value="" style="width:100px;" /></div>
<div id="tab_save" class="tab_hidden"><input type="button" value="Guardar" /></div>
<?php if ($SUPERUSER > 0): ?>
    <div id="tab_dele" class="tab_hidden"><a href="#" class="dele">Eliminar</a></div>
<?php endif; ?>

<script>
$(document).ready(function(){
    $('.datepicker').datepicker();

    $('#frmInpSrcFilter .formathour').bind('click focusin', function(){
        $(this).select();
    });
    $('#frmInpSrcFilter .formathour').keydown(function(event){
        if (event.keyCode == 13) {
            $('#dateok').focus().click();
        } else if (
            (event.keyCode < 48 || event.keyCode > 57) &&
            (event.keyCode < 96 || event.keyCode > 105) &&
            (event.keyCode < 9  || event.keyCode > 9)
        ) {
            return false;
        }
    });
    $('#frmInpSrcFilter .formathour').keyup(function(){
        if ($(this).val().length == 2) {
            $(this).val($(this).val() + ':');
        }
    });

    $('#dateok').click(function(){
        var date1 = $('#date1').val();
        date1 = date1.split('/');
        date1 = date1[2] + '-' + date1[1] + '-' + date1[0];
        var date2 = $('#date2').val();
        date2 = date2.split('/');
        date2 = date2[2] + '-' + date2[1] + '-' + date2[0];
        var frmData = $('#frmInpSrcFilter').serialize();
        $('#dateok').parent().parent().html('<div style="white-space: nowrap;"><img alt="" src="../files/img/ajax-loader.gif" /> Cargando las Prácticas Médicas</div>');
        $('tr.inputSearch').html('');
        ajxM = $.ajax({
            type: 'POST',
            url: '../tajax/index.php/<?=$this->router->fetch_class()?>/listado/'+date1+'/'+date2+'/',
            data: frmData,
            context: document.body
        }).done(function(data) {
            $('#diagnosticos_medicos').html(data);
        });
    })
    $('#dateexport').click(function(){
        var date1 = $('#date1').val();
        date1 = date1.split('/');
        date1 = date1[2] + '-' + date1[1] + '-' + date1[0];
        var date2 = $('#date2').val();
        date2 = date2.split('/');
        date2 = date2[2] + '-' + date2[1] + '-' + date2[0];
        var frmData = $('#frmInpSrcFilter').serialize();
        $('#frmInpSrcFilter').attr('action', '../tajax/index.php/<?=$this->router->fetch_class()?>/exportar/'+date1+'/'+date2+'/');
        $('#frmInpSrcFilter').submit();
    });
    $('#frmInpSrcFilter input').keypress(function(e){
        if(e.which == 13) {
            $('#dateok').focus().click();
        }
    });
    var tagsACMD = [
        <?php $cnct = ''; ?>
        <?php foreach ($medicos_cm AS $rs_mcm): ?>
            <?=$cnct?>{label: '<?=utf8_encode(trim(utf8_decode($rs_mcm['apellidos'])))?>, <?=utf8_encode(trim(utf8_decode($rs_mcm['nombres'])))?><?=($rs_mcm['externo'] == 1 ? ' (Externo)' : '')?> - <?=$rs_mcm['matricula']?>', value: '<?=$rs_mcm['matricula']?>'}
            <?php $cnct = ','; ?>
        <?php endforeach; ?>
    ];
    $('#diagnosticos_medicos .aBtnL a, .dmBtnA').click(function(event){
        event.preventDefault();
        ajxM = $.ajax({
            type: 'POST',
            url: $(this).attr('href'),
            context: document.body
        }).done(function(data) {
            $('#diagnosticos_medicos').html(data);
        });
    });
    $('.clickHidden').click(function(){
        $('.clickHidden').each(function(){
            $(this).attr('style', 'visibility:hidden;');
        })
    })
    $('.tdTab').dblclick(function(event){
        if (event.target == this) {
            $(this).parent().find('.tdTab').each(function(){
                var valDefault = $(this).html().replace('$&nbsp;', '').replace('---', '');
                if (valDefault[0] != '<') {
                    $(this).html($('#tab_' + $(this).data('mth')).html().replace('date_picker', 'datepicker'));
                    switch ($(this).find('input, select').prop('tagName')) {
                        case 'INPUT':
                            if ($(this).find('input').attr('type') != 'button') {
                                $(this).find('input').val(valDefault);
                            }
                            break;
                        case 'SELECT':
                            $(this).find('select option').each(function(){
                                if ($(this).html().toLowerCase() == valDefault.toLowerCase()) {
                                    $(this).attr("selected", true);
                                }
                            });
                            break;
                    }
                    $(this).find('.ac_matricula_derivacion').autocomplete({
                        source: tagsACMD
                    });
                    $(this).find('input[type="button"]').attr('data-id', $(this).parent().data('id'));
                    $(this).find('input[type="button"]').click(function(){
                        $(this).parent().html('');
                        var pre_d = '#id_te_' + $(this).data('id') + ' *[data-mth="';
                        var pre_i = '#id_te_' + $(this).data('id') + ' input[name="';
                        var pre_s = '#id_te_' + $(this).data('id') + ' select[name="';
                        var pos = '"]';
                        var serialized;
                        serialized = 'id_turnos_estudios=' + $(this).data('id');
                        serialized+= '&codigoalternat=' + $(pre_i + 'codigoalternat' + pos).val();
                        serialized+= '&id_estudios=' + $(pre_s + 'id_estudios' + pos).val();
                        serialized+= '&id_medicos=' + $(pre_s + 'id_medicos' + pos).val();
                        serialized+= '&id_obras_sociales=' + $(pre_s + 'id_obras_sociales' + pos).val();
                        serialized+= '&fecha_presentacion=' + $(pre_i + 'fecha_presentacion' + pos).val();
                        serialized+= '&nro_orden=' + $(pre_i + 'nro_orden' + pos).val();
                        serialized+= '&nro_afiliado=' + $(pre_i + 'nro_afiliado' + pos).val();
                        serialized+= '&cantidad=' + $(pre_i + 'cantidad' + pos).val();
                        serialized+= '&tipo=' + $(pre_s + 'tipo' + pos).val();
                        serialized+= '&trajo_pedido=' + $(pre_s + 'trajo_pedido' + pos).val();
                        serialized+= '&trajo_orden=' + $(pre_s + 'trajo_orden' + pos).val();
                        serialized+= '&trajo_arancel=' + $(pre_i + 'trajo_arancel' + pos).val();
                        serialized+= '&deja_deposito=' + $(pre_i + 'deja_deposito' + pos).val();
                        serialized+= '&matricula_derivacion=' + $(pre_i + 'matricula_derivacion' + pos).val();
                        serialized+= '&observaciones=' + $(pre_i + 'observaciones' + pos).val();
                        $(pre_d + 'codigoalternat' + pos).html('&#8634;');
                        $(pre_d + 'estudios' + pos).html('&#8634;');
                        $(pre_d + 'medicos' + pos).html('&#8634;');
                        $(pre_d + 'obras_sociales' + pos).html('&#8634;');
                        $(pre_d + 'fecha_presentacion' + pos).html('&#8634;');
                        $(pre_d + 'nro_orden' + pos).html('&#8634;');
                        $(pre_d + 'nro_afiliado' + pos).html('&#8634;');
                        $(pre_d + 'cantidad' + pos).html('&#8634;');
                        $(pre_d + 'tipo' + pos).html('&#8634;');
                        $(pre_d + 'trajo_pedido' + pos).html('&#8634;');
                        $(pre_d + 'trajo_orden' + pos).html('&#8634;');
                        $(pre_d + 'trajo_arancel' + pos).html('&#8634;');
                        $(pre_d + 'deja_deposito' + pos).html('&#8634;');
                        $(pre_d + 'matricula_derivacion' + pos).html('&#8634;');
                        $(pre_d + 'observaciones' + pos).html('&#8634;');
                        ajxM = $.ajax({
                            type: 'POST',
                            data: serialized,
                            url: '../tajax/index.php/<?=$this->router->fetch_class()?>/savediagnostico',
                            context: $('#id_te_' + $(this).data('id'))
                        }).done(function(data) {
                            var dataJSON = JSON && JSON.parse(data) || $.parseJSON(data);
                            $(pre_d + 'codigoalternat' + pos).html(dataJSON['codigoalternat']);
                            $(pre_d + 'estudios' + pos).html(dataJSON['id_estudios']);
                            $(pre_d + 'medicos' + pos).html(dataJSON['id_medicos']);
                            $(pre_d + 'obras_sociales' + pos).html(dataJSON['id_obras_sociales']);
                            $(pre_d + 'fecha_presentacion' + pos).html(dataJSON['fecha_presentacion']);
                            $(pre_d + 'nro_orden' + pos).html(dataJSON['nro_orden']);
                            $(pre_d + 'nro_afiliado' + pos).html(dataJSON['nro_afiliado']);
                            $(pre_d + 'cantidad' + pos).html(dataJSON['cantidad']);
                            $(pre_d + 'tipo' + pos).html(dataJSON['tipo']);
                            $(pre_d + 'trajo_pedido' + pos).html(dataJSON['trajo_pedido']);
                            $(pre_d + 'trajo_orden' + pos).html(dataJSON['trajo_orden']);
                            $(pre_d + 'trajo_arancel' + pos).html(dataJSON['trajo_arancel']);
                            $(pre_d + 'deja_deposito' + pos).html(dataJSON['deja_deposito']);
                            $(pre_d + 'matricula_derivacion' + pos).html(dataJSON['matricula_derivacion']);
                            $(pre_d + 'medicos_derivacion' + pos).html(dataJSON['medicos_derivacion']);
                            $(pre_d + 'observaciones' + pos).html(dataJSON['observaciones']);
                        });
                    });
                    <?php if ($SUPERUSER > 0): ?>
                        $(this).find('a.dele').click(function(event){
                            event.preventDefault();
                            if (confirm('Seguro que desea Eliminar?')) {
                                dId = $(this).parent().parent().data('id');
                                ajxM = $.ajax({
                                    type: 'POST',
                                    data: {id_turnos_estudios: dId},
                                    url: '../tajax/index.php/<?=$this->router->fetch_class()?>/delediagnostico',
                                    context: $('#id_te_' + dId)
                                }).done(function() {
                                    $('#id_te_' + dId).remove();
                                });
                            }
                        });
                    <?php endif; ?>
                }
            });
        }
    });
    $('#frmInpSrcFilter input[type="checkbox"].checked').change(function(){
        var trRemove = $(this).parent().parent();
        dId = $(this).parent().parent().data('id');
        var vUrl;
        if ($(this)[0].checked) {
            vUrl = '../tajax/index.php/<?=$this->router->fetch_class()?>/check/';
        } else {
            vUrl = '../tajax/index.php/<?=$this->router->fetch_class()?>/uncheck/';
        }
        ajxM = $.ajax({
            type: 'POST',
            url: vUrl,
            data: {id_turnos_estudios: dId},
            context: document.body
        }).done(function(data) {
            trRemove.remove();
        });
    });
    $('body').on('focus',".datepicker", function(){
        if( $(this).hasClass('hasDatepicker') === false )  {
            $(this).datepicker();
        }
    });
    $('.dOrder').click(function(){
        if ($('#orderby_field').val() == $(this).data('order')) {
            if ($('#orderby_order').val() == 'ASC') {
                $('#orderby_order').val('DESC');
            } else {
                $('#orderby_order').val('ASC');
            }
        } else {
            $('#orderby_field').val($(this).data('order'));
            $('#orderby_order').val('ASC');
        }
        $('#dateok').focus().click();
    });
    <?php if ($orderby_order == 'ASC'): ?>
        var apnd = '&nbsp;&#9650;';
    <?php else: ?>
        var apnd = '&nbsp;&#9660;';
    <?php endif; ?>
    $('.dOrder[data-order="<?=$orderby_field?>"]')
        .append(apnd)
        .attr('style', 'color:#ff0;')
    ;
});
</script>

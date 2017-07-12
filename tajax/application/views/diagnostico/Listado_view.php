<h1>Diagnóstico por Imágenes</h1>
<table id="tblDxI" border="0" cellspacing="0" cellpadding="0">
    <tbody>
        <tr class="trDate">
            <td colspan="100%" class="aBtnL">
                <a href="<?=base_url().$this->router->fetch_class().'/listado/'.dateLegiblePlus($date, $ds['ayer'])?>">Anterior</a>
                <strong style="margin:0 10px;"><?=utf8_encode(dateLegible($date))?></strong>
                <a href="<?=base_url().$this->router->fetch_class().'/listado/'.dateLegiblePlus($date, $ds['mana'])?>">Siguiente</a>
                <!--
                <a href="<?=base_url().$this->router->fetch_class().'/form_agregar/'.$date?>">Sacar un Turno por la Mañana</a>
                <a href="<?=base_url().$this->router->fetch_class().'/form_agregar/'.$date?>">Sacar un Turno por la Tarde</a>
                //-->
            </td>
        </tr>
        <tr class="trHead">
            <td>Hora</td>
            <td>Paciente</td>
            <td>Estado</td>
            <td>Estudio</td>
            <!--td>Acción</td//-->
            <td>Realizador</td>
            <td>O.Social</td>
            <td>Presentación</td>
            <td>Nro.Orden</td>
            <td>Nro.Afiliado</td>
            <td>Cant.</td>
            <td>Tipo</td>
            <td>TP</td>
            <td>TO</td>
            <td>TA</td>
            <td>DD</td>
            <td>Derivador</td>
        </tr>
        <tr><td colspan="100%" class="aBtnL"><?=$pagination_links;?></td></tr>
        <?php if (count($listado) > 0) : ?>
            <?php foreach($listado as $item):?>
                <?php
                $coln = " style=\"color:#{$item['color']};\"";
                $colc = " style=\"color:#{$item['color']};text-align:center;\"";
                $colr = " style=\"color:#{$item['color']};text-align:right;\"";
                $idme = 'class="tdTab" data-id="'.$item['id_turnos_estudios'].'" data-method=';
                ?>
                <tr class="tsEst<?=$item['estado']?>">
                    <td<?=$coln?>><?=$item['hora']?></td>
                    <td<?=$coln?>><?=utf8_encode(ucwords(lower(trim(utf8_decode($item['pacientes'])))))?></td>
                    <td<?=$coln?>><?=ucwords(strtolower($item['turnos_estados']))?></td>
                    <td<?=$coln?>><?=utf8_encode(ucwords(lower(trim(utf8_decode($item['estudios'])))))?></td>
                    <!--td<?=$coln?> class="aBtnL">
                        <a href="<?=base_url().$this->router->fetch_class()?>/form_modificar/<?=$item['id_turnos']?>">Modificar</a>
                        <a href="<?=base_url().$this->router->fetch_class()?>/form_borrar/<?=$item['id_turnos']?>">Eliminar</a>
                    </td//-->
                    <td<?=$coln.$idme?>"medicos"><?=trim($item['medicos']) ? utf8_encode(ucwords(lower(trim(utf8_decode($item['medicos']))))) : '---'?></td>
                    <td<?=$coln.$idme?>"obras_sociales"><?=$item['obras_sociales'] ? $item['obras_sociales'] : '---'?></td>
                    <td<?=$coln.$idme?>"fecha_presentacion"><?=$item['fecha_presentacion'] ? date("d/m/Y", strtotime($item['fecha_presentacion'])) : '---'?></td>
                    <td<?=$coln.$idme?>"nro_orden"><?=$item['nro_orden'] ? $item['nro_orden'] : '---'?></td>
                    <td<?=$coln.$idme?>"nro_afiliado"><?=$item['nro_afiliado'] ? $item['nro_afiliado'] : '---'?></td>
                    <td<?=$colc.$idme?>"cantidad"><?=$item['cantidad'] ? $item['cantidad'] : '---'?></td>
                    <td<?=$coln.$idme?>"tipo"><?=$item['tipo'] == '1' ? 'A' : ($item['tipo'] == '2' ? 'I' : '---')?></td>
                    <td<?=$colc.$idme?>"trajo_pedido"><?=$item['trajo_pedido'] == '1' ? 'TP' : ($item['trajo_pedido'] == '2' ? 'No' : '---')?></td>
                    <td<?=$colc.$idme?>"trajo_orden"><?=$item['trajo_orden'] == '1' ? 'TO' : ($item['trajo_orden'] == '2' ? 'No' : '---')?></td>
                    <td<?=$colr.$idme?>"trajo_arancel"><?=$item['trajo_arancel'] > 0 ? "\$&nbsp;{$item['trajo_arancel']}" : '---'?></td>
                    <td<?=$colr.$idme?>"deja_deposito"><?=$item['deja_deposito'] > 0 ? "\$&nbsp;{$item['deja_deposito']}" : '---'?></td>
                    <td<?=$colr.$idme?>"matricula_derivacion"><?=$item['matricula_derivacion'] ? $item['matricula_derivacion'] : '---'?></td>
                </tr>
            <?php endforeach;?>
        <?php else: ?>
            <tr>
                <td colspan="100%">No se encontró ningún turno</td>
            </tr>
        <?php endif; ?>
        <tr><td colspan="100%" class="aBtnL"><?=$pagination_links;?></td></tr>
    </tbody>
    <tfoot>
        <th colspan="100%">Total: <?=$pagination_config['total_rows']?></th>
    </tfoot>
</table>

<div id="tab_medicos" class="tab_hidden">
    <select name="id_medicos" style="width:120px;">
        <option value="">---</option>
        <?php foreach ($medicos AS $row_med): ?>
            <option
                value="<?=$row_med['id_medicos']?>"
            ><?=
                utf8_encode(ucwords(lower(trim(utf8_decode(
                    $row_med['saludo'].' '.$row_med['apellidos'].', '.$row_med['nombres']
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
<div id="tab_fecha_presentacion" class="tab_hidden"><input type="text" name="fecha_presentacion" value="" style="width:80px;" class="datepicker" /></div>
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

<script>
$(document).ready(function(){
    $('#diagnosticos_medicos a').click(function(event){
        event.preventDefault();
        ajxM = $.ajax({
            type: 'POST',
            url: $(this).attr('href'),
            context: document.body
        }).done(function(data) {
            $('#diagnosticos_medicos').html(data);
        });
    });
    $('.tdTab').dblclick(function(){
        var valDefault = $(this).html().replace('$&nbsp;', '').replace('---', '');
        $(this).html($('#tab_' + $(this).data('method')).html());
        switch ($(this).find('input, select').prop('tagName')) {
            case 'INPUT':
                $(this).find('input').val(valDefault);
                break;
            case 'SELECT':
                if (console && console.log) console.log(valDefault.toLowerCase());
                $(this).find('select option').each(function(){
                    if ($(this).html().toLowerCase() == valDefault.toLowerCase()) {
                        if (console && console.log) console.log('si ' + $(this).html().toLowerCase());
                        $(this).attr("selected", true);
                    } else {
                        if (console && console.log) console.log('no' + $(this).html().toLowerCase());
                    }
                });
                break;
        }
        $(this).find('input, select').focus();
        switch ($(this).data('method')) {
            case "medicos":
                break;
        }
        $('.tdTab select, .tdTab input').attr('data-id', $(this).data('id'));
        $('.tdTab input, .tdTab select').focusout(function(){
            var my_id = $(this).data('id');
            var my_name = $(this).attr('name');
            var my_value = $(this).val();
            var layer = $(this).parent();
            $(this).parent().html('&#8634;');
            ajxM = $.ajax({
                type: 'POST',
                data: {
                    id: my_id,
                    name: my_name,
                    value: my_value
                },
                url: '<?=base_url()?>diagnostico/save',
                context: layer
            }).done(function(data) {
                 $(this).html(data);
            });
        });
    });
});
</script>

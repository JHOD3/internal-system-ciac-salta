<h1>Diagnóstico por Imágenes</h1>
<table id="tblDxI" border="0" cellspacing="0" cellpadding="0">
    <tbody>
        <tr class="trDate">
            <td colspan="100%" class="aBtnL">
                <a href="<?=base_url().$this->router->fetch_class().'/listado/'.dateLegiblePlus($date, $ds['ayer'])?>">Anterior</a>
                <strong style="margin:0 10px;"><?=dateLegible($date)?></strong>
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
            <td>Realizador</td>
            <!--td>Acción</td//-->
            <td>Estudio</td>
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
                $coln =  " style=\"color:#{$item['color']};\"";
                $colc =  " style=\"color:#{$item['color']};text-align:center;\"";
                $colr =  " style=\"color:#{$item['color']};text-align:right;\"";
                ?>
                <tr class="tsEst<?=$item['estado']?>">
                    <td<?=$coln?>><?=$item['hora']?></td>
                    <td<?=$coln?>><?=ucwords(strtolower($item['pacientes']))?></td>
                    <td<?=$coln?>><?=ucwords(strtolower($item['turnos_estados']))?></td>
                    <td<?=$coln?>><?=trim($item['medicos']) ? ucwords(strtolower($item['medicos'])) : '---'?></td>
                    <!--td<?=$coln?> class="aBtnL">
                        <a href="<?=base_url().$this->router->fetch_class()?>/form_modificar/<?=$item['id_turnos']?>">Modificar</a>
                        <a href="<?=base_url().$this->router->fetch_class()?>/form_borrar/<?=$item['id_turnos']?>">Eliminar</a>
                    </td//-->
                    <td<?=$coln?>><?=ucwords(strtolower($item['estudios']))?></td>
                    <td<?=$coln?>><?=$item['obras_sociales'] ? $item['obras_sociales'] : '---'?></td>
                    <td<?=$coln?>><?=$item['fecha_presentacion'] ? $item['fecha_presentacion'] : '---'?></td>
                    <td<?=$coln?>><?=$item['nro_orden'] ? $item['nro_orden'] : '---'?></td>
                    <td<?=$coln?>><?=$item['nro_afiliado'] ? $item['nro_afiliado'] : '---'?></td>
                    <td<?=$colc?>><?=$item['cantidad'] ? $item['cantidad'] : '---'?></td>
                    <td<?=$coln?>><?=$item['tipo'] == '1' ? 'Ambulatorios' : ($item['tipo'] == '2' ? 'Internado' : '---')?></td>
                    <td<?=$colc?>><?=$item['trajo_pedido'] == '1' ? 'TP' : '---'?></td>
                    <td<?=$colc?>><?=$item['trajo_orden'] == '1' ? 'TO' : '---'?></td>
                    <td<?=$colr?>><?=$item['trajo_arancel'] > 0 ? "\$&nbsp;{$item['trajo_arancel']}" : '---'?></td>
                    <td<?=$colr?>><?=$item['deja_deposito'] > 0 ? "\$&nbsp;{$item['deja_deposito']}" : '---'?></td>
                    <td<?=$colr?>><?=$item['matricula_derivacion'] ? $item['matricula_derivacion'] : '---'?></td>
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
});
</script>

<div><?=$calendar?></div>
<div class="detail">
    <div class="title">
        <?php if (is_array($aHorarios)): ?>
            Horarios disponibles para el <span><?=$day?>/<?=$month?>/<?=$year?></span>
            <input type="hidden" id="fecha" name="fecha" value="<?=$year?>-<?=$month?>-<?=$day?>" />
        <?php else: ?>
            <br />No hay Horarios disponibles para el <span><?=$day?>/<?=$month?>/<?=$year?></span>
        <?php endif; ?>
    </div>
    <?php if (is_array($aHorarios) and isset($aHorarios[0])): ?>
        <div class="atencion">
            <?php if (in_array($aHorarios[0]['id_turnos_tipos'], array(3, 4))): ?>
                Estimado paciente, la anteción con <?=doSaludo($rsMedico)?> se realiza por orden de llegada.
                Por favor diríjase a mesa de entrada
            <?php elseif (in_array($aHorarios[0]['id_turnos_tipos'], array(5, 6))): ?>
                Estimado paciente para reserva de turno con <?=doSaludo($rsMedico)?>,
                por favor diríjase a mesa de entrada de lunes a viernes de 07:30 a 21:00 hs
            <?php elseif (in_array($aHorarios[0]['id_turnos_tipos'], array(7, 8))): ?>
                Estimado paciente los turnos con <?=doSaludo($rsMedico)?> son asignados exclusivamente por el Profesional,
                por favor diríjase a mesa de entrada de lunes a viernes de 07:30 a 21:00 hs
            <?php endif; ?>
            <?php if (in_array($aHorarios[0]['id_turnos_tipos'], array(3, 4, 5, 6, 7, 8))): ?>
                <div>
                    <span>
                        Horario de atención de <?=doSaludo($rsMedico)?>:<br />
                        <?=$vcDiasHorarios?>
                    </span>
                </div>
            <?php endif; ?>
        </div>
        <?php if (in_array($aHorarios[0]['id_turnos_tipos'], array(1, 2))): ?>
            <div class="hours">
                <?php foreach ($aHorarios AS $rsH): ?>
                    <?php
                    if (in_array($rsH['id_turnos_tipos'], array(3, 4))) {
                        $Tx = "Turno 1";
                    } else {
                        $Tx = "";
                    }
                    ?>
                    <?php for ($h = $rsH['desde']; $h <= $rsH['hasta']; $h = horaMM($h, $vcDuracionTurno)): ?>
                        <?php if (in_array($h, $aTurnosReservados)): ?>
                            <div><?=$Tx ? $Tx : substr($h, 0, 5)?></div>
                        <?php else: ?>
                            <?php
                            $boHI = false;
                            for ($hi = 0; $hi < count($aHorariosInhabilitados); $hi++) {
                                if (
                                    isset($aHorariosInhabilitados[$hi]['desde']) and
                                    $aHorariosInhabilitados[$hi]['desde'] and
                                    isset($aHorariosInhabilitados[$hi]['hasta']) and
                                    $aHorariosInhabilitados[$hi]['hasta'] and
                                    $h > $aHorariosInhabilitados[$hi]['desde'] and
                                    $h < $aHorariosInhabilitados[$hi]['hasta']
                                ) {
                                    $boHI = true;
                                }
                            }
                            if ($boHI):
                            ?>
                                <div><?=$Tx ? $Tx : substr($h, 0, 5)?></div>
                            <?php else: ?>
                                <?php if (date("Ymd") == $year.$month.$day and $h <= date("H:i:s", strtotime("+2 hours"))): ?>
                                    <div><?=$Tx ? $Tx : substr($h, 0, 5)?></div>
                                <?php else: ?>
                                    <a href=""><div><?=$Tx ? $Tx : substr($h, 0, 5)?></div></a>
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php endif; ?>
                        <?php
                        if (in_array($rsH['id_turnos_tipos'], array(3, 4))) {
                            $Tx++;
                        }
                        ?>
                    <?php endfor; ?>
                    <br />
                <?php endforeach; ?>
            </div>
            <input type="hidden" id="desde" name="desde" value="" />
        <?php endif; ?>
    <?php endif; ?>
</div>
<div class="clearfloat"></div>
<div>
    <?php
    $aOSrec_ids = array();
    if (count($rsObrasSocialesDeMedico) > 0):
        foreach ($rsObrasSocialesDeMedico AS $rsOSM):
            $aOSrec_ids[] = $rsOSM['id_obras_sociales'];
        endforeach;
    endif;
    ?>
    <?php /*
    <div class="osTitle">Obras Sociales que recibe este Profesional:</div>
    <div class="osList">
        <?php $aOSrec_ids = array(); ?>
        <?php if (count($rsObrasSocialesDeMedico) > 0): ?>
            <?php foreach ($rsObrasSocialesDeMedico AS $rsOSM): ?>
                <?php $aOSrec_ids[] = $rsOSM['id_obras_sociales']; ?>
                <span title="<?=$rsOSM['nombre']?>"><?=$rsOSM['abreviacion']?>,&nbsp;</span>
            <?php endforeach; ?>
        <?php else: ?>
            Atención sin obra social
        <?php endif; ?>
    </div>
    */ ?>
    <?php if (isset($aObrasSociales) and count($aObrasSociales) > 0): ?>
    <div id="osrec">
        <div class="osTitle">Elige la Obra Social</div>
        <span class="custom-dropdown custom-dropdown--white">
            <select class="custom-dropdown__select custom-dropdown__select--white" id="id_obras_sociales" name="id_obras_sociales">
                <option value="">-- Seleccione una Obra Social --</option>
                <?php foreach ($aObrasSociales AS $rsOS): ?>
                    <option id="opt<?=$rsOS['id_obras_sociales']?>" class="rec<?=in_array($rsOS['id_obras_sociales'], $aOSrec_ids)?>" value="<?=$rsOS['id_obras_sociales']?>"><?=$rsOS['abreviacion']?> - <?=ucwords(lower($rsOS['nombre']))?></option>
                <?php endforeach; ?>
            </select>
        </span>
        <div id="id_obras_sociales_div"></div>
    </div>
    <?php endif; ?>
    <div class="clearfloat10"></div>
</div>

<script>
$(document).ready(function(){
    $('#id_obras_sociales').bind('change click', function() {
        $('#id_obras_sociales_div').hide();
        if ($('#osrec #opt'+$(this).val()).attr('class') == 'rec') {
            var os = $('#osrec #opt'+$(this).val()).html().split('-');
            os.shift();
            os = os.join('-').trim();
            $('#id_obras_sociales_div')
                .html(
                    '<b>ATENCIÓN</b><br /><strong>'+
                    '<?=ucwords(doSaludo($rsMedico))?>'+
                    '</strong> no trabaja con la Obra Social <strong>'+
                    os+
                    '</strong>.<br />'+
                    'Aún así puede asistir a la consulta de forma particular.'
                )
                .show()
            ;
        }
    });
    $('#calendar tr td strong').parent().html($('#calendar tr td strong').html());
    $('#calendar tr td a').each(function(){
        if ($(this).text() == <?=(integer)$day?>) {
            $(this).parent().addClass('slctd');
        }
    });
    $('#calendar table a').click(function(event){
        event.preventDefault();
        $('#divLoading').html('<div class="opacityBackground"><div class="loading"><img src="assets/images/loading.gif" alt="" /></div></div>');
        $.ajax({
            url: $(this).attr('href'),
            context: document.body
        }).done(function(data) {
            $('#calendar').html(data);
            $('#divLoading').html('');
        }).fail(function(jqXHR, textStatus, errorThrown ){
            if (textStatus == 'abort'){
                $('#calendar').html('<div style="width:100%;text-align:center;">Espere unos segundos por favor...</div>');
            } else {
                $('#calendar').html('<div style="width:100%;text-align:center;">Hubo un problema de conexión. Por favor reintente mas tarde.</div>');
                $('#divLoading').html('');
            }
        });
    });
    $('#calendar .detail a').click(function(event){
        event.preventDefault();
        $('#calendar .detail a div').removeAttr('class');
        $(this).find('div').attr('class', 'selected')
        $('#desde').val($(this).find('div').html());
    });
    <?php if (isset($post['desde'])): ?>
        $('.hours a div').each(function(){
            if ($(this).html() == '<?=$post['desde']?>') {
                $(this).parent().click();
            }
        });
    <?php endif; ?>
});
</script>

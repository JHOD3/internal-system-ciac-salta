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
            <div>
                <span>
                    Horario de atención
                    <?=
                    (substr(doSaludo($rsMedico), 0, 2) == 'el' ? 'd' : 'de ').
                    doSaludo($rsMedico)
                    ?>:<br />
                    <?=$vcDiasHorarios?>
                </span>
            </div>
        </div>
        <?php if (in_array($aHorarios[0]['id_turnos_tipos'], array(9, 10))): ?>
            <div class="leyendadisp">
                <div class="ocup">Ocupado&nbsp;</div>
                <div class="disp">Disponible&nbsp;</div>
            </div>
            <div class="hours">
                <?php foreach ($aHorarios AS $rsH): ?>
                    <?php
                    $Tx = "";
                    ?>
                    <?php for ($h = $rsH['desde']; $h <= $rsH['hasta']; $h = horaMM($h, $vcDuracionTurno)): ?>
                        <?php if (in_array($h, $aTurnosReservados)): ?>
                            <div data-alt="Ocupado"><?=$Tx ? $Tx : substr($h, 0, 5)?></div>
                        <?php else: ?>
                            <?php
                            $boHI = false;
                            for ($hi = 0; $hi < count($aHorariosInhabilitados); $hi++) {
                                if (
                                    isset($aHorariosInhabilitados[$hi]['desde']) and
                                    $aHorariosInhabilitados[$hi]['desde'] and
                                    isset($aHorariosInhabilitados[$hi]['hasta']) and
                                    $aHorariosInhabilitados[$hi]['hasta'] and
                                    $h >= $aHorariosInhabilitados[$hi]['desde'] and
                                    $h < $aHorariosInhabilitados[$hi]['hasta']
                                ) {
                                    $boHI = true;
                                }
                            }
                            if ($boHI):
                            ?>
                                <div data-alt="Ocupado"><?=$Tx ? $Tx : substr($h, 0, 5)?></div>
                            <?php else: ?>
                                <?php if (date("Ymd") == $year.$month.$day and $h <= date("H:i:s", strtotime("+3 hours"))): ?>
                                    <div data-alt="Ocupado"><?=$Tx ? $Tx : substr($h, 0, 5)?></div>
                                <?php else: ?>
                                    <a href=""><div data-alt="Disponible"><?=$Tx ? $Tx : substr($h, 0, 5)?></div></a>
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php endfor; ?>
                    <br /><br />
                <?php endforeach; ?>
            </div>
            <input type="hidden" id="desde" name="desde" value="" />
        <?php endif; ?>
    <?php endif; ?>
</div>
<div class="clearfloat"></div>

<script>
$(document).ready(function(){
    $('#diagnosticos_horarios tr td strong').parent().html($('#diagnosticos_horarios tr td strong').html());
    $('#diagnosticos_horarios tr td a').each(function(){
        if ($(this).text() == <?=(integer)$day?>) {
            $(this).parent().addClass('slctd');
        }
    });
    $('#diagnosticos_horarios table a').click(function(event){
        event.preventDefault();
        $('#divLoading').html('<div class="opacityBackground"><div class="loading"><img src="assets/images/loading.gif" alt="" /></div></div>');
        $.ajax({
            url: $(this).attr('href'),
            context: document.body
        }).done(function(data) {
            $('#diagnosticos_horarios').html(data);
            $('#divLoading').html('');
        }).fail(function(jqXHR, textStatus, errorThrown ){
            if (textStatus == 'abort'){
                $('#diagnosticos_horarios').html('<div style="width:100%;text-align:center;">Espere unos segundos por favor...</div>');
            } else {
                $('#diagnosticos_horarios').html('<div style="width:100%;text-align:center;">Hubo un problema de conexión. Por favor reintente mas tarde.</div>');
                $('#divLoading').html('');
            }
        });
    });
    $('#diagnosticos_horarios .detail a').click(function(event){
        event.preventDefault();
        $('#diagnosticos_horarios .detail a div').removeAttr('class');
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

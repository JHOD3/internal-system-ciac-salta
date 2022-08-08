<div class="MedEsp">
        <span class="fa fa-user-md"></span>
        <?=doSaludo($rsMedico, false)?>
        <br />
        <span class="fa fa-stethoscope"></span>
        <?=$rsEspecialidad['nombre']?>
</div>
<div class="row">
    <div class="col-md-6 col-xs-12"><?=$calendar?></div>
    <div class="detail col-md-6 col-xs-12">
		<input type="hidden" id="fecha" name="fecha" value="<?=$year?>-<?=$month?>-<?=$day?>" />
		<?php if (count($aHorariosInhabilitados) > 0): ?>
			<div>
				<!-- Este codigo comentado agrega el motivo por el cual el medico no puede asistir un dia X -->
				<!-- 
				<?php $cnct = 'Motivo: '; ?>
				<?php foreach ($aHorariosInhabilitados AS $rsHI): ?>
					<?php if ($rsHI['motivo_descripcion'] != 'Otro'): ?>
						<?=$cnct?><?=$rsHI['motivo_descripcion']?>
						<?php $cnct = '<span style="font-weight: normal;">,</span> '; ?>
					<?php else: ?>
						<?=$cnct?>El Médico no puede asistir
						<?php $cnct = '<span style="font-weight: normal;">,</span> '; ?>
					<?php endif; ?>
				<?php endforeach; ?>
				-->
			</div>
		<?php endif; ?>        
        <?php if (is_array($aHorarios) and isset($aHorarios[0]) and in_array($aHorarios[0]['id_turnos_tipos'], array(3, 4, 5, 6, 7, 8))): ?>            
            <div class="atencion">
                <?php if (in_array($aHorarios[0]['id_turnos_tipos'], array(3, 4))): ?>
                    <?=str_replace('[medico]',$medicoSaludo,$aHorarios[0]['mensaje'])?>
                    <script> $('#btn-Siguiente').css('display','none');</script>
                    <input type="hidden" id="desde" name="desde" value="<?=$HoraLibre?>"/>
                <?php elseif (in_array($aHorarios[0]['id_turnos_tipos'], array(5, 6))): ?>
                    <?=str_replace('[medico]',$medicoSaludo,$aHorarios[0]['mensaje'])?>
                    <script> $('#btn-Siguiente').css('display','none');</script>
                    <script> $('#btn-Anterior').css('display','none');</script>
                <?php elseif (in_array($aHorarios[0]['id_turnos_tipos'], array(7, 8))): ?>
                    <?=str_replace('[medico]',$medicoSaludo,$aHorarios[0]['mensaje'])?>
                    <script> $('#btn-Siguiente').css('display','none');</script>
                    <script> $('#btn-Anterior').css('display','none');</script>
                <?php endif; ?>                
				<div style="padding-top: 10px;">
					<span>
						Horario de atención
						<?=
						(substr($medicoSaludo, 0, 2) == 'el' ? 'd' : 'de ').
						$medicoSaludo
						?>:<br />
						<?=$vcDiasHorarios?>
					</span>
				</div>                
            </div>                   
		<?php elseif (is_array($aHorarios) and isset($aHorarios[0]) and in_array($aHorarios[0]['id_turnos_tipos'], array(1, 2))): ?>
			<div>
				<p>Horarios para el día <span style="color: #0080bc;font-weight: bold;"><?=$day?>/<?=$month?>/<?=$year?></span>:</p>
				<!--<div class="leyendadisp">
					<div class="ocup">Ocupado&nbsp;</div>
					<div class="disp">Disponible&nbsp;</div>
				</div>-->
				<div class="hours" style="padding-top: 10px;">
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
							<?php
							if (in_array($rsH['id_turnos_tipos'], array(3, 4))) {
								$Tx++;
							}
							?>
						<?php endfor; ?>
						<br /><br />
					<?php endforeach; ?>
				</div>
				<input type="hidden" id="desde" name="desde" value=""/>
			</div>
		<?php else: ?>
			<div>
				<p>No hay Horarios disponibles para el día <span><?=$day?>/<?=$month?>/<?=$year?></span></p>
			</div>
		<?php endif; ?>
    </div>
</div>

@

<div class="clearfloat"></div>
<div style="margin-top: 25px;">
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
    <div class="osTitle">Elija la Obra Social</div>
         <select id="id_obras_sociales" name="id_obras_sociales">
                <option value="">-- Seleccione una Obra Social --</option>
                <?php foreach ($aObrasSociales AS $rsOS): ?>
                <?php if (in_array($rsOS['id_obras_sociales'], $aOSrec_ids) == '1' || $rsOS['abreviacion'] == 'PARTICULAR' ): ?>
                    <option id="opt<?=$rsOS['id_obras_sociales']?>" class="rec1" value="<?=$rsOS['id_obras_sociales']?>"><?=$rsOS['abreviacion']?> - <?=ucwords(lower($rsOS['nombre']))?></option>
                <?php endif ?>
                <?php endforeach; ?>
            </select>
        <div id="id_obras_sociales_div" style="text-align: center;"></div>
        </div>
        <?php endif; ?>
        <div class="clearfloat10"></div>
</div>



<script>
$(document).ready(function(){
    $('#id_obras_sociales').bind('change', function() {
        $('a[href="#next"]').click();
    });

    $('#id_obras_sociales').bind('change click', function() {

        $('#id_obras_sociales_div').hide();
        if ($('#osrec #opt'+$(this).val()).attr('class') == 'rec') {
            var os = $('#osrec #opt'+$(this).val()).html().split('-');
            os.shift();
            os = os.join('-').trim();
            $('#id_obras_sociales_div')
                .html(
                    '<b>ATENCIÓN</b><br /><strong>'+
                    '<?=ucwords($medicoSaludo)?>'+
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
        console.log($(this));
        $(this).addClass('search-day-a');
        if ($(this).text() == <?=(integer)$day?>) {
            $(this).parent().addClass('slctd');
            $(this).parent().addClass('search-day');
        }
    });
   
    $(document).on('click','search-day', function(event){
        event.preventDefault();
        console.log(2);
        $('#divLoading').html('<div class="opacityBackground"><div class="loading"><img src="assets/images/loading.gif" alt="" /></div></div>');
        $.ajax({
            url: $(this).attr('href'),
            context: document.body
        }).done(function(data) {
            var over = dividir(data);
               
            $('#calendar').html(over[0]);
            $('#obraSocial').html(over[1]);
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
            $('a[href="#next"]').click();
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

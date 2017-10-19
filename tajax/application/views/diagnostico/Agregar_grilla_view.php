<style>
.tab_float {
    float:left;
    margin: 0 8px 8px 0;
}
input[type="number"] {
    -moz-appearance: textfield;
}
#div_result_id_pacientes{
    display: inline;
    font-weight: bold;
    font-size: 16px;
    text-transform: uppercase;
    color: #ffffff;
    background-color: #008A47;
    padding: 2px 6px;
}
</style>

<?php if (isset($especialidades)): ?>
    Especialidad:<br />
    <select id="ag_id_especialidades" name="id_especialidades">
        <option value="">---</option>
        <?php foreach ($especialidades AS $row_esp): ?>
            <option
                value="<?=$row_esp['id_especialidades']?>"
                <?php
                if (
                    isset($id_especialidades) and
                    $id_especialidades and
                    $id_especialidades == $row_esp['id_especialidades']
                ):
                    ?>
                    selected="selected"
                    <?php
                endif;
                ?>
            ><?=
                utf8_encode(ucwords(upper(trim(utf8_decode(
                    $row_esp['nombre']
                )))))
            ?></option>
        <?php endforeach; ?>
    </select>
    <div id="div_result_id_pacientes"></div>
    <br />
<?php endif; ?>


<?php if (isset($id_especialidades) and $id_especialidades): ?>
    <div class="tab_float">
        Horario:<br />
        <select id="ag_horario" name="desde">
            <?php foreach ($aHorarios AS $rsH): ?>
                <?php for ($h = $rsH['desde']; $h <= $rsH['hasta']; $h = horaMM($h, $vcDuracionTurno)): ?>
                    <?php if (in_array($h, $aTurnosReservados)): ?>
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
                        <?php else: ?>
                            <?php if (date("Ymd") == $year.$month.$day and $h <= date("H:i:s", strtotime("+3 hours"))): ?>
                            <?php else: ?>
                                <?php
                                $coln = " style=\"color:#000;\"";
                                $colc = " style=\"color:#000;text-align:center;\"";
                                $colr = " style=\"color:#000;text-align:right;\"";
                                $idme = 'class="tdTab" data-method=';
                                ?>
                                    <option value="<?=$h?>"><?=substr($h, 0, 5)?></option>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php endfor; ?>
            <?php endforeach; ?>
            <option value="<?=$ultimo_horreal?>"><?=substr($ultimo_horreal, 0, 5)?> SOBRETURNO</option>
        </select>
    </div>
    <div class="tab_float">
        DNI del Paciente:<br />
        <input type="text" id="ag_id_pacientes" name="id_pacientes" value="" style="text-align:right;" class="ac_id_pacientes" />
    </div>
    <div class="tab_float">
        Estudio:<br />
        <select name="id_estudios">
            <?php foreach ($estudios AS $row_es): ?>
                <option
                    value="<?=$row_es['id_estudios']?>"
                ><?=
                utf8_encode(trim(utf8_decode(
                    $row_es['nombre']
                )))
                ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="tab_float">
        O. Social:<br />
        <select name="id_obras_sociales">
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
    <div class="tab_float">
        Fecha de Prestaci&oacute;n:<br />
        <input type="text" name="fecha_presentacion" value="" class="datepicker" />
    </div>
    <div class="tab_float">
        N° de Orden:<br />
        <input type="text" name="nro_orden" value="" />
    </div>
    <div class="tab_float">
        N° de Afiliado:<br />
        <input type="text" name="nro_afiliado" value="" />
    </div>
    <div class="tab_float">
        Cantidad:<br />
        <input type="text" name="cantidad" value="1" />
    </div>
    <div class="tab_float">
        Tipo:<br />
        <select name="tipo">
            <option value="1">A</option>
            <option value="2">I</option>
        </select>
    </div>
    <div class="tab_float">
        TP:<br />
        <select name="trajo_pedido">
            <option value="1">TP</option>
            <option value="2">No</option>
        </select>
    </div>
    <div class="tab_float">
        TO:<br />
        <select name="trajo_orden">
            <option value="1">TO</option>
            <option value="2">No</option>
        </select>
    </div>
    <div class="tab_float">
        TA:<br />
        <input type="number" name="trajo_arancel" value="" style="text-align:right;" />
    </div>
    <div class="tab_float">
        DD:<br />
        <input type="number" name="deja_deposito" value="" style="text-align:right;" />
    </div>
    <div class="tab_float">
        Derivador:<br />
        <input type="text" name="matricula_derivacion" value="" style="text-align:right;" class="ac_matricula_derivacion" />
    </div>
    <div style="float:none;clear:both;"></div>
    <div class="tab_float">
        <input id="ag_submit" type="button" value="Guardar" />
    </div>

<?php endif; ?>

<div style="float:none;clear:both;"></div>

<script>
$(document).ready(function(){
    $('.datepicker').datepicker();
    <?php if (isset($especialidades)): ?>
        var tagsACMD = [
            <?php $cnct = ''; ?>
            <?php if (isset($medicos_cm)): ?>
                <?php foreach ($medicos_cm AS $rs_mcm): ?>
                    <?=$cnct?>{label: '<?=utf8_encode(trim(utf8_decode($rs_mcm['apellidos'])))?>, <?=utf8_encode(trim(utf8_decode($rs_mcm['nombres'])))?> - <?=$rs_mcm['matricula']?>', value: '<?=$rs_mcm['matricula']?>'}
                    <?php $cnct = ','; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        ];
        $('.ac_matricula_derivacion').autocomplete({
            source: tagsACMD
        });
    <?php endif; ?>
    $('#ag_id_especialidades').change(function(event){
        event.preventDefault();
        ag_fecha = $('#ag_fecha').val().split('/');
        ag_fecha = ag_fecha[2] + '-' + ag_fecha[1] + '-' + ag_fecha[0];
        ag_id_medicos = $('#ag_id_medicos').val();
        ag_id_especialidades = $('#ag_id_especialidades').val();
        $('#AgregarGrilla').html('<div style="white-space: nowrap;"><img src="../files/img/ajax-loader.gif" alt=""> Espere un momento por favor</div>');
        ajxM = $.ajax({
            type: 'POST',
            url: '../tajax/index.php/<?=$this->router->fetch_class()?>/agregar_grilla/'+ag_fecha+'/'+ag_id_medicos+'/'+ag_id_especialidades,
            context: document.body
        }).done(function(data) {
            $('#AgregarGrilla').html(data);
        });
    });
    $('#ag_id_pacientes').change(function(){
        $.post(
            '../tajax/index.php/<?=$this->router->fetch_class()?>/buscar_paciente/',
            {'nro_documento': $(this).val()}
        ).done(function(data) {
            $('#div_result_id_pacientes').html(data);
        });
    });
    $('#ag_submit').click(function(){
        if ($('#ag_id_pacientes').val().trim() == '') {
            alert('Debe completar el campo de DNI del Paciente');
        } else if (!$('#ag_horario').val()) {
            alert('Debe tener disponible un horario de atención');
        } else {
            dataForm = $('#frmAgrTur').serialize();
            $('#AgregarGrilla').html('<div style="white-space: nowrap;"><img src="../files/img/ajax-loader.gif" alt=""> Espere un momento por favor</div>');
            ajxM = $.ajax({
                type: $('#frmAgrTur').attr('method'),
                url: $('#frmAgrTur').attr('action'),
                data: dataForm,
                context: document.body
            }).done(function(data) {
                $('#AgregarGrilla').html(data);
            });
        }
    });
});
</script>

<?php foreach ($aMPEPD AS $row): ?>
    <a
        class="aBtnD btn_medicos"
        href=""
        data-id_especialidades="<?=$row['id_especialidades']?>"
        data-id_medicos="<?=$row['id_medicos']?>"
    >
        <div>
            <?=$row['nombre']?><br />
            <?=$row['apellidos']?>
            <?=$row['nombres']?>
        </div>
    </a>
<?php endforeach; ?>
<script>
$(document).ready(function(){
    $('.aBtnD.btn_medicos').click(function(event){
        event.preventDefault();
        ajxM = $.ajax({
            type: 'POST',
            url:
                '../tajax/diagnostico/horarios/' +
                $(this).data('id_especialidades') +
                '/' +
                $(this).data('id_medicos') +
                '<?=date("/Y/m")?>'
            ,
            context: document.body
        }).done(function(data) {
            $('#diagnosticos_horarios').html(data);
            $('#diagnosticos_paciente').html('');
            $('#diagnosticos_datos').html('');
        });
    })
})
</script>
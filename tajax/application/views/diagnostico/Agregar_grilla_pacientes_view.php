<?php if (count($paciente) > 0): ?>
    <?=$paciente[0]['nombres']?>
    <?=$paciente[0]['apellidos']?>
<?php else: ?>
    no se encontró el paciente con el dni <?=$nro_documento?>
    <script>
    $('#ag_id_pacientes').val('').focus();
    </script>
<?php endif; ?>

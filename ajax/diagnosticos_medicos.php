<?php
require_once ("../engine/config.php");
require_once ("../engine/restringir_acceso.php");
requerir_class("tpl","mysql","querys","estructura");

//requerir_class("dias_semana");
$this_db = new MySQL();

$SQL = <<<SQL
    SELECT
        m.*,
        e.*
    FROM
        medicos AS m
    RIGHT JOIN medicos_especialidades AS me
        ON me.id_medicos = m.id_medicos
    RIGHT JOIN especialidades AS e
        ON me.id_especialidades = e.id_especialidades
    INNER JOIN medicos_horarios AS tt
        ON tt.id_medicos = m.id_medicos AND tt.id_especialidades = e.id_especialidades
    WHERE
        m.estado = 1 AND
        me.estado = 1 AND
        e.estado = 1 AND
        tt.estado = 1 AND
        tt.id_turnos_tipos IN (9, 10)
    GROUP BY
        e.nombre,
        m.apellidos,
        m.nombres
    ORDER BY
        e.nombre,
        m.apellidos,
        m.nombres
SQL;
$query = $this_db->consulta($SQL);

while ($row = $this_db->fetch_array($query)):
    $value = utf8_encode($row['nombre']." / ".$row['saludo']." ".$row['nombres']." ".$row['apellidos']);
    ?>
        <a
            class="diagnosticos_medicos"
            href=""
            data-value="<?=$row['id_especialidades']?>|<?=$row['id_medicos']?>"
        ><?=$value?></a><br />
    </a>
    <?php
endwhile;
?>
<script>
$(document).ready(function(){
    $('a.diagnosticos_medicos').click(function(event){
        event.preventDefault();
        ajxM = $.ajax({
            type: 'POST',
            url:'../ajax/diagnosticos_horarios.php',
			data: {step: "1"},
            context: document.body
        }).done(function(data) {
            $('#diagnosticos_horarios').html(data);
        });
    });
});
</script>
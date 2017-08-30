<style>
form select {
    padding: 8px 8px 9px 8px;
}
</style>
<h1>Prácticas Médicas</h1>
<h2>Asignar un Turno</h2>

<form id="frmAgrTur" action="../tajax/index.php/<?=$this->router->fetch_class()?>/agregar_turno/" method="post">

Fecha:<br />
<input
    type="text"
    id="ag_fecha"
    name="fecha"
    value="<?=date("d/m/Y", strtotime((isset($post['fecha']) and $post['fecha']) ? $post['fecha'] : date('Y-m-d')))?>"
    class="datepicker"
/><br />

Realizador:<br />
<select id="ag_id_medicos" name="id_medicos" style="width:120px;">
    <option value="">---</option>
    <?php foreach ($medicos AS $row_med): ?>
        <option
            value="<?=$row_med['id_medicos']?>"
        ><?=
            utf8_encode(ucwords(upper(trim(utf8_decode(
                $row_med['saludo'].' '.$row_med['apellidos'].', '.$row_med['nombres']
            )))))
        ?></option>
    <?php endforeach; ?>
</select>

<div id="AgregarGrilla" style="background-color:rgba(255, 255, 255, 0.85);"></div>

</form>

<script>
$(document).ready(function(){
    $('#ag_fecha, #ag_id_medicos').change(function(event){
        event.preventDefault();
        ag_fecha = $('#ag_fecha').val().split('/');
        ag_fecha = ag_fecha[2] + '-' + ag_fecha[1] + '-' + ag_fecha[0];
        ag_id_medicos = $('#ag_id_medicos').val();
        $('#AgregarGrilla').html('<div style="white-space: nowrap;"><img src="../files/img/ajax-loader.gif" alt=""> Espere un momento por favor</div>');
        ajxM = $.ajax({
            type: 'POST',
            url: '../tajax/index.php/<?=$this->router->fetch_class()?>/agregar_grilla/'+ag_fecha+'/'+ag_id_medicos,
            context: document.body
        }).done(function(data) {
            $('#AgregarGrilla').html(data);
        });
    });
});
</script>
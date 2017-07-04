<h1>Modificar Usuario</h1>

<?=form_open(base_url().$this->router->fetch_class().'/guardar_modificar/'.$item['id_turnos']);?>

    Fecha:
    <?=form_input('fecha', set_value('fecha', $item['fecha']))?>
    <div class="validation_errors"><?php echo form_error('fecha'); ?></div>
    <br />

    Apellido:
    <?=form_input('apellido', set_value('apellido', $item['apellido']))?>
    <div class="validation_errors"><?php echo form_error('apellido'); ?></div>
    <br />

    Sexo:
    <select name="sexoid">
        <option value="">-Por favor seleccione el sexo</option>
        <?php foreach($sexo_listado As $sexo_item):?>
            <option
                value="<?=$sexo_item['sexoid'];?>"
                <?php if ($sexo_item['sexoid'] == set_value('sexoid', $item['sexoid'])): ?>
                    selected="selected"
                <?php endif; ?>
            >
                <?=$sexo_item['descripcion'];?>
            </option>
        <?php endforeach;?>
    </select>
    <div class="validation_errors"><?php echo form_error('sexoid'); ?></div>
    <br />

    Fecha de Nacimiento:
    <?=form_input('fechanac', set_value('fechanac', $item['fechanac']))?>
    <div class="validation_errors"><?php echo form_error('fechanac'); ?></div>
    <br />

    Dni:
    <?=form_input('dni', set_value('dni', $item['dni']))?>
    <div class="validation_errors"><?php echo form_error('dni'); ?></div>
    <br />

    Estado:
    <input type="radio" id="estado1" name="estado" value="1"<?=set_value_boolean('estado', $item['estado']) != '0' ? ' checked="checked"' : ''?>/>
    <label for="estado1">Activo</label>
    <input type="radio" id="estado2" name="estado" value="0"<?=set_value_boolean('estado', $item['estado']) == '0' ? ' checked="checked"' : ''?>/>
    <label for="estado2">Borrado</label>
    <div class="validation_errors"><?php echo form_error('estado'); ?></div>
    <br />

    <input type="submit" value="Guardar"/>
    <a href="<?=base_url().$this->router->fetch_class()?>/listado">Volver</a>

<?=form_close();?>

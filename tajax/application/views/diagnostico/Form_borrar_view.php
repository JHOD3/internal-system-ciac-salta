<h1>Eliminar Usuario</h1>

<?=form_open(base_url().$this->router->fetch_class().'/guardar_borrar');?>

    <table>
        <tr>
            <td>ID:</td>
            <td><?=$item['id_turnos']?></td>
        </tr>
        <tr>
            <td>Fecha:</td>
            <td><?=$item['fecha']?></td>
        </tr>
        <tr>
            <td>Apellido:</td>
            <td><?=$item['apellido']?></td>
        </tr>
        <tr>
            <td>Fecha de Nacimiento:</td>
            <td><?=$item['fechanac']?></td>
        </tr>
         <tr>
            <td>DNI:</td>
            <td><?=$item['dni']?></td>
        </tr>
         <tr>
            <td>Sexo:</td>
            <td><?=$item['descripcion']?></td>
        </tr>

        <tr>
            <td>Estado:</td>
            <td><?=$item['estado'] == 'f' ? 'Borrado' : 'Activo'?></td>
        </tr>
    </table>

    <input type="hidden" name="id_turnos" value="<?=$item['id_turnos']?>"/>
    <input type="submit" value="Borrar"/>
    <a href="<?=base_url().$this->router->fetch_class()?>/listado">Volver</a>

<?=form_close();?>

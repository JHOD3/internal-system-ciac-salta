<table id="tblDxI" cellspacing="0" cellpadding="0" border="0">
    <tbody>
        <tr class="trHead">
            <td>Fecha/Hora</td>
            <td>Cod. Alt.</td>
            <td>Real.</td>
            <td>O.Social</td>
            <td>Presen.</td>
            <td>N°Ord.</td>
            <td>N°Afil.</td>
            <td>C.</td>
            <td>T.</td>
            <td>TP</td>
            <td>TO</td>
            <td>TA</td>
            <td>DD</td>
            <td>Diferencia</td>
            <td>Depósito Fecha</td>
            <td>Der.</td>
            <td>Obs.</td>
            <td>Usuario</td>
        </tr>
        <?php
        $tmpTEH = array();
        ?>
        <?php foreach ($aTEH AS $itmTEH): ?>
            <tr>
                <td><?=$itmTEH['fechahora']?></td>
                <?=compare($tmpTEH, $itmTEH, 'codigoalternat')?><?=$itmTEH['codigoalternat']?></td>
                <?=compare($tmpTEH, $itmTEH, 'medicos')?><?=$itmTEH['medicos']?></td>
                <?=compare($tmpTEH, $itmTEH, 'obras_sociales')?><?=$itmTEH['obras_sociales']?></td>
                <?=compare($tmpTEH, $itmTEH, 'fecha_presentacion')?><?=$itmTEH['fecha_presentacion']?></td>
                <?=compare($tmpTEH, $itmTEH, 'nro_orden')?><?=$itmTEH['nro_orden']?></td>
                <?=compare($tmpTEH, $itmTEH, 'nro_afiliado')?><?=$itmTEH['nro_afiliado']?></td>
                <?=compare($tmpTEH, $itmTEH, 'cantidad')?><?=$itmTEH['cantidad']?></td>
                <?=compare($tmpTEH, $itmTEH, 'tipo')?><?=$itmTEH['tipo']?></td>
                <?=compare($tmpTEH, $itmTEH, 'trajo_pedido')?><?=$itmTEH['trajo_pedido']?></td>
                <?=compare($tmpTEH, $itmTEH, 'trajo_orden')?><?=$itmTEH['trajo_orden']?></td>
                <?=compare($tmpTEH, $itmTEH, 'trajo_arancel')?><?=$itmTEH['trajo_arancel']?></td>
                <?=compare($tmpTEH, $itmTEH, 'deja_deposito')?><?=$itmTEH['deja_deposito']?></td>
                <?=compare($tmpTEH, $itmTEH, 'deja_deposito_diferencia')?><?=$itmTEH['deja_deposito_diferencia']?></td>
                <?=compare($tmpTEH, $itmTEH, 'deja_deposito_fecha')?><?=$itmTEH['deja_deposito_fecha']?></td>
                <?=compare($tmpTEH, $itmTEH, 'matricula_derivacion')?><?=$itmTEH['matricula_derivacion']?></td>
                <?=compare($tmpTEH, $itmTEH, 'observaciones')?><?=$itmTEH['observaciones']?></td>
                <td><?=$itmTEH['usuarios']?></td>
            </tr>
            <?php
            $tmpTEH = $itmTEH;
            ?>
        <?php endforeach; ?>
    </tbody>
</table>

<style>
* {
    margin: 0;
    padding: 0;
    border: none;
    outline: 0;
    font-family: Arial, Verdana;
    font-size: 13px;
}
#tblDxI{
    background-color: #fff;
    margin: 10px;
}
#tblDxI td{
    text-align: left;
    vertical-align: middle;
    color: #007FA6;
}
#tblDxI tr:nth-child(even) td{
    background-color:#e9e9e9;
}
#tblDxI tr > *{
    line-height: 14px;
    padding: 8px;
}
#tblDxI tr:nth-child(even) td:first-child,
#tblDxI tbody tr.trHead td:first-child{
    border-radius: 4px 0 0 4px;
}
#tblDxI tr:nth-child(even) td:last-child,
#tblDxI tbody tr.trHead td:last-child{
    border-radius: 0 4px 4px 0;
}
#tblDxI tbody tr.trHead td,
#tblDxI tfoot tr th{
    font-weight: bold;
    background-color: #007FA6;
    color: #ffffff;
}
#tblDxI tfoot tr th{
    border-radius: 4px;
}
#tblDxI tbody tr.trDate td{
    font-size: 1.5em;
    color: #008A47;
    padding: 0 6px 6px 6px;
}
#tblDxI .aBtnL > a,
#tblDxI .aBtnR > a{
    font-size: 12px;
    font-weight: bold;
    float: right;
    background-color: #008A47;
    display: block;
    padding:1px 6px;
    margin: 0 0 0 2px;
    color: #ffffff;
    border-radius: 4px;
    box-shadow: 1px 1px 2px #999;
}
#tblDxI .aBtnL > a,
#tblDxI .aBtnL > strong{
    float: left;
    margin: 0 2px 0 0;
}
#tblDxI .trDate strong {
    width: 370px;
    text-align: center;
}
#tblDxI tr.tsEst0 td{
    text-decoration:line-through;
    background-color: #f0f0f0;
    color: #999!important;
}
#tblDxI td.frst{
    font-weight: bold;
}
#tblDxI td.same{
    color: #ccc;
}
#tblDxI td.diff{
    font-weight: bold;
    color: #c00;
}
</style>
<?php
function compare($tmpTEH, $itmTEH, $field)
{
    if (count($tmpTEH) == 0) {
        return '<td class="frst">';
    } else {
        if ($tmpTEH[$field] == $itmTEH[$field]) {
            return '<td class="same">';
        } else {
            return '<td class="diff">';
        }
    }
}
?>
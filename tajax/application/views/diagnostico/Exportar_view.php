<?php
$date3 = date("Y-m-d_His");
header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=Practicas-Medicas-Exportado-{$date3}.xls");
?>
<table>
    <thead>
        <?php foreach ($set_heading AS $sh): ?>
            <th><?=$sh?></th>
        <?php endforeach; ?>
    </thead>
    <tbody>
        <?php foreach ($aDiagnosticos AS $rsD): ?>
            <tr>
                <td><?=$rsD['orden']?></td>
                <td><?=$rsD['pacientes']?></td>
                <td><?=$rsD['presentador']?></td>
                <td><?=$rsD['medicos']?></td>
                <td><?=$rsD['obras_sociales']?></td>
                <td style="mso-number-format: 'dd/mm/yyyy';"><?=$rsD['fecha']?></td>
                <td style="mso-number-format: '###0';"><?=$rsD['nro_orden']?></td>
                <td style="mso-number-format: '###0';"><?=$rsD['nro_afiliado']?></td>
                <td><?=$rsD['nombre']?></td>
                <td style="mso-number-format: '###0';"><?=$rsD['codigopractica']?></td>
                <td style="mso-number-format: '###0';"><?=$rsD['cantidad']?></td>
                <td><?=$rsD['tipo']?></td>
                <td><?=$rsD['trajo_pedido']?></td>
                <td><?=$rsD['trajo_orden']?></td>
                <td style="mso-number-format: 'Currency';"><?=isset($rsD['trajo_arancel']) ? "\$ {$rsD['trajo_arancel']},00": ''?></td>
                <td style="mso-number-format: 'Currency';"><?=isset($rsD['deja_deposito']) ? "\$ {$rsD['deja_deposito']},00": ''?></td>
                <td><?=$rsD['matricula_derivacion']?></td>
                <td><?=$rsD['observaciones']?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
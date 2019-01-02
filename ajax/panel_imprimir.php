<?php
require_once ("../engine/config.php");
require_once ("../engine/restringir_acceso.php");
requerir_class("tpl","mysql","querys","estructura");
$this_db = new MySQL();

/******************************************************************************/
if (empty($_POST['opc'])):
    ?>
    <img
        src="../files/img/logo_imprimir.png"
        style="width:100%; margin:auto; text-align:center; display: inherit; max-width: 600px;"
        alt=""
    />
    <h2 style="font-size:12px; font-family:Arial; text-align:center; font-weight:normal; line-height: normal;">Santiago del Estero 449 &shy; A4400BKI - Salta - Argentina<br />Tel: 4214738 - 4213251<br />administracion@ciacsalta.com.ar<br />Lunes a viernes de 7.30 a 21 hs</h2>
    <a href="#" class="btn btn-info noPrint" style="margin-top: 6px;" id="postItSend">Imprimir</a>
    <a href="#" class="btn btn-success noPrint" style="margin-top: 6px;" id="postItClean">Limpiar</a>
    <?php
    $query_string = <<<SQL
        SELECT
            E.id_estudios,
            E.nombre
        FROM
            estudios AS E
        INNER JOIN
            medicos_estudios AS ME
            ON ME.id_estudios = E.id_estudios
        INNER JOIN
            medicos AS M
            ON ME.id_medicos = M.id_medicos
        INNER JOIN
            medicos_horarios AS MH
            ON MH.id_medicos = M.id_medicos
        INNER JOIN
            turnos_tipos AS TT
            ON MH.id_turnos_tipos = TT.id_turnos_tipos
        WHERE
            E.estado = 1 AND
            ME.estado = 1 AND
            M.estado = 1 AND
            MH.estado = 1 AND
            TT.estado = 1 AND
            TT.tipo = 'ESTUDIOS'
        GROUP BY
            E.nombre
        ORDER BY
            E.nombre
SQL;
    $result = $this_db->consulta($query_string);
    ?>
    <select id="ddEstudio" class="noPrint">
        <option value="">- Elija un estudio -</option>
        <?php while ($row = $this_db->fetch_assoc($result)): ?>
            <option value="<?=$row['id_estudios']?>"><?=utf8_encode($row['nombre'])?></option>
        <?php endwhile; ?>
    </select>
    <script>
    $('#ddEstudio').change(function(event) {
		$.ajax({
			type: "POST",
			url: "../ajax/panel_imprimir.php",
            data: {'opc': 'getEstudio', 'id_estudios': $('#ddEstudio').val()},
			success: function(requestData){
                $('#panelEstudioList').append(requestData);
                $('#ddEstudio').val('');
			}
		});
    });
    $('#postItSend').click(function(event) {
        event.preventDefault();
        window.print();
    });
    $('#postItClean').click(function(event) {
        event.preventDefault();
        $('#panelEstudioList').html('');
    });
    </script>
    <div id="panelEstudioList" style="background-color:#fff;"></div>
    <textarea></textarea>
    <?php
/******************************************************************************/
elseif($_POST['opc'] == 'getEstudio'):
    $query_string = <<<SQL
        SELECT
            *
        FROM
            estudios AS E
        WHERE
            E.estado = 1 AND
            E.id_estudios = '{$_POST['id_estudios']}'
        ORDER BY
            E.nombre
        LIMIT 1
SQL;
    $result = $this_db->consulta($query_string);
    if ($row = $this_db->fetch_assoc($result)):
        ?>
        <div class="divClose">
            <hr />
            <a
                href="#"
                class="btnClose"
            >quitar estudio</a>
            <div>
                <div>
                    <strong>
                        ESTUDIO: <?=utf8_encode($row['nombre'])?>
                        <?php if (trim($row['codigopractica'])): ?>
                            / CÓD:
                            <?=utf8_encode(number_format($row['codigopractica'], 0, ",", "."))?>
                        <?php endif; ?>
                    </strong>
                </div>
                <?php if (trim($row['requisitos'])): ?>
                    <div><strong>Requisitos:</strong> <?=utf8_encode($row['requisitos'])?></div>
                <?php endif; ?>
                <div class="divClose">
                    <a
                        href="#"
                        class="btnClose"
                    >quitar importes</a>
                    <?php if ($row['importe'] > 0): ?>
                        <strong>Importe:</strong> $<?=utf8_encode(number_format($row['importe'], 0, ",", "."))?>
                    <?php endif; ?>
                    <?php if ($row['importe'] > 0 and $row['arancel'] > 0): ?>
                        /
                    <?php endif; ?>
                    <?php if ($row['arancel'] > 0): ?>
                        <strong>Arancel:</strong> $<?=utf8_encode(number_format($row['arancel'], 0, ",", "."))?>
                    <?php endif; ?>
                </div>
                <div>
                    <?php
                    $query_string2 = <<<SQL
                        SELECT
                            NI.*
                        FROM
                            notas_impresion AS NI
                        INNER JOIN
                            notas_impresion_estudios AS NIE
                            ON NIE.id_notas_impresion = NI.id_notas_impresion
                        WHERE
                            NI.estado = 1 AND
                            NIE.estado = 1 AND
                            NIE.id_estudios = '{$_POST['id_estudios']}'
                        ORDER BY
                            NI.nombre
SQL;
                    $result2 = $this_db->consulta($query_string2);
                    while ($row2 = $this_db->fetch_assoc($result2)):
                        ?>
                        <div><?=nl2br(utf8_encode($row2['detalle']))?></div>
                        <?php
                    endwhile;
                    ?>
                </div>
            </div>
        </div>
        <script>
        $('.countChildRemove').each(function(){
            if (console && console.log) console.log($(this).children());
            if (console && console.log) console.log($(this).children().length);
            if ($(this).children().length == 0) {
                $(this).parent().parent().parent().remove();
            }
        });
        $('.btnClose').click(function(event) {
            event.preventDefault();
            $(this).parent().remove();
        });
        </script>
        <?php
    endif;
/******************************************************************************/
endif;
?>
<style media="screen">
textarea {
    width: 100%;
    margin-bottom: 100px;
    font-style: italic;
}
.divClose {
    border: 1px solid #d4d6d8;
    padding: 5px;
    margin: 0 0 5px 0;
}
.btnClose {
    float: right;
    display: block;
    font-size: 11px;
    color: #2f96b4;
    text-align: right;
}
</style>
<style media="print">
textarea {
    border: none;
    width: 100%;
    font-size: 13px;
    margin: 0px;
    padding: 0px;
    font-style: italic;
}
.btnClose,
hr {
    display: none;
    visibility: hidden;
    width: 0px;
    height: 0px;
    overflow: hidden;
}
</style>
<?php
//EOF panel_imprimir.php

<?php
if (is_array($_GET) and $_GET['get'] and ($_GET['get'] > 0 or $_GET['get'] == '-1')) {
    require_once("../engine/config.php");
    require_once("../engine/restringir_acceso.php");
    requerir_class("tpl","querys","mysql","estructura","json");

    $this_db = new MySQL();

    if ($_GET['get'] > 0) {
        if ($_SESSION['TIPO_USR'] == 'M') {
            $sql = "
                UPDATE
                    novedades_medicos AS nm
                SET
                    nm.confirmacion = '".date("Y-m-d H:i:s")."'
                WHERE
                    nm.id_medicos = '{$_SESSION['ID_MEDICO']}' AND
                    nm.id_novedades = '{$_GET['get']}'
                LIMIT 1
            ";
        } else {
            $sql = "
                UPDATE
                    novedades_usuarios AS no
                SET
                    no.confirmacion = '".date("Y-m-d H:i:s")."'
                WHERE
                    no.id_usuarios = '{$_SESSION['ID_USUARIO']}' AND
                    no.id_novedades = '{$_GET['get']}'
                LIMIT 1
            ";
        }
        $this_db->consulta($sql);
    }
    if ($_SESSION['TIPO_USR'] == 'M') {
        $sql = "
            SELECT
                n.*
            FROM
                novedades AS n
            INNER JOIN novedades_medicos AS nm
                ON nm.id_novedades = n.id_novedades
            WHERE
                nm.id_medicos = '{$_SESSION['ID_MEDICO']}' AND
                nm.confirmacion IS NULL
            ORDER BY
                n.fechahora ASC
            LIMIT 1
        ";
    } else {
        $sql = "
            SELECT
                n.*
            FROM
                novedades AS n
            INNER JOIN novedades_usuarios AS no
                ON no.id_novedades = n.id_novedades
            WHERE
                no.id_usuarios = '{$_SESSION['ID_USUARIO']}' AND
                no.confirmacion IS NULL
            ORDER BY
                n.fechahora ASC
            LIMIT 1
        ";
    }
    $novedades = $this_db->consulta($sql);
    if ($nov = $this_db->fetch_array($novedades)):
        ?>
        <div>
            <input type="hidden" id="dieAjaxNovedadesHidden" name="dieAjaxNovedadesHidden" value="<?=$nov['id_novedades']?>" />
            <h1 style="color:#007FA6;"><?=utf8_encode($nov['titulo'])?></h1>
            <h4 style="color:#007FA6;"><?=date("d/m/Y H:i", strtotime($nov['fechahora']))?>hs.</h4>
            <div style="color:#008A47;font-size:20px;"><?=utf8_encode(nl2br($nov['contenido']))?></div>
            <br />
            <div>
                <input id="dieAjaxNovedadesButton" type="button" value="He le&iacute;do" />
                <input id="dieAjaxNovedadesCancel" type="button" value="Leer despu&eacute;s" />
            </div>
        </div>
        <style>
        #dieAjaxNovedadesDiv{
            position:absolute;
            top:10px;
            left:10px;
            z-index:895879437;
            background-color:#f5f5f5;
            border-radius:10px;
            border:2px solid #CCCCCC;
            padding:30px;
        }
        #dieAjaxNovedadesButton,
        #dieAjaxNovedadesCancel{
            background-color:#008A47;
            color:white;
            border:none;
            padding:10px 20px;
            border-radius:3px;
            font-size:22px;
        }
        #dieAjaxNovedadesCancel{
            background-color:#999;
        }
        </style>
        <script>
        $(function() {
            $('#dieAjaxNovedadesButton').click(function(event){
                event.preventDefault();
                id = $('#dieAjaxNovedadesHidden').val();
                $('#dieAjaxNovedadesDiv').html('');
                $.ajax({
                    url: "../ajax/novedades.php?get=" + id,
                    context: document.body
                }).done(function(data) {
                    $('#dieAjaxNovedadesDiv').html(data);
                });
            });
            $('#dieAjaxNovedadesCancel').click(function(event){
                event.preventDefault();
                $('#dieAjaxNovedadesDiv').html('');
            });
        });
        </script>
        <?php
    endif;
    die;
}

if (
    is_array($_POST) and
    isset($_POST['inpTitulo']) and
    $_POST['inpTitulo'] and
    isset($_POST['textContenido']) and
    $_POST['textContenido']
) {
    require_once("../engine/config.php");
    requerir_class("tpl","querys","mysql","estructura","json");

    $this_db = new MySQL();

    $sql = "
        INSERT INTO
            novedades
            (titulo, contenido, fechahora)
        VALUES
            (
                '".str_replace("'", "\\'", utf8_decode($_POST['inpTitulo']))."',
                '".str_replace("'", "\\'", utf8_decode($_POST['textContenido']))."',
                '".date("Y-m-d H:i:s")."'
            )
    ";
    $this_db->consulta($sql);
    $last_insert_id = $this_db->ultimo_id_insertado();

    for ($i = 0; $i < count($_POST['selOperadores']); $i++) {
        $sql = "
            INSERT INTO
                novedades_usuarios
                (id_novedades, id_usuarios)
            VALUES
                (
                    '{$last_insert_id}',
                    '{$_POST['selOperadores'][$i]}'
                )
        ";
        $this_db->consulta($sql);
    }

    for ($i = 0; $i < count($_POST['selMedicos']); $i++) {
        $sql = "
            INSERT INTO
                novedades_medicos
                (id_novedades, id_medicos)
            VALUES
                (
                    '{$last_insert_id}',
                    '{$_POST['selMedicos'][$i]}'
                )
        ";
        $this_db->consulta($sql);
    }

    header("Location: ../sas/index.php");
    die;
}

$isAdmin = ($_SESSION['ID_USUARIO'] == '0' and $_SESSION['TIPO_USR'] == 'U');

$this_db = new MySQL();

$sql = "
    SELECT *
    FROM
        medicos
    WHERE
        estado = 1
    ORDER BY
        saludo,
        nombres,
        apellidos
";
$medicos = $this_db->consulta($sql);

$sql = "
    SELECT *
    FROM
        usuarios
    WHERE
        estado = 1 AND
        id_usuarios NOT IN (-1, 0, 1)
    ORDER BY
        nombres,
        apellidos
";
$operadores = $this_db->consulta($sql);
?>
<div>
    <?php if ($isAdmin): ?>
        <form action="../ajax/novedades.php" method="post">
            <h3>Agregar Novedad Nueva</h3>
            <table style="width:100%;">
                <tbody>
                    <tr valign="top">
                        <td style="width:100%;">
                            <strong>T&iacute;tulo de la Novedad</strong><br />
                            <input id="inpTitulo" name="inpTitulo" style="min-width:300px;width:99%;" /><br />
                            <br />
                            <strong>Contenido de la Novedad</strong><br />
                            <textarea id="textContenido" name="textContenido" style="min-width:300px;width:98%;height:200px;text-transform:none!important;"></textarea><br />
                            <br />
                            <input id="inpSubmit" type="submit" value="Guardar" />
                        </td>
                        <td>&nbsp;&nbsp;&nbsp;</td>
                        <td>
                            <strong>Operadores que la leer&aacute;n</strong><br />
                            <select id="selOperadores" name="selOperadores[]" multiple="1" size="16" style="width:300px;">
                                <?php while ($ope = $this_db->fetch_array($operadores)): ?>
                                    <option value="<?=$ope['id_usuarios']?>"><?=utf8_encode($ope['nombres'].", ".$ope['apellidos'])?></option>
                                <?php endwhile; ?>
                            </select>
                        </td>
                        <td>&nbsp;&nbsp;&nbsp;</td>
                        <td>
                            <strong>M&eacute;dicos que la leer&aacute;n</strong><br />
                            <select id="selMedicos" name="selMedicos[]" multiple="1" size="16" style="width:300px;">
                                <?php while ($med = $this_db->fetch_array($medicos)): ?>
                                    <option value="<?=$med['id_medicos']?>"><?=utf8_encode($med['saludo']." ".$med['nombres'].", ".$med['apellidos'])?></option>
                                <?php endwhile; ?>
                            </select>
                        </td>
                    </tr>
                </tbody>
            </table>
        </form>
    <?php endif; ?>
</div>

<?php
if ($isAdmin) {
    $where = "";
} elseif ($_SESSION['TIPO_USR'] == 'U') {
    $where = "
        INNER JOIN novedades_usuarios AS no
            ON no.id_novedades = n.id_novedades
        WHERE
            no.id_usuarios = '{$_SESSION['ID_USUARIO']}'
    ";
}

$sql = "
    SELECT
        n.*
    FROM
        novedades AS n
    {$where}
    ORDER BY
        n.fechahora DESC
";
$novedades = $this_db->consulta($sql);
?>
<?php while ($nov = $this_db->fetch_array($novedades)): ?>
    <div style="<?=$isAdmin ? 'margin:0 0 10px 0;' : 'float:left;margin:0 10px 10px 0;'?>border:1px solid #ccc;background-color:#f5f5f5;border-radius:3px;padding:5px 10px;">
        <table style="width:100%;">
            <tbody>
                <tr valign="top">
                    <td<?=$isAdmin ? ' style="width:40%;"' : ''?>>
                        <div><strong><?=utf8_encode($nov['titulo'])?></strong> - <?=date("d/m/Y H:i", strtotime($nov['fechahora']))?>hs.</div>
                        <div><?=utf8_encode(nl2br($nov['contenido']))?></div>
                    </td>
                    <?php if ($isAdmin): ?>
                        <td style="width:35%;">
                            <div style="overflow:auto;max-height:200px;">
                                <table>
                                    <tbody>
                                        <?php
                                        $sql = "
                                            SELECT
                                                CONCAT(
                                                    m.saludo,
                                                    ' ',
                                                    m.nombres,
                                                    ', ',
                                                    m.apellidos
                                                ) AS nombre,
                                                nm.confirmacion
                                            FROM
                                                novedades_medicos AS nm
                                            INNER JOIN
                                                medicos AS m
                                                ON nm.id_medicos = m.id_medicos
                                            WHERE
                                                nm.id_novedades = '{$nov['id_novedades']}'
                                            UNION
                                                SELECT
                                                    CONCAT(
                                                        u.nombres,
                                                        ', ',
                                                        u.apellidos
                                                    ) AS nombre,
                                                    nu.confirmacion
                                                FROM
                                                    novedades_usuarios AS nu
                                                INNER JOIN
                                                    usuarios AS u
                                                    ON nu.id_usuarios = u.id_usuarios
                                                WHERE
                                                    nu.id_novedades = '{$nov['id_novedades']}'
                                            ORDER BY
                                                confirmacion ASC,
                                                nombre ASC
                                        ";
                                        $visto1 = $this_db->consulta($sql);
                                        $visto2 = $this_db->consulta($sql);
                                        ?>
                                        <?php $i = 1; ?>
                                        <?php while ($vis = $this_db->fetch_array($visto1)): ?>
                                            <?php if (isset($vis['confirmacion']) and $vis['confirmacion']): ?>
                                                <?php if ($i == 1): ?>
                                                    <tr valign="top">
                                                        <td colspan="5"><strong style="color:#008A47;">CONFIRMACIONES DE LECTURA</strong></td>
                                                    </tr>
                                                <?php endif; ?>
                                                <tr valign="top">
                                                    <td style="color:#008A47;"><?=$i?>.-</td>
                                                    <td>&nbsp;&nbsp;</td>
                                                    <td style="color:#008A47;"><?=utf8_encode($vis['nombre'])?></td>
                                                    <td>&nbsp;&nbsp;</td>
                                                    <td style="color:#008A47;">Le&iacute;do el <?=date("d/m/Y H:i", strtotime($vis['confirmacion']))?>hs</td>
                                                </tr>
                                                <?php $i++; ?>
                                            <?php endif; ?>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </td>
                        <td style="width:25%;">
                            <div style="overflow:auto;max-height:200px;">
                                <table>
                                    <tbody>
                                        <?php $j = 1; ?>
                                        <?php while ($vis = $this_db->fetch_array($visto2)): ?>
                                            <?php if (!isset($vis['confirmacion']) or !$vis['confirmacion']): ?>
                                                <?php if ($j == 1): ?>
                                                    <tr valign="top">
                                                        <td colspan="3"><strong style="color:#007FA6;">PENDIENTES DE LECTURA</strong></td>
                                                    </tr>
                                                <?php endif; ?>
                                                <tr valign="top">
                                                    <td style="color:#007FA6;"><?=$j?>.-</td>
                                                    <td>&nbsp;&nbsp;</td>
                                                    <td style="color:#007FA6;"><?=utf8_encode($vis['nombre'])?></td>
                                                </tr>
                                                <?php $j++; ?>
                                            <?php endif; ?>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </td>
                    <?php endif; ?>
                </tr>
            </tbody>
        </table>
    </div>
<?php endwhile; ?>

<?php

//EOF novedades.php

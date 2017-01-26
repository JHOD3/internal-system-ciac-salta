<?php
require_once("../engine/config.php");
requerir_class("tpl","querys","mysql","estructura");

ob_start();

$this_db = new MySQL();

$req = "'".str_replace("||", "', '", utf8_decode($_POST['req']))."'";

$sql = "
    SELECT
        nombre,
        importe,
        requisitos
    FROM
        estudios
    WHERE
        nombre IN ({$req})
";
$result = $this_db->consulta($sql);
while ($row = $this_db->fetch_array($result)) {
    ?>
    <div><strong style="color:#008A47;"><?=$row['nombre']?></strong> - <strong style="color:#007FA6;">Importe: $<?=$row['importe']?></strong></div>
    <?php if (trim($row['requisitos'])): ?>
        <div><strong>Requisitos:</strong> <?=$row['requisitos']?></div>
    <?php endif; ?>
    <br />
    <?php
}

print utf8_encode(ob_get_clean());

// EOF operaciones_requisitos_estudios.php

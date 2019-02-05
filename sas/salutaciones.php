<?php
ob_start();
?>
<script>
$(function(){
<?php
$mesdia = date("m-d");
$query_string = <<<SQL
    SELECT
        *
    FROM
        novedades
    WHERE
        mesdia = '{$mesdia}'
SQL;
$result = $this_db->consulta($query_string);
if ($this_db->num_rows($result) > 0) {
    while ($row = $this_db->fetch_assoc($result)) {
        ?>
        $('body').prepend('<div class="imgHB"><a href="#"><div style="background-image:url(../files/img/salutaciones.jpg);position:absolute;z-index:999999;"><h1><?=$row['titulo']?></h1><h3><?=$row['contenido']?></h3></div></a></div>');
        <?php
    }
}
?>
    $('div.imgHB').click(function(event){
        event.preventDefault();
        $(this).remove();
        return false;
    });
});
</script>
<?php
$html_cumple.= ob_get_clean();

$_SESSION['felicitado'] = true;

//EOF salutaciones.php

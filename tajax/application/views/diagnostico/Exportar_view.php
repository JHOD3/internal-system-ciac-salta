<?php
header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=ciac-salta-dxi-desde-{$date1}-hasta-{$date2}.xls");
?>
<?=utf8_decode($this->table->generate($aDiagnosticos))?>
<?php
header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=ciac-salta-dxi-{$year}-{$month}.xls");
?>
<?=utf8_decode($this->table->generate($aDiagnosticos))?>
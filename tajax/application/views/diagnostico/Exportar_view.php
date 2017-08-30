<?php
$date3 = date("Y-m-d_His");
header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=Practicas-Medicas-Exportado-{$date3}.xls");
?>
<?=utf8_decode($this->table->generate($aDiagnosticos))?>
<?php

$desde = $_POST['desde'];
$d = DateTime::createFromFormat('Y-m-d', $desde);
if (!$d or $d->format($format) != $date or strlen($desde) != 10) {
    $desde = date('Y-m-d', strtotime('-1 month +1 day'));
}
$desde_text = date('d/m/Y', strtotime($desde));

$hasta = $_POST['hasta'];
$d = DateTime::createFromFormat('Y-m-d', $hasta);
if (!$d or $d->format($format) != $date or strlen($hasta) != 10) {
    $hasta = date('Y-m-d');
}
$hasta_text = date('d/m/Y', strtotime($hasta));

$html_graph = <<<EOT
    <form style="margin: 0 auto;width:480px;" action="index.php?show=estadisticas" method="post">
        Desde: <input type="date" name="desde" value="{$desde}" style="width:150px;" />
        Hasta: <input type="date" name="hasta" value="{$hasta}" style="width:150px;" />
        <input type="submit" value="ok"/>
    </form>
EOT;

$this_db = new MySQL();
$html_graph.= '<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>';
if ($_GET['show'] != 'estadisticas') {

    $html_button = <<<EOT
<a id="btnEst" href="index.php?show=estadisticas" class="btn" data-nombre="estadisticas" style="float:left!important;">
	<img src="../files/img/btns/estadisticas.png" width="30" />
    <span>Estad&iacute;sticas</span>
</a>
EOT;

} else {

    $html_graph.= "<script>\$(document).ready(function(){setTimeout(function(){\$('html, body').animate({scrollTop:460}, 920);}, 1000)});</script>";

    $html_button = <<<EOT
<a id="btnEst" href="index.php" class="btn" data-nombre="estadisticas" style="float:left!important;">
	<img src="../files/img/btns/estadisticas.png" width="30" />
    <span>Ocultar Estad.</span>
</a>
EOT;

    // graph 1
    $sql = "
        SELECT
            DATE_FORMAT(t.fecha, '%d/%m') AS myfecha,
            COUNT(t.id_turnos) AS total
        FROM
            turnos AS t
        WHERE
            t.id_medicos = '{$_SESSION['ID_MEDICO']}' AND
            t.fecha BETWEEN '{$desde}' AND '{$hasta}'
        GROUP BY
            t.fecha
    ";
	$query = $this_db->consulta($sql);

    $html_graph.= <<<EOT
<script type="text/javascript">
  google.charts.load('current', {'packages':['corechart']});
  google.charts.setOnLoadCallback(drawChart1);

  function drawChart1() {
    var data1 = google.visualization.arrayToDataTable([
      ['Fecha', 'Turnos', {role: 'style'}]
EOT;
	while ($row = $this_db->fetch_array($query)){
      $html_graph.= utf8_encode(",['{$row['myfecha']}',  {$row['total']}, '#007FA6']\n");
    }
    $html_graph.= <<<EOT
    ]);

    var options1 = {
      title: 'Histórico de Turnos (Desde {$desde_text} Hasta {$hasta_text})',
      legend: { position: 'none' }
    };

    var chart = new google.visualization.LineChart(document.getElementById('curve_chart1'));

    chart.draw(data1, options1);
  }
</script>
<div id="curve_chart1" style="width: 100%; height: 280px"></div>
EOT;

    // graph 2
    $sql = "
        SELECT
            o.abreviacion,
            COUNT(t.id_turnos) AS total
        FROM
            turnos AS t
        INNER JOIN
            pacientes AS p
            ON t.id_pacientes = p.id_pacientes
        INNER JOIN
            obras_sociales AS o
            ON p.id_obras_sociales = o.id_obras_sociales
        WHERE
            t.id_medicos = '{$_SESSION['ID_MEDICO']}' AND
            t.fecha BETWEEN '{$desde}' AND '{$hasta}'
        GROUP BY
            p.id_obras_sociales
        ORDER BY
            total DESC
    ";
	$query = $this_db->consulta($sql);

    $html_graph.= <<<EOT
<script type="text/javascript">
  google.charts.load('current', {'packages':['corechart']});
  google.charts.setOnLoadCallback(drawChart2);

  function drawChart2() {
    var data2 = google.visualization.arrayToDataTable([
      ['Obra Social', 'Turnos', {role: 'style'}]
EOT;
	while ($row = $this_db->fetch_array($query)){
      $html_graph.= utf8_encode(",['{$row['abreviacion']}',  {$row['total']}, '#007FA6']\n");
    }
    $html_graph.= <<<EOT
    ]);

    var options2 = {
      title: 'Cantidad de Turnos por Obras Sociales (Desde {$desde_text} Hasta {$hasta_text})',
      legend: { position: 'none' }
    };

    var chart = new google.visualization.BarChart(document.getElementById('curve_chart2'));

    chart.draw(data2, options2);
  }
</script>
<div id="curve_chart2" style="width: 100%; height: 500px"></div>
EOT;

    // graph 3
    $sql = "
        SELECT
            e.nombre,
            COUNT(t.id_turnos) AS total
        FROM
            turnos AS t
        INNER JOIN
            turnos_estados AS e
            ON e.id_turnos_estados = t.id_turnos_estados
        WHERE
            t.id_medicos = '{$_SESSION['ID_MEDICO']}' AND
            t.fecha BETWEEN '{$desde}' AND '{$hasta}'
        GROUP BY
            t.id_turnos_estados
        ORDER BY
            total DESC
    ";
	$query = $this_db->consulta($sql);

    $html_graph.= <<<EOT
<script type="text/javascript">
  google.charts.load('current', {'packages':['corechart']});
  google.charts.setOnLoadCallback(drawChart3);

  function drawChart3() {
    var data3 = google.visualization.arrayToDataTable([
      ['Estado', 'Turnos', {role: 'style'}]
EOT;
	while ($row = $this_db->fetch_array($query)){
      $html_graph.= utf8_encode(",['{$row['nombre']}',  {$row['total']}, '#007FA6']\n");
    }
    $html_graph.= <<<EOT
    ]);

    var options3 = {
      title: 'Cantidad de Turnos por Estados de Turnos (Desde {$desde_text} Hasta {$hasta_text})',
      legend: { position: 'none' }
    };

    var chart = new google.visualization.BarChart(document.getElementById('curve_chart3'));

    chart.draw(data3, options3);
  }
</script>
<div id="curve_chart3" style="width: 100%; height: 500px"></div>
EOT;

    // graph 4
    $sql = "
        SELECT
            e.nombre,
            COUNT(t.id_turnos) AS total
        FROM
            turnos AS t
        INNER JOIN
            turnos_estudios AS r
            ON r.id_turnos = t.id_turnos
        INNER JOIN
            estudios AS e
            ON r.id_estudios = e.id_estudios
        WHERE
            t.id_medicos = '{$_SESSION['ID_MEDICO']}' AND
            t.fecha BETWEEN '{$desde}' AND '{$hasta}'
        GROUP BY
            r.id_estudios
        ORDER BY
            total DESC
    ";
	$query = $this_db->consulta($sql);

    $html_graph.= <<<EOT
<script type="text/javascript">
  google.charts.load('current', {'packages':['corechart']});
  google.charts.setOnLoadCallback(drawChart4);

  function drawChart4() {
    var data4 = google.visualization.arrayToDataTable([
      ['Estudio', 'Turnos', {role: 'style'}]
EOT;
	while ($row = $this_db->fetch_array($query)){
      $html_graph.= utf8_encode(",['{$row['nombre']}',  {$row['total']}, '#007FA6']\n");
    }
    $html_graph.= <<<EOT
    ]);

    var options4 = {
      title: 'Cantidad de Estudios realizados (Desde {$desde_text} Hasta {$hasta_text})',
      legend: { position: 'none' }
    };

    var chart = new google.visualization.BarChart(document.getElementById('curve_chart4'));

    chart.draw(data4, options4);
  }
</script>
<div id="curve_chart4" style="width: 100%; height: 500px"></div>
EOT;

    // graph 5
    $sql = "
        SELECT
            u.nombres,
            u.apellidos,
            COUNT(t.id_turnos) AS total
        FROM
            turnos AS t
        INNER JOIN usuarios AS u
            ON t.id_usuarios = u.id_usuarios
        WHERE
            t.id_medicos = '{$_SESSION['ID_MEDICO']}' AND
            t.fecha BETWEEN '{$desde}' AND '{$hasta}'
        GROUP BY
            t.id_usuarios
    ";
	$query = $this_db->consulta($sql);

    $html_graph.= <<<EOT
<script type="text/javascript">
  google.charts.load('current', {'packages':['corechart']});
  google.charts.setOnLoadCallback(drawChart5);

  function drawChart5() {
    var data5 = google.visualization.arrayToDataTable([
      ['Usuario', 'Turnos', {role: 'style'}]
EOT;
	while ($row = $this_db->fetch_array($query)){
      $html_graph.= utf8_encode(",['{$row['nombres']} {$row['apellidos']}',  {$row['total']}, '#007FA6']\n");
    }
    $html_graph.= <<<EOT
    ]);

    var options5 = {
      title: 'Turnos Otorgados por Usuarios (Desde {$desde_text} Hasta {$hasta_text})',
      legend: { position: 'none' }
    };

    var chart = new google.visualization.BarChart(document.getElementById('curve_chart5'));

    chart.draw(data5, options5);
  }
</script>
<div id="curve_chart5" style="width: 100%; height: 280px"></div>
EOT;

}

$htm_gral->Asigna("ESTADISTICAS_BUTTON", utf8_encode($html_button));
$htm_gral->Asigna("ESTADISTICAS_GRAPH", utf8_encode($html_graph));

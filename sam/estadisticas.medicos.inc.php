<?php
// graph 1
$sql = "
    SELECT
        DATE_FORMAT(t.fecha, '%d/%m') AS myfecha,
        COUNT(t.id_turnos) AS total
    FROM
        turnos AS t
    WHERE
        t.id_medicos = '{$ses_id_medico}' AND
        t.fecha BETWEEN '{$desde}' AND '{$hasta}'
    GROUP BY
        t.fecha
";
$query = $this_db->consulta($sql);
#$graph1count = $this_db->num_rows($query);

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
      title: '',
      legend: { position: 'none' },
      chartArea:{top:10, height:"100%"},
    };

    var chart = new google.visualization.LineChart(document.getElementById('curve_chart1'));

    chart.draw(data1, options1);
  }
</script>
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
        t.id_medicos = '{$ses_id_medico}' AND
        t.fecha BETWEEN '{$desde}' AND '{$hasta}'
    GROUP BY
        p.id_obras_sociales
    ORDER BY
        total DESC
";
$query = $this_db->consulta($sql);
$graph2count = 10 + ($this_db->num_rows($query) * 41);

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
      title: '',
      legend: { position: 'none' },
      chartArea:{top:10, height:"100%"},
    };

    var chart = new google.visualization.BarChart(document.getElementById('curve_chart2'));

    chart.draw(data2, options2);
  }
</script>
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
        t.id_medicos = '{$ses_id_medico}' AND
        t.fecha BETWEEN '{$desde}' AND '{$hasta}'
    GROUP BY
        t.id_turnos_estados
    ORDER BY
        total DESC
";
$query = $this_db->consulta($sql);
$graph3count = 10 + ($this_db->num_rows($query) * 41);

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
      title: '',
      legend: { position: 'none' },
      chartArea:{top:10, height:"100%"},
    };

    var chart = new google.visualization.BarChart(document.getElementById('curve_chart3'));

    chart.draw(data3, options3);
  }
</script>
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
        t.id_medicos = '{$ses_id_medico}' AND
        t.fecha BETWEEN '{$desde}' AND '{$hasta}'
    GROUP BY
        r.id_estudios
    ORDER BY
        total DESC
";
$query = $this_db->consulta($sql);
$graph4count = 10 + ($this_db->num_rows($query) * 41);

if ($this_db->num_rows($query) > 0) {
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
      title: '',
      legend: { position: 'none' },
      chartArea:{top:10, height:"100%"},
    };

    var chart = new google.visualization.BarChart(document.getElementById('curve_chart4'));

    chart.draw(data4, options4);
  }
</script>
EOT;
}

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
        t.id_medicos = '{$ses_id_medico}' AND
        t.fecha BETWEEN '{$desde}' AND '{$hasta}'
    GROUP BY
        t.id_usuarios
    ORDER BY
        COUNT(t.id_turnos) DESC
";
$query = $this_db->consulta($sql);
$graph5count = 10 + ($this_db->num_rows($query) * 41);

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
      title: '',
      legend: { position: 'none' },
      chartArea:{top:10, height:"100%"},
    };

    var chart = new google.visualization.BarChart(document.getElementById('curve_chart5'));

    chart.draw(data5, options5);
    /**************************************************************************/
    /**************************************************************************/
    /*CARGAR TABS CUANDO SE CARGUEN LOS GRÁFICOS*******************************/
    $("#tabs").tabs().attr(
        'style',
        'position:absolute;left:0.5%;margin:0 auto;width:99%;height:100%;visibility:show;'
    );
    /**************************************************************************/
    /**************************************************************************/
    /**************************************************************************/
  }
</script>
EOT;

$html_graph.= <<<HTML
<style>
#tabs .ui-state-active a,
#tabs .ui-state-active a:link,
#tabs .ui-state-active a:visited{
    color: #ffffff!important;
}
#tabs .ui-state-active,
#tabs .ui-widget-content .ui-state-active,
#tabs .ui-widget-header .ui-state-active{
    background:#008a47!important;
}
</style>
<div id="tabs" style="position:absolute;left:0;width:100%;height:100%;visibility:hidden;">
  <ul>
    <li><a href="#tabs-1">HISTÓRICO<br />DE<br />TURNOS</a></li>
    <li><a href="#tabs-2">TURNOS<br />POR<br />OBRAS SOCIALES</a></li>
    <li><a href="#tabs-3">TURNOS<br />POR<br />ESTADOS</a></li>
    <li><a href="#tabs-4">CANTIDAD<br />DE ESTUDIOS<br />REALIZADOS</a></li>
    <li><a href="#tabs-5">TURNOS<br />OTORGADOS<br />POR USUARIOS</a></li>
  </ul>
  <div id="tabs-1">
    <div id="curve_chart1" style="width: 100%; height: 400px"></div>
  </div>
  <div id="tabs-2">
    <div id="curve_chart2" style="width: 100%; height: {$graph2count}px"></div>
  </div>
  <div id="tabs-3">
    <div id="curve_chart3" style="width: 100%; height: {$graph3count}px"></div>
  </div>
  <div id="tabs-4">
    <div id="curve_chart4" style="width: 100%; height: {$graph4count}px"></div>
  </div>
  <div id="tabs-5">
    <div id="curve_chart5" style="width: 100%; height: {$graph5count}px"></div>
  </div>
</div>
HTML;

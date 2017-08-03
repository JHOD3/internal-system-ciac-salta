<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE HTML>
<html>
<head>
    <base href="<?=base_url()?>" />
	<meta http-equiv="content-type" content="text/html" />
    <meta charset="UTF-8" />
	<title>TURNOS ONLINE CIAC | Centro Integral de Alta Complejidad</title>
    <link rel="shortcut icon" type="image/x-icon" href="http://www.ciacsalta.com.ar/favicon.ico">
    <link rel="stylesheet" href="assets/css/ciac.css" />
    <link rel="stylesheet" href="assets/css/media.queries.ciac.css" />
    <script src="//code.jquery.com/jquery-1.12.0.min.js"></script>
</head>

<body>
	<div id="container">
		<h1><?php echo $heading; ?></h1>
		<?php echo $message; ?>
	</div>
</body>
</html>
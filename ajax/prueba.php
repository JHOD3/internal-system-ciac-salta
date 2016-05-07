<?php

$datos = $_POST["variables"];

parse_str(stripslashes($datos));

echo ($estudios[0])
?>
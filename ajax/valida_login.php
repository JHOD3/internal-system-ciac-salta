<?php
require_once("../engine/config.php");
requerir_class("estructura","mysql","querys","tpl");

if (
    !isset($_POST['captcha']) or
    !isset($_SESSION['tmptxt']) or
    $_SESSION['tmptxt'] != $_POST['captcha']
) {
    die('3'); // Captcha incorrecto
}

$usuario = $_POST["usr"];
//$password = base64_encode($_POST["pass"]);
$password = $_POST["pass"];
$sistema = $_POST["sistema"];

switch($sistema){
	case "sas":
		requerir_class("usuarios");
		$obj_usuario = new Usuarios();
		$rta = $obj_usuario->ValidaLogueo($usuario, $password);
	break;
	case "sam":
		requerir_class("medicos");
		$obj_medicos = new Medicos();
		$rta = $obj_medicos->ValidaLogueo($usuario, $password);
	break;
	case "sag":

	break;
}
echo ($rta);

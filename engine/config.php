<?php
session_start();
// Para evitar problemas con los acentos cuando pongo en mayuscula una palabra
setlocale(LC_ALL,"es_ES@euro","es_ES","esp");

header("Content-Type: text/html; charset=utf-8");
header("Cache-Control: no-store, no-cache, must-revalidate");

define("NOMBRE_SITIO", "ciac");
define("TITULO_SITIO", "Centro Integral de Alta Complejidad");
define("TITULO_PIE", "ReproiSa - Soluciones Informáticas");
define("ANIO", "2014");	

define("ID_APP_FACEBOOK", "");

if ((strpos($_SERVER['HTTP_HOST'], "localhost") !== FALSE) || (strpos($_SERVER['HTTP_HOST'], "192.168.0.253") !== FALSE) || (strpos($_SERVER['HTTP_HOST'], "7.90.193.32") !== FALSE)){
	/*define("BD_NOMBRE", NOMBRE_SITIO);
	define("SERVIDOR", "192.168.1.8");
	define("BD_USUARIO", "user");
	define("BD_PASS", "user");*/
	
	//define("BD_NOMBRE", NOMBRE_SITIO);
	define("BD_NOMBRE", "sigecon");
	define("SERVIDOR", "127.0.0.1");
	define("BD_USUARIO", "root");	
	define("BD_PASS", "");
	
	/*define("BD_NOMBRE", NOMBRE_SITIO);
	define("SERVIDOR", "localhost");
	define("BD_USUARIO", "root");
	define("BD_PASS", "");
	
	/*define("BD_NOMBRE", NOMBRE_SITIO);
	define("SERVIDOR", "192.168.0.102");
	define("BD_USUARIO", "root");
	define("BD_PASS", "");*/
	
	//GRANT ALL PRIVILEGES ON *.* TO root@[IP_o_NombreDelEquipo] IDENTIFIED BY [PasswordDelSqlUser]
	
	define("DIR_BASE", "/".NOMBRE_SITIO."/"); //Relativa
	define("ROOT", $_SERVER["DOCUMENT_ROOT"].DIR_BASE); //Absoluta
	define("URL", 'http://'.$_SERVER['HTTP_HOST'].'/'.NOMBRE_SITIO.'/');
	define("URL_ARC_IMG", 'http://'.$_SERVER['HTTP_HOST'].'/'.NOMBRE_SITIO.'/'.'files/archivos/img/');
}else{
	define("BD_NOMBRE", "");
	define("SERVIDOR", "localhost");
	define("BD_USUARIO", "");
	define("BD_PASS", "Diablo021085");
	define("DIR_BASE", "/"); //Relativa
	define("ROOT", $_SERVER["DOCUMENT_ROOT"].DIR_BASE); //Absoluta
	define("URL", 'http://'.$_SERVER['HTTP_HOST'].'/');
	define("URL_ARC_IMG", 'http://'.$_SERVER['HTTP_HOST'].DIR_BASE.'files/archivos/img/');
}

define("ARC_IMG", ROOT."files/archivos/img/");
define("ADMIN", "admin/");
define("CLASE", ROOT."class/");
define("HTM", ROOT."htm/");
define("HTMMOVIL", ROOT."movil/htm/");
define("LANG", DIR_BASE."lang/");
define("FILES", DIR_BASE."files/");
define("IMG", FILES."img/");
define("ARC", FILES."archivos/");
define("CSS", FILES."css/");
define("JS", FILES."js/");
define("SWF", FILES."swf/");
define("AUDIO", FILES."audio/");
define("VIDEOS", FILES."videos/");
define("AJAX", DIR_BASE."ajax/");


function requerir_class(){
	$args = func_get_args();
	foreach ($args as $arg){
		require_once(CLASE."class_".$arg.".php");	
	}	
}

function CargarVariablesGrales($htm_gral, $tipo = ""){
	$htm_gral->Asigna('URL', URL);
	$htm_gral->Asigna('TITULO_SITIO', TITULO_SITIO);
	$htm_gral->Asigna('TITULO_PIE', TITULO_PIE);
	$htm_gral->Asigna('ANIO', ANIO);
	$htm_gral->Asigna('VIDEOS', VIDEOS);
	$htm_gral->Asigna('IMG', IMG);
	$htm_gral->Asigna('CSS', CSS);
	$htm_gral->Asigna('JS', JS);
	$htm_gral->Asigna('ARC', ARC);
}

?>

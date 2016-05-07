<?php

//CONTROLO SI SE LOGUEO O NO, SINO REDIRECCIONO
header("Cache-control:private");
if (isset($_SESSION['USUARIO']))
{
	$fechaGuardada = $_SESSION["ULTIMO_ACCESO"]; 
	$ahora = date("Y-n-j H:i:s"); 
	$tiempo_transcurrido = (strtotime($ahora)-strtotime($fechaGuardada)); 

	/*//comparamos el tiempo transcurrido 
	if($tiempo_transcurrido >= 3600000) 
	{ 
		//si pasaron 10 minutos o más 
		session_destroy(); // destruyo la sesión 
		header("Location: login.php"); 
	}else 
		$_SESSION["ULTIMO_ACCESO"] = $ahora;*/
}
else
{
	session_destroy(); // destruyo la sesión 
	header("Location: login.php"); 
} 

?>
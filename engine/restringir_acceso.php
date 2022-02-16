<?php
//CONTROLO SI SE LOGUEO O NO, SINO REDIRECCIONO
header("Cache-control:private");
if (isset($_SESSION['USUARIO'])) {
	/*
    $fechaGuardada = $_SESSION["ULTIMO_ACCESO"];
	$ahora = date("Y-n-j H:i:s");
	$tiempo_transcurrido = (strtotime($ahora)-strtotime($fechaGuardada));
	/*comparamos el tiempo transcurrido
	if($tiempo_transcurrido >= 3600000)
	{
		//si pasaron 10 minutos o más
		session_destroy(); // destruyo la sesión
		header("Location: login.php");
	}else
		$_SESSION["ULTIMO_ACCESO"] = $ahora;
    */
    if(in_array($_SESSION['SUPERUSER'], [0,1])){
        echo    "<script type='text/javascript'>
                    var contadorAfk = 0;
                    $(document).ready(function () {
                        //Cada minuto se lanza la función ctrlTiempo
                        var contadorAfk = setInterval(ctrlTiempo, 60000); 
                    
                        //Si el usuario mueve el ratón cambiamos la variable a 0.
                        $(this).mousemove(function (e) {
                            contadorAfk = 0;
                        });
                        //Si el usuario presiona alguna tecla cambiamos la variable a 0.
                        $(this).keypress(function (e) {
                            contadorAfk = 0;
                        });
                    });
                    
                    function ctrlTiempo() {
                        //Se aumenta en 1 la variable.
                        contadorAfk++;
                        //Se comprueba si ha pasado del tiempo que designemos.
                        if (contadorAfk > 35) { // Más de 59 minutos, lo detectamos como ausente o inactivo.
                            location.href = 'login.php';
                        }
                    }
                </script> ";
    }
} else {
	session_destroy(); // destruyo la sesión
    if(
        !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
    ) {
        print "<script>top.location = 'login.php?err=1';</script>";
    } else {
    	header("Location: login.php?err=1");
    }
    die;
}

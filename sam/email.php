<?php
    ini_set( 'display_errors', 1 );
    error_reporting( E_ALL );
    $from = "Prueba@ciacsalta.com.ar";
    $to = "ogslash@hotmail.com";
    $subject = "Envio de correo con php";
    $message = "Php Email Prueba de envios ";
    $headers = "From:" . $from;
    mail($to,$subject,$message, $headers);
    echo "Mensaje enviado, satisfactoriamente";

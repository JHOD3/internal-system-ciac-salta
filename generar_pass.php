<?php
$contrasenia = "13835310"; //MARIA INES

$contrasenia_codificada = base64_encode($contrasenia);
$contrasenia_decodificada = base64_decode($contrasenia);

echo ("Contrese&ntilde;a Codificada en BASE64: ");
echo ($contrasenia_codificada);
echo ("<br/>");

echo ("Contrese&ntilde;a Decodificada en BASE64: ");
echo ($contrasenia_decodificada);
echo ("<br/>");

$contrasenia_codificada = md5($contrasenia);

echo ("Contrese&ntilde;a Codificada en MD5: ");
echo ($contrasenia_codificada);

$contrasenia_codificada = crc32($contrasenia);

echo ("Contrese&ntilde;a Codificada en crc32: ");
echo ($contrasenia_codificada);

?>
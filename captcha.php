<?php

// Notificar todos los errores de PHP
//error_reporting(-1);
// Lo mismo que error_reporting(E_ALL);
//ini_set('error_reporting', E_ALL);

// archivo: captcha.php
function randomText($length) {
  $pattern = "1234567890abcdefghijklmnopqrstuvwxyz";
  for($i=0;$i<$length;$i++) {
	@$key .= $pattern{rand(0,35)};
  }
  return $key;
}
session_start();
$_SESSION['tmptxt'] = randomText(8);
$captcha = imagecreatefromgif("files/img/bgcaptcha.gif");
$colText = imagecolorallocate($captcha, 0, 0, 0);

imagestring($captcha, 15, 16, 7, $_SESSION['tmptxt'], $colText);
header("Content-type: image/gif");
imagegif($captcha);

echo $captcha;

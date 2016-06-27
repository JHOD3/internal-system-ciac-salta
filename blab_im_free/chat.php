<?php

if(isset($_GET['u'])){$u=(int)$_GET['u'];}else{$u=0;} if($u<1){die();}

require_once 'config.php';
require_once 'incl/main.inc';

dbconnect(); session_start();
$settings=get_settings(); get_user(); $options=get_options(); $lang=get_language();

if(!isset($_SESSION['ID_USUARIO']) || !isset($_SESSION['bmf_name'])){die();}

if (strlen($_SESSION['NOMBRES']) > 1 and strlen($_SESSION['APELLIDOS']) > 1) {
    $_SESSION['bmf_name'] = utf8_encode($_SESSION['NOMBRES'].', '.$_SESSION['APELLIDOS']);
} else {
    $_SESSION['bmf_name'] = utf8_encode($_SESSION['USUARIO']);
}
if ($_SESSION['TIPO_USR'] == 'M') {
    $_SESSION['bmf_name'] = 'DOC. '.$_SESSION['bmf_name'];
} elseif ($_SESSION['TIPO_USR'] == 'U') {
    $_SESSION['bmf_name'] = 'Op. '.$_SESSION['bmf_name'];
}

$bim_id=(int)$_SESSION['ID_USUARIO'];

$file_list='';
include $skin_dir.'/'.$emo_file;

$sm_tag=array(); $sm_img=array();

for($i=0;$i<count($emoticons);$i++){
$csm=explode(' ',$emoticons[$i]);
if(isset($csm[1])){$sm_tag[]="'$csm[0]'";$sm_img[]="'$csm[1]'";}}

$sm_tag=implode(',',$sm_tag);
$sm_img=implode(',',$sm_img);

$title=htmrem($settings['html_title']);

include $skin_dir.'/templates/head.pxtm';
include $skin_dir.'/templates/chat.pxtm';

?>
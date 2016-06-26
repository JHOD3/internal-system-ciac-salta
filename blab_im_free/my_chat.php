<?php 

require_once 'config.php';

if(isset($_COOKIE['bmf_disable'])){
print '<input type="button" class="btn" style="font-weight:bold;background-color:#fff;color:#000;border-width:0px" value="&nbsp;'.$bim_disable.'&nbsp;" onclick="window.location=enable_url" />';
die();}

session_start();

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

require_once 'incl/main.inc';

dbconnect(); $settings=get_settings(); $options=get_options(); $lang=get_language();

$u = $_SESSION['bmf_id'];
$timestamp = time();

$query="
    SELECT *
    FROM `{$dbss['prfx']}_lines`
    WHERE
        `chatto` = '{$u}'
    ORDER BY `line_id` DESC
    LIMIT 10
";
$result=neutral_query($query);

$result=neutral_query($query);$tr=1;
if(neutral_num_rows($result)>0){
    $chats = '';
    while($row=neutral_fetch_array($result)){
        $chats =
            "<strong>".
            htmlspecialchars($row['usr_name']).
            ":</strong> ".
            htmlspecialchars($row['line_txt']).
            "<br />\n".
            $chats
        ;
    }
    print "<h2>ULTIMOS CHATS</h2>";
    print $chats;
}
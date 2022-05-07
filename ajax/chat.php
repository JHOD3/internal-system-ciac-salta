<?php

require_once("../engine/config.php");
require_once("../engine/restringir_acceso.php");
requerir_class("tpl","querys","mysql","estructura","json","usuarios","medicos");

$input = json_decode(file_get_contents("php://input"), true);
$query = $input["query"];
switch ($query){
    case 'user_on':
            /*$usuario = new Usuarios();
            $rts = $usuario->cargar_usuarios_activos();*/
            $medicos = new Medicos();
            $rtm = $medicos->cargar_medicos_activos();
            $rtm = (empty($rtm))?false:$rtm;
        break;

    case 'user_load_chat_medico':
        $medicos = new Medicos();
        $rtm = $medicos->cargar_usuarios_chat_de_usuario($input['id_medico'], $input['id_usuario']);
        break;
    case 'user_send_mensage':
        $medicos = new Medicos();
        $rtm = $medicos->send_mensaje($input['id_medico'], $input['id_usuario'], $input['mensaje'],'usuario');
        break;
    case 'medico_on':
        $usuarios = new Usuarios();
        $rtm = $usuarios->cargar_usuarios_activos();
        $rtm = (empty($rtm))?false:$rtm;
        break;
    case 'user_load_chat_usuario':
        $usuarios = new Usuarios();
        $rtm = $usuarios->cargar_usuarios_chat_de_usuario($input['id_medico'], $input['id_usuario']);
        break;
    case 'medico_send_mensage':
        $medicos = new Medicos();
        $rtm = $medicos->send_mensaje($input['id_medico'], $input['id_usuario'], $input['mensaje'], 'medico');
        break;
}
/*echo "<pre>";
print_r($rtm);
echo "</pre>";*/
echo json_encode($rtm);
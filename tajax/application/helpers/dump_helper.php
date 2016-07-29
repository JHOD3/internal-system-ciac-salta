<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
if ( ! function_exists('dumpLindo'))
{
    function dumpLindo($var) {
        ob_start();
            print '<pre>';
            print_r( $var);
            print '</pre>';
        $html = ob_get_contents();
        return $html;
    }
}

if ( ! function_exists('dumpMuyLindo'))
{

    function dumpMuyLindo($var, $height = 350) {
        ob_start();
            print '<div style="height:'.$height.'px;width:100%;overflow:auto;border:solid 1px #C00;margin:4px;background: white;color:#C00;font-size:11px;font-family: Arial"><pre>';
            print_r($var);
            print '</pre></div>';
        $html = ob_get_contents();
        return $html;
    }
}

if ( ! function_exists('dumpSuperLindo'))
{

    function dumpSuperLindo($var) {
        $CI = get_instance();
        if (count($var) > 0) {
            $CI->table->set_heading(array_keys($var[0]));
        }
        $tmpl = array (
            'table_open' => '<table border="1" cellpadding="4" cellspacing="0">'
        );
        $CI->table->set_template($tmpl); 
        print $CI->table->generate($var);
    }
}

if ( ! function_exists('horaMM'))
{

    function horaMM($hora, $masm) {
        $aHora = explode(":", $hora);
        $aMasM = explode(":", $masm);
        $aSuma = array(
            $aHora[0] + $aMasM[0] + floor(($aHora[1] + $aMasM[1]) / 60),
            ($aHora[1] + $aMasM[1]) % 60,
            '00'
        );
        if ($aSuma[0] < 10) $aSuma[0] = '0'.$aSuma[0];
        if ($aSuma[1] < 10) $aSuma[1] = '0'.$aSuma[1];
        return implode(":", $aSuma);
    }
}

if ( ! function_exists('upper'))
{
    function upper($str)
    {
        $arrAcentos = array('á', 'é', 'í', 'ó', 'ú', 'ñ', 'ü');
        $arrReemplz = array('Á', 'É', 'Í', 'Ó', 'Ú', 'Ñ', 'Ü');
        $str = str_replace($arrAcentos, $arrReemplz, $str);
        return strtoupper($str);
    }
}

if ( ! function_exists('lower'))
{
    function lower($str)
    {
        $arrAcentos = array('Á', 'É', 'Í', 'Ó', 'Ú', 'Ñ', 'Ü');
        $arrReemplz = array('á', 'é', 'í', 'ó', 'ú', 'ñ', 'ü');
        $str = str_replace($arrAcentos, $arrReemplz, $str);
        return strtolower($str);
    }
}

//EOF dump_helper.php

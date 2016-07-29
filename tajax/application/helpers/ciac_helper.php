<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('doSaludo'))
{
    function doSaludo($rsMedico, $prefix = true)
    {
        $str = "";
        if ($prefix) {
            switch (lower($rsMedico['saludo'])) {
                case "dr.":
                    $str.= "el ";
                    break;
                case "dra.":
                    $str.= "la ";
                    break;
            }
        }
        $str.= ucwords(lower(trim($rsMedico['saludo'])));
        $str.= " ";
        $str.= upper(trim($rsMedico['apellidos']));
        $str.= ", ";
        $str.= ucwords(lower(trim($rsMedico['nombres'])));
        return $str;
    }
}

//EOF ciac_helper.php
<?php
/**
 * GetDateTimeFromStr
 *
 * Devuelve un objeto DateTime a partir de una fecha en formato válido. En caso de no poder crearlo devuelve null o una referencia no valida al objeto.
 *
 * @access	public
 * @param	string	cadena que representa una fecha en formato valido.
 * @return	DateTime
 */
function GetDateTimeFromStr($dateTime) {
	if(empty($dateTime)) {
		return null;
	}
	try
	{
		$oDt = @new DateTime($dateTime);
	}
	catch(Exception $e)
	{
		$oDt = @date_create($dateTime);
	}
	
	return $oDt;
}
/**
 * GetDateFromISO
 *
 * Devuelve una cadena de caracteres que representa una fecha en formato french.
 *
 * @access	public
 * @param	string	cadena que representa una fecha en formato valido.
 * @param	boolean	indica si se debe devolver el string 'NULL' en caso de estar vacio.
 * @return	string
 */
function GetDateFromISO($date,$bRetNULL=true)
{
	$sRet = GetFormatDate('d/m/Y', $date);
	if($date === null && $bRetNULL == TRUE){
		return 'NULL';
	}
	return $sRet;
}

/**
 * GetDateTimeFromISO
 *
 * Devuelve una cadena de caracteres que representa una fecha hora en formato french con formato de hora 24.
 *
 * @access	public
 * @param	string	cadena que representa una fecha en formato valido.
 * @param	boolean	indica si se debe devolver el string 'NULL' en caso de estar vacio.
 * @return	string
 */
function GetDateTimeFromISO($dateTime,$bRetNULL=false)
{
	$sRet = GetFormatDate('d/m/Y H:i:s', $dateTime);
	if($dateTime === null && $bRetNULL == TRUE){
		return 'NULL';
	}
	return $sRet;
}

/**
 * GetDateFromFrenchToISO
 *
 * Convierte una cadena de caracteres que representa una fecha en formato french a ISO.
 *
 * @access	public
 * @param	string	cadena que representa una fecha en formato valido.
 * @param	boolean	indica si se debe devolver el string 'NULL' en caso de estar vacio.
 * @return	string
 */
function GetDateFromFrenchToISO($date,$bRetNULL=true)
{    
	$ret='NULL';
	if(!empty($date)) {
	    $f = explode(' ',$date);
	   
		if ( preg_match( "/([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})/", $date, $regs ) ) 
		{
	    	$ret = $regs[3].'-'.$regs[2].'-'.$regs[1];
	    	if($ret=='0000-00-00') {
	    		$ret='NULL';
	    	}
		}
		if(!$bRetNULL && $ret=='NULL') {
			$ret = null;
		}
	}
	return $ret;
}
/**
 * GetDateFromFrenchToISO
 *
 * Convierte una cadena de caracteres que representa una fecha y hora en formato french a ISO.
 *
 * @access	public
 * @param	string	cadena que representa una fecha y hora en formato valido.
 * @param	boolean	indica si se debe devolver el string 'NULL' en caso de estar vacio.
 * @return	string
 */
function GetDateTimeFromFrenchToISO($date, $bRetNULL = true)
{	
	$ret='NULL';
	if(!empty($date)) {
	    $f = explode(' ',$date);
		
		$hora = '';
		if(sizeof($f) > 1) {
			$hora = ' '.$f[1];
		}
	    
		if ( preg_match( "/([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})/", $date, $regs ) ) 
		{
	    	$ret = $regs[3].'-'.$regs[2].'-'.$regs[1];
	    	if($ret=='0000-00-00') {
	    		$ret='NULL';
	    	} else {
	    		$ret.= $hora;
	    	}
		}
		if(!$bRetNULL && $ret=='NULL') {
			$ret = null;
		}
	}
	return $ret;
}
/**
 * GetMonthName
 *
 * Devuelve el nombre del mes a partir una cadena de caracteres que representa una fecha en formato valildo.
 *
 * @access	public
 * @param	boolean Indica si debe abreviar el nombre con tres caracteres.
 * @param	string	cadena que representa una fecha y hora en formato valido.
 * @return	string
 */
function GetMonthName($bAbrev,$val)
{
	$sRet = '';
	$CI =& get_instance();
	$CI->lang->load('date.ext');
	$aMonthNames = $CI->lang->line('date_months_names');
	if(array_key_exists($val, $aMonthNames)) {
		$sRet = $aMonthNames[$val];
	} else {
		$sRet = $aMonthNames[($val % 12) + 1];
	}
	if($bAbrev)
		$sRet = substr($sRet,0,3).'.';
	return $sRet;
}
/**
 * GetDateString
 *
 * Devuelve la fecha legible a partir de una cadena de caracteres que representa una fecha en formato valildo.
 *
 * @access	public
 * @param	string	cadena que representa una fecha y hora en formato valido.
 * @return	string
 */
function GetDateString($date) {
	
	if(empty($date)) {
		return null;
	}
	

	$dateTime = GetDateTimeFromStr($date);
	die();
	if (empty($dateTime)) {
		return null;
	}
	
	$sRet = '';
	$CI =& get_instance();
	$CI->lang->load('date.ext');
	$str =explode('-', $dateTime->format('w-d-n-Y'));
	
	$aMonthNames = $CI->lang->line('date_months_names');
	$aDayNames = $CI->lang->line('date_days_names');
	
	$day = array_shift($str);
	
	$str[0] = $aDayNames[$str[0]%7];
	
	$str[1] = $aMonthNames[($str[2] % 12) + 1];
	
	return  $str[0].' '. $day . $CI->lang->line('date_date_string_connector') . $str[1] . $CI->lang->line('date_date_string_connector') . $str[2];
}
/**
 * GetToday
 *
 * Devuelve la fecha actual en un formato dado.
 *
 * @access	public
 * @param	string	opcional, formato de la cadena de salida.
 * @return	string
 */
function GetToday($format='')
{
	try
	{
		$oDt = @new DateTime();	
	}
	catch(Exception $e)
	{
		$oDt = @date_create();
	}
	$CI =& get_instance();
	$CI->lang->load('date.ext');
	return $oDt->format((!empty($format))? $format: $CI->lang->line('date_datetime_format'));
}
/**
 * GetFormatDate
 *
 * Devuelve la fecha en un formato dado. En caso de no estecificar un valor o de que el objeto DateTime no pueda interpretar la cadena de entrada devolverá null.
 *
 * @access	public
 * @param	string	formato de la cadena de salida.
 * @return	string
 */
function GetFormatDate($sFormat, $date)
{
	if(empty($date)) {
		return null;
	}
	$oDt = GetDateTimeFromStr($date);
	return (!empty($oDt))? $oDt->format($sFormat): null;
}
/**
 * getStringResolved
 *
 * Convierte una fecha y hora a un formato legible. Al estilo face, twit o gplus por ejemplo.
 *
 * @access	public
 * @param	string	cadena de entrada, una fecha valida para el objeto DateTime.
 * @param	string	opcional, cadena que representa una fecha y hora valida para usar de referencia.
 * @return  string
 */
function getStringResolved($date, $compareTo = NULL)
{
    if(!is_null($compareTo)) {
    	try {
    		$compareTo = new DateTime($compareTo);
    	} catch(Exception $e) {
    		$compareTo = @date_create($compareTo);
    	}
    }
    try {
		$date = new DateTime($date);
	} catch(Exception $e) {
		$date = @date_create($date);
	}
    return getString($date, $compareTo);
}
/**
 * getString
 *
 * Ayudante de la funcion getStringResolved para abstraer los tipos de datos.
 *
 * @access	public
 * @param	DateTime	una fecha.
 * @param	DateTime	opcional, fecha y hora valida para usar de referencia.
 * @param	boolean		opvional, true para agregar la hora a la salida.
 * @param	boolean		opvional, true para limitar la descripcion a un solo dia, luego seran fachas normales.
 * @return  string
 */
function getString(DateTime $date, DateTime $compareTo = NULL, $viewTime = TRUE, $oneDayDiffLimit = TRUE)
{
    if(is_null($compareTo)) {
    	try {
    		$compareTo = new DateTime('now');
    	} catch(Exception $e) {
    		$compareTo = @date_create('now');
    	}
    }
    $diff = $compareTo->format('U') - $date->format('U');
    $dayDiff = floor($diff / 86400);

    if(is_nan($dayDiff) || $dayDiff < 0) {
        return '';
    }
	$CI =& get_instance();
	$CI->lang->load('date.ext');
    $msj = '';
    if($dayDiff == 0) {
        if($diff < 60) {
            $msj = $CI->lang->line('date_just_now');
        } elseif($diff < 120) {
            $msj = $CI->lang->line('date_1_minute_ago');
        } elseif($diff < 3600) {
            $msj = floor($diff/60) .' '. $CI->lang->line('date_minutes_ago');
        } elseif($diff < 7200) {
            $msj = $CI->lang->line('date_1_hour_ago');
        } elseif($diff < 86400) {
            $msj = floor($diff/3600) .' '. $CI->lang->line('date_hours_ago');
        }
    } elseif($dayDiff == 1) {
        $msj = $CI->lang->line('date_yesterday');
    } elseif($dayDiff < 7 && !$oneDayDiffLimit) {
        $msj = $dayDiff .' '.$CI->lang->line('date_days_ago');
    } elseif($dayDiff == 7 && !$oneDayDiffLimit) {
        $msj = $CI->lang->line('date_1_week_ago');
    } elseif($dayDiff < (7*6) && !$oneDayDiffLimit) { // Modifications Start Here
        // 6 weeks at most
        $msj = ceil($dayDiff/7) .' '. $CI->lang->line('date_weeks_ago');
    } elseif($dayDiff < 365 && !$oneDayDiffLimit) {
        $msj = ceil($dayDiff/(365/12)) .' '.$CI->lang->line('date_months_ago');
    } elseif(!$oneDayDiffLimit) {
        $years = round($dayDiff/365);
        $msj = $years .' '. $CI->lang->line(('date_year' . ($years != 1 ? 's' : '') . '_ago'));
    } else {
    	$msj = $date->format($CI->lang->line('date_date_format'));
    }
	
	return $msj . (($viewTime==TRUE && $dayDiff > 0)? ' a la(s) ' . $date->format($CI->lang->line('date_time_format')): '');
}

function dateFrenchIsValid($frenchDate) {
	$regexp = '/^(((0[1-9]|[12]\d|3[01])\/(0[13578]|1[02])\/((1[6-9]|[2-9]\d)\d{2}))|((0[1-9]|[12]\d|30)\/(0[13456789]|1[012])\/((1[6-9]|[2-9]\d)\d{2}))|((0[1-9]|1\d|2[0-8])\/02\/((1[6-9]|[2-9]\d)\d{2}))|(29\/02\/((1[6-9]|[2-9]\d)(0[48]|[2468][048]|[13579][26])|((16|[2468][048]|[3579][26])00))))$/';
	return (bool)preg_match($regexp, $frenchDate);
}
// EOF dateext_helper.php
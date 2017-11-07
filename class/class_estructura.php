<?php
class Estructura{

	protected $_data = array();

	function __construct($id = ""){
		$this->querys = new Querys();
		$this->db = new MySQL();
		$this->vector = array();
		$this->registro = array();
		$this->meses = array("enero","febrero","marzo","abril","mayo","junio","julio","agosto","septiembre","octubre","noviembre","diciembre");
		$this->Meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
		$this->mes = array("ene","feb","mar","abr","may","jun","jul","ago","sep","oct","nov","dic");
		$this->Mes = array("Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic");
		if ($id != ""){
			$this->_data["id"] = $id;

			$registro = $this->Registro($id);

			//CREO EL ARRAY CON TODA LA INFO
			$this->vector = $registro;
			$this->registro = $registro;

			$cant = count($registro);

			if ($cant > 1){
				//CREO EL ARRAY QUE ME VA A PERMITIR CREAR LOS ATRIBUTOS CON SET Y GET
				foreach ($registro as $indice => $valor){
					$this->_data[$indice] = $valor;
				}
			}
		}
    }

	public function __set($key, $val){
		$this->_data[$key] = $val;
    }

    public function __get($key){
		$rta = "";
		if (array_key_exists($key, $this->_data)){
			if ($this->_data[$key] != NULL){
				if ($this->_data[$key] != "" || $this->_data[$key] != NULL)
					$rta = $this->_data[$key];
			}
		}else{
			$rta = "Sin Definir";
		}
        return $rta;
    }

	public function html($nombre_htm){
		$this->contenido = new SpynTPL(HTM);
		$this->contenido->Fichero($nombre_htm.".htm");
		return ($this->contenido);
	}

	function Registro($id){
		$query_string = $this->querys->Registro($this->nombre_tabla,$id);
		$query = $this->db->consulta($query_string);
		$registro = $this->db->fetch_array($query);
		return $registro;
	}

	function RegistroXAtributo($atributo,$valor, $tipo = ""){
		$query_string = $this->querys->RegistroXAtributo($this->nombre_tabla, $atributo, $valor, $tipo);
		//error_log(print_r("DATOS: " .$query_string,1));
		$query = $this->db->consulta($query_string);
		//$registro = $this->db->fetch_array($query);
		return $query;
	}

	////////////////////////////////////////////////////
    //Convierte fecha de mysql a normal
    ////////////////////////////////////////////////////
    function cambiaf_a_normal($fecha, $separador){
		if ($fecha != NULL || $fecha != ""){
			//ereg( "([0-9]{2,4})-([0-9]{1,2})-([0-9]{1,2})", $fecha, $mifecha);
			$mifecha = explode("-", $fecha);
			$lafecha=$mifecha[2].$separador.$mifecha[1].$separador.$mifecha[0];
			if ($lafecha == "00/00/0000"){
				$lafecha = "Sin Definir";
			}else{

			}


		}else{
			$lafecha = "Sin Definir";
		}
        return $lafecha;
    }

    ////////////////////////////////////////////////////
    //Convierte fecha de normal a mysql
    ////////////////////////////////////////////////////
    function cambiaf_a_mysql($fecha){
		$separador = "-";
		$mifecha = explode("/", $fecha);
       	$lafecha=$mifecha[2].$separador.$mifecha[1].$separador.$mifecha[0];
        return $lafecha;
    }

	function Drop($ordenar = "",$id = "",$id_padre = "", $nombre = "", $orden = "", $multiple = "", $size = ""){

		switch($this->nombre_tabla){
			case "medicos_especialidades":
				$htm = "drop_uno";
			break;
			default:
				$htm = "drop";
		}

		$drop = $this->html($htm);

		$drop->Asigna("LABEL_ELIJA", $this->drop_label_elija);
		if ($nombre == "")
			$drop->Asigna("NOMBRE_SELECT", $this->nombre_tabla);
		else
			$drop->Asigna("NOMBRE_SELECT", $nombre);


        switch ($this->nombre_tabla){
			case "medicos_especialidades":
				$query_string = $this->querys->DropMedicosEspecialidades($id_padre);
			break;
			case "especialidades":
				$query_string = $this->querys->Registros($this->nombre_tabla, "estado", "1", "nombre", "", "", "ASC");
			break;
            case "dias_semana":
				$query_string = $this->querys->Registros($this->nombre_tabla, "estado", "1", "id_dias_semana", "", "", "ASC");
            break;
            case "obras_sociales":
				$query_string = $this->querys->Registros($this->nombre_tabla, "estado", "1", "abreviacion", "", "", "ASC");
            break;
			default:
				if($id_padre != "")
					$query_string = $this->querys->Registros($this->nombre_tabla, "id_".$this->tabla_padre, $id_padre, $ordenar, "","",$orden);
				else
					$query_string = $this->querys->TodosRegistros($this->nombre_tabla,$orden);
		}

        $query = $this->db->consulta($query_string);
		$cant_registros = $this->db->num_rows($query);
		if ($multiple != ""){
			$drop->Asigna("MULTIPLE",'multiple="multiple"');
		}else{
			$drop->Asigna("MULTIPLE",'');
		}
		if ($size > 1){
			$drop->Asigna("SIZE",'size="'.$size.'"');
		}else{
			$drop->Asigna("SIZE",'');
		}
		if ($cant_registros != 0){
			while ($row = $this->db->fetch_array($query)){
                $parent_id = '';

				if ($id == $row["id_".$this->nombre_tabla]){
					$row["SELECTED"] = "selected='selected'";
				}else{
					$row["SELECTED"] = "";
				}

				$texto = $row["nombre"];

                switch($this->nombre_tabla){
                    case "subsectores":
                        $parent_id = ' data-pid="'.$row['id_sectores'].'"';
						$valor = $row["id_".$this->nombre_tabla];
                    break;
                    case "medicos":
						$valor = $row["id_".$this->nombre_tabla];
						$texto = $row["saludo"]." ".$row["apellidos"].", ".$row["nombres"];
                    break;
					case "medicos_especialidades":
						$valor = $row["id_especialidades"];
						$texto = utf8_encode($row["nombre"]);
                        $texto = str_replace("á", "&aacute;", $texto);
                        $texto = str_replace("Á", "&Aacute;", $texto);
                        $texto = str_replace("é", "&eacute;", $texto);
                        $texto = str_replace("É", "&Eacute;", $texto);
                        $texto = str_replace("í", "&iacute;", $texto);
                        $texto = str_replace("Í", "&Iacute;", $texto);
                        $texto = str_replace("ó", "&oacute;", $texto);
                        $texto = str_replace("Ó", "&Oacute;", $texto);
                        $texto = str_replace("ú", "&uacute;", $texto);
                        $texto = str_replace("Ú", "&Uacute;", $texto);
                        $texto = str_replace("ñ", "&ntilde;", $texto);
                        $texto = str_replace("Ñ", "&Ntilde;", $texto);
					break;
					case "localidades_zonas":
						$valor = $row["id_localidades"];
					break;
					case "obras_sociales":
						$texto = $row["abreviacion"];
						$valor = $row["id_".$this->nombre_tabla];
					break;
					default:
						$valor = $row["id_".$this->nombre_tabla];
				}
				$row["VALUE"] = $valor;
				$row["TEXTO_OPTION"] = $texto;
                $row["PARENT_ID"] = $parent_id;
                #var_dump($row);
				$drop->AsignaBloque('block_option',$row);
			}
		}else{
			$drop->AsignaBloque('block_option',"");
		}
		return $drop->Muestra();
	}

	function DropArmado($nombre, $datos, $seleccionado = -99, $multiple = ""){
		$drop = $this->html("drop");
		$drop->Asigna("LABEL_ELIJA", $datos["label_elija"]);
		$drop->Asigna("NOMBRE_SELECT", $nombre);

		if ($multiple != ""){
			$drop->Asigna("MULTIPLE",'multiple="multiple"');
		}else{
			$drop->Asigna("MULTIPLE",'');
		}

		unset($datos["label_elija"]);

		foreach ($datos as $i => $value){
			if ($i == $seleccionado){
				$row["SELECTED"] = "selected='selected'";
			}else{
				$row["SELECTED"] = "";
			}
			if (is_array($value)){
				$row["TEXTO_OPTION"] = $value[0];
			}else{
				$row["TEXTO_OPTION"] = $value;
			}
			switch ($nombre){
				case "hora_inicio":
				case "hora_fin":
				case "combustibles":
				case "tipos_operaciones":
				case "desde":
				case "hasta":
					$row["VALUE"] = $value;
				break;
				default:
					$row["VALUE"] = $i;
			}

			$drop->AsignaBloque('block_option', $row);
		}
		return $drop->Muestra();
	}

	function DropSiNo($nombre, $seleccionado = ""){
		$drop = $this->html("drop");

		$drop->Asigna("MULTIPLE",'');

		$drop->Asigna("LABEL_ELIJA", "Elija una Opci&oacute;n");
		$drop->Asigna("NOMBRE_SELECT", $nombre);

		$row["VALUE"] = "Si";
		$row["TEXTO_OPTION"] = "Si";

		if ($seleccionado == "Si"){
			$row["SELECTED"] = "selected='selected'";
		}else{
			$row["SELECTED"] = "";
		};
		$drop->AsignaBloque('block_option',$row);

		$row["VALUE"] = "No";
		$row["TEXTO_OPTION"] = "No";

		if ($seleccionado == "No"){
			$row["SELECTED"] = "selected='selected'";
		}else{
			$row["SELECTED"] = "";
		};

		$drop->AsignaBloque('block_option',$row);

		return $drop->Muestra();
	}

	function DropVacio($nombre = ""){
		$drop = $this->html("drop");
		$drop->Asigna("LABEL_ELIJA", $this->drop_label_elija);
		if ($nombre == ""){
			$drop->Asigna("NOMBRE_SELECT", $this->nombre_tabla);
		}else{
			$drop->Asigna("NOMBRE_SELECT", $nombre);
		}

		$drop->Asigna("MULTIPLE",'');
		$row = "";
		$drop->AsignaBloque('block_option', $row);
		return $drop->Muestra();
	}


	function RecortarTexto($texto, $nro_caracteres){
		if(strlen($texto)>=$nro_caracteres)
			$texto = substr($texto, 0, $nro_caracteres)."...";
		return $texto;
	}

	function crear_check($seccion, $id_registro){
		switch ($seccion){
			case "noticias":
				$query_string = $this->querys->solo_registro($seccion,$id_registro);
				$nombre_control = "ckbx_mostrar";
				$texto_control = "Mostrar en Principal";
			break;
		}

		$query = $this->db->consulta($query_string);

		while ($row = $this->db->fetch_array($query)){
			if ($row[5] == "on")
				$check = "<input type='checkbox' name='".$nombre_control."' id='".$nombre_control."' checked='checked'/>".$texto_control;
			else
				$check = "<input type='checkbox' name='".$nombre_control."' id='".$nombre_control."'/>".$texto_control;
		}
		return utf8_encode($check);
	}

	function prox_id_insertar($nombre_bd, $tabla){
		$query_string = "SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA = '".$nombre_bd."' AND TABLE_NAME = '".$tabla."'";
		$query = $this->db->consulta($query_string);
		$row = $this->db->fetch_array($query);
		return $row[0];
	}

	// semilla de microsegundos
	function make_seed(){
		list($usec, $sec) = explode(' ', microtime());
		return (float) $sec + ((float) $usec * 100000);
	}

	function thumbjpeg($imagen,$anchura,$altura,$id,$tamano = "") {
		$dir_thumb = $tamano."/";
		$prefijo_thumb = "";

		$dir_contenido = $id."/";
		$camino_nombre=explode("/",$imagen);
		// Aquí tendremos el nombre de la imagen.
		$nombre=end($camino_nombre);
		// Aquí la ruta especificada para buscar la imagen.
		// SI NO SE DEFINE EL ID ES PORQUE VIENE DE CANCIONES SINO ES CONTENIDOS
		if ($id != ""){
			$camino=substr($imagen,0,strlen($imagen)-strlen("original/".$id."/".$nombre));
		}else{
			$camino=substr($imagen,0,strlen($imagen)-strlen("original/".$nombre));
		}

		// Intentamos crear el directorio de thumbnails, si no existiera previamente.

		//error_log("CARPETA: ".$imagen);
		if (!file_exists($camino.$dir_thumb)){
			mkdir ($camino.$dir_thumb, 0777);

			// or die("No se ha podido crear el directorio $dir_thumb");
			//umask(0);
		}
		if (!file_exists($camino.$dir_thumb.$dir_contenido)){
			mkdir ($camino.$dir_thumb.$dir_contenido, 0777);
		}
		$ext =  strtolower(substr(strrchr($imagen, '.'), 1)); //extension de la imagens
		switch($ext) {
			case 'jpg':
				$img = imagecreatefromjpeg($imagen);
				break;
			case 'png':
				$img = imagecreatefrompng($imagen);
				break;
			case 'gif':
				$img = imagecreatefromgif($imagen);
				break;
			default:
				echo "error";
		}
		//$original = imagecreatefromstring($imagen);
		//$img = imagecreatefromjpeg($imagen); // or die("No se encuentra la imagen $camino$nombre<br>n");
		if (($tamano == "560_388")||($tamano == "720_540")){
			// miramos el tamaño de la imagen original...
			$datos = getimagesize($imagen); // or die("Problemas con $camino$nombre<br>");
			// intentamos escalar la imagen original a la medida que nos interesa
			//error_log ("Camino: ".$imagen);
			if ($datos[0] > $datos[1]){
				$ratio = ($datos[0] / $anchura);
				$altura = round($datos[1] / $ratio);
			}else{
				$ratio = ($datos[1] / $altura);
				$anchura = round($datos[0] / $ratio);
			}
			// esta será la nueva imagen reescalada
			$thumb = imagecreatetruecolor($anchura,$altura);
			imagecopyresampled ($thumb, $img, 0, 0, 0, 0, $anchura, $altura, $datos[0], $datos[1]);
		}else{
			//Ahora uso la función antes definida, con unos parámetros de ancho y alto que yo quiera
			$thumb = $this->resizeFit($img, $anchura, $altura);
		}
		// con esta función la reescalamos


		if (($ext == "gif") || ($ext == "png")){



			$colorTransparancia=imagecolortransparent($img);
			//if($colorTransparancia!=-1){ //TIENE TRANSPARENCIA

				$colorTransparente = imagecolorsforindex($img, $colorTransparancia);
				imagealphablending($img,true);
				imagealphablending($thumb,false);
				imagesavealpha($thumb,true);
				$idColorTransparente = imagecolorallocatealpha($thumb, 255,255,255,0);
 				imagecolortransparent($thumb, $idColorTransparente);
				imagefill($thumb,0,0,$idColorTransparente);

			//}

			// Dibujar el fondo transparente
			//imagefill($thumb, 0, 0, $negro);
			// Dibujar un rectángulo rojo
			//imagefilledrectangle($im, 4, 4, 50, 25, $rojo);
			// Guardar la imagen

			//imagepng($thumb);
			//imagedestroy($thumb);

		}else{

		}
		switch($ext) {
			case 'jpg':
				imagejpeg($thumb,$camino.$dir_thumb.$dir_contenido.$nombre, 100);
				break;
			case 'png':
				imagepng($thumb,$camino.$dir_thumb.$dir_contenido.$nombre, 9);
				break;
			case 'gif':
				imagegif($thumb,$camino.$dir_thumb.$dir_contenido.$nombre);
				break;
			default:
				echo "error";
		}
		imagedestroy($thumb);
		imagedestroy($img);
		//}
		return "ok";
	}

	function resizeFit($im,$width,$height) {
		//Original sizes
		$ow = imagesx($im); $oh = imagesy($im);
		//To fit the image in the new box by cropping data from the image, i have to check the biggest prop. in height and width
		if($width/$ow > $height/$oh) {
			$nw = $width;
			$nh = ($oh * $nw) / $ow;
			$px = 0;
			$py = ($height - $nh) / 2;
		}else{
			$nh = $height;
			$nw = ($ow * $nh) / $oh;
			$py = 0;
			$px = ($width - $nw) / 2;
		}
		//Create a new image width requested size
		$new = imagecreatetruecolor($width,$height);
		//Copy the image loosing the least space
		imagecopyresampled($new, $im, $px, $py, 0, 0, $nw, $nh, $ow, $oh);
		return $new;
	}
	function ListadoArchivos($origen){
		$htm_galeria = $this->Html("a_listado_archivos");
		switch ($origen){
			case "admin":
				$dir = "";
			break;
			case "ajax":
				$dir = "../";
			break;
		}
		$nombre_carpeta = $dir."files/archivos/".$this->nombre_tabla."/".$this->id;
		if (is_dir($nombre_carpeta)){
			$archivos = scandir($nombre_carpeta);
			$cantidad_img = count($archivos) - 2; //No cuento . y ..
			if ($cantidad_img != 0) {
				unset ($archivos[0]); // borro .
				unset ($archivos[1]); // borro ..

				natsort($archivos);
				$archivos = array_reverse($archivos);

				$nro_imagenv = split('[.]', $archivos[0]);

				foreach ($archivos as $value){
					$row["ID_CONTENIDOS"] = $this->id;
					$ext =  strtolower(substr(strrchr($value, '.'), 1));
					switch ($ext){
						case "ppt":
						case "pptx":
							$img = URL."files/img/archivo_ppt.jpg";
						break;
						case "doc":
						case "docx":
							$img = URL."files/img/archivo_doc.jpg";
						break;
						case "xls":
						case "xlsx":
							$img = URL."files/img/archivo_xls.jpg";
						break;
						case "pdf":
							$img = URL."files/img/archivo_pdf.jpg";
						break;

					}
					$row["NRO_FOTO"] = $img;
					$row["ARCHIVO"] = $value;
					$htm_galeria->AsignaBloque('block_registros',$row);
				}

				$htm_galeria->Asigna('ID_CONTENIDOS_PRINCIPAL', $this->id);
				$htm_galeria->Asigna('FOTO_PRINCIPAL', $archivos[0]);

				$htm_galeria->Asigna('SECCION', $this->nombre_tabla);
				$htm_galeria->Asigna('URL', URL);

				$rta = array (
							"listado" => $htm_galeria->Muestra(),
							"nro_imagen" => $nro_imagenv[0] + 1);
			}else{
				$rta = array (
							"listado" => "",
							"nro_imagen" => 1);
			}
		}else{
			$rta = array (
							"listado" => "",
							"nro_imagen" => 1);
		}

		return ($rta);
	}
	function ListadoImagenes($origen, $tipo = ""){
		if ($tipo == "")
			$htm_galeria = $this->Html("listado_imagenes");
		else
			$htm_galeria = $this->Html($this->nombre_tabla."/listado_imagenes_".$tipo);

		switch ($origen){
			case "admin":
				$dir = "";
			break;
			case "ajax":
				$dir = "../";
			break;
		}
		$nombre_carpeta = $dir."files/img/".$this->nombre_tabla."/original/".$this->id;

		$sin_img = $dir."files/img/".$this->nombre_tabla."/no_imagen_100_100.jpg";

		if (is_dir($nombre_carpeta)){
			$archivos = scandir($nombre_carpeta);
			$cantidad_img = count($archivos) - 2; //No cuento . y ..
			if ($cantidad_img != 0) {
				unset ($archivos[0]); // borro .
				unset ($archivos[1]); // borro ..

				natsort($archivos);

				$primer_foto = $archivos[2];

				if ($tipo == ""){
					$archivos = array_reverse($archivos);
				}


				$nro_imagenv = explode('.', $archivos[0]);

				foreach ($archivos as $value){
					$row["ID_CONTENIDOS"] = $this->id;
					$row["NRO_FOTO"] = $value;
					$htm_galeria->AsignaBloque('block_registros',$row);
				}

				$img_face = $dir."files/img/".$this->nombre_tabla."/100_100/".$this->id."/".$archivos[0];

				$htm_galeria->Asigna('ID_CONTENIDOS_PRINCIPAL', $this->id);
				$htm_galeria->Asigna('FOTO_PRINCIPAL', $archivos[0]);

				$htm_galeria->Asigna('SECCION', $this->nombre_tabla);

				$clave = array_rand($archivos, 1);
				$nombre_aleatorio = $archivos[$clave];


				$rta = array (
							"listado" => $htm_galeria->Muestra(),
							"nro_imagen" => $nro_imagenv[0] + 1,
							"nombre_aleatorio" => $nombre_aleatorio,
							"img_face" => $img_face,
							"primer_foto" => $primer_foto);
			}else{
				$rta = array (
							"listado" => "",
							"nro_imagen" => 1,
							"nombre_aleatorio" => 0,
							"img_face" => $sin_img,
							"primer_foto" => 0);
			}
		}else{
			$rta = array (
							"listado" => "",
							"nro_imagen" => 1,
							"nombre_aleatorio" => 0,
							"img_face" => $sin_img,
							"primer_foto" => 0);
		}

		return ($rta);
	}

	function ReproductorVideo($origen, $fuente, $tipo = ""){
		$htm_reproductor = $this->Html("reproductor_video_".$fuente);

		switch ($origen){
			case "admin":
				$base = "";
			break;
			case "ajax":
				$base = "../";
			break;
		}

		$dir = "files/videos/".$this->nombre_tabla."/".$this->id;
		$nombre_carpeta = $base.$dir;
		if (is_dir($nombre_carpeta)){
			$archivos = scandir($nombre_carpeta);
			$cantidad_videos = count($archivos) - 2; //No cuento . y ..
			if ($cantidad_videos != 0) {
				unset ($archivos[0]); // borro .
				unset ($archivos[1]); // borro ..

				natsort($archivos);

				if ($tipo == ""){
					$archivos = array_reverse($archivos);
				}


				$dir .= "/".$archivos[0];
				$htm_reproductor->Asigna('ID', $this->id);
				$htm_reproductor->Asigna('TABLA', $this->nombre_tabla);
				$htm_reproductor->Asigna('URL_VIDEO', $dir);




				$rta = array (
							"reproductor" => $htm_reproductor->Muestra(),
							"cant_videos" => $cantidad_videos);
			}else{
				$rta = array (
							"reproductor" => "",
							"cant_videos" => 0);
			}
		}else{
			$rta = array (
							"reproductor" => "",
							"cant_videos" => 0);
		}

		return ($rta);
	}


	function NombreColumnas(){// DEVUELVE EL NOMBRE DE LAS COLUMNAS DE UNA TABLA DADA
		$query_string = $this->querys->TodosRegistros($this->nombre_tabla, "DESC");
		$query = $this->db->consulta($query_string);
		$num = $this->db->num_fields($query);
		$xcampo = array();
		for ($x=0;$x<$num;$x++){
			$xcampo[$x] = $this->db->field_name($query, $x);
		}
		return $xcampo;
	}

	function Alta($datos, $columnas, $noautoincremental = "",$tipo = ""){
		if ($noautoincremental == ""){
			//ELIMINO LA PRIMERA COLUMNA PORQUE ES AUTOINCREMETAL, NO CARGO ESTE ATRIBUTO
			unset ($columnas[0]);
		}

		$columnas = implode(",",$columnas);
		$query_string = $this->querys->Alta($this->nombre_tabla, $columnas,$datos);
		if ($this->db->consulta($query_string))
			$rta = $this->db->ultimo_id_insertado();
		else
			$rta = false;
		return $rta;
	}

	function Baja($id = ""){

		if ($id == ""){
			$id = $this->id;
		}

		$query_string = $this->querys->Baja($this->nombre_tabla, $id);
		if ($this->db->consulta($query_string))
			$rta = "Baja Exitosa";
		else
			$rta = "No se pudo dar de bajo el registro. Intente de nuevo";

		return $rta;
	}

	function Inhabilitar($id = ""){

		if ($id == ""){
			$id = $this->id;
		}

		$query_string = $this->querys->Inhabilitar($this->nombre_tabla, $id);
		if ($this->db->consulta($query_string))
			$rta = "Inhabilitaci&oacute;n Exitosa";
		else
			$rta = "No se pudo inhabilitar el registro. Intente de nuevo";

		return $rta;
	}

	function Modificacion($datos, $columnas, $tipo = ""){
		$query_string = $this->querys->Modificacion($this->nombre_tabla, $datos, $columnas, $tipo);
		if ($this->db->consulta($query_string))
			$rta = "Modificación Exitosa";
		else
			$rta = "Ocurrió un Error";
		return $rta;
	}

	function CargarVideoYT($id,$video_yt){
		$query_string = $this->querys->CargarVideoYT($this->nombre_tabla, $id, $video_yt);
		if ($this->db->consulta($query_string))
			$rta = "Video Cargado Correctamente";
		else
			$rta = false;
		return $rta;
	}

	function EnviarMail($datos,$tipo){ //$destinos ES UN ARRAY CON LOS DESTINOS DEL MAIL
		  requerir_class("phpmailer");
		  parse_str(stripslashes($datos));
		  $destino1 = "info@reproisa.com";
		  $mail = new PHPMailer();
		  //$mail->IsSMTP();
		  //$mail->SMTPAuth = true;
		  //$mail->SMTPSecure = "ssl";
		  $mail->Host = "mail.reproisa.com";
		  //$mail->Port = 465;
		  $mail->Username = "info@reproisa.com";
		  $mail->Password = "Diablo021085";
		  $mail->From = "info@reproisa.com";
		  $mail->FromName = utf8_decode(TITULO_SITIO);

		  //foreach ($destinos as $i => $value){
		  //$mail->AddAddress($destino2);
		  $mail->AddAddress($destino1);
		  //}
		  //$mail->AddBCC("info@reproisa.com");
		  //$encriptar = base64_encode("email=".$destino."&token=".$token);
		  //$url = "http://www.AdBisnes.com/activacion.php?".$encriptar;
		  //$url="http://www.adbisnes.com/activacion.php?".$encriptar;
		  $fecha= date("d-m-Y H:i:s");

		  $body = "";
		  switch ($tipo){
			case "curriculum":
				$mail->Subject = utf8_decode("Contacto por Curriculum - ReproiSa");
				//BUSCO EL ARCHIVO DEL CURRICULUM PARA ADJUNTARLO EN EL MAIL
				$nombre_carpeta = ROOT."files/curriculum/".$id_curriculum;
				if (is_dir($nombre_carpeta)){
					$archivos = scandir($nombre_carpeta);
					$cantidad_img = count($archivos) - 2; //No cuento . y ..
					if ($cantidad_img != 0) {
						unset ($archivos[0]); // borro .
						unset ($archivos[1]); // borro ..
						natsort($archivos);
						foreach ($archivos as $value){
							$arc=$nombre_carpeta."/".$value;
							//ADJUNTO EL O LOS ARCHIVOS
							$mail->AddAttachment($arc);
						}
					}
				}
			  	$fecha= date("d-m-Y H:i:s");
				$url="http://www.reproisa.com/";
				$body = "<a href='".$url."'><img src='http://www.reproisa.com/files/img/header.png' border='0'></a>";
				$body .= "<p><strong>Interesados en formar parte en ReproiSa</strong></p>";
				$body .= "<p><strong>IP: </strong>".$this->GetRealIP()."</p>";
				$body .= "<p><strong>Transaccion (COD): </strong>".$id_curriculum."</p>";
				$body .= "<p><strong>Fecha y Hora: </strong>".$fecha."</p>";
				$body .= "<p><strong>Email: </strong>".$email."</p>";
				$body .= "<p><strong>Aporte: </strong>".utf8_decode($descripcion)."</p>";
				$body .= "<p><strong><a href='".$url."'>www.ReproiSa.com</a></strong></p>";

			break;
			case "contacto":
				$mail->Subject = utf8_decode("Comentario desde formulario Contacto");
				$body .= "<p><strong>Comentario desde formulario de contacto</strong></p>";
				$body .= "<p><strong>Fecha y Hora: </strong>".$fecha."</p>";
				$body .= "<p><strong>De: </strong>".utf8_decode($nombre)."</p>";
				$body .= "<p><strong>Email: </strong>".$email."</p>";
				$body .= "<p><strong>Direccion: </strong>".$direccion."</p>";
				$body .= "<p><strong>CP: </strong>".$cp."</p>";
				$body .= "<p><strong>Tel&eacute;fono: </strong>".$telefono."</p>";
				$body .= "<p><strong>Comentario: </strong>".utf8_decode($mensaje)."</p>";
			break;
			case "suscripciones":
				$destino1 = $email;
				$cod = base64_encode($email);
				$link = "<a href=".URL."confirmar_suscripcion.php?cod=".$cod.">".utf8_decode("Confirmar Suscripción")."</a>";
				$mail->Subject = utf8_decode("Confirmación de Suscripción");
				$body .= "<p>Estimado/a hemos recibido un pedido de suscripci&oacute;n del siguiente correo: ".$email." en nuestro sitio web: <a href='".URL."'><b>".URL."</b></a> para confirmar la suscripci&oacute;n haga click en el siguiente link.</p>";
				$body .= "<p><strong>Link: </strong>".$link."</p>";

			break;

		  }

		  $mail->AddAddress($destino1);
		  $mail->AddBCC("info@reproisa.com");
		  $mail->Body = $body;
		  $mail->IsHTML(true);
		  	if(!$mail->Send()) {
		        $rta = 'Error: ' . $mail->ErrorInfo;
			}
			else {
				$rta =  "Gracias, en breve nos pondremos en contacto con usted.";
			}

		echo $rta;
 	}




	function EnviarMailNewsletters($datos,$tipo){ //$destinos ES UN ARRAY CON LOS DESTINOS DEL MAIL
		requerir_class("phpmailer");
		parse_str(stripslashes($datos));
		$mail = new PHPMailer();
		$mail->Host = "mail.educacionsalta.com.ar";
		$mail->Username = "soporte@educacionsalta.com.ar";
		$mail->Password = "Diablo6686";
		$mail->From = $email_rta;
		$mail->FromName = utf8_decode($nombre_proyecto);
		$mail->Subject = utf8_decode($asunto);
		$fecha= date("d-m-Y H:i:s");
		$body = "";
		switch ($tipo){
			case "newsletter_db":
				$query_string = "SELECT * FROM suscripciones";
				$query = $this->db->consulta($query_string);
				$cant = $this->db->num_rows($query);
				if ($cant != 0){
					while ($row = $this->db->fetch_array($query)){
						$email = $row["email"];
						$mail->AddAddress($email);
						$mail->Body = utf8_decode($cuerpo);
						$mail->IsHTML(true);
						if(!$mail->Send()) {
							$rta = 'Error. No se entrego el mail a '.$email.' | '.$mail->ErrorInfo.'<br>';
						}else {
							$rta = 'Mail enviado a '.$email.' <br>';
						}
						echo $rta;
						$mail->ClearAddresses();
						//sleep(1);
					}
				}else{
					$rta = "No existen registros cargados.";
					echo $rta;
				}
			break;
			case "newsletter_txt":
				$query_string = "SELECT * FROM suscripciones";
				$query = $this->db->consulta($query_string);
				$cant = $this->db->num_rows($query);
				if ($cant != 0){
					while ($row = $this->db->fetch_array($query)){
						$email = $row["email"];
						$mail->AddAddress($email);
						$mail->Body = utf8_decode($cuerpo);
						$mail->IsHTML(true);
						if(!$mail->Send()) {
							$rta = 'Error. No se entrego el mail a '.$email.' | '.$mail->ErrorInfo.'<br>';
						}else {
							$rta = 'Mail enviado a '.$email.' <br>';
						}
						echo $rta;
						$mail->ClearAddresses();
						//sleep(1);
					}
				}else{
					$rta = "No existen registros cargados.";
					echo $rta;
				}
			break;
		}
	}




	function EliminarImagen($nro,$secc){

		$dir="../files/img/".$secc."/";
		$archivos = scandir($dir);
		unset ($archivos[0]);
		unset ($archivos[1]);



		foreach ($archivos as $value){
			 if ($this->id!=""){
			 	$direccion = $dir.$value."/".$this->id."/".$nro;
			 }else{
				$direccion = $dir.$value."/".$nro;
			 }
			 unlink ($direccion);
		}

	}

	function EliminarVideo(){

		$dir="../files/videos/".$this->nombre_tabla."/".$this->id."/";
		$archivos = scandir($dir);
		unset ($archivos[0]);
		unset ($archivos[1]);

		foreach ($archivos as $value){
		 	$direccion = $dir.$value;
			unlink ($direccion);
		}

		rmdir($dir);

	}

	/*function ConversorMonedas($moneda_origen,$moneda_destino,$cantidad) {
		$cantidad = urlencode($cantidad);
		$moneda_origen = urlencode($moneda_origen);
		$moneda_destino = urlencode($moneda_destino);
		$url = "http://www.google.com/ig/calculator?hl=en&q=$cantidad$moneda_origen=?$moneda_destino";
		$ch = curl_init();
		$timeout = 0;
		curl_setopt ($ch, CURLOPT_URL, $url);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch,  CURLOPT_USERAGENT , "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1)");
		curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		$rawdata = curl_exec($ch);
		curl_close($ch);
		$data = explode('"', $rawdata);
		$data = explode(' ', $data['3']);
		$var = $data['0'];
		return $var;
	}*/

	function ConversorMonedas($moneda_origen,$moneda_destino,$cantidad) {
		$cant_origen = 1;
		$moneda_origen = urlencode($moneda_origen);
		$moneda_destino = urlencode($moneda_destino);
		$url = "http://www.google.com/ig/calculator?hl=en&q=$cant_origen$moneda_origen=?$moneda_destino";
		$ch = curl_init();
		$timeout = 0;
		curl_setopt ($ch, CURLOPT_URL, $url);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_USERAGENT , "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1)");
		curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		$rawdata = curl_exec($ch);
		curl_close($ch);

		$data = explode('"', $rawdata);
		$data = explode(' ', $data['3']);

		$data_string = (string)$data['0'];
		$data_string = utf8_decode($data_string);
		$data_string = str_replace(chr(160), "", $data_string);

		$var = (float)$data_string;

		$total = $var * $cantidad;
		return round($total,2);
		}

	function GetRealIP(){
	   if( $_SERVER['HTTP_X_FORWARDED_FOR'] != '' ){
		  $client_ip = (!empty($_SERVER['REMOTE_ADDR'])) ? $_SERVER['REMOTE_ADDR'] : ( ( !empty($_ENV['REMOTE_ADDR']) ) ? $_ENV['REMOTE_ADDR'] : "unknown" );

		  // los proxys van añadiendo al final de esta cabecera
		  // las direcciones ip que van "ocultando". Para localizar la ip real
		  // del usuario se comienza a mirar por el principio hasta encontrar
		  // una dirección ip que no sea del rango privado. En caso de no
		  // encontrarse ninguna se toma como valor el REMOTE_ADDR

		  $entries = split('[, ]', $_SERVER['HTTP_X_FORWARDED_FOR']);

		  reset($entries);
		  while (list(, $entry) = each($entries)){
			 $entry = trim($entry);
			 if (preg_match("/^([0-9]+\.[0-9]+\.[0-9]+\.[0-9]+)/", $entry, $ip_list)){
				// http://www.faqs.org/rfcs/rfc1918.html
				$private_ip = array(
					  '/^0\./',
					  '/^127\.0\.0\.1/',
					  '/^192\.168\..*/',
					  '/^172\.((1[6-9])|(2[0-9])|(3[0-1]))\..*/',
					  '/^10\..*/');
				$found_ip = preg_replace($private_ip, $client_ip, $ip_list[1]);
				if ($client_ip != $found_ip){
				   $client_ip = $found_ip;
				   break;
				}
			 }
		  }
	   }else{
		  $client_ip = (!empty($_SERVER['REMOTE_ADDR']) ) ? $_SERVER['REMOTE_ADDR'] : ( ( !empty($_ENV['REMOTE_ADDR']) ) ? $_ENV['REMOTE_ADDR'] : "unknown" );
	   }
	   return $client_ip;
	}

	function BorrarDirectorio($dir, $borrarme){
		if(!$dh = @opendir($dir)) return;
		while (false !== ($obj = readdir($dh)))
		{
			if($obj=='.' || $obj=='..') continue;
			if (!@unlink($dir.'/'.$obj)) borrar_directorio($dir.'/'.$obj, true);
		}
		closedir($dh);
		if ($borrarme)
		{
			@rmdir($dir);
		}
	}

	function DescripcionFacebook($datos){
		$htm = $this->html("descripcion_facebook");
		$htm->Asigna("TITULO",utf8_encode($datos["titulo"]));
		$htm->Asigna("TITULO_SITIO",utf8_encode($datos["titulo_sitio"]));
		$htm->Asigna("URL",$datos["url"]);
		$htm->Asigna("URL_IMG",$datos["url_img"]);
		$htm->Asigna("DESCRIPCION_WEB",utf8_encode(strip_tags($datos["descripcion_web"])));
		return $htm->Muestra();
	}

	function ActualizarPosicion($x, $y){
		$query_string = $this->querys->ActualizarPosicion($this->nombre_tabla, $this->id, $x, $y);
		if ($this->db->consulta($query_string))
			$rta = true;
		else
			$rta = false;
		return $rta;

	}

	function ExisteUbicacionGM(){
		if ($this->x == 0 && $this->y == 0){
			$rta = false;
		}else{
			$rta = true;
		}

		return $rta;
	}

	function EliminarComillas($cadena){
		$cadena = str_replace("'","", $cadena);
		$cadena = str_replace('"',"", $cadena);
		return $cadena;
	}

	function EliminarSaltos($texto){
		return preg_replace("[\n|\r|\n\r]", '', $texto);
	}

	function Paginacion($t,$p,$link){
		if($p>=1 || $p<=$t){
		   $o = 5;
		   if($t>1){
			 // Iniciamos la variable
			 $a = "";
			 // Link Primera Pagina)
			 if($p>2){
			   $ln = str_replace('{P}','1',$link);
			   $a .= '<li class="ctrl_grales">'.$ln.'Primera P&aacute;g.</a></li>';
			 }
			 // Link Pagina Anterior
			 if($p>1){
			   $ln = str_replace('{P}',($p-1),$link);
			   $a .= '<li class="ctrl_grales">'.$ln.'P&aacute;g. Anterior</a></li>';
			 }
			 $offset = $p-$o;
			 $offset_init = ($p-($o+2));
			 if($p>4){
			   for($i=$offset_init;$i<=($offset+1);$i++){
				 if($i>0){
				   $ln = str_replace('{P}',$i,$link);
				   $a .= '<li>'.$ln.$i.'</a></li>';
				 }
			   }
			   $a .= '<li><span class="separador">...</span></li>';
			   for($i=$p;$i<($p+4);$i++){
				 if($i<=$t){
				   if($i==$p){
					 $a .= '<li><span class="active">'.$i.'</span></li>';
				   }else{
					 $ln = str_replace('{P}',$i,$link);
					 $a .= '<li>'.$ln.$i.'</a></li>';
				   }
				 }
			   }
			 }else{
			   for($i=1;$i<($p+4);$i++){
				 if($i<=$t){
				   // Esta Pagina
				   if($i==$p){
					 $a .= '<li><span class="active">'.$i.'</span></li>';
				   }else{
					 $ln = str_replace('{P}',$i,$link);
					 $a .= '<li>'.$ln.$i.'</a></li>';
				   }
				 }
			   }
			 }
			 // Link Pagina Siguiente
			 if($p<$t){
			   $ln = str_replace('{P}',($p+1),$link);
			   $a .= '<li class="ctrl_grales">'.$ln.'P&aacute;g. Siguiente</a></li>';
			 }
			 // Link Ultima Pagina
			 if($p<($t-2)){
			   $ln = str_replace('{P}',$t,$link);
			   $a .= '<li class="ctrl_grales">'.$ln.'Ultima P&aacute;g.</a></li>';
			 }
		   }
		return $a;
		}
  	}
	function ListadoArchivoCurriculum($origen, $id){
		$htm_galeria = $this->Html("listado_archivo");
		switch ($origen){
			case "admin":
				$dir = "";
			break;
			case "ajax":
				$dir = "../";
			break;
		}
		$nombre_carpeta = $dir."files/curriculum/".$id;

		if (is_dir($nombre_carpeta)){
			$archivos = scandir($nombre_carpeta);
			$cantidad_img = count($archivos) - 2; //No cuento . y ..
			if ($cantidad_img != 0) {
				unset ($archivos[0]); // borro .
				unset ($archivos[1]); // borro ..

				natsort($archivos);
				$archivos = array_reverse($archivos);

				$nro_imagenv = explode('.', $archivos[0]);

				foreach ($archivos as $value){
					$row["ID_CONTENIDOS"] = $id;
					$ext =  strtolower(substr(strrchr($value, '.'), 1));
					switch ($ext){
						case "ppt":
						case "pptx":
							$img = "ppt.jpg";
						break;
						case "doc":
						case "docx":
							$img = "doc.jpg";
						break;
						case "xls":
						case "xlsx":
							$img = "xls.jpg";
						break;
						case "pdf":
							$img = "pdf.jpg";
						break;

					}
					$row["NRO_FOTO"] = $value;
					$row["DOC_FOTO"] = $img;
					$row["URL"] = URL;

					$htm_galeria->AsignaBloque('block_registros',$row);
				}
				$htm_galeria->Asigna('SECCION','curriculum');
				$htm_galeria->Asigna('WEB',URL);
				$rta = array (
							"listado" => $htm_galeria->Muestra(),
							"nro_imagen" => $nro_imagenv[0] + 1);
			}else{
				$rta = array (
							"listado" => "",
							"nro_imagen" => 1);
			}
		}else{
			$rta = array (
							"listado" => "",
							"nro_imagen" => 1);
		}
		return ($rta);
	}

	function LimpiarVariables($htm){
		$cadena = $htm;
		$s=0;
		$j=strlen($cadena);
		for($i=0; $i<=$j;$i++)
		{
			if($cadena[$i]=='[') $s=1;
			if($s==0) $cadena1 .= $cadena[$i];
			if($cadena[$i]==']') $s=0;
		}

		return $cadena1;

	}

	function Navegacion($accion, $id_padre = "", $tipo = ""){
		$htm = $this->Html("navegacion");

		switch($this->nombre_tabla){
			case "empresas":
				$htm->Asigna("DATOS_EMPRESA","");
				$url_mas_opciones = "empresas.php";
				$titulo_mas_opciones = "Volver a Listado ".$this->titulo_tabla;
			break;

			case "clientes":
				requerir_class("empresas");
				$obj_empresa = new Empresas($id_padre);

				$datos_empresa = "<h2 class='empresa'>Empresa: <span class='negro'>".$obj_empresa->nombre."</span>";
				/*$datos_empresa .= '<a href="clientes.php?empresa='.$id_padre.'" class="button" title="Empresas Clientes"><img src="files/img/admin/clientes.png" border="0"></a>
                    			<a href="form_modificacion.php?secc=empresas&id='.$id_padre.'" class="button" title="Editar"><img src="files/img/admin/editar.png" border="0"></a>';*/
				$datos_empresa .= "<a href='empresas.php' class='azul volver'>Cambiar Empresa</a></h2>";

				$htm->Asigna("DATOS_EMPRESA",$datos_empresa);

				$url_mas_opciones =  $this->nombre_tabla.".php?empresa=".$id_padre;
				$titulo_mas_opciones = "Volver a Listado ".$this->titulo_tabla;
			break;

			case "reportes":
				requerir_class("empresas");
				$obj_empresa = new Empresas($id_padre);

				$datos_empresa = "<h2 class='empresa'>Empresa: <span class='negro'>".$obj_empresa->nombre."</span>";
				/*$datos_empresa .= '<a href="clientes.php?empresa='.$id_padre.'" class="button" title="Empresas Clientes"><img src="files/img/admin/clientes.png" border="0"></a>
                    			<a href="form_modificacion.php?secc=empresas&id='.$id_padre.'" class="button" title="Editar"><img src="files/img/admin/editar.png" border="0"></a>';*/
				$datos_empresa .= "<a href='empresas.php' class='azul volver'>Cambiar Empresa</a></h2>";

				$htm->Asigna("DATOS_EMPRESA",$datos_empresa);

				$url_mas_opciones =  $this->nombre_tabla.".php?empresa=".$id_padre;
				$titulo_mas_opciones = "Volver a Listado ".$this->titulo_tabla;

			break;

			case "casos":
				requerir_class("clientes","empresas");
				$obj_cliente = new Clientes($id_padre);
				$obj_empresa = new Empresas($obj_cliente->id_empresas);

				$datos_empresa = "<h2 class='empresa'>Empresa: <span class='negro'>".$obj_empresa->nombre."</span>";
				/*$datos_empresa .= '<a href="clientes.php?empresa='.$obj_cliente->id_empresas.'" class="button" title="Empresas Clientes"><img src="files/img/admin/clientes.png" border="0"></a>
                    			<a href="form_modificacion.php?secc=empresas&id='.$obj_cliente->id_empresas.'" class="button" title="Editar"><img src="files/img/admin/editar.png" border="0"></a>';*/
				$datos_empresa .= "<a href='empresas.php' class='azul volver'>Cambiar Empresa</a></h2>";


				$datos_cliente = "<h2 class='empresa'>Cliente: <span class='negro'>".$obj_cliente->nombre."</span><a href='clientes.php?empresa=".$obj_empresa->id_empresas."' class='azul volver'>Cambiar Cliente</a></h2>";

				$htm->Asigna("DATOS_EMPRESA",$datos_empresa.$datos_cliente);

				$url_mas_opciones =  $this->nombre_tabla.".php?cliente=".$id_padre;
				$titulo_mas_opciones = "Volver a Listado ".$this->titulo_tabla;
			break;



			case "pagos":
			case "gestiones":
			case "recordatorios":

				if ($id_padre != 0){
					requerir_class("casos", "clientes", "empresas");
					$obj_caso = new Casos($id_padre);
					$obj_cliente = new Clientes($obj_caso->id_clientes);
					$obj_empresa = new Empresas($obj_cliente->id_empresas);

					$datos_empresa = "<h2 class='empresa'>Empresa: <span class='negro'>".$obj_empresa->nombre."</span>";
					/*$datos_empresa .= '<a href="clientes.php?empresa='.$obj_cliente->id_empresas.'" class="button" title="Empresas Clientes"><img src="files/img/admin/clientes.png" border="0"></a>
									<a href="form_modificacion.php?secc=empresas&id='.$obj_cliente->id_empresas.'" class="button" title="Editar"><img src="files/img/admin/editar.png" border="0"></a>';*/
					$datos_empresa .= "<a href='empresas.php' class='azul volver'>Cambiar Empresa</a></h2>";


					$datos_cliente = "<h2 class='empresa'>Cliente: <span class='negro'>".$obj_cliente->nombre."</span><a href='clientes.php?empresa=".$obj_empresa->id_empresas."' class='azul volver'>Cambiar Cliente</a></h2>";

					requerir_class("tipos_conceptos");
					$obj_tipo_concepto = new Tipos_conceptos($obj_caso->id_tipos_conceptos);

					$datos_caso = "<h2 class='empresa'>Caso: <span class='negro'>".$obj_caso->responsable."(".$obj_tipo_concepto->nombre.")</span><br /><br />";

					switch ($_SESSION['TIPO_ACCESO']){
						case 3: //ADMINISTRADOR
							$datos_caso .= "<a href='recordatorios.php?caso=".$id_padre."' class='button' title='Recordatorios'><img src='files/img/admin/recordatorios.png' border='0'></a>
							<a href='pagos.php?caso=".$id_padre."' class='button' title='Pagos'><img src='files/img/admin/cuentacorriente.png' border='0'></a>
							<a href='gestiones.php?caso=".$id_padre."' class='button' title='Gestiones'><img src='files/img/admin/gestiones.png' border='0'></a>
							<a href='mostrar.php?secc=casos&id=".$id_padre."' class='button' title='Detalle'><img src='files/img/admin/detalle.png' border='0'></a>
							<a href='form_modificacion.php?secc=".$obj_caso->nombre_tabla."&id=".$id_padre."' class='button' title='Editar'><img src='files/img/admin/editar.png' border='0'></a>";
						break;
						case 1: //COMUN
							$datos_caso .= "<a href='recordatorios.php?caso=".$id_padre."' class='button' title='Recordatorios'><img src='files/img/admin/recordatorios.png' border='0'></a>
							<a href='pagos.php?caso=".$id_padre."' class='button' title='Pagos'><img src='files/img/admin/cuentacorriente.png' border='0'></a>
							<a href='gestiones.php?caso=".$id_padre."' class='button' title='Gestiones'><img src='files/img/admin/gestiones.png' border='0'></a>
							<a href='mostrar.php?secc=casos&id=".$id_padre."' class='button' title='Detalle'><img src='files/img/admin/detalle.png' border='0'></a>";
						break;
					}


					$datos_caso .= "<a href='casos.php?cliente=".$obj_cliente->id_clientes."' class='azul volver'>Cambiar Caso</a></h2>";

					$htm->Asigna("DATOS_EMPRESA",$datos_empresa . $datos_cliente . $datos_caso);

					$url_mas_opciones = $this->nombre_tabla.".php?caso=".$id_padre;
					$titulo_mas_opciones = "Volver a Listado ".$this->titulo_tabla;
				}else{
					//POR EJEMPLO ESTO PADA EN CALCULADORA DE DEUDA
					$htm->Asigna("DATOS_EMPRESA","");
					$url_mas_opciones = "empresas.php";
					$titulo_mas_opciones = "Volver a Listado de Empresas";
				}

			break;


			case "tipos_gestiones":

				$htm->Asigna("DATOS_EMPRESA","");

				if ($id_padre != ""){
					$url_mas_opciones = "form_alta.php?secc=gestiones&caso=".$id_padre;
					$titulo_mas_opciones = "Volver a Alta Nueva Gesti&oacute;n";
				}else{
					$url_mas_opciones = "empresas.php";
					$titulo_mas_opciones = "Volver a Listado Empresas";
				}
			break;
			case "tipos_usuarios":

				$htm->Asigna("DATOS_EMPRESA","");

				switch ($accion){
					case "alta":
					case "modificacion":
						$url_mas_opciones = "tipos_usuarios.php";
						$titulo_mas_opciones = "Volver a Tipos de Usuarios";
					break;
					case "listado":
						$url_mas_opciones = "empresas.php";
						$titulo_mas_opciones = "Volver a Listado Empresas";
					break;
				}
			break;
			case "tipos_conceptos":
				$htm->Asigna("DATOS_EMPRESA","");

				if ($id_padre != ""){
					$url_mas_opciones = "form_alta.php?secc=casos&cliente=".$id_padre;
					$titulo_mas_opciones = "Volver a Alta Nuevo Caso";
				}else{
					$url_mas_opciones = "empresas.php";
					$titulo_mas_opciones = "Volver a Listado Empresas";
				}
			break;
			case "usuarios":
				$htm->Asigna("DATOS_EMPRESA","");

				if ($accion != "listado"){
					$url_mas_opciones = "usuarios.php";
					$titulo_mas_opciones = "Volver a Listado Usuarios";
				}else{
					$url_mas_opciones = "empresas.php";
					$titulo_mas_opciones = "Volver a Listado Empresas";
				}
			break;
			default:
				$htm->Asigna("DATOS_EMPRESA","");

				$url_mas_opciones = "empresas.php";
				$titulo_mas_opciones = "Volver a Listado Empresas";
		}


		switch($accion){
			case "listado":
				switch ($this->nombre_tabla){
					case "tipos_gestiones":
					case "tipos_conceptos":
					case "tipos_usuarios":
					case "usuarios":
						$accion_titulo = "Listado";
						$mas_opciones = "<a class='azul volver' href='".$url_mas_opciones."'>".$titulo_mas_opciones."</a>";
					break;
					case "reportes":
						$accion_titulo = $this->titulo_tabla;
						$mas_opciones = "";
					break;
					default:
						$accion_titulo = "Listado";
						$mas_opciones = "";
				}
			break;
			case "alta":
				$accion_titulo = "Alta ".$this->titulo_btn_alta;
				$mas_opciones = "<a class='azul volver' href='".$url_mas_opciones."'>".$titulo_mas_opciones."</a>";
			break;
			case "modificacion":
				$accion_titulo = "Modificaci&oacute;n Registro";
				$mas_opciones = "<a class='azul volver' href='".$url_mas_opciones."'>".$titulo_mas_opciones."</a>";
			break;
			case "detalle":
				$accion_titulo = "Detalle";
				$mas_opciones = "<a class='azul volver' href='".$url_mas_opciones."'>".$titulo_mas_opciones."</a>";
			break;
			case "calculadora":
				$accion_titulo = "Calculadora de Deuda";
				$mas_opciones = "<a class='azul volver' href='".$url_mas_opciones."'>".$titulo_mas_opciones."</a>";
			break;
			case "buscar":
				$accion_titulo = "Buscar";
				$mas_opciones = "<a class='azul volver' href='".$url_mas_opciones."'>".$titulo_mas_opciones."</a>";
			break;
		}


		$htm->Asigna("ACCION",$accion_titulo);
		$htm->Asigna("MAS_OPCIONES",$mas_opciones);

		return $htm->Muestra();
	}

	function SumarHorasTime($h1,$h2){
		$h2h = date('H', strtotime($h2));
		$h2m = date('i', strtotime($h2));
		$h2s = date('s', strtotime($h2));
		$hora2 =$h2h." hour ". $h2m ." min ".$h2s ." second";

		$horas_sumadas= $h1." + ". $hora2;
		$text=date('H:i:s', strtotime($horas_sumadas)) ;
		return $text;
	}

	function RestarHorasTime($horaIni, $horaFin){

		return (date("H:i:s", strtotime("00:00:00") + strtotime($horaFin) - strtotime($horaIni) ));

	}

	function NroLetras($xcifra){
		$xarray = array(0 => "Cero",
	1 => "UN", "DOS", "TRES", "CUATRO", "CINCO", "SEIS", "SIETE", "OCHO", "NUEVE",
	"DIEZ", "ONCE", "DOCE", "TRECE", "CATORCE", "QUINCE", "DIECISEIS", "DIECISIETE", "DIECIOCHO", "DIECINUEVE",
	"VEINTI", 30 => "TREINTA", 40 => "CUARENTA", 50 => "CINCUENTA", 60 => "SESENTA", 70 => "SETENTA", 80 => "OCHENTA", 90 => "NOVENTA",
	100 => "CIENTO", 200 => "DOSCIENTOS", 300 => "TRESCIENTOS", 400 => "CUATROCIENTOS", 500 => "QUINIENTOS", 600 => "SEISCIENTOS", 700 => "SETECIENTOS", 800 => "OCHOCIENTOS", 900 => "NOVECIENTOS"
	);
	//
		$xcifra = trim($xcifra);
		$xlength = strlen($xcifra);
		$xpos_punto = strpos($xcifra, ".");
		$xaux_int = $xcifra;
		$xdecimales = "00";
		if (!($xpos_punto === false)){
			if ($xpos_punto == 0){
				$xcifra = "0".$xcifra;
				$xpos_punto = strpos($xcifra, ".");
			}
			$xaux_int = substr($xcifra, 0, $xpos_punto); // obtengo el entero de la cifra a covertir
			$xdecimales = substr($xcifra."00", $xpos_punto + 1, 2); // obtengo los valores decimales
		}

		$XAUX = str_pad($xaux_int, 18, " ", STR_PAD_LEFT); // ajusto la longitud de la cifra, para que sea divisible por centenas de miles (grupos de 6)
		$xcadena = "";
		for($xz = 0; $xz < 3; $xz++){
			$xaux = substr($XAUX, $xz * 6, 6);
			$xi = 0; $xlimite = 6; // inicializo el contador de centenas xi y establezco el l&#65533;mite a 6 d&#65533;gitos en la parte entera
			$xexit = true; // bandera para controlar el ciclo del While
			while ($xexit)
			{
				if ($xi == $xlimite) // si ya lleg&#65533; al l&#65533;mite máximo de enteros
				{
					break; // termina el ciclo
				}

				$x3digitos = ($xlimite - $xi) * -1; // comienzo con los tres primeros digitos de la cifra, comenzando por la izquierda
				$xaux = substr($xaux, $x3digitos, abs($x3digitos)); // obtengo la centena (los tres d&#65533;gitos)
				for ($xy = 1; $xy < 4; $xy++) // ciclo para revisar centenas, decenas y unidades, en ese orden
				{
					switch ($xy){
						case 1: // checa las centenas
							if (substr($xaux, 0, 3) < 100) // si el grupo de tres d&#65533;gitos es menor a una centena ( < 99) no hace nada y pasa a revisar las decenas
								{
								}
							else
								{
								$xseek = $xarray[substr($xaux, 0, 3)]; // busco si la centena es n&#65533;mero redondo (100, 200, 300, 400, etc..)
								if ($xseek){
									$xsub = $this->Sufijo($xaux); // devuelve el subfijo correspondiente (Mill&#65533;n, Millones, Mil o nada)
									if (substr($xaux, 0, 3) == 100)
										$xcadena = " ".$xcadena." CIEN ".$xsub;
									else
										$xcadena = " ".$xcadena." ".$xseek." ".$xsub;
									$xy = 3; // la centena fue redonda, entonces termino el ciclo del for y ya no reviso decenas ni unidades
								}else{ // entra aqu&#65533; si la centena no fue numero redondo (101, 253, 120, 980, etc.)
									$xseek = $xarray[substr($xaux, 0, 1) * 100]; // toma el primer caracter de la centena y lo multiplica por cien y lo busca en el arreglo (para que busque 100,200,300, etc)
									$xcadena = " ".$xcadena." ".$xseek;
								} // ENDIF ($xseek)
							} // ENDIF (substr($xaux, 0, 3) < 100)
						break;
						case 2: // checa las decenas (con la misma l&#65533;gica que las centenas)
							if (substr($xaux, 1, 2) < 10){
							}else{
								$xseek = $xarray[substr($xaux, 1, 2)];
								if ($xseek){
									$xsub = $this->Sufijo($xaux);
									if (substr($xaux, 1, 2) == 20)
										$xcadena = " ".$xcadena." VEINTE ".$xsub;
									else
										$xcadena = " ".$xcadena." ".$xseek." ".$xsub;
									$xy = 3;
								}else{
									$xseek = $xarray[substr($xaux, 1, 1) * 10];
									if (substr($xaux, 1, 1) * 10 == 20)
										$xcadena = " ".$xcadena." ".$xseek;
									else
										$xcadena = " ".$xcadena." ".$xseek." Y ";
								} // ENDIF ($xseek)
							} // ENDIF (substr($xaux, 1, 2) < 10)
						break;
						case 3: // checa las unidades
							if (substr($xaux, 2, 1) < 1){ // si la unidad es cero, ya no hace nada

							}else{
								$xseek = $xarray[substr($xaux, 2, 1)]; // obtengo directamente el valor de la unidad (del uno al nueve)
								$xsub = $this->Sufijo($xaux);
								$xcadena = " ".$xcadena." ".$xseek." ".$xsub;
							} // ENDIF (substr($xaux, 2, 1) < 1)
						break;
					} // END SWITCH
				} // END FOR
				$xi = $xi + 3;
			} // ENDDO

			if (substr(trim($xcadena), -5, 5) == "ILLON") // si la cadena obtenida termina en MILLON o BILLON, entonces le agrega al final la conjuncion DE
				$xcadena.= " DE";

			if (substr(trim($xcadena), -7, 7) == "ILLONES") // si la cadena obtenida en MILLONES o BILLONES, entoncea le agrega al final la conjuncion DE
				$xcadena.= " DE";

			// ----------- esta l&#65533;nea la puedes cambiar de acuerdo a tus necesidades o a tu pa&#65533;s -------
			if (trim($xaux) != ""){
				switch ($xz){
					case 0:
						if (trim(substr($XAUX, $xz * 6, 6)) == "1")
							$xcadena.= "UN BILLON ";
						else
							$xcadena.= " BILLONES ";
					break;
					case 1:
						if (trim(substr($XAUX, $xz * 6, 6)) == "1")
							$xcadena.= "UN MILLON ";
						else
							$xcadena.= " MILLONES ";
					break;
					case 2:
						if ($xcifra < 1 ){
							$xcadena = "$xdecimales/100 ";
						}
						if ($xcifra >= 1 && $xcifra < 2){
							$xcadena = "UNO CON $xdecimales/100 ";
						}
						if ($xcifra >= 2){
							$xcadena.= " CON $xdecimales/100 "; //
						}
					break;
				} // endswitch ($xz)
			} // ENDIF (trim($xaux) != "")
		// ------------------      en este caso, para M&#65533;xico se usa esta leyenda     ----------------
			$xcadena = str_replace("VEINTI ", "VEINTI", $xcadena); // quito el espacio para el VEINTI, para que quede: VEINTICUATRO, VEINTIUN, VEINTIDOS, etc
			$xcadena = str_replace("  ", " ", $xcadena); // quito espacios dobles
			$xcadena = str_replace("UN UN", "UN", $xcadena); // quito la duplicidad
			$xcadena = str_replace("  ", " ", $xcadena); // quito espacios dobles
			$xcadena = str_replace("BILLON DE MILLONES", "BILLON DE", $xcadena); // corrigo la leyenda
			$xcadena = str_replace("BILLONES DE MILLONES", "BILLONES DE", $xcadena); // corrigo la leyenda
			$xcadena = str_replace("DE UN", "UN", $xcadena); // corrigo la leyenda
		} // ENDFOR	($xz)
		return trim($xcadena, "M.N");
	} // END FUNCTION


	function Sufijo($xx){
		// esta funci&#65533;n regresa un subfijo para la cifra
		$xx = trim($xx);
		$xstrlen = strlen($xx);
		if ($xstrlen == 1 || $xstrlen == 2 || $xstrlen == 3)
			$xsub = "";
		//
		if ($xstrlen == 4 || $xstrlen == 5 || $xstrlen == 6)
			$xsub = "MIL";
		//
		return $xsub;
	} // END FUNCTION

	function diaSemana($ano,$mes,$dia)
	{
		// 0->domingo     | 6->sabado
		$dia= date("w",mktime(0, 0, 0, $mes, $dia, $ano));
		return $dia+1;
	}


	function QuitarTildes($cadena) {
		$no_permitidas= array ("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","À","Ã","Ì","Ò","Ù","Ã™","Ã ","Ã¨","Ã¬","Ã²","Ã¹","ç","Ç","Ã¢","ê","Ã®","Ã´","Ã»","Ã‚","ÃŠ","ÃŽ","Ã”","Ã›","ü","Ã¶","Ã–","Ã¯","Ã¤","«","Ò","Ã","Ã„","Ã‹");
		$permitidas= array ("a","e","i","o","u","A","E","I","O","U","n","N","A","E","I","O","U","a","e","i","o","u","c","C","a","e","i","o","u","A","E","I","O","U","u","o","O","i","a","e","U","I","A","E");
		$texto = str_replace($no_permitidas, $permitidas ,$cadena);
		$texto = str_replace(' ', '', $texto);
		return $texto;
	}

	function ConsultorioReservado($grillav,$inicio,$consultorio){
		if(isset($grillav[$inicio.$consultorio])){
			$rta = $this->ConsultorioReservado($grillav,$inicio,$consultorio + 1);
		}else{
			$rta = $consultorio;
		}
		return $rta;
	}

	function BorrarDeArray($array, $deleteIt, $useOldKeys = FALSE){
		$key = array_search($deleteIt,$array,TRUE);
		if($key === FALSE)
			return FALSE;
		unset($array[$key]);
		if(!$useOldKeys)
			$array = array_values($array);
		return $array;
	}

    function obtTurnosOtorgadosTotales($desde, $hasta, $id_usuarios){
		$query_string = $this->querys->dataTurnosOtorgadosTotales($desde, $hasta, $id_usuarios);

		$query = $this->db->consulta($query_string);
        $tot = 0;
        while ($row = $this->db->fetch_array($query)) {
            $tot+= $row['count'];
        }

		$query = $this->db->consulta($query_string);
        $data = "";
        $color = array('#007FA6');
        $i = 0;
        while ($row = $this->db->fetch_array($query)) {
            if ($row['id_usuarios'] == 1) {
                $nombre = $row['nombres']." ".$row['apellidos'];
            } else {
                $nombre = $row['nombres']." ".$row['apellidos'][0].".";
            }
            $style = $color[$i % count($color)];
            $percent = round($row['count'] * 10000 / $tot) / 100;
            $data.= ",['{$nombre}', {$row['count']}, '{$style}', '{$row['count']} ({$percent}%)']\n";
            $i++;
        }
        return array(utf8_encode($data), $i);
    }

    function obtTurnosOtorgadosPorDia($desde, $hasta, $id_usuarios){
		$query_string = $this->querys->dataTurnosOtorgadosPorDia($desde, $hasta, $id_usuarios);

		$query = $this->db->consulta($query_string);
        $data = "";
        $color = array('#007FA6');
        $i = 0;
        while ($row = $this->db->fetch_array($query)) {
            $fecha_alta = implode("/", array_reverse(explode("-", $row['fecha_alta'])));
            $style = $color[$i % count($color)];
            $data.= ",['{$fecha_alta}', {$row['count']}, '{$style}', '{$row['count']}']\n";
            $i++;
        }
        return array(utf8_encode($data), $i);
    }

    function obtTurnosOtorgadosPorOS($desde, $hasta, $id_usuarios){
		$query_string = $this->querys->dataTurnosOtorgadosPorOS($desde, $hasta, $id_usuarios);

		$query = $this->db->consulta($query_string);
        $data = "";
        $color = array('#007FA6');
        $i = 0;
        while ($row = $this->db->fetch_array($query)) {
            $style = $color[$i % count($color)];
            $data.= ",['{$row['abreviacion']}', {$row['count']}, '{$style}', '{$row['count']}']\n";
            $i++;
        }
        return array(utf8_encode($data), $i);
    }

    function obtTurnosOtorgadosPorEST($desde, $hasta, $id_usuarios){
		$query_string = $this->querys->dataTurnosOtorgadosPorEST($desde, $hasta, $id_usuarios);

		$query = $this->db->consulta($query_string);
        $data = "";
        $color = array('#007FA6');
        $i = 0;
        while ($row = $this->db->fetch_array($query)) {
            $style = $color[$i % count($color)];
            $data.= ",['{$row['nombre']}', {$row['count']}, '{$style}', '{$row['count']}']\n";
            $i++;
        }
        return array(utf8_encode($data), $i);
    }

    function obtTurnosOtorgadosPorENC($desde, $hasta, $id_usuarios){
		$query_string = $this->querys->dataTurnosOtorgadosPorENC($desde, $hasta, $id_usuarios);

		$query = $this->db->consulta($query_string);
        $data = "";
        $color = array('#007FA6');
        $i = 0;
        while ($row = $this->db->fetch_array($query)) {
            $style = $color[$i % count($color)];
            $data.= ",['{$row['pregunta']}', {$row['count']}, '{$style}', '{$row['count']}']\n";
            $i++;
        }
        return array(utf8_encode($data), $i);
    }

    function obtTurnosPorMedicos($desde, $hasta, $id_usuarios){
		$query_string = $this->querys->dataTurnosPorMedicos($desde, $hasta, $id_usuarios);
		$query = $this->db->consulta($query_string);
        $data = "";
        $color = array('#007FA6');
        $i = 0;
        while ($row = $this->db->fetch_array($query)) {
            $nombre = $row['apellidos']." ".$row['nombres'][0].".";
            $style = $color[$i % count($color)];
            $data.= ",['{$nombre}', {$row['count']}, '{$style}']\n";
            $i++;
        }
        return array(utf8_encode($data), $i);
    }

    function obtMotivosDeInhabilitaciones(){
		$query_string = $this->querys->obtMotivosDeInhabilitaciones($desde, $hasta, $id_usuarios);
		$query = $this->db->consulta($query_string);
        $data = "";
        while ($row = $this->db->fetch_array($query)) {
            $data.= "
                <option value=\"{$row['id_horarios_inhabilitados_motivos']}\" data-libre=\"{$row['motivo_libre']}\">{$row['motivo_descripcion']}</option>
            ";
        }
        return utf8_encode($data);
    }

}

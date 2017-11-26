<?php
set_time_limit(0);
require_once("../engine/config.php");
require_once("../engine/restringir_acceso.php");
requerir_class("tpl","querys","mysql","estructura");

requerir_class(
    'medicos',
    'medicosext',
    'medicosexp',
    'obras_sociales',
    'obras_sociales_planes',
    'especialidades',
    'estudios',
    'dias_semana',
    'cobros_conceptos',
    'estructura',
    'sectores',
    'subsectores',
    'agendas',
    'agendas_tipos',
    'mantenimientos',
    'mantenimhistoricos',
    'mantenimientosestados',
    'usuarios',
    'encuestas'
);

$tabla = $_GET["tabla"];

requerir_class($tabla);

$clase = ucwords($tabla);
$obj = new $clase();

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Easy set variables
 */

/* Array of database columns which should be read and sent back to DataTables. Use a space where
 * you want to insert a non-database field (for example a counter or static image)
 *
 * id added in first column for editables server support
 */
switch ($tabla){
	case "pacientes":
		$aColumns = array('id_pacientes', 'nro_documento', 'apellidos', 'nombres', 'id_obras_sociales');
	break;
	case "medicos":
		$aColumns = array('id_medicos', 'saludo', 'apellidos', 'nombres', 'nro_documento', 'email', 'telefonos', 'id_sectores', 'id_subsectores', 'interno', 'id_plantas', 'matricula');
	break;
	case "medicosext":
		$aColumns = array('id_medicosext', 'saludo', 'apellidos', 'nombres', 'matricula');
    break;
	case "medicosexp":
		$aColumns = array('mx.id_medicosexp','mx.saludo','mx.apellidos','mx.nombres','turnos_turnos','turnos_sobreturnos','turnos_total','minutos_horario','minutos_turnos','minutos_sobreturnos','minutos_total','horas_horario','horas_turnos','horas_sobreturnos','horas_total');
	break;
	case "especialidades":
		$aColumns = array('id_especialidades', 'nombre');
	break;
	case "estudios":
		$aColumns = array('id_estudios', 'nombre', 'importe', 'arancel', 'requisitos', 'codigopractica');
	break;
	case "obras_sociales":
		$aColumns = array('id_obras_sociales', 'abreviacion', 'nombre'/*, 'importe_consulta'*/);
	break;
	case "obras_sociales_planes":
		$aColumns = array('id_obras_sociales_planes', 'nombre');
	break;
	case "obras_sociales_estudios":
		$aColumns = array('id_obras_sociales_estudios', 'id_obras_sociales', 'nomenclador','id_estudios', 'importe');
	break;
	case "medicos_especialiades":
		$aColumns = array('id_medicos_especialidades', 'id_medicos','id_especialidades', 'duracion_turno');
	break;
	case "medicos_horarios":
		$aColumns = array('id_medicos_horarios', 'id_medicos', 'id_especialidades',  'id_dias_semana', 'id_plantas', 'desde', 'hasta');
	break;
	case 'medicos_estudios':
		$aColumns = array('id_medicos_estudios', 'id_medicos','id_estudios', 'particular');
	break;
	case 'medicos_obras_sociales':
		$aColumns = array('id_medicos_obras_sociales', 'id_medicos', 'nombre','arancel');
	break;
	case "cobros":
		$aColumns = array('id_cobros','fecha', 'hora','id_cobros_conceptos','id_pacientes', 'importe', 'reintegro');
	break;
	case "turnos":
		$aColumns = array('id_turnos','id_medicos','fecha','desde', 'hasta', 'estado');
	break;
	case "sectores":
		$aColumns = array('id_sectores','nombre');
	break;
	case "subsectores":
		$aColumns = array('id_subsectores','id_sectores','nombre');
	break;
	case "agendas":
		$aColumns = array('id_agendas','nombre','apellido','rubro','celular','telefono','direccion','id_agendas_tipos');
	break;
	case "mantenimientos":
    case "mantenimhistoricos":
		$aColumns = array('id_mantenimientos','fecha','id_sectores','solicitador','tarea','especialista','observaciones','id_mantenimientos_estados','id_usuarios');
	break;
	case "encuestas":
		$aColumns = array('er.id_encuestas_respuestas','t.fecha_alta','t.hora_alta','paciente','respuesta1','respuesta2','medico','especialidad');
	break;
	default:
		$aColumns = $obj->NombreColumnas();
}



/* Indexed column (used for fast and accurate table cardinality) */
$sIndexColumn = "id_".$obj->nombre_tabla;

/* DB table to use */
$sTable = $obj->nombre_tabla;
$pfTable = strtoupper($sTable[0]);
$sTableFrom = $sTable." ".$pfTable;

$gaSql['user']       = BD_USUARIO;
$gaSql['password']   = BD_PASS;
$gaSql['db']         = BD_NOMBRE;
$gaSql['server']     = SERVIDOR;


/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * If you just want to use the basic configuration for DataTables with PHP server-side, there is
 * no need to edit below this line
 */

/*
 * MySQL connection
 */
$link =  mysql_connect( $gaSql['server'], $gaSql['user'], $gaSql['password']  ) or
    die( 'Could not open connection to server' );

mysql_select_db( $gaSql['db'], $link ) or
    die( 'Could not select database '. $gaSql['db'] );


/*
 * PAGINADO
 */
$sLimit = "";
if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
{
		$sLimit = "LIMIT ".mysql_real_escape_string( $_GET['iDisplayStart'] ).", ".
				mysql_real_escape_string( $_GET['iDisplayLength'] );
}


/*
 * ORDEN
 */
$sOrder = "";
if ( isset( $_GET['iSortCol_0'] ) )
{
    switch ($tabla) {
        case "medicos_obras_sociales":
            $sTableFrom.= "
                LEFT JOIN obras_sociales AS os
                    ON M.id_obras_sociales = os.id_obras_sociales
            ";
            $sOrder = "ORDER BY nombre";
            break;
        default:
    		$sOrder = "ORDER BY  ";
    		for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
    		{
    				if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
    				{
    						$sOrder .= $aColumns[ intval( $_GET['iSortCol_'.$i] ) ]."
    								".mysql_real_escape_string( $_GET['sSortDir_'.$i] ) .", ";
    				}
    		}
    		$sOrder = substr_replace( $sOrder, "", -2 );
    		if ( $sOrder == "ORDER BY" )
    		{
    				$sOrder = "";
    		}
            break;
      }
}



/*
 * Filtering
 * NOTE this does not match the built-in DataTables filtering which does it
 * word by word on any field. It's possible to do here, but concerned about efficiency
 * on very large tables, and MySQL's regex functionality is very limited
 */

//EN CASO DE QUE SE NECESITE VENGA FILTRADO
switch ($tabla){
	case "medicos_especialidades":
	case "medicos_estudios":
	case "medicos_obras_sociales":
		$id_padre = $_GET["id"];
		$sWhere = "WHERE ( id_medicos = ".$id_padre;
	break;
	case "obras_sociales_estudios":
	case "obras_sociales_planes":
		$id_padre = $_GET["id"];
		$sWhere = "WHERE ( id_obras_sociales = ".$id_padre;
	break;
	case "cobros":
		$id_padre = $_GET["id"];
		$sWhere = "WHERE ( id_pacientes = ".$id_padre." AND reintegro <> 1";
	break;
	case 'turnos':
		$id_padre = $_GET["id"];
		$sWhere = "WHERE ( id_pacientes = ".$id_padre;
	break;
	case "medicos_horarios":
		$id_medico = $_GET["id_medico"];
		$id_especialidad = $_GET["id_especialidad"];
		$sWhere = "WHERE ( id_medicos = ".$id_medico." AND id_especialidades = ".$id_especialidad;
	break;
	default:
		$sWhere = "";
}

if ( isset($_GET['sSearch']) && $_GET['sSearch'] != "" )
{
		$parentesis = true;
		switch ($tabla){
			case "medicos_especialidades":
			case "medicos_estudios":
			case "medicos_obras_sociales":
				$id_padre = $_GET["id"];
				$sWhere = "WHERE id_medicos = ".$id_padre.' AND (';
			break;
			case "obras_sociales_estudios":
			case "obras_sociales_planes":
				$id_padre = $_GET["id"];
				$sWhere = "WHERE id_obras_sociales = ".$id_padre.' AND (';
			break;
			case "cobros":
				$id_padre = $_GET["id"];
				$sWhere = "WHERE id_pacientes = ".$id_padre." AND reintegro <> 1 AND (";
				$parentesis = false;
			break;
			case 'turnos':
				$id_padre = $_GET["id"];
				$sWhere = "WHERE id_pacientes = ".$id_padre.' AND (';
			break;
			case "medicos_horarios":
				$id_medico = $_GET["id_medico"];
				$id_especialidad = $_GET["id_especialidad"];
				$sWhere = "WHERE id_medicos = ".$id_medico." AND id_especialidades = ".$id_especialidad.' AND (';
			break;
			default:
				$sWhere = "WHERE (";
				$parentesis = false;
		}



		for ( $i=0 ; $i<count($aColumns) ; $i++ ){
			switch($tabla){
				case "pacientes":
				case "medicos_obras_sociales":
				case 'medicos_estudios':
				case 'medicos_especialidades':
					switch($aColumns[$i]){
						case "id_especialidades":
							$obj = new Especialidades();
							$query = $obj->RegistroXAtributo("nombre",$_GET['sSearch'],"like");
							//error_log($query);
							$cant = $obj->db->num_rows($query);

							if ($cant > 0){
								$ids = "(";
								$band = 0;
								while ($row = $obj->db->fetch_array($query)){
									$ids .= $row["id_".$obj->nombre_tabla].", ";
									$band = 1;
								}
								$ids = rtrim($ids, ", ");
								$ids = $ids.")";

								if ($band == 1){
									$buscar = $ids;
									$sWhere .= $aColumns[$i]." IN ".$buscar.' OR ';
								}else{
									$buscar = 0;
									$sWhere .= $aColumns[$i]." = ".$buscar.' OR ';
								}
							}
						break;

						case "id_estudios":
							$obj = new Estudios();
							$query = $obj->RegistroXAtributo("nombre",$_GET['sSearch'],"like");

							$cant = $obj->db->num_rows($query);

							if ($cant > 0){
								$ids = "(";
								$band = 0;
								while ($row = $obj->db->fetch_array($query)){
									$ids .= $row["id_".$obj->nombre_tabla].", ";
									$band = 1;
								}
								$ids = rtrim($ids, ", ");
								$ids = $ids.")";

								if ($band == 1){
									$buscar = $ids;
									$sWhere .= $aColumns[$i]." IN ".$buscar.' OR ';
								}else{
									$buscar = 0;
									$sWhere .= $aColumns[$i]." = ".$buscar.' OR ';
								}
							}
						break;
						case "id_obras_sociales":
							$obj = new Obras_sociales();
							$query = $obj->RegistroXAtributo("nombre",$_GET['sSearch'],"like");

							$cant = $obj->db->num_rows($query);

							//if ($cant > 0){
								$ids = "(";
								$band = 0;
								while ($row = $obj->db->fetch_array($query)){
									$ids .= $row["id_".$obj->nombre_tabla].", ";
									$band = 1;
								}
								$ids = rtrim($ids, ", ");
								$ids = $ids.")";

								if ($band == 1){
									$buscar = $ids;
									$sWhere .= $aColumns[$i]." IN ".$buscar.' OR ';
								}else{
									$buscar = 0;
									$sWhere .= $aColumns[$i]." = ".$buscar. ' OR ';
								}
							//}
						break;
						case "id_obras_sociales_planes":
							$obj = new Obras_sociales_planes();
							$query = $obj->RegistroXAtributo("nombre",$_GET['sSearch'],"like");

							$cant = $obj->db->num_rows($query);

							if ($cant > 0){
								$ids = "(";
								$band = 0;
								while ($row = $obj->db->fetch_array($query)){
									$ids .= $row["id_".$obj->nombre_tabla].", ";
									$band = 1;
								}
								$ids = rtrim($ids, ", ");
								$ids = $ids.")";

								if ($band == 1){
									$buscar = $ids;
									$sWhere .= $aColumns[$i]." IN ".$buscar;
								}else{
									$buscar = 0;
									$sWhere .= $aColumns[$i]." = ".$buscar;
								}
							}
						break;
						case "id_medicos_obras_sociales":
						case "id_medicos_estudios":
						case "arancel":
							$sWhere .= "";
							$parentesis = false;
						break;
						case "id_medicos":
								$sWhere .= "";
						break;
						default:
							$sWhere .= $aColumns[$i]." LIKE '%".mysql_real_escape_string( utf8_decode($_GET['sSearch']) )."%' OR ";
					}
				break;
				case 'medicos_horarios':
					switch($aColumns[$i]){
						case "id_dias_semana":
							$obj = new Dias_semana();
							$query = $obj->RegistroXAtributo("nombre",$_GET['sSearch'],"like");

							$cant = $obj->db->num_rows($query);

							if ($cant > 0){
								$ids = "(";
								$band = 0;
								while ($row = $obj->db->fetch_array($query)){
									$ids .= $row["id_".$obj->nombre_tabla].", ";
									$band = 1;
								}
								$ids = rtrim($ids, ", ");
								$ids = $ids.")";

								if ($band == 1){
									$buscar = $ids;
									$sWhere .= $aColumns[$i]." IN ".$buscar.' OR ';
								}else{
									$buscar = 0;
									$sWhere .= $aColumns[$i]." = ".$buscar.' OR ';
								}
							}
						break;
						case "id_plantas":
							$obj = new Plantas();
							$query = $obj->RegistroXAtributo("nombre",$_GET['sSearch'],"like");

							$cant = $obj->db->num_rows($query);

							if ($cant > 0){
								$ids = "(";
								$band = 0;
								while ($row = $obj->db->fetch_array($query)){
									$ids .= $row["id_".$obj->nombre_tabla].", ";
									$band = 1;
								}
								$ids = rtrim($ids, ", ");
								$ids = $ids.")";

								if ($band == 1){
									$buscar = $ids;
									$sWhere .= $aColumns[$i]." IN ".$buscar.' OR ';
								}else{
									$buscar = 0;
									$sWhere .= $aColumns[$i]." = ".$buscar.' OR ';
								}
							}
						break;
						case "id_medicos":
								$sWhere .= "";
						break;
						default:
							$sWhere .= $aColumns[$i]." LIKE '%".mysql_real_escape_string( utf8_decode($_GET['sSearch']) )."%' OR ";
					}
				break;
				case "medicos":
					switch($aColumns[$i]){
						case "id_sectores":
							$obj = new Sectores();
							$query = $obj->RegistroXAtributo("nombre",$_GET['sSearch'],"like");

							$cant = $obj->db->num_rows($query);

							if ($cant > 0){
								$ids = "(";
								$band = 0;
								while ($row = $obj->db->fetch_array($query)){
									$ids .= $row["id_".$obj->nombre_tabla].", ";
									$band = 1;
								}
								$ids = rtrim($ids, ", ");
								$ids = $ids.")";

								if ($band == 1){
									$buscar = $ids;
									$sWhere .= $aColumns[$i]." IN ".$buscar.' OR ';
								}else{
									$buscar = 0;
									$sWhere .= $aColumns[$i]." = ".$buscar.' OR ';
								}
							}
						break;
						case "id_subsectores":
							$obj = new Subsectores();
							$query = $obj->RegistroXAtributo("nombre",$_GET['sSearch'],"like");

							$cant = $obj->db->num_rows($query);

							if ($cant > 0){
								$ids = "(";
								$band = 0;
								while ($row = $obj->db->fetch_array($query)){
									$ids .= $row["id_".$obj->nombre_tabla].", ";
									$band = 1;
								}
								$ids = rtrim($ids, ", ");
								$ids = $ids.")";

								if ($band == 1){
									$buscar = $ids;
									$sWhere .= $aColumns[$i]." IN ".$buscar.' OR ';
								}else{
									$buscar = 0;
									$sWhere .= $aColumns[$i]." = ".$buscar.' OR ';
								}
							}
						break;
						case "id_plantas":
							$obj = new Plantas();
							$query = $obj->RegistroXAtributo("nombre",$_GET['sSearch'],"like");

							$cant = $obj->db->num_rows($query);

							if ($cant > 0){
								$ids = "(";
								$band = 0;
								while ($row = $obj->db->fetch_array($query)){
									$ids .= $row["id_".$obj->nombre_tabla].", ";
									$band = 1;
								}
								$ids = rtrim($ids, ", ");
								$ids = $ids.")";

								if ($band == 1){
									$buscar = $ids;
									$sWhere .= $aColumns[$i]." IN ".$buscar.' OR ';
								}else{
									$buscar = 0;
									$sWhere .= $aColumns[$i]." = ".$buscar.' OR ';
								}
							}
						break;
						default:
							$buscar = utf8_decode($_GET['sSearch']);
							$sWhere .= $aColumns[$i]." LIKE '%".$buscar."%' OR ";
					}
				break;
				case 'turnos':
					switch($aColumns[$i]){
						case "id_medicos":
							$obj = new Medicos();
							$query = $obj->RegistroXAtributo("apellidos",$_GET['sSearch'],"like");

							$ids = "(";
							$band = 0;
							while ($row = $obj->db->fetch_array($query)){
								$ids .= $row["id_".$obj->nombre_tabla].", ";
								$band = 1;
							}
							$ids = trim($ids, ", ");
							$ids .= ")";


							if ($band == 1){
								$buscar = $ids;
								$sWhere .= $aColumns[$i]." IN ".$buscar.' OR ';
							}else{
								$buscar = 0;
								$sWhere .= $aColumns[$i]." = ".$buscar.' OR ';
							}
						break;
						default:
							$buscar = utf8_decode($_GET['sSearch']);
							$sWhere .= $aColumns[$i]." LIKE '%".$buscar."%' OR ";
					}
				break;
				case 'cobros':
					case "id_cobros_conceptos":
							$obj = new Cobros_conceptos();
							$query = $obj->RegistroXAtributo("nombre",$_GET['sSearch'],"like");

							$cant = $obj->db->num_rows($query);

							if ($cant > 0){
								$ids = "(";
								$band = 0;
								while ($row = $obj->db->fetch_array($query)){
									$ids .= $row["id_".$obj->nombre_tabla].", ";
									$band = 1;
								}
								$ids = rtrim($ids, ", ");
								$ids = $ids.")";

								if ($band == 1){
									$buscar = $ids;
									$sWhere .= $aColumns[$i]." IN ".$buscar.' OR ';
								}else{
									$buscar = 0;
									$sWhere .= $aColumns[$i]." = ".$buscar.' OR ';
								}
							}
						break;
				break;
                case 'subsectores':
					switch($aColumns[$i]){
						case "id_sectores":
							$obj = new Sectores();
							$query = $obj->RegistroXAtributo("nombre",$_GET['sSearch'],"like");

							$cant = $obj->db->num_rows($query);

							if ($cant > 0){
								$ids = "(";
								$band = 0;
								while ($row = $obj->db->fetch_array($query)){
									$ids .= $row["id_".$obj->nombre_tabla].", ";
									$band = 1;
								}
								$ids = rtrim($ids, ", ");
								$ids = $ids.")";

								if ($band == 1){
									$buscar = $ids;
									$sWhere .= $aColumns[$i]." IN ".$buscar.' OR ';
								}else{
									$buscar = 0;
									$sWhere .= $aColumns[$i]." = ".$buscar.' OR ';
								}
							}
						break;
                    }
                break;
                case 'mantenimientos':
					switch($aColumns[$i]){
						case "id_sectores":
							$obj = new Sectores();
							$query = $obj->RegistroXAtributo("nombre",$_GET['sSearch'],"like");

							$cant = $obj->db->num_rows($query);

							if ($cant > 0){
								$ids = "(";
								$band = 0;
								while ($row = $obj->db->fetch_array($query)){
									$ids .= $row["id_".$obj->nombre_tabla].", ";
									$band = 1;
								}
								$ids = rtrim($ids, ", ");
								$ids = $ids.")";

								if ($band == 1){
									$buscar = $ids;
									$sWhere .= $aColumns[$i]." IN ".$buscar.' OR ';
								}else{
									$buscar = 0;
									$sWhere .= $aColumns[$i]." = ".$buscar.' OR ';
								}
							}
						break;
						case "id_mantenimientos_estados":
							$obj = new Mantenimientosestados();
							$query = $obj->RegistroXAtributo("nombre",$_GET['sSearch'],"like");

							$cant = $obj->db->num_rows($query);

							if ($cant > 0){
								$ids = "(";
								$band = 0;
								while ($row = $obj->db->fetch_array($query)){
									$ids .= $row["id_".$obj->nombre_tabla].", ";
									$band = 1;
								}
								$ids = rtrim($ids, ", ");
								$ids = $ids.")";

								if ($band == 1){
									$buscar = $ids;
									$sWhere .= $aColumns[$i]." IN ".$buscar.' OR ';
								}else{
									$buscar = 0;
									$sWhere .= $aColumns[$i]." = ".$buscar.' OR ';
								}
							}
						break;
						case "id_usuarios":
							$obj = new Usuarios();
							$query = $obj->RegistroXAtributo("usuario",$_GET['sSearch'],"like");

							$cant = $obj->db->num_rows($query);

							if ($cant > 0){
								$ids = "(";
								$band = 0;
								while ($row = $obj->db->fetch_array($query)){
									$ids .= $row["id_".$obj->nombre_tabla].", ";
									$band = 1;
								}
								$ids = rtrim($ids, ", ");
								$ids = $ids.")";

								if ($band == 1){
									$buscar = $ids;
									$sWhere .= $aColumns[$i]." IN ".$buscar.' OR ';
								}else{
									$buscar = 0;
									$sWhere .= $aColumns[$i]." = ".$buscar.' OR ';
								}
							}
						break;
                    }
                break;
                case 'agendas_tipos':
					switch($aColumns[$i]){
						case "id_agendas":
							$obj = new Agendas();
							$query = $obj->RegistroXAtributo("nombre",$_GET['sSearch'],"like");

							$cant = $obj->db->num_rows($query);

							if ($cant > 0){
								$ids = "(";
								$band = 0;
								while ($row = $obj->db->fetch_array($query)){
									$ids .= $row["id_".$obj->nombre_tabla].", ";
									$band = 1;
								}
								$ids = rtrim($ids, ", ");
								$ids = $ids.")";

								if ($band == 1){
									$buscar = $ids;
									$sWhere .= $aColumns[$i]." IN ".$buscar.' OR ';
								}else{
									$buscar = 0;
									$sWhere .= $aColumns[$i]." = ".$buscar.' OR ';
								}
							}
						break;
                    }
                break;
                case 'encuestas':
    				switch($aColumns[$i]){
    					case "paciente":
            				$buscar = $_GET['sSearch'];
            				$sWhere .= "CONCAT(TRIM(p.apellidos), ', ', TRIM(p.nombres)) LIKE '%".$buscar."%' OR ";
                        break;
    					case "respuesta1":
            				$buscar = $_GET['sSearch'];
            				$sWhere .= "ep.pregunta LIKE '%".$buscar."%' OR ";
                        break;
    					case "respuesta2":
            				$buscar = $_GET['sSearch'];
            				$sWhere .= "era.respuesta LIKE '%".$buscar."%' OR ";
                        break;
    					case "medico":
            				$buscar = $_GET['sSearch'];
            				$sWhere .= "CONCAT(m.saludo, ' ', m.apellidos, ', ', m.nombres) LIKE '%".$buscar."%' OR ";
                        break;
    					case "especialidad":
            				$buscar = $_GET['sSearch'];
            				$sWhere .= "e.nombre LIKE '%".$buscar."%' OR ";
                        break;
                        default:
            				$buscar = $_GET['sSearch'];
            				$sWhere .= $aColumns[$i]." LIKE '%".$buscar."%' OR ";
                        break;
                    }
                break;
				default:
					switch($aColumns[$i]){
						case 'id_especialidades':

						break;
						default:
							$buscar = $_GET['sSearch'];
							$sWhere .= $aColumns[$i]." LIKE '%".$buscar."%' OR ";
					}

			}

		}

		$sWhere = trim($sWhere, " OR ");
		$sWhere = trim($sWhere, " AND ");


		if ($parentesis)
			$sWhere .= ')';

}

//error_log($sWhere);

/* Individual column filtering */
for ( $i=0 ; $i<count($aColumns) ; $i++ ){

	if ( isset($_GET['bSearchable_'.$i]) && $_GET['bSearchable_'.$i] == "true" && $_GET['sSearch_'.$i] != '' ){
		if ( $sWhere == "" ){
				$sWhere = "WHERE (";
		}else{
				$sWhere .= " AND ";
		}

		switch($tabla){
			case "pacientes":
			case "medicos_obras_sociales":
			case 'medicos_estudios':
			case 'medicos_especialidades':

				//SE OCULTA LA PRIMERA COLUMNA, POR TAL MOTIVO PARA BUSCAR SE SUMA 1 AL NRO DE COLUMNA: $aColumns[$i + 1]

				switch($aColumns[$i + 1]){
					case "id_estudios":
						$obj = new Estudios();
						$query = $obj->RegistroXAtributo("nombre",$_GET['sSearch_'.$i],"like");

						$ids = "(";
						$band = 0;
						while ($row = $obj->db->fetch_array($query)){
							$ids .= $row["id_".$obj->nombre_tabla].", ";
							$band = 1;
						}
						$ids = trim($ids, ", ");
						$ids .= ")";


						if ($band == 1){
							$buscar = $ids;
							$sWhere .= $aColumns[$i + 1]." IN ".$buscar;
						}else{
							$buscar = 0;
							$sWhere .= $aColumns[$i + 1]." = ".$buscar;
						}
					break;
					case "id_especialidades":
						$obj = new Especialidades();
						$query = $obj->RegistroXAtributo("nombre",$_GET['sSearch_'.$i],"like");

						$ids = "(";
						$band = 0;
						while ($row = $obj->db->fetch_array($query)){
							$ids .= $row["id_".$obj->nombre_tabla].", ";
							$band = 1;
						}
						$ids = trim($ids, ", ");
						$ids .= ")";


						if ($band == 1){
							$buscar = $ids;
							$sWhere .= $aColumns[$i + 1]." IN ".$buscar;
						}else{
							$buscar = 0;
							$sWhere .= $aColumns[$i + 1]." = ".$buscar;
						}
					break;
					case "id_obras_sociales":
						$obj = new Obras_sociales();
						$query = $obj->RegistroXAtributo("nombre",$_GET['sSearch_'.$i],"like");

						$ids = "(";
						$band = 0;
						while ($row = $obj->db->fetch_array($query)){
							$ids .= $row["id_".$obj->nombre_tabla].", ";
							$band = 1;
						}
						$ids = trim($ids, ", ");
						$ids .= ")";


						if ($band == 1){
							$buscar = $ids;
							$sWhere .= $aColumns[$i + 1]." IN ".$buscar;
						}else{
							$buscar = 0;
							$sWhere .= $aColumns[$i + 1]." = ".$buscar;
						}
					break;
					case "id_obras_sociales_planes":
						$obj = new Obras_sociales_planes();
						$query = $obj->RegistroXAtributo("nombre",$_GET['sSearch_'.$i],"like");

						$ids = "(";
						$band = 0;
						while ($row = $obj->db->fetch_array($query)){
							$ids .= $row["id_".$obj->nombre_tabla].", ";
							$band = 1;
						}
						$ids = trim($ids, ", ");
						$ids .= ")";


						if ($band == 1){
							$buscar = $ids;
							$sWhere .= $aColumns[$i + 1]." IN ".$buscar;
						}else{
							$buscar = 0;
							$sWhere .= $aColumns[$i + 1]." = ".$buscar;
						}
					break;
					case "id_medicos":
						$sWhere .= "";
					break;
					default:
						$buscar = utf8_decode($_GET['sSearch_'.$i]);
						$sWhere .= $aColumns[$i + 1]." LIKE '%".$buscar."%' ";
				}

			break;
			case 'medicos_horarios':
				switch($aColumns[$i + 3]){

					case "id_dias_semana":

						$obj = new Dias_semana();
						$query = $obj->RegistroXAtributo("nombre",$_GET['sSearch_'.$i],"like");

						$ids = "(";
						$band = 0;
						while ($row = $obj->db->fetch_array($query)){
							$ids .= $row["id_".$obj->nombre_tabla].", ";
							$band = 1;
						}
						$ids = trim($ids, ", ");
						$ids .= ")";


						if ($band == 1){
							$buscar = $ids;
							$sWhere .= $aColumns[$i + 3]." IN ".$buscar;
						}else{
							$buscar = 0;
							$sWhere .= $aColumns[$i + 3]." = ".$buscar;
						}
					break;
					case "id_plantas":

						$obj = new Plantas();
						$query = $obj->RegistroXAtributo("nombre",$_GET['sSearch_'.$i],"like");

						$ids = "(";
						$band = 0;
						while ($row = $obj->db->fetch_array($query)){
							$ids .= $row["id_".$obj->nombre_tabla].", ";
							$band = 1;
						}
						$ids = trim($ids, ", ");
						$ids .= ")";


						if ($band == 1){
							$buscar = $ids;
							$sWhere .= $aColumns[$i + 3]." IN ".$buscar;
						}else{
							$buscar = 0;
							$sWhere .= $aColumns[$i + 3]." = ".$buscar;
						}
					break;
					case 'id_medicos':
					case 'id_especialidades':
						$sWhere .= "";
					break;
					default:
						$buscar = utf8_decode($_GET['sSearch_'.$i]);
						$sWhere .= $aColumns[$i + 3]." LIKE '%".$buscar."%' ";
				}
			break;
			case 'obras_sociales_estudios':
				switch($aColumns[$i + 2]){
					case "id_estudios":
						$obj = new Estudios();
						$query = $obj->RegistroXAtributo("nombre",$_GET['sSearch_'.$i],"like");

						$ids = "(";
						$band = 0;
						while ($row = $obj->db->fetch_array($query)){
							$ids .= $row["id_".$obj->nombre_tabla].", ";
							$band = 1;
						}
						$ids = trim($ids, ", ");
						$ids .= ")";


						if ($band == 1){
							$buscar = $ids;
							$sWhere .= $aColumns[$i + 2]." IN ".$buscar;
						}else{
							$buscar = 0;
							$sWhere .= $aColumns[$i + 2]." = ".$buscar;
						}
					break;
					default:
						$buscar = utf8_decode($_GET['sSearch_'.$i]);
						$sWhere .= $aColumns[$i + 2]." LIKE '%".$buscar."%' ";
				}
			break;
			case 'turnos':
				switch($aColumns[$i + 1]){
					case "id_medicos":
						$obj = new Medicos();
						$query = $obj->RegistroXAtributo("apellidos",$_GET['sSearch_'.$i],"like");

						$ids = "(";
						$band = 0;
						while ($row = $obj->db->fetch_array($query)){
							$ids .= $row["id_".$obj->nombre_tabla].", ";
							$band = 1;
						}
						$ids = trim($ids, ", ");
						$ids .= ")";


						if ($band == 1){
							$buscar = $ids;
							$sWhere .= $aColumns[$i + 1]." IN ".$buscar;
						}else{
							$buscar = 0;
							$sWhere .= $aColumns[$i + 1]." = ".$buscar;
						}
					break;
					case 'fecha':
						$obj_estructura = new Estructura();
						$buscar = $obj_estructura->cambiaf_a_mysql($_GET['sSearch_'.$i]);
						$sWhere .= $aColumns[$i + 1]." LIKE '%".$buscar."%' ";
					break;
					default:
						$buscar = utf8_decode($_GET['sSearch_'.$i]);
						$sWhere .= $aColumns[$i + 1]." LIKE '%".$buscar."%' ";
				}
			break;
			case 'cobros':
				switch($aColumns[$i + 1]){
					case "id_cobros_conceptos":
						$obj = new Cobros_conceptos();
						$query = $obj->RegistroXAtributo("nombre",$_GET['sSearch'],"like");

						$cant = $obj->db->num_rows($query);

						if ($cant > 0){
							$ids = "(";
							$band = 0;
							while ($row = $obj->db->fetch_array($query)){
								$ids .= $row["id_".$obj->nombre_tabla].", ";
								$band = 1;
							}
							$ids = rtrim($ids, ", ");
							$ids = $ids.")";

							if ($band == 1){
								$buscar = $ids;
								$sWhere .= $aColumns[$i + 1]." IN ".$buscar.' OR ';
							}else{
								$buscar = 0;
								$sWhere .= $aColumns[$i + 1]." = ".$buscar.' OR ';
							}
						}
					break;
					default:
						$buscar = utf8_decode($_GET['sSearch_'.$i]);
						$sWhere .= $aColumns[$i + 1]." LIKE '%".$buscar."%' ";
				}
			break;
			case 'mantenimientos':
			case 'mantenimhistoricos':
				switch($aColumns[$i]){
					case "id_sectores":
						$obj = new Sectores();
						$query = $obj->RegistroXAtributo("nombre",$_GET['sSearch_'.$i],"like");

						$ids = "(";
						$band = 0;
						while ($row = $obj->db->fetch_array($query)){
							$ids .= $row["id_".$obj->nombre_tabla].", ";
							$band = 1;
						}
						$ids = trim($ids, ", ");
						$ids .= ")";


						if ($band == 1){
							$buscar = $ids;
							$sWhere .= $aColumns[$i]." IN ".$buscar;
						}else{
							$buscar = 0;
							$sWhere .= $aColumns[$i]." = ".$buscar;
						}
					break;
					case "id_mantenimientos_estados":
						$obj = new Mantenimientosestados();
						$query = $obj->RegistroXAtributo("nombre",$_GET['sSearch_'.$i],"like");

						$ids = "(";
						$band = 0;
						while ($row = $obj->db->fetch_array($query)){
							$ids .= $row["id_".$obj->nombre_tabla].", ";
							$band = 1;
						}
						$ids = trim($ids, ", ");
						$ids .= ")";


						if ($band == 1){
							$buscar = $ids;
							$sWhere .= $aColumns[$i]." IN ".$buscar;
						}else{
							$buscar = 0;
							$sWhere .= $aColumns[$i]." = ".$buscar;
						}
					break;
					case "id_usuarios":
						$obj = new Usuarios();
						$query = $obj->RegistroXAtributo("usuario",$_GET['sSearch_'.$i],"like");

						$ids = "(";
						$band = 0;
						while ($row = $obj->db->fetch_array($query)){
							$ids .= $row["id_".$obj->nombre_tabla].", ";
							$band = 1;
						}
						$ids = trim($ids, ", ");
						$ids .= ")";


						if ($band == 1){
							$buscar = $ids;
							$sWhere .= $aColumns[$i]." IN ".$buscar;
						}else{
							$buscar = 0;
							$sWhere .= $aColumns[$i]." = ".$buscar;
						}
					break;
					default:
						$buscar = utf8_decode($_GET['sSearch_'.$i]);
						$sWhere .= $aColumns[$i]." LIKE '%".$buscar."%' ";
				}
			break;
            case 'encuestas':
				switch($aColumns[$i]){
					case "paciente":
        				$buscar = $_GET['sSearch_'.$i];
        				$sWhere .= "CONCAT(TRIM(p.apellidos), ', ', TRIM(p.nombres)) LIKE '%".$buscar."%' ";
                    break;
					case "respuesta1":
        				$buscar = $_GET['sSearch_'.$i];
        				$sWhere .= "ep.pregunta LIKE '%".$buscar."%' ";
                    break;
					case "respuesta2":
        				$buscar = $_GET['sSearch_'.$i];
        				$sWhere .= "era.respuesta LIKE '%".$buscar."%' ";
                    break;
					case "medico":
        				$buscar = $_GET['sSearch_'.$i];
        				$sWhere .= "CONCAT(m.saludo, ' ', m.apellidos, ', ', m.nombres) LIKE '%".$buscar."%' ";
                    break;
					case "especialidad":
        				$buscar = $_GET['sSearch_'.$i];
        				$sWhere .= "e.nombre LIKE '%".$buscar."%' ";
                    break;
                    default:
        				$buscar = $_GET['sSearch_'.$i];
        				$sWhere .= $aColumns[$i]." LIKE '%".$buscar."%' ";
                    break;
                }
            break;
			default:
				$buscar = $_GET['sSearch_'.$i];
				$sWhere .= $aColumns[$i]." LIKE '%".$buscar."%' ";
		}
	}
}

$sWhere = trim($sWhere, " AND ");
$sWhere = trim($sWhere, " OR ");

if ( $sWhere == "" ){
    if (
        $tabla != 'sectores' and
        $tabla != 'subsectores' and
        $tabla != 'medicosexp' and
        $tabla != 'encuestas'
    ) {
    		$sWhere = "WHERE $pfTable.estado = 1";
    }
} else {
    if (
        $tabla != 'sectores' and
        $tabla != 'subsectores' and
        $tabla != 'medicosexp' and
        $tabla != 'encuestas'
    ) {
    	$sWhere = $sWhere.") AND $pfTable.estado = 1";
    } else {
    	$sWhere = $sWhere.")";
    }
}


/*
 * SQL queries
 * Get data to display
 */
switch ($tabla) {
    case 'medicosexp':
        $mes = date("Y-m-", strtotime("-1 month"));
        $sQuery = <<<SQL
            SELECT
                SQL_CALC_FOUND_ROWS
                *
            FROM
                (
                    SELECT `m`.`id_medicos` AS `id_medicosexp`,
                           `m`.`saludo` AS `saludo`,
                           `m`.`apellidos` AS `apellidos`,
                           `m`.`nombres` AS `nombres`,
                           `ty`.`turnos` AS `turnos_turnos`,
                           (`tx`.`turnos` - `ty`.`turnos`) AS `turnos_sobreturnos`,
                           `tx`.`turnos` AS `turnos_total`,
                           `ty`.`minutos` AS `minutos_turnos`,
                           (`tx`.`minutos` - `ty`.`minutos`) AS `minutos_sobreturnos`,
                           `tx`.`minutos` AS `minutos_total`,
                           `ty`.`horas` AS `horas_turnos`,
                           (`tx`.`horas` - `ty`.`horas`) AS `horas_sobreturnos`,
                           `tx`.`horas` AS `horas_total`,
                           `my`.`minutos_horario`,
                           `my`.`horas_horario`,
                           `m`.`estado` AS `estado`
                    FROM (((
                              (SELECT `t`.`id_medicos` AS `id_medicos`,
                                      count(`t`.`id_turnos`) AS `turnos`,
                                      floor(sum((timestampdiff(SECOND,CONCAT(`t`.`fecha`,' ',`t`.`desde`),CONCAT(`t`.`fecha`,' ',`t`.`hasta`)) / 60))) AS `minutos`,
                                      floor(sum((timestampdiff(SECOND,CONCAT(`t`.`fecha`,' ',`t`.`desde`),CONCAT(`t`.`fecha`,' ',`t`.`hasta`)) / 3600))) AS `horas`
                               FROM `turnos` `t`
                               WHERE ((`t`.`fecha` LIKE '{$mes}%')
                                      AND (`t`.`estado` = 1)
                                      AND (`t`.`id_turnos_estados` IN (1,
                                                                       2,
                                                                       7)))
                               GROUP BY `t`.`id_medicos`)) `tx`
                           JOIN
                             (SELECT `t`.`id_medicos` AS `id_medicos`,
                                     count(`t`.`id_turnos`) AS `turnos`,
                                     floor(sum((timestampdiff(SECOND,CONCAT(`t`.`fecha`,' ',`t`.`desde`),CONCAT(`t`.`fecha`,' ',`t`.`hasta`)) / 60))) AS `minutos`,
                                     floor(sum((timestampdiff(SECOND,CONCAT(`t`.`fecha`,' ',`t`.`desde`),CONCAT(`t`.`fecha`,' ',`t`.`hasta`)) / 3600))) AS `horas`
                              FROM
                                (SELECT `tt`.`id_turnos` AS `id_turnos`,
                                        `tt`.`id_medicos` AS `id_medicos`,
                                        `tt`.`fecha` AS `fecha`,
                                        `tt`.`desde` AS `desde`,
                                        `tt`.`hasta` AS `hasta`,
                                        `tt`.`estado` AS `estado`
                                 FROM (`turnos` `tt`
                                       JOIN `medicos_horarios` `mh` on(((`tt`.`id_medicos` = `mh`.`id_medicos`)
                                                                        AND (`mh`.`estado` = 1)
                                                                        AND (dayofweek(`tt`.`fecha`) = `mh`.`id_dias_semana`)
                                                                        AND (`tt`.`desde` BETWEEN `mh`.`desde` AND `mh`.`hasta`))))
                                 WHERE ((`tt`.`fecha` LIKE '{$mes}%')
                                        AND (`tt`.`estado` = 1)
                                        AND (`tt`.`id_turnos_estados` IN (1,
                                                                          2,
                                                                          7)))
                                 GROUP BY `tt`.`id_turnos`) `t`
                              GROUP BY `t`.`id_medicos`
                              ORDER BY `minutos` DESC) `ty` on((`tx`.`id_medicos` = `ty`.`id_medicos`)))
                          JOIN `medicos` `m` on(`m`.`id_medicos` = `tx`.`id_medicos` AND `m`.`estado` = 1))
                    JOIN
                      (
                        SELECT
                        		`mh`.`id_medicos`,
                        		floor(sum(timestampdiff(SECOND,CONCAT('2017-10-01 ',`mh`.`desde`),CONCAT('2017-10-01 ',`mh`.`hasta`)) / 60) * truncate((datediff(STR_TO_DATE('20171130', '%Y%m%d'),STR_TO_DATE('20171101', '%Y%m%d')) - Weekday(date_add(STR_TO_DATE('20171130', '%Y%m%d'), interval(-`mh`.`id_dias_semana` + 1)day)) + 7) / 7, 0)) AS `minutos_horario`,
                        		floor(sum(timestampdiff(SECOND,CONCAT('2017-10-01 ',`mh`.`desde`),CONCAT('2017-10-01 ',`mh`.`hasta`)) / 3600) * truncate((datediff(STR_TO_DATE('20171130', '%Y%m%d'),STR_TO_DATE('20171101', '%Y%m%d')) - Weekday(date_add(STR_TO_DATE('20171130', '%Y%m%d'), interval(-`mh`.`id_dias_semana` + 1)day)) + 7) / 7, 0)) AS `horas_horario`
                        FROM `medicos_horarios` `mh`
                        WHERE
                        		`mh`.`estado` = 1
                        GROUP BY
                        	`mh`.`id_medicos`
                      ) AS `my`
                      ON
                        `m`.`id_medicos` = `my`.`id_medicos`
                ) AS `mx`
        		$sWhere
        		$sOrder
        		$sLimit
SQL;
        break;
    case 'encuestas':
        $sQuery = <<<SQL
            SELECT
                SQL_CALC_FOUND_ROWS
                er.id_encuestas_respuestas,
                t.fecha_alta,
                t.hora_alta,
                CONCAT(TRIM(p.apellidos), ', ', TRIM(p.nombres)) AS paciente,
                ep.pregunta AS respuesta1,
                era.respuesta AS respuesta2,
                CONCAT(m.saludo, ' ', m.apellidos, ', ', m.nombres) AS medico,
                e.nombre AS especialidad,
                t.id_turnos,
                p.id_pacientes
            FROM encuestas_respuestas AS er
            INNER JOIN
            	encuestas_preguntas AS ep
            	ON er.id_encuestas_preguntas = ep.id_encuestas_preguntas
            LEFT JOIN
            	encuestas_respuestas_abiertas AS era
            	ON era.id_encuestas_respuestas = er.id_encuestas_respuestas
            LEFT JOIN
            	turnos AS t
            	ON er.id_turnos = t.id_turnos
            LEFT JOIN
            	pacientes AS p
            	ON t.id_pacientes = p.id_pacientes
            LEFT JOIN
            	medicos AS m
            	ON t.id_medicos = m.id_medicos
            LEFT JOIN
            	especialidades AS e
            	ON t.id_especialidades = e.id_especialidades
    		$sWhere
    		$sOrder
    		$sLimit
SQL;
        break;
    default:
        $sQuery = "
        		SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns))."
        		FROM   $sTableFrom
        		$sWhere
        		$sOrder
        		$sLimit
        ";
        break;
}

#print "<pre>{$sWhere}</pre>";
#print "<pre>{$sOrder}</pre>";
#print "<pre>{$sLimit}</pre>";
#print "<pre>{$sQuery}</pre>";
//error_log($sQuery);

$rResult = mysql_query( $sQuery, $link ) or die(mysql_error());

/* Data set length after filtering */
$sQuery = "
		SELECT FOUND_ROWS()
";

$rResultFilterTotal = mysql_query( $sQuery, $link ) or die(mysql_error());
$aResultFilterTotal = mysql_fetch_array($rResultFilterTotal);
$iFilteredTotal = $aResultFilterTotal[0];

/* Total data set length */
switch ($tabla) {
    case 'medicosexp':
        $mes = date("Y-m-", strtotime("-1 month"));
        $sQuery = <<<SQL
            SELECT COUNT(`t`.`id_medicos`)
            FROM `turnos` `t`
            WHERE ((`t`.`fecha` LIKE '{$mes}%')
                  AND (`t`.`estado` = 1)
                  AND (`t`.`id_turnos_estados` IN (1,
                                                   2,
                                                   7)))
            GROUP BY `t`.`id_medicos`
SQL;
        break;
    case 'encuestas':
        $sQuery = <<<SQL
            SELECT COUNT(id_encuestas_respuestas)
            FROM encuestas_respuestas
SQL;
        break;
    default:
        $sQuery = "
    		SELECT COUNT(".$sIndexColumn.")
    		FROM   $sTableFrom
    ";
        break;
}

$rResultTotal = mysql_query( $sQuery, $link ) or die(mysql_error());
$aResultTotal = mysql_fetch_array($rResultTotal);
$iTotal = $aResultTotal[0];


/*
 * Output
 */

if (isset($_GET['sEcho'])){
	$sEcho = $_GET['sEcho'];
}else{
	$sEcho = "";
}

$output = array(
		"sEcho" => intval($sEcho),
		"iTotalRecords" => $iTotal,
		"iTotalDisplayRecords" => $iFilteredTotal,
		"aaData" => array()
);

$cant_registros = $obj->db->num_rows($rResult);

if ($cant_registros != 0){

	while ( $aRow = mysql_fetch_array( $rResult ) )
	{
			$row = array();
			for ($i=0; $i<count($aColumns); $i++){
				if ( $aColumns[$i] == "id_medicos" ){
					$obj_medico = new Medicos($aRow[$aColumns[$i]]);
					$medico = $obj_medico->apellidos.', '.$obj_medico->nombres;
				}

				if ( $aColumns[$i] == "id_obras_sociales" ){
					$obj_obra_social = new Obras_sociales($aRow[$aColumns[$i]]);
					$obra_social = $obj_obra_social->abreviacion;
				}

				if ( $aColumns[$i] == "id_obras_sociales_planes" ){
					$obj_obra_social_plan = new Obras_sociales_planes($aRow[$aColumns[$i]]);
					if ($obj_obra_social_plan->nombre != 'Sin Definir')
						$obra_social_plan = $obj_obra_social_plan->nombre;
					else
						$obra_social_plan = '-';
				}

				if ( $aColumns[$i] == "id_especialidades" ){
					$obj_especialidades = new Especialidades($aRow[$aColumns[$i]]);
					$especialidad = $obj_especialidades->nombre;
				}

				if ( $aColumns[$i] == "id_estudios" ){
					$obj_estudios = new Estudios($aRow[$aColumns[$i]]);
					$estudio = $obj_estudios->nombre;
				}

				if ($aColumns[$i] == "id_dias_semana"){
					$obj_dias_semana = new Dias_semana($aRow[$aColumns[$i]]);
					$dia_semana = $obj_dias_semana->nombre;
				}

				if ($aColumns[$i] == "id_sectores"){
					$obj_sectores = new Sectores($aRow[$aColumns[$i]]);
					$sector = $obj_sectores->nombre;
				}

				if ($aColumns[$i] == "id_usuarios"){
					$obj_usuarios = new Usuarios($aRow[$aColumns[$i]]);
					$usuario = $obj_usuarios->usuario;
				}

				if ($aColumns[$i] == "id_subsectores"){
					$obj_subsectores = new Subsectores($aRow[$aColumns[$i]]);
					$subsector = $obj_subsectores->nombre;
				}

				if ($aColumns[$i] == "id_mantenimientos_estados"){
					$obj_mantenimientos_estados = new Mantenimientosestados($aRow[$aColumns[$i]]);
					$mantenimientos_estados = $obj_mantenimientos_estados->nombre;
				}

				if ($aColumns[$i] == "id_agendas_tipos"){
					$obj_agendas_tipos = new Agendas_Tipos($aRow[$aColumns[$i]]);
					$agenda_tipo = $obj_agendas_tipos->nombre;
				}

				if ($aColumns[$i] == "id_plantas"){
					$obj_plantas = new Plantas($aRow[$aColumns[$i]]);
					$planta = $obj_plantas->nombre;
				}

				if ($aColumns[$i] == "id_cobros_conceptos"){
					$obj_cobro_concepto = new Cobros_conceptos($aRow[$aColumns[$i]]);
					$cobro_concepto = $obj_cobro_concepto->nombre;
				}

				if ($aColumns[$i] == "fecha"){
					$obj_estructura = new Estructura();
					$fecha = $obj_estructura->cambiaf_a_normal($aRow[$aColumns[$i]], '/');
				}

				$row[$i] = $aRow[ $aColumns[$i] ];
			}

			$mostrar = "<a href='#' class='btn_opciones' data-titulo='Detalle' data-tipo_btn='detalle' data-id='".$aRow["id_".$tabla]."' data-tipo='detalle' data-tabla='".$tabla."'><img src='".URL."files/img/btns/detalle.png' border='0'></a>";

			if (!isset($id_padre)){
				$editar = "<a href='#' class='btn_opciones' data-titulo='Editar ".$obj->titulo_tabla_singular."' data-tipo='editar' data-id='".$aRow["id_".$tabla]."' data-tabla='".$tabla."'><img src='".URL."files/img/btns/editar.png' border='0'></a>";
				$eliminar = "<a href='#' data-id='".$aRow[$aColumns[0]]."' data-titulo='Eliminar' data-tabla='".$tabla."' class='btn_eliminar'><img src='".URL."files/img/btns/eliminar.png' border='0' ></a>";
			}else{
				$editar = "<a href='#' class='btn_opciones' data-titulo='Editar ".$obj->titulo_tabla_singular."' data-tipo='editar' data-id='".$aRow["id_".$tabla]."' data-tabla='".$tabla."' data-id_padre='".$id_padre."'><img src='".URL."files/img/btns/editar.png' border='0'></a>";
				$eliminar = "<a href='#' data-id='".$aRow[$aColumns[0]]."' data-titulo='Eliminar' data-tabla='".$tabla."' class='btn_eliminar' data-id_padre='".$id_padre."'><img src='".URL."files/img/btns/eliminar.png' border='0'></a>";
			}

			switch ($tabla){
				case "pacientes":
					$checkbox = "<input type='checkbox' class='seleccion' id='".$aRow[$aColumns[0]]."' />";
					$cobros = "<a class='btn_opciones' href='#' data-id='".$aRow["id_pacientes"]."' data-tipo_btn='tabla_hija' data-hija='cobros' data-nombre='Pagos Realizados'><img src='".URL."files/img/btns/cobros.png' border='0'></a>";
					$turnos = "<a class='btn_opciones' href='#' data-id='".$aRow["id_pacientes"]."' data-tipo_btn='tabla_hija' data-hija='turnos' data-nombre='Turnos Reservados'><img src='".URL."files/img/btns/turnos.png' border='0'></a>";

					$row[0] = utf8_encode($aRow["id_pacientes"]);
					$row[1] = "<span class='paciente_buscado' data-id='".$aRow["id_pacientes"]."'>".$aRow["nro_documento"]."</span>";
					$row[2] = "<span class='paciente_buscado' data-id='".$aRow["id_pacientes"]."'>".utf8_encode($aRow["apellidos"])."</span>";
					$row[3] = utf8_encode($aRow["nombres"]);
					$row[4] = utf8_encode($obra_social);

                    $mySql = "
                        SELECT COUNT(id_turnos) AS `TurnosCant`
                        FROM turnos
                        WHERE id_pacientes = '{$row[0]}'
                        GROUP BY id_turnos
                    ";
            		$myResult = $obj->db->consulta($mySql);
                    $TurnosCant = 0;
                    if ($myRow = $obj->db->fetch_array($myResult)){
                        $TurnosCant = $myRow['TurnosCant'];
                    }

                    if ($_SESSION['ID_USUARIO'] === '0' or $TurnosCant == 0) {
                        $row[5] = $editar.''.$turnos.''.$cobros.''.$eliminar.'';
                    } else {
                        $row[5] = $editar.''.$turnos.''.$cobros.'';
                    }

				break;
				case "medicos":
					$especialidades = "<a class='btn_opciones' href='#' data-id='".$aRow[$aColumns[0]]."' data-tipo_btn='tabla_hija' data-hija='medicos_especialidades' data-nombre='Especialidades por M&eacute;dicos'><img src='".URL."files/img/btns/medicos_especialidades.png' border='0'></a>";
					$obras_sociales_planes = "<a class='btn_opciones' href='#' data-id='".$aRow[$aColumns[0]]."' data-tipo_btn='tabla_hija' data-hija='medicos_obras_sociales' data-nombre='Planes de Obras Sociales por M&eacute;dicos'><img src='".URL."files/img/btns/medicos_obras_sociales.png' border='0'></a>";
					$estudios = "<a class='btn_opciones' href='#' data-id='".$aRow[$aColumns[0]]."' data-tipo_btn='tabla_hija' data-hija='medicos_estudios' data-nombre='Estudios por M&eacute;dicos'><img src='".URL."files/img/btns/medicos_estudios.png' border='0'></a>";

					$checkbox = "<input type='checkbox' class='seleccion' id='".$aRow[$aColumns[0]]."' />";

					$row[0] = $aRow["id_medicos"];
					$row[1] = utf8_encode($aRow["saludo"]);
					$row[2] = utf8_encode($aRow["apellidos"]);
					$row[3] = utf8_encode($aRow["nombres"]);
					$row[4] = number_format(utf8_encode($aRow["nro_documento"]), 0, ",", ".");
					$row[5] = strtolower(utf8_encode($aRow["email"]));
					$row[6] = utf8_encode($aRow["telefonos"]);
					$row[7] = utf8_encode($sector);
					$row[8] = utf8_encode($subsector);
					$row[9] = utf8_encode($aRow["interno"]);
					$row[10] = utf8_encode($planta);
					$row[11] = utf8_encode($aRow["matricula"]);
                    if ($_SESSION['ID_USUARIO'] === '0') {
                        $row[12] =
                            $editar.''.
                            $especialidades.''.
                            $obras_sociales_planes.''.
                            $estudios.''.
                            $eliminar.''
                        ;
                    } else {
                        $row[12] =
                            $especialidades.''.
                            $obras_sociales_planes.''.
                            $estudios.''
                        ;
                    }

				break;
				case "medicosext":
					$row[0] = $aRow["id_medicosext"];
					$row[1] = utf8_encode($aRow["saludo"]);
					$row[2] = utf8_encode($aRow["apellidos"]);
					$row[3] = utf8_encode($aRow["nombres"]);
					$row[4] = utf8_encode($aRow["matricula"]);
                    if ($_SESSION['ID_USUARIO'] === '0') {
                        $row[5] = $editar.''.$eliminar.'';
                    } else {
                        $row[5] = $editar.'';
                    }

				break;
				case "medicosexp":
					$row[0] = $aRow["id_medicosexp"];
					$row[1] = utf8_encode($aRow["saludo"]);
					$row[2] = utf8_encode($aRow["apellidos"]);
					$row[3] = utf8_encode($aRow["nombres"]);
					$row[4] = utf8_encode($aRow["turnos_turnos"]);
					$row[5] = utf8_encode($aRow["turnos_sobreturnos"]);
					$row[6] = utf8_encode($aRow["turnos_total"]);
					$row[7] = utf8_encode($aRow["minutos_horario"]);
					$row[8] = utf8_encode($aRow["minutos_turnos"]);
					$row[9] = utf8_encode($aRow["minutos_sobreturnos"]);
					$row[10] = utf8_encode($aRow["minutos_total"]);
					$row[11] = utf8_encode($aRow["horas_horario"]);
					$row[12] = utf8_encode($aRow["horas_turnos"]);
					$row[13] = utf8_encode($aRow["horas_sobreturnos"]);
					$row[14] = utf8_encode($aRow["horas_total"]);

				break;
				case "especialidades":
					$checkbox = "<input type='checkbox' class='seleccion' id='".$aRow[$aColumns[0]]."' />";

					$row[0] = $aRow["id_especialidades"];
					$row[1] = utf8_encode($aRow["nombre"]);
                    if ($_SESSION['ID_USUARIO'] === '0') {
                        $row[2] = $editar.''.$eliminar.'';
                    } else {
                        $row[2] = '';
                    }

				break;
				case "estudios":
					$row[0] = $aRow["id_estudios"];
					$row[1] = utf8_encode($aRow["nombre"]);
					$row[2] = utf8_encode($aRow["importe"]);
					$row[3] = utf8_encode($aRow["arancel"]);
					$row[4] = utf8_encode($aRow["requisitos"]);
					$row[5] = utf8_encode($aRow["codigopractica"]);
                    if ($_SESSION['ID_USUARIO'] === '0') {
                        $row[6] = $editar.''.$eliminar.'';
                    } else {
                        $row[6] = '';
                    }

				break;
				case "obras_sociales":
					$planes = "<a class='btn_opciones' href='#' data-id='".$aRow[$aColumns[0]]."' data-tipo_btn='tabla_hija' data-hija='obras_sociales_planes' data-nombre='Planes por Obra Social'><img src='".URL."files/img/btns/obras_sociales_planes.png' border='0'></a>";

					$estudios = "<a class='btn_opciones' href='#' data-id='".$aRow[$aColumns[0]]."' data-tipo_btn='tabla_hija' data-hija='obras_sociales_estudios' data-nombre='Estudios por Obra Social'><img src='".URL."files/img/btns/obras_sociales_estudios.png' border='0'></a>";

					$row[0] = $aRow["id_obras_sociales"];
					$row[1] = utf8_encode($aRow["abreviacion"]);
					$row[2] = utf8_encode($aRow["nombre"]);
					//$row[3] = utf8_encode($aRow["importe_consulta"]);
                    if ($_SESSION['ID_USUARIO'] === '0') {
						$row[3] = $editar.''.$planes.''.$estudios.''.$eliminar.'';
                    } else {
						$row[3] = $planes.''.$estudios;
                    }
				break;

				case "medicos_especialidades":
					$horarios = "<a class='btn_opciones' href='#' data-id='".$aRow["id_medicos"]."-".$aRow["id_especialidades"]."' data-tipo_btn='tabla_hija' data-hija='medicos_horarios' data-nombre='Horario de Medicos por Especialidad'><img src='".URL."files/img/btns/medicos_horarios.png' border='0'></a>";

					$checkbox = "<input type='checkbox' class='seleccion' id='".$aRow[$aColumns[0]]."' />";

					$row[0] = $aRow["id_medicos_especialidades"];
					$row[1] = utf8_encode($especialidad);
					$row[2] = utf8_encode(
                        substr($aRow["duracion_turno"], 0, 2) * 60 +
                        substr($aRow["duracion_turno"], 3, 2)
                    )." min";
					//$row[3] = $mostrar." ".$editar." ".$horarios;
                    if ($_SESSION['ID_USUARIO'] === '0') {
						$row[3] = $horarios.''.$eliminar.'';
                    } else {
						$row[3] = $horarios.'';
                    }
				break;

				case "medicos_estudios":

					$row[0] = $aRow["id_medicos_estudios"];
					$row[1] = utf8_encode($estudio);
					$row[2] = '<input type="text" class="particular" id="'.$aRow["id_medicos_estudios"].'" value="'.$aRow["particular"].'" />';
					//$row[2] = $aRow["particular"];
					//$row[2] = $mostrar." ".$editar;
                    if ($_SESSION['ID_USUARIO'] === '0') {
                        $row[3] = $editar.'';
                    } else {
                        $row[3] = '';
                    }
				break;

				case "medicos_obras_sociales":

					$row[0] = $aRow["id_medicos_obras_sociales"];
					$row[1] = utf8_encode($aRow["nombre"]);
					$row[2] = '<input type="text" class="arancel" id="'.$aRow["id_medicos_obras_sociales"].'" value="'.$aRow["arancel"].'" />';
                    if (
                        $_SESSION['ID_USUARIO'] === '0' OR
                        isset($_SESSION['ID_MEDICO'])
                    ) {
                        $row[3] = $eliminar.'';
                    } else {
                        $row[3] = '';
                    }
				break;
				case 'obras_sociales_planes':
					$row[0] = $aRow["id_obras_sociales_planes"];
					$row[1] = utf8_encode($aRow["nombre"]);
                    if ($_SESSION['ID_USUARIO'] === '0') {
                        $row[2] = $editar.'';
                    } else {
                        $row[2] = '';
                    }
				break;
				case "obras_sociales_estudios":

					$row[0] = utf8_encode($aRow["nomenclador"]);
					$row[1] = utf8_encode($estudio);
					$row[2] = utf8_encode($aRow["importe"]);
					//$row[3] = $mostrar." ".$editar;
                    if ($_SESSION['ID_USUARIO'] === '0') {
                        $row[3] = $editar.'';
                    } else {
                        $row[3] = '';
                    }
				break;

				case "medicos_horarios":
					$checkbox = "<input type='checkbox' class='seleccion' id='".$aRow[$aColumns[0]]."' />";

					$row[0] = $aRow["id_medicos_horarios"];
					$row[1] = utf8_encode($dia_semana);
					$row[2] = utf8_encode($planta);
					$row[3] = utf8_encode(substr($aRow["desde"], 0, 5));
					$row[4] = utf8_encode(substr($aRow["hasta"], 0, 5));
                    if ($_SESSION['ID_USUARIO'] === '0') {
                        $row[5] = $editar.''.$eliminar.'';
                    } else {
                        $row[5] = '';
                    }
				break;
				case "cobros":
					$hoy = date("Y-m-d");
					$fecha_limite =  date( "Y-m-d", strtotime( $aRow["fecha"] ." +1 day" ) );
					//if (($hoy <= $fecha_limite) && $aRow['reintegro'] == 0){
					if ((($aRow['id_cobros_conceptos'] == 2) || ($aRow['id_cobros_conceptos'] == 4)) && $aRow['reintegro'] == 0){
						$reintegro = "<a class='btn_reintegro' href='#' data-id='".$aRow[$aColumns[0]]."' data-nombre='Confirmar Reintegro'><img id='rein' src='".URL."files/img/btns/confirmar.png' border='0'></a>";
					}else{
						$reintegro = "";
					}

					//$row[0] = utf8_encode($aRow["id_cobros"]);
					$row[0] = $aRow["fecha"];
					$row[1] = $aRow["hora"];
					$row[2] = utf8_encode($cobro_concepto);
					$row[3] = utf8_encode($aRow["importe"]);
					//$row[4] = $mostrar.$reintegro;
					$row[4] = utf8_encode($reintegro);
				break;
				case 'turnos':
					$row[0] = $aRow["id_turnos"];
					$row[1] = utf8_encode($medico);
					$row[2] = utf8_encode($fecha);
					$row[3] = utf8_encode($aRow["desde"]);
					$row[4] = utf8_encode($aRow["hasta"]);
					$row[5] = utf8_encode($aRow["estado"]);
				break;
				case 'sectores':
					$row[0] = $aRow["id_sectores"];
					$row[1] = utf8_encode($aRow['nombre']);
                    $row[2] = $editar.''.$eliminar.'';
				break;
				case 'subsectores':
					$row[0] = $aRow["id_subsectores"];
					$row[1] = utf8_encode($sector);
					$row[2] = utf8_encode($aRow['nombre']);
                    $row[3] = $editar.''.$eliminar.'';
				break;
				case 'agendas':
					$row[0] = $aRow["id_agendas"];
					$row[1] = utf8_encode($aRow['nombre']);
					$row[2] = utf8_encode($aRow['apellido']);
					$row[3] = utf8_encode($aRow['rubro']);
					$row[4] = utf8_encode($aRow['celular']);
					$row[5] = utf8_encode($aRow['telefono']);
					$row[6] = utf8_encode($aRow['direccion']);
					$row[7] = utf8_encode($agenda_tipo);
                    $row[8] = $editar.''.$eliminar.'';
				break;
				case 'mantenimientos':
                case 'mantenimhistoricos':
					$row[0] = $aRow["id_mantenimientos"];
					$row[1] = utf8_encode(date("d/m/Y H:i", strtotime($aRow['fecha']))."hs");
					$row[2] = utf8_encode($sector);
					$row[3] = utf8_encode($aRow['solicitador']);
					$row[4] = utf8_encode($aRow['tarea']);
					$row[5] = utf8_encode($aRow['especialista']);
					$row[6] = utf8_encode($aRow['observaciones']);
					$row[7] = utf8_encode($mantenimientos_estados);
					$row[8] = utf8_encode($usuario);
                    $color = '<script>$(this).parent().parent().html(\'diego\');</script>';
                    if ($tabla == 'mantenimhistoricos') {
                        $row[9] = '';
                    } else {
                        $row[9] = $editar.''.$eliminar.''.$color.'';
                    }
				break;
				case 'encuestas':
					$turnos = "<a class='btn_opciones' href='#' data-id='".$aRow["id_pacientes"]."' data-tipo_btn='tabla_hija' data-hija='turnos' data-nombre='Turnos Reservados'><img src='".URL."files/img/btns/turnos.png' border='0'></a>";
					$row[0] = $aRow["id_encuestas_respuestas"];
					$row[1] = utf8_encode($aRow['fecha_alta']);
					$row[2] = utf8_encode($aRow['hora_alta']);
					$row[3] = utf8_encode($aRow['paciente']);
					$row[4] = utf8_encode($aRow['respuesta1']);
					$row[5] = utf8_encode($aRow['respuesta2']);
					$row[6] = utf8_encode($aRow['medico']);
					$row[7] = utf8_encode($aRow['especialidad']);
                    $row[8] = $turnos;
				break;

			}
			$output['aaData'][] = $row;
	}
}else{
	$output['aaData'] = "";
}

echo json_encode( $output );

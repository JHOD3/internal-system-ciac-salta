<?php
class MySQL{
	private $conexion;
	private $total_consultas;

	function __construct(){
		//Variables BD
		$this->conexion = (mysql_connect(SERVIDOR,BD_USUARIO,BD_PASS)) or die(mysql_error());
		mysql_select_db(BD_NOMBRE,$this->conexion) or die(mysql_error());
	}
	
 	public function consulta($consulta){
  		$this->total_consultas++;
		$resultado = mysql_query($consulta,$this->conexion);
  		if(!$resultado){
  			echo 'MySQL Error: ' . mysql_error();
  			exit;
  		}
  		return $resultado;
  	}
	
	public function fetch_array($consulta){
		return mysql_fetch_array($consulta);
	}
	
	public function fetch_assoc($consulta){
		return mysql_fetch_assoc($consulta);
	}

	public function num_rows($consulta){
		return mysql_num_rows($consulta);
	}
	
	public function num_fields($consulta){
		return mysql_num_fields($consulta);
	}
	
	public function field_name($consulta, $x){
		return mysql_field_name($consulta, $x);
	}	

	public function getTotalConsultas(){
		return $this->total_consultas;
	}
	
	public function ultimo_id_insertado(){
		return mysql_insert_id();
	}
}
?>
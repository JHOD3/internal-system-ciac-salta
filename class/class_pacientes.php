<?php
interface iPacientes{
	function FormAlta();
	function FormModificacion();
}

class Pacientes extends Estructura implements iPacientes{

	function __construct($id = ""){
		$this->nombre_tabla = "pacientes";
		$this->titulo_tabla = "Pacientes";
		$this->titulo_tabla_singular = "Paciente";
		$this->tabla_padre = "";

		$this->drop_label_elija = "Elija un Paciente";

		parent::__construct($id);
	}

	function FormAlta(){
		$htm = $this->Html($this->nombre_tabla."/form_alta");

		requerir_class("tipos_documentos","obras_sociales", "obras_sociales_planes");

		$obj_tipos_documentos = new Tipos_Documentos();
		$htm->Asigna("DROP_TIPOS_DOCUMENTOS",$obj_tipos_documentos->Drop("", 1));

		$obj_obras_sociales = new Obras_sociales();
		$htm->Asigna("DROP_OBRAS_SOCIALES", $obj_obras_sociales->Drop('nombre'));

		$obj_obras_sociales_planes = new Obras_sociales_planes();
		$htm->Asigna("DROP_OBRAS_SOCIALES_PLANES", $obj_obras_sociales_planes->DropVacio());

		$htm->Asigna("TABLA",$this->nombre_tabla);

		CargarVariablesGrales($htm, $tipo = "");

		return ($htm->Muestra());
	}

	function FormModificacion(){
		$htm = $this->Html($this->nombre_tabla."/form_modificacion");
		$row = $this->registro;

		requerir_class("tipos_documentos","obras_sociales", "obras_sociales_planes");

		$obj_tipos_documentos = new Tipos_Documentos();
		$row["DROP_TIPOS_DOCUMENTOS"] = $obj_tipos_documentos->Drop("DESC", $row["id_tipos_documentos"]);

		$obj_obras_sociales = new Obras_sociales();
		$row["DROP_OBRAS_SOCIALES"] = $obj_obras_sociales->Drop("ASC", $row["id_obras_sociales"]);

		$obj_obras_sociales_planes = new Obras_sociales_planes();
		$row["DROP_OBRAS_SOCIALES_PLANES"] = $obj_obras_sociales_planes->Drop("DESC", $row["id_obras_sociales_planes"]);

        if ($_SESSION['SUPERUSER'] > 1) {
            $row['bloqueado0'] = '';
            $row['bloqueado1'] = '';
            $row['bloqueado'.$row['bloqueado']] = ' checked="checked"';
        } else {
            $row['bloqueado0'] = ' disabled="disabled"';
            $row['bloqueado1'] = ' disabled="disabled"';
            $row['bloqueado'.$row['bloqueado']] = ' checked="checked" disabled="disabled"';
        }

		$htm->Asigna("TABLA",$this->nombre_tabla);

		$htm->AsignaBloque('block_registros',$row);

		CargarVariablesGrales($htm, $tipo = "");

		return  utf8_encode($htm->Muestra());
	}

	function Detalle($tipo, $sistema = "sas"){
		$htm = $this->Html($this->nombre_tabla."/detalle_".$tipo);
		$row = $this->registro;

		requerir_class('obras_sociales','obras_sociales_planes');

		$obj_obra_social = new Obras_sociales($row['id_obras_sociales']);
		$row["OBRA_SOCIAL"] = $obj_obra_social->abreviacion;

		$obj_obra_social_plan = new Obras_sociales_planes($row['id_obras_sociales_planes']);
		$row["OBRA_SOCIAL_PLAN"] = $obj_obra_social_plan->nombre;
        $row['nro_documento'] = number_format($row['nro_documento'], 0, ",", ".");

		$row['nombres'] = upper($row['nombres']);
		$row['apellidos'] = upper($row['apellidos']);
		$htm->AsignaBloque('block_registros',$row);

		$rta = $htm->Muestra();

		return utf8_encode($rta);
	}



	function TablaAdmin(){
		$tabla = $this->html($this->nombre_tabla."/a_tabla");

		$tabla->Asigna("NOMBRE_TABLA",$this->nombre_tabla);

		$rta = utf8_encode($tabla->Muestra());

		return $rta;
	}

	function Buscar($dni){
		$htm = $this->Html($this->nombre_tabla."/detalle_corto");

		$query_string = $this->querys->Buscar($this->nombre_tabla, $dni);
		$query = $this->db->consulta($query_string);
		$cant = $this->db->num_rows($query);

		if ($cant != 0){

			requerir_class('obras_sociales','obras_sociales_planes');

			while ($row = $this->db->fetch_array($query)){
				$obj_obra_social = new Obras_sociales($row['id_obras_sociales']);
				$row["OBRA_SOCIAL"] = $obj_obra_social->abreviacion;

				$obj_obra_social_plan = new Obras_sociales_planes($row['id_obras_sociales_planes']);
				$row["OBRA_SOCIAL_PLAN"] = $obj_obra_social_plan->nombre;
                $row['nro_documento'] = number_format($row['nro_documento'], 0, ",", ".");

				$row['nombres'] = upper($row['nombres']);
				$row['apellidos'] = upper($row['apellidos']);


                if ($_SESSION['SISTEMA'] == 'sas') {
					//kcmnt Aqui obtiene de db las observaciones del paciente y arma el html.
            		$query_string_po = $this->querys->ObservacionesDePacientes($row['id_pacientes']);
            		$query_po = $this->db->consulta($query_string_po);
                    $pacientes_observaciones = '';
                    if ($this->db->num_rows($query_po) > 0) {
            			while ($row_po = $this->db->fetch_array($query_po)) {
                            $fechahora = date("d/m/y H:i", strtotime($row_po['fechahora']))."hs";
                            $pacientes_observaciones = <<<HTML
                                <div>
                                    <div><strong>{$fechahora} - {$row_po['usuario']}</strong></div>
                                    <div>{$row_po['observacion']}</div>
                                </div>
                                <hr style="margin:10px 0;" />
                                {$pacientes_observaciones}
HTML;
            		    }
                    } else {
                        $pacientes_observaciones.= <<<HTML
                            <div>
                                <div>no hay observaciones</div>
                            </div>
                            <hr style="margin:10px 0;" />
HTML;
                    }
                    $URL = URL;
                    $pacientes_observaciones.= <<<HTML
                        <div>
                            <a
                                href="#"
                                class="btn_tabla_ready"
                                data-id_padre="{$row['id_pacientes']}"
                                data-nombre="pacientes_observaciones"
                                style="color: #838383"
                            ><img src='{$URL}files/img/btns/detalle.png' border='0'>&nbsp;Administrar Observaciones</a>
                        </div>
                        <script>
                		$(".btn_tabla_ready").click(function(event) {
                		    event.preventDefault();
                        	var id_padre = $(this).data("id_padre");
                            var tabla = $(this).data("nombre");
                			if (tabla != "mensajes"){
                				IniciarVentana("ventana_menu", "abrir", tabla);
                				$.ajax({
                					type: "POST",
                					url: "{$URL}ajax/admin_tabla.php",
                					data: {tabla: tabla, id_padre: id_padre},
                					beforeSend: function() {
                						$(ventana_menu).html("");
                					},
                					success: function(requestData){
                						var rta = requestData;
                						$(ventana_menu).html(rta);
                					},
                					complete: function(requestData, exito){
                					},
                					error: function (){
                						alert ("error");
                					}
                				});
                				$(ventana_menu).dialog('option', 'title', 'Administraci\u00f3n de Observaciones de Pacientes');
                				$(ventana_menu).dialog( "open" );
                				$(ventana_menu).focus();
                			}else{
                				alert('M\u00f3dulo Inhabilitado.<br /> Temporalmente en Construcci\u00f3n.','ATENCI\u00d3N')
                			}
                		});
                        </script>
HTML;
                    $row['PACIENTES_OBSERVACIONES'] =
                        '<strong>OBSERVACIONES:</strong><hr style="margin:10px 0;" />'.
                        $pacientes_observaciones
                    ;
                } else {
                    $row['PACIENTES_OBSERVACIONES'] = '';
                }

                $htm->AsignaBloque('block_registros',$row);
			}

			$rta = $htm->Muestra();
        } else {
            $row['PACIENTES_OBSERVACIONES'] = '';
			$rta = "Paciente no encontrado";
		}


		return utf8_encode($rta);
	}

	function PanelAdmin(){
		$htm = $this->Html($this->nombre_tabla."/panel_admin");

		$htm->Asigna("LISTADO", $this->TablaAdmin());

		$htm->Asigna("TABLA", $this->nombre_tabla);
		$htm->Asigna("TITULO_TABLA", $this->titulo_tabla_singular);

		CargarVariablesGrales($htm, $tipo = "");

		echo ($htm->Muestra());

	}

}
?>
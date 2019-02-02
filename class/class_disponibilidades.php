<?php
interface iDisponibilidades{

}

class Disponibilidades extends Estructura implements iDisponibilidades{

	function __construct($id = ""){
		$this->nombre_tabla = "disponibilidades";
		$this->titulo_tabla = "Disponibilidades";
		$this->tabla_padre = "";

		$this->drop_label_elija = "Elija un Día";

		parent::__construct($id);
	}

    function upper($str)
    {
        $arrAcentos = array('á', 'é', 'í', 'ó', 'ú', 'ñ', 'ü');
        $arrReemplz = array('Á', 'É', 'Í', 'Ó', 'Ú', 'Ñ', 'Ü');
        $str = str_replace($arrAcentos, $arrReemplz, $str);
        return strtoupper($str);
    }

    function lower($str)
    {
        $arrAcentos = array('Á', 'É', 'Í', 'Ó', 'Ú', 'Ñ', 'Ü');
        $arrReemplz = array('á', 'é', 'í', 'ó', 'ú', 'ñ', 'ü');
        $str = str_replace($arrAcentos, $arrReemplz, $str);
        return strtolower($str);
    }

    function doSaludo($rsMedico, $prefix = true) {
        $str = "";
        if ($prefix) {
            switch ($this->lower($rsMedico['saludo'])) {
                case "dr.":
                    $str.= "el ";
                    break;
                case "dra.":
                    $str.= "la ";
                    break;
            }
        }
        $str.= ucwords($this->lower(trim($rsMedico['saludo'])));
        $str.= " ";
        $str.= $this->upper(trim($rsMedico['apellidos']));
        $str.= ", ";
        $str.= ucwords($this->lower(trim($rsMedico['nombres'])));
        return $str;
    }

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

	function DiaLibre(){
		$htm = $this->Html($this->nombre_tabla."/form_detalle");

		$htm->Asigna("TABLA",$this->nombre_tabla);

        $row = $this->registro;

		$query_string = $this->querys->DetalleDiaDisponibilidad($row['id_dias_semana']);
		$query = $this->db->consulta($query_string);

		$addRows = "";
        $cnct = "";
        $arrRows = array();
        $columnas = array();
        while ($row = $this->db->fetch_array($query)) {
            $arrRows[] = $row;
            if (!in_array($row['nro_consultorio'], $columnas)) {
                $columnas[] = $row['nro_consultorio'];
            }
            $desdeH = (int)substr($row['desde'], 0, 2);
            $desdeM = (int)substr($row['desde'], 3, 2);
            $hastaH = (int)substr($this->horaMM($row['hasta'], $row['duracion_turno']), 0, 2);
            $hastaM = (int)substr($this->horaMM($row['hasta'], $row['duracion_turno']), 3, 2);
            $doSaludo = $this->doSaludo($row, false);
            $addRows.= "{$cnct}['Consultorio {$row['nro_consultorio']}', '{$doSaludo} - {$row['especialidad']}', new Date(0,0,0,{$desdeH},{$desdeM},0), new Date(0,0,0,{$hastaH},{$hastaM},0)]";
            $cnct = ",\n";
        }
        sort($columnas);

        if (!trim($addRows)) {
            $addRows = "]);}</script>No se encontraron datos.<script>nul=([";
        }
        $htm->Asigna("ADDTABLETHEADS", "<th>Consultorio<br />".implode("</th><th>Consultorio<br />", $columnas)."</th>");
        $htm->Asigna("ADDROWS", utf8_encode($addRows));

        $addTableRows = "";
        $arrEspecialidades = array();
        for (
            $hora = '07:00:00';
            $hora <= '22:00:00';
            $hora = $this->SumarHorasTime($hora, '00:30:00')
        ) {
            $addTableRows.= "<tr><td>".substr($hora, 0, 5)."hs</td>";
            for ($j = 0; $j < count($columnas); $j++) {
                $data = null;
                for ($k = 0; $k < count($arrRows); $k++) {
                    if (
                        $arrRows[$k]['nro_consultorio'] == utf8_decode($columnas[$j]) and
                        $arrRows[$k]['desde'] <= $hora and
                        $this->SumarHorasTime($arrRows[$k]['hasta'], $arrRows[$k]['duracion_turno']) >= $hora
                    ) {
                        $data = $arrRows[$k];
                        if (!isset($arrEspecialidades[$data['especialidad']])) {
                            $arrEspecialidades[$data['especialidad']] = count($arrEspecialidades) + 1;
                        }
                    }
                }
                if ($data) {
                    $addTableRows.= '<td class="ocu col1">OCUPADO</td>';
                } else {
                    $addTableRows.= '<td class="ocu col2">DISPONIBLE</td>';
                }
            }
            $addTableRows.= "</tr>";
        }

        $htm->Asigna("ADDTABLEROWS", utf8_encode($addTableRows));

		CargarVariablesGrales($htm, $tipo = "");

		return ($htm->Muestra());
	}

	function DiaOcupado(){
		$htm = $this->Html($this->nombre_tabla."/form_detalle");

		$htm->Asigna("TABLA",$this->nombre_tabla);

        $row = $this->registro;

		$query_string = $this->querys->DetalleDiaDisponibilidad($row['id_dias_semana']);
		$query = $this->db->consulta($query_string);

		$addRows = "";
        $cnct = "";
        $arrRows = array();
        $columnas = array();
        while ($row = $this->db->fetch_array($query)) {
            $arrRows[] = $row;
            if (!in_array($row['nro_consultorio'], $columnas)) {
                $columnas[] = $row['nro_consultorio'];
            }
            $desdeH = (int)substr($row['desde'], 0, 2);
            $desdeM = (int)substr($row['desde'], 3, 2);
            $hastaH = (int)substr($this->horaMM($row['hasta'], $row['duracion_turno']), 0, 2);
            $hastaM = (int)substr($this->horaMM($row['hasta'], $row['duracion_turno']), 3, 2);
            $doSaludo = $this->doSaludo($row, false);
            $addRows.= "{$cnct}['Consultorio {$row['nro_consultorio']}', '{$doSaludo} - {$row['especialidad']}', new Date(0,0,0,{$desdeH},{$desdeM},0), new Date(0,0,0,{$hastaH},{$hastaM},0)]";
            $cnct = ",\n";
        }
        sort($columnas);

        if (!trim($addRows)) {
            $addRows = "]);}</script>No se encontraron datos.<script>nul=([";
        }
        $htm->Asigna("ADDTABLETHEADS", "<th>Consultorio<br />".implode("</th><th>Consultorio<br />", $columnas)."</th>");
        $htm->Asigna("ADDROWS", utf8_encode($addRows));

        $addTableRows = "";
        $arrEspecialidades = array();
        for (
            $hora = '07:00:00';
            $hora <= '22:00:00';
            $hora = $this->SumarHorasTime($hora, '00:30:00')
        ) {
            $addTableRows.= "<tr><td>".substr($hora, 0, 5)."hs</td>";
            for ($j = 0; $j < count($columnas); $j++) {
                $data = null;
                for ($k = 0; $k < count($arrRows); $k++) {
                    if (
                        $arrRows[$k]['nro_consultorio'] == utf8_decode($columnas[$j]) and
                        $arrRows[$k]['desde'] <= $hora and
                        $this->SumarHorasTime($arrRows[$k]['hasta'], $arrRows[$k]['duracion_turno']) >= $hora
                    ) {
                        $data = $arrRows[$k];
                        if (!isset($arrEspecialidades[$data['especialidad']])) {
                            $arrEspecialidades[$data['especialidad']] = count($arrEspecialidades) + 1;
                        }
                    }
                }
                if ($data) {
                    $addTableRows.=
                        '<td class="ocu col'.
                        ($arrEspecialidades[$data['especialidad']] % 10).
                        '">'.
                        $data['especialidad'].
                        '</td>'
                    ;
                } else {
                    $addTableRows.= '<td class="lib"></td>';
                }
            }
            $addTableRows.= "</tr>";
        }

        $htm->Asigna("ADDTABLEROWS", utf8_encode($addTableRows));

		CargarVariablesGrales($htm, $tipo = "");

		return ($htm->Muestra());
	}

	function TablaAdmin(){
		$tabla = $this->html($this->nombre_tabla."/a_tabla");

		$tabla->Asigna("NOMBRE_TABLA",$this->nombre_tabla);

		$rta = utf8_encode($tabla->Muestra());

		return $rta;
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

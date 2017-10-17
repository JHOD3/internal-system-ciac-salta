<?php
class Diagnostico_model extends CI_Model
{

    // CONFIGURACIONES

    function validar()
    {
        $this->form_validation
            ->set_rules(
                'fecha',
                'Fecha',
                'trim|required'
            )
            ->set_rules(
                'apellido',
                'Apellido',
                'trim|required|min_length[5]|max_length[50]'
            )
            ->set_rules(
                'sexoid',
                'Sexo',
                'trim|required'
            )
            ->set_rules(
                'fechanac',
                'Fecha de Nacimiento',
                'trim|required'
            )
            ->set_rules(
                'dni',
                'DNI',
                'trim|required|min_length[5]|max_length[15]|alpha_numeric'
            )
            ->set_rules(
                'estado',
                'Estado',
                'trim|required'
            )
        ;
        return ($this->form_validation->run() != FALSE);
    }

    function obtenerDropDownsRel()
    {
        #$this->load->model('Sexo_model', 'Sexo');
        #$ddRel['sexo_listado'] = $this->Sexo->obtenerListado();
        #return $ddRel;
    }

    // CONSULTA DE LECTURA

    function obtenerItem($id)
    {
        $query = $this->db
            ->from('turnos')
            ->limit(1)
            ->get()
            ->result_array()
        ;
        if (count($query) > 0) {
            #$query[0]['fechanac'] = date_view($query[0]['fechanac']);
            return $query[0];
        } else {
            return null;
        }
    }

    protected function _filtroListado($date1, $date2, $post)
    {
        $this->db
            ->where_in('t.id_especialidades', explode(", ", ID_ESPECIALIDADES . ', 33'))
            ->where_in('t.id_turnos_estados', array(2, 7))
            ->where('t.estado', 1)
            ->where("CONCAT(t.fecha, ' ', t.desde) BETWEEN '{$date1} {$post['hour1']}:00' AND '{$date2} {$post['hour2']}:00'")
        ;
        $aPost = array(
            'spac' => "CONCAT(TRIM(p.apellidos), ', ', TRIM(p.nombres))",
            'sces' => "e.codigopractica",
            'sest' => "e.nombre",
            'srea' => "m.id_medicos",
            'soso' => "os.abreviacion",
            'snor' => "ts.nro_orden",
            'snaf' => "ts.nro_afiliado",
            'scan' => "ts.cantidad",
            'sder' => "ts.matricula_derivacion"
        );
        $cnct = "";
        $where = "";
        foreach ($aPost AS $kP => $rP) {
            if (isset($post[$kP]) and trim($post[$kP])) {
                switch ($kP) {
                    case "srea":
                        $where.= "
                            {$cnct} {$rP} = '{$post[$kP]}'
                        ";
                        break;
                    case "spac":
                        $Palabras = explode(' ', $post[$kP]);
                        foreach ($Palabras AS $pal) {
                            $where.= "
                                {$cnct} {$rP} LIKE '%{$pal}%'
                            ";
                            $cnct = "AND";
                        }
                        break;
                    default:
                        $where.= "
                            {$cnct} {$rP} LIKE '%{$post[$kP]}%'
                        ";
                        break;
                }
                $cnct = "AND";
            }
        }
        if (trim($where)) {
            $this->db->where("($where)");
        }
        /*
        if (trim($post) != '' and $post != '0') {
            $post = str_replace('%20', ' ', $post);
            $aFiltros = explode(' ', $post);
            $where = "";
            $cnct = "";
            foreach ($aFiltros AS $f) {
                $where.= "
                    {$cnct} (
                        CONCAT(TRIM(p.apellidos), ' ', TRIM(p.nombres)) LIKE '%{$f}%' OR
                        CONCAT(TRIM(m.apellidos), ' ', TRIM(m.nombres)) LIKE '%{$f}%' OR
                        TRIM(os.abreviacion) LIKE '%{$f}%' OR
                        TRIM(e.nombre) LIKE '%{$f}%'
                    )
                ";
                $cnct = "AND";
            }
            $this->db->where(
                "($where)"
            );
            #print "<pre>{$where}</pre>";
        }
        */
    }

    function obtDejaDepositoSuma($date1, $date2, $post)
    {
        $suma1 = 0;
        $this->db
            ->select('SUM(ts.trajo_arancel) AS Suma')
            ->from('turnos AS t')
            ->join('pacientes AS p', 't.id_pacientes = p.id_pacientes', 'left')
            ->join('turnos_estados AS te', 't.id_turnos_estados= te.id_turnos_estados', 'left')
            ->join('turnos_estudios AS ts', 'ts.id_turnos = t.id_turnos', 'left')
            ->join('medicos AS m', 'ts.id_medicos = m.id_medicos', 'left')
            ->join('obras_sociales AS os', 'ts.id_obras_sociales = os.id_obras_sociales', 'left')
            ->join('estudios AS e', 'ts.id_estudios = e.id_estudios', 'left')
        ;
        $this->_filtroListado($date1, $date2, $post);
        $query = $this->db
            ->get()
            ->result_array()
        ;
        #print "<pre>".$this->db->last_query()."</pre>";
        if (count($query) > 0) {
            $suma1+= $query[0]['Suma'];
        }

        $suma2 = 0;
        $this->db
            ->select('SUM(ts.deja_deposito) AS Suma')
            ->from('turnos AS t')
            ->join('pacientes AS p', 't.id_pacientes = p.id_pacientes', 'left')
            ->join('turnos_estados AS te', 't.id_turnos_estados= te.id_turnos_estados', 'left')
            ->join('turnos_estudios AS ts', 'ts.id_turnos = t.id_turnos', 'left')
            ->join('medicos AS m', 'ts.id_medicos = m.id_medicos', 'left')
            ->join('obras_sociales AS os', 'ts.id_obras_sociales = os.id_obras_sociales', 'left')
            ->join('estudios AS e', 'ts.id_estudios = e.id_estudios', 'left')
        ;
        $this->_filtroListado($date1, $date2, $post);
        $query = $this->db
            ->get()
            ->result_array()
        ;
        if (count($query) > 0) {
            $suma2+= $query[0]['Suma'];
        }

        return array($suma1, $suma2);
    }

    function obtenerListadoCount($date1, $date2, $post)
    {
        $this->db
            ->select('COUNT(t.id_turnos) AS Count')
            ->from('turnos AS t')
            ->join('pacientes AS p', 't.id_pacientes = p.id_pacientes', 'left')
            ->join('turnos_estados AS te', 't.id_turnos_estados= te.id_turnos_estados', 'left')
            ->join('turnos_estudios AS ts', 'ts.id_turnos = t.id_turnos', 'left')
            ->join('medicos AS m', 'ts.id_medicos = m.id_medicos', 'left')
            ->join('obras_sociales AS os', 'ts.id_obras_sociales = os.id_obras_sociales', 'left')
            ->join('estudios AS e', 'ts.id_estudios = e.id_estudios', 'left')
        ;
        $this->_filtroListado($date1, $date2, $post);
        $query = $this->db
            ->where('ts.estado', 1)
            ->get()
            ->result_array()
        ;
        return $query[0]['Count'];
    }

    function obtCantidadDeOrdenes($date1, $date2, $post)
    {
        $this->db
            ->select("
                COUNT(ts.trajo_orden) AS Count
            ")
            ->from('turnos AS t')
            ->join('pacientes AS p', 't.id_pacientes = p.id_pacientes', 'left')
            ->join('turnos_estados AS te', 't.id_turnos_estados= te.id_turnos_estados', 'left')
            ->join('turnos_estudios AS ts', 'ts.id_turnos = t.id_turnos', 'left')
            ->join('medicos AS m', 'ts.id_medicos = m.id_medicos', 'left')
            ->join('obras_sociales AS os', 'ts.id_obras_sociales = os.id_obras_sociales', 'left')
            ->join('estudios AS e', 'ts.id_estudios = e.id_estudios', 'left')
        ;
        $this->_filtroListado($date1, $date2, $post);
        $query1 = $this->db
            ->where('ts.trajo_orden', 1)
            ->where('ts.estado', 1)
            ->order_by('ts.estado DESC, t.fecha, t.desde, t.hasta, t.id_turnos')
            ->get()
            ->result_array()
        ;

        $this->db
            ->select("
                COUNT(ts.trajo_pedido) AS Count
            ")
            ->from('turnos AS t')
            ->join('pacientes AS p', 't.id_pacientes = p.id_pacientes', 'left')
            ->join('turnos_estados AS te', 't.id_turnos_estados= te.id_turnos_estados', 'left')
            ->join('turnos_estudios AS ts', 'ts.id_turnos = t.id_turnos', 'left')
            ->join('medicos AS m', 'ts.id_medicos = m.id_medicos', 'left')
            ->join('obras_sociales AS os', 'ts.id_obras_sociales = os.id_obras_sociales', 'left')
            ->join('estudios AS e', 'ts.id_estudios = e.id_estudios', 'left')
        ;
        $this->_filtroListado($date1, $date2, $post);
        $query2 = $this->db
            ->where('ts.trajo_pedido', 1)
            ->where('ts.estado', 1)
            ->order_by('ts.estado DESC, t.fecha, t.desde, t.hasta, t.id_turnos')
            ->get()
            ->result_array()
        ;

        return array($query1[0]['Count'], $query2[0]['Count']);
    }

    function obtenerListado($date1, $date2, $post)
    {
        $this->db
            ->select("
                t.*,
                LEFT(t.desde, 5) AS hora,
                CONCAT(p.apellidos, ', ', p.nombres) AS pacientes,
                CONCAT(m.saludo, ' ', m.apellidos, ', ', m.nombres) AS medicos,
                os.abreviacion AS obras_sociales,
                e.nombre AS estudios,
                e.codigopractica,
                ts.*,
                te.nombre AS turnos_estados
            ")
            ->from('turnos AS t')
            ->join('pacientes AS p', 't.id_pacientes = p.id_pacientes', 'left')
            ->join('turnos_estados AS te', 't.id_turnos_estados= te.id_turnos_estados', 'left')
            ->join('turnos_estudios AS ts', 'ts.id_turnos = t.id_turnos', 'left')
            ->join('medicos AS m', 'ts.id_medicos = m.id_medicos', 'left')
            ->join('obras_sociales AS os', 'ts.id_obras_sociales = os.id_obras_sociales', 'left')
            ->join('estudios AS e', 'ts.id_estudios = e.id_estudios', 'left')
        ;
        $this->_filtroListado($date1, $date2, $post);
        $query = $this->db
            ->where('ts.estado', 1)
            ->order_by('ts.estado DESC, t.fecha, t.desde, t.hasta, t.id_turnos')
            ->get()
            ->result_array()
        ;
        #print "<pre>".($this->db->last_query())."</pre>";
        for ($i = 0; $i < count($query); $i++) {
            #$query[$i]['fechanac'] = date_view($query[$i]['fechanac']);
        }
        return $query;
    }
/*
    public function obtDiasSemanaDiagnostico($date1, $date2){
        $query = $this->db
            ->select('id_dias_semana')
            ->from('medicos_horarios')
            ->where_in('id_especialidades', explode(", ", ID_ESPECIALIDADES) . ', 33')
            ->where('estado', 1)
            ->group_by('id_dias_semana')
            ->order_by('id_dias_semana')
            ->get()
            ->result_array()
        ;
        $dias_semana = array();
        foreach ($query AS $itm){
            $val = (($itm['id_dias_semana'] + 5) % 7) + 1;
            $dias_semana[$val] = $val;
        }
        sort($dias_semana);

        $while_condition = true;
        $i = -1;
        while ($while_condition) {
            $dataView['ayer'] = "{$i} day";
            $dia = date("N", strtotime($dataView['ayer'], strtotime($date)));
            $while_condition = !in_array($dia, $dias_semana);
            $i--;
        }

        $while_condition = true;
        $i = 1;
        while ($while_condition) {
            $dataView['mana'] = "{$i} day";
            $dia = date("N", strtotime($dataView['mana'], strtotime($date)));
            $while_condition = !in_array($dia, $dias_semana);
            $i++;
        }

        return $dataView;
    }
*/
    public function obtMedicos()
    {
        $ID_ESPECIALIDADES = ID_ESPECIALIDADES;
        $query = <<<SQL
            SELECT
                m.*
            FROM
                medicos AS m
            INNER JOIN
                medicos_especialidades AS me
                ON me.id_medicos = m.id_medicos
            WHERE
                m.estado = 1 AND
                me.estado = 1 AND
                me.id_especialidades IN ({$ID_ESPECIALIDADES}) AND
                me.id_medicos != 205
            GROUP BY
                m.id_medicos
            ORDER BY
                m.apellidos,
                m.nombres
SQL;
        return $this->db->query($query)->result_array();
    }

    public function buscarPacientes($nro_documento)
    {
        $query = <<<SQL
            SELECT
                p.*
            FROM
                pacientes AS p
            WHERE
                p.estado = 1 AND
                p.nro_documento = '{$nro_documento}'
            ORDER BY
                p.apellidos,
                p.nombres
SQL;
        return $this->db->query($query)->result_array();
    }

    public function obtEstudios()
    {
        $query = <<<SQL
            SELECT
                e.*
            FROM
                estudios AS e
            WHERE
                e.estado = 1
            ORDER BY
                e.nombre
SQL;
        return $this->db->query($query)->result_array();
    }

    public function obtEspecialidadDeMedico($id_medico)
    {
        $ID_ESPECIALIDADES = ID_ESPECIALIDADES;
        $query = <<<SQL
            SELECT
                e.*
            FROM
                medicos_especialidades AS me
            INNER JOIN
                especialidades AS e
                ON me.id_especialidades = e.id_especialidades
            WHERE
                me.id_medicos = '{$id_medico}' AND
                me.estado = 1 AND
                me.id_especialidades IN ({$ID_ESPECIALIDADES}) AND
                e.estado = 1
            GROUP BY
                me.id_medicos,
                me.id_especialidades
SQL;
        return $this->db->query($query)->result_array();
    }

    public function obtMedicosConMatriculas()
    {
        $query = <<<SQL
            SELECT
                m.*
            FROM
                medicos AS m
            INNER JOIN
                medicos_especialidades AS me
                ON me.id_medicos = m.id_medicos
            WHERE
                m.estado = 1 AND
                m.matricula > 0 AND
                me.estado = 1
            GROUP BY
                m.id_medicos
            ORDER BY
                m.apellidos,
                m.nombres
SQL;
        return $this->db->query($query)->result_array();
    }

    public function obtObrasSociales()
    {
        $query = <<<SQL
            SELECT
                *
            FROM
                obras_sociales AS os
            WHERE
                os.estado = 1
            ORDER BY
                os.abreviacion
SQL;
        return $this->db->query($query)->result_array();
    }

    public function obtDiagnosticosExport($date1, $date2, $post)
    {
        $this->db
            ->select("
                '0' AS orden,
                CONCAT(p.apellidos, ', ', p.nombres) AS pacientes,
                'CIAC' AS presentador,
                CONCAT(m.saludo, ' ', m.apellidos, ', ', m.nombres) AS medicos,
                os.abreviacion AS obras_sociales,
                t.fecha,
                ts.nro_orden,
                ts.nro_afiliado,
                e.nombre,
                e.codigopractica,
                ts.cantidad,
                ts.tipo,
                ts.trajo_pedido,
                ts.trajo_orden,
                ts.trajo_arancel,
                ts.deja_deposito,
                ts.matricula_derivacion,
                ts.observaciones
            ")
            ->from('turnos AS t')
            ->join('pacientes AS p', 't.id_pacientes = p.id_pacientes', 'left')
            ->join('turnos_estados AS te', 't.id_turnos_estados= te.id_turnos_estados', 'left')
            ->join('turnos_estudios AS ts', 'ts.id_turnos = t.id_turnos', 'left')
            ->join('medicos AS m', 'ts.id_medicos = m.id_medicos', 'left')
            ->join('obras_sociales AS os', 'ts.id_obras_sociales = os.id_obras_sociales', 'left')
            ->join('estudios AS e', 'ts.id_estudios = e.id_estudios', 'left')
        ;
        $this->_filtroListado($date1, $date2, $post);
        $query = $this->db
            ->where('ts.estado', 1)
            ->order_by('ts.estado DESC, t.fecha, t.desde, t.hasta, t.id_turnos')
            ->get()
            ->result_array()
        ;
        for ($i = 0; $i < count($query); $i++) {
            $query[$i]['orden'] = $i + 1;
            switch ($query[$i]['tipo']) {
                case '1': $query[$i]['tipo'] = 'A'; break;
                case '2': $query[$i]['tipo'] = 'I'; break;
                default: $query[$i]['tipo'] = ''; break;
            }
            switch ($query[$i]['trajo_pedido']) {
                case '1': $query[$i]['trajo_pedido'] = 'Si'; break;
                case '2': $query[$i]['trajo_pedido'] = 'No'; break;
                default: $query[$i]['trajo_pedido'] = ''; break;
            }
            switch ($query[$i]['trajo_orden']) {
                case '1': $query[$i]['trajo_orden'] = 'Si'; break;
                case '2': $query[$i]['trajo_orden'] = 'No'; break;
                default: $query[$i]['trajo_orden'] = ''; break;
            }
            if ($query[$i]['trajo_arancel'] > 0) {
                $query[$i]['trajo_arancel'] = "{$query[$i]['trajo_arancel']}";
            }
            if ($query[$i]['deja_deposito'] > 0) {
                $query[$i]['deja_deposito'] = "{$query[$i]['deja_deposito']}";
            }
        }
        return $query;
    }

    // IMPACTOS EN LA BASE DE DATOS

    function guardar_agregar($post)
    {
        #$post['fechanac'] = date_db($post['fechanac']);
        $this->db->insert('turnos', $post);
        return $this->db->insert_id();
    }

    function guardar_modificar($post, $id = null)
    {
        #$post['fechanac'] = date_db($post['fechanac']);
        $this->db
            ->where('id_turnos', $id)
            ->update('turnos', $post)
        ;
    }

    function guardar_eliminar($id)
    {
        $this->db
             ->where('id_turnos', $id)
             ->delete('turnos')
        ;
    }

    function guardarDiagnostico($post, $id_usuario = null)
    {
        $id_turnos_estudios = $post['id_turnos_estudios'];
        unset($post['id_turnos_estudios']);

        $post['fecha_presentacion'] = implode("-", array_reverse(explode("/", $post['fecha_presentacion'])));

        $deja_deposito = $this->db
            ->select('deja_deposito')
            ->from('turnos_estudios')
            ->where('id_turnos_estudios', $id_turnos_estudios)
            ->limit(1)
            ->get()
            ->result_array()
        ;

        $this->db
            ->where('id_turnos_estudios', $id_turnos_estudios)
            ->update('turnos_estudios', $post)
        ;

        $post['deja_deposito_fecha'] = date("Y-m-d");
        if (count($deja_deposito) > 0) {
            $post['deja_deposito_diferencia'] =
                $post['deja_deposito'] -
                $deja_deposito[0]['deja_deposito']
            ;
        } else {
            $post['deja_deposito_diferencia'] = 0;
        }

        unset($post['id_estudios']);
        $this->db
            ->insert(
                'turnos_estudios_historicos',
                array_merge(
                    $post,
                    array(
                        'id_turnos_estudios' => $id_turnos_estudios,
                        'id_usuarios' => $id_usuario,
                        'fechahora' => date("Y-m-d H:i:s")
                    )
                )
            )
        ;

        $query = $this->db
            ->from('turnos_estudios')
            ->where('id_turnos_estudios', $id_turnos_estudios)
            ->limit(1)
            ->get()
            ->result_array()
        ;
        if (count($query) == 1) {
            $query_est = $this->db
                ->from('estudios')
                ->where('id_estudios', $query[0]['id_estudios'])
                ->get()
                ->result_array()
            ;
            if (count($query_est) > 0) {
                $query[0]['id_estudios'] = utf8_encode(ucwords(lower(trim(utf8_decode(
                    $query_est[0]['nombre']
                )))));
                $query[0]['codigopractica'] = $query_est[0]['codigopractica'];
            } else {
                $query[0]['id_estudios'] = '---';
                $query[0]['codigopractica'] = '';
            }

            $query_med = $this->db
                ->from('medicos')
                ->where('id_medicos', $query[0]['id_medicos'])
                ->get()
                ->result_array()
            ;
            if (count($query_med) > 0) {
                $query[0]['id_medicos'] = utf8_encode(ucwords(upper(trim(utf8_decode(
                    $query_med[0]['saludo'].' '.$query_med[0]['apellidos'].', '.$query_med[0]['nombres']
                )))));
            } else {
                $query[0]['id_medicos'] = '---';
            }

            $query_os = $this->db
                ->from('obras_sociales')
                ->where('id_obras_sociales', $query[0]['id_obras_sociales'])
                ->get()
                ->result_array()
            ;
            if (count($query_os) > 0) {
                $query[0]['id_obras_sociales'] = $query_os[0]['abreviacion'];
            } else {
                $query[0]['id_obras_sociales'] = '---';
            }

            $query[0]['fecha_presentacion'] = date("d/m/Y", strtotime($query[0]['fecha_presentacion']));

            if (trim($query[0]['nro_orden']) == '') {
                $query[0]['nro_orden'] = '---';
            }

            if (trim($query[0]['nro_afiliado']) == '') {
                $query[0]['nro_afiliado'] = '---';
            }

            if (trim($query[0]['cantidad']) == '') {
                $query[0]['cantidad'] = '---';
            }

            if ($query[0]['tipo'] == '1') {
                $query[0]['tipo'] = 'A';
            } elseif ($query[0]['tipo'] == '2') {
                $query[0]['tipo'] = 'I';
            } else {
                $query[0]['tipo'] = '---';
            }

            if ($query[0]['trajo_pedido'] == '1') {
                $query[0]['trajo_pedido'] = 'TP';
            } elseif ($query[0]['trajo_pedido'] == '2') {
                $query[0]['trajo_pedido'] = 'No';
            } else {
                $query[0]['trajo_pedido'] = '---';
            }

            if ($query[0]['trajo_orden'] == '1') {
                $query[0]['trajo_orden'] = 'TO';
            } elseif ($query[0]['trajo_orden'] == '2') {
                $query[0]['trajo_orden'] = 'No';
            } else {
                $query[0]['trajo_orden'] = '---';
            }

            if ($query[0]['trajo_arancel'] > 0) {
                $query[0]['trajo_arancel'] = "\$&nbsp;{$query[0]['trajo_arancel']}";
            } else {
                $query[0]['trajo_arancel'] = '---';
            }

            if ($query[0]['deja_deposito'] > 0) {
                $query[0]['deja_deposito'] = "\$&nbsp;{$query[0]['deja_deposito']}";
            } else {
                $query[0]['deja_deposito'] = '---';
            }

            if (trim($query[0]['matricula_derivacion']) == '') {
                $query[0]['matricula_derivacion'] = '---';
            }
            return $query[0];
        } else {
            return array();
        }
    }

    public function agregarTurno($vcDuracionTurno, $post)
    {
        if ($post['fecha'] != '') {
            $post['fecha'] = implode("-", array_reverse(explode("/", $post['fecha'])));
        }
        if ($post['fecha_presentacion'] != '') {
            $post['fecha_presentacion'] = implode("-", array_reverse(explode("/", $post['fecha_presentacion'])));
        }
        if ($post['desde'] != '') {
            $post['hasta'] = horaMM($post['desde'], $vcDuracionTurno);
        }
        if ($this->session->userdata('ID_USUARIO') != '') {
            $post['id_usuarios'] = $this->session->userdata('ID_USUARIO');
        } else {
            $post['id_usuarios'] = '0';
        }
        $post['id_pacientes'] = $this->buscarPacientes($post['id_pacientes']);
        if (count($post['id_pacientes']) > 0) {
            $post['id_pacientes'] = $post['id_pacientes'][0]['id_pacientes'];
        } else {
            $post['id_pacientes'] = '0';
        }
        $dataInsertTurno['id_medicos'] = $post['id_medicos'];
        $dataInsertTurno['id_especialidades'] = $post['id_especialidades'];
        $dataInsertTurno['id_pacientes'] = $post['id_pacientes'];
        $dataInsertTurno['id_turnos_estados'] = '7';
        $dataInsertTurno['fecha'] = $post['fecha'];
        $dataInsertTurno['desde'] = $post['desde'];
        $dataInsertTurno['hasta'] = $post['hasta'];
        $dataInsertTurno['id_turnos_tipos'] = '2';
        $dataInsertTurno['estado'] = '1';
        $dataInsertTurno['tipo_usuario'] = 'U';
        $dataInsertTurno['id_usuarios'] = $post['id_usuarios'];
        $dataInsertTurno['fecha_alta'] = date("Y-m-d");
        $dataInsertTurno['hora_alta'] = date("H:m:s");
        $this->db->insert('turnos', $dataInsertTurno);
        $id_turnos = $this->db->insert_id();

        $dataInsertTurnosEstudios['id_turnos'] = $id_turnos;
        $dataInsertTurnosEstudios['id_estudios'] = $post['id_estudios'];
        $dataInsertTurnosEstudios['estado'] = '1';
        $dataInsertTurnosEstudios['id_medicos'] = $post['id_medicos'];
        $dataInsertTurnosEstudios['id_obras_sociales'] = $post['id_obras_sociales'];
        $dataInsertTurnosEstudios['fecha_presentacion'] = $post['fecha_presentacion'];
        $dataInsertTurnosEstudios['nro_orden'] = $post['nro_orden'];
        $dataInsertTurnosEstudios['nro_afiliado'] = $post['nro_afiliado'];
        $dataInsertTurnosEstudios['cantidad'] = $post['cantidad'];
        $dataInsertTurnosEstudios['tipo'] = $post['tipo'];
        $dataInsertTurnosEstudios['trajo_pedido'] = $post['trajo_pedido'];
        $dataInsertTurnosEstudios['trajo_orden'] = $post['trajo_orden'];
        $dataInsertTurnosEstudios['trajo_arancel'] = $post['trajo_arancel'];
        $dataInsertTurnosEstudios['deja_deposito'] = $post['deja_deposito'];
        $dataInsertTurnosEstudios['matricula_derivacion'] = $post['matricula_derivacion'];
        $dataInsertTurnosEstudios['observaciones'] = $post['observaciones'];
        $this->db->insert('turnos_estudios', $dataInsertTurnosEstudios);
    }

    public function getUsuario($id_usuarios)
    {
        $query = $this->db
            ->from('usuarios')
            ->where('id_usuarios', $id_usuarios)
            ->limit(1)
            ->get()
            ->result_array()
        ;
        return $query[0];

    }

}

//EOF Diagnostico_model.php

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

    function obtenerPaginacion($date)
    {
        $config = array(
            'base_url' => base_url().$this->router->fetch_class().'/listado/'.$date.'/',
            'total_rows' => $this->obtenerListadoCount($date),
            'per_page' => 50
        );
        $this->pagination->initialize($config);
        return array(
            'pagination_config' => $config,
            'pagination_links' => $this->pagination->create_links()
        );
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

    function obtenerListadoCount($date)
    {
        $query = $this->db
            ->select('COUNT(t.id_turnos) AS Count')
            ->from('turnos AS t')
            ->join('turnos_estudios AS ts', 'ts.id_turnos = t.id_turnos')
            ->join('estudios AS e', 'ts.id_estudios = e.id_estudios')
            ->where('t.id_especialidades', ID_ESPECIALIDADES)
            ->where('t.estado', 1)
            ->where('t.fecha', $date)
            ->where('e.codigopractica >', 0)
            ->get()
            ->result_array()
        ;
        return $query[0]['Count'];
    }

    function obtenerListado($date, $limit, $offset)
    {
        $query = $this->db
            ->select("
                t.*,
                LEFT(t.desde, 5) AS hora,
                CONCAT(p.apellidos, ', ', p.nombres) AS pacientes,
                CONCAT(m.saludo, ' ', m.apellidos, ', ', m.nombres) AS medicos,
                os.abreviacion AS obras_sociales,
                e.nombre AS estudios,
                ts.*,
                te.nombre AS turnos_estados,
                te.color
            ")
            ->from('turnos AS t')
            ->join('pacientes AS p', 't.id_pacientes = p.id_pacientes', 'left')
            ->join('turnos_estados AS te', 't.id_turnos_estados= te.id_turnos_estados', 'left')
            ->join('turnos_estudios AS ts', 'ts.id_turnos = t.id_turnos', 'left')
            ->join('medicos AS m', 'ts.id_medicos = m.id_medicos', 'left')
            ->join('obras_sociales AS os', 'ts.id_obras_sociales = os.id_obras_sociales', 'left')
            ->join('estudios AS e', 'ts.id_estudios = e.id_estudios', 'left')
            ->where('t.id_especialidades', ID_ESPECIALIDADES)
            ->where('t.estado', 1)
            ->where('t.fecha', $date)
            ->where('e.codigopractica >', 0)
            ->order_by('ts.estado DESC, t.fecha, t.desde, t.hasta, t.id_turnos')
            ->limit($limit, $offset)
            ->get()
            ->result_array()
        ;
        for ($i = 0; $i < count($query); $i++) {
            #$query[$i]['fechanac'] = date_view($query[$i]['fechanac']);
        }
        return $query;
    }

    public function obtDiasSemanaDiagnostico($date){
        $query = $this->db
            ->select('id_dias_semana')
            ->from('medicos_horarios')
            ->where('id_especialidades', ID_ESPECIALIDADES)
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

    public function obtMedicos()
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
                me.estado = 1 AND
                me.id_especialidades IN (63, 64, 65, 68, 66)
            GROUP BY
                m.id_medicos
            ORDER BY
                m.apellidos,
                m.nombres
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

    public function obtDiagnosticosExport($year, $month)
    {
        $fecha = $year.'-'.$month.'-%';
        $query = $this->db
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
                ts.matricula_derivacion
            ")
            ->from('turnos AS t')
            ->join('pacientes AS p', 't.id_pacientes = p.id_pacientes', 'left')
            ->join('turnos_estados AS te', 't.id_turnos_estados= te.id_turnos_estados', 'left')
            ->join('turnos_estudios AS ts', 'ts.id_turnos = t.id_turnos', 'left')
            ->join('medicos AS m', 'ts.id_medicos = m.id_medicos', 'left')
            ->join('obras_sociales AS os', 'ts.id_obras_sociales = os.id_obras_sociales', 'left')
            ->join('estudios AS e', 'ts.id_estudios = e.id_estudios', 'left')
            ->where('t.estado', 1)
            ->where('YEAR(t.fecha)', $year)
            ->where('MONTH(t.fecha)', $month)
            ->where('e.codigopractica >', 0)
            ->where_in('ts.estado', array(1, 2, 7))
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
                $query[$i]['trajo_arancel'] = "\$&nbsp;{$query[$i]['trajo_arancel']}";
            }
            if ($query[$i]['deja_deposito'] > 0) {
                $query[$i]['deja_deposito'] = "\$&nbsp;{$query[$i]['deja_deposito']}";
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

    function saveDiagnostico($post)
    {
        $id_turnos_estudios = $post['id_turnos_estudios'];
        unset($post['id_turnos_estudios']);
        $post['fecha_presentacion'] = implode("-", array_reverse(explode("/", $post['fecha_presentacion'])));
        $this->db
            ->where('id_turnos_estudios', $id_turnos_estudios)
            ->update('turnos_estudios', $post)
        ;
        $query = $this->db
            ->from('turnos_estudios')
            ->where('id_turnos_estudios', $id_turnos_estudios)
            ->limit(1)
            ->get()
            ->result_array()
        ;
        if (count($query) == 1) {
            $query_med = $this->db
                ->from('medicos')
                ->where('id_medicos', $query[0]['id_medicos'])
                ->get()
                ->result_array()
            ;
            if (count($query_med) > 0) {
                $query[0]['id_medicos'] = utf8_encode(ucwords(lower(trim(utf8_decode(
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

}

//EOF Diagnostico_model.php

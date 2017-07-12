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

    function save($post)
    {
        if ($post['name'] == 'fecha_presentacion') {
            $post['value'] = implode("-", array_reverse(explode("/", $post['value'])));
        }
        $this->db
            ->where('id_turnos_estudios', $post['id'])
            ->update(
                'turnos_estudios',
                array(
                    $post['name'] => $post['value']
                )
            )
        ;
        $query = $this->db
            ->select($post['name'])
            ->from('turnos_estudios')
            ->where('id_turnos_estudios', $post['id'])
            ->limit(1)
            ->get()
            ->result_array()
        ;
        if (count($query) == 1) {
            switch ($post['name']) {
                case "id_medicos":
                    $query = $this->db
                        ->from('medicos')
                        ->where('id_medicos', $query[0][$post['name']])
                        ->get()
                        ->result_array()
                    ;
                    if (count($query) > 0) {
                        return utf8_encode(ucwords(lower(trim(utf8_decode(
                            $query[0]['saludo'].' '.$query[0]['apellidos'].', '.$query[0]['nombres']
                        )))));
                    } else {
                        return '---';
                    }
                    break;
                case "id_obras_sociales":
                    $query = $this->db
                        ->from('obras_sociales')
                        ->where('id_obras_sociales', $query[0][$post['name']])
                        ->get()
                        ->result_array()
                    ;
                    if (count($query) > 0) {
                        return $query[0]['abreviacion'];
                    } else {
                        return '---';
                    }
                    break;
                case "tipo":
                    if ($query[0][$post['name']] == '1') {
                        return 'A';
                    } elseif ($query[0][$post['name']] == '2') {
                        return 'I';
                    } else {
                        return '---';
                    }
                    break;
                case "trajo_pedido":
                    if ($query[0][$post['name']] == '1') {
                        return 'TP';
                    } elseif ($query[0][$post['name']] == '2') {
                        return 'No';
                    } else {
                        return '---';
                    }
                    break;
                case "trajo_orden":
                    if ($query[0][$post['name']] == '1') {
                        return 'TO';
                    } elseif ($query[0][$post['name']] == '2') {
                        return 'No';
                    } else {
                        return '---';
                    }
                    break;
                case "trajo_arancel":
                case "deja_deposito":
                    if ($query[0][$post['name']] > 0) {
                        return "\$&nbsp;{$query[0][$post['name']]}";
                    } else {
                        return '---';
                    }
                    break;
                case "fecha_presentacion":
                    return date("d/m/Y", strtotime($query[0][$post['name']]));
                    break;
                default:
                    if (trim($query[0][$post['name']]) != '') {
                        return $query[0][$post['name']];
                    } else {
                        return '---';
                    }
                    break;
            }
        } else {
            return '---';
        }
    }

}

//EOF Diagnostico_model.php

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
            ->where('t.id_especialidades', 33)
            ->where('t.estado', 1)
            ->where('t.fecha', $date)
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
                CONCAT(m.apellidos, ', ', m.nombres) AS medicos,
                os.abreviacion AS obras_sociales,
                te.nombre AS turnos_estados,
                te.color
            ")
            ->from('turnos AS t')
            ->join('pacientes AS p', 't.id_pacientes = p.id_pacientes', 'left')
            ->join('turnos_estados AS te', 't.id_turnos_estados= te.id_turnos_estados', 'left')
            ->join('medicos AS m', 't.id_medicos = m.id_medicos', 'left')
            ->join('obras_sociales AS os', 't.id_obras_sociales = os.id_obras_sociales', 'left')
            ->where('t.id_especialidades', 33)
            ->where('t.estado', 1)
            ->where('t.fecha', $date)
            ->order_by('t.fecha, t.desde, t.hasta, t.id_turnos')
            ->limit($limit, $offset)
            ->get()
            ->result_array()
        ;
        for ($i = 0; $i < count($query); $i++) {
            #$query[$i]['fechanac'] = date_view($query[$i]['fechanac']);
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

}

//EOF Diagnostico_model.php

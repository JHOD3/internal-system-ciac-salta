<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
/**
 * Turnero_model
 *
 * @package ciac
 * @author DiegoG
 * @copyright 2016
 * @version $Id$
 * @access public
 */
class Turnero_model extends CI_model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function obtEspecialidad($id_especialidades)
    {
        $query = $this->db
            ->from('especialidades')
            ->where('id_especialidades', $id_especialidades)
            ->get()
            ->result_array()
        ;
        return array_shift($query);
    }

    public function obtEspecialidades()
    {
        $query = $this->db
            ->from('especialidades AS e')
            ->join('medicos_especialidades AS me', 'me.id_especialidades = e.id_especialidades', 'left')
            ->join('medicos AS m', 'm.id_medicos = me.id_medicos', 'left')
            ->where('e.estado', 1)
            ->where('m.estado', 1)
            ->where('me.estado', 1)
            ->group_by('e.id_especialidades')
            ->order_by('e.nombre')
            ->get()
            ->result_array()
        ;
        return $query;
    }

    public function obtMedico($id_medicos)
    {
        $query = $this->db
            ->from('medicos')
            ->where('id_medicos', $id_medicos)
            ->limit(1)
            ->get()
            ->result_array()
        ;
        return array_shift($query);
    }

    public function obtObrasSocialesDeMedico($id_medicos)
    {
        $query = $this->db
            ->select('os.id_obras_sociales, os.abreviacion, os.nombre, COUNT(p.id_pacientes) AS cantidad')
            ->from('medicos_obras_sociales AS mos')
            ->join('obras_sociales AS os', 'mos.id_obras_sociales = os.id_obras_sociales')
            ->join('pacientes AS p', 'p.id_obras_sociales = os.id_obras_sociales AND p.estado = 1', 'left')
            ->where('mos.id_medicos', $id_medicos)
            ->where('mos.estado', '1')
            ->where('os.estado', '1')
            ->group_by('mos.id_medicos_obras_sociales, os.id_obras_sociales')
            ->order_by('COUNT(p.id_pacientes) DESC')
            ->get()
            ->result_array()
        ;
        return $query;
    }

    public function obtObrasSociales()
    {
        $query = $this->db
            ->from('obras_sociales')
            ->where('estado', 1)
            ->order_by('abreviacion, nombre')
            ->get()
            ->result_array()
        ;
        return $query;
    }

    public function obtObrasSocialesDropDownMedico($id_medicos)
    {
        $query = $this->db
            ->from('obras_sociales')
            ->where('estado', 1)
            ->order_by('abreviacion, nombre')
            ->get()
            ->result_array()
        ;
        return $query;
    }

    public function obtObraSocial($id_obras_sociales)
    {
        $query = $this->db
            ->from('obras_sociales')
            ->where('id_obras_sociales', $id_obras_sociales)
            ->limit(1)
            ->get()
            ->result_array()
        ;
        return $query[0];
    }

    public function obtMedicos($id_especialidades)
    {
        $query = $this->db
            ->select('m.*')
            ->from('medicos AS m')
            ->join('medicos_especialidades AS me', 'me.id_medicos = m.id_medicos')
            ->where('m.estado', 1)
            ->where('me.estado', 1)
            ->where('me.id_especialidades', $id_especialidades)
            ->group_by('m.id_medicos')
            ->order_by('m.apellidos, m.nombres')
            ->get()
            ->result_array()
        ;
        return $query;
    }

    public function obtMedicosPorEspecialidad($tipo, $filter)
    {
        $this->db
            ->select('m.*, e.*')
            ->from('medicos AS m')
            ->join('medicos_especialidades AS me', 'me.id_medicos = m.id_medicos', 'right')
            ->join('especialidades AS e', 'me.id_especialidades = e.id_especialidades', 'right')
            ->join('medicos_horarios AS tt', 'tt.id_medicos = m.id_medicos AND tt.id_especialidades = e.id_especialidades', 'join')
            ->where('m.estado', 1)
            ->where('me.estado', 1)
            ->where('e.estado', 1)
            ->where('tt.estado', 1)
            ->where_in('tt.id_turnos_tipos', array(1, 2, 3, 4, 5, 6, 7, 8))
            ->group_by('e.nombre, m.apellidos, m.nombres')
            ->order_by('e.nombre, m.apellidos, m.nombres')
        ;
        if (trim($tipo) and trim($filter)) {
            if (in_array($tipo, array('dr', 'dra', 'lic'))) {
                $search = trim($tipo).". ".trim(str_replace("-", " ", $filter));
                $where = <<<SQL
                    CONCAT(
                        TRIM(m.saludo),
                        ' ',
                        TRIM(m.apellidos),
                        ' ',
                        TRIM(m.nombres)
                    ) LIKE '{$search}'
SQL;
            } else {
                $search = trim(str_replace("-", " ", $filter));
                $where = <<<SQL
                    TRIM(e.nombre) LIKE '{$search}'
SQL;
            }
            $this->db->where($where);
        }
        $query = $this->db
            ->get()
            ->result_array()
        ;
        return $query;
    }

    public function obtDiasSemana(
        $id_especialidades,
        $id_medicos,
        $year,
        $month
    ){
        $query = $this->db
            ->select('id_dias_semana')
            ->from('medicos_horarios')
            ->where('id_especialidades', $id_especialidades)
            ->where('id_medicos', $id_medicos)
            ->where('estado', 1)
            ->where_not_in('id_turnos_tipos', array(9, 10))
            ->group_by('id_dias_semana')
            ->order_by('id_dias_semana')
            ->get()
            ->result_array()
        ;
        $result = array();
        foreach ($query AS $itm){
            $result[] = $itm['id_dias_semana'];
        }
        return $result;
    }

    public function obtDias($id_especialidades, $id_medicos, $year, $month){
        $proximoTurnoDisponible = $this->proximoTurnoDisponible(
            $id_especialidades,
            $id_medicos
        );
        $dias_semana = $this->obtDiasSemana(
            $id_especialidades,
            $id_medicos,
            $year,
            $month
        );
        $result = array();
        for ($i = 1; $i <= cal_days_in_month(CAL_GREGORIAN, $month, $year); $i++) {
            $id_dias_semana = date('w', strtotime($year."-".$month."-".($i<10?'0':'').$i));
            if ($id_dias_semana == 7) {
                $id_dias_semana = 1;
            } else {
                $id_dias_semana++;
            }
            if (
                in_array($id_dias_semana, $dias_semana) and
                $proximoTurnoDisponible <= $year."-".$month."-".($i<10?'0':'').$i
            ){
                $result[$i] = base_url()."index.php/turnero/calendar/{$id_especialidades}/{$id_medicos}/{$year}/{$month}/".($i<10?'0':'')."{$i}/";
            }
        }
        return $result;
    }

    public function obtHorarios(
        $id_especialidades,
        $id_medicos,
        $year,
        $month,
        $day
    ) {
        $id_dias_semana = date('w', strtotime($year."-".$month."-".$day));
        if ($id_dias_semana == 7) {
            $id_dias_semana = 1;
        } else {
            $id_dias_semana++;
        }
        $query = $this->db
            ->from('medicos_horarios')
            ->where('id_especialidades', $id_especialidades)
            ->where('id_medicos', $id_medicos)
            ->where('estado', 1)
            ->where('id_dias_semana', $id_dias_semana)
            ->where_not_in('id_turnos_tipos', array(9, 10))
            ->order_by('desde')
            ->get()
            ->result_array()
        ;
        return $query;
    }

    public function obtDiasHorarios($id_especialidades, $id_medicos, $year, $month){
        $query = $this->db
            ->select('ds.nombre, mh.desde, mh.hasta')
            ->from('medicos_horarios AS mh')
            ->join('dias_semana AS ds', 'ds.id_dias_semana = mh.id_dias_semana', 'left')
            ->where('mh.id_especialidades', $id_especialidades)
            ->where('mh.id_medicos', $id_medicos)
            ->where('mh.estado', 1)
            ->where_not_in('mh.id_turnos_tipos', array(9, 10))
            ->order_by('mh.id_dias_semana, mh.desde, mh.hasta')
            ->get()
            ->result_array()
        ;
        $txt = "";
        $dia = "";
        $slt = "";
        foreach ($query AS $row) {
            if ($dia != $row['nombre']) {
                $txt.= $slt;
                $txt.= ucwords(lower($row['nombre']));
                $txt.= " de ";
                $dia = $row['nombre'];
            } else {
                $txt.= " y de ";
            }
            $txt.= str_replace(":00", "", substr($row['desde'], 0 , 5));
            $txt.= " a ";
            $txt.= str_replace(":00", "", substr($row['hasta'], 0 , 5));
            $txt.= "hs";
            $slt = "<br />\n";
        }
        return $txt;
    }

    public function obtDuracionTurno($id_especialidades, $id_medicos)
    {
        $query = $this->db
            ->select('duracion_turno')
            ->from('medicos_especialidades')
            ->where('estado', 1)
            ->where('id_especialidades', $id_especialidades)
            ->where('id_medicos', $id_medicos)
            ->get()
            ->result_array()
        ;
        $query = array_shift($query);
        return $query['duracion_turno'];
    }

    public function obtTurnosReservados(
        $id_especialidades,
        $id_medicos,
        $year,
        $month,
        $day
    ) {
        $query = $this->db
            ->select('desde')
            ->from('turnos')
            ->where('id_especialidades', $id_especialidades)
            ->where('id_medicos', $id_medicos)
            ->where('estado', 1)
            ->where_in('id_turnos_estados', array(1, 2, 4, 7))
            ->where('fecha', $year."-".$month."-".$day)
            ->get()
            ->result_array()
        ;
        $result = array();
        foreach ($query AS $itm){
            $result[] = $itm['desde'];
        }
        return $result;
    }

    public function obtTurno($id_turnos) {
        $query = $this->db
            ->from('turnos AS t')
            ->where('t.id_turnos', $id_turnos)
            ->get()
            ->result_array()
        ;
        return count($query) > 0 ? $query[0] : null;
    }

    public function obtHorariosInhabilitados(
        $id_especialidades,
        $id_medicos,
        $year,
        $month,
        $day
    ) {
        $query = $this->db
            ->select('desde, hasta')
            ->from('horarios_inhabilitados')
            ->where('id_especialidades', $id_especialidades)
            ->where('id_medicos', $id_medicos)
            ->where('estado', 1)
            ->where('fecha', $year."-".$month."-".$day)
            ->get()
            ->result_array()
        ;
        return $query;
    }

    public function obtPaciente($nro_documento)
    {
        $query = $this->db
            ->from('pacientes')
            ->where('nro_documento', $nro_documento)
            ->where('estado', 1)
            ->limit(100)
            ->get()
            ->result_array()
        ;
        return $query;

    }

    public function obtPacienteById($id_pacientes)
    {
        $query = $this->db
            ->select('p.*, o.abreviacion')
            ->from('pacientes AS p')
            ->join('obras_sociales AS o', 'p.id_obras_sociales = o.id_obras_sociales', 'left')
            ->where('p.id_pacientes', $id_pacientes)
            ->limit(100)
            ->get()
            ->result_array()
        ;
        return count($query) > 0 ? $query[0] : null;
    }

    public function addTurno($post)
    {
        $this->db->trans_start();
            if ($post['id_pacientes'] == 0) {
                // ARMAR LOS DATOS DEL PACIENTE
                $insertPaciente = array(
                    'id_tipos_documentos' => '1',
                    'id_obras_sociales' => $post['id_obras_sociales'],
                    'apellidos' => upper($post['apellidos']),
                    'nombres' => upper($post['nombres']),
                    'telefonos' => $post['telefonos'],
                    'nro_documento' => $post['nro_documento'],
                    'domicilio' => '',
                    'email' => lower($post['email']),
                    'estado' => 1,
                    'id_obras_sociales_planes' => '0'
                );
                // BUSCO SI EXISTE EL PACIENTE Y LO TRAIGO
                $query = $this->db
                    ->select('id_pacientes')
                    ->from('pacientes')
                    ->where($insertPaciente)
                    ->limit(1)
                    ->get()
                    ->result_array()
                ;
                if (count($query) == 0) {
                    // SI NO EXISTE LO AGREGO
                    $this->db->insert(
                        'pacientes',
                        $insertPaciente
                    );
                    $post['id_pacientes'] = $this->db->insert_id();
                } else {
                    $post['id_pacientes'] = $query[0]['id_pacientes'];
                }
            } else {
                // SI EXISTE MODIFICO SU E-MAIL
                $this->db->update(
                    'pacientes',
                    array(
                        'email' => lower($post['email']),
                        'id_obras_sociales' => $post['id_obras_sociales']
                    ),
                    'id_pacientes = '.$post['id_pacientes']
                );
            }

            // BUSCAR LA DURACION DE UN TURNO SEGÚN MEDICO Y ESPECIALIDAD
            $query = $this->db
                ->select('duracion_turno')
                ->from('medicos_especialidades')
                ->where('id_especialidades', $post['id_especialidades'])
                ->where('id_medicos', $post['id_medicos'])
                ->where('estado', 1)
                ->get()
                ->result_array()
            ;
            $post['hasta'] = substr(
                horaMM(
                    $post['desde'],
                    $query[0]['duracion_turno']
                ),
                0,
                5
            );

            // ARMAR LOS DATOS DEL TURNO
            $insertTurno = array(
                'id_medicos' => $post['id_medicos'],
                'id_especialidades' => $post['id_especialidades'],
                'id_pacientes' => $post['id_pacientes'],
                'id_turnos_estados' => '1',
                'fecha' => $post['fecha'],
                'desde' => $post['desde'],
                'hasta' => $post['hasta'],
                'trae_orden' => '0',
                'trae_pedido' => '0',
                'arancel_diferenciado' => '0',
                'id_medicos_derivacion' => '0',
                'id_especialidades_derivacion' => '0',
                'es_derivacion_externa' => '0',
                'id_turnos_tipos' => '1',
                'estado' => '1',
                'tipo_usuario' => 'U',
                'consultorio' => '0',
                'id_usuarios' => '1'
            );
            // BUSCO SI YA OCUPARON EL TURNO
            $selectTurno = array(
                'id_medicos' => $post['id_medicos'],
                'id_especialidades' => $post['id_especialidades'],
                'id_pacientes !=' => $post['id_pacientes'],
                'fecha' => $post['fecha'],
                'desde' => $post['desde'],
                'hasta' => $post['hasta'],
                'estado' => '1'
            );
            $query = $this->db
                ->from('turnos')
                ->where($selectTurno)
                ->where_in('id_turnos_estados', array('1', '2', '7'))
                ->order_by('fecha', 'DESC')
                ->limit(1)
                ->get()
                ->result_array()
            ;
            if (count($query) > 0) {
                $this->db->trans_complete();
                return false;
            }

            // BUSCO SI EXISTE EL TURNO
            $query = $this->db
                ->select('id_turnos')
                ->from('turnos')
                ->where($insertTurno)
                ->limit(1)
                ->get()
                ->result_array()
            ;
            if (count($query) == 0) {
                // AGREGAR EL TURNO EN LA BD
                $insertTurno['fecha_alta'] = date("Y-m-d");
                $insertTurno['hora_alta'] = date("H:i:s");
                $this->db->insert('turnos', $insertTurno);
                $this->sendEmailTurnoReservado($post, $this->db->insert_id());
            }
        $this->db->trans_complete();
        return true;
    }

    public function cancelTurno($post)
    {
        // AGREGAR EL TURNO EN LA BD
        $this->db
            ->where('id_turnos', $post['id_turnos'])
            ->update(
                'turnos',
                array(
                    'id_turnos_estados' => '5'
                )
            )
        ;
    }

    public function proximoTurnoDisponible($id_especialidades, $id_medicos)
    {
        $day = date("Y-m-d");
        $total = 0;
        $max = 0;
        while ($total <= 0 and $max < 150) {
            $id_dias_semana = date('w', strtotime($day));
            if ($id_dias_semana == 7) {
                $id_dias_semana = 1;
            } else {
                $id_dias_semana++;
            }
            $vcDT = $vcDuracionTurno = $this->obtDuracionTurno(
                $id_especialidades,
                $id_medicos
            );
            $vcDT = explode(":", $vcDT);
            $vcDT = ((($vcDT[0] * 60) + $vcDT[1]) * 60) + $vcDT[2];
            $rango = $this->db
                ->select('id_dias_semana, desde, hasta')
                ->from('medicos_horarios')
                ->where('id_especialidades', $id_especialidades)
                ->where('id_medicos', $id_medicos)
                ->where('id_dias_semana', $id_dias_semana)
                ->where('estado', 1)
                ->where_not_in('id_turnos_tipos', array(9, 10))
                ->order_by('desde')
                ->get()
                ->result_array()
            ;
            $total = 0;
            for ($i = 0; $i < count($rango); $i++) {
                $desde = explode(":", $rango[$i]['desde']);
                $hasta = explode(":", $rango[$i]['hasta']);
                $total +=
                    (
                        ((($hasta[0] * 60) + $hasta[1]) * 60) + $hasta[2] -
                        ((($desde[0] * 60) + $desde[1]) * 60) + $desde[2]
                    ) /
                    $vcDT
                ;
            }
            if ($total > 0) {
                $dados = $this->obtTurnosReservados(
                    $id_especialidades,
                    $id_medicos,
                    date("Y", strtotime($day)),
                    date("m", strtotime($day)),
                    date("d", strtotime($day))
                );
                $total -= count($dados);
            }
            $day = date("Y-m-d", strtotime('+1 day', strtotime($day)));
            $max++;
        }
        return date("Y-m-d", strtotime('-1 day', strtotime($day)));
    }

    public function sendEmailTurnoReservado($post, $id_turnos)
    {
        $post['id_turnos'] = $id_turnos;
        $aData['post'] = $post;
        $aData['rsE'] = $this->obtEspecialidad(
            $post['id_especialidades']
        );
        $aData['rsM'] = $this->obtMedico(
            $post['id_medicos']
        );
        $aData['rsP'] = $this->obtPacienteById(
            $post['id_pacientes']
        );
        $to = $aData['rsP']['email'];
        $subject = "Turno Reservado | CIAC Salta";
        $this->load->library('calendar');
        $message = $this->load->view('emails/email_turnoreservado_view', $aData, true);
        $this->sendEmail($to, $subject, $message);
    }

    public function sendEmail($to, $subject, $message)
    {
        if ($to) {
            $this->load->library("email");
            $this->email->initialize(array(
                'useragent'   => 'ermes',
                'protocol'    => 'mail',
                'smtp_host'   => 'smtp.gmail.com',
                'smtp_port'   => '465',
                'smtp_user'   => 'noreply@ciacsalta.com.ar',
                'smtp_pass'   => 'norteciac1593',
                'smtp_crypto' => 'ssl',
                'mailtype'    => 'html',
                'charset'     => 'utf-8',
                'newline'     => '\r\n',
                'wordwrap'    => TRUE
            ));
            $this->email->from("noreply@ciacsalta.com.ar", "CIAC Salta");
            $this->email->to($to);
            $this->email->subject($subject);
            $this->email->message($message);
            if (!$this->email->send()){
                die($this->email->print_debugger());
            }
        }
    }

    public function getTurnosNext($next)
    {
        $query = $this->db
            ->select(
                "t.id_turnos, t.fecha, t.desde, t.fecha_alta, t.hora_alta, ".
                "e.id_especialidades, e.nombre AS especialidad, ".
                "m.id_medicos, m.saludo, m.nombres AS mednombres, m.apellidos AS medapellidos, ".
                "p.id_pacientes, p.apellidos AS pacapellidos, p.nombres AS pacnombres, p.email, p.telefonos, ".
                "o.nombre AS obrnombre"
            )
            ->from('turnos AS t')
            ->join('especialidades AS e', 't.id_especialidades = e.id_especialidades')
            ->join('medicos AS m', 't.id_medicos = m.id_medicos')
            ->join('pacientes AS p', 't.id_pacientes = p.id_pacientes')
            ->join('obras_sociales AS o', 'p.id_obras_sociales = o.id_obras_sociales')
            ->where('t.estado', 1)
            ->where('t.id_turnos_estados', 1)
            ->where('t.fecha', date("Y-m-d", strtotime($next)))
            ->where('t.desde LIKE', date("H", strtotime($next)).":%:%")
            ->where_not_in('p.email', array('', '-'))
            ->like('p.email', '@')
            ->order_by('t.desde')
            ->get()
            ->result_array()
        ;
        return $query;
    }

    public function getTurnosCanceladosPorElMedico($next)
    {
        $query = $this->db
            ->select('id_turnos')
            ->from('turnos_cambios_estados AS tce')
            ->where('id_turnos_estados_viejos', '1')
            ->where_in('id_turnos_estados_nuevos', array('4', '8'))
            ->where('tce.fecha', date("Y-m-d", strtotime($next)))
            ->where('tce.hora LIKE', date("H", strtotime($next)).":%:%")
            ->where('tce.estado', '1')
            ->order_by('tce.id_turnos_cambios_estados', 'DESC')
            ->get()
            ->result_array()
        ;
        if (count($query)) {
            $arr_id_turnos = array();
            foreach ($query AS $itm) {
                $arr_id_turnos[] = $itm['id_turnos'];
            }
            $query = $this->db
                ->select(
                    "t.id_turnos, t.fecha, t.desde, ".
                    "e.id_especialidades, e.nombre AS especialidad, ".
                    "m.id_medicos, m.saludo, m.nombres AS mednombres, m.apellidos AS medapellidos, ".
                    "p.id_pacientes, p.apellidos AS pacapellidos, p.nombres AS pacnombres, p.email, p.telefonos, ".
                    "o.nombre AS obrnombre"
                )
                ->from('turnos AS t')
                ->join('especialidades AS e', 't.id_especialidades = e.id_especialidades')
                ->join('medicos AS m', 't.id_medicos = m.id_medicos')
                ->join('pacientes AS p', 't.id_pacientes = p.id_pacientes')
                ->join('obras_sociales AS o', 'p.id_obras_sociales = o.id_obras_sociales')
                ->where('t.estado', 1)
                ->where_in('t.id_turnos', $arr_id_turnos)
                ->where_not_in('p.email', array('', '-'))
                ->like('p.email', '@')
                ->order_by('t.desde')
                ->get()
                ->result_array()
            ;
            return $query;
        } else {
            return array();
        }
    }

    public function obtMedEsp($post)
    {
        $med_esp = "";
        $query = $this->db
            ->from('especialidades')
            ->where('id_especialidades', $post['id_especialidades'])
            ->limit(1)
            ->get()
            ->result_array()
        ;
        if (count($query) > 0) {
            $med_esp = trim($query[0]['nombre']);
        }
        $query = $this->db
            ->from('medicos')
            ->where('id_medicos', $post['id_medicos'])
            ->limit(1)
            ->get()
            ->result_array()
        ;
        if (count($query) > 0) {
            if (strlen(trim($med_esp)) > 0) {
                $med_esp.= " - ";
            }
            $med_esp.= trim($query[0]['apellidos']).", ".trim($query[0]['apellidos']);
        }
        return $med_esp;
    }

}
//EOF Turnero_model.php
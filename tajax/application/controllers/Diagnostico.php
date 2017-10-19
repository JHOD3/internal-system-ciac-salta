<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Diagnostico extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model($this->router->fetch_class().'_model', 'Model');
    }

    /*
    function microtime_float()
    {
        list($useg, $seg) = explode(" ", microtime());
        return ((float)$useg + (float)$seg);
    }
    */

    public function agregar()
    {
        $dataView['post'] = $this->input->post();
        $dataView['medicos'] = $this->Model->obtMedicos();
        $dataView['estudios'] = $this->Model->obtEstudios();
        $this->load->view('diagnostico/Agregar_view', $dataView);
    }

    public function agregar_grilla(
        $fecha,
        $id_medicos = null,
        $id_especialidades = null
    ) {
        $dataView['id_especialidades'] = $id_especialidades;
        if ($id_medicos) {
            $dataView['especialidades'] = $this->Model->obtEspecialidadDeMedico($id_medicos);
            if ($id_especialidades) {
                $this->load->model('Turnero_model');
                $year = substr($fecha, 0, 4);
                $month = substr($fecha, 5, 2);
                $day = substr($fecha, 8, 2);
                $dataView['year'] = $year;
                $dataView['month'] = $month;
                $dataView['day'] = $day;
                $dataView['aHorarios'] = $this->turnero_model->obtHorarios(
                    $id_especialidades,
                    $id_medicos,
                    $year,
                    $month,
                    $day
                );
                $dataView['vcDuracionTurno'] = $this->turnero_model->obtDuracionTurno(
                    $id_especialidades,
                    $id_medicos
                );
                $dataView['aTurnosReservados'] = $this->turnero_model->obtTurnosReservados(
                    $id_especialidades,
                    $id_medicos,
                    $year,
                    $month,
                    $day
                );
                $dataView['aHorariosInhabilitados'] = $this->turnero_model->obtHorariosInhabilitados(
                    $id_especialidades,
                    $id_medicos,
                    $year,
                    $month,
                    $day
                );
                $dataView['estudios'] = $this->Model->obtEstudios();
                $dataView['medicos_cm'] = $this->Model->obtMedicosConMatriculas();
                $dataView['obras_sociales'] = $this->Model->obtObrasSociales();
            }
        }

        $dataView['ultimo_horreal'] = $this->Model->obtUltimoHorReal(
            $fecha,
            $id_medicos,
            $id_especialidades
        );

        $this->load->view('diagnostico/Agregar_grilla_view', $dataView);
    }

    public function buscar_paciente() {
        $dataView['nro_documento'] = $this->input->post('nro_documento');
        $dataView['paciente'] = $this->Model->buscarPacientes($dataView['nro_documento']);
        $this->load->view('diagnostico/Agregar_grilla_pacientes_view', $dataView);
    }

    public function agregar_turno() {
        $vcDuracionTurno = $this->turnero_model->obtDuracionTurno(
            $this->input->post('id_especialidades'),
            $this->input->post('id_medicos')
        );
        $this->Model->agregarTurno($vcDuracionTurno, $this->input->post());
        print "<strong>Se agregó el turno con éxito!</strong>";
        print "<script>setTimeout(function() {\$('#ag_fecha').change();}, 1000);</script>";
    }

    public function listado($date1, $date2, $id_usuario = null)
    {
        if (isset($id_usuario)) {
            $aUsuario = $this->Model->getUsuario($id_usuario);
            $this->session->set_userdata(array(
                'ID_USUARIO' => $id_usuario,
                'SUPERUSER' => $aUsuario['superuser']
            ));
        }
        $post = $this->input->post();
        if (!isset($post['hour1']) or !$post['hour1']) {
            $post['hour1'] = '00:00';
        }
        if (!isset($post['hour2']) or !$post['hour2']) {
            $post['hour2'] = '23:59';
        }
        $dataView = $post;
        $dataView['date1'] = $date1;
        $dataView['date2'] = $date2;
        $dataView['listado'] = $this->Model->obtenerListado(
            $date1,
            $date2,
            $post
        );
        if (
            $this->session->userdata('SUPERUSER') > 0 or
            $date1 >= date("Y-m-d", strtotime("-2 days"))
        ) {
            $dataView['deja_deposito_suma'] = $this->Model->obtDejaDepositoSuma(
                $date1,
                $date2,
                $post
            );
        } else {
            $dataView['deja_deposito_suma'] = array(null, null);
        }
        $dataView['cantidad_de_ordenes'] = $this->Model->obtCantidadDeOrdenes(
            $date1,
            $date2,
            $post
        );
        $dataView['listado_count'] = $this->Model->obtenerListadoCount(
            $date1,
            $date2,
            $post
        );

        $dataView['medicos'] = $this->Model->obtMedicos();
        $dataView['estudios'] = $this->Model->obtEstudios();
        $dataView['medicos_cm'] = $this->Model->obtMedicosConMatriculas();
        $dataView['obras_sociales'] = $this->Model->obtObrasSociales();

        $this->load->view($this->router->fetch_class().'/Listado_view', $dataView);
    }

    public function savediagnostico()
    {
        print utf8_encode(
            json_encode(
                $this->Model->guardarDiagnostico(
                    $this->input->post(),
                    $this->session->userdata('ID_USUARIO')
                )
            )
        );
    }

    public function exportar($date1, $date2, $filter = '')
    {
        $post = $this->input->post();
        $dataView['date1'] = $date1;
        $dataView['date2'] = $date2;
        $dataView['filter'] = utf8_encode($filter);
        $dataView['aDiagnosticos'] = $this->Model->obtDiagnosticosExport($date1, $date2, $post);
        $dataView['set_heading'] = array(
            'Nro de Paciente',
            'Paciente',
            'Presentador',
            'Realizador',
            'Obra Social',
            'Fecha de Prestación',
            'Nro. de Orden',
            'Nro. de Afiliado',
            'Estudio',
            'Código de Práctica',
            'Cantidad',
            'Tipo',
            'TP',
            'TO',
            'TA',
            'DD',
            'Derivador',
            'Observaciones'
        );
        print utf8_decode($this->load->view($this->router->fetch_class().'/Exportar_view', $dataView, true));
    }

}

//EOF Diagnostico.php

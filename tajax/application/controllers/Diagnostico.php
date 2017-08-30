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
        #$tiempo_inicio = $this->microtime_float();

        if (isset($id_usuario)) {
            $this->session->set_userdata(array('ID_USUARIO' => $id_usuario));
        }
        $post = $this->input->post();
        $dataView = $post;
        $dataView['date1'] = $date1;
        $dataView['date2'] = $date2;
        #$tiempo_fin = $this->microtime_float(); $tiempo = $tiempo_fin - $tiempo_inicio; echo "Tiempo empleado: " . ($tiempo_fin - $tiempo_inicio)."<br />";
        $dataView['listado'] = $this->Model->obtenerListado(
            $date1,
            $date2,
            $post
        );
        #$tiempo_fin = $this->microtime_float(); $tiempo = $tiempo_fin - $tiempo_inicio; echo "Tiempo empleado: " . ($tiempo_fin - $tiempo_inicio)."<br />";
        $dataView['deja_deposito_suma'] = $this->Model->obtDejaDepositoSuma(
            $date1,
            $date2,
            $post
        );
        #$tiempo_fin = $this->microtime_float(); $tiempo = $tiempo_fin - $tiempo_inicio; echo "Tiempo empleado: " . ($tiempo_fin - $tiempo_inicio)."<br />";
        #$dataView['ds'] = $this->Model->obtDiasSemanaDiagnostico($date1, $date2);

        $dataView['medicos'] = $this->Model->obtMedicos();
        $dataView['estudios'] = $this->Model->obtEstudios();
        $dataView['medicos_cm'] = $this->Model->obtMedicosConMatriculas();
        $dataView['obras_sociales'] = $this->Model->obtObrasSociales();

        $this->load->view($this->router->fetch_class().'/Listado_view', $dataView);

        #$tiempo_fin = $this->microtime_float(); $tiempo = $tiempo_fin - $tiempo_inicio; echo "Tiempo empleado: " . ($tiempo_fin - $tiempo_inicio)."<br />";
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
        $this->load->library('table');
        $post = $this->input->post();
        $dataView['date1'] = $date1;
        $dataView['date2'] = $date2;
        $dataView['filter'] = utf8_encode($filter);
        $dataView['aDiagnosticos'] = $this->Model->obtDiagnosticosExport($date1, $date2, $post);
        $this->table->set_heading(
            'Nro de Paciente',
            'Paciente',
            'Presentador',
            'Realizador',
            'Obra Social',
            'Fecha de Presentación',
            'Nro. de Orden',
            'Nro. de Afiliado',
            'Estudio',
            'Código de Presentación',
            'Cantidad',
            'Tipo',
            'TP',
            'TO',
            'TA',
            'DD',
            'Derivador'
        );
        $this->load->view($this->router->fetch_class().'/Exportar_view', $dataView);
    }

}

//EOF Diagnostico.php

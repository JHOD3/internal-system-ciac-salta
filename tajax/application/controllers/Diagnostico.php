<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Diagnostico extends CI_Controller {

    function __construct()
    {
        parent::__construct();
        $this->load->model('Diagnostico_model');
        $this->output->set_header('Access-Control-Allow-Origin: *');
    }

	public function index()
	{
        show_404();
	}

    public function medicos()
    {
        $aData['aMPEPD'] = $this->Diagnostico_model->obtMedicosPorEspecialidadParaDiagnostico();
        $this->load->view('diagnostico_medicos_view', $aData);
    }

    public function horarios(
        $id_especialidades,
        $id_medicos,
        $year,
        $month,
        $day = null,
        $desde = null
    ) {
        $aData['proximoTurnoDisponible'] = $this->Diagnostico_model->proximoTurnoDisponibleParaDiagnostico(
            $id_especialidades,
            $id_medicos
        );
        if (isset($desde)) {
            $aData['post'] = array(
                'desde' => $desde
            );
        }
        $aData['rsEspecialidad'] = $this->Diagnostico_model->obtEspecialidadParaDiagnostico($id_especialidades);
        $aData['rsMedico'] = $this->Diagnostico_model->obtMedicoParaDiagnostico($id_medicos);
        $aData['rsObrasSocialesDeMedico'] = $this->Diagnostico_model->obtObrasSocialesDeMedicoParaDiagnostico($id_medicos);
        if (empty($year)) {
            $year = date("Y");
        }
        if (empty($month)) {
            $month = date("m");
        }
        if (empty($day)) {
            $day = date("d");
        }
        if ($year."-".$month."-".$day < $aData['proximoTurnoDisponible']) {
            $year = date("Y", strtotime($aData['proximoTurnoDisponible']));
            $month = date("m", strtotime($aData['proximoTurnoDisponible']));
            $day = date("d", strtotime($aData['proximoTurnoDisponible']));
        }
        $aData['year'] = $year;
        $aData['month'] = $month;
        $aData['day'] = $day;

        $aData['aDias'] = $this->Diagnostico_model->obtDiasParaDiagnostico(
            $id_especialidades,
            $id_medicos,
            $year,
            $month
        );
        $aData['aHorarios'] = $this->Diagnostico_model->obtHorariosParaDiagnostico(
            $id_especialidades,
            $id_medicos,
            $year,
            $month,
            $day
        );
        $aData['vcDiasHorarios'] = $this->Diagnostico_model->obtDiasHorariosParaDiagnostico(
            $id_especialidades,
            $id_medicos,
            $year,
            $month
        );
        $aData['vcDuracionTurno'] = $this->Diagnostico_model->obtDuracionTurnoParaDiagnostico(
            $id_especialidades,
            $id_medicos
        );
        $aData['aTurnosReservados'] = $this->Diagnostico_model->obtTurnosReservadosParaDiagnostico(
            $id_especialidades,
            $id_medicos,
            $year,
            $month,
            $day
        );
        $aData['aHorariosInhabilitados'] = $this->Diagnostico_model->obtHorariosInhabilitadosParaDiagnostico(
            $id_especialidades,
            $id_medicos,
            $year,
            $month,
            $day
        );

        $prefsCalendar = array (
            #'start_day' => 'monday',
            'show_next_prev'  => TRUE,
            'next_prev_url'   => base_url()."index.php/diagnostico/horarios/{$id_especialidades}/{$id_medicos}"
        );
        $this->load->library('calendar', $prefsCalendar);

        $aData['calendar'] = $this->calendar->generate($year, $month, $aData['aDias']);
        $aData['calendar'] = str_replace(
            "<td>&nbsp;</td>",
            "<td></td>",
            $aData['calendar']
        );
        $aData['aObrasSociales'] = $this->Diagnostico_model->obtObrasSocialesParaDiagnostico();
        $this->load->view('calendar_diagnostico_view', $aData);
    }

}
//EOF Diagnostico.php

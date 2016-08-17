<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Turnero extends CI_Controller {

    function __construct()
    {
        parent::__construct();
        $this->output->set_header('Access-Control-Allow-Origin: *');
    }

	public function index()
	{
        show_404();
	}

    public function calendar(
        $id_especialidades,
        $id_medicos,
        $year,
        $month,
        $day = null,
        $desde = null
    ) {
        $aData['proximoTurnoDisponible'] = $this->turnero_model->proximoTurnoDisponible(
            $id_especialidades,
            $id_medicos
        );
        if (isset($desde)) {
            $aData['post'] = array(
                'desde' => $desde
            );
        }
        $aData['rsEspecialidad'] = $this->turnero_model->obtEspecialidad($id_especialidades);
        $aData['rsMedico'] = $this->turnero_model->obtMedico($id_medicos);
        $aData['rsObrasSocialesDeMedico'] = $this->turnero_model->obtObrasSocialesDeMedico($id_medicos);
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

        $aData['aDias'] = $this->turnero_model->obtDias(
            $id_especialidades,
            $id_medicos,
            $year,
            $month
        );
        $aData['aHorarios'] = $this->turnero_model->obtHorarios(
            $id_especialidades,
            $id_medicos,
            $year,
            $month,
            $day
        );
        $aData['vcDiasHorarios'] = $this->turnero_model->obtDiasHorarios(
            $id_especialidades,
            $id_medicos,
            $year,
            $month
        );
        $aData['vcDuracionTurno'] = $this->turnero_model->obtDuracionTurno(
            $id_especialidades,
            $id_medicos
        );
        $aData['aTurnosReservados'] = $this->turnero_model->obtTurnosReservados(
            $id_especialidades,
            $id_medicos,
            $year,
            $month,
            $day
        );
        $aData['aHorariosInhabilitados'] = $this->turnero_model->obtHorariosInhabilitados(
            $id_especialidades,
            $id_medicos,
            $year,
            $month,
            $day
        );

        $prefsCalendar = array (
            #'start_day' => 'monday',
            'show_next_prev'  => TRUE,
            'next_prev_url'   => base_url()."index.php/turnero/calendar/{$id_especialidades}/{$id_medicos}"
        );
        $this->load->library('calendar', $prefsCalendar);

        $aData['calendar'] = $this->calendar->generate($year, $month, $aData['aDias']);
        $aData['calendar'] = str_replace(
            "<td>&nbsp;</td>",
            "<td></td>",
            $aData['calendar']
        );
        $aData['aObrasSociales'] = $this->turnero_model->obtObrasSociales();
        $this->load->view('calendar_view', $aData);
    }

}
//EOF Turnero.php

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Diagnostico extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model($this->router->fetch_class().'_model', 'Model');
    }

    public function listado($date1, $date2, $filtro = null, $offset = 0, $id_usuario = null)
    {
        if (isset($id_usuario)) {
            $this->session->set_userdata(array('ID_USUARIO' => $id_usuario));
        }
        $dataView = $this->Model->obtenerPaginacion($date1, $date2, $filtro);
        $dataView['date1'] = $date1;
        $dataView['date2'] = $date2;
        $dataView['filtro'] = $filtro == '0' ? '' : trim(str_replace('%20', ' ', $filtro));
        $dataView['listado'] = $this->Model->obtenerListado(
            $date1,
            $date2,
            $filtro,
            $dataView['pagination_config']['per_page'],
            $offset
        );
        #$dataView['ds'] = $this->Model->obtDiasSemanaDiagnostico($date1, $date2);

        $dataView['medicos'] = $this->Model->obtMedicos();
        $dataView['medicos_cm'] = $this->Model->obtMedicosConMatriculas();
        $dataView['obras_sociales'] = $this->Model->obtObrasSociales();

        $this->load->view($this->router->fetch_class().'/Listado_view', $dataView);
    }

    public function savediagnostico()
    {
        print utf8_encode(
            json_encode(
                $this->Model->saveDiagnostico(
                    $this->input->post(),
                    $this->session->userdata('ID_USUARIO')
                )
            )
        );
    }

    public function exportar($date1, $date2, $filter)
    {
        $this->load->library('table');
        $dataView['date1'] = $date1;
        $dataView['date2'] = $date2;
        $dataView['filter'] = $filter;
        $dataView['aDiagnosticos'] = $this->Model->obtDiagnosticosExport($date1, $date2, $filter);
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

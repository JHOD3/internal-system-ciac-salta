<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Diagnostico extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model($this->router->fetch_class().'_model', 'Model');
    }

    public function listado($date, $offset = 0)
    {
        $dataView = $this->Model->obtenerPaginacion($date);
        $dataView['date'] = $date;
        $dataView['listado'] = $this->Model->obtenerListado(
            $date,
            $dataView['pagination_config']['per_page'],
            $offset
        );
        $dataView['ds'] = $this->Model->obtDiasSemanaDiagnostico($date);

        $this->load->view($this->router->fetch_class().'/Listado_view', $dataView);
    }

}

//EOF Diagnostico.php

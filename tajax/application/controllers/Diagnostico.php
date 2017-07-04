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

	public function form_agregar()
	{
        $dataView = $this->Model->obtenerDropDownsRel();
		$this->load->view($this->router->fetch_class().'/Form_agregar_view', $dataView);
	}

    public function guardar_agregar()
    {
        if (!$this->Model->validar()) {
            $this->form_agregar();
        } else {
            $this->Model->guardar_agregar($this->input->post());
            $this->load->view($this->router->fetch_class().'/Alert_guardar_agregar_view');
        }
    }

    public function form_modificar($id)
	{
        $dataView = $this->Model->obtenerDropDownsRel();
        $dataView['item'] = $this->Model->obtenerItem($id);
		$this->load->view($this->router->fetch_class().'/Form_modificar_view', $dataView);
	}

    public function guardar_modificar($id = null)
    {
        if (!$this->Model->validar()) {
            $this->form_modificar($id);
        } else {
            $this->Model->guardar_modificar($this->input->post(), $id);
            $this->load->view($this->router->fetch_class().'/Alert_guardar_modificar_view');
        }
    }

    public function form_borrar($id)
    {
        $dataView['item'] = $this->Model->obtenerItem($id);
        $this->load->view($this->router->fetch_class().'/Form_borrar_view', $dataView);
    }

    public function guardar_borrar()
    {
        $this->Model->guardar_eliminar($this->input->post('id_turnos'));
        $this->load->view($this->router->fetch_class().'/Alert_borrar_view');
    }

}

//EOF Diagnostico.php

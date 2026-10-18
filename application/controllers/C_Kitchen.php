<?php

class C_Kitchen extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->functions->validate_session($this->segment->get('isActive'));

        $this->view->set_js('index');

        $this->view->set_menu(array(
            'modules' => $this->segment->get('modules'),
            'view'    => 'kitchen'
        ));

        $this->view->set_view('index');
    }

    public function get_orders()
    {
        $this->functions->validate_session($this->segment->get('isActive'));

        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            return $this->kitchen_json('error', 'Método no permitido.');
        }

        $r = $this->load_model('Kitchen')->get_active_orders();
        $this->kitchen_response($r, 'Comandas encontradas.');
    }

    public function change_status()
    {
        $this->functions->validate_session($this->segment->get('isActive'));

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->kitchen_json('error', 'Método no permitido.');
        }

        $input = json_decode(file_get_contents('php://input'), true);

        if (empty($input)) {
            $input = filter_input_array(INPUT_POST);
        }

        $id     = isset($input['id']) ? intval($input['id']) : 0;
        $status = isset($input['status']) ? strtoupper(trim($input['status'])) : '';

        // Cocina solo puede avanzar la comanda
        $allowed = array('EN_PREPARACION', 'LISTO', 'ENTREGADO');

        if ($id <= 0 || !in_array($status, $allowed, true)) {
            return $this->kitchen_json('warning', 'Datos de estado inválidos.');
        }

        $r = $this->load_model('Kitchen')->change_status(array('id' => $id, 'status' => $status));
        $this->kitchen_response($r, 'Estado actualizado.');
    }

    private function kitchen_response($response, $success_msg)
    {
        if ($response['status'] === 'OK') {
            header('Content-Type: application/json');
            echo json_encode(array(
                'status' => 'OK',
                'type'   => 'success',
                'msg'    => $success_msg,
                'data'   => $response['result']
            ));
        } elseif ($response['status'] === 'EXCEPTION') {
            $this->kitchen_json('error', $response['result']->getMessage());
        } else {
            $this->kitchen_json('warning', 'No se pudo completar la operación.');
        }
    }

    private function kitchen_json($type, $msg)
    {
        header('Content-Type: application/json');
        echo json_encode(array(
            'status' => 'ERROR',
            'type'   => $type,
            'msg'    => $msg,
            'data'   => array()
        ));
    }
}
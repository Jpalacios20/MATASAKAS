<?php

class C_Orders extends Controller
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
            'view'    => 'orders'
        ));

        $this->view->set_view('index');
    }

    public function get_orders()
    {
        $this->functions->validate_session($this->segment->get('isActive'));

        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            return $this->order_json('error', 'Método no permitido.');
        }

        $date = isset($_GET['date']) ? trim($_GET['date']) : '';

        if ($date !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return $this->order_json('warning', 'Fecha no válida.');
        }

        $r = $this->load_model('Orders')->get_orders($date);
        $this->order_response($r, 'Comandas encontradas.');
    }

    public function get_order_detail()
    {
        $this->functions->validate_session($this->segment->get('isActive'));

        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            return $this->order_json('warning', 'Comanda no válida.');
        }

        $r = $this->load_model('Orders')->get_order_detail(array('id' => $id));
        $this->order_response($r, 'Detalle encontrado.');
    }

    public function change_status()
    {
        $this->functions->validate_session($this->segment->get('isActive'));

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->order_json('error', 'Método no permitido.');
        }

        $input = json_decode(file_get_contents('php://input'), true);

        if (empty($input)) {
            $input = filter_input_array(INPUT_POST);
        }

        $id     = isset($input['id']) ? intval($input['id']) : 0;
        $status = isset($input['status']) ? strtoupper(trim($input['status'])) : '';

        $allowed = array('PENDIENTE', 'EN_PREPARACION', 'LISTO', 'ENTREGADO', 'PAGADO', 'ANULADO');

        if ($id <= 0 || !in_array($status, $allowed, true)) {
            return $this->order_json('warning', 'Datos de estado inválidos.');
        }

        $r = $this->load_model('Orders')->change_status(array('id' => $id, 'status' => $status));
        $this->order_response($r, 'Estado actualizado.');
    }

    private function order_response($response, $success_msg)
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
            $this->order_json('error', $response['result']->getMessage());
        } else {
            $this->order_json('warning', 'No se encontraron registros.');
        }
    }

    private function order_json($type, $msg)
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
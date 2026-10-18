<?php

class C_Kitchen extends Controller
{
    // Estados que la cocina puede asignar a una comanda.
    // Deben coincidir con los valores que acepta la columna status de hs_orders.
    private $valid_status = array(
        'PENDIENTE',
        'EN_PREPARACION',
        'LISTO'
    );


    public function __construct()
    {
        parent::__construct();
    }


    // ---------------------------------------------------------
    // PANTALLA DE COCINA
    // ---------------------------------------------------------
    public function index()
    {
        $this->functions->validate_session(
            $this->segment->get('isActive')
        );

        $this->view->set_js('index');

        $this->view->set_menu(array(
            'modules' => $this->segment->get('modules'),
            'view'    => 'kitchen'
        ));

        $this->view->set_view('index');
    }


    // ---------------------------------------------------------
    // OBTENER COMANDAS ACTIVAS (con sus platos)
    // ---------------------------------------------------------
    public function get_orders()
    {
        $this->functions->validate_session(
            $this->segment->get('isActive')
        );

        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            return $this->kitchen_json('ERROR', 'error', 'Método no permitido.');
        }

        $obj = $this->load_model('Kitchen');

        $response = $obj->get_orders();

        switch ($response['status']) {

            case 'OK':

                $this->kitchen_json(
                    'OK',
                    'success',
                    'Comandas encontradas.',
                    $response['result']
                );

                break;

            case 'ERROR':

                // Que no haya comandas activas es normal en cocina.
                $this->kitchen_json(
                    'OK',
                    'info',
                    'No hay comandas pendientes.',
                    array()
                );

                break;

            case 'EXCEPTION':

                $this->kitchen_json(
                    'ERROR',
                    'error',
                    $response['result']->getMessage()
                );

                break;

            default:

                $this->kitchen_json(
                    'ERROR',
                    'error',
                    'Respuesta no reconocida.'
                );

                break;
        }
    }


    // ---------------------------------------------------------
    // CAMBIAR ESTADO DE UNA COMANDA
    // ---------------------------------------------------------
    public function update_status()
    {
        $this->functions->validate_session(
            $this->segment->get('isActive')
        );

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->kitchen_json('ERROR', 'error', 'Método no permitido.');
        }

        $input = json_decode(
            file_get_contents('php://input'),
            true
        );

        if (empty($input)) {
            $input = filter_input_array(INPUT_POST);
        }

        $id_order = isset($input['id_order'])
            ? intval($input['id_order'])
            : 0;

        $status = isset($input['status'])
            ? strtoupper(trim($input['status']))
            : '';

        if ($id_order <= 0 || !in_array($status, $this->valid_status, true)) {
            return $this->kitchen_json(
                'ERROR',
                'warning',
                'Comanda o estado no válidos.'
            );
        }

        $obj = $this->load_model('Kitchen');

        $response = $obj->update_status(array(
            'id_order' => $id_order,
            'status'   => $status
        ));

        if ($response['status'] === 'OK') {

            $this->kitchen_json(
                'OK',
                'success',
                'Estado de la comanda actualizado.'
            );

        } elseif ($response['status'] === 'EXCEPTION') {

            $this->kitchen_json(
                'ERROR',
                'error',
                $response['result']->getMessage()
            );

        } else {

            $this->kitchen_json(
                'ERROR',
                'warning',
                'No se pudo actualizar la comanda.'
            );
        }
    }


    // ---------------------------------------------------------
    // RESPUESTA JSON
    // ---------------------------------------------------------
    private function kitchen_json($status, $type, $msg, $data = array())
    {
        header('Content-Type: application/json');

        echo json_encode(array(
            'status' => $status,
            'type'   => $type,
            'msg'    => $msg,
            'data'   => $data
        ));
    }
}
<?php

class C_Meal extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }


    // ---------------------------------------------------------
    // VISTAS
    // ---------------------------------------------------------
    public function index()
    {
        $this->validate_session_and_view('index');
    }

    public function tables()
    {
        $this->validate_session_and_view('index');
    }

    public function menu()
    {
        $this->validate_session_and_view('menu');
    }

    private function validate_session_and_view($view_name)
    {
        $this->functions->validate_session($this->segment->get('isActive'));

        /*
        $this->functions->check_permissions(
            $this->segment->get('modules'),
            'Meal'
        );
        */

        $this->view->set_js($view_name);
        $this->view->set_menu(array(
            'modules' => $this->segment->get('modules'),
            'view'    => 'meal'
        ));
        $this->view->set_view($view_name);
    }


    // ---------------------------------------------------------
    // OBTENER TODAS LAS COMIDAS
    // ---------------------------------------------------------
    public function get_meal()
    {
        $this->handle_get_request(function($obj, $input) {
            return $obj->get_meal();
        }, 'Listado de comidas encontradas.', 'No se encontraron comidas.');
    }


    // ---------------------------------------------------------
    // OBTENER COMIDAS PARA NUEVA COMANDA
    // ---------------------------------------------------------
    public function get_meals_comanda()
    {
        $this->handle_get_request(function($obj, $input) {
            return $obj->get_meals_comanda();
        }, 'Listado de comidas encontrado.', 'No se encontraron comidas.');
    }


    // ---------------------------------------------------------
    // OBTENER COMIDA POR ID
    // ---------------------------------------------------------
    public function get_meal_by_id()
    {
        $this->functions->validate_session($this->segment->get('isActive'));

        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            return $this->meal_json('error', 'Método no permitido.', 'ERROR', array());
        }

        $input = $this->get_request_input();

        if (empty($input['id_meal'])) {
            return $this->meal_json('warning', 'No se enviaron los campos necesarios.', 'ERROR', array());
        }

        $obj = $this->load_model('Meal');
        $bind = array('id_meal' => intval($input['id_meal']));
        $response = $obj->get_meal_by_id($bind);

        $this->process_response($response, 'Comida encontrada.', 'No se encontró la comida.');
    }


    // ---------------------------------------------------------
    // OBTENER CATEGORÍAS
    // ---------------------------------------------------------
    public function get_categories()
    {
        $this->functions->validate_session($this->segment->get('isActive'));

        $obj = $this->load_model('Categories');
        $response = $obj->get_categories();

        $this->process_response($response, 'Listado de categorías encontrado.', 'No se encontraron categorías.');
    }


    // ---------------------------------------------------------
    // OBTENER MESAS
    // ---------------------------------------------------------
    public function get_tables()
    {
        $this->handle_get_request(function($obj, $input) {
            return $obj->get_tables();
        }, 'Listado de mesas encontrado.', 'No se encontraron mesas en el sistema.');
    }


    // ---------------------------------------------------------
    // CREAR COMANDA
    // ---------------------------------------------------------
    public function create_comanda()
    {
        $this->functions->validate_session($this->segment->get('isActive'));

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->meal_json('error', 'Método no permitido.', 'ERROR', array());
        }

        $input = $this->get_request_input();

        if (empty($input)) {
            return $this->meal_json('warning', 'No se enviaron los datos de la comanda.', 'ERROR', array());
        }

        $obj = $this->load_model('Meal');
        $response = $obj->create_comanda($input);

        if (isset($response['status']) && $response['status'] === 'OK') {
            $data = isset($response['result']) ? $response['result'] : array();
            return $this->meal_json('success', 'Comanda creada exitosamente.', 'OK', $data);
        }

        $error_message = 'No se pudo registrar la comanda en la base de datos.';
        if (isset($response['result']) && is_string($response['result'])) {
            $error_message = $response['result'];
        }

        return $this->meal_json('warning', $error_message, 'ERROR', array());
    }


    // ---------------------------------------------------------
    // GUARDAR COMIDA (crear o editar)
    // ---------------------------------------------------------
    public function save_meal()
    {
        $this->functions->validate_session($this->segment->get('isActive'));

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->meal_json('error', 'Método no permitido.', 'ERROR', array());
        }

        $input = $this->get_request_input();

        $id_meal     = isset($input['id_meal']) ? intval($input['id_meal']) : 0;
        $name        = isset($input['meal_name']) ? trim($input['meal_name']) : '';
        $description = isset($input['meal_description']) ? trim($input['meal_description']) : '';
        $sku         = isset($input['meal_sku']) ? trim($input['meal_sku']) : '';
        $id_category = isset($input['id_category']) ? intval($input['id_category']) : 0;
        $price       = isset($input['meal_price']) ? floatval($input['meal_price']) : 0;

        if ($name === '' || $id_category <= 0 || $price <= 0) {
            return $this->meal_json('warning', 'Completa nombre, categoría y un precio mayor a 0.', 'ERROR', array());
        }

        if ($sku === '') {
            $sku = 'MEAL-' . date('ymdHis');
        }

        $bind = array(
            'meal_sku'         => $sku,
            'meal_name'        => $name,
            'meal_description' => $description,
            'id_category'      => $id_category,
            'meal_price'       => number_format($price, 2, '.', '')
        );

        $obj = $this->load_model('Meal');

        if ($id_meal > 0) {
            $bind['id_meal'] = $id_meal;
            $response = $obj->update_meal($bind);
            $msg = 'Plato actualizado correctamente.';
        } else {
            $response = $obj->create_meal($bind);
            $msg = 'Plato creado correctamente.';
        }

        $this->meal_response_legacy($response, $msg);
    }


    // ---------------------------------------------------------
    // ELIMINAR COMIDA
    // ---------------------------------------------------------
    public function delete_meal()
    {
        $this->functions->validate_session($this->segment->get('isActive'));

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->meal_json('error', 'Método no permitido.', 'ERROR', array());
        }

        $input = $this->get_request_input();
        $id_meal = isset($input['id_meal']) ? intval($input['id_meal']) : 0;

        if ($id_meal <= 0) {
            return $this->meal_json('warning', 'Plato no válido.', 'ERROR', array());
        }

        $obj = $this->load_model('Meal');
        $response = $obj->delete_meal(array('id_meal' => $id_meal));

        $this->meal_response_legacy($response, 'Plato eliminado correctamente.');
    }


    // ---------------------------------------------------------
    // MÉTODOS AUXILIARES PRIVADOS (EVITANDO DUPLICACIÓN)
    // ---------------------------------------------------------
    private function get_request_input()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input)) {
            $input = filter_input_array(INPUT_GET) ?: filter_input_array(INPUT_POST) ?: array();
        }
        return $input;
    }

    private function handle_get_request($callback, $success_msg, $error_msg)
    {
        $this->functions->validate_session($this->segment->get('isActive'));

        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            return $this->meal_json('error', 'Método no permitido.', 'ERROR', array());
        }

        $input = $this->get_request_input();
        $obj = $this->load_model('Meal');
        $response = $callback($obj, $input);

        $this->process_response($response, $success_msg, $error_msg);
    }

    private function process_response($response, $success_msg, $error_msg)
    {
        $status = isset($response['status']) ? $response['status'] : '';

        switch ($status) {
            case 'OK':
                $data = isset($response['result']) ? $response['result'] : array();
                $this->meal_json('success', $success_msg, 'OK', $data);
                break;

            case 'ERROR':
                $this->meal_json('warning', $error_msg, 'ERROR', array());
                break;

            case 'EXCEPTION':
                $error_msg_ex = is_object($response['result']) ? $response['result']->getMessage() : 'Excepción en base de datos.';
                $this->meal_json('error', $error_msg_ex, 'ERROR', array());
                break;

            default:
                $this->meal_json('error', 'Respuesta no reconocida.', 'ERROR', array());
                break;
        }
    }

    private function meal_response_legacy($response, $success_msg)
    {
        if (isset($response['status']) && $response['status'] === 'OK') {
            $this->meal_json('success', $success_msg, 'OK', isset($response['result']) ? $response['result'] : array());
        } elseif (isset($response['status']) && $response['status'] === 'EXCEPTION') {
            $err = is_object($response['result']) ? $response['result']->getMessage() : 'Error en la operación.';
            $this->meal_json('error', $err, 'ERROR', array());
        } else {
            $this->meal_json('warning', 'No se pudo completar la operación.', 'ERROR', array());
        }
    }

    private function meal_json($type, $msg, $status, $data)
    {
        header('Content-Type: application/json');

        echo json_encode(array(
            'status' => $status,
            'type'   => $type,
            'msg'    => $msg,
            'data'   => $data
        ));
        exit;
    }
}
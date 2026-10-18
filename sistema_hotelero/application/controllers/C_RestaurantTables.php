<?php

class C_RestaurantTables extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    // ---------------------------------------------------------
    // VISTA PRINCIPAL - MESAS
    // ---------------------------------------------------------
    public function index()
    {
        $this->functions->validate_session(
            $this->segment->get('isActive')
        );

        // Usamos el mismo menú que funciona en Meal
        $this->view->set_js('index');

        $this->view->set_menu(array(
            'modules' => $this->segment->get('modules'),
            'view'    => 'meal'
        ));

        $this->view->set_view('index');
    }


    // ---------------------------------------------------------
    // OBTENER TODAS LAS MESAS
    // ---------------------------------------------------------
    public function get_tables()
    {
        $this->functions->validate_session(
            $this->segment->get('isActive')
        );

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {

            $obj = $this->load_model('RestaurantTables');

            $response = $obj->get_tables();

            switch ($response['status']) {

                case 'OK':

                    $json = array(
                        'status' => 'OK',
                        'type'   => 'success',
                        'msg'    => 'Listado de mesas encontrado.',
                        'data'   => $response['result']
                    );

                    break;

                case 'ERROR':

                    $json = array(
                        'status' => 'ERROR',
                        'type'   => 'warning',
                        'msg'    => 'No se encontraron mesas.',
                        'data'   => array()
                    );

                    break;

                case 'EXCEPTION':

                    $json = array(
                        'status' => 'ERROR',
                        'type'   => 'error',
                        'msg'    => $response['result']->getMessage(),
                        'data'   => array()
                    );

                    break;

                default:

                    $json = array(
                        'status' => 'ERROR',
                        'type'   => 'error',
                        'msg'    => 'Respuesta no reconocida.',
                        'data'   => array()
                    );

                    break;
            }

        } else {

            $json = array(
                'status' => 'ERROR',
                'type'   => 'error',
                'msg'    => 'Método no permitido.',
                'data'   => array()
            );
        }

        header('Content-Type: application/json');

        echo json_encode($json);
    }


    // ---------------------------------------------------------
    // OBTENER MESA POR ID
    // ---------------------------------------------------------
    public function get_table_by_id()
    {
        $this->functions->validate_session(
            $this->segment->get('isActive')
        );

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {

            $input = json_decode(
                file_get_contents('php://input'),
                true
            );

            if (empty($input)) {
                $input = filter_input_array(INPUT_GET);
            }

            if (!empty($input['id'])) {

                $obj = $this->load_model('RestaurantTables');

                $bind = array(
                    'id' => intval($input['id'])
                );

                $response = $obj->get_table_by_id($bind);

                switch ($response['status']) {

                    case 'OK':

                        $json = array(
                            'status' => 'OK',
                            'type'   => 'success',
                            'msg'    => 'Mesa encontrada.',
                            'data'   => $response['result']
                        );

                        break;

                    case 'ERROR':

                        $json = array(
                            'status' => 'ERROR',
                            'type'   => 'warning',
                            'msg'    => 'No se encontró la mesa.',
                            'data'   => array()
                        );

                        break;

                    case 'EXCEPTION':

                        $json = array(
                            'status' => 'ERROR',
                            'type'   => 'error',
                            'msg'    => $response['result']->getMessage(),
                            'data'   => array()
                        );

                        break;

                    default:

                        $json = array(
                            'status' => 'ERROR',
                            'type'   => 'error',
                            'msg'    => 'Respuesta no reconocida.',
                            'data'   => array()
                        );

                        break;
                }

            } else {

                $json = array(
                    'status' => 'ERROR',
                    'type'   => 'warning',
                    'msg'    => 'No se indicó el ID de la mesa.',
                    'data'   => array()
                );
            }

        } else {

            $json = array(
                'status' => 'ERROR',
                'type'   => 'error',
                'msg'    => 'Método no permitido.',
                'data'   => array()
            );
        }

        header('Content-Type: application/json');

        echo json_encode($json);
    }


    // ---------------------------------------------------------
    // CREAR MESA
    // ---------------------------------------------------------
    public function create_table()
    {
        $this->functions->validate_session(
            $this->segment->get('isActive')
        );

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $input = json_decode(
                file_get_contents('php://input'),
                true
            );

            if (empty($input)) {
                $input = filter_input_array(INPUT_POST);
            }

            if (!empty($input)) {

                $obj = $this->load_model('RestaurantTables');

                $response = $obj->create_table($input);

                switch ($response['status']) {

                    case 'OK':

                        $json = array(
                            'status' => 'OK',
                            'type'   => 'success',
                            'msg'    => 'Mesa creada correctamente.',
                            'data'   => isset($response['result'])
                                ? $response['result']
                                : array()
                        );

                        break;

                    case 'ERROR':

                        $json = array(
                            'status' => 'ERROR',
                            'type'   => 'warning',
                            'msg'    => 'No se pudo crear la mesa.',
                            'data'   => array()
                        );

                        break;

                    case 'EXCEPTION':

                        $json = array(
                            'status' => 'ERROR',
                            'type'   => 'error',
                            'msg'    => $response['result']->getMessage(),
                            'data'   => array()
                        );

                        break;

                    default:

                        $json = array(
                            'status' => 'ERROR',
                            'type'   => 'error',
                            'msg'    => 'Respuesta no reconocida.',
                            'data'   => array()
                        );

                        break;
                }

            } else {

                $json = array(
                    'status' => 'ERROR',
                    'type'   => 'warning',
                    'msg'    => 'No se enviaron los datos de la mesa.',
                    'data'   => array()
                );
            }

        } else {

            $json = array(
                'status' => 'ERROR',
                'type'   => 'error',
                'msg'    => 'Método no permitido.',
                'data'   => array()
            );
        }

        header('Content-Type: application/json');

        echo json_encode($json);
    }


    // ---------------------------------------------------------
    // ACTUALIZAR MESA
    // ---------------------------------------------------------
    public function update_table()
    {
        $this->functions->validate_session(
            $this->segment->get('isActive')
        );

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $input = json_decode(
                file_get_contents('php://input'),
                true
            );

            if (empty($input)) {
                $input = filter_input_array(INPUT_POST);
            }

            if (!empty($input)) {

                $obj = $this->load_model('RestaurantTables');

                $response = $obj->update_table($input);

                switch ($response['status']) {

                    case 'OK':

                        $json = array(
                            'status' => 'OK',
                            'type'   => 'success',
                            'msg'    => 'Mesa actualizada correctamente.',
                            'data'   => isset($response['result'])
                                ? $response['result']
                                : array()
                        );

                        break;

                    case 'ERROR':

                        $json = array(
                            'status' => 'ERROR',
                            'type'   => 'warning',
                            'msg'    => 'No se pudo actualizar la mesa.',
                            'data'   => array()
                        );

                        break;

                    case 'EXCEPTION':

                        $json = array(
                            'status' => 'ERROR',
                            'type'   => 'error',
                            'msg'    => $response['result']->getMessage(),
                            'data'   => array()
                        );

                        break;

                    default:

                        $json = array(
                            'status' => 'ERROR',
                            'type'   => 'error',
                            'msg'    => 'Respuesta no reconocida.',
                            'data'   => array()
                        );

                        break;
                }

            } else {

                $json = array(
                    'status' => 'ERROR',
                    'type'   => 'warning',
                    'msg'    => 'No se enviaron los datos de la mesa.',
                    'data'   => array()
                );
            }

        } else {

            $json = array(
                'status' => 'ERROR',
                'type'   => 'error',
                'msg'    => 'Método no permitido.',
                'data'   => array()
            );
        }

        header('Content-Type: application/json');

        echo json_encode($json);
    }


    // ---------------------------------------------------------
    // CAMBIAR ESTADO
    // ---------------------------------------------------------
    public function update_status()
    {
        $this->functions->validate_session(
            $this->segment->get('isActive')
        );

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $input = json_decode(
                file_get_contents('php://input'),
                true
            );

            if (empty($input)) {
                $input = filter_input_array(INPUT_POST);
            }

            if (!empty($input)) {

                $obj = $this->load_model('RestaurantTables');

                $response = $obj->update_status($input);

                switch ($response['status']) {

                    case 'OK':

                        $json = array(
                            'status' => 'OK',
                            'type'   => 'success',
                            'msg'    => 'Estado actualizado correctamente.',
                            'data'   => isset($response['result'])
                                ? $response['result']
                                : array()
                        );

                        break;

                    case 'ERROR':

                        $json = array(
                            'status' => 'ERROR',
                            'type'   => 'warning',
                            'msg'    => 'No se pudo actualizar el estado.',
                            'data'   => array()
                        );

                        break;

                    case 'EXCEPTION':

                        $json = array(
                            'status' => 'ERROR',
                            'type'   => 'error',
                            'msg'    => $response['result']->getMessage(),
                            'data'   => array()
                        );

                        break;

                    default:

                        $json = array(
                            'status' => 'ERROR',
                            'type'   => 'error',
                            'msg'    => 'Respuesta no reconocida.',
                            'data'   => array()
                        );

                        break;
                }

            } else {

                $json = array(
                    'status' => 'ERROR',
                    'type'   => 'warning',
                    'msg'    => 'No se enviaron los datos.',
                    'data'   => array()
                );
            }

        } else {

            $json = array(
                'status' => 'ERROR',
                'type'   => 'error',
                'msg'    => 'Método no permitido.',
                'data'   => array()
            );
        }

        header('Content-Type: application/json');

        echo json_encode($json);
    }


    // ---------------------------------------------------------
    // ELIMINAR MESA
    // ---------------------------------------------------------
    public function delete_table()
    {
        $this->functions->validate_session(
            $this->segment->get('isActive')
        );

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $input = json_decode(
                file_get_contents('php://input'),
                true
            );

            if (empty($input)) {
                $input = filter_input_array(INPUT_POST);
            }

            if (!empty($input)) {

                $obj = $this->load_model('RestaurantTables');

                $response = $obj->delete_table($input);

                switch ($response['status']) {

                    case 'OK':

                        $json = array(
                            'status' => 'OK',
                            'type'   => 'success',
                            'msg'    => 'Mesa eliminada correctamente.',
                            'data'   => isset($response['result'])
                                ? $response['result']
                                : array()
                        );

                        break;

                    case 'ERROR':

                        $json = array(
                            'status' => 'ERROR',
                            'type'   => 'warning',
                            'msg'    => 'No se pudo eliminar la mesa.',
                            'data'   => array()
                        );

                        break;

                    case 'EXCEPTION':

                        $json = array(
                            'status' => 'ERROR',
                            'type'   => 'error',
                            'msg'    => $response['result']->getMessage(),
                            'data'   => array()
                        );

                        break;

                    default:

                        $json = array(
                            'status' => 'ERROR',
                            'type'   => 'error',
                            'msg'    => 'Respuesta no reconocida.',
                            'data'   => array()
                        );

                        break;
                }

            } else {

                $json = array(
                    'status' => 'ERROR',
                    'type'   => 'warning',
                    'msg'    => 'No se enviaron los datos.',
                    'data'   => array()
                );
            }

        } else {

            $json = array(
                'status' => 'ERROR',
                'type'   => 'error',
                'msg'    => 'Método no permitido.',
                'data'   => array()
            );
        }

        header('Content-Type: application/json');

        echo json_encode($json);
    }
}
<?php 
class C_Accessories extends Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $this->functions->validate_session($this->segment->get('isActive'));
        $this->functions->check_permissions($this->segment->get('modules'), 'Accessories');
        $this->view->set_js('index');
        $this->view->set_menu(array('modules' => $this->segment->get('modules'), 'view' => 'Accessories'));
        $this->view->set_view('index');
    }


    // Obtener todos los accesorios
    public function get_accessories() 
    {
        $this->functions->validate_session($this->segment->get('isActive'));
        $request = $_SERVER['REQUEST_METHOD'];

        if ($request === 'GET') {
            $obj = $this->load_model('Accessories');
            $response = $obj->get_accessories();

            switch ($response['status']) {
                case 'OK':
                    $json = array(
                        'status' => 'OK',
                        'type' => 'success',
                        'msg' => 'Listado de registros encontrados.',
                        'data' => $response['result']
                    );
                    break;
                case 'ERROR':
                    $json = array(
                        'status' => 'ERROR',
                        'type' => 'warning',
                        'msg' => 'No se encontraron registros en el sistema.',
                        'data' => array(),
                    );
                    break;
                case 'EXCEPTION':
                    $json = array(
                        'status' => 'ERROR',
                        'type' => 'error',
                        'msg' => $response['result']->getMessage(),
                        'data' => array()
                    );
                    break;
                default:
                    $json = array(
                        'status' => 'ERROR',
                        'type' => 'error',
                        'msg' => 'Respuesta desconocida del servidor.',
                        'data' => array()
                    );
                    break;
            }
        } else {
            $json = array(
                'status' => 'ERROR',
                'type' => 'error',
                'msg' => 'Método no permitido.',
                'data' => array()
            );
        }

        header('Content-Type: application/json');
        echo json_encode($json);
    }
    public function get_accessories_by_id()
    {
        $this->functions->validate_session($this->segment->get('isActive'));
        $request = $_SERVER['REQUEST_METHOD'];

        if ($request === 'GET') {
            $input = json_decode(file_get_contents('php://input'), true);
            if (empty($input)) {
                $input = filter_input_array(INPUT_GET);
            }

            error_log("Datos recibidos en la petición: " . json_encode($input));

            if (!empty($input['id_accessory'])) {
                $obj = $this->load_model('accessories');
                $bind = array(
                    'id_accessory' => intval($input['id_accessory'])
                );

                $response = $obj->get_accessory_by_id($bind);

                error_log("Respuesta del modelo: " . json_encode($response));

                switch ($response['status']) {
                    case 'OK':
                        $json = array(
                            'status' => 'OK',
                            'type' => 'success',
                            'msg' => 'Listado de registros encontrados.',
                            'data' => $response['result']
                        );
                        break;
                    case 'ERROR':
                        $json = array(
                            'status' => 'ERROR',
                            'type' => 'warning',
                            'msg' => 'No se encontraron registros en el sistema.',
                            'data' => array(),
                        );
                        break;
                    case 'EXCEPTION':
                        $json = array(
                            'status' => 'ERROR',
                            'type' => 'error',
                            'msg' => $response['result'],
                            'data' => array()
                        );
                        break;
                }
            } else {
                $json = array(
                    'status' => 'ERROR',
                    'type' => 'warning',
                    'msg' => 'No se enviaron los campos necesarios, verificar.',
                    'data' => array()
                );
            }
        } else {
            $json = array(
                'status' => 'ERROR',
                'type' => 'error',
                'msg' => 'Método no permitido.',
                'data' => array()
            );
        }

        header('Content-Type: application/json');
        echo json_encode($json);
    }



    // Crear un accesorio
    public function create_accessory() {
        $this->functions->validate_session($this->segment->get('isActive'));
        $request = $_SERVER['REQUEST_METHOD'];

        if ($request === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);
            if (empty($input)) {
                $input = filter_input_array(INPUT_POST);
            }

            if (!empty($input['id_accessory']) && !empty($input['accessory_description']) && isset($input['accessory_price']) && isset($input['accessory_stock'])) {
                $id_accessory = intval($input['id_accessory']);
                $accessory_description = $this->functions->clean_string($input['accessory_description']);
                $accessory_price = floatval($input['accessory_price']);
                $accessory_stock = intval($input['accessory_stock']);

                $bind = array(
                    'id_accessory' => $id_accessory,
                    'accessory_description' => $accessory_description,
                    'accessory_price' => $accessory_price,
                    'accessory_stock' => $accessory_stock,
                );

                $obj = $this->load_model('Accessories');
                $response = $obj->create_accessory($bind);

                switch ($response['status']) {
                    case 'OK':
                        $json = array('status' => 'OK', 'type' => 'success', 'msg' => 'Accesorio creado exitosamente.', 'data' => array());
                        break;
                    case 'ERROR':
                        $json = array('status' => 'ERROR', 'type' => 'warning', 'msg' => 'No se pudo crear el accesorio.', 'data' => array());
                        break;
                    case 'EXCEPTION':
                        $json = array('status' => 'ERROR', 'type' => 'error', 'msg' => $response['result']->getMessage(), 'data' => array());
                        break;
                }
            } else {
                $json = array('status' => 'ERROR', 'type' => 'warning', 'msg' => 'Campos obligatorios faltantes.', 'data' => array());
            }
        } else {
            $json = array('status' => 'ERROR', 'type' => 'error', 'msg' => 'Método no permitido.', 'data' => array());
        }

        header('Content-Type: application/json');
        echo json_encode($json);
    }

    // Actualizar un accesorio
    public function update_accessory() {
        $this->functions->validate_session($this->segment->get('isActive'));
        $request = $_SERVER['REQUEST_METHOD'];

        if ($request === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);
            if (empty($input)) {
                $input = filter_input_array(INPUT_POST);
            }

            if (!empty($input['id_accessory']) && !empty($input['accessory_description']) && !empty($input['accessory_price']) && !empty($input['accessory_stock'])) {
                $id_accessory = intval($input['id_accessory']);
                $accessory_description = $this->functions->clean_string($input['accessory_description']);
                $accessory_price = floatval($input['accessory_price']);
                $accessory_stock = intval($input['accessory_stock']);

                $bind = array(
                    'id_accessory' => $id_accessory,
                    'accessory_description' => $accessory_description,
                    'accessory_price' => $accessory_price,
                    'accessory_stock' => $accessory_stock,
                );

                $obj = $this->load_model('Accessories');
                $response = $obj->update_accessory($bind);

                switch ($response['status']) {
                    case 'OK':
                        $json = array('status' => 'OK', 'type' => 'success', 'msg' => 'Accesorio actualizado exitosamente.', 'data' => array());
                        break;
                    case 'ERROR':
                        $json = array('status' => 'ERROR', 'type' => 'warning', 'msg' => 'No se pudo actualizar el accesorio.', 'data' => array());
                        break;
                    case 'EXCEPTION':
                        $json = array('status' => 'ERROR', 'type' => 'error', 'msg' => $response['result']->getMessage(), 'data' => array());
                        break;
                }
            } else {
                $json = array('status' => 'ERROR', 'type' => 'warning', 'msg' => 'Campos obligatorios faltantes.', 'data' => array());
            }
        } else {
            $json = array('status' => 'ERROR', 'type' => 'error', 'msg' => 'Método no permitido.', 'data' => array());
        }

        header('Content-Type: application/json');
        echo json_encode($json);
    }

    // Eliminar un accesorio
    public function delete_accessory() {
        $this->functions->validate_session($this->segment->get('isActive'));
        $request = $_SERVER['REQUEST_METHOD'];

        if ($request === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);
            if (empty($input)) {
                $input = filter_input_array(INPUT_POST);
            }

            if (!empty($input['id_accessory'])) {
                $id_accessory = intval($input['id_accessory']);

                $bind = array('id_accessory' => $id_accessory);

                $obj = $this->load_model('Accessories');
                $response = $obj->delete_accessory($bind);

                switch ($response['status']) {
                    case 'OK':
                        $json = array('status' => 'OK', 'type' => 'success', 'msg' => 'Accesorio eliminado exitosamente.', 'data' => array());
                        break;
                    case 'ERROR':
                        $json = array('status' => 'ERROR', 'type' => 'warning', 'msg' => 'No se pudo eliminar el accesorio.', 'data' => array());
                        break;
                    case 'EXCEPTION':
                        $json = array('status' => 'ERROR', 'type' => 'error', 'msg' => $response['result']->getMessage(), 'data' => array());
                        break;
                }
            } else {
                $json = array('status' => 'ERROR', 'type' => 'warning', 'msg' => 'ID de accesorio faltante.', 'data' => array());
            }
        } else {
            $json = array('status' => 'ERROR', 'type' => 'error', 'msg' => 'Método no permitido.', 'data' => array());
        }

        header('Content-Type: application/json');
        echo json_encode($json);
    }
}

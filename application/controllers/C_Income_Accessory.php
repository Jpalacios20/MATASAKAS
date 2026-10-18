<?php
class C_Income_Accessory extends Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $this->functions->validate_session($this->segment->get('isActive'));
        $this->functions->check_permissions($this->segment->get('modules'), 'Income_Accessory');
        $this->view->set_js('index');
        $this->view->set_menu(array('modules' => $this->segment->get('modules'), 'view' => 'Income_Accessory'));
        $this->view->set_view('index');
    }

    // LISTADO
    public function get_income_accessory() {
        $this->functions->validate_session($this->segment->get('isActive'));

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $obj = $this->load_model('Income_Accessory');
            $response = $obj->get_income_accessory();

            switch ($response['status']) {
                case 'OK':
                    $json = [
                        'status' => 'OK',
                        'type'   => 'success',
                        'msg'    => 'Listado de registros encontrados.',
                        'data'   => $response['result']
                    ];
                    break;
                case 'ERROR':
                    $json = [
                        'status' => 'ERROR',
                        'type'   => 'warning',
                        'msg'    => 'No se encontraron registros en el sistema.',
                        'data'   => []
                    ];
                    break;
                default:
                    $json = [
                        'status' => 'ERROR',
                        'type'   => 'error',
                        'msg'    => 'Respuesta no válida del modelo.',
                        'data'   => []
                    ];
                    break;
            }
        } else {
            $json = ['status' => 'ERROR', 'type' => 'error', 'msg' => 'Método no permitido.', 'data' => []];
        }

        header('Content-Type: application/json');
        echo json_encode($json);
        exit;
    }

    // DETALLE MODAL VER
    public function income_accessory_details() {
        $this->functions->validate_session($this->segment->get('isActive'));

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if (!isset($_GET['id_income_accessory']) || empty($_GET['id_income_accessory'])) {
                $json = ['status' => 'ERROR', 'type' => 'error', 'msg' => 'ID no proporcionado.'];
            } else {
                $id_income_accessory = intval($_GET['id_income_accessory']);

                if ($id_income_accessory <= 0) {
                    $json = ['status' => 'ERROR', 'type' => 'error', 'msg' => 'ID no válido.'];
                } else {
                    $obj = $this->load_model('Income_Accessory');
                    $response = $obj->get_income_accessory_details($id_income_accessory);

                    if (isset($response['status']) && $response['status'] === 'OK') {
                        $json = [
                            'status' => 'OK',
                            'type'   => 'success',
                            'msg'    => 'Detalles encontrados.',
                            'result' => $response['result']
                        ];
                    } else {
                        $json = [
                            'status' => 'ERROR',
                            'type'   => 'warning',
                            'msg'    => 'No se encontraron detalles para este ingreso.',
                            'result' => []
                        ];
                    }
                }
            }
        } else {
            $json = ['status' => 'ERROR', 'type' => 'error', 'msg' => 'Método no permitido.'];
        }

        header('Content-Type: application/json');
        echo json_encode($json);
        exit;
    }

    // CREAR INGRESO
    public function create_income_accessory() {
        header('Content-Type: application/json');
        $this->functions->validate_session($this->segment->get('isActive'));

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);
            if (empty($input)) {
                $input = filter_input_array(INPUT_POST);
            }

            if (!empty($input['id_client']) && !empty($input['id_voucher_type']) && !empty($input['id_payment_type'])
                && !empty($input['id_payment_shape']) && !empty($input['series']) && !empty($input['number_serial'])) {

                $id_person        = $this->functions->clean_string($input['id_client']);
                $id_voucher_type  = $this->functions->clean_string($input['id_voucher_type']);
                $id_payment_type  = $this->functions->clean_string($input['id_payment_type']);
                $id_payment_shape = $this->functions->clean_string($input['id_payment_shape']);
                $voucher_series   = $this->functions->clean_string($input['series']);
                $number_serial    = $this->functions->clean_string($input['number_serial']);

                $dataUser  = $this->segment->get('data');
                $id_user   = $dataUser['id_user'] ?? 1;
                $id_campus = $this->segment->get('isLocation') ?? null;

                $bind = array(
                    'id_person'        => $id_person,
                    'id_user'          => $id_user,
                    'id_voucher_type'  => $id_voucher_type,
                    'voucher_series'   => $voucher_series,
                    'number_serial'    => $number_serial,
                    'id_payment_type'  => $id_payment_type,
                    'id_payment_shape' => $id_payment_shape,
                    'tax'              => 0,
                    'purchase_total'   => floatval($input['purchase_total']),
                    'id_campus'        => $id_campus,
                    'status'           => 1,
                    'data_time'        => $this->functions->clean_string($input['data_time']),
                );

                $obj = $this->load_model('Income_Accessory');
                $insertedId = $obj->create_income_accessory($bind);

                if ($insertedId && $insertedId !== false) {
                    echo json_encode(['status' => 'OK', 'id' => $insertedId]);
                } else {
                    echo json_encode(['status' => 'ERROR', 'msg' => 'No se pudo crear el ingreso.']);
                }
            } else {
                echo json_encode(['status' => 'ERROR', 'msg' => 'Campos obligatorios faltantes.']);
            }
        }
    }

    // CREAR DETALLE
    public function create_income_accessory_details() {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);

            if (empty($input['accesorios'])) {
                echo json_encode(['status' => 'ERROR', 'msg' => 'No se enviaron accesorios.']);
                return;
            }

            $obj = $this->load_model('Income_Accessory');
            $insertCount = 0;
            $errors = [];

            foreach ($input['accesorios'] as $accesorio) {
                if (empty($accesorio['id_income_accessory']) || empty($accesorio['id_accessory']) || empty($accesorio['quantity']) || empty($accesorio['full_purchase'])) {
                    $errors[] = "Accesorio incompleto: " . json_encode($accesorio);
                    continue;
                }

                $accesorioLimpio = [
                    'id_income_accessory' => intval($accesorio['id_income_accessory']),
                    'id_accessory'        => intval($accesorio['id_accessory']),
                    'quantity'            => intval($accesorio['quantity']),
                    'full_purchase'       => floatval($accesorio['full_purchase']),
                    'subtotal'            => floatval($accesorio['subtotal'] ?? ($accesorio['quantity'] * $accesorio['full_purchase'])),
                    'selling_price'       => isset($accesorio['selling_price']) ? floatval($accesorio['selling_price']) : null
                ];

                $result = $obj->insertIncomeAccessoryDetails($accesorioLimpio);

                if ($result === true) {
                    $insertCount++;
                } else {
                    $errors[] = $result;
                }
            }

            if ($insertCount > 0) {
                echo json_encode(['status' => 'OK', 'message' => 'Accesorios insertados correctamente.', 'insertados' => $insertCount]);
            } else {
                echo json_encode(['status' => 'ERROR', 'msg' => 'No se pudieron insertar los accesorios.', 'errors' => $errors]);
            }
        }
    }

    // ELIMINAR
    public function delete_income_accessory() {
        $this->functions->validate_session($this->segment->get('isActive'));

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);
            if (empty($input)) {
                $input = filter_input_array(INPUT_POST);
            }

            if (!empty($input['id_income_accessory'])) {
                $bind = array('id_income_accessory' => intval($input['id_income_accessory']));
                $obj = $this->load_model('Income_Accessory');
                $response = $obj->delete_income_accessory($bind);

                switch ($response['status']) {
                    case 'OK':
                        $json = array('status' => 'OK', 'type' => 'success', 'msg' => 'Registro eliminado exitosamente.', 'data' => array());
                        break;
                    case 'ERROR':
                        $json = array('status' => 'ERROR', 'type' => 'warning', 'msg' => 'No se pudo eliminar el registro.', 'data' => array());
                        break;
                    case 'EXCEPTION':
                        $json = array('status' => 'ERROR', 'type' => 'error', 'msg' => $response['result'], 'data' => array());
                        break;
                }
            } else {
                $json = array('status' => 'ERROR', 'type' => 'warning', 'msg' => 'No se enviaron los campos necesarios.', 'data' => array());
            }
        } else {
            $json = array('status' => 'ERROR', 'type' => 'error', 'msg' => 'Método no permitido.', 'data' => array());
        }

        header('Content-Type: application/json');
        echo json_encode($json);
    }
}
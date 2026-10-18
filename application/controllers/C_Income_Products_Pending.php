<?php 
// --
class C_Income_Products_Pending extends Controller {

    // --
    public function __construct() {
		parent::__construct();
    }
    
    // --
    public function index() {
        // --
        $this->functions->validate_session($this->segment->get('isActive'));
        $this->functions->check_permissions($this->segment->get('modules'), 'Income_Products');
        // --
        $this->view->set_js('index');       // -- Load JS
        $this->view->set_menu(array('modules' => $this->segment->get('modules'), 'view' => 'Income_Products')); // -- Active Menu
        $this->view->set_view('index');     // -- Load View
    }

    public function get_income_products_pending() {

        $this->functions->validate_session($this->segment->get('isActive'));

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $obj = $this->load_model('Income_Products_Pending');
            $response = $obj->get_income_products_pending(); // Obtenemos datos del modelo

        
            if (isset($response['status']) && $response['status'] === 'OK' && !empty($response['result'])) {
                $json = [
                    'status' => 'OK',
                    'type' => 'success',
                    'msg' => 'Listado de registros encontrados.',
                    'data' => $response['result']
                ];
            } else {
                $json = [
                    'status' => 'ERROR',
                    'type' => 'warning',
                    'msg' => 'No se encontraron registros en el sistema.',
                    'data' => []
                ];
            }
        } else {
            $json = [
                'status' => 'ERROR',
                'type' => 'error',
                'msg' => 'Método no permitido.',
                'data' => []
            ];
        }

        header('Content-Type: application/json');
        echo json_encode($json);
        exit;
    }
    

    public function update_income_products_status(){
        $this->functions->validate_session($this->segment->get('isActive'));

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Cargar el modelo
            $obj = $this->load_model('Income_Products_Pending');

            // Obtener los datos enviados por AJAX
            $data = json_decode(json_encode($_POST), true);

            if (!isset($data['registros']) || !is_array($data['registros']) || empty($data['registros'])) {
                $json = [
                    'status' => 'ERROR',
                    'type' => 'warning',
                    'msg' => 'Datos inválidos o vacíos.',
                    'data' => []
                ];
            } else {
                // Llamar al modelo para actualizar el estado de los registros
                $response = $obj->update_income_products_status($data['registros']);

                if (isset($response['status']) && $response['status'] === 'OK') {
                    $json = [
                        'status' => 'OK',
                        'type' => 'success',
                        'msg' => 'Registros actualizados correctamente.',
                        'data' => $response['result']
                    ];
                } else {
                    $json = [
                        'status' => 'ERROR',
                        'type' => 'error',
                        'msg' => 'No se pudo actualizar los registros.',
                        'data' => []
                    ];
                }
            }
        } else {
            $json = [
                'status' => 'ERROR',
                'type' => 'error',
                'msg' => 'Método no permitido.',
                'data' => []
            ];
        }

        // Devolver respuesta en formato JSON
        header('Content-Type: application/json');
        echo json_encode($json);
        exit;
    }
}
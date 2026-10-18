<?php 
// --
class C_Report_Monthly extends Controller {

    // --
    public function __construct() {
		parent::__construct();
    }
    
    // --
    public function index() {
        // --
        $this->functions->validate_session($this->segment->get('isActive'));
        $this->functions->check_permissions($this->segment->get('modules'), 'report_monthly');
        // --
        $this->view->set_js('index');       // -- Load JS
        $this->view->set_menu(array('modules' => $this->segment->get('modules'), 'view' => 'report_monthly')); // -- Active Menu
        $this->view->set_view('index');     // -- Load View
    }

    public function get_report_data() {
        $this->functions->validate_session($this->segment->get('isActive'));

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $obj = $this->load_model('ReportMonthly');

            $response = $obj->get_report_data(); 

            if ($response['status'] === 'OK') {
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
    public function get_all_clients() {

    $this->functions->validate_session($this->segment->get('isActive'));

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $obj = $this->load_model('Clients');
        $response = $obj->get_all_clients();

        if ($response['status'] === 'OK') {
            $json = [
                'status' => 'OK',
                'type' => 'success',
                'msg' => 'Listado de clientes encontrados.',
                'data' => $response['data']
            ];
        } else {
            $json = [
                'status' => 'ERROR',
                'type' => 'warning',
                'msg' => 'No se encontraron clientes.',
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

}
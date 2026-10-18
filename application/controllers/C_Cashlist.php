<?php 
// --
class C_Cashlist extends Controller {

    // --
    public function __construct() {
		parent::__construct();
    }
    
    // --
    public function index() {
        // --
        $this->functions->validate_session($this->segment->get('isActive'));
        $this->functions->check_permissions($this->segment->get('modules'), 'Cashlist');
        // --
        $this->view->set_js('index');       // -- Load JS
        $this->view->set_menu(array('modules' => $this->segment->get('modules'), 'view' => 'Cashlist')); // -- Active Menu
        $this->view->set_view('index');     // -- Load View
    }

    public function get_cashlist() {
        $this->functions->validate_session($this->segment->get('isActive'));
        $request = $_SERVER['REQUEST_METHOD'];

        if ($request === 'GET') {
            $obj = $this->load_model('Cashlist');
            $response = $obj->get_cashlist();

            switch ($response['status']) {
                case 'OK':
                    $json = array(
                        'status' => 'OK',
                        'type' => 'success',
                        'msg' => 'Historial de cajas encontrado.',
                        'data' => $response['result']
                    );
                    break;
                case 'ERROR':
                    $json = array(
                        'status' => 'ERROR',
                        'type' => 'warning',
                        'msg' => 'No se encontraron registros.',
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
}
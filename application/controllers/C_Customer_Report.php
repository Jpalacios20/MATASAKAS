<?php

class C_Customer_Report extends Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $this->functions->validate_session($this->segment->get('isActive'));
        $this->functions->check_permissions($this->segment->get('modules'), 'customer_report');
        
        $this->view->set_js('index');
        $this->view->set_menu(array('modules' => $this->segment->get('modules'), 'view' => 'customer_report'));
        $this->view->set_view('index');
    }

    public function get_report_data() {
        $this->functions->validate_session($this->segment->get('isActive'));

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $filters = array();
            
            if (!empty($_GET['document_number'])) {
                $filters['document_number'] = $_GET['document_number'];
            }
            
            if (!empty($_GET['name'])) {
                $filters['name'] = $_GET['name'];
            }

            if (isset($_GET['status']) && $_GET['status'] !== '') {
                $filters['status'] = $_GET['status'];
            }

            $obj = $this->load_model('Customer_Report');
            $response = $obj->get_report_data($filters);

            $json = [
                'status' => $response['status'],
                'type' => ($response['status'] === 'OK') ? 'success' : 'error',
                'msg' => ($response['status'] === 'OK') ? 'Datos encontrados' : 'No se encontraron registros',
                'data' => ($response['status'] === 'OK') ? $response['result'] : []
            ];
        } else {
            $json = [
                'status' => 'ERROR',
                'type' => 'error',
                'msg' => 'Método no permitido'
            ];
        }

        header('Content-Type: application/json');
        echo json_encode($json);
    }
}
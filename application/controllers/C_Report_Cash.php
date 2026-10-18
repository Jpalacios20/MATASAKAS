<?php

class C_Report_Cash extends Controller {

// --
public function __construct() {
    parent::__construct();
}

public function index() {
    // --
    $this->functions->validate_session($this->segment->get('isActive'));
    $this->functions->check_permissions($this->segment->get('modules'), 'report_cash');
    // --
    $this->view->set_js('index');       // -- Load JS
    $this->view->set_menu(array('modules' => $this->segment->get('modules'), 'view' => 'report_cash')); // -- Active Menu
    $this->view->set_view('index');     // -- Load View
}

public function get_report_data() {
    $this->functions->validate_session($this->segment->get('isActive'));

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $filters = array();
        
        if (!empty($_GET['fecha_inicio'])) {
            $filters['fecha_inicio'] = $_GET['fecha_inicio'];
        }
        
        if (!empty($_GET['fecha_fin'])) {
            $filters['fecha_fin'] = $_GET['fecha_fin'];
        }
        
        if (!empty($_GET['id_user'])) {
            $filters['id_user'] = $_GET['id_user'];
        }

        $obj = $this->load_model('Report_Cash');
        $response = $obj->get_report_data($filters);

        switch ($response['status']) {
            case 'OK':
                $json = [
                    'status' => 'OK',
                    'type' => 'success',
                    'msg' => 'Listado de registros encontrados.',
                    'data' => $response['result']
                ];
                break;

            case 'ERROR':
                $json = [
                    'status' => 'ERROR',
                    'type' => 'warning',
                    'msg' => 'No se encontraron registros en el sistema.',
                    'data' => []
                ];
                break;

            case 'EXCEPTION':
                $json = [
                    'status' => 'ERROR',
                    'type' => 'error',
                    'msg' => 'Error en el servidor: ' . (is_object($response['result']) ? $response['result']->getMessage() : $response['result']),
                    'data' => []
                ];
                break;

            default:
                $json = [
                    'status' => 'ERROR',
                    'type' => 'error',
                    'msg' => 'Respuesta inesperada del servidor.',
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

public function get_user() {
    $this->functions->validate_session($this->segment->get('isActive'));
    $request = $_SERVER['REQUEST_METHOD'];

    if ($request === 'GET') {
      $obj = $this->load_model('Report_Cash');
      $response = $obj->get_user();

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
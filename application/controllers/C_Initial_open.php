<?php 
// --
class C_Initial_open extends Controller {

  // --
  public function __construct() {
   parent::__construct();
  }

  // --
  public function index() {
    // --
    $this->functions->validate_session($this->segment->get('isActive'));
    $this->functions->check_permissions($this->segment->get('modules'), 'Initial_open'); // Check Permissions
    
    // --
    $this->view->set_js('index');       // Load JS
    $this->view->set_menu(array('modules' => $this->segment->get('modules'), 'view' => 'Initial_open')); // Active Menu
    $this->view->set_view('index');     // Load View
  }


  public function get_user() {

    $this->functions->validate_session($this->segment->get('isActive'));
    $request = $_SERVER['REQUEST_METHOD'];

    if ($request === 'GET') {  // Cambiado de POST a GET
      $input = json_decode(file_get_contents('php://input'), true);
      if (empty($input)) {
        $input = filter_input_array(INPUT_GET);
      }

      $obj = $this->load_model('Initial_open');
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

  public function save_initial_open() {
    try {
        // Validar sesión
        $this->functions->validate_session($this->segment->get('isActive'));
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            throw new Exception('Método no permitido');
        }

        $input = $_POST;

        // Validaciones básicas
        if (empty($input['fecha_hora_apertura'])) {
            throw new Exception('La fecha de apertura es requerida');
        }

        if (empty($input['id_user'])) {
            throw new Exception('El usuario es requerido');
        }

        if (!isset($input['monto_inicial']) || !is_numeric($input['monto_inicial'])) {
            throw new Exception('El monto inicial es inválido');
        }

        // Procesar
        $model = $this->load_model('Initial_open');
        $result = $model->save_initial_open($input);

        echo json_encode($result);

    } catch (Exception $e) {
        echo json_encode([
            'status' => 'ERROR',
            'result' => $e->getMessage()
        ]);
    }
  }

}

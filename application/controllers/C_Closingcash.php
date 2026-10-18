<?php
// --
class C_Closingcash extends Controller
{

  // --
  public function __construct()
  {
    parent::__construct();
  }

  // --
  public function index()
  {
    // --
    $this->functions->validate_session($this->segment->get('isActive'));
    $this->functions->check_permissions($this->segment->get('modules'), 'Closingcash');
    // --
    $this->view->set_js('index');       // -- Load JS
    $this->view->set_menu(array('modules' => $this->segment->get('modules'), 'view' => 'Closingcash')); // -- Active Menu
    $this->view->set_view('index');     // -- Load View
  }

  public function get_cash_closing_form_today()
  {
    $this->functions->validate_session($this->segment->get('isActive'));
    $request = $_SERVER['REQUEST_METHOD'];

    if ($request === 'GET') {
      $input = json_decode(file_get_contents('php://input'), true);
      if (empty($input)) {
        $input = filter_input_array(INPUT_GET);
      }

      $obj = $this->load_model('Closingcash');
      $response = $obj->get_cash_closing_form_today();

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



  public function post_cash_closing()
  {
    // --
    $this->functions->validate_session($this->segment->get('isActive'));
    // --
    $request = $_SERVER['REQUEST_METHOD'];
    // --
    if ($request === 'POST') {
        // --
        $input = $_POST;

        // --
        if (
          isset($input['income']) && $input['income'] !== '' &&
          isset($input['expenses']) && $input['expenses'] !== '' &&
          isset($input['notes']) && $input['notes'] !== ''
        ) {
          // --
          $income = floatval($this->functions->clean_string($input['income']));
          $expenses = floatval($this->functions->clean_string($input['expenses']));
          $notes = $this->functions->clean_string($input['notes']);
          
          // --
          $bind = array(
            'income' => $income, 
            'expenses' => $expenses, 
            'notes' => $notes
          );
          
          // --
          $obj = $this->load_model('Closingcash');
          $response = $obj->post_cash_closing($bind);
          // --
          switch ($response['status']) {
              // --
            case 'OK':
              // --
              $json = array(
                'status' => 'OK',
                'type' => 'success',
                'msg' => 'Cierre de caja realizado correctamente.',
                'data' => array()
              );
              // --
              break;

            case 'ERROR':
              // --
              $json = array(
                'status' => 'ERROR',
                'type' => 'warning',
                'msg' => is_string($response['result']) ? $response['result'] : 'No fue posible realizar el cierre de caja.',
                'data' => array(),
              );
              // --
              break;

            case 'EXCEPTION':
              // --
              $json = array(
                'status' => 'ERROR',
                'type' => 'error',
                'msg' => $response['result']->getMessage(),
                'data' => array()
              );
              // --
              break;
          }
        } else {
          // --
          $json = array(
            'status' => 'ERROR',
            'type' => 'warning',
            'msg' => 'Faltan campos obligatorios: ingresos, egresos y observaciones.',
            'data' => array()
          );
        }
    } else {
      // --
      $json = array(
        'status' => 'ERROR',
        'type' => 'error',
        'msg' => 'Método no permitido.',
        'data' => array()
      );
    }

    // --
    header('Content-Type: application/json');
    echo json_encode($json);
  }

  public function closing_cash_history()
  {
    $this->functions->validate_session($this->segment->get('isActive'));
    $request = $_SERVER['REQUEST_METHOD'];

    if ($request === 'GET') {
      $input = json_decode(file_get_contents('php://input'), true);
      if (empty($input)) {
        $input = filter_input_array(INPUT_GET);
      }

      $obj = $this->load_model('Closingcash');
      $response = $obj->closing_cash_history();

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


  public function check_closing_cash_today()
  {
    $this->functions->validate_session($this->segment->get('isActive'));
    $request = $_SERVER['REQUEST_METHOD'];

    if ($request === 'GET') {
      $input = json_decode(file_get_contents('php://input'), true);
      if (empty($input)) {
        $input = filter_input_array(INPUT_GET);
      }

      $obj = $this->load_model('Closingcash');
      $response = $obj->check_closing_cash_today();

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

  public function get_daily_income() 
  {
    $this->functions->validate_session($this->segment->get('isActive'));
    
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        try {
            $obj = $this->load_model('Closingcash');
            $response = $obj->calculate_daily_income();

            if ($response['status'] === 'OK') {
                $json = array(
                    'status' => 'OK',
                    'type' => 'success',
                    'msg' => 'Ingresos calculados correctamente',
                    'data' => $response['result']
                );
            } else {
                $json = array(
                    'status' => 'ERROR',
                    'type' => 'warning',
                    'msg' => 'No se encontraron registros',
                    'data' => array('total_income' => 0)
                );
            }
        } catch (Exception $e) {
            $json = array(
                'status' => 'ERROR',
                'type' => 'error',
                'msg' => $e->getMessage(),
                'data' => array('total_income' => 0)
            );
        }
    } else {
        $json = array(
            'status' => 'ERROR',
            'type' => 'error',
            'msg' => 'Método no permitido',
            'data' => array('total_income' => 0)
        );
    }

    header('Content-Type: application/json');
    echo json_encode($json);
    exit;
}
}
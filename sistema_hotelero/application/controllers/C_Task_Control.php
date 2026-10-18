<?php 
// --
class C_task_control extends Controller {

  // --
  public function __construct() {
   parent::__construct();
  }

  // 
  public function index() {
    // 
    $this->functions->validate_session($this->segment->get('isActive'));
    $this->functions->check_permissions($this->segment->get('modules'), 'Task_Control');
    
    // 
    $this->view->set_js('index');       // 
    $this->view->set_menu(array('modules' => $this->segment->get('modules'), 'view' => 'Task_Control')); // Active Menu
    $this->view->set_view('index');     // 
  }

  // Obtener lista de clientes
  public function get_task_control() {
      $this->functions->validate_session($this->segment->get('isActive'));
      $request = $_SERVER['REQUEST_METHOD'];

      if ($request === 'GET') {
          $input = json_decode(file_get_contents('php://input'), true);
          if (empty($input)) {
              $input = filter_input_array(INPUT_GET);
          }

          $obj = $this->load_model('Task_Control');
          $response = $obj->get_task_control();

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
                      'msg' => $response['result'], // Aquí se obtiene directamente el mensaje
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
  public function get_task_control_by_id() {
    $this->functions->validate_session($this->segment->get('isActive'));
  
    $request = $_SERVER['REQUEST_METHOD'];
  
    if ($request === 'GET') {
        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input)) {
            $input = filter_input_array(INPUT_GET);
        }
  
        if (!empty($input['id_task_control'])) {
            $obj = $this->load_model('Task_Control');
            $bind = array('id_task_control' => intval($input['id_task_control']));
            $response = $obj->get_task_control_by_id($bind);
  
            switch ($response['status']) {
                case 'OK':
                    $json = array(
                        'status' => 'OK',
                        'type' => 'success',
                        'msg' => ' encontrado.',
                        'data' => $response['result']
                    );
                    break;
  
                case 'ERROR':
                    $json = array(
                        'status' => 'ERROR',
                        'type' => 'warning',
                        'msg' => 'No se encontró .',
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
                'type' => 'warning',
                'msg' => 'No se enviaron los campos necesarios.',
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
    
    // Crear nuevo registro de task_control
    public function create_task_control() {
        $this->functions->validate_session($this->segment->get('isActive'));
        $request = $_SERVER['REQUEST_METHOD'];

        if ($request === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);
            if (empty($input)) {
                $input = filter_input_array(INPUT_POST);
            }

            // Validar datos de entrada aquí (opcional)

            $obj = $this->load_model('Task_Control');
            $response = $obj->create_task_control($input);

            switch ($response['status']) {
                case 'OK':
                    $json = array(
                        'status' => 'OK',
                        'type' => 'success',
                        'msg' => 'Registro creado correctamente.',
                        'data' => $response['result'] 
                    );
                    break;

                case 'ERROR':
                    $json = array(
                        'status' => 'ERROR',
                        'type' => 'warning',
                        'msg' => 'No se pudo crear el registro.',
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

    // Obtener información para el modal de proceso de desarrollo
    public function get_task_control_process_modal() {
        $request = $_SERVER['REQUEST_METHOD'];
        if ($request === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);
            if (!empty($input['id_task_control'])) {
                $obj = $this->load_model('Task_Control');
                $response = $obj->get_task_control_process_modal($input['id_task_control']);

                switch ($response['status']) {
                    case 'OK':
                        $json = [
                            'success' => true,
                            'estudiante' => $response['result']['leader'],
                            'proyecto' => $response['result']['project_name']
                        ];
                        break;
                    case 'ERROR':
                        $json = ['success' => false, 'msg' => 'No se encontró el registro.'];
                        break;
                    case 'EXCEPTION':
                        $json = ['success' => false, 'msg' => $response['result']];
                        break;
                }
            } else {
                $json = ['success' => false, 'msg' => 'No se envió el ID del registro.'];
            }
        } else {
            $json = ['success' => false, 'msg' => 'Método no permitido.'];
        }
        header('Content-Type: application/json');
        echo json_encode($json);
    }
    public function save_development_process() {
        $this->functions->validate_session($this->segment->get('isActive'));
        $request = $_SERVER['REQUEST_METHOD'];
    
        if ($request === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);
            if (empty($input)) {
                $input = filter_input_array(INPUT_POST);
            }
    
            $obj = $this->load_model('Task_Control');
            $response = $obj->save_development_process($input);
    
            switch ($response['status']) {
                case 'OK':
                    $json = array(
                        'status' => 'OK',
                        'type' => 'success',
                        'msg' => 'Proceso de desarrollo guardado correctamente.',
                        'data' => $response['result']
                    );
                    break;
    
                case 'ERROR':
                    $json = array(
                        'status' => 'ERROR',
                        'type' => 'warning',
                        'msg' => 'No se pudo guardar el proceso de desarrollo.',
                        'data' => array()
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
                'type' => 'error',
                'msg' => 'Método no permitido.',
                'data' => array()
            );
        }
    
        header('Content-Type: application/json');
        echo json_encode($json);
    }

    public function get_development_process() {
        $this->functions->validate_session($this->segment->get('isActive'));
        $request = $_SERVER['REQUEST_METHOD'];
    
        if ($request === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);
            if (empty($input) || empty($input['development_id'])) {
                $json = array(
                    'status' => 'ERROR',
                    'type' => 'warning',
                    'msg' => 'ID de desarrollo no proporcionado.',
                    'data' => array()
                );
            } else {
                $obj = $this->load_model('Task_Control');
                $response = $obj->get_development_process_by_id($input['development_id']); // Llamada correcta al modelo
    
                switch ($response['status']) {
                    case 'OK':
                        $json = array(
                            'status' => 'OK',
                            'type' => 'success',
                            'msg' => 'Datos del proceso de desarrollo encontrados.',
                            'data' => $response['result']
                        );
                        break;
    
                    case 'ERROR':
                        $json = array(
                            'status' => 'ERROR',
                            'type' => 'warning',
                            'msg' => $response['result'],
                            'data' => array()
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

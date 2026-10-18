<?php
// --
class C_Roomtype extends Controller
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
    $this->functions->check_permissions($this->segment->get('modules'), 'RoomType');
    // --
    $this->view->set_js('index');       // -- Load JS
    $this->view->set_menu(array('modules' => $this->segment->get('modules'), 'view' => 'RoomType')); // -- Active Menu
    $this->view->set_view('index');     // -- Load View
  }
  public function get_room_types()
  {
    $this->functions->validate_session($this->segment->get('isActive'));
    $request = $_SERVER['REQUEST_METHOD'];

    if ($request === 'GET') {
      $input = json_decode(file_get_contents('php://input'), true);
      if (empty($input)) {
        $input = filter_input_array(INPUT_GET);
      }

      $obj = $this->load_model('RoomType');
      $response = $obj->get_room_types();

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



  //--



  public function create_room_types()
  {
    $this->functions->validate_session($this->segment->get('isActive'));
    $request = $_SERVER['REQUEST_METHOD'];

    if ($request === 'POST') {
      // Recibir los datos de las habitaciones desde la solicitud
      $input = json_decode(file_get_contents('php://input'), true);
      if (empty($input)) {
        $input = filter_input_array(INPUT_POST);
      }

      // Verificar si los datos necesarios están presentes en la solicitud
      if (
        !empty($input['type_name']) &&
        !empty($input['person_limit']) &&
        !empty($input['bed_type']) &&
        !empty($input['price_temporary']) &&
        !empty($input['price_half']) &&
        !empty($input['price_day'])&&
        !empty($input['price_holiday'])
      ) {
        // Realizar validaciones si es necesario

        // Crear un arreglo de datos para la inserción en la base de datos
        $bind = array(
          'type_name' => $input['type_name'],
          'person_limit' => $input['person_limit'],
          'bed_type' => $input['bed_type'],
          'price_temporary' => $input['price_temporary'],
          'price_half' => $input['price_half'],
          'price_day' => $input['price_day'],
          'price_holiday' => $input['price_holiday'],
          // Agregar otros campos si es necesario
        );

        // Llamar a la función que crea la habitación en el modelo correspondiente
        $obj = $this->load_model('RoomType');
        $response = $obj->create_room_types($bind);

        switch ($response['status']) {
          case 'OK':
            $json = array(
              'status' => 'OK',
              'type' => 'success',
              'msg' => 'Tipo de Habitación creada en el sistema con éxito.',
              'data' => array()
            );
            break;

          case 'ERROR':
            $json = array(
              'status' => 'ERROR',
              'type' => 'warning',
              'msg' => 'No fue posible crear la habitación ingresada, verificar.',
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
  //

  public function get_room_type_by_id()
  {
    $this->functions->validate_session($this->segment->get('isActive'));
    $request = $_SERVER['REQUEST_METHOD'];

    if ($request === 'GET') {
      $input = json_decode(file_get_contents('php://input'), true);
      if (empty($input)) {
        $input = filter_input_array(INPUT_GET);
      }

      if (!empty($input['id_type'])) {
        $bind = array(
          'id_type' => $this->functions->clean_string($input['id_type'])
        );

        $obj = $this->load_model('RoomType');
        $response = $obj->get_room_type_by_id($bind);

        switch ($response['status']) {
          case 'OK':
            $json = array(
              'status' => 'OK',
              'type' => 'success',
              'msg' => 'Registro encontrado.',
              'data' => $response['result']
            );
            break;

          case 'ERROR':
            $json = array(
              'status' => 'ERROR',
              'type' => 'warning',
              'msg' => 'No se encontró el registro.',
              'data' => array()
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
          'msg' => 'No se enviaron los campos necesarios, verificar.',
          'data' => array()
        );
      }
    } else {
      $json = array(
        'status' => 'ERROR',
        'type' => 'error',
        'msg' => 'Metodo no permitido.',
        'data' => array()
      );
    }

    header('Content-Type: application/json');
    echo json_encode($json);
  }
  public function update_room_type()
  {
    $this->functions->validate_session($this->segment->get('isActive'));
    $request = $_SERVER['REQUEST_METHOD'];

    if ($request === 'POST') {
      $input = json_decode(file_get_contents('php://input'), true);
      if (empty($input)) {
        $input = filter_input_array(INPUT_POST);
      }

      if (
        !empty($input['id_type']) &&
        !empty($input['type_name']) &&
        !empty($input['person_limit']) &&
        !empty($input['price_temporary']) &&
        !empty($input['price_half']) &&
        !empty($input['price_day']) &&
        !empty($input['price_holiday']) &&
        !empty($input['bed_type'])
      ) {
        $bind = array(
          'id_type' => $this->functions->clean_string($input['id_type']),
          'type_name' => $this->functions->clean_string($input['type_name']),
          'person_limit' => $this->functions->clean_string($input['person_limit']),
          'price_temporary' => $this->functions->clean_string($input['price_temporary']),
          'price_half' => $this->functions->clean_string($input['price_half']),
          'price_day' => $this->functions->clean_string($input['price_day']),
          'price_holiday' => $this->functions->clean_string($input['price_holiday']),
          'bed_type' => $this->functions->clean_string($input['bed_type'])
        );

        $obj = $this->load_model('RoomType');
        $response = $obj->update_room_type($bind);

        switch ($response['status']) {
          case 'OK':
            $json = array(
              'status' => 'OK',
              'type' => 'success',
              'msg' => 'Registro actualizado en el sistema con exito.',
              'data' => array()
            );
            break;

          case 'ERROR':
            $json = array(
              'status' => 'ERROR',
              'type' => 'warning',
              'msg' => 'No fue posible guardar el registro ingresado, verificar.',
              'data' => array()
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
          'msg' => 'No se enviaron los campos necesarios, verificar.',
          'data' => array()
        );
      }
    } else {
      $json = array(
        'status' => 'ERROR',
        'type' => 'error',
        'msg' => 'Metodo no permitido.',
        'data' => array()
      );
    }

    header('Content-Type: application/json');
    echo json_encode($json);
  }
  public function delete_room_type()
  {
    $this->functions->validate_session($this->segment->get('isActive'));
    $request = $_SERVER['REQUEST_METHOD'];

    if ($request === 'POST') {
      $input = json_decode(file_get_contents('php://input'), true);
      if (empty($input)) {
        $input = filter_input_array(INPUT_POST);
      }

      if (!empty($input['id_type'])) {
        $bind = array(
          'id_type' => $this->functions->clean_string($input['id_type'])
        );

        $obj = $this->load_model('RoomType');
        $response = $obj->delete_room_type($bind);

        switch ($response['status']) {
          case 'OK':
            $json = array(
              'status' => 'OK',
              'type' => 'success',
              'msg' => 'Registro eliminado del sistema con exito.',
              'data' => array()
            );
            break;

          case 'ERROR':
            $json = array(
              'status' => 'ERROR',
              'type' => 'warning',
              'msg' => 'No fue posible eliminar el registro, verificar.',
              'data' => array()
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
          'msg' => 'No se enviaron los campos necesarios, verificar.',
          'data' => array()
        );
      }
    } else {
      $json = array(
        'status' => 'ERROR',
        'type' => 'error',
        'msg' => 'Metodo no permitido.',
        'data' => array()
      );
    }

    header('Content-Type: application/json');
    echo json_encode($json);
  }
}

<?php 
// --

use Spipu\Html2Pdf\Tag\Html\Em;

class C_Notifications extends Controller {

    // --
    public function __construct() {
		parent::__construct();
    }
    
    // --
    public function index() {
      // --
      $this->functions->validate_session($this->segment->get('isActive'));
  }

    // --
    public function get_reservations() { 
      // --
        $this->functions->validate_session($this->segment->get('isActive'));
      // --
        $request = $_SERVER['REQUEST_METHOD'];
      // --
        if ($request === 'GET') {
          // --
            $input = json_decode(file_get_contents('php://input'), true);
            if (empty($input)) {
                $input = filter_input_array(INPUT_GET);
            }
          // --
            $obj = $this->load_model('Notifications');
          // --
            $response = $obj->get_reservations();
          // --
            switch ($response['status']) {
              // --
                case 'OK':
                    // --
                    $json = array(
                      'status' => 'OK',
                      'type' => 'success',
                      'msg' => 'Listado de registros encontrados.',
                      'data' => $response['result']
                    );
                    // --
                    break;

                case 'ERROR':
                  // --
                    $json = array(
                        'status' => 'ERROR',
                        'type' => 'warning',
                        'msg' => 'No se encontraron registros en el sistema.',
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
                'type' => 'error',
                'msg' => 'Método no permitido.',
                'data' => array()
            ); 
        }

      // --
        header('Content-Type: application/json');
        echo json_encode($json);
    }

    public function get_cleaning() { 
      // --
        $this->functions->validate_session($this->segment->get('isActive'));
      // --
        $request = $_SERVER['REQUEST_METHOD'];
      // --
        if ($request === 'GET') {
          // --
            $input = json_decode(file_get_contents('php://input'), true);
            if (empty($input)) {
                $input = filter_input_array(INPUT_GET);
            }
          // --
            $obj = $this->load_model('Notifications');
          // --
            $response = $obj->get_cleaning();
          // --
            switch ($response['status']) {
              // --
                case 'OK':
                    // --
                    $json = array(
                      'status' => 'OK',
                      'type' => 'success',
                      'msg' => 'Listado de registros encontrados.',
                      'data' => $response['result']
                    );
                    // --
                    break;

                case 'ERROR':
                  // --
                    $json = array(
                        'status' => 'ERROR',
                        'type' => 'warning',
                        'msg' => 'No se encontraron registros en el sistema.',
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
                'type' => 'error',
                'msg' => 'Método no permitido.',
                'data' => array()
            ); 
        }

      // --
        header('Content-Type: application/json');
        echo json_encode($json);
    }


    public function get_notifications() { 
      // --
        $this->functions->validate_session($this->segment->get('isActive'));
      // --
        $request = $_SERVER['REQUEST_METHOD'];
      // --
        if ($request === 'GET') {
          // --
            $input = json_decode(file_get_contents('php://input'), true);
            if (empty($input)) {
                $input = filter_input_array(INPUT_GET);
            }
          // --
            if (isset($input['continue']) && is_numeric($input['continue'])) {
                $continue = $input['continue'];
            } else {
                $continue = 0;
            }

            $continue = intval($continue);
            
            $bind = array('continue' => $continue);

            $obj = $this->load_model('Notifications');
          // --
            $response = $obj->get_notifications($bind);
          // --
            switch ($response['status']) {
              // --
                case 'OK':
                    // --
                    $json = array(
                      'status' => 'OK',
                      'type' => 'success',
                      'msg' => 'Listado de registros encontrados.',
                      'data' => $response['result']
                    );
                    // --
                    break;

                case 'ERROR':
                  // --
                    $json = array(
                        'status' => 'ERROR',
                        'type' => 'warning',
                        'msg' => 'No se encontraron registros en el sistema.',
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
                'type' => 'error',
                'msg' => 'Método no permitido.',
                'data' => array()
            ); 
        }

      // --
        header('Content-Type: application/json');
        echo json_encode($json);
    }


    // public function get_notifications() { 
    //   // --
    //     $this->functions->validate_session($this->segment->get('isActive'));
    //   // --
    //     $request = $_SERVER['REQUEST_METHOD'];
    //   // --
    //     if ($request === 'GET') {
    //       // --
    //         $input = json_decode(file_get_contents('php://input'), true);
    //         if (empty($input)) {
    //             $input = filter_input_array(INPUT_GET);
    //         }
    //       // --
    //         $obj = $this->load_model('Notifications');
    //       // --
    //         $response = $obj->get_notifications();
    //       // --
    //         switch ($response['status']) {
    //           // --
    //             case 'OK':
    //                 // --
    //                 $json = array(
    //                   'status' => 'OK',
    //                   'type' => 'success',
    //                   'msg' => 'Listado de registros encontrados.',
    //                   'data' => $response['result']
    //                 );
    //                 // --
    //                 break;

    //             case 'ERROR':
    //               // --
    //                 $json = array(
    //                     'status' => 'ERROR',
    //                     'type' => 'warning',
    //                     'msg' => 'No se encontraron registros en el sistema.',
    //                     'data' => array(),
    //                 );
    //                 // --
    //                 break;

    //             case 'EXCEPTION':
    //               // --
    //                 $json = array(
    //                     'status' => 'ERROR',
    //                     'type' => 'error',
    //                     'msg' => $response['result']->getMessage(),
    //                     'data' => array()
    //                 );
    //                 // --
    //                 break;
    //         }

    //     } else {
    //       // --
    //         $json = array(
    //             'status' => 'ERROR',
    //             'type' => 'error',
    //             'msg' => 'Método no permitido.',
    //             'data' => array()
    //         ); 
    //     }

    //   // --
    //     header('Content-Type: application/json');
    //     echo json_encode($json);
    // }

    public function create_notifications()
    {
      // --
      $this->functions->validate_session($this->segment->get('isActive'));
      // --
      $request = $_SERVER['REQUEST_METHOD'];
      // --
      if ($request === 'POST') {
        // --
        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input)) {
          $input = filter_input_array(INPUT_POST);
        }
        // --
        if (
          !empty($input['date_notification']) &&
          !empty($input['time_notification']) &&
          !empty($input['type']) &&
          !empty($input['id_reservation']) &&
          !empty($input['sku_notification'])
        ) {
          // --
          $date_notification = $this->functions->clean_string($input['date_notification']);
          $time_notification = $this->functions->clean_string($input['time_notification']);
          $type = $this->functions->clean_string($input['type']);
          $id_reservation = $this->functions->clean_string($input['id_reservation']);
          $sku_notification = $this->functions->clean_string($input['sku_notification']);
          $status_notification = "New";
          // --
          $bind = array(
            'date_notification' => $date_notification,
            'time_notification' => $time_notification,
            'type' => $type,
            'id_reservation' => $id_reservation,
            'sku_notification' => $sku_notification,
            'status_notification' => $status_notification
          );
          // --
          $obj = $this->load_model('Notifications');
          $response = $obj->create_notifications($bind);
          // --
          switch ($response['status']) {
              // --
            case 'OK':
              // --
              $json = array(
                'status' => 'OK',
                'type' => 'success',
                'msg' => 'Nueva notificación.',
                'data' => array()
              );
              // --
              break;
  
            case 'ERROR':
              // --
              $json = array(
                'status' => 'ERROR',
                'type' => 'warning',
                'msg' => '',
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
            'msg' => 'No se enviaron los campos necesarios, verificar.',
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


    public function update_notification() { 
      // --
        $this->functions->validate_session($this->segment->get('isActive'));
      // --
        $request = $_SERVER['REQUEST_METHOD'];
      // --
        if ($request === 'POST') {
          // --
            $input = json_decode(file_get_contents('php://input'), true);
            if (empty($input)) {
                $input = filter_input_array(INPUT_GET);
            }
          // --
            $obj = $this->load_model('Notifications');
          // --
            $response = $obj->update_notification();
          // --
            switch ($response['status']) {
              // --
                case 'OK':
                    // --
                    $json = array(
                      'status' => 'OK',
                      'type' => 'success',
                      'msg' => 'Notificaciones vistas.',
                      'data' => $response['result']
                    );
                    // --
                    break;

                case 'ERROR':
                  // --
                    $json = array(
                        'status' => 'ERROR',
                        'type' => 'warning',
                        'msg' => 'Error al ver las notificaciones.',
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
                'type' => 'error',
                'msg' => 'Método no permitido.',
                'data' => array()
            ); 
        }

      // --
        header('Content-Type: application/json');
        echo json_encode($json);
    }
}
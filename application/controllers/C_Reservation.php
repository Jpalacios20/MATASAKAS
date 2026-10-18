<?php 
// --

use Spipu\Html2Pdf\Tag\Html\Em;

class C_Reservation extends Controller {

    // --
    public function __construct() {
		parent::__construct();
    }
    
    // --
    public function index() {
        // --
        $this->functions->validate_session($this->segment->get('isActive'));
        $this->functions->check_permissions($this->segment->get('modules'), 'Reservation');
        // --
        $this->view->set_js('index');       // -- Load JS
        $this->view->set_menu(array('modules' => $this->segment->get('modules'), 'view' => 'Reservation')); // -- Active Menu
        $this->view->set_view('index');     // -- Load View
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
            $obj = $this->load_model('Reservation');
          // --
            $response = $obj->get_reservations();
          // --
            switch ($response['status']) {
              // --
                case 'OK':
                    $data = array();
                  // --
                    foreach ($response['result'] as $item) {
                      // --
                      // --
                        $data[] = array(
    'id_reservation' => $item['id_reservation'],
    'id_payment' => $item['id_payment'],
    'checkin_date' => $item['checkin_date'],
    'checkin_time' => $item['checkin_time'],
    'checkout_date' => $item['checkout_date'],
    'checkout_time' => $item['checkout_time'],
    'status' => $item['status'],
    'room_number' => $item['room_number'],
    'room_status' => $item['room_status'],
    'type_name' => $item['type_name'],
    'person_limit' => $item['person_limit'],
    'bed_type' => $item['bed_type'],
    'id_person' => $item['id_person'],
    'cliente' => $item['cliente'],
    'document_number' => $item['document_number'],
    'business_name' => $item['business_name'],
    'payment_total' => $item['payment_total'],
    'payment_room' => $item['payment_room']
                        );
                    }
                  // --
                    $json = array(
                        'status' => 'OK',
                        'type' => 'success',
                        'msg' => $this->messages->message['list'],
                        'data' => $data
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

    // --
    public function get_reservation() {
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
            if (!empty($input['id_reservation'])) {
              // --
                $obj = $this->load_model('Reservation');
              // --
                $bind = array(
                    'id_reservation' => intval($input['id_reservation']));
              // --
                $response = $obj->get_reservation($bind);
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



//--- funcion para el modulo ventas
//-------


public function product_sales_details(){
  $this->functions->validate_session($this->segment->get('isActive'));
  $request = $_SERVER['REQUEST_METHOD'];
  if($request === 'POST'){
    $input = json_decode(file_get_contents('php://input'), true);
    if(empty($input)){
      $input = filter_input_array(INPUT_POST);
      file_put_contents('php://stderr', print_r($input, true));
    }

    if(
      !empty($input['id_reservation']) &&
      !empty($input['id_payment']) &&
      isset($input['id_product']) && is_array($input['id_product']) &&
      !empty($input['fecha_venta']) &&
      !empty($input['product_sku']) &&
      !empty($input['product_name']) &&
      !empty($input['cantidad']) &&
      !empty($input['product_price'])
      
    ){


      $products = [];
      foreach ($input['id_product'] as $key => $id_product){
        $products[] = [
          'id_reservation' => $this -> functions->clean_string($input['id_reservation']),
          'id_payment' => $this -> functions->clean_string($input['id_payment']),
          'id_product' => $this->functions->clean_string($id_product),
          'fecha_venta' => $this->functions->clean_string($input['fecha_venta'][$key]),
          'product_sku' => $this->functions->clean_string($input['product_sku'][$key]),
          'product_name' => $this->functions->clean_string($input['product_name'][$key]),
          'cantidad' => $this->functions->clean_string($input['cantidad'][$key]),
          'product_price' => $this->functions->clean_string($input['product_price'][$key]),
        ];
      }

      

          $obj = $this->load_model('Reservation');
          $response = $obj->product_sales_details($products);

            switch ($response['status']) {
              // --
            case 'OK':
              // --
              $json = array(
                'status' => 'OK',
                'type' => 'success',
                'msg' => 'Registro actualizado en el sistema con éxito.',
                'data' => array()
              );
              // --
              break;

            case 'ERROR':
              // --
              $json = array(
                'status' => 'ERROR',
                'type' => 'warning',
                'msg' => 'No fue posible guardar el registro ingresado, verificar.',
                'data' => array(),
              );
              // --
              break;

            case 'EXCEPTION':
              // --
              $json = array(
                'status' => 'ERROR',
                'type' => 'error',
                'msg' => $response['result'],
                'data' => array()
              );
              // --
              break;
          }
        } else{
          $json = array(
            'status' => 'ERROR',
            'type' => 'warning',
            'msg' => 'No se enviaron los campos necesarios, verificar.',
            'data' => array()
          );
        }
  } else{
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







//-------------------
//--------------------------


// --

  public function get_sales_food() {
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
          if (!empty($input['id_reservation'])) {
              // --
              $obj = $this->load_model('Reservation');
              // --
              $bind = array(
                  'id_reservation' => intval($input['id_reservation']));
              // --
              $response = $obj->get_sales_food($bind);
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

  public function get_sales_accessory() {
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
        if (!empty($input['id_reservation'])) {
            // --
            $obj = $this->load_model('Reservation');
            // --
            $bind = array(
                'id_reservation' => intval($input['id_reservation']));
            // --
            $response = $obj->get_sales_accessory($bind);
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

// --

  public function get_payment_extra() { 
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
          $obj = $this->load_model('Reservation');
        // --
          $response = $obj->get_payment_extra();
        // --
          switch ($response['status']) {
            // --
              case 'OK':
                  $data = array();
                // --
                  foreach ($response['result'] as $item) {
                    // --
                    // --
                      $data[] = array(
                          'extra_time' => $item['extra_time'],
                          'price_extra' => $item['price_extra']
                      );
                  }
                // --
                  $json = array(
                      'status' => 'OK',
                      'type' => 'success',
                      'msg' => $this->messages->message['list'],
                      'data' => $data
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

  public function update_reservation()
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
      $roomStatus = $input['room_status'];
      $valueCheckout = true;
      // --
      if($roomStatus != 'Libre'){
        $valueCheckout = !empty($input['checkout_date']) && !empty($input['checkout_time']);
      }else{
        $valueCheckout = true;
      }
      
      if (
        !empty($input['id_reservation']) &&
        !empty($input['id_room']) &&
        !empty($input['id_payment']) &&
        !empty($input['checkin_date']) &&
        !empty($input['checkin_time']) &&
        !empty($input['payment_room']) &&
        $valueCheckout &&
        !empty($roomStatus) && ($roomStatus === 'Ocupado' || $roomStatus === 'Reservado' || $roomStatus == 'Libre')
      ) {
        // --
        $id_room = $this->functions->clean_string($input['id_room']);
        $id_reservation = $this->functions->clean_string($input['id_reservation']);
        $id_payment = $this->functions->clean_string($input['id_payment']);
        $checkin_date = $this->functions->clean_string($input['checkin_date']);
        $checkout_date = $this->functions->clean_string($input['checkout_date']);
        $checkin_time = $this->functions->clean_string($input['checkin_time']);
        $checkout_time = $this->functions->clean_string($input['checkout_time']);
        $payment_room = $this->functions->clean_string($input['payment_room']);
        $status = $this->functions->clean_string($roomStatus);

        // --
        $bind = array(
          'id_room' => $id_room,
          'id_reservation' => $id_reservation,
          'id_payment' => $id_payment,
          'payment_room' => $payment_room,
          'checkin_date' => $checkin_date,
          'checkout_date' => $checkout_date,
          'checkin_time' => $checkin_time,
          'checkout_time' => $checkout_time,
          'status' => $status
        );
        // --
        $obj = $this->load_model('Reservation');
        $response = $obj->update_reservation($bind);
        // --
        switch ($response['status']) {
            // --
          case 'OK':
            // --
            $json = array(
              'status' => 'OK',
              'type' => 'success',
              'msg' => 'Registro actualizado en el sistema con éxito.',
              'data' => array()
            );
            // --
            break;

          case 'ERROR':
            // --
            $json = array(
              'status' => 'ERROR',
              'type' => 'warning',
              'msg' => 'No fue posible guardar el registro ingresado, verificar.',
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

  public function update_payment()
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
        !empty($input['id_reservation']) &&
        !empty($input['id_payment']) &&
        !empty($input['payment_cancelled']) &&
        !empty($input['payment_total']) &&
        !empty($input['departure_date']) &&
        !empty($input['departure_time']) 
      ) {
        // --
        $id_reservation = $this->functions->clean_string($input['id_reservation']);
        $id_payment = $this->functions->clean_string($input['id_payment']);
        $payment_extra = $this->functions->clean_string($input['payment_extra']);
        $payment_discount = $this->functions->clean_string($input['payment_discount']);
        $pre_payment = $this->functions->clean_string($input['payment_cancelled']);
        $departure_date = $this->functions->clean_string($input['departure_date']);
        $departure_time = $this->functions->clean_string($input['departure_time']);
        $payment_total = $this->functions->clean_string($input['payment_total']);
        // --
        $bind = array(
          'id_reservation' => $id_reservation,
          'id_payment' => $id_payment,
          'payment_extra' => $payment_extra,
          'payment_discount' => $payment_discount,
          'pre_payment' => $pre_payment,
          'departure_date' => $departure_date,
          'departure_time' => $departure_time,
          'payment_total' => $payment_total
        );
        // --
        $obj = $this->load_model('Reservation');
        $response = $obj->update_payment($bind);
        // --
        switch ($response['status']) {
            // --
          case 'OK':
            // --
            $json = array(
              'status' => 'OK',
              'type' => 'success',
              'msg' => 'Registro actualizado en el sistema con éxito.',
              'data' => array()
            );
            // --
            break;

          case 'ERROR':
            // --
            $json = array(
              'status' => 'ERROR',
              'type' => 'warning',
              'msg' => 'No fue posible guardar el registro ingresado, verificar.',
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


  public function finish_reservation()
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
        !empty($input['id_reservation']) &&
        !empty($input['id_room'])
      ) {
        // --
        $id_reservation = $this->functions->clean_string($input['id_reservation']);
        $id_room = $this->functions->clean_string($input['id_room']);
        // --
        $bind = array(
          'id_room' => $id_room,
          'id_reservation' => $id_reservation
        );
        // --
        $obj = $this->load_model('Reservation');
        $response = $obj->finish_reservation($bind);
        // --
        switch ($response['status']) {
            // --
          case 'OK':
            // --
            $json = array(
              'status' => 'OK',
              'type' => 'success',
              'msg' => 'Reserva finalizada.',
              'data' => array()
            );
            // --
            break;

          case 'ERROR':
            // --
            $json = array(
              'status' => 'ERROR',
              'type' => 'warning',
              'msg' => 'No fue posible finalizar el registro ingresado, verificar.',
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

  public function date_reservation() {
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
          if (
            !empty($input['id_room'])  &&
            !empty($input['id_reservation'])  &&
            !empty($input['checkin_date']) &&
            !empty($input['checkout_date'])
          ) 
          {
            // --
              $obj = $this->load_model('Reservation');
            // --
              $bind = array(
                  'id_room' => intval($input['id_room']),
                  'checkin_date' => $input['checkin_date'],
                  'checkout_date' => $input['checkout_date'],
                  'id_reservation' => intval($input['id_reservation'])
                );
            // --
              $response = $obj->date_reservation($bind);
            // --
              switch ($response['status']) {
                // --
                  case 'OK':
                    // --
                      $json = array(
                          'status' => 'OK',
                          'type' => 'success',
                          'msg' => 'Fechas correctas.',
                          'data' => array()
                      );
                    // --
                      break;

                  case 'ERROR':
                    // --
                      $json = array(
                          'status' => 'ERROR',
                          'type' => 'warning',
                          'msg' => 'La habitación ya está reservada para esas fechas.',
                          'data' => array()
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
}

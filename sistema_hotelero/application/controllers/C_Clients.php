<?php 
// --
class C_Clients extends Controller {

  // --
  public function __construct() {
   parent::__construct();
  }

  // --
  public function index() {
    // --
    $this->functions->validate_session($this->segment->get('isActive'));
    $this->functions->check_permissions($this->segment->get('modules'), 'Clients');
    
    // --
    $this->view->set_js('index');       // Load JS
    $this->view->set_menu(array('modules' => $this->segment->get('modules'), 'view' => 'Clients')); // Active Menu
    $this->view->set_view('index');     // Load View
  }
  
  // VALIDACIÓN DE DUPLICADOS (AJAX)
  public function check_duplicate() {
      $this->functions->validate_session($this->segment->get('isActive'));
      $request = $_SERVER['REQUEST_METHOD'];

      if ($request === 'POST') {
          $input = filter_input_array(INPUT_POST);
          
          $document_number = $this->functions->clean_string($input['document_number'] ?? '');
          $document_type = $this->functions->clean_string($input['document_type'] ?? '');

          $obj = $this->load_model('Clients');
          $exists = $obj->verify_document_exists($document_number, $document_type);

          $json = array('exists' => $exists);
      } else {
          $json = array('exists' => false, 'error' => 'Método no permitido');
      }

      header('Content-Type: application/json');
      echo json_encode($json);
  }

  // Obtener lista de clientes
  public function get_clients() {
    $this->functions->validate_session($this->segment->get('isActive'));
    $request = $_SERVER['REQUEST_METHOD'];

    if ($request === 'GET') {
      $input = json_decode(file_get_contents('php://input'), true);
      if (empty($input)) {
        $input = filter_input_array(INPUT_GET);
      }

      $obj = $this->load_model('Clients');
      $response = $obj->get_clients();

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

  // Obtener cliente por ID
  public function get_client_by_id() {
    $this->functions->validate_session($this->segment->get('isActive'));
    $request = $_SERVER['REQUEST_METHOD'];

    if ($request === 'GET') {
      $input = json_decode(file_get_contents('php://input'), true);
      if (empty($input)) {
        $input = filter_input_array(INPUT_GET);
      }

      if (!empty($input['id_clients'])) {
        $obj = $this->load_model('Clients');
        $bind = array('id_clients' => intval($input['id_clients']));
        $response = $obj->get_client_by_id($bind);

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
              'msg' => 'No se encontró el cliente.',
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
          'msg' => 'No se envió un ID válido.',
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

  public function get_client_by_dni()
{
    $this->functions->validate_session($this->segment->get('isActive'));
    $request = $_SERVER['REQUEST_METHOD'];

    if ($request === 'GET') {

        $input = filter_input_array(INPUT_GET);

        if (!empty($input['dni'])) {

            $dni = $this->functions->clean_string($input['dni']);

            $obj = $this->load_model('Clients');
            $bind = array('document_number' => $dni);

            $response = $obj->get_client_by_dni($bind);

            if ($response['status'] === 'OK') {

                $json = array(
                    'status' => 'OK',
                    'data' => $response['result']
                );

            } else {

                $json = array(
                    'status' => 'ERROR'
                );
            }

        } else {

            $json = array(
                'status' => 'ERROR',
                'msg' => 'DNI vacío'
            );
        }

    } else {

        $json = array(
            'status' => 'ERROR',
            'msg' => 'Método no permitido'
        );
    }

    header('Content-Type: application/json');
    echo json_encode($json);
}

  //Jalar el nombre de Clients
  public function get_name() {
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
        $obj = $this->load_model('Clients');
        // --
        $response = $obj->get_name();
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
  public function create_clients() {
    // Se valida si la sesión está activa
    $this->functions->validate_session($this->segment->get('isActive'));
    
    // Verifica el método de la solicitud (debe ser POST para crear un cliente)
    $request = $_SERVER['REQUEST_METHOD'];
    
    if ($request === 'POST') {
        // Obtiene los datos del cuerpo de la solicitud (puede ser JSON o POST)
        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input)) {
            // Si no se recibe un JSON, se obtiene como datos de un formulario POST
            $input = filter_input_array(INPUT_POST);
        }
        
        // Verifica que todos los campos obligatorios estén presentes en los datos recibidos
        if (!empty($input['document_type']) &&
            !empty($input['name']) &&
            !empty($input['document_number']) &&
            !empty($input['nationality'])) {

            // Limpieza y validación de los datos obligatorios
            $document_type = $this->functions->clean_string($input['document_type']);
            $name = $this->functions->clean_string(strtoupper(ucfirst($input['name']))); // Se convierte el nombre a formato adecuado
            $document_number = $this->functions->clean_string($input['document_number']);
            $nationality = $this->functions->clean_string($input['nationality']);

            // Limpieza y asignación de valores para los campos opcionales (permitiendo nulos)
            $birth_date = !empty($input['birth_date']) ? $this->functions->clean_string($input['birth_date']) : null;
            $email = !empty($input['email']) ? $this->functions->clean_string($input['email']) : null;
            $phone = !empty($input['phone']) ? $this->functions->clean_string($input['phone']) : null;
            $address = !empty($input['address']) ? $this->functions->clean_string($input['address']) : null;
            $business_name = !empty($input['business_name']) ? $this->functions->clean_string($input['business_name']) : null;

            // Prepara los datos para la inserción
            $bind = array(
                'id_document_type' => $document_type,
                'name' => $name,
                'document_number' => $document_number,
                'nationality' => $nationality,
                'birth_date' => $birth_date,
                'email' => $email,
                'phone' => $phone,
                'address' => $address,
                'business_name' => $business_name
            );

            // Se carga el modelo para manejar la creación del cliente
            $obj = $this->load_model('Clients');
            $response = $obj->create_clients($bind); // Inserción en la base de datos

            // Manejo de la respuesta según el resultado
            switch ($response['status']) {
                case 'OK':
                    $json = array(
                        'status' => 'OK',
                        'type' => 'success',
                        'msg' => 'Registro almacenado en el sistema con éxito.',
                        'data' => array()
                    );
                    break;

                case 'ERROR':
                    $json = array(
                        'status' => 'ERROR',
                        'type' => 'warning',
                        'msg' => 'No fue posible guardar el registro ingresado, verificar.',
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
            // Si faltan campos obligatorios, se retorna un mensaje de advertencia
            $json = array(
                'status' => 'ERROR',
                'type' => 'warning',
                'msg' => 'No se enviaron los campos obligatorios, verificar.',
                'data' => array()
            );
        }
    } else {
        // Si el método de la solicitud no es POST, se retorna un mensaje de error
        $json = array(
            'status' => 'ERROR',
            'type' => 'error',
            'msg' => 'Método no permitido.',
            'data' => array()
        );
    }

    // Se establece que la respuesta será en formato JSON
    header('Content-Type: application/json');
    // Se imprime la respuesta en formato JSON
    echo json_encode($json);
  }

  // --
  public function update_clients() {
    $this->functions->validate_session($this->segment->get('isActive'));
    $request = $_SERVER['REQUEST_METHOD'];
    
    if ($request === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input)) {
            $input = filter_input_array(INPUT_POST);
        }

        // Verificar campos obligatorios
        if (!empty($input['id_clients']) && 
            !empty($input['document_type']) && 
            !empty($input['name']) &&
            !empty($input['document_number']) &&
            !empty($input['nationality']) &&
            !empty($input['description_document_type']) 
        ) {
            // Limpieza de valores obligatorios
            $id_clients = $this->functions->clean_string($input['id_clients']);
            $document_type = $this->functions->clean_string($input['document_type']);
            $name = $this->functions->clean_string(strtoupper(ucfirst($input['name'])));
            $document_number = $this->functions->clean_string($input['document_number']);
            $description_document_type = $this->functions->clean_string($input['description_document_type']);
            $nationality = $this->functions->clean_string($input['nationality']);

            // Limpieza de valores opcionales (pueden ser null)
            $business_name = isset($input['business_name']) ? $this->functions->clean_string($input['business_name']) : null;
            $birth_date = isset($input['birth_date']) ? $this->functions->clean_string($input['birth_date']) : null;
            $birth_place = isset($input['birth_place']) ? $this->functions->clean_string($input['birth_place']) : null;
            $address = isset($input['address']) ? $this->functions->clean_string($input['address']) : null;
            $phone = isset($input['phone']) ? $this->functions->clean_string($input['phone']) : null;
            $email = isset($input['email']) ? $this->functions->clean_string($input['email']) : null;

            // Verificación de tipo de documento
            $is_verified = $this->functions->verified_document_type($description_document_type, $document_number);

            if ($is_verified) {
                // Preparar datos para la actualización
                $bind = array(
                    'id_clients' => $id_clients,
                    'id_document_type' => $document_type,
                    'name' => $name,
                    'document_number' => $document_number,
                    'nationality' => $nationality,
                    'birth_date' => $birth_date,
                    'birth_place' => $birth_place,
                    'address' => $address,
                    'phone' => $phone,
                    'business_name' => $business_name, // Puede ser null
                    'email' => $email
                );

                $obj = $this->load_model('Clients');
                $response = $obj->update_clients($bind);

                switch ($response['status']) {
                    case 'OK':
                        $json = array(
                            'status' => 'OK',
                            'type' => 'success',
                            'msg' => 'Registro actualizado en el sistema con éxito.',
                            'data' => array()
                        );
                        break;

                    case 'ERROR':
                        $json = array(
                            'status' => 'ERROR',
                            'type' => 'warning',
                            'msg' => 'No fue posible actualizar el registro, verificar.',
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
                    'msg' => 'Número de documento inválido, verificar.',
                );
            }
        } else {
            $json = array(
                'status' => 'ERROR',
                'type' => 'warning',
                'msg' => 'No se enviaron los campos obligatorios, verificar.',
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
  // -- Eliminar cliente
    public function delete_clients(): void {
    $this->functions->validate_session($this->segment->get('isActive')); // Verificar la sesión activa
    $request = $_SERVER['REQUEST_METHOD']; // Obtener el método de la solicitud

    if ($request === 'POST') { // Si el método es POST
        // Obtener los datos enviados (ya sea JSON o POST tradicional)
        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input)) {
            $input = filter_input_array(INPUT_POST);
        }

        // Verificar si se envió el id_cliente
        if (!empty($input['id_clients'])) {
            $bind = array(
                'id_clients' => intval($input['id_clients']) // Bind de id_cliente
            );

            // Cargar el modelo de clientes
            $obj = $this->load_model('Clients');
            // Llamar a la función de eliminación del modelo
            $response = $obj->delete_clients($bind);

            // Evaluar la respuesta y enviar el mensaje correspondiente
            switch ($response['status']) {
                case 'OK':
                    $json = array(
                        'status' => 'OK',
                        'type' => 'success',
                        'msg' => 'Cliente eliminado exitosamente.',
                        'data' => array()
                    );
                    break;
                case 'ERROR':
                    $json = array(
                        'status' => 'ERROR',
                        'type' => 'warning',
                        'msg' => 'No se pudo eliminar el cliente, verificar.',
                        'data' => array(),
                    );
                    break;
                case 'EXCEPTION':
                    $json = array(
                        'status' => 'ERROR',
                        'type' => 'error',
                        'msg' => $response['result'], // Mostrar el error detallado
                        'data' => array()
                    );
                    break;
            }
        } else {
            // Si no se envió el id_cliente
            $json = array(
                'status' => 'ERROR',
                'type' => 'warning',
                'msg' => 'No se enviaron los campos necesarios, verificar.',
                'data' => array()
            );
        }
    } else {
        // Si el método de la solicitud no es POST
        $json = array(
            'status' => 'ERROR',
            'type' => 'error',
            'msg' => 'Método no permitido.',
            'data' => array()
        );
    }

    // Devolver la respuesta en formato JSON
    header('Content-Type: application/json');
    echo json_encode($json);
    }

    //----------------------------------------------------------------
    public function get_business_name() {
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
            $obj = $this->load_model('Clients');
            // --
            $response = $obj->get_business_name();
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
    public function get_business_name_cli()
    {
        // --
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
            $obj = $this->load_model('Clients');
            // --
            $response = $obj->get_business_name_cli();
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
    
   
    //----------------------------------------------------------------
   // Función para obtener los datos de la empresa mediante consulta a RENIEC o SUNAT
    public function get_company_data()
    {
        // Validación de la sesión, asegurándose de que el usuario esté activo
        $this->functions->validate_session($this->segment->get('isActive'));
        
        // Obtener el método de la solicitud HTTP (en este caso GET)
        $request = $_SERVER['REQUEST_METHOD'];

        // Si la solicitud es un GET
        if ($request === 'GET') {
            // Filtrar los datos recibidos a través de GET
            $input = filter_input_array(INPUT_GET);

            // Verificar si el número de documento ha sido enviado
            if (!empty($input['nroDoc'])) {
                // Limpiar el número de documento para evitar inyecciones de código
                $nroDoc = $this->functions->clean_string($input['nroDoc']);
                
                // Token de autorización para acceder a la API externa
                $token = getenv('APIS_TOKEN');
                if ($token === false || $token === '') {
                    $json = array(
                        'status' => 'ERROR',
                        'type' => 'error',
                        'msg' => 'La integración de consulta de documentos no está configurada.',
                        'data' => array()
                    );
                    echo json_encode($json);
                    return;
                }

                // Validar el formato del número de documento
                if (strlen($nroDoc) == 8) {
                    // Si el número de documento es de 8 dígitos, se consulta a RENIEC
                    $url = 'https://api.apis.net.pe/v2/reniec/dni?numero=' . $nroDoc;
                } elseif (strlen($nroDoc) == 11) {
                    // Si el número de documento es de 11 dígitos, se consulta a SUNAT
                    $url = 'https://api.apis.net.pe/v2/sunat/ruc?numero=' . $nroDoc;                 
                } else {
                    // Si el número de documento no es válido, se retorna un error
                    $json = array(
                        'status' => 'ERROR',
                        'type' => 'error',
                        'msg' => 'Número de documento inválido.',
                        'data' => array()
                    );
                    // Mostrar el mensaje de error en formato JSON
                    echo json_encode($json);
                    return; // Terminar la ejecución de la función
                }

                // Inicializar la sesión cURL para hacer la solicitud HTTP
                $curl = curl_init();
                // Configurar las opciones de cURL
                curl_setopt_array($curl, array(
                    CURLOPT_URL => $url,  // URL de la API que consulta los datos
                    CURLOPT_RETURNTRANSFER => true,  // Retornar la respuesta como string
                    CURLOPT_SSL_VERIFYPEER => 0,  // Desactivar la verificación de SSL (opcional)
                    CURLOPT_CUSTOMREQUEST => 'GET',  // Método de la solicitud HTTP (GET)
                    CURLOPT_HTTPHEADER => array(
                        'Referer: https://apis.net.pe',  // Referencia de la solicitud
                        'Authorization: Bearer ' . $token  // Token de autenticación
                    ),
                ));

                // Ejecutar la solicitud cURL y obtener la respuesta
                $response = curl_exec($curl);
                // Cerrar la sesión cURL
                curl_close($curl);

                // Decodificar la respuesta JSON de la API
                $empresa = json_decode($response);

                // Verificar si la respuesta de la API es válida
                if ($empresa && !isset($empresa->message)) {
                    // Si los datos son válidos, se retornan en formato JSON
                    $json = array(
                        'status' => 'OK',
                        'type' => 'success',
                        'msg' => 'Datos obtenidos con éxito.',
                        'data' => $empresa
                    );
                } else {
                    // Si hubo un error o no se recibieron datos válidos, se retorna un mensaje de error
                    $json = array(
                        'status' => 'ERROR',
                        'type' => 'error',
                        'msg' => $empresa->message ?? 'Error desconocido al consultar.',
                        'data' => array()
                    );
                }
                // Mostrar el resultado en formato JSON
                echo json_encode($json);
            } else {
                // Si no se envió el número de documento, se retorna un error
                $json = array(
                    'status' => 'ERROR',
                    'type' => 'error',
                    'msg' => 'No se envió el número de documento.',
                    'data' => array()
                );
                // Mostrar el mensaje de error en formato JSON
                echo json_encode($json);
            }
        }
    }

}

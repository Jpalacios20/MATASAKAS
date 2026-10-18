<?php
// --
class C_Product extends Controller
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
    $this->functions->check_permissions($this->segment->get('modules'), 'Product');
    // --
    $this->view->set_js('index');       // -- Load JS
    $this->view->set_menu(array('modules' => $this->segment->get('modules'), 'view' => 'Product')); // -- Active Menu
    $this->view->set_view('index');     // -- Load View
  }
  public function get_product() 
    {
        $this->functions->validate_session($this->segment->get('isActive'));
        $request = $_SERVER['REQUEST_METHOD'];

        if ($request === 'GET') {
            $obj = $this->load_model('Product');
            $response = $obj->get_product();

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
                default:
                    $json = array(
                        'status' => 'ERROR',
                        'type' => 'error',
                        'msg' => 'Respuesta desconocida del servidor.',
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
    // -- Obtener todos los productos
  public function get_products() {
    $this->functions->validate_session($this->segment->get('isActive'));
    $request = $_SERVER['REQUEST_METHOD'];

    if ($request === 'GET') {
        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input)) {
            $input = filter_input_array(INPUT_GET);
        }

        $obj = $this->load_model('Product');
        $response = $obj->get_product();

        switch ($response['status']) {
            case 'OK':
                $json = array(
                    'status' => 'OK',
                    'type' => 'success',
                    'msg' => 'Listado de productos encontrados.',
                    'data' => $response['result']
                );
                break;
            case 'ERROR':
                $json = array(
                    'status' => 'ERROR',
                    'type' => 'warning',
                    'msg' => 'No se encontraron productos.',
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

  

  // -- Obtener producto por ID
  public function get_product_by_id() {
  $this->functions->validate_session($this->segment->get('isActive'));

  $request = $_SERVER['REQUEST_METHOD'];

  if ($request === 'GET') {
      $input = json_decode(file_get_contents('php://input'), true);
      if (empty($input)) {
          $input = filter_input_array(INPUT_GET);
      }

      if (!empty($input['id_product'])) {
          $obj = $this->load_model('Product');
          $bind = array('id_product' => intval($input['id_product']));
          $response = $obj->get_product_by_id($bind);

          switch ($response['status']) {
              case 'OK':
                  $json = array(
                      'status' => 'OK',
                      'type' => 'success',
                      'msg' => 'Producto encontrado.',
                      'data' => $response['result']
                  );
                  break;

              case 'ERROR':
                  $json = array(
                      'status' => 'ERROR',
                      'type' => 'warning',
                      'msg' => 'No se encontró el producto.',
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
  

  // -- Obtener categorías para el formulario
  public function get_categories() {
  $this->functions->validate_session($this->segment->get('isActive'));
  
  $obj = $this->load_model('Categories');
  $response = $obj->get_categories();

  switch ($response['status']) {
      case 'OK':
          $json = array(
              'status' => 'OK',
              'type' => 'success',
              'msg' => 'Listado de categorías encontrado.',
              'data' => $response['result']
          );
          break;
      case 'ERROR':
          $json = array(
              'status' => 'ERROR',
              'type' => 'warning',
              'msg' => 'No se encontraron categorías.',
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

  header('Content-Type: application/json');
  echo json_encode($json);
  }

  // -- Crear Producto
  public function create_product() {
  $this->functions->validate_session($this->segment->get('isActive'));

  $request = $_SERVER['REQUEST_METHOD'];

  if ($request === 'POST') {
      $input = json_decode(file_get_contents('php://input'), true);
      if (empty($input)) {
          $input = filter_input_array(INPUT_POST);
      }

      if (
        !empty($input['product_sku']) && 
        !empty($input['product_name']) && 
        !empty($input['id_category'])) {
          // Asignar campos y limpiar las cadenas
          $product_sku = $this->functions->clean_string($input['product_sku']);
          $product_name = $this->functions->clean_string($input['product_name']);
          $product_description = $this->functions->clean_string($input['product_description']);
          $id_category = intval($input['id_category']);
          $expiration_date = $input['expiration_date'];
          $status_expiration_date = intval($input['status_expiration_date']);

          // Si no se proporciona un precio o stock, asignarles un valor por defecto
          $product_price = isset($input['product_price']) && !empty($input['product_price']) ? floatval($input['product_price']) : 0.00;
          $product_stock = isset($input['product_stock']) && !empty($input['product_stock']) ? intval($input['product_stock']) : 0;

          // Preparar los datos para el modelo
          $bind = array(
              'product_sku' => $product_sku,
              'product_name' => $product_name,
              'product_description' => $product_description,
              'id_category' => $id_category,
              'product_price' => $product_price,
              'product_stock' => $product_stock,
              'expiration_date' => $expiration_date,
              'status_expiration_date' => $status_expiration_date
          );

          $obj = $this->load_model('Product');
          $response = $obj->create_product($bind);

          switch ($response['status']) {
              case 'OK':
                  $json = array('status' => 'OK', 'type' => 'success', 'msg' => 'Producto creado exitosamente.', 'data' => array());
                  break;
              case 'ERROR':
                  $json = array('status' => 'ERROR', 'type' => 'warning', 'msg' => 'No se pudo crear el producto.', 'data' => array());
                  break;
              case 'EXCEPTION':
                  $json = array('status' => 'ERROR', 'type' => 'error', 'msg' => $response['result']->getMessage(), 'data' => array());
                  break;
          }
      } else {
          $json = array('status' => 'ERROR', 'type' => 'warning', 'msg' => 'Campos obligatorios faltantes.', 'data' => array());
      }
  } else {
      $json = array('status' => 'ERROR', 'type' => 'error', 'msg' => 'Método no permitido.', 'data' => array());
  }

  header('Content-Type: application/json');
  echo json_encode($json);
  }
  

  // -- Actualizar Producto
  public function update_product() {
  $this->functions->validate_session($this->segment->get('isActive'));

  $request = $_SERVER['REQUEST_METHOD'];

  if ($request === 'POST') {
      $input = json_decode(file_get_contents('php://input'), true);
      if (empty($input)) {
          $input = filter_input_array(INPUT_POST);
      }

      if (!empty($input['id_product']) && !empty($input['product_sku']) && !empty($input['product_name']) && !empty($input['id_category'])) {
          // Asignar campos y limpiar las cadenas
          $id_product = intval($input['id_product']);
          $product_sku = $this->functions->clean_string($input['product_sku']);
          $product_name = $this->functions->clean_string($input['product_name']);
          $product_description = $this->functions->clean_string($input['product_description']);
          $id_category = intval($input['id_category']);
          $expiration_date = $input['expiration_date'];
          $status_expiration_date = intval($input['status_expiration_date']);

          // Asegurar que siempre haya un valor para precio y stock (0 si no se proporciona)
          $product_price = isset($input['product_price']) && !empty($input['product_price']) ? floatval($input['product_price']) : 0.00;
          $product_stock = isset($input['product_stock']) && !empty($input['product_stock']) ? intval($input['product_stock']) : 0;

          // Preparar los datos para el modelo
          $bind = array(
              'id_product' => $id_product,
              'product_sku' => $product_sku,
              'product_name' => $product_name,
              'product_description' => $product_description,
              'id_category' => $id_category,
              'product_price' => $product_price,
              'product_stock' => $product_stock,
              'expiration_date' => $expiration_date,
              'status_expiration_date' => $status_expiration_date
          );

          $obj = $this->load_model('Product');
          $response = $obj->update_product($bind);

          switch ($response['status']) {
              case 'OK':
                  $json = array('status' => 'OK', 'type' => 'success', 'msg' => 'Producto actualizado exitosamente.', 'data' => array());
                  break;
              case 'ERROR':
                  $json = array('status' => 'ERROR', 'type' => 'warning', 'msg' => 'No se pudo actualizar el producto.', 'data' => array());
                  break;
              case 'EXCEPTION':
                  $json = array('status' => 'ERROR', 'type' => 'error', 'msg' => $response['result']->getMessage(), 'data' => array());
                  break;
          }
      } else {
          $json = array('status' => 'ERROR', 'type' => 'warning', 'msg' => 'Campos obligatorios faltantes.', 'data' => array());
      }
  } else {
      $json = array('status' => 'ERROR', 'type' => 'error', 'msg' => 'Método no permitido.', 'data' => array());
  }

  header('Content-Type: application/json');
  echo json_encode($json);
  }

  // -- Eliminar producto
  public function delete_product() {
    $this->functions->validate_session($this->segment->get('isActive'));
    $request = $_SERVER['REQUEST_METHOD'];

    if ($request === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input)) {
            $input = filter_input_array(INPUT_POST);
        }

        if (!empty($input['id_product'])) {
            $bind = array(
                'id_product' => intval($input['id_product'])
            );

            $obj = $this->load_model('Product');
            $response = $obj->delete_product($bind);

            switch ($response['status']) {
                case 'OK':
                    $json = array(
                        'status' => 'OK',
                        'type' => 'success',
                        'msg' => 'Producto eliminado exitosamente.',
                        'data' => array()
                    );
                    break;
                case 'ERROR':
                    $json = array(
                        'status' => 'ERROR',
                        'type' => 'warning',
                        'msg' => 'No se pudo eliminar el producto, verificar.',
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
}
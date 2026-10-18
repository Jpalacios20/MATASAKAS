<?php
class C_Restobar extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    private function auth()
    {
        // Solo valida la sesión. El acceso a cada opción lo controla el menú (tabla permission).
        $this->functions->validate_session($this->segment->get('isActive'));
    }

    public function index()
    {
        $this->auth();
        header('Location: ' . BASE_URL . 'Restobar/Bebidas');
        exit;
    }

    public function Bebidas()
    {
        $this->auth();
        $this->view->set_js('bebidas');
        $this->view->set_menu(['modules' => $this->segment->get('modules'), 'view' => 'Restobar/Bebidas']);
        $this->view->set_view('bebidas');
    }

    public function Pedidos()
    {
        $this->auth();
        $this->view->set_js('pedidos');
        $this->view->set_menu(['modules' => $this->segment->get('modules'), 'view' => 'Restobar/Pedidos']);
        $this->view->set_view('pedidos');
    }

    public function Ventas()
    {
        $this->auth();
        $this->view->set_js('ventas');
        $this->view->set_menu(['modules' => $this->segment->get('modules'), 'view' => 'Restobar/Ventas']);
        $this->view->set_view('ventas');
    }

    public function get_bebidas()
    {
        $this->auth();
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') return $this->jsonError('Método no permitido.');
        $r = $this->load_model('Restobar')->get_bebidas();
        $this->respond($r, 'Bebidas encontradas.');
    }

    public function get_pedidos()
    {
        $this->auth();
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') return $this->jsonError('Método no permitido.');
        $r = $this->load_model('Restobar')->get_pedidos();
        $this->respond($r, 'Pedidos encontrados.');
    }

    public function get_pedido_detalle()
    {
        $this->auth();
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id) return $this->jsonError('Pedido no válido.');
        $r = $this->load_model('Restobar')->get_pedido_detalle(['id' => $id]);
        $this->respond($r, 'Detalle encontrado.');
    }

    public function crear_pedido()
    {
        $this->auth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return $this->jsonError('Método no permitido.');
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) $input = $_POST;
        $items = isset($input['items']) && is_string($input['items']) ? json_decode($input['items'], true) : ($input['items'] ?? []);
        $items = is_array($items) ? $items : [];
        if (!$items) return $this->jsonError('Agrega al menos una bebida al pedido.');
        $bind = [
            'customer_name' => trim($input['customer_name'] ?? ''),
            'table_number' => trim($input['table_number'] ?? ''),
            'notes' => trim($input['notes'] ?? ''),
            'created_by' => (int)($this->segment->get('id_user') ?: 0),
            'items' => $items
        ];
        $r = $this->load_model('Restobar')->crear_pedido($bind);
        $this->respond($r, 'Pedido registrado correctamente.');
    }

    public function cambiar_estado()
    {
        $this->auth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return $this->jsonError('Método no permitido.');
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) $input = $_POST;
        $id = (int)($input['id'] ?? 0);
        $estado = strtoupper(trim($input['status'] ?? ''));
        $permitidos = ['PENDIENTE','EN_PREPARACION','LISTO','ENTREGADO','PAGADO','ANULADO'];
        if (!$id || !in_array($estado, $permitidos, true)) return $this->jsonError('Datos de estado inválidos.');
        $r = $this->load_model('Restobar')->cambiar_estado(['id' => $id, 'status' => $estado]);
        $this->respond($r, 'Estado actualizado.');
    }

    public function cobrar_pedido()
    {
        $this->auth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return $this->jsonError('Método no permitido.');
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) $input = $_POST;
        $id = (int)($input['id'] ?? 0);
        if (!$id) return $this->jsonError('Pedido no válido.');
        $r = $this->load_model('Restobar')->cambiar_estado(['id' => $id, 'status' => 'PAGADO']);
        $this->respond($r, 'Pedido cobrado correctamente.');
    }

    public function get_ventas()
    {
        $this->auth();
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') return $this->jsonError('Método no permitido.');
        $r = $this->load_model('Restobar')->get_ventas();
        $this->respond($r, 'Ventas encontradas.');
    }

    private function respond($response, $successMsg)
    {
        $status = $response['status'] ?? 'ERROR';
        if ($status === 'OK') {
            $json = ['status'=>'OK','type'=>'success','msg'=>$successMsg,'data'=>$response['result'] ?? []];
        } elseif ($status === 'ERROR') {
            $json = ['status'=>'ERROR','type'=>'warning','msg'=>$response['result'] ?? 'No se encontraron registros.','data'=>[]];
        } else {
            $msg = $response['result'] ?? 'Error de base de datos.';
            if ($msg instanceof PDOException) $msg = $msg->getMessage();
            $json = ['status'=>'ERROR','type'=>'error','msg'=>$msg,'data'=>[]];
        }
        header('Content-Type: application/json');
        echo json_encode($json);
    }

    private function jsonError($msg)
    {
        header('Content-Type: application/json');
        echo json_encode(['status'=>'ERROR','type'=>'warning','msg'=>$msg,'data'=>[]]);
    }
}
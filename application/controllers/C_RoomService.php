<?php

class C_RoomService extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }


    private function auth()
    {
        // Solo valida la sesión. El acceso a cada opción lo controla el menú (tabla permission).
        $this->functions->validate_session(
            $this->segment->get('isActive')
        );
    }


    public function index()
    {
        $this->auth();

        header(
            'Location: ' .
            BASE_URL .
            'RoomService/NuevoPedido'
        );

        exit;
    }


    public function NuevoPedido()
    {
        $this->auth();

        $this->view->set_js('index');

        $this->view->set_menu([
            'modules' => $this->segment->get('modules'),
            'view' => 'RoomService/NuevoPedido'
        ]);

        $this->view->set_view('index');
    }


    public function PedidosPendientes()
    {
        $this->auth();

        $this->view->set_js('pedidos');

        $this->view->set_menu([
            'modules' => $this->segment->get('modules'),
            'view' => 'RoomService/PedidosPendientes'
        ]);

        $this->view->set_view('pedidos');
    }


    public function PedidosPreparacion()
    {
        $this->auth();

        $this->view->set_js('preparacion');

        $this->view->set_menu([
            'modules' => $this->segment->get('modules'),
            'view' => 'RoomService/PedidosPreparacion'
        ]);

        $this->view->set_view('pedidos');
    }


    public function PedidosEntregados()
    {
        $this->auth();

        $this->view->set_js('entregados');

        $this->view->set_menu([
            'modules' => $this->segment->get('modules'),
            'view' => 'RoomService/PedidosEntregados'
        ]);

        $this->view->set_view('pedidos');
    }


    public function CargoHabitacion()
    {
        $this->auth();

        $this->view->set_js('cargo');

        $this->view->set_menu([
            'modules' => $this->segment->get('modules'),
            'view' => 'RoomService/CargoHabitacion'
        ]);

        $this->view->set_view('cargo');
    }


    public function get_habitaciones()
    {
        $this->auth();

        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            return $this->jsonError(
                'Método no permitido.'
            );
        }

        $r = $this->load_model(
            'Rooms'
        )->get_rooms();

        $this->respond(
            $r,
            'Habitaciones encontradas.'
        );
    }


    public function get_productos()
    {
        $this->auth();

        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            return $this->jsonError(
                'Método no permitido.'
            );
        }

        $r = $this->load_model(
            'Products'
        )->get_roomservice_products();

        $this->respond(
            $r,
            'Productos encontrados.'
        );
    }


    public function crear_pedido()
    {
        $this->auth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonError(
                'Método no permitido.'
            );
        }

        $input = json_decode(
            file_get_contents('php://input'),
            true
        );

        if (!is_array($input)) {
            $input = $_POST;
        }

        $roomId = (int)(
            $input['room_id'] ?? 0
        );

        $customerName = trim(
            $input['customer_name'] ?? ''
        );

        $notes = trim(
            $input['notes'] ?? ''
        );

        $items = $input['items'] ?? [];

        if ($roomId <= 0) {
            return $this->jsonError(
                'Selecciona una habitación.'
            );
        }

        if (
            !is_array($items) ||
            count($items) === 0
        ) {
            return $this->jsonError(
                'Agrega al menos un producto.'
            );
        }

        $bind = [
            'room_id' => $roomId,
            'customer_name' => $customerName,
            'notes' => $notes,
            'created_by' => (int)(
                $this->segment->get('id_user') ?: 0
            ),
            'items' => $items
        ];

        $r = $this->load_model(
            'RoomService'
        )->crear_pedido($bind);

        $this->respond(
            $r,
            'Pedido registrado correctamente.'
        );
    }


    public function get_pedidos_pendientes()
    {
        $this->auth();

        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            return $this->jsonError(
                'Método no permitido.'
            );
        }

        $r = $this->load_model(
            'RoomService'
        )->get_pedidos_pendientes();

        $this->respond(
            $r,
            'Pedidos pendientes encontrados.'
        );
    }


    public function get_pedidos_preparacion()
    {
        $this->auth();

        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            return $this->jsonError(
                'Método no permitido.'
            );
        }

        $r = $this->load_model(
            'RoomService'
        )->get_pedidos_preparacion();

        $this->respond(
            $r,
            'Pedidos en preparación encontrados.'
        );
    }


    public function get_pedidos_listos()
    {
        $this->auth();

        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            return $this->jsonError(
                'Método no permitido.'
            );
        }

        $r = $this->load_model(
            'RoomService'
        )->get_pedidos_listos();

        $this->respond(
            $r,
            'Pedidos listos encontrados.'
        );
    }


    public function get_pedidos_entregados()
    {
        $this->auth();

        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            return $this->jsonError(
                'Método no permitido.'
            );
        }

        $r = $this->load_model(
            'RoomService'
        )->get_pedidos_entregados();

        $this->respond(
            $r,
            'Pedidos entregados encontrados.'
        );
    }


    public function get_reservaciones_para_cargo()
    {
        $this->auth();

        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            return $this->jsonError(
                'Método no permitido.'
            );
        }

        $orderId = (int)(
            $_GET['order_id'] ?? 0
        );

        if ($orderId <= 0) {
            return $this->jsonError(
                'Pedido inválido.'
            );
        }

        $r = $this->load_model(
            'RoomService'
        )->get_reservaciones_para_cargo(
            $orderId
        );

        $this->respond(
            $r,
            'Reservaciones encontradas.'
        );
    }


    public function cargar_pedido_habitacion()
    {
        $this->auth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonError(
                'Método no permitido.'
            );
        }

        $input = json_decode(
            file_get_contents('php://input'),
            true
        );

        if (!is_array($input)) {
            $input = $_POST;
        }

        $orderId = (int)(
            $input['order_id'] ?? 0
        );

        $reservationId = (int)(
            $input['id_reservation'] ?? 0
        );

        if ($orderId <= 0) {
            return $this->jsonError(
                'Pedido inválido.'
            );
        }

        if ($reservationId <= 0) {
            return $this->jsonError(
                'Selecciona una reserva.'
            );
        }

        $r = $this->load_model(
            'RoomService'
        )->cargar_pedido_habitacion([
            'order_id' => $orderId,
            'id_reservation' => $reservationId
        ]);

        $this->respond(
            $r,
            'Cargo registrado en la habitación correctamente.'
        );
    }


    public function cambiar_estado()
    {
        $this->auth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonError(
                'Método no permitido.'
            );
        }

        $input = json_decode(
            file_get_contents('php://input'),
            true
        );

        if (!is_array($input)) {
            $input = $_POST;
        }

        $id = (int)(
            $input['id'] ?? 0
        );

        $status = strtoupper(
            trim($input['status'] ?? '')
        );

        $permitidos = [
            'PENDIENTE',
            'EN_PREPARACION',
            'LISTO',
            'ENTREGADO',
            'PAGADO',
            'ANULADO'
        ];

        if (
            !$id ||
            !in_array(
                $status,
                $permitidos,
                true
            )
        ) {
            return $this->jsonError(
                'Datos de estado inválidos.'
            );
        }

        $r = $this->load_model(
            'RoomService'
        )->cambiar_estado([
            'id' => $id,
            'status' => $status
        ]);

        $this->respond(
            $r,
            'Estado actualizado correctamente.'
        );
    }


    private function respond(
        $response,
        $successMsg
    ) {

        $status = $response['status']
            ?? 'ERROR';

        if ($status === 'OK') {

            $json = [
                'status' => 'OK',
                'type' => 'success',
                'msg' => $successMsg,
                'data' => $response['result'] ?? []
            ];

        } elseif ($status === 'ERROR') {

            $msg = $response['result']
                ?? 'No se encontraron registros.';

            if (is_array($msg)) {
                $msg = 'No se encontraron registros.';
            }

            $json = [
                'status' => 'ERROR',
                'type' => 'warning',
                'msg' => $msg,
                'data' => []
            ];

        } else {

            $msg = $response['result']
                ?? 'Error de base de datos.';

            if ($msg instanceof PDOException) {
                $msg = $msg->getMessage();
            }

            $json = [
                'status' => 'ERROR',
                'type' => 'error',
                'msg' => $msg,
                'data' => []
            ];
        }

        header(
            'Content-Type: application/json'
        );

        echo json_encode($json);
    }


    private function jsonError($msg)
    {
        header(
            'Content-Type: application/json'
        );

        echo json_encode([
            'status' => 'ERROR',
            'type' => 'warning',
            'msg' => $msg,
            'data' => []
        ]);
    }
}
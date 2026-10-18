<?php

class C_POS extends Controller
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

    private function json($data)
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    private function formatResponse($response, $successMessage)
    {
        if (($response['status'] ?? '') === 'OK') {
            return [
                'status' => 'OK',
                'msg' => $successMessage,
                'data' => $response['result'] ?? []
            ];
        }

        $message = $response['result'] ?? 'No se pudo completar la operación.';
        if ($message instanceof Throwable) {
            $message = $message->getMessage();
        }

        return [
            'status' => 'ERROR',
            'msg' => is_string($message) ? $message : 'Error de base de datos.',
            'data' => []
        ];
    }

    private function input()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        return is_array($data) ? $data : $_POST;
    }

    private function model()
    {
        return $this->load_model('POS');
    }

    private function getRequest($method, $successMessage)
    {
        $this->auth();

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET') {
            $this->json(['status' => 'ERROR', 'msg' => 'Método no permitido.', 'data' => []]);
            return;
        }

        $result = $this->model()->{$method}();
        $this->json($this->formatResponse($result, $successMessage));
    }

    public function index() { $this->NuevaVenta(); }

    public function NuevaVenta()
    {
        $this->auth();
        $this->view->set_js('nueva_venta');
        $this->view->set_menu(['modules' => $this->segment->get('modules'), 'view' => 'POS/NuevaVenta']);
        $this->view->set_view('nueva_venta');
    }

    public function VentasRecepcion()
    {
        $this->auth();
        $this->view->set_js('ventas_recepcion');
        $this->view->set_menu(['modules' => $this->segment->get('modules'), 'view' => 'POS/VentasRecepcion']);
        $this->view->set_view('ventas_recepcion');
    }

    public function VentaHabitacion()
    {
        $this->auth();
        $this->view->set_js('venta_habitacion');
        $this->view->set_menu(['modules' => $this->segment->get('modules'), 'view' => 'POS/VentaHabitacion']);
        $this->view->set_view('venta_habitacion');
    }

    public function VentaMinibar()
    {
        $this->auth();
        $this->view->set_js('venta_minibar');
        $this->view->set_menu(['modules' => $this->segment->get('modules'), 'view' => 'POS/VentaMinibar']);
        $this->view->set_view('venta_minibar');
    }

    public function HistorialVentas()
    {
        $this->auth();
        $this->view->set_js('historial_ventas');
        $this->view->set_menu(['modules' => $this->segment->get('modules'), 'view' => 'POS/HistorialVentas']);
        $this->view->set_view('historial_ventas');
    }

    public function Anulaciones()
    {
        $this->auth();
        $this->view->set_js('anulaciones');
        $this->view->set_menu(['modules' => $this->segment->get('modules'), 'view' => 'POS/Anulaciones']);
        $this->view->set_view('anulaciones');
    }

    public function get_productos() { $this->getRequest('get_productos', 'Productos encontrados.'); }
    public function get_ventas() { $this->getRequest('get_ventas', 'Ventas encontradas.'); }
    public function get_ventas_recepcion() { $this->getRequest('get_ventas_recepcion', 'Ventas encontradas.'); }
    public function get_habitaciones() { $this->getRequest('get_habitaciones', 'Habitaciones encontradas.'); }
    public function get_historial_ventas() { $this->getRequest('get_historial_ventas', 'Historial de ventas encontrado.'); }
    public function get_anulaciones() { $this->getRequest('get_anulaciones', 'Ventas anuladas encontradas.'); }

    public function crear_venta()
    {
        $this->auth();
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            $this->json(['status' => 'ERROR', 'msg' => 'Método no permitido.', 'data' => []]);
            return;
        }

        $input = $this->input();
        if (empty($input['items']) || !is_array($input['items'])) {
            $this->json(['status' => 'ERROR', 'msg' => 'Agrega al menos un producto.', 'data' => []]);
            return;
        }

        $input['created_by'] = (int)($this->segment->get('id_user') ?: 0);
        $this->json($this->formatResponse($this->model()->crear_venta($input), 'Venta registrada correctamente.'));
    }

    public function registrar_venta_habitacion() { $this->registrarConsumo('registrar_venta_habitacion'); }
    public function registrar_venta_minibar() { $this->registrarConsumo('registrar_venta_minibar'); }

    private function registrarConsumo($method)
    {
        $this->auth();
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            $this->json(['status' => 'ERROR', 'msg' => 'Método no permitido.', 'data' => []]);
            return;
        }

        $input = $this->input();
        if (empty($input['id_reservation']) || empty($input['items']) || !is_array($input['items'])) {
            $this->json(['status' => 'ERROR', 'msg' => 'Selecciona una habitación y agrega productos.', 'data' => []]);
            return;
        }

        $input['created_by'] = (int)($this->segment->get('id_user') ?: 0);
        $message = $method === 'registrar_venta_minibar'
            ? 'Consumo de minibar registrado correctamente.'
            : 'Consumo registrado correctamente.';
        $this->json($this->formatResponse($this->model()->{$method}($input), $message));
    }

    public function anular_venta()
    {
        $this->auth();
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            $this->json(['status' => 'ERROR', 'msg' => 'Método no permitido.', 'data' => []]);
            return;
        }

        $input = $this->input();
        $id = (int)($input['id'] ?? $input['order_id'] ?? 0);
        $motivo = trim($input['motivo'] ?? '');

        if ($id <= 0 || $motivo === '') {
            $this->json(['status' => 'ERROR', 'msg' => 'Indica una venta válida y el motivo de anulación.', 'data' => []]);
            return;
        }

        $this->json($this->formatResponse(
            $this->model()->anular_venta(['id' => $id, 'motivo' => $motivo, 'created_by' => (int)($this->segment->get('id_user') ?: 0)]),
            'Venta anulada correctamente.'
        ));
    }
}
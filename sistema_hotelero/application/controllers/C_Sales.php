<?php
// --
class C_Sales extends Controller
{

    // --
    public function __construct()
    {
        parent::__construct();
    }


    // ---------------------------------------------------------
    // VISTA PRINCIPAL DE VENTAS
    // ---------------------------------------------------------
    public function index()
    {
        // --
        $this->functions->validate_session(
            $this->segment->get('isActive')
        );

        $this->functions->check_permissions(
            $this->segment->get('modules'),
            'Sales'
        );

        // --
        $this->view->set_js('index');

        $this->view->set_menu([
            'modules' => $this->segment->get('modules'),
            'view' => 'Sales'
        ]);

        $this->view->set_view('index');
    }


    // ---------------------------------------------------------
    // OBTENER RESERVACIÓN POR ID
    // ---------------------------------------------------------
    public function get_reservation_by_id()
    {
        // --
        $this->functions->validate_session(
            $this->segment->get('isActive')
        );

        $request = $_SERVER['REQUEST_METHOD'];

        // --
        if ($request === 'GET') {

            $input = json_decode(
                file_get_contents('php://input'),
                true
            );

            if (empty($input)) {
                $input = filter_input_array(INPUT_GET);
            }

            // --
            if (!empty($input['id_reservation'])) {

                $obj = $this->load_model('Sales');

                $bind = [
                    'id_reservation' => intval(
                        $input['id_reservation']
                    )
                ];

                // Enviar los parámetros al modelo
                $response = $obj->get_reservation_by_id($bind);

                // --
                switch ($response['status']) {

                    case 'OK':

                        $json = [
                            'status' => 'OK',
                            'type' => 'success',
                            'msg' => 'Listado de registros encontrados.',
                            'data' => $response['result']
                        ];

                        break;


                    case 'ERROR':

                        $json = [
                            'status' => 'ERROR',
                            'type' => 'warning',
                            'msg' => 'No se encontraron registros en el sistema.',
                            'data' => []
                        ];

                        break;


                    case 'EXCEPTION':

                        $json = [
                            'status' => 'ERROR',
                            'type' => 'error',
                            'msg' => ($response['result'] instanceof PDOException)
                                ? $response['result']->getMessage()
                                : 'Ocurrió un error al consultar la reservación.',
                            'data' => []
                        ];

                        break;


                    default:

                        $json = [
                            'status' => 'ERROR',
                            'type' => 'error',
                            'msg' => 'Respuesta desconocida del servidor.',
                            'data' => []
                        ];

                        break;
                }

            } else {

                $json = [
                    'status' => 'ERROR',
                    'type' => 'warning',
                    'msg' => 'No se enviaron los campos necesarios, verificar.',
                    'data' => []
                ];
            }

        } else {

            $json = [
                'status' => 'ERROR',
                'type' => 'error',
                'msg' => 'Método no permitido.',
                'data' => []
            ];
        }

        // --
        header('Content-Type: application/json');

        echo json_encode($json);
    }


    // ---------------------------------------------------------
    // HISTORIAL DE VENTAS DE UNA RESERVACIÓN
    // ---------------------------------------------------------
    public function sales_product_history()
    {
        // --
        $this->functions->validate_session(
            $this->segment->get('isActive')
        );

        $request = $_SERVER['REQUEST_METHOD'];

        // --
        if ($request === 'GET') {

            $input = json_decode(
                file_get_contents('php://input'),
                true
            );

            if (empty($input)) {
                $input = filter_input_array(INPUT_GET);
            }

            // --
            if (!empty($input['id_reservation'])) {

                $obj = $this->load_model('Sales');

                $bind = [
                    'id_reservation' => intval(
                        $input['id_reservation']
                    )
                ];

                $response = $obj->sales_product_history($bind);

                // --
                switch ($response['status']) {

                    case 'OK':

                        $json = [
                            'status' => 'OK',
                            'type' => 'success',
                            'msg' => 'Listado de registros encontrados.',
                            'data' => $response['result']
                        ];

                        break;


                    case 'ERROR':

                        $json = [
                            'status' => 'ERROR',
                            'type' => 'warning',
                            'msg' => 'No se encontraron registros en el sistema.',
                            'data' => []
                        ];

                        break;


                    case 'EXCEPTION':

                        $json = [
                            'status' => 'ERROR',
                            'type' => 'error',
                            'msg' => ($response['result'] instanceof PDOException)
                                ? $response['result']->getMessage()
                                : 'Ocurrió un error al consultar el historial.',
                            'data' => []
                        ];

                        break;


                    default:

                        $json = [
                            'status' => 'ERROR',
                            'type' => 'error',
                            'msg' => 'Respuesta desconocida del servidor.',
                            'data' => []
                        ];

                        break;
                }

            } else {

                $json = [
                    'status' => 'ERROR',
                    'type' => 'warning',
                    'msg' => 'El campo id_reservation es obligatorio.',
                    'data' => []
                ];
            }

        } else {

            $json = [
                'status' => 'ERROR',
                'type' => 'error',
                'msg' => 'Método no permitido.',
                'data' => []
            ];
        }

        // --
        header('Content-Type: application/json');

        echo json_encode($json);
    }


    // ---------------------------------------------------------
    // ACTUALIZAR TOTAL DE VENTAS DE LA RESERVACIÓN
    // ---------------------------------------------------------
    public function update_payment_total()
    {
        // --
        $this->functions->validate_session(
            $this->segment->get('isActive')
        );

        // --
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

            header('Content-Type: application/json');

            echo json_encode([
                'status' => 'ERROR',
                'type' => 'error',
                'msg' => 'Método no permitido.',
                'data' => []
            ]);

            return;
        }

        // --
        $input = json_decode(
            file_get_contents('php://input'),
            true
        );

        // --
        if (empty($input)) {

            header('Content-Type: application/json');

            echo json_encode([
                'status' => 'ERROR',
                'type' => 'warning',
                'msg' => 'No se enviaron datos. Verifica el cuerpo de la solicitud.',
                'data' => []
            ]);

            return;
        }

        // -----------------------------------------------------
        // VALIDAR RESERVACIÓN
        // -----------------------------------------------------
        if (empty($input['id_reservation'])) {

            header('Content-Type: application/json');

            echo json_encode([
                'status' => 'ERROR',
                'type' => 'warning',
                'msg' => 'El campo id_reservation es obligatorio.',
                'data' => []
            ]);

            return;
        }

        // -----------------------------------------------------
        // VALIDAR TOTAL
        // -----------------------------------------------------
        if (!isset($input['payment_sales'])) {

            header('Content-Type: application/json');

            echo json_encode([
                'status' => 'ERROR',
                'type' => 'warning',
                'msg' => 'El campo payment_sales es obligatorio.',
                'data' => []
            ]);

            return;
        }

        // -----------------------------------------------------
        // CONVERTIR VALORES
        // -----------------------------------------------------
        $id_reservation = intval(
            $input['id_reservation']
        );

        $payment_sales = floatval(
            $input['payment_sales']
        );

        // -----------------------------------------------------
        // LLAMAR MODELO
        // -----------------------------------------------------
        $obj = $this->load_model('Sales');

        $bind = [
            'id_reservation' => $id_reservation,
            'payment_sales' => $payment_sales
        ];

        $response = $obj->update_payment_total($bind);

        // -----------------------------------------------------
        // RESPUESTA
        // -----------------------------------------------------
        if ($response['status'] === 'OK') {

            header('Content-Type: application/json');

            echo json_encode([
                'status' => 'OK',
                'type' => 'success',
                'msg' => 'Total de ventas actualizado correctamente.',
                'data' => $response['result']
            ]);

        } else {

            header('Content-Type: application/json');

            echo json_encode([
                'status' => 'ERROR',
                'type' => 'error',
                'msg' => 'Error al actualizar el total de ventas.',
                'data' => $response['result']
            ]);
        }
    }


    // ---------------------------------------------------------
    // ELIMINAR RESERVACIÓN
    //
    // IMPORTANTE:
    // Esta función NO debe utilizarse como "eliminar venta".
    // Se conserva temporalmente porque ya existe en el proyecto.
    // ---------------------------------------------------------
    public function delete_reservation()
    {
        // --
        $this->functions->validate_session(
            $this->segment->get('isActive')
        );

        // --
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

            header('Content-Type: application/json');

            echo json_encode([
                'status' => 'ERROR',
                'type' => 'error',
                'msg' => 'Método no permitido.',
                'data' => []
            ]);

            return;
        }

        // --
        $id_reservation = filter_input(
            INPUT_POST,
            'id_reservation',
            FILTER_VALIDATE_INT
        );

        // --
        if (!$id_reservation) {

            header('Content-Type: application/json');

            echo json_encode([
                'status' => 'ERROR',
                'type' => 'warning',
                'msg' => 'ID de reservación inválido.',
                'data' => []
            ]);

            return;
        }

        // --
        $obj = $this->load_model('Sales');

        $response = $obj->delete_reservation(
            $id_reservation
        );

        // --
        if ($response['status'] === 'OK') {

            header('Content-Type: application/json');

            echo json_encode([
                'status' => 'OK',
                'type' => 'success',
                'msg' => 'Reservación eliminada correctamente.',
                'data' => []
            ]);

        } else {

            header('Content-Type: application/json');

            echo json_encode([
                'status' => 'ERROR',
                'type' => 'error',
                'msg' => 'Error al eliminar la reservación.',
                'data' => $response['result']
            ]);
        }
    }
}
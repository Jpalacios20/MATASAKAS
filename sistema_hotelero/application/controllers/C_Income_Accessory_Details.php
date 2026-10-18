<?php 
class C_Income_Accessory_Details extends Controller {

    public function __construct() {
        parent::__construct();
    }
    
    public function index() {
        $this->functions->validate_session($this->segment->get('isActive'));
        $this->functions->check_permissions($this->segment->get('modules'), 'Income_Accessory');
        
        $this->view->set_js('index');       
        $this->view->set_menu(array('modules' => $this->segment->get('modules'), 'view' => 'Income_Accessory')); 
        $this->view->set_view('index');     
    }

    // --- OBTENER DATOS PARA EDITAR ---
    public function get_data_for_edit() {
        header('Content-Type: application/json');
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        
        if ($id > 0) {
            $obj = $this->load_model('Income_Accessory_Details');
            $data = $obj->get_full_income_info($id);
            echo json_encode(['status' => 'OK', 'data' => $data]);
        } else {
            echo json_encode(['status' => 'ERROR', 'msg' => 'ID inválido']);
        }
    }

    // --- ACTUALIZAR INGRESO ---
    public function update_income_accessory() {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (empty($input['master']['id_income_accessory']) || empty($input['details'])) {
                echo json_encode(['status' => 'ERROR', 'msg' => 'Datos incompletos para actualizar.']);
                return;
            }

            $id_user_session = $this->segment->get('id');
            if (empty($id_user_session)) $id_user_session = $this->segment->get('id_user');
            if (empty($id_user_session)) $id_user_session = $_SESSION['id'] ?? 1;

            $input['master']['id_user'] = $id_user_session;

            $obj = $this->load_model('Income_Accessory_Details');
            $response = $obj->update_income_transaction($input);

            echo json_encode($response);
        }
    }

    // --- CREAR INGRESO (NUEVA FUNCIÓN QUE FALTABA) ---
    public function create_income_accessory() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);
            
            // Validar datos mínimos
            if (empty($input['master']) || empty($input['details'])) {
                echo json_encode(['status' => 'ERROR', 'msg' => 'Datos incompletos.']);
                return;
            }

            // Obtener usuario
            $id_user_session = $this->segment->get('id');
            if (empty($id_user_session)) $id_user_session = $this->segment->get('id_user');
            if (empty($id_user_session)) $id_user_session = $_SESSION['id'] ?? 1;

            $input['master']['id_user'] = $id_user_session;

            $obj = $this->load_model('Income_Accessory_Details');
            // Llamamos a la función de creación en el modelo
            $response = $obj->create_income_transaction($input);

            echo json_encode($response);
        } else {
             echo json_encode(['status' => 'ERROR', 'msg' => 'Método no permitido']);
        }
    }
}
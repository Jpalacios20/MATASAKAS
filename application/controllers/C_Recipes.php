<?php

class C_Recipes extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    // ---------------------------------------------------------
    // PÁGINA DE RECETAS
    // ---------------------------------------------------------
    public function index()
    {
        $this->functions->validate_session($this->segment->get('isActive'));

        $this->view->set_js('index');

        $this->view->set_menu(array(
            'modules' => $this->segment->get('modules'),
            'view'    => 'recipes'
        ));

        $this->view->set_view('index');
    }

    // ---------------------------------------------------------
    // LISTA DE PLATOS CON COSTO DE RECETA
    // ---------------------------------------------------------
    public function get_recipes()
    {
        $this->functions->validate_session($this->segment->get('isActive'));

        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            return $this->recipe_json('error', 'Método no permitido.');
        }

        $r = $this->load_model('Recipes')->get_recipes();
        $this->recipe_response($r, 'Recetas encontradas.');
    }

    // ---------------------------------------------------------
    // INGREDIENTES DE UN PLATO
    // ---------------------------------------------------------
    public function get_recipe()
    {
        $this->functions->validate_session($this->segment->get('isActive'));

        $id_meal = filter_input(INPUT_GET, 'id_meal', FILTER_VALIDATE_INT);

        if (!$id_meal) {
            return $this->recipe_json('warning', 'Plato no válido.');
        }

        $r = $this->load_model('Recipes')->get_recipe(array('id_meal' => $id_meal));
        $this->recipe_response($r, 'Receta encontrada.');
    }

    // ---------------------------------------------------------
    // LISTA DE INGREDIENTES
    // ---------------------------------------------------------
    public function get_ingredients()
    {
        $this->functions->validate_session($this->segment->get('isActive'));

        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            return $this->recipe_json('error', 'Método no permitido.');
        }

        $r = $this->load_model('Recipes')->get_ingredients();
        $this->recipe_response($r, 'Ingredientes encontrados.');
    }

    // ---------------------------------------------------------
    // CREAR INGREDIENTE
    // ---------------------------------------------------------
    public function create_ingredient()
    {
        $this->functions->validate_session($this->segment->get('isActive'));

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->recipe_json('error', 'Método no permitido.');
        }

        $input = json_decode(file_get_contents('php://input'), true);

        if (empty($input)) {
            $input = filter_input_array(INPUT_POST);
        }

        $name = isset($input['name']) ? trim($input['name']) : '';
        $unit = isset($input['unit']) ? strtolower(trim($input['unit'])) : 'kg';
        $cost = isset($input['cost']) ? floatval($input['cost']) : 0;

        if ($name === '' || $cost < 0) {
            return $this->recipe_json('warning', 'Escribe el nombre y un costo válido.');
        }

        if (!in_array($unit, array('und', 'kg', 'g', 'l', 'ml'), true)) {
            $unit = 'kg';
        }

        $r = $this->load_model('Recipes')->create_ingredient(array(
            'name' => $name,
            'unit' => $unit,
            'cost' => number_format($cost, 2, '.', '')
        ));

        $this->recipe_response($r, 'Ingrediente creado correctamente.');
    }

    // ---------------------------------------------------------
    // GUARDAR RECETA
    // ---------------------------------------------------------
    public function save_recipe()
    {
        $this->functions->validate_session($this->segment->get('isActive'));

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->recipe_json('error', 'Método no permitido.');
        }

        $input = json_decode(file_get_contents('php://input'), true);

        if (empty($input)) {
            $input = filter_input_array(INPUT_POST);
        }

        $id_meal = isset($input['id_meal']) ? intval($input['id_meal']) : 0;

        if ($id_meal <= 0) {
            return $this->recipe_json('warning', 'Plato no válido.');
        }

        $units_ok = array('und', 'kg', 'g', 'l', 'ml');
        $items = array();
        $raw = (isset($input['items']) && is_array($input['items'])) ? $input['items'] : array();

        foreach ($raw as $row) {
            $id_ingredient = isset($row['id_ingredient']) ? intval($row['id_ingredient']) : 0;
            $quantity      = isset($row['quantity']) ? floatval($row['quantity']) : 0;
            $unit          = isset($row['unit']) ? strtolower(trim($row['unit'])) : 'und';

            if ($id_ingredient <= 0 || $quantity <= 0) {
                continue;
            }

            if (!in_array($unit, $units_ok, true)) {
                $unit = 'und';
            }

            $items[] = array(
                'id_ingredient' => $id_ingredient,
                'quantity'      => number_format($quantity, 3, '.', ''),
                'unit'          => $unit
            );
        }

        $r = $this->load_model('Recipes')->save_recipe($id_meal, $items);
        $this->recipe_response($r, 'Receta guardada correctamente.');
    }

    // ---------------------------------------------------------
    // AYUDAS PARA RESPONDER JSON
    // ---------------------------------------------------------
    private function recipe_response($response, $success_msg)
    {
        if ($response['status'] === 'OK') {
            header('Content-Type: application/json');
            echo json_encode(array(
                'status' => 'OK',
                'type'   => 'success',
                'msg'    => $success_msg,
                'data'   => $response['result']
            ));
        } elseif ($response['status'] === 'EXCEPTION') {
            $this->recipe_json('error', $response['result']->getMessage());
        } else {
            $this->recipe_json('warning', 'No se pudo completar la operación.');
        }
    }

    private function recipe_json($type, $msg)
    {
        header('Content-Type: application/json');
        echo json_encode(array(
            'status' => 'ERROR',
            'type'   => $type,
            'msg'    => $msg,
            'data'   => array()
        ));
    }
}
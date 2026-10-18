<?php

class M_Recipes extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_recipes()
    {
        try {
            $sql = 'SELECT
                        meal.id_meal,
                        meal.meal_name,
                        meal.meal_price,
                        categories.description AS name_category,
                        COUNT(r.id) AS total_ingredients,
                        COALESCE(SUM(r.quantity * i.cost), 0) AS cost
                    FROM meal
                    LEFT JOIN categories ON meal.id_category = categories.id
                    LEFT JOIN meal_recipe r ON r.id_meal = meal.id_meal
                    LEFT JOIN ingredient i ON i.id_ingredient = r.id_ingredient
                    GROUP BY meal.id_meal, meal.meal_name, meal.meal_price, categories.description
                    ORDER BY meal.meal_name ASC';

            $result = $this->pdo->fetchAll($sql);

            return array('status' => 'OK', 'result' => $result ? $result : array());

        } catch (PDOException $e) {
            return array('status' => 'EXCEPTION', 'result' => $e);
        }
    }

    public function get_recipe($bind)
    {
        try {
            $sql = 'SELECT
                        r.id_ingredient,
                        i.name,
                        i.unit,
                        i.cost,
                        r.quantity
                    FROM meal_recipe r
                    LEFT JOIN ingredient i ON i.id_ingredient = r.id_ingredient
                    WHERE r.id_meal = :id_meal
                    ORDER BY i.name ASC';

            $result = $this->pdo->fetchAll($sql, $bind);

            return array('status' => 'OK', 'result' => $result ? $result : array());

        } catch (PDOException $e) {
            return array('status' => 'EXCEPTION', 'result' => $e);
        }
    }

    public function get_ingredients()
    {
        try {
            $sql = 'SELECT id_ingredient, name, unit, cost
                    FROM ingredient
                    WHERE status = 1
                    ORDER BY name ASC';

            $result = $this->pdo->fetchAll($sql);

            return array('status' => 'OK', 'result' => $result ? $result : array());

        } catch (PDOException $e) {
            return array('status' => 'EXCEPTION', 'result' => $e);
        }
    }

    public function create_ingredient($bind)
    {
        try {
            $result = $this->pdo->perform(
                'INSERT INTO ingredient (name, unit, cost)
                 VALUES (:name, :unit, :cost)',
                $bind
            );

            return array('status' => $result ? 'OK' : 'ERROR', 'result' => array());

        } catch (PDOException $e) {
            return array('status' => 'EXCEPTION', 'result' => $e);
        }
    }

    public function save_recipe($id_meal, $items)
    {
        try {
            $this->pdo->perform(
                'DELETE FROM meal_recipe WHERE id_meal = :id_meal',
                array('id_meal' => $id_meal)
            );

            foreach ($items as $item) {
                $this->pdo->perform(
                    'INSERT INTO meal_recipe (id_meal, id_ingredient, quantity, unit)
                     VALUES (:id_meal, :id_ingredient, :quantity, :unit)',
                    array(
                        'id_meal'       => $id_meal,
                        'id_ingredient' => $item['id_ingredient'],
                        'quantity'      => $item['quantity'],
                        'unit'          => $item['unit']
                    )
                );
            }

            return array('status' => 'OK', 'result' => array());

        } catch (PDOException $e) {
            return array('status' => 'EXCEPTION', 'result' => $e);
        }
    }
}
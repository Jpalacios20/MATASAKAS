<?php

class M_Meal extends Model
{
    // =========================================================
    // CONSTRUCTOR
    // =========================================================

    public function __construct()
    {
        parent::__construct();
    }


    // =========================================================
    // OBTENER TODAS LAS COMIDAS
    // =========================================================

    public function get_meal()
    {
        try {

            $sql = 'SELECT
                        meal.id_meal,
                        meal.meal_sku,
                        meal.meal_name,
                        meal.meal_description,
                        meal.id_category,
                        categories.description AS name_category,
                        meal.meal_price
                    FROM meal
                    LEFT JOIN categories
                        ON meal.id_category = categories.id
                    ORDER BY meal.meal_name ASC';

            $result = $this->pdo->fetchAll($sql);

            if ($result) {

                return array(
                    'status' => 'OK',
                    'result' => $result
                );

            }

            return array(
                'status' => 'ERROR',
                'result' => array()
            );

        } catch (PDOException $e) {

            return array(
                'status' => 'EXCEPTION',
                'result' => $e
            );
        }
    }


    // =========================================================
    // OBTENER COMIDAS + PRODUCTOS PARA NUEVA COMANDA
    // =========================================================

    public function get_meals_comanda()
    {
        try {

            $sql = '
                SELECT
                    meal.id_meal AS id_meal,
                    meal.meal_sku AS sku,
                    meal.meal_name AS meal_name,
                    meal.meal_description AS meal_description,
                    meal.id_category AS id_category,
                    categories.description AS name_category,
                    meal.meal_price AS meal_price,
                    "MEAL" AS item_type

                FROM meal

                LEFT JOIN categories
                    ON meal.id_category = categories.id


                UNION ALL


                SELECT
                    (product.id_product * -1) AS id_meal,
                    product.product_sku AS sku,
                    product.product_name AS meal_name,
                    product.product_description AS meal_description,
                    product.id_category AS id_category,
                    categories.description AS name_category,
                    product.product_price AS meal_price,
                    "PRODUCT" AS item_type

                FROM product

                LEFT JOIN categories
                    ON product.id_category = categories.id

                WHERE product.status = 1


                ORDER BY
                    name_category ASC,
                    meal_name ASC
            ';


            $result = $this->pdo->fetchAll($sql);


            if ($result) {

                return array(
                    'status' => 'OK',
                    'result' => $result
                );

            }


            return array(
                'status' => 'ERROR',
                'result' => array()
            );


        } catch (PDOException $e) {

            return array(
                'status' => 'EXCEPTION',
                'result' => $e
            );
        }
    }


    // =========================================================
    // OBTENER COMIDA POR ID
    // =========================================================

    public function get_meal_by_id($bind)
    {
        try {

            $sql = 'SELECT
                        meal.id_meal,
                        meal.meal_sku,
                        meal.meal_name,
                        meal.meal_description,
                        meal.id_category,
                        categories.description AS name_category,
                        meal.meal_price
                    FROM meal
                    LEFT JOIN categories
                        ON meal.id_category = categories.id
                    WHERE meal.id_meal = :id_meal
                    LIMIT 1';

            $result = $this->pdo->fetchOne(
                $sql,
                $bind
            );


            if ($result) {

                return array(
                    'status' => 'OK',
                    'result' => $result
                );

            }


            return array(
                'status' => 'ERROR',
                'result' => array()
            );


        } catch (PDOException $e) {

            return array(
                'status' => 'EXCEPTION',
                'result' => $e
            );
        }
    }


    // =========================================================
    // CREAR COMIDA
    // =========================================================

    public function create_meal($bind)
    {
        try {

            $sql = 'INSERT INTO meal
                    (
                        meal_sku,
                        meal_name,
                        meal_description,
                        id_category,
                        meal_price
                    )
                    VALUES
                    (
                        :meal_sku,
                        :meal_name,
                        :meal_description,
                        :id_category,
                        :meal_price
                    )';

            $result = $this->pdo->perform(
                $sql,
                $bind
            );


            if ($result) {

                return array(
                    'status' => 'OK',
                    'result' => array()
                );

            }


            return array(
                'status' => 'ERROR',
                'result' => array()
            );


        } catch (PDOException $e) {

            return array(
                'status' => 'EXCEPTION',
                'result' => $e
            );
        }
    }


    // =========================================================
    // ACTUALIZAR COMIDA
    // =========================================================

    public function update_meal($bind)
    {
        try {

            $sql = 'UPDATE meal
                    SET
                        meal_sku = :meal_sku,
                        meal_name = :meal_name,
                        meal_description = :meal_description,
                        id_category = :id_category,
                        meal_price = :meal_price
                    WHERE id_meal = :id_meal';

            $result = $this->pdo->perform(
                $sql,
                $bind
            );


            if ($result) {

                return array(
                    'status' => 'OK',
                    'result' => array()
                );

            }


            return array(
                'status' => 'ERROR',
                'result' => array()
            );


        } catch (PDOException $e) {

            return array(
                'status' => 'EXCEPTION',
                'result' => $e
            );
        }
    }


    // =========================================================
    // ELIMINAR COMIDA
    // =========================================================

    public function delete_meal($bind)
    {
        try {

            $sql = 'DELETE FROM meal
                    WHERE id_meal = :id_meal';

            $result = $this->pdo->perform(
                $sql,
                $bind
            );


            if ($result) {

                return array(
                    'status' => 'OK',
                    'result' => array()
                );

            }


            return array(
                'status' => 'ERROR',
                'result' => array()
            );


        } catch (PDOException $e) {

            return array(
                'status' => 'EXCEPTION',
                'result' => $e
            );
        }
    }


    // =========================================================
    // OBTENER MESAS
    // =========================================================

    public function get_tables()
    {
        try {

            $sql = 'SELECT
                        id AS id_table,
                        table_name AS table_number,
                        seats AS table_capacity,
                        "" AS table_area,
                        status AS table_status
                    FROM restaurant_tables';

            $result = $this->pdo->fetchAll($sql);


            if ($result) {

                return array(
                    'status' => 'OK',
                    'result' => $result
                );

            }


            return array(
                'status' => 'ERROR',
                'result' => array()
            );


        } catch (PDOException $e) {

            return array(
                'status' => 'EXCEPTION',
                'result' => $e
            );
        }
    }


    // =========================================================
    // CREAR COMANDA
    // =========================================================
    //
    // AHORA UTILIZA:
    //
    // hs_orders
    // hs_order_items
    //
    // =========================================================

    public function create_comanda($bind)
    {
        try {

            // -----------------------------------------------------
            // DATOS PRINCIPALES
            // -----------------------------------------------------

            $table = isset($bind['table'])
                ? trim($bind['table'])
                : '';

            $observations = isset($bind['observations'])
                ? trim($bind['observations'])
                : '';

            $items = isset($bind['items'])
                ? $bind['items']
                : array();


            // -----------------------------------------------------
            // VALIDAR MESA / REFERENCIA
            // -----------------------------------------------------

            if ($table === '') {

                $table = 'Mesa General';
            }


            // -----------------------------------------------------
            // VALIDAR ITEMS
            // -----------------------------------------------------

            if (
                !is_array($items) ||
                count($items) === 0
            ) {

                return array(
                    'status' => 'ERROR',
                    'result' => 'Agrega al menos un producto.'
                );
            }


            // -----------------------------------------------------
            // VARIABLES
            // -----------------------------------------------------

            $total = 0;

            $items_validos = array();


            // =====================================================
            // RECORRER ITEMS
            // =====================================================

            foreach ($items as $item) {

                $id_meal = isset($item['id_meal'])
                    ? intval($item['id_meal'])
                    : 0;

                $quantity = isset($item['quantity'])
                    ? intval($item['quantity'])
                    : 0;


                // -------------------------------------------------
                // VALIDAR
                // -------------------------------------------------

                if (
                    $id_meal == 0 ||
                    $quantity <= 0
                ) {

                    continue;
                }


                // =================================================
                // COMIDA
                // =================================================

                if ($id_meal > 0) {

                    $sql_meal = 'SELECT
                                    id_meal,
                                    meal_sku,
                                    meal_name,
                                    meal_price
                                FROM meal
                                WHERE id_meal = :id_meal
                                LIMIT 1';


                    $meal = $this->pdo->fetchOne(
                        $sql_meal,
                        array(
                            'id_meal' => $id_meal
                        )
                    );


                    if (!$meal) {

                        continue;
                    }


                    $price = floatval(
                        $meal['meal_price']
                    );


                    $subtotal =
                        $price * $quantity;


                    $total += $subtotal;


                    $items_validos[] = array(

                        'kind' =>
                            'COMIDA',

                        'source_id' =>
                            $id_meal,

                        'sku' =>
                            isset($meal['meal_sku'])
                                ? $meal['meal_sku']
                                : null,

                        'name' =>
                            $meal['meal_name'],

                        'quantity' =>
                            $quantity,

                        'unit_price' =>
                            $price,

                        'total' =>
                            $subtotal
                    );
                }


                // =================================================
                // PRODUCTO
                // =================================================

                else {

                    $id_product = abs(
                        $id_meal
                    );


                    $sql_product = 'SELECT
                                        id_product,
                                        product_sku,
                                        product_name,
                                        product_price
                                    FROM product
                                    WHERE id_product = :id_product
                                    AND status = 1
                                    LIMIT 1';


                    $product = $this->pdo->fetchOne(
                        $sql_product,
                        array(
                            'id_product' =>
                                $id_product
                        )
                    );


                    if (!$product) {

                        continue;
                    }


                    $price = floatval(
                        $product['product_price']
                    );


                    $subtotal =
                        $price * $quantity;


                    $total += $subtotal;


                    $items_validos[] = array(

                        'kind' =>
                            'PRODUCTO',

                        'source_id' =>
                            $id_product,

                        'sku' =>
                            isset($product['product_sku'])
                                ? $product['product_sku']
                                : null,

                        'name' =>
                            $product['product_name'],

                        'quantity' =>
                            $quantity,

                        'unit_price' =>
                            $price,

                        'total' =>
                            $subtotal
                    );
                }
            }


            // =====================================================
            // VALIDAR ITEMS
            // =====================================================

            if (
                count($items_validos) === 0
            ) {

                return array(
                    'status' => 'ERROR',
                    'result' =>
                        'No existen productos válidos en la comanda.'
                );
            }


            // =====================================================
            // CREAR CABECERA
            // =====================================================
            //
            // TABLA:
            // hs_orders
            //
            // =====================================================

            $sql_order = 'INSERT INTO hs_orders
                            (
                                order_type,
                                id_reservation,
                                room_number,
                                customer_name,
                                table_number,
                                status,
                                notes,
                                total,
                                created_by
                            )
                            VALUES
                            (
                                :order_type,
                                :id_reservation,
                                :room_number,
                                :customer_name,
                                :table_number,
                                :status,
                                :notes,
                                :total,
                                :created_by
                            )';


            $bind_order = array(

                'order_type' =>
                    'RESTAURANTE',

                'id_reservation' =>
                    null,

                'room_number' =>
                    null,

                'customer_name' =>
                    null,

                'table_number' =>
                    $table,

                'status' =>
                    'PENDIENTE',

                'notes' =>
                    $observations,

                'total' =>
                    number_format(
                        $total,
                        2,
                        '.',
                        ''
                    ),

                'created_by' =>
                    null
            );


            $result_order =
                $this->pdo->perform(
                    $sql_order,
                    $bind_order
                );


            if (!$result_order) {

                return array(
                    'status' => 'ERROR',
                    'result' =>
                        'No se pudo registrar la comanda en hs_orders.'
                );
            }


            // =====================================================
            // OBTENER ID DE LA COMANDA
            // =====================================================

            $id_comanda =
                $this->pdo->lastInsertId();


            if (!$id_comanda) {

                return array(
                    'status' => 'ERROR',
                    'result' =>
                        'No se pudo obtener el número de comanda.'
                );
            }


            // =====================================================
            // INSERTAR DETALLE
            // =====================================================
            //
            // TABLA:
            // hs_order_items
            //
            // =====================================================

            foreach (
                $items_validos as $item
            ) {

                $sql_item =
                    'INSERT INTO hs_order_items
                    (
                        order_id,
                        kind,
                        source_id,
                        sku,
                        name,
                        quantity,
                        unit_price,
                        total
                    )
                    VALUES
                    (
                        :order_id,
                        :kind,
                        :source_id,
                        :sku,
                        :name,
                        :quantity,
                        :unit_price,
                        :total
                    )';


                $bind_item = array(

                    'order_id' =>
                        $id_comanda,

                    'kind' =>
                        $item['kind'],

                    'source_id' =>
                        $item['source_id'],

                    'sku' =>
                        $item['sku'],

                    'name' =>
                        $item['name'],

                    'quantity' =>
                        $item['quantity'],

                    'unit_price' =>
                        number_format(
                            $item['unit_price'],
                            2,
                            '.',
                            ''
                        ),

                    'total' =>
                        number_format(
                            $item['total'],
                            2,
                            '.',
                            ''
                        )
                );


                $result_item =
                    $this->pdo->perform(
                        $sql_item,
                        $bind_item
                    );


                if (!$result_item) {

                    return array(
                        'status' => 'ERROR',
                        'result' =>
                            'No se pudo registrar el producto "' .
                            $item['name'] .
                            '" en hs_order_items.'
                    );
                }
            }


            // =====================================================
            // RESPUESTA EXITOSA
            // =====================================================

            return array(

                'status' =>
                    'OK',

                'result' => array(

                    'id_comanda' =>
                        $id_comanda,

                    'table' =>
                        $table,

                    'observations' =>
                        $observations,

                    'total' =>
                        number_format(
                            $total,
                            2,
                            '.',
                            ''
                        ),

                    'items' =>
                        $items_validos
                )
            );


        } catch (PDOException $e) {

            return array(
                'status' => 'EXCEPTION',
                'result' => $e
            );
        }
    }
}
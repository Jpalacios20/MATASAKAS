<?php

// --
class M_Products extends Model
{
    // --
    public function __construct()
    {
        parent::__construct();
    }


    // --
    public function get_products()
    {
        try {

            $sql = 'SELECT 
                        p.id AS id_product,
                        p.code,
                        p.id_unit,
                        u.description AS unit,
                        p.name,
                        p.description,
                        p.price,
                        p.id_label,
                        l.name AS label,
                        p.status
                    FROM products p
                    INNER JOIN measuring_unit u 
                        ON u.id = p.id_unit
                    LEFT JOIN labels l 
                        ON l.id = p.id_label';

            $result = $this->pdo->fetchAll($sql);

            if ($result) {

                $response = array(
                    'status' => 'OK',
                    'result' => $result
                );

            } else {

                $response = array(
                    'status' => 'ERROR',
                    'result' => array()
                );
            }

        } catch (PDOException $e) {

            $response = array(
                'status' => 'EXCEPTION',
                'result' => $e
            );
        }

        return $response;
    }


    // --
    public function get_product_by_id($bind)
    {
        try {

            $sql = 'SELECT 
                        p.id AS id_product,
                        p.code,
                        p.id_unit,
                        u.description AS unit,
                        u.code AS unit_code,
                        GROUP_CONCAT(ch.id_header) AS id_headers,
                        p.name,
                        p.description,
                        p.price,
                        p.id_label,
                        COALESCE(l.name, "Sin etiqueta") AS label,
                        p.status
                    FROM products p
                    INNER JOIN measuring_unit u 
                        ON u.id = p.id_unit
                    LEFT JOIN labels l 
                        ON l.id = p.id_label
                    INNER JOIN content_headers ch 
                        ON ch.id_product = p.id
                    WHERE p.id = :id_product
                    GROUP BY 
                        p.id,
                        p.code,
                        p.id_unit,
                        u.description,
                        p.name,
                        p.description,
                        p.price,
                        p.id_label,
                        l.name,
                        p.status';

            $result = $this->pdo->fetchOne(
                $sql,
                $bind
            );

            if ($result) {

                $response = array(
                    'status' => 'OK',
                    'result' => $result
                );

            } else {

                $response = array(
                    'status' => 'ERROR',
                    'result' => array()
                );
            }

        } catch (PDOException $e) {

            $response = array(
                'status' => 'EXCEPTION',
                'result' => $e
            );
        }

        return $response;
    }


    // --
    public function get_u_medida()
    {
        try {

            $sql = 'SELECT 
                        id,
                        description,
                        status
                    FROM measuring_unit';

            $result = $this->pdo->fetchAll($sql);

            if ($result) {

                $response = array(
                    'status' => 'OK',
                    'result' => $result
                );

            } else {

                $response = array(
                    'status' => 'ERROR',
                    'result' => array()
                );
            }

        } catch (PDOException $e) {

            $response = array(
                'status' => 'EXCEPTION',
                'result' => $e
            );
        }

        return $response;
    }


    // --
    public function get_head_types()
    {
        try {

            $sql = 'SELECT 
                        id,
                        name
                    FROM headers';

            $result = $this->pdo->fetchAll($sql);

            if ($result) {

                $response = array(
                    'status' => 'OK',
                    'result' => $result
                );

            } else {

                $response = array(
                    'status' => 'ERROR',
                    'result' => array()
                );
            }

        } catch (PDOException $e) {

            $response = array(
                'status' => 'EXCEPTION',
                'result' => $e
            );
        }

        return $response;
    }


    // ---
    public function get_labels()
    {
        try {

            $sql = 'SELECT 
                        id,
                        name
                    FROM labels';

            $result = $this->pdo->fetchAll($sql);

            if ($result) {

                $response = array(
                    'status' => 'OK',
                    'result' => $result
                );

            } else {

                $response = array(
                    'status' => 'ERROR',
                    'result' => array()
                );
            }

        } catch (PDOException $e) {

            $response = array(
                'status' => 'EXCEPTION',
                'result' => $e
            );
        }

        return $response;
    }


    // --
    public function create_product($bind)
    {
        $this->pdo->beginTransaction();

        try {

            $sql = 'INSERT INTO products 
                    (
                        id_unit,
                        name,
                        description,
                        code
                    ) 
                    VALUES 
                    (
                        :id_u_medida,
                        :name,
                        :description,
                        :code
                    )';

            $result = $this->pdo->perform(
                $sql,
                $bind
            );

            $id = $this->pdo->lastInsertId();

            if (
                $result &&
                is_array($bind['head_type'])
            ) {

                foreach (
                    $bind['head_type'] as $value
                ) {

                    $sql_insert = 'INSERT INTO content_headers 
                                   (
                                       id_product,
                                       id_header
                                   ) 
                                   VALUES 
                                   (
                                       :id_product,
                                       :head_type
                                   )';

                    if (
                        !$this->pdo->perform(
                            $sql_insert,
                            [
                                'id_product' => $id,
                                'head_type' => $value
                            ]
                        )
                    ) {

                        throw new Exception(
                            'Error al insertar en content_headers'
                        );
                    }
                }

                $this->pdo->commit();

                return [
                    'status' => 'OK',
                    'result' => []
                ];
            }

            throw new Exception(
                'Error en la inserción'
            );

        } catch (Exception $e) {

            $this->pdo->rollBack();

            return [
                'status' => 'ERROR',
                'result' => $e->getMessage()
            ];
        }
    }


    // --
    public function update_product($bind)
    {
        $this->pdo->beginTransaction();

        try {

            $sql = 'UPDATE products 
                    SET
                        id_unit = :id_u_medida,
                        name = :name,
                        description = :description,
                        code = :code
                    WHERE id = :id_product';

            $result = $this->pdo->perform(
                $sql,
                $bind
            );

            $status_transaction = false;

            if ($result) {

                $sql_delete = 'DELETE FROM content_headers 
                               WHERE id_product = :id_product';

                $this->pdo->perform(
                    $sql_delete,
                    [
                        'id_product' =>
                            $bind['id_product']
                    ]
                );


                if (
                    is_array(
                        $bind['head_type']
                    )
                ) {

                    foreach (
                        $bind['head_type'] as $value
                    ) {

                        $bind_head_type = array(
                            'id_product' =>
                                $bind['id_product'],

                            'head_type' =>
                                $value
                        );


                        $sql_insert = 'INSERT INTO content_headers 
                                       (
                                           id_product,
                                           id_header
                                       ) 
                                       VALUES 
                                       (
                                           :id_product,
                                           :head_type
                                       )';


                        $result_insert =
                            $this->pdo->perform(
                                $sql_insert,
                                $bind_head_type
                            );


                        if (!$result_insert) {

                            $status_transaction =
                                false;

                            break;
                        }
                    }

                    $status_transaction = true;

                } else {

                    $status_transaction = false;

                    throw new Exception(
                        'head_type debe ser un array.'
                    );
                }
            }


            if ($status_transaction) {

                $response = array(
                    'status' => 'OK',
                    'result' => array()
                );

                $this->pdo->commit();

            } else {

                $response = array(
                    'status' => 'ERROR',
                    'result' => array()
                );

                $this->pdo->rollBack();
            }

        } catch (PDOException $e) {

            $response = array(
                'status' => 'EXCEPTION',
                'result' => $e
            );

            $this->pdo->rollBack();

        } catch (Exception $e) {

            $response = array(
                'status' => 'EXCEPTION',
                'result' => $e->getMessage()
            );

            $this->pdo->rollBack();
        }

        return $response;
    }


    // --
    public function delete_product($bind)
    {
        try {

            $sql = 'DELETE FROM products 
                    WHERE id = :id_product';

            $result = $this->pdo->perform(
                $sql,
                $bind
            );

            if ($result) {

                $response = array(
                    'status' => 'OK',
                    'result' => array()
                );

            } else {

                $response = array(
                    'status' => 'ERROR',
                    'result' => array()
                );
            }

        } catch (PDOException $e) {

            $response = array(
                'status' => 'EXCEPTION',
                'result' => $e
            );
        }

        return $response;
    }


    /*
    |--------------------------------------------------------------------------
    | PRODUCTOS CON STOCK
    |--------------------------------------------------------------------------
    */

    public function get_product_by_campus_and_id()
    {
        try {

            $sql = 'SELECT 
                        p.id AS id_product,
                        p.code,
                        p.id_unit,
                        u.description AS unit,
                        p.name,
                        p.description,
                        p.price,
                        p.id_label,
                        l.name AS label,
                        ps.stock,
                        p.status
                    FROM products p
                    INNER JOIN measuring_unit u 
                        ON u.id = p.id_unit
                    INNER JOIN product_stock ps 
                        ON ps.id_product = p.id
                    LEFT JOIN labels l 
                        ON l.id = p.id_label
                    WHERE ps.stock >= 1';

            $result = $this->pdo->fetchAll(
                $sql
            );

            if ($result) {

                $response = array(
                    'status' => 'OK',
                    'result' => $result
                );

            } else {

                $response = array(
                    'status' => 'ERROR',
                    'result' => array()
                );
            }

        } catch (PDOException $e) {

            $response = array(
                'status' => 'EXCEPTION',
                'result' => $e
            );
        }

        return $response;
    }


    /*
    |--------------------------------------------------------------------------
    | PRODUCTOS PARA ROOM SERVICE
    |--------------------------------------------------------------------------
    |
    | IMPORTANTE:
    | El sistema tiene otra tabla llamada "product"
    | que contiene los productos de bebidas/alimentos.
    |
    */

    public function get_roomservice_products()
    {
        try {

            $sql = 'SELECT
                        id_product,
                        product_sku,
                        product_name,
                        product_description,
                        product_price,
                        product_stock,
                        price_sell,
                        status
                    FROM product
                    WHERE status = 1
                    ORDER BY product_name ASC';

            $result = $this->pdo->fetchAll(
                $sql
            );

            if ($result) {

                $response = array(
                    'status' => 'OK',
                    'result' => $result
                );

            } else {

                $response = array(
                    'status' => 'ERROR',
                    'result' => array()
                );
            }

        } catch (PDOException $e) {

            $response = array(
                'status' => 'EXCEPTION',
                'result' => $e
            );
        }

        return $response;
    }
}
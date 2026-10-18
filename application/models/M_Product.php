<?php
    // --
    class M_Product extends Model
    {
        // --
        public function __construct()
        {
            parent::__construct();
        }

        // -- Obtener todos los productos
        public function get_product()
        {
            try {
                // --
                $sql = 'SELECT   
                            product.id_product,
                            product.product_sku,
                            product.product_name,
                            product.product_description,
                            categories.description AS category_description,
                            unit_type.description AS unit_type_description,
                            product.product_price,
                            product.product_stock,
                            product.expiration_date,
                            product.status_expiration_date 
                        FROM product
                        JOIN categories ON product.id_category = categories.id
                        LEFT JOIN unit_type ON product.unit_type = unit_type.id';
                        
                // --
                $result = $this->pdo->fetchAll($sql);
                if ($result) {
                    $response = array('status' => 'OK', 'result' => $result);
                } else {
                    $response = array('status' => 'ERROR', 'result' => array());
                }
            } catch (PDOException $e) {
                $response = array('status' => 'EXCEPTION', 'result' => $e);
            }
            return $response;
        }

        // -- Obtener producto por ID
        public function get_product_by_id($bind)
        {
        try {
            $sql = 'SELECT 
                        product.id_product,
                        product.product_sku,
                        product.product_name,
                        product.product_description,
                        product.id_category,
                        product.product_price,
                        product.product_stock,
                        product.expiration_date,
                        product.status_expiration_date  
                    FROM product 
                    WHERE id_product = :id_product';

            $result = $this->pdo->fetchOne($sql, $bind);
            
            if ($result) {
                $response = array('status' => 'OK', 'result' => $result);
            } else {
                $response = array('status' => 'ERROR', 'result' => array());
            }
        } catch (PDOException $e) {
            $response = array('status' => 'EXCEPTION', 'result' => $e);
        }

        return $response;
        }

        // -- Crear Producto
        public function create_product($bind)
        {
        try {
            $sql = 'INSERT INTO product 
                    (product_sku, product_name, product_description, id_category, product_price, product_stock, expiration_date, status_expiration_date) 
                    VALUES 
                    (:product_sku, :product_name, :product_description, :id_category, :product_price, :product_stock, :expiration_date, :status_expiration_date)';
            
            $result = $this->pdo->perform($sql, $bind);
            
            if ($result) {
                $response = array('status' => 'OK', 'result' => array());
            } else {
                $response = array('status' => 'ERROR', 'result' => array());
            }
        } catch (PDOException $e) {
            $response = array('status' => 'EXCEPTION', 'result' => $e);
        }
        return $response;
        }

        // -- Actualizar Producto
        public function update_product($bind)
        {
        try {
            $sql = 'UPDATE product 
                    SET 
                        product_sku = :product_sku,
                        product_name = :product_name,
                        product_description = :product_description,
                        id_category = :id_category,
                        product_price = :product_price,
                        product_stock = :product_stock,
                        expiration_date = :expiration_date,
                        status_expiration_date = :status_expiration_date
                    WHERE id_product = :id_product';

            $result = $this->pdo->perform($sql, $bind);

            if ($result) {
                $response = array('status' => 'OK', 'result' => array());
            } else {
                $response = array('status' => 'ERROR', 'result' => array());
            }
        } catch (PDOException $e) {
            $response = array('status' => 'EXCEPTION', 'result' => $e);
        }
        return $response;
        }

        // -- Eliminar un producto
        public function delete_product($bind)
        {
            try {
                $sql = 'DELETE FROM product WHERE id_product = :id_product';
                // --
                $result = $this->pdo->perform($sql, $bind);
                if ($result) {
                    $response = array('status' => 'OK', 'result' => array());
                } else {
                    $response = array('status' => 'ERROR', 'result' => array());
                }
            } catch (PDOException $e) {
                $response = array('status' => 'EXCEPTION', 'result' => $e);
            }
            return $response;
        }
    }
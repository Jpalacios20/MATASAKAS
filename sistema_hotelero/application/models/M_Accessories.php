<?php 
class M_Accessories extends Model {
    public function __construct() {
        parent::__construct();
    }

    public function get_accessories() {
        try {
            $sql = 'SELECT 
                        accessory.id_accessory,
                        accessory.sku_accessory,
                        accessory.accessory_name,
                        unit_type.description AS unit_type_description,
                        accessory.accessory_price,
                        accessory.accessory_stock
                    FROM accessory
                    LEFT JOIN unit_type ON accessory.unit_type = unit_type.id';
                    
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

    public function get_accessory_by_id($bind) {
        try {
            $sql = 'SELECT 
                        id_accessory,
                        accessory_description,
                        accessory_price,
                        accessory_stock
                    FROM accessory 
                    WHERE id_accessory = :id_accessory';
    
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['id_accessory' => $bind['id_accessory']]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            if ($result) {
                $response = array('status' => 'OK', 'result' => $result);
            } else {
                $response = array('status' => 'ERROR', 'result' => array());
            }
        } catch (PDOException $e) {
            $response = array('status' => 'EXCEPTION', 'result' => $e->getMessage());
        }
    
        // Agregar depuración
        error_log("Consulta ejecutada: " . json_encode($bind));
        error_log("Resultado de la consulta: " . json_encode($response));
    
        return $response;
    }
    
    public function create_accessory($bind) {
        try {
            $sql = 'INSERT INTO accessory 
                    (id_accessory, accessory_description, accessory_price, accessory_stock) 
                    VALUES 
                    (:id_accessory, :accessory_description, :accessory_price, :accessory_stock)';
            
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

    public function update_accessory($bind) {
        try {
            $sql = 'UPDATE accessory 
                    SET 
                        accessory_description = :accessory_description,
                        accessory_price = :accessory_price,
                        accessory_stock = :accessory_stock
                    WHERE id_accessory = :id_accessory';

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

    public function delete_accessory($bind) {
        try {
            $sql = 'DELETE FROM accessory WHERE id_accessory = :id_accessory';
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
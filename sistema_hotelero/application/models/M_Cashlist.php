<?php

class M_Cashlist extends Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_cashlist() {
        try {
            $sql = "SELECT c.id, CONCAT(u.first_name, ' ', u.last_name) AS usuario, c.monto_inicial, c.monto_final, c.fecha_hora_apertura, c.fecha_hora_cierre, c.observaciones, c.estado
                    FROM caja c
                    LEFT JOIN user u ON c.id_user = u.id
                    ORDER BY c.id DESC";
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
}
<?php

class M_Report_Cash extends Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_report_data($filters = array()){
        try{ 
            $sql = 'SELECT 
                c.id,
                c.fecha_hora_apertura,
                c.fecha_hora_cierre,
                CONCAT(u.first_name, " ", u.last_name) as usuario,
                c.monto_inicial,
                c.ingresos,
                c.egresos,
                c.monto_final,
                c.observaciones,
                c.comprobante,
                c.estado
                FROM caja c
                INNER JOIN user u ON u.id = c.id_user';

            $params = array();
            $conditions = array();

            if (!empty($filters['fecha_inicio'])) {
                $conditions[] = "DATE(c.fecha_hora_apertura) >= ?";
                $params[] = $filters['fecha_inicio'];
            }

            if (!empty($filters['fecha_fin'])) {
                $conditions[] = "DATE(c.fecha_hora_apertura) <= ?";
                $params[] = $filters['fecha_fin'];
            }

            if (!empty($filters['id_user'])) {
                $conditions[] = "c.id_user = ?";
                $params[] = $filters['id_user'];
            }

            if (!empty($conditions)) {
                $sql .= ' WHERE ' . implode(' AND ', $conditions);
            }

            $sql .= ' ORDER BY c.fecha_hora_apertura DESC';

            $result = $this->pdo->fetchAll($sql, $params);

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

    public function get_user() {
        try {
            $sql = "SELECT id, CONCAT(first_name, ' ', last_name) AS full_name FROM user WHERE status = 1 ORDER BY first_name";
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
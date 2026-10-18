<?php
class M_Initial_open extends Model {
    public function __construct() {
        parent::__construct();
    }

    public function save_initial_open($data) {
        try {
            $sql_check = "SELECT id FROM caja WHERE estado = 'Abierta'";
            $stmt = $this->pdo->prepare($sql_check);
            $stmt->execute();
            
            if ($stmt->fetch()) {
                return [
                    'status' => 'ERROR',
                    'result' => 'Ya existe una caja abierta en el sistema.'
                ];
            }

            $sql = "INSERT INTO caja (
                fecha_hora_apertura,
                id_user,
                monto_inicial,
                observaciones,
                estado
            ) VALUES (
                :fecha_hora_apertura,
                :id_user,
                :monto_inicial,
                :observaciones,
                'Abierta'
            )";

            $params = [
                ':fecha_hora_apertura' => $data['fecha_hora_apertura'],
                ':id_user' => $data['id_user'],
                ':monto_inicial' => $data['monto_inicial'],
                ':observaciones' => !empty($data['observaciones']) ? $data['observaciones'] : null
            ];

            $stmt = $this->pdo->prepare($sql);
            
            if ($stmt->execute($params)) {
                return [
                    'status' => 'OK',
                    'result' => 'Caja aperturada exitosamente'
                ];
            }

            return [
                'status' => 'ERROR',
                'result' => 'Error al aperturar la caja'
            ];

        } catch (PDOException $e) {
            error_log("Error en save_initial_open: " . $e->getMessage());
            return [
                'status' => 'ERROR',
                'result' => 'Error al guardar en la base de datos: ' . $e->getMessage()
            ];
        }
    }

    public function get_caja() {
        try {
            $sql = "SELECT * FROM caja";
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

    public function get_user() {
        try {
            $sql = "SELECT id, CONCAT(first_name, ' ', last_name) AS full_name FROM user";
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
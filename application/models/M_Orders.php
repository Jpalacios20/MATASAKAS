<?php

class M_Orders extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    // Comandas del restaurante (por fecha, o todas si no se envía)
    public function get_orders($date)
    {
        try {
            $sql = 'SELECT id, table_number, status, notes, total, created_at
                    FROM hs_orders
                    WHERE order_type = "RESTAURANTE"';

            $bind = array();

            if ($date !== '') {
                $sql .= ' AND DATE(created_at) = :date';
                $bind['date'] = $date;
            }

            $sql .= ' ORDER BY id DESC';

            $result = $this->pdo->fetchAll($sql, $bind);

            return array('status' => 'OK', 'result' => $result ? $result : array());

        } catch (PDOException $e) {
            return array('status' => 'EXCEPTION', 'result' => $e);
        }
    }

    // Cabecera + platos de una comanda
    public function get_order_detail($bind)
    {
        try {
            $order = $this->pdo->fetchOne(
                'SELECT id, table_number, status, notes, total, created_at
                 FROM hs_orders
                 WHERE id = :id AND order_type = "RESTAURANTE"
                 LIMIT 1',
                $bind
            );

            if (!$order) {
                return array('status' => 'ERROR', 'result' => array());
            }

            $items = $this->pdo->fetchAll(
                'SELECT name, quantity, unit_price, total
                 FROM hs_order_items
                 WHERE order_id = :id
                 ORDER BY id ASC',
                $bind
            );

            $order['items'] = $items ? $items : array();

            return array('status' => 'OK', 'result' => $order);

        } catch (PDOException $e) {
            return array('status' => 'EXCEPTION', 'result' => $e);
        }
    }

    public function change_status($bind)
    {
        try {
            $result = $this->pdo->perform(
                'UPDATE hs_orders
                 SET status = :status
                 WHERE id = :id AND order_type = "RESTAURANTE"',
                $bind
            );

            return array('status' => $result ? 'OK' : 'ERROR', 'result' => array());

        } catch (PDOException $e) {
            return array('status' => 'EXCEPTION', 'result' => $e);
        }
    }
}
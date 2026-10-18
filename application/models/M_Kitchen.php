<?php

class M_Kitchen extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    // Comandas activas del restaurante con sus platos
    public function get_active_orders()
    {
        try {
            $rows = $this->pdo->fetchAll(
                'SELECT o.id, o.table_number, o.status, o.notes, o.created_at,
                        i.name, i.quantity
                 FROM hs_orders o
                 LEFT JOIN hs_order_items i ON i.order_id = o.id
                 WHERE o.order_type = "RESTAURANTE"
                   AND o.status IN ("PENDIENTE", "EN_PREPARACION", "LISTO")
                 ORDER BY o.id ASC, i.id ASC'
            );

            $orders = array();

            if ($rows) {
                foreach ($rows as $r) {
                    $id = $r['id'];

                    if (!isset($orders[$id])) {
                        $orders[$id] = array(
                            'id'           => $r['id'],
                            'table_number' => $r['table_number'],
                            'status'       => $r['status'],
                            'notes'        => $r['notes'],
                            'created_at'   => $r['created_at'],
                            'items'        => array()
                        );
                    }

                    if ($r['name'] !== null) {
                        $orders[$id]['items'][] = array(
                            'name'     => $r['name'],
                            'quantity' => $r['quantity']
                        );
                    }
                }
            }

            return array('status' => 'OK', 'result' => array_values($orders));

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
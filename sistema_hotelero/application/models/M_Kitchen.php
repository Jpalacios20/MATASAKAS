<?php

class M_Kitchen extends Model
{
    // =========================================================
    // CONSTRUCTOR
    // =========================================================

    public function __construct()
    {
        parent::__construct();
    }


    // =========================================================
    // OBTENER COMANDAS ACTIVAS CON SUS PLATOS
    // =========================================================
    //
    // Muestra las comandas que la cocina todavía tiene en curso
    // (no incluye las ya PAGADAS). Incluye restaurante y room service.
    //
    // =========================================================

    public function get_orders()
    {
        try {

            $sql = 'SELECT
                        o.id AS id_order,
                        o.order_type,
                        o.status,
                        o.notes,
                        o.created_at,
                        CASE
                            WHEN o.order_type = "ROOM_SERVICE"
                                THEN CONCAT("Hab. ", o.room_number)
                            ELSE o.table_number
                        END AS location
                    FROM hs_orders o
                    WHERE o.status IN ("PENDIENTE", "EN_PREPARACION", "LISTO")
                    ORDER BY o.created_at ASC';

            $orders = $this->pdo->fetchAll($sql);

            if (!$orders) {

                return array(
                    'status' => 'ERROR',
                    'result' => array()
                );
            }

            // IDs de las comandas (convertidos a número por seguridad)
            $ids = array();

            foreach ($orders as $order) {
                $ids[] = intval($order['id_order']);
            }

            $sql_items = 'SELECT
                              order_id,
                              name,
                              quantity
                          FROM hs_order_items
                          WHERE order_id IN (' . implode(',', $ids) . ')
                          ORDER BY order_id ASC';

            $items = $this->pdo->fetchAll($sql_items);

            // Agrupar los platos dentro de cada comanda
            $by_order = array();

            if ($items) {

                foreach ($items as $item) {

                    $by_order[$item['order_id']][] = array(
                        'name'     => $item['name'],
                        'quantity' => $item['quantity']
                    );
                }
            }

            foreach ($orders as $key => $order) {

                $orders[$key]['items'] = isset($by_order[$order['id_order']])
                    ? $by_order[$order['id_order']]
                    : array();
            }

            return array(
                'status' => 'OK',
                'result' => $orders
            );

        } catch (PDOException $e) {

            return array(
                'status' => 'EXCEPTION',
                'result' => $e
            );
        }
    }


    // =========================================================
    // CAMBIAR ESTADO DE UNA COMANDA
    // =========================================================
    //
    // No permite tocar comandas que ya están PAGADAS.
    //
    // =========================================================

    public function update_status($bind)
    {
        try {

            $sql = 'UPDATE hs_orders
                    SET
                        status = :status,
                        updated_at = NOW()
                    WHERE id = :id_order
                    AND status <> "PAGADO"';

            $result = $this->pdo->perform(
                $sql,
                $bind
            );

            if ($result && $result->rowCount() > 0) {

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
}
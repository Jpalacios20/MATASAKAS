<?php

class M_RoomService extends Model
{
    public function __construct()
    {
        parent::__construct();
    }


    public function crear_pedido($bind)
    {
        $this->pdo->beginTransaction();

        try {

            $sqlRoom = 'SELECT
                            id_room,
                            room_number,
                            room_status
                        FROM room
                        WHERE id_room = :id_room
                        LIMIT 1';

            $room = $this->pdo->fetchOne($sqlRoom, [
                'id_room' => $bind['room_id']
            ]);

            if (!$room) {
                throw new Exception(
                    'La habitación seleccionada no existe.'
                );
            }

            if (
                !isset($bind['items']) ||
                !is_array($bind['items']) ||
                count($bind['items']) === 0
            ) {
                throw new Exception(
                    'El pedido debe tener al menos un producto.'
                );
            }

            $totalPedido = 0;
            $itemsProcesados = [];

            foreach ($bind['items'] as $item) {

                $productId = (int)(
                    $item['product_id'] ?? 0
                );

                $quantity = (int)(
                    $item['quantity'] ?? 0
                );

                if (
                    $productId <= 0 ||
                    $quantity <= 0
                ) {
                    throw new Exception(
                        'Producto o cantidad inválida.'
                    );
                }

                $sqlProduct = 'SELECT
                                    id_product,
                                    product_sku,
                                    product_name,
                                    product_price,
                                    status
                               FROM product
                               WHERE id_product = :id_product
                               LIMIT 1';

                $product = $this->pdo->fetchOne(
                    $sqlProduct,
                    [
                        'id_product' => $productId
                    ]
                );

                if (!$product) {
                    throw new Exception(
                        'El producto seleccionado no existe.'
                    );
                }

                if ((int)$product['status'] !== 1) {
                    throw new Exception(
                        'El producto "' .
                        $product['product_name'] .
                        '" no está disponible.'
                    );
                }

                $unitPrice = (float)(
                    $product['product_price'] ?? 0
                );

                $itemTotal = $unitPrice * $quantity;

                $totalPedido += $itemTotal;

                $itemsProcesados[] = [
                    'id_product' => (int)$product['id_product'],
                    'sku' => $product['product_sku'],
                    'name' => $product['product_name'],
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total' => $itemTotal
                ];
            }


            $sqlOrder = 'INSERT INTO hs_orders
                        (
                            order_type,
                            room_number,
                            customer_name,
                            status,
                            notes,
                            total,
                            created_by
                        )
                        VALUES
                        (
                            :order_type,
                            :room_number,
                            :customer_name,
                            :status,
                            :notes,
                            :total,
                            :created_by
                        )';

            $resultOrder = $this->pdo->perform(
                $sqlOrder,
                [
                    'order_type' => 'ROOM_SERVICE',
                    'room_number' => $room['room_number'],
                    'customer_name' => $bind['customer_name'] ?? '',
                    'status' => 'PENDIENTE',
                    'notes' => $bind['notes'] ?? '',
                    'total' => $totalPedido,
                    'created_by' => $bind['created_by'] ?? null
                ]
            );

            if (!$resultOrder) {
                throw new Exception(
                    'No fue posible crear el pedido.'
                );
            }

            $orderId = $this->pdo->lastInsertId();

            if (!$orderId) {
                throw new Exception(
                    'No se pudo obtener el ID del pedido.'
                );
            }


            foreach ($itemsProcesados as $item) {

                $sqlItem = 'INSERT INTO hs_order_items
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

                $resultItem = $this->pdo->perform(
                    $sqlItem,
                    [
                        'order_id' => $orderId,
                        'kind' => 'PRODUCTO',
                        'source_id' => $item['id_product'],
                        'sku' => $item['sku'],
                        'name' => $item['name'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'total' => $item['total']
                    ]
                );

                if (!$resultItem) {
                    throw new Exception(
                        'No fue posible guardar el detalle del pedido.'
                    );
                }
            }

            $this->pdo->commit();

            return [
                'status' => 'OK',
                'result' => [
                    'id_order' => (int)$orderId,
                    'room_number' => $room['room_number'],
                    'total' => $totalPedido
                ]
            ];

        } catch (PDOException $e) {

            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            return [
                'status' => 'EXCEPTION',
                'result' => $e
            ];

        } catch (Exception $e) {

            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            return [
                'status' => 'ERROR',
                'result' => $e->getMessage()
            ];
        }
    }


    public function get_pedidos_pendientes()
    {
        return $this->get_pedidos_por_estado('PENDIENTE');
    }


    public function get_pedidos_preparacion()
    {
        return $this->get_pedidos_por_estado('EN_PREPARACION');
    }


    public function get_pedidos_listos()
    {
        return $this->get_pedidos_por_estado('LISTO');
    }


    public function get_pedidos_entregados()
    {
        return $this->get_pedidos_por_estado('ENTREGADO');
    }


    private function get_pedidos_por_estado($estado)
    {
        try {

            $sql = 'SELECT
                        o.id,
                        o.order_type,
                        o.room_number,
                        o.customer_name,
                        o.status,
                        o.notes,
                        o.total,
                        o.created_at
                    FROM hs_orders o
                    WHERE o.order_type = :order_type
                    AND o.status = :status
                    ORDER BY o.id DESC';

            $result = $this->pdo->fetchAll(
                $sql,
                [
                    'order_type' => 'ROOM_SERVICE',
                    'status' => $estado
                ]
            );

            if (!$result) {

                return [
                    'status' => 'ERROR',
                    'result' => []
                ];
            }

            foreach ($result as &$pedido) {

                $sqlItems = 'SELECT
                                id,
                                order_id,
                                kind,
                                source_id,
                                sku,
                                name,
                                quantity,
                                unit_price,
                                total
                             FROM hs_order_items
                             WHERE order_id = :order_id
                             ORDER BY id ASC';

                $pedido['items'] = $this->pdo->fetchAll(
                    $sqlItems,
                    [
                        'order_id' => $pedido['id']
                    ]
                );
            }

            unset($pedido);

            return [
                'status' => 'OK',
                'result' => $result
            ];

        } catch (PDOException $e) {

            return [
                'status' => 'EXCEPTION',
                'result' => $e
            ];
        }
    }


    public function get_reservaciones_para_cargo($orderId)
    {
        try {

            $sqlOrder = 'SELECT
                            id,
                            room_number,
                            customer_name,
                            total,
                            status
                         FROM hs_orders
                         WHERE id = :id
                         AND order_type = :order_type
                         LIMIT 1';

            $order = $this->pdo->fetchOne(
                $sqlOrder,
                [
                    'id' => $orderId,
                    'order_type' => 'ROOM_SERVICE'
                ]
            );

            if (!$order) {
                return [
                    'status' => 'ERROR',
                    'result' => 'El pedido no existe.'
                ];
            }

            $sql = 'SELECT
                        r.id_reservation,
                        r.id_room,
                        r.id_guest,
                        r.checkin_date,
                        r.checkin_time,
                        r.checkout_date,
                        r.checkout_time,
                        r.status,
                        rm.room_number,
                        CONCAT(
                            g.first_names,
                            " ",
                            g.last_names
                        ) AS guest_name
                    FROM reservation r
                    INNER JOIN room rm
                        ON rm.id_room = r.id_room
                    LEFT JOIN guest g
                        ON g.id_guest = r.id_guest
                    WHERE rm.room_number = :room_number
                    ORDER BY
                        r.id_reservation DESC';

            $reservaciones = $this->pdo->fetchAll(
                $sql,
                [
                    'room_number' => $order['room_number']
                ]
            );

            if (!$reservaciones) {

                return [
                    'status' => 'ERROR',
                    'result' => 'No existen reservas para la habitación ' .
                        $order['room_number'] . '.'
                ];
            }

            return [
                'status' => 'OK',
                'result' => $reservaciones
            ];

        } catch (PDOException $e) {

            return [
                'status' => 'EXCEPTION',
                'result' => $e
            ];
        }
    }


    public function cargar_pedido_habitacion($bind)
    {
        $this->pdo->beginTransaction();

        try {

            $orderId = (int)$bind['order_id'];
            $reservationId = (int)$bind['id_reservation'];


            /*
            |--------------------------------------------------------------------------
            | PEDIDO
            |--------------------------------------------------------------------------
            */

            $sqlOrder = 'SELECT
                            id,
                            room_number,
                            total,
                            status
                         FROM hs_orders
                         WHERE id = :id
                         AND order_type = :order_type
                         LIMIT 1';

            $order = $this->pdo->fetchOne(
                $sqlOrder,
                [
                    'id' => $orderId,
                    'order_type' => 'ROOM_SERVICE'
                ]
            );

            if (!$order) {
                throw new Exception(
                    'El pedido no existe.'
                );
            }

            if ($order['status'] !== 'ENTREGADO') {
                throw new Exception(
                    'El pedido ya no está disponible para cargar.'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | RESERVACIÓN
            |--------------------------------------------------------------------------
            */

            $sqlReservation = 'SELECT
                                    r.id_reservation,
                                    r.id_room,
                                    rm.room_number,
                                    r.status
                               FROM reservation r
                               INNER JOIN room rm
                                   ON rm.id_room = r.id_room
                               WHERE r.id_reservation = :id_reservation
                               LIMIT 1';

            $reservation = $this->pdo->fetchOne(
                $sqlReservation,
                [
                    'id_reservation' => $reservationId
                ]
            );

            if (!$reservation) {
                throw new Exception(
                    'La reserva seleccionada no existe.'
                );
            }


            if (
                (string)$reservation['room_number'] !==
                (string)$order['room_number']
            ) {
                throw new Exception(
                    'La reserva no corresponde a la habitación del pedido.'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | PAYMENT
            |--------------------------------------------------------------------------
            */

            $sqlPayment = 'SELECT
                                id_payment,
                                payment_sales
                           FROM payment
                           WHERE id_reservation = :id_reservation
                           ORDER BY id_payment DESC
                           LIMIT 1';

            $payment = $this->pdo->fetchOne(
                $sqlPayment,
                [
                    'id_reservation' => $reservationId
                ]
            );


            if (!$payment) {

                $sqlInsertPayment = 'INSERT INTO payment
                                    (
                                        id_reservation,
                                        payment_room,
                                        payment_sales,
                                        payment_extra,
                                        payment_discount,
                                        pre_payment,
                                        payment_total
                                    )
                                    VALUES
                                    (
                                        :id_reservation,
                                        0,
                                        0,
                                        0,
                                        0,
                                        0,
                                        NULL
                                    )';

                $resultPayment = $this->pdo->perform(
                    $sqlInsertPayment,
                    [
                        'id_reservation' => $reservationId
                    ]
                );

                if (!$resultPayment) {
                    throw new Exception(
                        'No fue posible crear la cuenta de la reserva.'
                    );
                }

                $paymentId = $this->pdo->lastInsertId();

                $paymentSalesActual = 0;

            } else {

                $paymentId = (int)$payment['id_payment'];

                $paymentSalesActual = (float)(
                    $payment['payment_sales'] ?? 0
                );
            }


            /*
            |--------------------------------------------------------------------------
            | DETALLES DEL PEDIDO
            |--------------------------------------------------------------------------
            */

            $sqlItems = 'SELECT
                            source_id,
                            sku,
                            name,
                            quantity,
                            unit_price,
                            total
                         FROM hs_order_items
                         WHERE order_id = :order_id
                         ORDER BY id ASC';

            $items = $this->pdo->fetchAll(
                $sqlItems,
                [
                    'order_id' => $orderId
                ]
            );

            if (!$items) {
                throw new Exception(
                    'El pedido no tiene productos.'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | INSERTAR CONSUMOS
            |--------------------------------------------------------------------------
            */

            foreach ($items as $item) {

                $sqlDetail = 'INSERT INTO payment_sales_details
                              (
                                  id_payment,
                                  id_product,
                                  product_sku,
                                  id_reservation,
                                  fecha_venta,
                                  product_name,
                                  cantidad,
                                  product_price,
                                  total_price
                              )
                              VALUES
                              (
                                  :id_payment,
                                  :id_product,
                                  :product_sku,
                                  :id_reservation,
                                  CURDATE(),
                                  :product_name,
                                  :cantidad,
                                  :product_price,
                                  :total_price
                              )';

                $resultDetail = $this->pdo->perform(
                    $sqlDetail,
                    [
                        'id_payment' => $paymentId,
                        'id_product' => $item['source_id'],
                        'product_sku' => $item['sku'] ?? '',
                        'id_reservation' => $reservationId,
                        'product_name' => $item['name'],
                        'cantidad' => $item['quantity'],
                        'product_price' => $item['unit_price'],
                        'total_price' => $item['total']
                    ]
                );

                if (!$resultDetail) {
                    throw new Exception(
                        'No fue posible registrar el consumo.'
                    );
                }
            }


            /*
            |--------------------------------------------------------------------------
            | ACTUALIZAR TOTAL DE CONSUMOS
            |--------------------------------------------------------------------------
            */

            $nuevoTotal =
                $paymentSalesActual +
                (float)$order['total'];


            $sqlUpdatePayment = 'UPDATE payment
                                 SET payment_sales = :payment_sales
                                 WHERE id_payment = :id_payment';

            $resultUpdatePayment = $this->pdo->perform(
                $sqlUpdatePayment,
                [
                    'payment_sales' => $nuevoTotal,
                    'id_payment' => $paymentId
                ]
            );

            if (!$resultUpdatePayment) {
                throw new Exception(
                    'No fue posible actualizar el total de consumos.'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | MARCAR PEDIDO COMO PAGADO
            |--------------------------------------------------------------------------
            |
            | En el esquema actual no existe un estado CARGADO.
            | PAGADO se utiliza aquí como estado final para evitar
            | volver a cargar el mismo pedido.
            |--------------------------------------------------------------------------
            */

            $sqlUpdateOrder = 'UPDATE hs_orders
                               SET status = :status
                               WHERE id = :id
                               AND order_type = :order_type
                               AND status = :status_actual';

            $resultUpdateOrder = $this->pdo->perform(
                $sqlUpdateOrder,
                [
                    'status' => 'PAGADO',
                    'id' => $orderId,
                    'order_type' => 'ROOM_SERVICE',
                    'status_actual' => 'ENTREGADO'
                ]
            );

            if (!$resultUpdateOrder) {
                throw new Exception(
                    'No fue posible finalizar el cargo del pedido.'
                );
            }


            $this->pdo->commit();


            return [
                'status' => 'OK',
                'result' => [
                    'id_order' => $orderId,
                    'id_reservation' => $reservationId,
                    'id_payment' => (int)$paymentId,
                    'total_cargado' => (float)$order['total'],
                    'total_consumos' => $nuevoTotal
                ]
            ];


        } catch (PDOException $e) {

            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            return [
                'status' => 'EXCEPTION',
                'result' => $e
            ];

        } catch (Exception $e) {

            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            return [
                'status' => 'ERROR',
                'result' => $e->getMessage()
            ];
        }
    }


    public function cambiar_estado($bind)
    {
        try {

            $sql = 'UPDATE hs_orders
                    SET status = :status
                    WHERE id = :id
                    AND order_type = :order_type';

            $result = $this->pdo->perform(
                $sql,
                [
                    'id' => $bind['id'],
                    'status' => $bind['status'],
                    'order_type' => 'ROOM_SERVICE'
                ]
            );

            if ($result) {

                return [
                    'status' => 'OK',
                    'result' => []
                ];
            }

            return [
                'status' => 'ERROR',
                'result' => 'No fue posible actualizar el pedido.'
            ];

        } catch (PDOException $e) {

            return [
                'status' => 'EXCEPTION',
                'result' => $e
            ];
        }
    }
}
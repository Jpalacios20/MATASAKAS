<?php

class M_POS extends Model
{
    public function __construct() { parent::__construct(); }

    private function error($e)
    {
        return ['status' => 'EXCEPTION', 'result' => $e];
    }

    private function precioProducto($producto)
    {
        $precio = (float)($producto['price_sell'] ?? 0);
        if ($precio <= 0) $precio = (float)($producto['product_price'] ?? 0);
        return $precio;
    }

    public function get_productos()
    {
        try {
            $sql = "SELECT p.id_product, p.product_sku, p.product_name, p.product_description,
                           p.id_category, c.description AS category, p.product_stock,
                           p.price_sell, p.product_price,
                           CASE WHEN p.price_sell > 0 THEN p.price_sell ELSE p.product_price END AS sale_price
                    FROM product p
                    LEFT JOIN categories c ON c.id = p.id_category
                    WHERE p.status = 1
                    ORDER BY p.product_name ASC";
            return ['status' => 'OK', 'result' => $this->pdo->fetchAll($sql) ?: []];
        } catch (Throwable $e) { return $this->error($e); }
    }

    private function prepararDetalles($items)
    {
        if (!is_array($items) || !$items) throw new RuntimeException('Agrega al menos un producto.');
        $details = [];
        $total = 0;

        foreach ($items as $item) {
            $idProduct = (int)($item['id_product'] ?? 0);
            $quantity = (int)($item['quantity'] ?? $item['cantidad'] ?? 0);
            if ($idProduct <= 0 || $quantity <= 0) throw new RuntimeException('Producto o cantidad inválidos.');

            // Bloquea la fila para evitar vender más unidades de las disponibles.
            $product = $this->pdo->fetchOne(
                "SELECT id_product, product_sku, product_name, product_stock, price_sell, product_price
                 FROM product WHERE id_product = :id AND status = 1 LIMIT 1",
                ['id' => $idProduct]
            );
            if (!$product) throw new RuntimeException('Un producto no está disponible.');
            if ($quantity > (int)$product['product_stock']) {
                throw new RuntimeException('Stock insuficiente para ' . $product['product_name']);
            }

            $price = $this->precioProducto($product);
            if ($price < 0) throw new RuntimeException('El precio del producto no es válido.');
            $subtotal = round($price * $quantity, 2);
            $total += $subtotal;
            $details[] = [
                'id_product' => $idProduct, 'sku' => $product['product_sku'],
                'name' => $product['product_name'], 'quantity' => $quantity,
                'unit_price' => $price, 'total' => $subtotal
            ];
        }

        return ['details' => $details, 'total' => round($total, 2)];
    }

    private function insertarDetalles($orderId, $details)
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO hs_order_items (order_id, kind, source_id, sku, name, quantity, unit_price, total)
             VALUES (:order_id, 'PRODUCTO', :source_id, :sku, :name, :quantity, :unit_price, :total)"
        );
        foreach ($details as $item) {
            $stmt->execute([
                'order_id' => $orderId, 'source_id' => $item['id_product'],
                'sku' => $item['sku'], 'name' => $item['name'], 'quantity' => $item['quantity'],
                'unit_price' => number_format($item['unit_price'], 2, '.', ''),
                'total' => number_format($item['total'], 2, '.', '')
            ]);
        }
    }

    private function descontarStock($details)
    {
        $stmt = $this->pdo->prepare(
            "UPDATE product SET product_stock = product_stock - :quantity
             WHERE id_product = :id AND product_stock >= :quantity AND status = 1"
        );
        foreach ($details as $item) {
            $stmt->execute(['quantity' => $item['quantity'], 'id' => $item['id_product']]);
            if ($stmt->rowCount() !== 1) {
                throw new RuntimeException('No se pudo actualizar el stock de ' . $item['name'] . '. Verifica las existencias.');
            }
        }
    }

    public function crear_venta($bind)
    {
        try {
            $customer = trim($bind['customer_name'] ?? '');
            $payment = strtoupper(trim($bind['payment_method'] ?? ''));
            $notes = trim($bind['notes'] ?? '');
            $allowed = ['EFECTIVO', 'TARJETA', 'TRANSFERENCIA', 'YAPE', 'PLIN', 'OTRO'];
            if (!in_array($payment, $allowed, true)) return ['status' => 'ERROR', 'result' => 'Selecciona un método de pago válido.'];

            $this->pdo->beginTransaction();
            $prepared = $this->prepararDetalles($bind['items'] ?? []);
            $details = $prepared['details'];
            $total = $prepared['total'];
            $fullNotes = 'Método de pago: ' . $payment . ($notes !== '' ? ' | Observaciones: ' . $notes : '');

            $stmt = $this->pdo->prepare(
                "INSERT INTO hs_orders (order_type, customer_name, table_number, status, notes, total, created_by)
                 VALUES ('POS', :customer, NULL, 'PAGADO', :notes, :total, :created_by)"
            );
            $stmt->execute([
                'customer' => $customer !== '' ? $customer : null,
                'notes' => $fullNotes, 'total' => number_format($total, 2, '.', ''),
                'created_by' => !empty($bind['created_by']) ? (int)$bind['created_by'] : null
            ]);
            $id = (int)$this->pdo->lastInsertId();
            if ($id <= 0) throw new RuntimeException('No se pudo crear la venta.');
            $this->insertarDetalles($id, $details);
            $this->descontarStock($details);
            $this->pdo->commit();

            return ['status' => 'OK', 'result' => [
                'id' => $id, 'customer_name' => $customer, 'payment_method' => $payment,
                'total' => number_format($total, 2, '.', ''), 'items' => $details
            ]];
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) $this->pdo->rollBack();
            return $this->error($e);
        }
    }

    public function get_ventas()
    {
        try {
            $sql = "SELECT o.id, o.customer_name, o.status, o.notes, o.total,
                           DATE_FORMAT(o.created_at, '%Y-%m-%d %H:%i') AS created_at,
                           COALESCE(d.cantidad_productos, 0) AS cantidad_productos
                    FROM hs_orders o
                    LEFT JOIN (SELECT order_id, SUM(quantity) AS cantidad_productos FROM hs_order_items GROUP BY order_id) d
                      ON d.order_id = o.id
                    WHERE o.order_type = 'POS' ORDER BY o.id DESC";
            return ['status' => 'OK', 'result' => $this->pdo->fetchAll($sql) ?: []];
        } catch (Throwable $e) { return $this->error($e); }
    }

    public function get_ventas_recepcion() { return $this->get_ventas(); }

    public function get_habitaciones()
    {
        try {
            $sql = "SELECT r.id_reservation, rm.id_room, rm.room_number, g.id_guest,
                           TRIM(CONCAT(COALESCE(g.first_names, ''), ' ', COALESCE(g.last_names, ''))) AS customer_name,
                           r.status AS estado_reserva
                    FROM reservation r
                    INNER JOIN room rm ON rm.id_room = r.id_room
                    LEFT JOIN guest g ON g.id_guest = r.id_guest
                    WHERE LOWER(TRIM(r.status)) = 'ocupado'
                    ORDER BY rm.room_number ASC";
            return ['status' => 'OK', 'result' => $this->pdo->fetchAll($sql) ?: []];
        } catch (Throwable $e) { return $this->error($e); }
    }

    public function registrar_venta_minibar($bind) { return $this->registrarConsumoHabitacion($bind, 'MINIBAR'); }
    public function registrar_venta_habitacion($bind) { return $this->registrarConsumoHabitacion($bind, 'ROOM_SERVICE'); }

    private function registrarConsumoHabitacion($bind, $tipo)
    {
        try {
            $idReserva = (int)($bind['id_reservation'] ?? 0);
            $observaciones = trim($bind['observaciones'] ?? $bind['notes'] ?? '');
            if ($idReserva <= 0) throw new RuntimeException('Selecciona una habitación válida.');

            $this->pdo->beginTransaction();
            $reserva = $this->pdo->fetchOne(
                "SELECT r.id_reservation, rm.room_number,
                        TRIM(CONCAT(COALESCE(g.first_names, ''), ' ', COALESCE(g.last_names, ''))) AS customer_name
                 FROM reservation r
                 INNER JOIN room rm ON rm.id_room = r.id_room
                 LEFT JOIN guest g ON g.id_guest = r.id_guest
                 WHERE r.id_reservation = :id AND LOWER(TRIM(r.status)) = 'ocupado' LIMIT 1",
                ['id' => $idReserva]
            );
            if (!$reserva) throw new RuntimeException('La reserva no existe o la habitación no está ocupada.');

            $prepared = $this->prepararDetalles($bind['items'] ?? []);
            $details = $prepared['details'];
            $total = $prepared['total'];
            $notes = $tipo . ' | Reserva: ' . $idReserva . ' | Habitación: ' . $reserva['room_number'];
            if ($observaciones !== '') $notes .= ' | Observaciones: ' . $observaciones;

            $stmt = $this->pdo->prepare(
                "INSERT INTO hs_orders (order_type, id_reservation, room_number, customer_name, table_number, status, notes, total, created_by)
                 VALUES (:type, :reservation, :room, :customer, NULL, 'PENDIENTE', :notes, :total, :created_by)"
            );
            $stmt->execute([
                'type' => $tipo, 'reservation' => $idReserva, 'room' => $reserva['room_number'],
                'customer' => trim($reserva['customer_name']) ?: null, 'notes' => $notes,
                'total' => number_format($total, 2, '.', ''),
                'created_by' => !empty($bind['created_by']) ? (int)$bind['created_by'] : null
            ]);
            $orderId = (int)$this->pdo->lastInsertId();
            if ($orderId <= 0) throw new RuntimeException('No se pudo crear el consumo.');
            $this->insertarDetalles($orderId, $details);
            $this->descontarStock($details);
            $this->pdo->commit();

            return ['status' => 'OK', 'result' => [
                'id' => $orderId, 'id_reservation' => $idReserva, 'room_number' => $reserva['room_number'],
                'customer_name' => trim($reserva['customer_name']), 'total' => number_format($total, 2, '.', '')
            ]];
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) $this->pdo->rollBack();
            return $this->error($e);
        }
    }

    public function get_historial_ventas()
    {
        try {
            $sql = "SELECT o.id, o.order_type, o.id_reservation, o.room_number, o.customer_name, o.status, o.notes, o.total,
                           DATE_FORMAT(o.created_at, '%Y-%m-%d %H:%i') AS created_at,
                           COALESCE(d.cantidad_productos, 0) AS cantidad_productos
                    FROM hs_orders o
                    LEFT JOIN (SELECT order_id, SUM(quantity) AS cantidad_productos FROM hs_order_items GROUP BY order_id) d
                      ON d.order_id = o.id
                    WHERE o.order_type IN ('POS', 'MINIBAR', 'ROOM_SERVICE')
                    ORDER BY o.id DESC";
            return ['status' => 'OK', 'result' => $this->pdo->fetchAll($sql) ?: []];
        } catch (Throwable $e) { return $this->error($e); }
    }

    public function get_anulaciones()
    {
        try {
            $sql = "SELECT o.id, o.order_type, o.id_reservation, o.room_number, o.customer_name, o.status, o.notes, o.total,
                           DATE_FORMAT(o.created_at, '%Y-%m-%d %H:%i') AS created_at
                    FROM hs_orders o WHERE o.status = 'ANULADO' ORDER BY o.id DESC";
            return ['status' => 'OK', 'result' => $this->pdo->fetchAll($sql) ?: []];
        } catch (Throwable $e) { return $this->error($e); }
    }

    public function anular_venta($bind)
    {
        try {
            $id = (int)($bind['id'] ?? 0);
            $motivo = trim($bind['motivo'] ?? '');
            if ($id <= 0) return ['status' => 'ERROR', 'result' => 'Número de venta inválido.'];
            if ($motivo === '') return ['status' => 'ERROR', 'result' => 'Escribe el motivo de anulación.'];

            $this->pdo->beginTransaction();
            $venta = $this->pdo->fetchOne("SELECT id, status, notes FROM hs_orders WHERE id = :id LIMIT 1", ['id' => $id]);
            if (!$venta) throw new RuntimeException('No se encontró la venta.');
            if ($venta['status'] === 'ANULADO') throw new RuntimeException('Esta venta ya está anulada.');

            $notes = trim($venta['notes'] ?? '');
            $notes .= ($notes !== '' ? ' | ' : '') . 'Motivo de anulación: ' . $motivo;

            $stmt = $this->pdo->prepare("UPDATE hs_orders SET status = 'ANULADO', notes = :notes WHERE id = :id AND status <> 'ANULADO'");
            $stmt->execute(['notes' => $notes, 'id' => $id]);

            // Registra la anulación en la tabla existente. No se borran las ventas.
            $audit = $this->pdo->prepare("INSERT INTO hs_cancellations (order_id, reason, created_by) VALUES (:order_id, :reason, :created_by)");
            $audit->execute([
                'order_id' => $id, 'reason' => $motivo,
                'created_by' => !empty($bind['created_by']) ? (int)$bind['created_by'] : null
            ]);

            $this->pdo->commit();
            return ['status' => 'OK', 'result' => ['id' => $id, 'status' => 'ANULADO']];
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) $this->pdo->rollBack();
            return $this->error($e);
        }
    }
}

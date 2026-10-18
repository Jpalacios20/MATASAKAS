<?php
class M_Restobar extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_bebidas()
    {
        try {
            $sql = "SELECT
                        p.id_product,
                        p.product_sku,
                        p.product_name,
                        c.description AS category,
                        COALESCE(ut.description, '') AS unit,
                        p.price_sell,
                        p.product_price,
                        p.product_stock,
                        p.expiration_date,
                        p.status
                    FROM product p
                    INNER JOIN categories c ON c.id = p.id_category
                    LEFT JOIN unit_type ut ON ut.id = p.unit_type
                    WHERE p.status = 1
                      AND UPPER(c.description) IN ('BEBIDAS','AGUA','LICORES','GASEOSAS')
                    ORDER BY c.description, p.product_name";
            $rows = $this->pdo->fetchAll($sql);
            return $rows ? ['status'=>'OK','result'=>$rows] : ['status'=>'ERROR','result'=>'No hay bebidas registradas.'];
        } catch (PDOException $e) {
            return ['status'=>'EXCEPTION','result'=>$e];
        }
    }

    public function get_pedidos()
    {
        try {
            $sql = "SELECT id, order_type, customer_name, table_number, status, notes, total,
                           DATE_FORMAT(created_at,'%Y-%m-%d %H:%i') AS created_at
                    FROM hs_orders
                    WHERE order_type = 'RESTOBAR'
                    ORDER BY id DESC";
            $rows = $this->pdo->fetchAll($sql);
            return $rows ? ['status'=>'OK','result'=>$rows] : ['status'=>'ERROR','result'=>'No hay pedidos de Restobar.'];
        } catch (PDOException $e) {
            return ['status'=>'EXCEPTION','result'=>$e];
        }
    }

    public function get_pedido_detalle($bind)
    {
        try {
            $sql = "SELECT id, order_type, customer_name, table_number, status, notes, total,
                           DATE_FORMAT(created_at,'%Y-%m-%d %H:%i') AS created_at
                    FROM hs_orders WHERE id = :id AND order_type = 'RESTOBAR'";
            $order = $this->pdo->fetchOne($sql, ['id'=>$bind['id']]);
            if (!$order) return ['status'=>'ERROR','result'=>'Pedido no encontrado.'];
            $items = $this->pdo->fetchAll("SELECT id, kind, source_id, sku, name, quantity, unit_price, total
                                           FROM hs_order_items WHERE order_id = :id ORDER BY id", ['id'=>$bind['id']]);
            $order['items'] = $items ?: [];
            return ['status'=>'OK','result'=>$order];
        } catch (PDOException $e) {
            return ['status'=>'EXCEPTION','result'=>$e];
        }
    }

    public function crear_pedido($bind)
    {
        try {
            $this->pdo->beginTransaction();
            $total = 0;
            $itemsPrepared = [];
            foreach ($bind['items'] as $item) {
                $idProduct = (int)($item['id_product'] ?? $item['id'] ?? 0);
                $qty = max(1, (int)($item['quantity'] ?? 1));
                if (!$idProduct) continue;
                $product = $this->pdo->fetchOne("SELECT p.id_product, p.product_sku, p.product_name,
                                                       p.price_sell, p.product_price, c.description AS category
                                                FROM product p
                                                INNER JOIN categories c ON c.id = p.id_category
                                                WHERE p.id_product = :id AND p.status = 1
                                                  AND UPPER(c.description) IN ('BEBIDAS','AGUA','LICORES','GASEOSAS')",
                                                ['id'=>$idProduct]);
                if (!$product) continue;
                $price = (float)$product['price_sell'];
                if ($price <= 0) $price = (float)$product['product_price'];
                $line = $price * $qty;
                $total += $line;
                $itemsPrepared[] = [$product, $qty, $price, $line];
            }
            if (!$itemsPrepared) throw new RuntimeException('No se encontraron bebidas válidas.');

            $stmt = $this->pdo->prepare("INSERT INTO hs_orders
                (order_type, customer_name, table_number, status, notes, total, created_by)
                VALUES ('RESTOBAR', :customer_name, :table_number, 'PENDIENTE', :notes, :total, :created_by)");
            $stmt->execute([
                'customer_name'=>$bind['customer_name'] ?: null,
                'table_number'=>$bind['table_number'] ?: null,
                'notes'=>$bind['notes'] ?: null,
                'total'=>number_format($total,2,'.',''),
                'created_by'=>$bind['created_by'] ?: null
            ]);
            $orderId = $this->pdo->lastInsertId();

            foreach ($itemsPrepared as [$product,$qty,$price,$line]) {
                $stmt = $this->pdo->prepare("INSERT INTO hs_order_items
                    (order_id, kind, source_id, sku, name, quantity, unit_price, total)
                    VALUES (:order_id,'PRODUCTO',:source_id,:sku,:name,:quantity,:unit_price,:total)");
                $stmt->execute([
                    'order_id'=>$orderId,
                    'source_id'=>$product['id_product'],
                    'sku'=>$product['product_sku'],
                    'name'=>$product['product_name'],
                    'quantity'=>$qty,
                    'unit_price'=>number_format($price,2,'.',''),
                    'total'=>number_format($line,2,'.','')
                ]);
            }
            $this->pdo->commit();
            return ['status'=>'OK','result'=>['id'=>$orderId,'total'=>$total]];
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) $this->pdo->rollBack();
            return ['status'=>'EXCEPTION','result'=>$e];
        }
    }

    public function cambiar_estado($bind)
    {
        try {
            $stmt = $this->pdo->prepare("UPDATE hs_orders SET status = :status WHERE id = :id AND order_type = 'RESTOBAR'");
            $stmt->execute(['status'=>$bind['status'],'id'=>$bind['id']]);
            return $stmt->rowCount() >= 0 ? ['status'=>'OK','result'=>[]] : ['status'=>'ERROR','result'=>'No se actualizó el pedido.'];
        } catch (PDOException $e) {
            return ['status'=>'EXCEPTION','result'=>$e];
        }
    }

    public function get_ventas()
    {
        try {
            $sql = "SELECT id, customer_name, table_number, status, total,
                           DATE_FORMAT(updated_at,'%Y-%m-%d %H:%i') AS paid_at,
                           DATE_FORMAT(created_at,'%Y-%m-%d %H:%i') AS created_at
                    FROM hs_orders
                    WHERE order_type = 'RESTOBAR' AND status = 'PAGADO'
                    ORDER BY id DESC";
            $rows = $this->pdo->fetchAll($sql);
            return $rows ? ['status'=>'OK','result'=>$rows] : ['status'=>'ERROR','result'=>'No hay ventas cobradas.'];
        } catch (PDOException $e) {
            return ['status'=>'EXCEPTION','result'=>$e];
        }
    }
}

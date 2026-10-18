<?php 
// --
class M_Income_Products_Pending extends Model {

  // --
    public function __construct() {
    parent::__construct();
    }

public function get_income_products_pending() {
    try {
        $sql = 'SELECT 
                f.id AS id_income_products,
                c.name ,
                f.sale_date,
                f.series,
                f.number_serial,
                f.expiration_date,
                vt.description AS voucher_type_description,
                pt.description AS payment_type_description,
                pm.description AS payment_shape_description,
                SUM(d.subtotal) AS total_purchase,  -- Sumar el subtotal
                f.status
                FROM 
                    income_products f
                LEFT JOIN 
                    person c ON f.id_person = c.id  
                LEFT JOIN 
                    voucher_type vt ON f.id_voucher_type = vt.id  
                LEFT JOIN 
                    payment_type pt ON f.id_payment_type = pt.id
                LEFT JOIN 
                    payment_shape pm ON f.id_payment_shape = pm.id
                LEFT JOIN 
                    income_products_details d ON f.id = d.id_income_products  
                WHERE 
                    f.status = 1  -- Filtrar solo Pendiente (1) y Aceptado (2)
                GROUP BY 
                    f.id, c.name, f.sale_date, f.series, f.number_serial, f.expiration_date,
                    vt.description, pt.description, pm.description, f.status;';
        $stmt = $this->pdo->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return ['status' => 'OK', 'result' => $result]; 
    } catch (PDOException $e) {
        return ['status' => 'EXCEPTION', 'result' => $e->getMessage()];
    }
}

public function update_income_products_status($ids) {
    try {
        $placeholders = implode(",", array_fill(0, count($ids), "?")); // Crear placeholders (?, ?, ?)
        $sql = "UPDATE income_products SET status = 2 WHERE id IN ($placeholders)";
        $stmt = $this->pdo->prepare($sql);

        if ($stmt->execute($ids)) {
            return ["status" => "OK", "result" => "Registros actualizados correctamente"];
        } else {
            return ["status" => "ERROR", "result" => "No se pudo actualizar los registros"];
        }
    } catch (PDOException $e) {
        return ["status" => "EXCEPTION", "result" => $e->getMessage()];
    }
}

}
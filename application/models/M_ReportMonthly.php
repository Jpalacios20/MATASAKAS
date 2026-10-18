<?php

class M_ReportMonthly extends Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_report_data() {
        try {
            $sql1 = 'SELECT 
                f.id AS id_income_products,
                c.name AS name,
                c.document_number,
                f.sale_date AS date,
                vt.description AS voucher_type_description,
                f.number_serial,
                SUM(d.subtotal) AS total_purchase,
                f.status,
                "Venta" AS origin
            FROM 
                income_products f
            LEFT JOIN 
                person c ON f.id_person = c.id
            LEFT JOIN 
                voucher_type vt ON f.id_voucher_type = vt.id
            LEFT JOIN 
                income_products_details d ON f.id = d.id_income_products
            GROUP BY 
                f.id, c.name, c.document_number, f.sale_date, vt.description, f.number_serial, f.status';

            $sql2 = 'SELECT 
                r.id_reservation AS id_income_products,
                g.first_names AS name,
                g.document_number,
                r.checkin_date AS date,
                "Reservación" AS voucher_type_description,
                "" AS number_serial,
                rt.price_temporary AS total_purchase,
                r.status,
                "Reserva" AS origin
            FROM 
                reservation r
            LEFT JOIN 
                guest g ON r.id_guest = g.id_guest
            LEFT JOIN 
                room rm ON r.id_room = rm.id_room
            LEFT JOIN 
                room_type rt ON rm.id_type = rt.id_type';

            // Combinar resultados
            $sql = "($sql1) UNION ($sql2) ORDER BY date DESC";

            $stmt = $this->pdo->query($sql);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return ['status' => 'OK', 'result' => $result];
        } catch (PDOException $e) {
            return ['status' => 'ERROR', 'result' => $e->getMessage()];
        }
    }

    
}
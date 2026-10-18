<?php

class M_Report_Billing extends Model {

public function __construct() {
    parent::__construct();
}

public function get_report_data(){
   try{ $sql= 'SELECT 
            bp.id AS id_billingpersale,
            bp.issue_date,
            c.name AS clients,
            vt.description AS voucher_type,
            bp.series,
            bp.correlative,
            bp.leyend,
            bp.total_amount,
            bp.response,
            bp.status
            FROM Billingpersale bp
            INNER JOIN person c ON c.id = bp.clients_id
            INNER JOIN voucher_type vt ON vt.id = bp.voucher_type';

            $stmt = $this->pdo->query($sql);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return ['status' => 'OK', 'result' => $result];
            } catch (PDOException $e) {
            return ['status' => 'ERROR', 'result' => $e->getMessage()];
            }
}
}

<?php

class M_Customer_Report extends Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_report_data($filters = array()) {
        try {
            $sql = "SELECT 
                    c.id,
                    c.name,
                    c.document_number,
                    c.nationality,
                    c.birth_date,
                    c.birth_place,
                    c.address,
                    c.phone,
                    c.email,
                    c.business_name,
                    c.status,
                    dt.description as document_type
                FROM person c
                LEFT JOIN document_type dt ON dt.id = c.id_document_type
                WHERE 1=1";

            $params = array();

            if (!empty($filters['document_number'])) {
                $sql .= " AND c.document_number LIKE ?";
                $params[] = '%' . $filters['document_number'] . '%';
            }

            if (!empty($filters['name'])) {
                $sql .= " AND c.name LIKE ?";
                $params[] = '%' . $filters['name'] . '%';
            }

            if (isset($filters['status'])) {
                $sql .= " AND c.status = ?";
                $params[] = $filters['status'];
            }

            $sql .= " ORDER BY c.name ASC";

            $result = $this->pdo->fetchAll($sql, $params);

            return array(
                'status' => ($result ? 'OK' : 'ERROR'),
                'result' => $result
            );

        } catch (PDOException $e) {
            return array(
                'status' => 'EXCEPTION',
                'result' => $e->getMessage()
            );
        }
    }
}
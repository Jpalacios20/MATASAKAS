<?php  
class M_Income_Accessory extends Model {

    public function __construct() {
        parent::__construct();
    }

    // LISTADO
    public function get_income_accessory() {
        try {
            $sql = "SELECT 
                f.id AS id_income_accessory,
                c.name,
                f.voucher_series,
                f.number_serial,
                DATE_FORMAT(f.data_time, '%Y-%m-%d') AS data_time,
                f.id_campus,
                CONCAT(u.first_name, ' ', u.last_name) AS user_name,
                vt.description AS voucher_type_description,
                pt.description AS payment_type_description,
                pm.description AS payment_shape_description,
                f.purchase_total AS total_purchase,
                f.status
            FROM income_accessory f
            LEFT JOIN person c ON f.id_person = c.id
            LEFT JOIN `user` u ON f.id_user = u.id
            LEFT JOIN voucher_type vt ON f.id_voucher_type = vt.id
            LEFT JOIN payment_type pt ON f.id_payment_type = pt.id
            LEFT JOIN payment_shape pm ON f.id_payment_shape = pm.id
            ORDER BY f.data_time DESC";

            $stmt = $this->pdo->query($sql);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($result) {
                return ['status' => 'OK', 'result' => $result];
            } else {
                return ['status' => 'ERROR', 'result' => []];
            }
        } catch (PDOException $e) {
            return ['status' => 'EXCEPTION', 'result' => $e->getMessage()];
        }
    }

    // CREAR INGRESO
    public function create_income_accessory($bind) {
        try {
            $sql = "INSERT INTO income_accessory (
                id_person, id_user, id_voucher_type, voucher_series, number_serial, 
                id_payment_type, id_payment_shape, tax, purchase_total, id_campus, status, data_time
            ) VALUES (
                :id_person, :id_user, :id_voucher_type, :voucher_series, :number_serial,
                :id_payment_type, :id_payment_shape, :tax, :purchase_total, :id_campus, :status, :data_time
            )";

            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute($bind);

            if ($result) {
                return $this->pdo->lastInsertId();
            } else {
                return false;
            }
        } catch (PDOException $e) {
            return false;
        }
    }

    // INSERTAR DETALLE
    public function insertIncomeAccessoryDetails($accesorio) {
        try {
            $sql = "INSERT INTO income_accessory_details (
                        id_income_accessory, id_accessory, quantity, full_purchase, 
                        selling_price, subtotal
                    ) VALUES (
                        :id_income_accessory, :id_accessory, :quantity, :full_purchase,
                        :selling_price, :subtotal
                    )";

            $stmt = $this->pdo->prepare($sql);

            $full_purchase_val = $accesorio['full_purchase'] ?? $accesorio['subtotal'] ?? 0;
            $selling_price = $accesorio['selling_price'] ?? null;
            $subtotal_val = $accesorio['subtotal'] ?? 0;

            $stmt->bindParam(':id_income_accessory', $accesorio['id_income_accessory'], PDO::PARAM_INT);
            $stmt->bindParam(':id_accessory', $accesorio['id_accessory'], PDO::PARAM_INT);
            $stmt->bindParam(':quantity', $accesorio['quantity'], PDO::PARAM_INT);
            $stmt->bindParam(':full_purchase', $full_purchase_val, PDO::PARAM_STR);
            $stmt->bindParam(':selling_price', $selling_price, PDO::PARAM_STR);
            $stmt->bindParam(':subtotal', $subtotal_val, PDO::PARAM_STR);

            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            return "ERROR SQL: " . $e->getMessage();
        }
    }

    // DETALLE MODAL VER
    public function get_income_accessory_details($id_income_accessory) {
        try {
            $sql = 'SELECT 
                f.id AS id_income_accessory,
                c.name AS client_name,
                DATE_FORMAT(f.data_time, "%Y-%m-%d") AS sale_date,
                f.voucher_series AS series,
                f.number_serial,
                f.id_campus,
                vt.description AS voucher_type_description,
                pt.description AS payment_type_description,
                pm.description AS payment_shape_description,
                d.id AS income_accessory_details_id, 
                a.id_accessory, 
                a.accessory_description AS accessory_name,
                d.quantity,
                d.full_purchase,
                d.selling_price,
                d.subtotal,
                f.status
            FROM income_accessory f
            INNER JOIN person c ON f.id_person = c.id  
            INNER JOIN voucher_type vt ON f.id_voucher_type = vt.id  
            INNER JOIN payment_type pt ON f.id_payment_type = pt.id
            INNER JOIN payment_shape pm ON f.id_payment_shape = pm.id
            INNER JOIN income_accessory_details d ON f.id = d.id_income_accessory
            INNER JOIN accessory a ON d.id_accessory = a.id_accessory
            WHERE f.id = :id_income_accessory';

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id_income_accessory', $id_income_accessory, PDO::PARAM_INT);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!$rows) {
                return ['status' => 'NOT_FOUND', 'message' => 'No se encontraron detalles para este ingreso.'];
            }

            $incomeAccessory = [
                'id_income_accessory' => $rows[0]['id_income_accessory'],
                'client_name'         => $rows[0]['client_name'],
                'sale_date'           => $rows[0]['sale_date'],
                'series'              => $rows[0]['series'],
                'number_serial'       => $rows[0]['number_serial'],
                'voucher_type'        => $rows[0]['voucher_type_description'],
                'payment_type'        => $rows[0]['payment_type_description'],
                'payment_shape'       => $rows[0]['payment_shape_description'],
                'status'              => $rows[0]['status'],
                'accesorios'          => []
            ];

            foreach ($rows as $row) {
                $incomeAccessory['accesorios'][] = [
                    'id_accessory'   => $row['id_accessory'],
                    'accessory_name' => $row['accessory_name'],
                    'quantity'       => $row['quantity'],
                    'full_purchase'  => $row['full_purchase'],
                    'selling_price'  => $row['selling_price'],
                    'subtotal'       => $row['subtotal']
                ];
            }

            return ['status' => 'OK', 'result' => $incomeAccessory];
        } catch (PDOException $e) {
            return ['status' => 'EXCEPTION', 'message' => 'Error en la base de datos: ' . $e->getMessage()];
        }
    }

    // ELIMINAR
    public function delete_income_accessory($bind) {
        try {
            $this->pdo->beginTransaction();

            $sqlDetails = 'DELETE FROM income_accessory_details WHERE id_income_accessory = :id_income_accessory';
            $stmtDetails = $this->pdo->prepare($sqlDetails);
            $stmtDetails->execute($bind);
            $resultDetails = $stmtDetails->rowCount();

            $sqlIncome = 'DELETE FROM income_accessory WHERE id = :id_income_accessory';
            $stmtIncome = $this->pdo->prepare($sqlIncome);
            $stmtIncome->execute($bind);
            $resultIncome = $stmtIncome->rowCount();

            if ($resultDetails > 0 || $resultIncome > 0) {
                $this->pdo->commit();
                return ['status' => 'OK', 'result' => []];
            } else {
                $this->pdo->rollBack();
                return ['status' => 'ERROR', 'result' => 'No se eliminó ningún registro.'];
            }
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            return ['status' => 'EXCEPTION', 'result' => $e->getMessage()];
        }
    }
}


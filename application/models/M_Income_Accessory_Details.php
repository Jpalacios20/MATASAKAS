<?php 
class M_Income_Accessory_Details extends Model {
    
    public function __construct() { parent::__construct(); }

    // ... (Tus funciones get_clients_list, etc. si las tenías aquí, agrégalas si faltan) ...

    // --- 1. TRANSACCIÓN DE CREACIÓN (LA QUE FALTABA) ---
    public function create_income_transaction($data) {
        try {
            $this->pdo->beginTransaction();

            // A. Insertar Maestro
            $sql_master = "INSERT INTO income_accessory (
                id_client, id_user, id_voucher_type, id_payment_type, 
                proof_series, voucher_series, date, full_purchase, status, igv
            ) VALUES (
                :idc, :idu, :ivt, :ipt,
                :ps, :vs, :d, :fp, 1, 0.00
            )";

            // Calcular total
            $total = 0;
            foreach ($data['details'] as $d) {
                $total += (floatval($d['stock']) * floatval($d['purchase_price']));
            }

            $stmt_master = $this->pdo->prepare($sql_master);
            $stmt_master->execute([
                'idc' => $data['master']['id_client'],
                'idu' => $data['master']['id_user'],
                'ivt' => $data['master']['id_voucher_type'],
                'ipt' => $data['master']['id_payment_type'],
                'ps'  => $data['master']['proof_series'],
                'vs'  => $data['master']['voucher_series'],
                'd'   => $data['master']['date'],
                'fp'  => $total
            ]);
            
            $id_income = $this->pdo->lastInsertId();

            // B. Insertar Detalles y Actualizar Stock
            $sql_ins = "INSERT INTO income_accessory_details (id_income_accessory, id_accessory, serie, stock, purchase_price, sale_price, status) VALUES (:id_inc, :id_acc, :ser, :stk, :pp, :sp, 1)";
            $sql_upd_stock = "UPDATE accessory SET accessory_stock = accessory_stock + :qty WHERE id_accessory = :id";
            
            $stmt_ins = $this->pdo->prepare($sql_ins);
            $stmt_stock = $this->pdo->prepare($sql_upd_stock);

            foreach ($data['details'] as $item) {
                // Insertar detalle
                $stmt_ins->execute([
                    'id_inc' => $id_income,
                    'id_acc' => $item['id_accessory'],
                    'ser'    => $item['serie'],
                    'stk'    => $item['stock'],
                    'pp'     => $item['purchase_price'],
                    'sp'     => $item['sale_price']
                ]);
                
                // Sumar Stock
                $stmt_stock->execute(['qty' => $item['stock'], 'id' => $item['id_accessory']]);
            }

            $this->pdo->commit();
            return ['status' => 'OK', 'id' => $id_income, 'msg' => 'Ingreso registrado correctamente.'];

        } catch (Exception $e) {
            $this->pdo->rollBack();
            return ['status' => 'ERROR', 'msg' => 'Error DB: ' . $e->getMessage()];
        }
    }

    // --- 2. TRANSACCIÓN DE ACTUALIZACIÓN ---
    public function update_income_transaction($data) {
        try {
            $this->pdo->beginTransaction();

            $id_income = $data['master']['id_income_accessory'];

            // A. Revertir Stock Antiguo
            $old_details = $this->pdo->fetchAll("SELECT id_accessory, stock FROM income_accessory_details WHERE id_income_accessory = :id", ['id' => $id_income]);
            
            $sql_revert = "UPDATE accessory SET accessory_stock = accessory_stock - :qty WHERE id_accessory = :id";
            $stmt_revert = $this->pdo->prepare($sql_revert);
            
            foreach ($old_details as $item) {
                $stmt_revert->execute(['qty' => $item['stock'], 'id' => $item['id_accessory']]);
            }

            // B. Borrar Detalles Antiguos
            $sql_del = "DELETE FROM income_accessory_details WHERE id_income_accessory = :id";
            $stmt_del = $this->pdo->prepare($sql_del);
            $stmt_del->execute(['id' => $id_income]);

            // C. Actualizar Maestro
            $new_total = 0;
            foreach ($data['details'] as $d) {
                $new_total += (floatval($d['stock']) * floatval($d['purchase_price']));
            }

            $sql_update = "UPDATE income_accessory SET 
                           id_client = :idc, id_user = :idu, id_voucher_type = :ivt, id_payment_type = :ipt,
                           proof_series = :ps, voucher_series = :vs, date = :d, full_purchase = :fp
                           WHERE id = :id";
            
            $stmt_upd = $this->pdo->prepare($sql_update);
            $stmt_upd->execute([
                'idc' => $data['master']['id_client'],
                'idu' => $data['master']['id_user'],
                'ivt' => $data['master']['id_voucher_type'],
                'ipt' => $data['master']['id_payment_type'],
                'ps'  => $data['master']['proof_series'],
                'vs'  => $data['master']['voucher_series'],
                'd'   => $data['master']['date'],
                'fp'  => $new_total,
                'id'  => $id_income
            ]);

            // D. Insertar Nuevos Detalles y Sumar Nuevo Stock
            $sql_ins = "INSERT INTO income_accessory_details (id_income_accessory, id_accessory, serie, stock, purchase_price, sale_price, status) VALUES (:id_inc, :id_acc, :ser, :stk, :pp, :sp, 1)";
            $sql_upd_stock = "UPDATE accessory SET accessory_stock = accessory_stock + :qty WHERE id_accessory = :id";
            
            $stmt_ins = $this->pdo->prepare($sql_ins);
            $stmt_stock = $this->pdo->prepare($sql_upd_stock);

            foreach ($data['details'] as $item) {
                // Insertar
                $stmt_ins->execute([
                    'id_inc' => $id_income,
                    'id_acc' => $item['id_accessory'],
                    'ser'    => $item['serie'],
                    'stk'    => $item['stock'],
                    'pp'     => $item['purchase_price'],
                    'sp'     => $item['sale_price']
                ]);
                // Sumar Stock
                $stmt_stock->execute(['qty' => $item['stock'], 'id' => $item['id_accessory']]);
            }

            $this->pdo->commit();
            return ['status' => 'OK', 'msg' => 'Ingreso actualizado correctamente.'];

        } catch (Exception $e) {
            $this->pdo->rollBack();
            return ['status' => 'ERROR', 'msg' => 'Error DB: ' . $e->getMessage()];
        }
    }

    // --- 3. OBTENER DATOS COMPLETOS (PARA EDITAR) ---
    public function get_full_income_info($id) {
        // A. Datos del Maestro
        $sqlM = "SELECT * FROM income_accessory WHERE id = :id";
        $master = $this->pdo->fetchOne($sqlM, ['id' => $id]);

        // B. Datos de los Detalles
        $sqlD = "SELECT d.*, a.accessory_description 
                 FROM income_accessory_details d
                 JOIN accessory a ON d.id_accessory = a.id_accessory
                 WHERE d.id_income_accessory = :id";
        $details = $this->pdo->fetchAll($sqlD, ['id' => $id]);

        return ['master' => $master, 'details' => $details];
    }
}
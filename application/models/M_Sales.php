<?php
// --
class M_Sales extends Model
{
    // --
    public function __construct()
    {
        parent::__construct();
    }

    // ---------------------------------------------------------
    // OBTENER RESERVA PARA REALIZAR UNA VENTA
    // ---------------------------------------------------------
    public function get_reservation_by_id($bind)
    {
        try {

            $sql = 'SELECT 
                        r.*,
                        room.room_number,
                        room.id_type,
                        room_type.type_name,
                        guest.document_type,
                        guest.document_number,
                        guest.first_names,
                        guest.last_names,
                        guest.address,
                        guest.company_name
                    FROM reservation AS r
                    JOIN room 
                        ON r.id_room = room.id_room
                    JOIN guest 
                        ON r.id_guest = guest.id_guest
                    JOIN room_type 
                        ON room.id_type = room_type.id_type
                    WHERE r.id_reservation = :id_reservation';

            $result = $this->pdo->fetchAll($sql, $bind);

            if ($result) {

                $response = array(
                    'status' => 'OK',
                    'result' => $result
                );

            } else {

                $response = array(
                    'status' => 'ERROR',
                    'result' => array()
                );
            }

        } catch (PDOException $e) {

            $response = array(
                'status' => 'EXCEPTION',
                'result' => $e
            );
        }

        return $response;
    }


    // ---------------------------------------------------------
    // HISTORIAL DE VENTAS
    // ---------------------------------------------------------
    public function sales_product_history($bind)
    {
        try {

            $sql = 'SELECT
                        id_reservation,
                        fecha_venta,
                        product_name,
                        cantidad,
                        product_price,
                        total_price
                    FROM payment_sales_details
                    WHERE id_reservation = :id_reservation';

            $result = $this->pdo->fetchAll($sql, $bind);

            if ($result) {

                $response = array(
                    'status' => 'OK',
                    'result' => $result
                );

            } else {

                $response = array(
                    'status' => 'ERROR',
                    'result' => array()
                );
            }

        } catch (PDOException $e) {

            $response = array(
                'status' => 'EXCEPTION',
                'result' => $e
            );
        }

        return $response;
    }


    // ---------------------------------------------------------
    // ACTUALIZAR TOTAL DE VENTAS
    // ---------------------------------------------------------
    public function update_payment_total($bind)
    {
        try {

            $sql = 'UPDATE payment
                    SET payment_sales = :payment_sales
                    WHERE id_reservation = :id_reservation';

            $result = $this->pdo->prepare($sql);

            $execute = $result->execute([
                ':payment_sales' => $bind['payment_sales'],
                ':id_reservation' => $bind['id_reservation']
            ]);

            if ($execute) {

                return array(
                    'status' => 'OK',
                    'result' => $result->rowCount()
                );

            } else {

                return array(
                    'status' => 'ERROR',
                    'result' => $result->errorInfo()
                );
            }

        } catch (PDOException $e) {

            return array(
                'status' => 'EXCEPTION',
                'result' => $e->getMessage()
            );
        }
    }


    // ---------------------------------------------------------
    // NO USAR PARA ELIMINAR VENTAS
    // ---------------------------------------------------------
    public function delete_reservation($id_reservation)
    {
        try {

            $sqlPayment = 'DELETE FROM payment
                           WHERE id_reservation = :id_reservation';

            $stmtPayment = $this->pdo->prepare($sqlPayment);

            $stmtPayment->bindParam(
                ':id_reservation',
                $id_reservation,
                PDO::PARAM_INT
            );

            $stmtPayment->execute();


            $sqlReservation = 'DELETE FROM reservation
                               WHERE id_reservation = :id_reservation';

            $stmtReservation = $this->pdo->prepare($sqlReservation);

            $stmtReservation->bindParam(
                ':id_reservation',
                $id_reservation,
                PDO::PARAM_INT
            );

            $stmtReservation->execute();


            if ($stmtReservation->rowCount() > 0) {

                return [
                    'status' => 'OK',
                    'result' => $stmtReservation->rowCount()
                ];

            } else {

                return [
                    'status' => 'ERROR',
                    'result' => 'No se encontró la reservación.'
                ];
            }

        } catch (PDOException $e) {

            return [
                'status' => 'EXCEPTION',
                'result' => $e->getMessage()
            ];
        }
    }
}
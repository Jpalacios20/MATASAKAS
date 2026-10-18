<?php
// --
class M_Notifications extends Model
{
    // --
    public function __construct()
    {
        parent::__construct();
    }
    public function get_reservations()
    {
        // --
        try {
            // --
            $sql = 'SELECT 
                            r.id_reservation,
                            r.checkin_date,
                            r.checkin_time,
                            r.checkout_date,
                            r.checkout_time,
                            r.departure_date,
                            r.departure_time,
                            r.status,
                            ro.room_number,
                            ro.room_status,
                            rt.type_name,
                            rt.bed_type,
                            g.document_type,
                            g.document_number,
                            g.first_names,
                            g.last_names,
                            g.company_name
                        FROM reservation r
                        JOIN room ro ON ro.id_room = r.id_room
                        JOIN room_type rt ON rt.id_type = ro.id_type
                        JOIN guest g ON g.id_guest = r.id_guest
                        WHERE r.status IN ("Reservado", "Ocupado");
                ';
            // --
            $result = $this->pdo->fetchAll($sql);
            // --
            if ($result) {
                // --
                $response = array('status' => 'OK', 'result' => $result);
            } else {
                // --
                $response = array('status' => 'ERROR', 'result' => array());
            }
        } catch (PDOException $e) {
            // --
            $response = array('status' => 'EXCEPTION', 'result' => $e);
        }
        // --
        return $response;
    }


    public function get_cleaning()
    {
        // --
        try {
            // --
            $sql = 'WITH cte 
                        AS (   
                            SELECT r.*, ro.room_number, ROW_NUMBER() OVER (PARTITION BY r.id_room ORDER BY r.departure_date DESC) 
                            AS rn   FROM reservation r   
                            JOIN room ro ON ro.id_room = r.id_room   
                            WHERE r.status = "Finalizado" AND ro.room_status = "Limpieza" 
                        ) 
                        SELECT * FROM cte WHERE rn = 1;
                ';
            // --
            $result = $this->pdo->fetchAll($sql);
            // --
            if ($result) {
                // --
                $response = array('status' => 'OK', 'result' => $result);
            } else {
                // --
                $response = array('status' => 'ERROR', 'result' => array());
            }
        } catch (PDOException $e) {
            // --
            $response = array('status' => 'EXCEPTION', 'result' => $e);
        }
        // --
        return $response;
    }

    public function get_notifications($bind)
    {
        // --
        try {
            // --
            $sql = 'SELECT 
                            n.*,
                            r.*,
                            ro.room_number,
                            g.document_number,
                            g.first_names,
                            g.last_names
                        FROM notification n
                        INNER JOIN reservation r ON n.id_reservation = r.id_reservation
                        INNER JOIN room ro ON ro.id_room = r.id_room
                        INNER JOIN guest g ON g.id_guest = r.id_guest
                        ORDER BY n.date_notification DESC, n.time_notification DESC
                        LIMIT 10 OFFSET :continue;
                        ';
            // --
            $result = $this->pdo->fetchAll($sql, $bind);
            // --
            if ($result) {
                // --
                $response = array('status' => 'OK', 'result' => $result);
            } else {
                // --
                $response = array('status' => 'ERROR', 'result' => array());
            }
        } catch (PDOException $e) {
            // --
            $response = array('status' => 'EXCEPTION', 'result' => $e);
        }
        // --
        return $response;
    }

    // public function get_notifications()
    // {
    //     // --
    //     try {
    //         // --
    //         $sql = 'SELECT  
    //                         n.*,
    //                         r.*,
    //                         ro.room_number,
    //                         g.document_number,
    //                         g.first_names,
    //                         g.last_names
    //                     FROM notification n
    //                     JOIN reservation r ON n.id_reservation = r.id_reservation
    //                     JOIN room ro ON ro.id_room = r.id_room
    //                     JOIN guest g ON g.id_guest = r.id_guest
    //                     ORDER BY n.date_notification DESC, n.time_notification DESC;
    //                     ';
    //         // --
    //         $result = $this->pdo->fetchAll($sql);
    //         // --
    //         if ($result) {
    //             // --
    //             $response = array('status' => 'OK', 'result' => $result);
    //         } else {
    //             // --
    //             $response = array('status' => 'ERROR', 'result' => array());
    //         }
    //     } catch (PDOException $e) {
    //         // --
    //         $response = array('status' => 'EXCEPTION', 'result' => $e);
    //     }
    //     // --
    //     return $response;
    // }

    public function create_notifications($bind)
    {
        // --
        try {
            // --
            $sql = 'INSERT INTO notification (date_notification, time_notification, type, id_reservation, status_notification, sku_notification)
                        SELECT :date_notification, :time_notification, :type, :id_reservation, :status_notification, :sku_notification
                        FROM dual
                        WHERE NOT EXISTS (
                            SELECT *
                            FROM notification
                            WHERE sku_notification = :sku_notification
                        );
            ';
            // --
            $result = $this->pdo->perform($sql, $bind);

            // --
            if ($result->rowCount() > 0) {
                // --
                $response = array('status' => 'OK', 'result' => array());
            } else {
                // --
                $response = array('status' => 'ERROR', 'result' => array());
            }
        } catch (PDOException $e) {
            // --
            $response = array('status' => 'EXCEPTION', 'result' => $e);
        }
        // --
        return $response;
    }

    public function update_notification()
    {
        // --
        try {
            // --
            $sql = 'UPDATE notification SET status_notification = "Seen" WHERE status_notification != "Seen" ';
            // --
            $result = $this->pdo->perform($sql);

            // --
            if ($result) {
                // --
                $response = array('status' => 'OK', 'result' => array());
            } else {
                // --
                $response = array('status' => 'ERROR', 'result' => array());
            }
        } catch (PDOException $e) {
            // --
            $response = array('status' => 'EXCEPTION', 'result' => $e);
        }
        // --
        return $response;
    }
}


<?php
// --
class M_Reservation extends Model
{
  // --
  public function __construct()
  {
    parent::__construct();
  }

  public function get_reservations()
  {
    try {
      $sql = 'SELECT 
          r.id_reservation,
          p.id_payment,
          r.checkin_date,
          r.checkin_time,
          r.checkout_date,
          r.checkout_time,
          r.status,
          ro.room_number,
          ro.room_status,
          rt.type_name,
          rt.person_limit,
          rt.bed_type,
          pe.id AS id_person,
          pe.name AS cliente,
          pe.document_number,
          pe.business_name,
          p.payment_total,
          p.payment_room
        FROM reservation r
        LEFT JOIN room ro ON ro.id_room = r.id_room
        LEFT JOIN room_type rt ON rt.id_type = ro.id_type
        LEFT JOIN person pe ON pe.id = r.id_person
        LEFT JOIN payment p ON r.id_reservation = p.id_reservation
        WHERE r.status IN ("Pendiente","Reservado", "Finalizado", "Ocupado", "Libre")';

      // --
      $result = $this->pdo->fetchAll($sql);

      // --
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

    // --
    return $response;
  }


  // ---------------------------------------------------------
  // OBTENER RESERVACIÓN
  // ---------------------------------------------------------

  public function get_reservation($bind)
  {
    try {

      $sql = 'SELECT 
                p.*,
                r.id_reservation,
                p.id_payment,
                r.checkin_date,
                r.checkin_time,
                r.checkout_date,
                r.checkout_time,
                r.id_room,
                r.id_person,
                r.status,
                r.departure_date,
                r.departure_time,
                ro.room_number,
                ro.room_status,
                rt.type_name,
                rt.person_limit,
                rt.price_temporary,
                rt.price_half,
                rt.price_day,
                rt.bed_type,
                pe.id AS id_person,
                pe.name AS cliente,
                pe.document_number,
                pe.address,
                pe.business_name
              FROM payment p
              JOIN reservation r 
                ON p.id_reservation = r.id_reservation
              JOIN room ro 
                ON ro.id_room = r.id_room
              JOIN room_type rt 
                ON rt.id_type = ro.id_type
              JOIN person pe 
                ON pe.id = r.id_person
              WHERE p.id_reservation = :id_reservation';

      // --
      $result = $this->pdo->fetchOne($sql, $bind);

      // --
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

    // --
    return $response;
  }


  // ---------------------------------------------------------
  // INSERTAR DETALLES DE VENTA DE PRODUCTOS
  // ---------------------------------------------------------

  public function product_sales_details($products)
  {
    try {

      $this->pdo->beginTransaction();

      $sql = 'INSERT INTO payment_sales_details
              (
                id_reservation,
                id_payment,
                id_product,
                fecha_venta,
                product_sku,
                product_name,
                cantidad,
                product_price,
                total_price
              )
              VALUES
              (
                :id_reservation,
                :id_payment,
                :id_product,
                :fecha_venta,
                :product_sku,
                :product_name,
                :cantidad,
                :product_price,
                :total_price
              )';

      $stmt = $this->pdo->prepare($sql);

      foreach ($products as $product) {

        $cantidad = (int)$product['cantidad'];
        $precio = (float)$product['product_price'];

        // Calcular el total de la línea
        $total_price = $cantidad * $precio;

        $stmt->bindValue(
          ':id_reservation',
          (int)$product['id_reservation'],
          PDO::PARAM_INT
        );

        $stmt->bindValue(
          ':id_payment',
          (int)$product['id_payment'],
          PDO::PARAM_INT
        );

        $stmt->bindValue(
          ':id_product',
          (int)$product['id_product'],
          PDO::PARAM_INT
        );

        $stmt->bindValue(
          ':fecha_venta',
          $product['fecha_venta']
        );

        $stmt->bindValue(
          ':product_sku',
          $product['product_sku']
        );

        $stmt->bindValue(
          ':product_name',
          $product['product_name']
        );

        $stmt->bindValue(
          ':cantidad',
          $cantidad,
          PDO::PARAM_INT
        );

        $stmt->bindValue(
          ':product_price',
          $precio
        );

        $stmt->bindValue(
          ':total_price',
          $total_price
        );

        $stmt->execute();
      }

      $this->pdo->commit();

      return array(
        'status' => 'OK',
        'result' => array()
      );

    } catch (PDOException $e) {

      if ($this->pdo->inTransaction()) {
        $this->pdo->rollBack();
      }

      return array(
        'status' => 'EXCEPTION',
        'result' => $e->getMessage()
      );
    }
  }


  // ---------------------------------------------------------
  // OBTENER VENTAS DE COMIDA
  // ---------------------------------------------------------

  public function get_sales_food($bind)
  {
    try {

      $sql = 'SELECT 
                    food.food_description, 
                    food.food_price, 
                    sales_food.amount_fd,
                    sales_food.price_sales_fd
                FROM sales_food
                INNER JOIN food 
                  ON sales_food.id_food = food.id_food
                WHERE sales_food.id_reservation = :id_reservation';

      // --
      $result = $this->pdo->fetchAll($sql, $bind);

      // --
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

    // --
    return $response;
  }


  // ---------------------------------------------------------
  // OBTENER VENTAS DE ACCESORIOS
  // ---------------------------------------------------------

  public function get_sales_accessory($bind)
  {
    try {

      $sql = 'SELECT 
                  accessory.accessory_description, 
                  accessory.accessory_price, 
                  sales_accessory.amount_ac,
                  sales_accessory.price_sales_ac
              FROM sales_accessory
              INNER JOIN accessory 
                ON sales_accessory.id_accessory = accessory.id_accessory
              WHERE sales_accessory.id_reservation = :id_reservation';

      // --
      $result = $this->pdo->fetchAll($sql, $bind);

      // --
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

    // --
    return $response;
  }


  // ---------------------------------------------------------
  // OBTENER TIEMPOS EXTRA
  // ---------------------------------------------------------

  public function get_payment_extra()
  {
    try {

      $sql = 'SELECT 
                  extra_time, 
                  price_extra
              FROM payment_extra';

      // --
      $result = $this->pdo->fetchAll($sql);

      // --
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

    // --
    return $response;
  }


  // ---------------------------------------------------------
  // ACTUALIZAR RESERVACIÓN
  // ---------------------------------------------------------

  public function update_reservation($bind)
  {
    try {

      $sql = 'UPDATE payment
              SET
                payment_room = :payment_room
              WHERE id_payment = :id_payment;

              UPDATE reservation 
              SET
                checkin_date = :checkin_date,
                checkin_time = :checkin_time,
                checkout_date = :checkout_date,
                checkout_time = :checkout_time,
                status = :status
              WHERE id_reservation = :id_reservation;

              UPDATE room
              SET
                room_status = :status
              WHERE id_room = :id_room';

      // --
      $result = $this->pdo->perform($sql, $bind);

      // --
      if ($result) {

        $response = array(
          'status' => 'OK',
          'result' => array()
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
  // ACTUALIZAR PAGO
  // ---------------------------------------------------------

  public function update_payment($bind)
  {
    try {

      $sql = 'UPDATE payment
              SET
                payment_extra = :payment_extra,
                payment_discount = :payment_discount,
                pre_payment = :pre_payment,
                payment_total = :payment_total
              WHERE id_payment = :id_payment;

              UPDATE reservation 
              SET
                departure_date = :departure_date,
                departure_time = :departure_time
              WHERE id_reservation = :id_reservation';

      // --
      $result = $this->pdo->perform($sql, $bind);

      // --
      if ($result) {

        $response = array(
          'status' => 'OK',
          'result' => array()
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
  // FINALIZAR RESERVACIÓN
  // ---------------------------------------------------------

  public function finish_reservation($bind)
  {
    try {

      $sql = 'UPDATE reservation 
              SET
                status = "Finalizado"
              WHERE id_reservation = :id_reservation;

              UPDATE room
              SET
                room_status = "Limpieza"
              WHERE id_room = :id_room';

      // --
      $result = $this->pdo->perform($sql, $bind);

      // --
      if ($result) {

        $response = array(
          'status' => 'OK',
          'result' => array()
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
  // VALIDAR FECHAS DE RESERVACIÓN
  // ---------------------------------------------------------

  public function date_reservation($bind)
  {
    try {

      $sql = 'SELECT 
                  r.id_reservation,
                  r.checkin_date,
                  r.checkin_time,
                  r.checkout_date,
                  r.checkout_time,
                  r.status,
                  r.id_room,
                  ro.room_number,
                  ro.room_status
              FROM reservation r
              JOIN room ro 
                ON ro.id_room = r.id_room
              WHERE r.status IN ("Reservado", "Ocupado")
                AND r.id_room = :id_room
                AND r.id_reservation != :id_reservation
                AND (
                  CONCAT(r.checkin_date, " ", r.checkin_time)
                  BETWEEN CONCAT(:checkin_date)
                  AND CONCAT(:checkout_date)

                  OR

                  CONCAT(r.checkout_date, " ", r.checkout_time)
                  BETWEEN CONCAT(:checkin_date)
                  AND CONCAT(:checkout_date)
                )';

      // --
      $result = $this->pdo->fetchAll($sql, $bind);

      // --
      if (count($result) == 0) {

        $response = array(
          'status' => 'OK',
          'result' => $result
        );

      } else {

        $response = array(
          'status' => 'ERROR',
          'result' => $result
        );
      }

    } catch (PDOException $e) {

      $response = array(
        'status' => 'EXCEPTION',
        'result' => $e
      );
    }

    // --
    return $response;
  }

}
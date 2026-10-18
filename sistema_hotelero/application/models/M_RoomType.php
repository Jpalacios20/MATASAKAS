<?php
// --
class M_Roomtype extends Model
{
  // --
  public function __construct()
  {
    parent::__construct();
  }

  // --
  public function get_room_types()
  {
    try {
      $sql = 'SELECT id_type, type_name, person_limit, price_temporary, price_half, price_day, bed_type, price_holiday FROM room_type';
      $result = $this->pdo->fetchAll($sql);
      

      if ($result) {
        $response = array('status' => 'OK', 'result' => $result);
      } else {
        $response = array('status' => 'ERROR', 'result' => array());
      }
    } catch (PDOException $e) {
      $response = array('status' => 'EXCEPTION', 'result' => $e);
    }
    return $response;
  }

  public function create_room_types($bind)
  {
    // --
    try {
      // --
      $sql = 'INSERT INTO room_type (type_name,person_limit,price_temporary,price_half,price_day, price_holiday, bed_type) VALUES (:type_name, :person_limit, :price_temporary,:price_half,:price_day,:price_holiday,:bed_type)';
      // --
      $result = $this->pdo->perform($sql, $bind);

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
  //
  
  public function get_room_type_by_id($bind)
  {
    try {
      $sql = 'SELECT id_type, type_name, person_limit, price_temporary, price_half, price_day, price_holiday, bed_type
              FROM room_type
              WHERE id_type = :id_type';

      $result = $this->pdo->fetchOne($sql, $bind);

      if ($result) {
        $response = array('status' => 'OK', 'result' => $result);
      } else {
        $response = array('status' => 'ERROR', 'result' => array());
      }
    } catch (PDOException $e) {
      $response = array('status' => 'EXCEPTION', 'result' => $e);
    }

    return $response;
  }

  public function update_room_type($bind)
  {
    try {
      $sql = 'UPDATE room_type
              SET type_name = :type_name,
                  person_limit = :person_limit,
                  price_temporary = :price_temporary,
                  price_half = :price_half,
                  price_day = :price_day,
                  price_holiday = :price_holiday,
                  bed_type = :bed_type
              WHERE id_type = :id_type';

      $result = $this->pdo->perform($sql, $bind);

      if ($result) {
        $response = array('status' => 'OK', 'result' => array());
      } else {
        $response = array('status' => 'ERROR', 'result' => array());
      }
    } catch (PDOException $e) {
      $response = array('status' => 'EXCEPTION', 'result' => $e);
    }

    return $response;
  }

  public function delete_room_type($bind)
  {
    try {
      $sql = 'DELETE FROM room_type WHERE id_type = :id_type';

      $result = $this->pdo->perform($sql, $bind);

      if ($result) {
        $response = array('status' => 'OK', 'result' => array());
      } else {
        $response = array('status' => 'ERROR', 'result' => array());
      }
    } catch (PDOException $e) {
      $response = array('status' => 'EXCEPTION', 'result' => $e);
    }

    return $response;
  }
}

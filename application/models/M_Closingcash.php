<?php
// --
class M_Closingcash extends Model
{
  // --
  public function __construct()
  {
    parent::__construct();
  }

  public function get_cash_closing_form_today()
  {
    try {

      $sql = 'SELECT c.*, u.first_name, u.last_name 
              FROM caja c 
              INNER JOIN user u ON c.id_user = u.id 
              WHERE c.estado = "Abierta" 
              AND DATE(c.fecha_hora_apertura) = CURDATE()';
      
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

  public function post_cash_closing($bind)
  {
    try {
      $sql = 'SELECT id, monto_inicial FROM caja WHERE estado = "Abierta" AND DATE(fecha_hora_apertura) = CURDATE()';
      $caja = $this->pdo->fetchAll($sql);
      
      if (!$caja) {
        return array('status' => 'ERROR', 'result' => 'No hay una caja abierta para cerrar el día de hoy');
      }

      $id_caja = $caja[0]['id'];
      $monto_inicial = floatval($caja[0]['monto_inicial']);
      $ingresos = floatval($bind['income']);
      $egresos = floatval($bind['expenses']);

      $monto_final = $monto_inicial + $ingresos - $egresos;

      $sql = 'UPDATE caja SET 
                fecha_hora_cierre = NOW(),
                ingresos = ?,
                egresos = ?,
                observaciones = ?,
                monto_final = ?,
                estado = "Cerrada"
              WHERE id = ?';

      $params = array(
        $ingresos,
        $egresos,
        $bind['notes'],
        $monto_final,
        $id_caja
      );

      $result = $this->pdo->perform($sql, $params);

      if ($result) {
        $response = array(
          'status' => 'OK', 
          'result' => array(
            'monto_final' => $monto_final,
            'message' => 'Caja cerrada correctamente'
          )
        );
      } else {
        $response = array('status' => 'ERROR', 'result' => 'Error al actualizar la base de datos');
      }
    } catch (PDOException $e) {
      $response = array('status' => 'EXCEPTION', 'result' => $e);
    }
    return $response;
  }

  public function closing_cash_history()
  {
    try {
      $sql = 'SELECT c.*, u.first_name, u.last_name 
              FROM caja c
              INNER JOIN user u ON c.id_user = u.id 
              WHERE c.estado = "Cerrada"
              ORDER BY c.fecha_hora_cierre DESC';
              
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

  public function calculate_daily_income()
  {
  try {
      $today = date('Y-m-d');
      
      $sql = "SELECT 
              COALESCE(SUM(p.pre_payment), 0) as pre_payment,
              COALESCE(SUM(p.payment_sales), 0) as sales_product
              FROM payment p
              WHERE DATE(p.payment_date) = ?";

      $result = $this->pdo->fetchAll($sql, array($today));

      if ($result && count($result) > 0) {
          $data = $result[0];
          $total_income = $data['pre_payment'] + $data['sales_product'];
          
          return array(
              'status' => 'OK',
              'result' => array(
                  'pre_payment' => floatval($data['pre_payment']),
                  'sales_product' => floatval($data['sales_product']),
                  'total_income' => floatval($total_income)
              )
          );
      }

      return array(
          'status' => 'ERROR',
          'result' => array(
              'pre_payment' => 0,
              'sales_product' => 0,
              'total_income' => 0
          )
      );

  } catch (PDOException $e) {
      return array(
          'status' => 'EXCEPTION',
          'result' => $e
      );
  }
}
}

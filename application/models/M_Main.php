<?php 
// --
class M_Main extends Model {
    // --
    public function __construct() {
		  parent::__construct();
    }
    
	// --
	public function get_document_types() {
        // --
        try {
            // --
            $sql = 'SELECT id, description, status FROM document_type;';
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

    //--
    public function get_voucher_type() {
        // --
        try {
            // --
            $sql = 'SELECT 
                        id, 
                        description, 
                        status 
                    FROM voucher_type
                    WHERE id IN (1,2,8);';//Aqui se agrega el id del que quieres que apresca en este caso bolote fatura y ticket
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

    //--
    public function get_payment_type() {
        // --
        try {
            // --
            $sql = 'SELECT 
                        id, 
                        description, 
                        status 
                    FROM payment_type;';
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

    //payment shape
    
    public function get_payment_shape() {
        try {
            $sql = 'SELECT 
                        id, 
                        description, 
                        status 
                    FROM payment_shape;';
            $result = $this->pdo->fetchAll($sql);
            
            if ($result) {
                $response = array('status' => 'OK', 'result' => $result);
            } else {
                $response = array('status' => 'ERROR', 'result' => array());
            }
        } catch (PDOException $e) {
            $response = array('status' => 'EXCEPTION', 'result' => $e->getMessage());
        }
        return $response;
    }
    
    //payment shape
    public function get_nombres_apellidos() {
        // --
        try {
            // --
            $sql = 'SELECT
                        idpracticante,
                        nombres_apellidos,
                        dni,
                        institucion,
                        sede,
                        especialidad,
                        modalidad,
                        correo,
                        numero,
                        fecha_inicio,
                        fecha_termino,
                        estado,
                        grupo,
                        tarea
                    FROM practicantes;';
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
    // --
    public function get_practicante_by_id($idpracticante) {
        try {
            $sql = 'SELECT dni, sede, numero 
                    FROM practicantes 
                    WHERE idpracticante = :idpracticante';
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':idpracticante', $idpracticante);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
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
    public function get_integrante_by_dni($dni) {
        try {
            $sql = 'SELECT nombres_apellidos, dni FROM practicantes WHERE dni = :dni';
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':dni', $dni);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
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
	
    public function get_payment_method()
    {
        // --
        try {
            // --
            $sql = 'SELECT 
                        id, 
                        description, 
                        status 
                    FROM payment_shape;';
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
    public function get_campus()
    {
        // Resto del código permanece igual
        try {
            $sql = 'SELECT 
                        id, 
                        description, 
                        status 
                    FROM payment_type;';
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
    // --    

    //--
    public function get_coins()
    {
        // --
        try {
            // --
            $sql = 'SELECT 
                        id,
                        code, 
                        description, 
                        status 
                    FROM coin
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

    //--
    public function get_igv()
    {
        // --
        try {
            // --
            $sql = 'SELECT 
                        id, 
                        value, 
                        status 
                    FROM igv
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
    public function get_role()
    {
        // --
        try {
            // --
            $sql = 'SELECT 
                        id, 
                        description,
                        status
                    FROM roleclients
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

}
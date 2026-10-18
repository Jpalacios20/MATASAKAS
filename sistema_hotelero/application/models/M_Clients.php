<?php
// --
class M_Clients extends Model {
    // --
    public function __construct() {
        parent::__construct();
    }
    
    // Obtener todos los clientes aqui esta el secreto
    public function get_clients() {
        try {
            $sql = 'SELECT 
                        c.id AS id_clients,
                        c.id_document_type,
                        dt.description AS document_type,
                        c.name,
                        c.document_number,
                        c.nationality,
                        c.birth_date,
                        c.birth_place,
                        c.address,
                        c.phone,
                        c.business_name,
                        c.email,
                        c.status
                    FROM person c
                    INNER JOIN document_type dt ON dt.id = c.id_document_type';
            $result = $this->pdo->fetchAll($sql);
    
            if ($result) {
                return ['status' => 'OK', 'result' => $result];
            } else {
                return ['status' => 'ERROR', 'result' => []];
            }
        } catch (PDOException $e) {
            return ['status' => 'EXCEPTION', 'result' => $e->getMessage()];
        }
    }
    

    // Obtener un cliente por ID
    public function get_client_by_id($bind) {
        try {
            $sql = 'SELECT 
                        c.id AS id_clients,
                        c.id_document_type,
                        dt.description AS document_type,
                        c.name,
                        c.document_number,
                        c.nationality,
                        c.birth_date,
                        c.birth_place,
                        c.address,
                        c.phone,
                        c.business_name,
                        c.email,
                        c.status
                    FROM person c
                    INNER JOIN document_type dt ON dt.id = c.id_document_type
                    WHERE c.id = :id_clients AND c.status = 1';

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


    // --
    public function create_clients($bind) {
        try {
            // Consulta SQL para insertar un nuevo cliente en la tabla 'clients'
            $sql = 'INSERT INTO person
            (
                id_document_type,
                name,
                document_number,
                nationality,
                birth_date,
                birth_place,
                address,
                phone,
                business_name,
                email
            ) 
            VALUES 
            (
                :id_document_type,
                :name,
                :document_number,
                :nationality,
                :birth_date,
                :birth_place,
                :address,
                :phone,
                :business_name,
                :email
            )';
    
            // Normalización de los valores para asegurar que los opcionales estén definidos
            $bind = array_merge([
                'birth_date' => null,
                'birth_place' => null,
                'address' => null,
                'phone' => null,
                'business_name' => null,
                'email' => null,
            ], $bind);
    
            // Ejecuta la consulta con los valores proporcionados en el array $bind
            $result = $this->pdo->perform($sql, $bind);
    
            // Verifica si la consulta se ejecutó correctamente
            if ($result) {
                // Si la inserción fue exitosa, retorna un array con el estado 'OK' y un array vacío como resultado
                $response = array('status' => 'OK', 'result' => array());
            } else {
                // Si no se pudo realizar la inserción, retorna un array con el estado 'ERROR'
                $response = array('status' => 'ERROR', 'result' => array());
            }
        } catch (PDOException $e) {
            // Si ocurre una excepción (error en la base de datos), captura el error y lo retorna
            $response = array('status' => 'EXCEPTION', 'result' => $e);
        }
    
        // Devuelve la respuesta que contiene el estado y el resultado de la operación
        return $response;
    }

    public function get_client_by_dni($bind)
{
    try {
        $sql = 'SELECT 
                    name,
                    address
                FROM person 
                WHERE document_number = :document_number 
                AND status = 1
                LIMIT 1';

        $result = $this->pdo->fetchOne($sql, $bind);

        if ($result) {
            return [
                'status' => 'OK',
                'result' => $result
            ];
        } else {
            return [
                'status' => 'ERROR',
                'result' => []
            ];
        }

    } catch (PDOException $e) {
        return [
            'status' => 'EXCEPTION',
            'result' => $e->getMessage()
        ];
    }
}
    
    
    //Jalar el mombre 
    public function get_name() {
        // --
        try {
            // --
            $sql = 'SELECT 
                    id,
                    name,
                    status
                FROM person';
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
    public function update_clients($bind) {
        try {
            // Consulta SQL para actualizar un cliente
            $sql = 'UPDATE person 
                    SET
                        id_document_type = :id_document_type,
                        name = :name,
                        document_number = :document_number,
                        nationality = :nationality,
                        birth_date = :birth_date,
                        birth_place = :birth_place,
                        address = :address,
                        phone = :phone,
                        email = :email,
                        business_name = :business_name
                    WHERE id = :id_clients';
    
            // Filtrar los valores opcionales que pueden ser null
            $bind = array_filter($bind, function ($value) {
                return $value !== null;
            });
    
            // Ejecutar la consulta
            $result = $this->pdo->perform($sql, $bind);
    
            // Verificar el resultado
            if ($result) {
                $response = array('status' => 'OK', 'result' => array());
            } else {
                $response = array('status' => 'ERROR', 'result' => array());
            }
        } catch (PDOException $e) {
            // Manejo de excepciones
            $response = array('status' => 'EXCEPTION', 'result' => $e);
        }
    
        return $response;
    }
    
    

    // --
    public function delete_clients($bind) 
    {
    try {
        // Consulta SQL para eliminar un cliente
        $sql = 'DELETE FROM person WHERE id = :id_clients'; // Usamos el campo 'id' como clave primaria en la tabla 'clients'
        
        // Ejecutamos la consulta con los parámetros vinculados
        $result = $this->pdo->perform($sql, $bind);

        // Comprobamos si la consulta afectó alguna fila (es decir, si la eliminación fue exitosa)
        if ($result) {
            $response = array('status' => 'OK', 'result' => array());
        } else {
            $response = array('status' => 'ERROR', 'result' => array());
        }
    } catch (PDOException $e) {
        // Si ocurre una excepción, la capturamos y devolvemos un mensaje de error
        $response = array('status' => 'EXCEPTION', 'result' => $e);
    }

    return $response; // Retornamos la respuesta
    }

    public function get_business_name() {
        // --
        try {
            // --
            $sql = 'SELECT 
                    id,
                    business_name,
                    status
                FROM person';
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
    public function get_business_name_cli()
    {
        // --
        try {
            // --
            $sql = "SELECT c.id, COALESCE(NULLIF(TRIM(c.name), ''), NULLIF(TRIM(c.business_name), ''), CONCAT('Persona #', c.id)) AS business_name, c.document_number, c.address FROM person c ORDER BY business_name ASC, c.id ASC";
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

    
    
    public function get_config()
    {
        try {
            $sql = "SELECT token FROM api_config WHERE status = 1 LIMIT 1";
            $result = $this->pdo->fetchOne($sql);

            if (!$result) {
                error_log("No se encontró un token válido en la base de datos.");
                return ['status' => 'ERROR', 'result' => null];
            }

            return ['status' => 'OK', 'result' => $result];
        } catch (PDOException $e) {
            error_log("Error al obtener configuración de API: " . $e->getMessage());
            return ['status' => 'EXCEPTION', 'result' => $e->getMessage()];
        }
    }

    //Dupplicados
    public function verify_document_exists($number, $type) {
        try {
            $sql = 'SELECT COUNT(*) AS total 
                    FROM person 
                    WHERE document_number = :document_number 
                    AND id_document_type = :id_document_type';
                    
            $bind = array(
                'document_number' => $number,
                'id_document_type' => $type
            );
            
            $result = $this->pdo->fetchOne($sql, $bind);
            
            if ($result && $result['total'] > 0) {
                return true;
            }
            return false;
        } catch (PDOException $e) {
            return false;
        }
    }
    

}

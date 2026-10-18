<?php
// --
class M_Task_Control extends Model {
    // --
    public function __construct() {
        parent::__construct();
    }
    
    // Obtener todos los clientes aqui esta el secreto
    public function get_task_control() {
        try {
            $sql = 'SELECT 
                        id,
                        entry_date, 
                        start_date, 
                        end_date, 
                        leader, 
                        project_name, 
                        service_status, 
                        payment_status, 
                        outstanding_balance, 
                        status 
                    FROM task_control
                    ';
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
    public function get_task_control_by_id($bind)
        {
        try {
            $sql = 'SELECT 
                        entry_date, 
                        start_date, 
                        end_date, 
                        leader, 
                        project_name, 
                        service_status, 
                        payment_status, 
                        outstanding_balance, 
                        status 
                    FROM task_control';

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
        
    // Crear nuevo registro de task_control
    public function create_task_control($data) {
        try {
            $sql = "INSERT INTO task_control (
                        entry_date, 
                        start_date, 
                        end_date, 
                        leader, 
                        project_name, 
                        service_status, 
                        payment_status, 
                        outstanding_balance, 
                        status, 
                        delivery_status, 
                        document_number, 
                        institution, 
                        phone,
                        group_members
                    ) VALUES (
                        :entry_date, 
                        :start_date, 
                        :end_date, 
                        :leader, 
                        :project_name, 
                        :service_status, 
                        :payment_status, 
                        :outstanding_balance, 
                        :status, 
                        :delivery_status, 
                        :document_number, 
                        :institution, 
                        :phone,
                        :group_members
                    )";
    
            $stmt = $this->pdo->prepare($sql);
    
            // Asignar valores a los parámetros de la consulta
            $stmt->bindValue(':entry_date', date('Y-m-d')); // Fecha actual
            $stmt->bindValue(':start_date', null); // Puedes ajustar esto según tu lógica
            $stmt->bindValue(':end_date', null); // Puedes ajustar esto según tu lógica
            $stmt->bindValue(':leader', $data['nombres_apellidos']);
            $stmt->bindValue(':project_name', $data['nombre_proyecto']);
            $stmt->bindValue(':service_status', $data['estado_servicio']);
            $stmt->bindValue(':payment_status', $data['estado_pago']);
            $stmt->bindValue(':outstanding_balance', $data['costo_desarrollo']);
            $stmt->bindValue(':status', 'Activo'); // Puedes ajustar esto según tu lógica
            $stmt->bindValue(':delivery_status', $data['estado_entrega']);
            $stmt->bindValue(':document_number', $data['documento']);
            $stmt->bindValue(':institution', $data['sede']);
            $stmt->bindValue(':phone', $data['telefono']);
    
            // Guardar integrantes en la columna group_members
            $stmt->bindValue(':group_members', $data['integrantes']);
    
            $stmt->execute();
    
            // Obtener el ID del último registro insertado
            $lastInsertedId = $this->pdo->lastInsertId();
    
            return ['status' => 'OK', 'result' => $lastInsertedId];
        } catch (PDOException $e) {
            return ['status' => 'EXCEPTION', 'result' => $e->getMessage()];
        }
    }
    
    // Obtener información para el modal de proceso de desarrollo (nuevo)
    public function get_task_control_process_modal($id_task_control) {
        try {
            $sql = 'SELECT leader, project_name FROM task_control WHERE id = :id';
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id_task_control, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                return ['status' => 'OK', 'result' => $result];
            } else {
                return ['status' => 'ERROR', 'result' => 'No se encontró el registro.'];
            }
        } catch (PDOException $e) {
            return ['status' => 'EXCEPTION', 'result' => $e->getMessage()];
        }
    }
    
    public function save_development_process($data) {
        try {
            $sql = "INSERT INTO development_process (
                AN_start_date, AN_end_date, AN_status, AN_comment, 
                DI_start_date, DI_end_date, DI_status, DI_comment, 
                DE_start_date, DE_end_date, DE_status, DE_comment, 
                IM_start_date, IM_end_date, IM_status, IM_comment, 
                MAN_start_date, MAN_end_date, MAN_status, MAN_comment
            ) VALUES (
                :AN_start_date, :AN_end_date, :AN_status, :AN_comment, 
                :DI_start_date, :DI_end_date, :DI_status, :DI_comment, 
                :DE_start_date, :DE_end_date, :DE_status, :DE_comment, 
                :IM_start_date, :IM_end_date, :IM_status, :IM_comment, 
                :MAN_start_date, :MAN_end_date, :MAN_status, :MAN_comment
            )";
    
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($data);
    
            return ['status' => 'OK', 'result' => 'Proceso guardado'];
        } catch (PDOException $e) {
            return ['status' => 'EXCEPTION', 'result' => $e->getMessage()];
        }
    }
    public function get_development_process_by_id($development_id) {
        try {
            $sql = "SELECT * FROM development_process WHERE development_id = :development_id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':development_id', $development_id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if ($result) {
                return ['status' => 'OK', 'result' => $result];
            } else {
                return ['status' => 'ERROR', 'result' => 'No se encontraron datos para el ID proporcionado.'];
            }
        } catch (PDOException $e) {
            return ['status' => 'EXCEPTION', 'result' => $e->getMessage()];
        }
    }
}


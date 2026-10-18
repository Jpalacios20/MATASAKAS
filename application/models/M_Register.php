<?php 
// --
class M_Register extends Model {
    // --
    public function __construct() {
        parent::__construct();
    }

    // -- Función para insertar el nuevo registro
    public function insertar($datos) {
        return $this->db->insert('reservation', $datos);
    }

    // -- Función para obtener los registros relacionando la reserva, el invitado y los datos de la persona
    public function obtener_registros() {
        // Seleccionamos todos los campos de la reserva y unimos el nombre y apellido de la tabla person
        $this->db->select('reservation.*, CONCAT(person.name, " ", person.lastname) as cliente');
        $this->db->from('reservation');
        
        // Relacionamos reservation con person usando id_guest que es donde están los IDs válidos
        $this->db->join('person', 'person.id = reservation.id_guest', 'left');
        
        // Ordenamos por la llave primaria correcta de la tabla reservation
        $this->db->order_by('reservation.id_reservation', 'DESC');
        
        $query = $this->db->get();
        return $query->result_array();
    }
}
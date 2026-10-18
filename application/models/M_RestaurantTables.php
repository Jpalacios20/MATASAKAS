<?php

class M_RestaurantTables extends Model
{
    // =========================================================
    // CONSTRUCTOR
    // =========================================================
    public function __construct()
    {
        parent::__construct();
    }


    // =========================================================
    // OBTENER TODAS LAS MESAS
    // =========================================================
    public function get_tables()
    {
        try {

            $sql = 'SELECT
                        id,
                        table_name,
                        seats,
                        status
                    FROM hs_restaurant_tables
                    ORDER BY id ASC';

            $result = $this->pdo->fetchAll($sql);

            if ($result) {

                return array(
                    'status' => 'OK',
                    'result' => $result
                );

            } else {

                return array(
                    'status' => 'ERROR',
                    'result' => array()
                );
            }

        } catch (PDOException $e) {

            return array(
                'status' => 'EXCEPTION',
                'result' => $e
            );
        }
    }


    // =========================================================
    // OBTENER UNA MESA POR ID
    // =========================================================
    public function get_table_by_id($bind)
    {
        try {

            $sql = 'SELECT
                        id,
                        table_name,
                        seats,
                        status
                    FROM hs_restaurant_tables
                    WHERE id = :id
                    LIMIT 1';

            $result = $this->pdo->fetchOne(
                $sql,
                $bind
            );

            if ($result) {

                return array(
                    'status' => 'OK',
                    'result' => $result
                );

            } else {

                return array(
                    'status' => 'ERROR',
                    'result' => array()
                );
            }

        } catch (PDOException $e) {

            return array(
                'status' => 'EXCEPTION',
                'result' => $e
            );
        }
    }


    // =========================================================
    // CREAR MESA
    // =========================================================
    public function create_table($bind)
    {
        try {

            $table_name = isset($bind['table_name'])
                ? trim($bind['table_name'])
                : '';

            $seats = isset($bind['seats'])
                ? intval($bind['seats'])
                : 0;

            $status = isset($bind['status'])
                ? trim($bind['status'])
                : 'LIBRE';


            // -------------------------------------------------
            // VALIDACIONES
            // -------------------------------------------------

            if ($table_name === '') {

                return array(
                    'status' => 'ERROR',
                    'result' => 'Ingresa el nombre o número de la mesa.'
                );
            }


            if ($seats <= 0) {

                return array(
                    'status' => 'ERROR',
                    'result' => 'La mesa debe tener al menos 1 asiento.'
                );
            }


            $estados_validos = array(
                'LIBRE',
                'OCUPADA',
                'RESERVADA',
                'INACTIVA'
            );


            if (!in_array($status, $estados_validos)) {

                $status = 'LIBRE';
            }


            // -------------------------------------------------
            // VERIFICAR SI YA EXISTE
            // -------------------------------------------------

            $sql_existente = 'SELECT
                                id
                              FROM hs_restaurant_tables
                              WHERE table_name = :table_name
                              LIMIT 1';

            $existente = $this->pdo->fetchOne(
                $sql_existente,
                array(
                    'table_name' => $table_name
                )
            );


            if ($existente) {

                return array(
                    'status' => 'ERROR',
                    'result' => 'Ya existe una mesa con ese nombre.'
                );
            }


            // -------------------------------------------------
            // INSERTAR
            // -------------------------------------------------

            $sql = 'INSERT INTO hs_restaurant_tables
                    (
                        table_name,
                        seats,
                        status
                    )
                    VALUES
                    (
                        :table_name,
                        :seats,
                        :status
                    )';


            $result = $this->pdo->perform(
                $sql,
                array(
                    'table_name' => $table_name,
                    'seats'      => $seats,
                    'status'     => $status
                )
            );


            if ($result) {

                $id = $this->pdo->lastInsertId();

                return array(
                    'status' => 'OK',
                    'result' => array(
                        'id'         => $id,
                        'table_name' => $table_name,
                        'seats'      => $seats,
                        'status'     => $status
                    )
                );

            } else {

                return array(
                    'status' => 'ERROR',
                    'result' => 'No se pudo crear la mesa.'
                );
            }

        } catch (PDOException $e) {

            return array(
                'status' => 'EXCEPTION',
                'result' => $e
            );
        }
    }


    // =========================================================
    // ACTUALIZAR MESA
    // =========================================================
    public function update_table($bind)
    {
        try {

            $id = isset($bind['id'])
                ? intval($bind['id'])
                : 0;

            $table_name = isset($bind['table_name'])
                ? trim($bind['table_name'])
                : '';

            $seats = isset($bind['seats'])
                ? intval($bind['seats'])
                : 0;

            $status = isset($bind['status'])
                ? trim($bind['status'])
                : 'LIBRE';


            // -------------------------------------------------
            // VALIDACIONES
            // -------------------------------------------------

            if ($id <= 0) {

                return array(
                    'status' => 'ERROR',
                    'result' => 'ID de mesa no válido.'
                );
            }


            if ($table_name === '') {

                return array(
                    'status' => 'ERROR',
                    'result' => 'Ingresa el nombre o número de la mesa.'
                );
            }


            if ($seats <= 0) {

                return array(
                    'status' => 'ERROR',
                    'result' => 'La mesa debe tener al menos 1 asiento.'
                );
            }


            $estados_validos = array(
                'LIBRE',
                'OCUPADA',
                'RESERVADA',
                'INACTIVA'
            );


            if (!in_array($status, $estados_validos)) {

                return array(
                    'status' => 'ERROR',
                    'result' => 'El estado de la mesa no es válido.'
                );
            }


            // -------------------------------------------------
            // VERIFICAR QUE LA MESA EXISTA
            // -------------------------------------------------

            $sql_buscar = 'SELECT
                                id
                           FROM hs_restaurant_tables
                           WHERE id = :id
                           LIMIT 1';

            $mesa = $this->pdo->fetchOne(
                $sql_buscar,
                array(
                    'id' => $id
                )
            );


            if (!$mesa) {

                return array(
                    'status' => 'ERROR',
                    'result' => 'La mesa no existe.'
                );
            }


            // -------------------------------------------------
            // VERIFICAR NOMBRE DUPLICADO
            // -------------------------------------------------

            $sql_existente = 'SELECT
                                id
                              FROM hs_restaurant_tables
                              WHERE table_name = :table_name
                              AND id <> :id
                              LIMIT 1';

            $existente = $this->pdo->fetchOne(
                $sql_existente,
                array(
                    'table_name' => $table_name,
                    'id'         => $id
                )
            );


            if ($existente) {

                return array(
                    'status' => 'ERROR',
                    'result' => 'Ya existe otra mesa con ese nombre.'
                );
            }


            // -------------------------------------------------
            // ACTUALIZAR
            // -------------------------------------------------

            $sql = 'UPDATE hs_restaurant_tables
                    SET
                        table_name = :table_name,
                        seats = :seats,
                        status = :status
                    WHERE id = :id';


            $result = $this->pdo->perform(
                $sql,
                array(
                    'id'         => $id,
                    'table_name' => $table_name,
                    'seats'      => $seats,
                    'status'     => $status
                )
            );


            if ($result) {

                return array(
                    'status' => 'OK',
                    'result' => array(
                        'id'         => $id,
                        'table_name' => $table_name,
                        'seats'      => $seats,
                        'status'     => $status
                    )
                );

            } else {

                return array(
                    'status' => 'ERROR',
                    'result' => 'No se pudo actualizar la mesa.'
                );
            }

        } catch (PDOException $e) {

            return array(
                'status' => 'EXCEPTION',
                'result' => $e
            );
        }
    }


    // =========================================================
    // ACTUALIZAR SOLO EL ESTADO
    // =========================================================
    public function update_status($bind)
    {
        try {

            $id = isset($bind['id'])
                ? intval($bind['id'])
                : 0;

            $status = isset($bind['status'])
                ? trim($bind['status'])
                : '';


            // -------------------------------------------------
            // VALIDACIONES
            // -------------------------------------------------

            if ($id <= 0) {

                return array(
                    'status' => 'ERROR',
                    'result' => 'ID de mesa no válido.'
                );
            }


            $estados_validos = array(
                'LIBRE',
                'OCUPADA',
                'RESERVADA',
                'INACTIVA'
            );


            if (!in_array($status, $estados_validos)) {

                return array(
                    'status' => 'ERROR',
                    'result' => 'Estado de mesa no válido.'
                );
            }


            // -------------------------------------------------
            // ACTUALIZAR
            // -------------------------------------------------

            $sql = 'UPDATE hs_restaurant_tables
                    SET
                        status = :status
                    WHERE id = :id';


            $result = $this->pdo->perform(
                $sql,
                array(
                    'id'     => $id,
                    'status' => $status
                )
            );


            if ($result) {

                return array(
                    'status' => 'OK',
                    'result' => array(
                        'id'     => $id,
                        'status' => $status
                    )
                );

            } else {

                return array(
                    'status' => 'ERROR',
                    'result' => 'No se pudo actualizar el estado.'
                );
            }

        } catch (PDOException $e) {

            return array(
                'status' => 'EXCEPTION',
                'result' => $e
            );
        }
    }


    // =========================================================
    // ELIMINAR MESA
    // =========================================================
    public function delete_table($bind)
    {
        try {

            $id = isset($bind['id'])
                ? intval($bind['id'])
                : 0;


            if ($id <= 0) {

                return array(
                    'status' => 'ERROR',
                    'result' => 'ID de mesa no válido.'
                );
            }


            // -------------------------------------------------
            // VERIFICAR QUE EXISTA
            // -------------------------------------------------

            $sql_buscar = 'SELECT
                                id,
                                table_name,
                                status
                           FROM hs_restaurant_tables
                           WHERE id = :id
                           LIMIT 1';


            $mesa = $this->pdo->fetchOne(
                $sql_buscar,
                array(
                    'id' => $id
                )
            );


            if (!$mesa) {

                return array(
                    'status' => 'ERROR',
                    'result' => 'La mesa no existe.'
                );
            }


            // -------------------------------------------------
            // NO ELIMINAR MESA OCUPADA
            // -------------------------------------------------

            if (
                isset($mesa['status']) &&
                $mesa['status'] === 'OCUPADA'
            ) {

                return array(
                    'status' => 'ERROR',
                    'result' => 'No se puede eliminar una mesa ocupada.'
                );
            }


            // -------------------------------------------------
            // ELIMINAR
            // -------------------------------------------------

            $sql = 'DELETE FROM hs_restaurant_tables
                    WHERE id = :id';


            $result = $this->pdo->perform(
                $sql,
                array(
                    'id' => $id
                )
            );


            if ($result) {

                return array(
                    'status' => 'OK',
                    'result' => array(
                        'id' => $id
                    )
                );

            } else {

                return array(
                    'status' => 'ERROR',
                    'result' => 'No se pudo eliminar la mesa.'
                );
            }

        } catch (PDOException $e) {

            return array(
                'status' => 'EXCEPTION',
                'result' => $e
            );
        }
    }
}
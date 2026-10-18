<?php

class M_Billingpersale extends Model
{

    // ---------------------------------------------------------
    // CONSTRUCTOR
    // ---------------------------------------------------------
    public function __construct()
    {
        parent::__construct();
    }


    // ---------------------------------------------------------
    // LISTAR COMPROBANTES
    // ---------------------------------------------------------
    public function get_Billingpersale()
    {
        try {

            $sql = 'SELECT 
                        bp.id AS id_billingpersale,
                        bp.issue_date,
                        c.name AS clients,
                        vt.description AS voucher_type,
                        bp.series,
                        bp.correlative,
                        bp.leyend,
                        bp.total_amount,
                        bp.response,
                        bp.status
                    FROM Billingpersale bp
                    INNER JOIN person c ON c.id = bp.clients_id
                    INNER JOIN voucher_type vt ON vt.id = bp.voucher_type';

            $result = $this->pdo->fetchAll($sql);

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
    // OBTENER PRODUCTOS PARA AGREGAR A LA VENTA
    // ---------------------------------------------------------
    public function get_salesproduct($id_campus = null)
    {
        try {
            // salesproduct es la fuente del catalogo de venta.
            // Evitar JOINs opcionales que impidan devolver JSON.
            $sql = 'SELECT
                        sp.id_salesproduct,
                        sp.id_product,
                        sp.product_sku,
                        sp.product_name,
                        sp.unit_type,
                        sp.product_stock,
                        sp.price_sell,
                        "" AS category_name,
                        "" AS image_url,
                        "Gravado" AS affectation,
                        sp.status
                    FROM salesproduct sp
                    WHERE sp.status = 1
                    ORDER BY sp.product_name ASC';
            $result = $this->pdo->fetchAll($sql);
            return array('status' => 'OK', 'result' => $result ?: array());
        } catch (PDOException $e) {
            return array('status' => 'EXCEPTION', 'result' => $e);
        }
    }


    // ---------------------------------------------------------
    // REPORTE DE COMPROBANTE
    // ---------------------------------------------------------
    public function get_billingpersale_Report($billingpersale)
    {
        try {

            $sql = 'SELECT 
                bp.id AS id_billingpersale,
                u.first_name AS name_user,
                c.name AS name_client,
                c.id_document_type,
                dt.description AS documento_client,
                c.document_number,
                c.address AS address_client,
                bp.currency,
                coin.description AS DescCurrency,
                bp.operation_type,
                bp.voucher_type,
                vt.code,
                ps.description AS payment_medium,
                bp.issue_date,
                bp.due_date,
                bp.series,
                bp.correlative,
                bp.igv,
                bp.leyend,
                bp.total_amount,
                bp.taxable_operations,
                bp.free_operations,
                bp.exempt_operations,
                bp.unaffected_operations,
                bp.status
            FROM billingpersale bp
            LEFT JOIN person c ON bp.clients_id = c.id 
            LEFT JOIN voucher_type vt ON vt.id = bp.voucher_type
            LEFT JOIN payment_shape ps ON ps.id = bp.payment_medium
            LEFT JOIN document_type dt ON dt.id = c.id_document_type
            LEFT JOIN coin ON coin.code = bp.currency
            LEFT JOIN user u ON u.id = bp.user_id
            WHERE bp.id = :billingpersale';

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(
                ':billingpersale',
                $billingpersale,
                PDO::PARAM_INT
            );

            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

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
                'result' => $e->getMessage()
            );
        }

        return $response;
    }


    // ---------------------------------------------------------
    // DETALLE DEL REPORTE
    // ---------------------------------------------------------
    public function get_billingpersale_details_Report($billingpersale)
    {
        try {

            /*
             * Los datos del detalle de la venta ya se guardan en
             * billingpersale_detail (description, code, quantity, precios,
             * impuestos, etc.). No es necesario unirlos con una tabla
             * "products", que no corresponde a esta estructura de BD.
             *
             * Esto además conserva los datos tal como fueron vendidos,
             * aunque el producto cambie posteriormente.
             */
            $sql = 'SELECT
                        bd.description AS articulo,
                        bd.code,
                        bd.serie,
                        bd.unit_type,
                        bd.unit_value,
                        bd.free_unit_value,
                        bd.tax_affectation_type,
                        bd.tax_percentage,
                        bd.tax_amount,
                        bd.quantity,
                        bd.item_unit_price
                    FROM billingpersale_detail bd
                    WHERE bd.sale_id = :billingpersale
                    ORDER BY bd.item ASC';

            $stmt = $this->pdo->prepare($sql);

            $stmt->bindParam(
                ':billingpersale',
                $billingpersale,
                PDO::PARAM_INT
            );

            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
                'result' => $e->getMessage()
            );
        }

        return $response;
    }


    // ---------------------------------------------------------
    // XML
    // ---------------------------------------------------------
    public function get_billingpersale_xml($billingpersale)
    {
        try {

            $sql = 'SELECT 
                bp.id AS id_billingpersale,
                u.first_name AS name_user,
                c.name AS name_client, 
                c.id_document_type,
                dt.description AS documento_client,
                c.document_number,
                c.address AS address_client,
                bp.currency,
                coin.description AS DescCurrency,
                bp.operation_type,
                bp.voucher_type,
                vt.code,
                ps.description AS payment_medium,
                CONCAT(bp.issue_date, " ", bp.issue_time) as issue_date,
                bp.due_date,
                bp.series,
                bp.correlative,
                bp.igv,
                bp.leyend,
                bp.total_amount,
                c.address,
                bp.taxable_operations,
                bp.free_operations,
                bp.exempt_operations,
                bp.unaffected_operations,
                bp.status
            FROM billingpersale bp
            LEFT JOIN person c ON bp.clients_id = c.id 
            LEFT JOIN voucher_type vt ON vt.id = bp.voucher_type
            LEFT JOIN payment_shape ps ON ps.id = bp.payment_medium
            LEFT JOIN document_type dt ON dt.id = c.id_document_type
            LEFT JOIN coin ON coin.code = bp.currency
            LEFT JOIN user u ON u.id = bp.user_id
            WHERE bp.id = :billingpersale';

            $stmt = $this->pdo->prepare($sql);

            $stmt->bindParam(
                ':billingpersale',
                $billingpersale,
                PDO::PARAM_INT
            );

            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

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
                'result' => $e->getMessage()
            );
        }

        return $response;
    }


    // ---------------------------------------------------------
    // DETALLE XML
    // ---------------------------------------------------------
    public function get_billingpersale_details_xml($billingpersale)
    {
        try {

            $sql = 'SELECT
                    bd.description AS articulo,
                    bd.code,
                    bd.serie,
                    bd.unit_type,
                    bd.unit_value,
                    bd.free_unit_value,
                    bd.tax_affectation_type,
                    bd.tax_percentage,
                    bd.tax_amount,
                    bd.quantity,
                    bd.item_unit_price
                FROM billingpersale_detail bd
                WHERE bd.sale_id = :billingpersale
                ORDER BY bd.item ASC';

            $stmt = $this->pdo->prepare($sql);

            $stmt->bindParam(
                ':billingpersale',
                $billingpersale,
                PDO::PARAM_INT
            );

            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
                'result' => $e->getMessage()
            );
        }

        return $response;
    }


    // ---------------------------------------------------------
    // DATOS DE LA EMPRESA
    // ---------------------------------------------------------
    public function get_company()
    {
        try {

            $sql = 'SELECT * FROM company';

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

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
    // OBTENER COMPROBANTE POR ID
    // ---------------------------------------------------------
    public function get_billingpersale_by_id($bind)
    {
        try {

            $sql = 'SELECT 
                        p.id AS id_billingpersale,
                        p.clients_id,
                        u.user AS user,
                        vt.description AS voucher_type,
                        pt.description AS payment_type,
                        p.date_issue,
                        p.series,
                        p.correlative,
                        p.coins,
                        p.igv,
                        p.igv_total,
                        p.legend,
                        p.total_sale,
                        p.address,
                        p.date_expiration,
                        p.op_taxed,
                        p.sustent,
                        p.doc_related,
                        p.proof_unique,
                        p.status
                    FROM billingpersale p
                    INNER JOIN person c ON p.clients_id = c.id
                    INNER JOIN user u ON u.id = p.id_user
                    INNER JOIN voucher_type vt ON vt.id = p.id_voucher_type
                    INNER JOIN payment_type pt ON pt.id = p.id_payment_type
                    WHERE c.id = :id_billingpersale';

            $result = $this->pdo->fetchOne($sql, $bind);

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
    // CREAR
    // ---------------------------------------------------------
    public function create_billingpersale($bind)
    {
        try {

            $sql = 'INSERT INTO billingpersale
            (
                id_document_type,
                name,
                document_number,
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
                :address,
                :phone,
                :business_name,
                :email   
            )';

            $result = $this->pdo->perform($sql, $bind);

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
    // ACTUALIZAR
    // ---------------------------------------------------------
    public function update_billingpersale($bind)
    {
        try {

            $sql = 'UPDATE billingpersale
                SET
                    id_document_type = :id_document_type,
                    name = :name,
                    document_number = :document_number,
                    address = :address,
                    phone = :phone,
                    email = :email,
                    business_name = :business_name
                WHERE id = :id_billingpersale';

            $result = $this->pdo->perform($sql, $bind);

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
    // ELIMINAR
    // ---------------------------------------------------------
    public function delete_billingpersale($bind)
    {
        try {

            $sql = 'DELETE FROM billingpersale 
                    WHERE id = :id_billingpersale';

            $result = $this->pdo->perform($sql, $bind);

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
    // ACTUALIZAR ESTADO
    // ---------------------------------------------------------
    public function get_billingpersale_status($bind)
    {
        try {

            $sql = 'UPDATE billingpersale 
                    SET status = :status,
                        response = :response
                    WHERE id = :id_billingpersale';

            $result = $this->pdo->perform($sql, $bind);

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
    // OBTENER EMAIL Y TELÉFONO
    // ---------------------------------------------------------
    public function get_number_email($bind)
    {
        try {

            $sql = 'SELECT 
                        bp.id AS id_billingpersale,
                        bp.clients_id,
                        c.email,
                        c.phone
                    FROM billingpersale bp
                    INNER JOIN person c ON bp.clients_id = c.id 
                    WHERE bp.id = :id_billingpersale';

            $result = $this->pdo->fetchOne($sql, $bind);

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

}
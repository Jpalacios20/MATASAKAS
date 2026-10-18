<!DOCTYPE html>
<html>

<head>
    <title>Comprobante</title>
    <style type="text/css">
        table {
            color: black;
            border: none;
            width: 100%;
        }

        .caja {
            border: 1px solid black;
            border-radius: 12px;
            padding: 8px;
            text-align: center;
        }

        .razon-social {
            color: red;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .direccion-empresa {
            font-size: 9px;
            text-align: left;
        }

        .cuadro-cliente {
            border: 1px solid black;
            border-radius: 8px;
            padding: 4px;
            font-size: 10px;
        }

        .cabecera th {
            background: #1D1B1B;
            color: white;
            font-size: 10px;
            padding: 6px 3px;
            text-align: center;
        }

        .detalle td {
            font-size: 9px;
            padding: 5px 4px;
            border-bottom: 0.3px solid #999;
        }

        .totales td {
            font-size: 9px;
            text-align: center;
            border: 0.3px solid black;
            padding: 4px;
        }

        .totales .titulo td {
            font-weight: bold;
            background: #f0f0f0;
        }

        .son {
            border: 0.3px solid black;
            font-size: 9px;
            padding: 5px;
        }

        .foot {
            font-size: 8pt;
            text-align: center;
        }
    </style>
</head>

<body>
    <?php
    // Datos de la empresa (vienen del controlador)
    if (isset($companyData) && $companyData['status'] === 'OK' && !empty($companyData['result'])) {
        $regc = $companyData['result'];
        $empresa = $regc['business_name'] ?? '';
        $rucE = $regc['ruc'] ?? '';
        $direccion = $regc['address'] ?? '';
        $direccion2 = $regc['address2'] ?? '';
        $distrito = $regc['district'] ?? '';
        $provincia = $regc['province'] ?? '';
        $departamento = $regc['department'] ?? '';
        $fecha_inicio = $regc['start_date'] ?? '';
        $telefono = $regc['phone'] ?? '';
        $correo = $regc['email'] ?? '';
        $rubro = $regc['industry'] ?? '';
        $web = $regc['web'] ?? '';
        // Logo local del proyecto (el de la base de datos apuntaba a un sitio externo)
        $logo = realpath(__DIR__ . '/../../img/elparaiso.png');
        if ($logo === false) {
            $logo = 'C:/xampp/htdocs/sistema_hotelero/img/elparaiso.png';
        }
    } else {
        throw new Exception("Error: No se encontraron datos de la compañía.");
    }

    // Datos de la venta (vienen del controlador)
    if (isset($reportData) && $reportData['status'] === 'OK' && !empty($reportData['result'])) {
        $regc = $reportData['result'];
        $nombre_user = $regc['name_user'] ?? '';
        $codeVoucher = $regc['code'] ?? '';
        $tipo_voucher = ($regc['voucher_type'] == '1') ? 'FACTURA ELECTRÓNICA' : 'BOLETA ELECTRÓNICA';
        $Docmuent_client = $regc['documento_client'] ?? '';
        $tipo_documento_cliente = ($regc['documento_client'] == 'DNI') ? '1' : '6';

        $cliente = $regc['name_client'] ?? '';
        $direccioncliente = $regc['address_client'] ?? '';

        $igv_asig = $regc['igv'] ?? 0;
        $rucC = $regc['document_number'] ?? '';
        $serie = $regc['series'] ?? '';
        $correlativo = $regc['correlative'] ?? '';
        $moneda = $regc['DescCurrency'] ?? '';
        $fecha = $regc['issue_date'] ?? '';
        $fecha_ven = $regc['due_date'] ?? '';
        $total_venta = $regc['total_amount'] ?? 0;
        $op_gravadas = $regc['taxable_operations'] ?? 0;
        $op_gratuitas = $regc['free_operations'] ?? 0;
        $op_exoneradas = $regc['exempt_operations'] ?? 0;
        $op_inafectas = $regc['unaffected_operations'] ?? 0;
        $codigotipo_pago = $regc['payment_medium'] ?? '';
        $leyenda = $regc['leyend'] ?? '';

        $billingModel = new M_Billingpersale();
        $rspta_detalle = $billingModel->get_billingpersale_details_Report($id_billingpersale);
        if ($rspta_detalle['status'] !== 'OK') {
            throw new Exception("Error: No se encontraron detalles para el ID proporcionado.");
        }
        $detalles = $rspta_detalle['result'];
        $item = 0;
    } else {
        throw new Exception("Error: No se encontraron datos de facturación para el ID proporcionado.");
    }

    // Formato de fecha sin depender de la extensión intl
    $meses = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
    $fmt_fecha = function ($f) use ($meses) {
        if (empty($f)) {
            return '';
        }
        $d = new DateTime($f);
        return $d->format('d') . ' de ' . $meses[(int)$d->format('n') - 1] . ' de ' . $d->format('Y');
    };
    $fecha_txt = $fmt_fecha($fecha);
    ?>

    <!-- ENCABEZADO -->
    <table style="width: 100%">
        <tr>
            <td style="width: 58%; text-align: left; vertical-align: top;">
                <img style="width: 40%;" src="<?= $logo ?>" alt="Logo"><br>
                <span class="razon-social"><?= $empresa; ?></span>
            </td>
            <td style="width: 4%;"></td>
            <td style="width: 38%; vertical-align: top;">
                <div class="caja">
                    <span style="font-size: 12px;">R.U.C. <?= $rucE; ?></span><br><br>
                    <b style="font-size: 12px;"><?= $tipo_voucher; ?></b><br><br>
                    <b style="font-size: 12px;"><?= $serie . ' - ' . $correlativo ?></b>
                </div>
            </td>
        </tr>
    </table>

    <div class="direccion-empresa">
        Dirección: <?= $direccion; ?> - <?= $distrito; ?> - <?= $provincia; ?><br>
        Telef.: <?= $telefono; ?> Email.: <?= $correo; ?>
    </div>
    <br>

    <!-- DATOS DEL CLIENTE -->
    <div class="cuadro-cliente">
        <table>
            <tr>
                <td style="width: 14%"><b>CLIENTE</b></td>
                <td style="width: 86%">: <?= $cliente; ?></td>
            </tr>
            <tr>
                <td style="width: 14%"><b><?= $Docmuent_client; ?></b></td>
                <td style="width: 86%">: <?= $rucC; ?></td>
            </tr>
            <tr>
                <td style="width: 14%"><b>DIRECCIÓN</b></td>
                <td style="width: 86%">: <?= $direccioncliente; ?></td>
            </tr>
            <tr>
                <td style="width: 14%"><b>FECHA</b></td>
                <td style="width: 86%">: <?= $fecha_txt; ?>
                    &nbsp;&nbsp;&nbsp;<b>Moneda</b>: <?= $moneda; ?>
                    &nbsp;&nbsp;&nbsp;<b>Vendedor</b>: <?= $nombre_user; ?>
                    &nbsp;&nbsp;&nbsp;<b>Forma de pago</b>: <?= $codigotipo_pago; ?></td>
            </tr>
        </table>
    </div>
    <br>

    <!-- DETALLE -->
    <table class="cabecera" cellspacing="0">
        <tr>
            <th style="width: 12%;">CÓDIGO</th>
            <th style="width: 48%;">DESCRIPCIÓN</th>
            <th style="width: 10%;">CANT.</th>
            <th style="width: 15%;">P. UNIT.</th>
            <th style="width: 15%;">IMPORTE</th>
        </tr>
    </table>

    <table class="detalle" cellspacing="0">
        <?php
        if (isset($detalles) && is_array($detalles)) {
            foreach ($detalles as $regd) {
                $item += 1;
                $estilo = ($item % 2 == 0) ? '#DAF9FB' : '#F0F0F0';
                $precioV = ($regd['item_unit_price'] + $regd['tax_amount']) / $regd['quantity'];
                $importe = $precioV * $regd['quantity'];
        ?>
                <tr style="background-color: <?= $estilo; ?>;">
                    <td style="width: 12%; text-align: center;"><?= $regd['code']; ?></td>
                    <td style="width: 48%; text-align: left;"><?= $regd['articulo'] . " " . $regd['serie']; ?></td>
                    <td style="width: 10%; text-align: center;"><?= $regd['quantity']; ?></td>
                    <td style="width: 15%; text-align: right;"><?= number_format($precioV, 2, '.', ','); ?></td>
                    <td style="width: 15%; text-align: right;"><?= number_format($importe, 2, '.', ','); ?></td>
                </tr>
        <?php }
        } else {
            echo "<tr><td>Error: No se encontraron detalles para el ID proporcionado.</td></tr>";
        } ?>
    </table>
    <br>

    <!-- TOTALES -->
    <table class="totales" cellspacing="0">
        <tr class="titulo">
            <td style="width: 14%;">OP. GRAVADA</td>
            <td style="width: 14%;">OP. GRATUITA</td>
            <td style="width: 14%;">OP. EXONERADA</td>
            <td style="width: 14%;">OP. INAFECTA</td>
            <td style="width: 14%;">DESCTO TOTAL</td>
            <td style="width: 14%;">IGV (18%)</td>
            <td style="width: 16%;">PRECIO TOTAL</td>
        </tr>
        <tr>
            <td>S/ <?= number_format($op_gravadas, 2, '.', ','); ?></td>
            <td>S/ <?= number_format($op_gratuitas, 2, '.', ','); ?></td>
            <td>S/ <?= number_format($op_exoneradas, 2, '.', ','); ?></td>
            <td>S/ <?= number_format($op_inafectas, 2, '.', ','); ?></td>
            <td>S/ 0.00</td>
            <td>S/ <?= number_format($igv_asig, 2, '.', ','); ?></td>
            <td><b>S/ <?= number_format($total_venta, 2, '.', ','); ?></b></td>
        </tr>
    </table>
    <br>
    <div class="son">SON: <?= $leyenda ?></div>

    <page_footer>
        <div class="foot">
            <table cellspacing="0" cellpadding="0">
                <tr>
                    <td style="width: 88%; text-align: center;">
                        ¡¡¡ GRACIAS POR SU COMPRA VUELVA PRONTO !!!<br>
                        Representación impresa de la <?= $tipo_voucher; ?><br>
                        Emitida del sistema del contribuyente autorizado con fecha
                        <b><?= $fmt_fecha($fecha_inicio); ?></b><br>
                        Puede consultar su comprobante electrónico utilizando su clave SOL, en la plataforma de SUNAT. <?= $web; ?>
                    </td>
                    <td style="width: 12%; text-align: center;">
                        <?php
                        include_once "vendor/phpqrcode/qrlib.php";
                        $tamaño = 2;
                        $level = 'Q';
                        $framSize = 1;
                        $contenido = $rucE . '|' .  $codeVoucher . '|' . $serie . '|' . $correlativo . '|' . $igv_asig . '|' . $total_venta . '|' . $fecha . '|' . $tipo_documento_cliente . '|' . $rucC . '|';
                        ob_start();
                        QRcode::png($contenido, null, $level, $tamaño, $framSize);
                        $imageString = base64_encode(ob_get_contents());
                        ob_end_clean();
                        echo '<img src="data:image/png;base64,' . $imageString . '" />';
                        ?>
                    </td>
                </tr>
            </table>
        </div>
    </page_footer>
</body>

</html>
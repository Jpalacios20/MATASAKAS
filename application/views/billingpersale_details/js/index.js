$(document).ready(function () {

    // ============================================================
    // VARIABLES
    // ============================================================

    window.id_campus = null;
    window.current_user_id = null;


    // ============================================================
    // CREAR FACTURA
    // ============================================================

    function create_billingpersale(form) {

        let formData = new FormData(form);

        formData.append("id_campus", window.id_campus);
        formData.append("id_user", window.current_user_id);

        $.ajax({
            url: BASE_URL + "Billingpersale_Details/create_billingpersale",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,

            beforeSend: function () {
                $(".btn-save").prop("disabled", true);
            },

            success: function (response) {

                if (response.status === "OK") {

                    functions.toast_message(
                        "success",
                        response.msg,
                        "ÉXITO"
                    );

                    setTimeout(function () {
                        window.location.assign(
                            BASE_URL + "Billingpersale"
                        );
                    }, 1200);

                } else {

                    functions.toast_message(
                        response.type || "warning",
                        response.msg,
                        "AVISO"
                    );

                    $(".btn-save").prop("disabled", false);
                }
            },

            error: function (xhr) {

                console.log(xhr.responseText);

                functions.toast_message(
                    "error",
                    "Ocurrió un error al crear la factura.",
                    "ERROR"
                );

                $(".btn-save").prop("disabled", false);
            }
        });
    }


    // ============================================================
    // SUBMIT FORMULARIO
    // ============================================================

    $(document).on("submit", "#form-billingpersale", function (e) {

        e.preventDefault();

        create_billingpersale(this);
    });



    // ============================================================
    // DATOS DE VENTA DESDE LA BASE DE DATOS
    // ============================================================

    function cargarSelect(selector, data, placeholder) {
        let html = '<option value="">' + (placeholder || 'Seleccionar') + '</option>';
        (data || []).forEach(function (element) {
            html += '<option value="' + element.id + '">' +
                $('<div>').text(element.description || element.name || '').html() +
                '</option>';
        });
        $(selector).html(html).trigger('change.select2');
    }

    // Clientes: tabla person
    function get_business_name_cli() {
        $.ajax({
            url: BASE_URL + 'Clients/get_business_name_cli',
            type: 'GET',
            dataType: 'json',
            cache: false,
            success: function (data) {
                if (data.status !== 'OK') {
                    console.error('No se pudieron cargar los clientes:', data.msg);
                    return;
                }

                let html = '<option value="">Seleccionar</option>';
                (data.data || []).forEach(function (element) {
                    html += '<option value="' + element.id + '"' +
                        ' data-document-number="' + $('<div>').text(element.document_number || '').html() + '"' +
                        ' data-address="' + $('<div>').text(element.address || '').html() + '">' +
                        $('<div>').text(element.business_name || element.name || '').html() +
                        '</option>';
                });

                $('#form-billingpersale select[name="business_name_cli"]').html(html).trigger('change.select2');
            },
            error: function (xhr) {
                console.error('Error cargando clientes:', xhr.responseText);
            }
        });
    }

    // Forma de pago: tabla payment_shape
    function get_payment_method() {
        $.ajax({
            url: BASE_URL + 'Main/get_payment_method',
            type: 'GET',
            dataType: 'json',
            cache: false,
            success: function (data) {
                if (data.status === 'OK') {
                    cargarSelect('#form-billingpersale select[name="fp_description"]', data.data, 'Seleccionar');
                } else {
                    console.error('No se pudo cargar la forma de pago:', data.msg);
                }
            },
            error: function (xhr) {
                console.error('Error cargando forma de pago:', xhr.responseText);
            }
        });
    }

    // Tipo de comprobante: tabla voucher_type
    function get_voucher_type() {
        $.ajax({
            url: BASE_URL + 'Main/get_voucher_type',
            type: 'GET',
            dataType: 'json',
            cache: false,
            success: function (data) {
                if (data.status === 'OK') {
                    cargarSelect('#form-billingpersale select[name="vt_description"]', data.data, 'Seleccionar');
                } else {
                    console.error('No se pudo cargar el tipo de comprobante:', data.msg);
                }
            },
            error: function (xhr) {
                console.error('Error cargando tipo de comprobante:', xhr.responseText);
            }
        });
    }

    // Medio de pago: tabla payment_type
    function get_payment_type() {
        $.ajax({
            url: BASE_URL + 'Main/get_payment_type',
            type: 'GET',
            dataType: 'json',
            cache: false,
            success: function (data) {
                if (data.status === 'OK') {
                    cargarSelect('#form-billingpersale select[name="pt_description"]', data.data, 'Seleccionar');
                } else {
                    console.error('No se pudo cargar el medio de pago:', data.msg);
                }
            },
            error: function (xhr) {
                console.error('Error cargando medio de pago:', xhr.responseText);
            }
        });
    }

    // Moneda: tabla coin
    function get_coins() {
        $.ajax({
            url: BASE_URL + 'Main/get_coins',
            type: 'GET',
            dataType: 'json',
            cache: false,
            success: function (data) {
                if (data.status === 'OK' && data.data && data.data.length) {
                    const coin = data.data[0];
                    $('#form-billingpersale span[name="coins"]').text(coin.code || coin.description || '');
                    $('#form-billingpersale input[name="coins"]').val(coin.code || coin.description || '');
                }
            },
            error: function (xhr) {
                console.error('Error cargando moneda:', xhr.responseText);
            }
        });
    }

    // IGV: tabla igv
    function get_igv() {
        $.ajax({
            url: BASE_URL + 'Main/get_igv',
            type: 'GET',
            dataType: 'json',
            cache: false,
            success: function (data) {
                if (data.status === 'OK' && data.data && data.data.length) {
                    $('#form-billingpersale input[name="igv"]').val(data.data[0].value);
                }
            },
            error: function (xhr) {
                console.error('Error cargando IGV:', xhr.responseText);
            }
        });
    }

    // Al elegir una persona, traer sus datos desde person ya devueltos por el endpoint.
    $(document).on('change', '#form-billingpersale select[name="business_name_cli"]', function () {
        const option = $(this).find('option:selected');
        $('#form-billingpersale input[name="document_number_cli"]').val(option.data('document-number') || '');
        $('#form-billingpersale input[name="address_cli"]').val(option.data('address') || '');
    });

    // ============================================================
    // PRODUCTOS
    // ============================================================

    function destroy_datatable_income_products() {

        if (
            $.fn.DataTable.isDataTable(
                "#datatables-income-products"
            )
        ) {

            $("#datatables-income-products")
                .DataTable()
                .destroy();
        }
    }


    function refresh_datatable_income_products() {

        if (
            $.fn.DataTable.isDataTable(
                "#datatables-income-products"
            )
        ) {

            $("#datatables-income-products")
                .DataTable()
                .ajax
                .reload();
        }
    }


    function load_datatable_income_products() {

        destroy_datatable_income_products();

        let dataTable =
            $("#datatables-income-products").DataTable({

                ajax: {

                    url:
                        BASE_URL +
                        "Billingpersale/get_salesproduct",

                    type: "GET",

                    cache: false,

                    dataSrc: function (json) {

                        console.log(
                            "Productos:",
                            json
                        );

                        if (
                            json.status === "OK"
                        ) {

                            return json.result;
                        }

                        return [];
                    }
                },

                columns: [

                    // OPCIONES
                    {
                        class: "center",
                        orderable: false,
                        render: function (data, type, row) {
                            return `
                                <button
                                    type="button"
                                    class="btn btn-warning btn-sm btn-round btn-icon btn_add"
                                    data-process-key="${row.id_salesproduct}"
                                    title="Agregar producto">
                                    +
                                </button>
                            `;
                        }
                    },

                    // NOMBRE
                    { data: "product_name" },

                    // UNIDAD
                    { data: "unit_type" },

                    // CATEGORIA
                    {
                        data: "category_name",
                        defaultContent: ""
                    },

                    // CODIGO
                    { data: "product_sku" },

                    // STOCK
                    { data: "product_stock" },

                    // PRECIO VENTA
                    {
                        data: "price_sell",
                        render: function (data) {
                            return parseFloat(data || 0).toFixed(2);
                        }
                    },

                    // IMAGEN
                    {
                        data: "image_url",
                        orderable: false,
                        render: function (data) {
                            if (!data) return "";
                            return `<img src="${data}" alt="" style="width:45px;height:45px;object-fit:cover;border-radius:4px;">`;
                        }
                    },

                    // AFECTACION
                    {
                        data: "affectation",
                        defaultContent: "Gravado"
                    }
                ],

                scrollX: true,
                autoWidth: false,

                language: {
                    url:
                        BASE_URL +
                        "public/assets/json/languaje-es.json"
                }
            });


        dataTable.on(
            "xhr",
            function () {

                let data =
                    dataTable.ajax.json();

                if (
                    data &&
                    data.status !== "OK"
                ) {

                    functions.toast_message(
                        "error",
                        "No se pudieron cargar los productos.",
                        "ERROR"
                    );
                }
            }
        );
    }


    // ============================================================
    // AGREGAR PRODUCTO
    // ============================================================

    $(document).on(
        "click",
        ".btn_add",
        function () {

            let value =
                $(this).attr(
                    "data-process-key"
                );


            let tributo =
                $(
                    'select.tributo-select[data-product-id="' +
                    value +
                    '"]'
                ).val();


            // Evitar producto repetido
            let existingRow =
                $(
                    '#add_products tr[data-product-id="' +
                    value +
                    '"][data-tributo="' +
                    tributo +
                    '"]'
                );


            if (
                existingRow.length > 0
            ) {

                functions.toast_message(
                    "info",
                    "Ya existe el producto.",
                    "INFORMACIÓN"
                );

                return;
            }


            // Obtener DataTable
            let table =
                $(
                    "#datatables-income-products"
                ).DataTable();


            let rowData = null;


            table.rows().every(
                function () {

                    let data =
                        this.data();

                    if (
                        String(
                            data.id_salesproduct
                        ) ===
                        String(value)
                    ) {

                        rowData = data;
                    }
                }
            );


            if (!rowData) {

                functions.toast_message(
                    "error",
                    "No se encontró el producto.",
                    "ERROR"
                );

                return;
            }


            rowData.tributo =
                tributo;


            $("#add_products").append(
                getHtml(rowData)
            );


            updateTotals();
        }
    );


    // ============================================================
    // HTML PRODUCTO AGREGADO
    // ============================================================

    function getHtml(item) {

        const tributo =
            item.tributo || "IGV";


        const precio =
            parseFloat(
                item.price_sell
            ) || 0;


        const stock =
            parseInt(
                item.product_stock
            ) || 0;


        const valores =
            calcularValores(
                precio,
                1,
                tributo
            );


        return `

            <tr
                class="table table-lg"
                data-product-id="${item.id_salesproduct}"
                data-tributo="${tributo}">

                <!-- ELIMINAR -->

                <td class="border text-center">

                    <button
                        type="button"
                        class="btn btn-danger btn-sm btn-delete-product">

                        X

                    </button>

                </td>


                <!-- CÓDIGO -->

                <td class="border">

                    <span>
                        ${item.product_sku}
                    </span>

                    <!-- IMPORTANTE:
                         Aquí va id_product ORIGINAL -->

                    <input
                        type="hidden"
                        name="id_product[]"
                        value="${item.id_product}">

                    <input
                        type="hidden"
                        name="code[]"
                        value="${item.product_sku}">

                </td>


                <!-- PRODUCTO -->

                <td class="border">

                    <span>
                        ${item.product_name}
                    </span>

                    <input
                        type="hidden"
                        name="name[]"
                        value="${item.product_name}">

                </td>


                <!-- SERIE -->

                <td class="border">

                    <input
                        type="text"
                        name="serie[]"
                        value=" "
                        class="form-control">

                </td>


                <!-- UNIDAD -->

                <td class="border">

                    <span>
                        ${item.unit_type ?? ""}
                    </span>

                    <input
                        type="hidden"
                        name="u_medida[]"
                        value="${item.unit_type ?? ""}">

                </td>


                <!-- CANTIDAD -->

                <td class="border">

                    <input
                        type="number"
                        name="cantidad[]"
                        value="1"
                        min="1"
                        max="${stock}"
                        class="form-control cantidad-input"
                        data-stock="${stock}"
                        style="width:50px">

                </td>


                <!-- PRECIO UNITARIO -->

                <td class="border">

                    <span name="price_u[]">
                        ${parseFloat(
                            valores.VentaU
                        ).toFixed(2)}
                    </span>

                    <input
                        type="hidden"
                        name="price_u[]"
                        value="${valores.VentaU}">

                </td>


                <!-- TRIBUTO -->

                <td class="border">

                    <span name="tributo[]">
                        ${tributo}
                    </span>

                    <input
                        type="hidden"
                        name="tributo[]"
                        value="${tributo}">

                </td>


                <!-- IMPUESTO -->

                <td class="border">

                    <span
                        name="impuesto[]"
                        class="impuesto-span">

                        ${valores.Impuesto}

                    </span>

                    <input
                        type="hidden"
                        name="impuesto[]"
                        value="${valores.Impuesto}">

                </td>


                <!-- PRECIO -->

                <td class="border">

                    <input
                        type="number"
                        class="form-control price-input"
                        name="price[]"
                        value="${valores.Newprice}"
                        step="0.01">

                </td>


                <!-- VENTA TOTAL -->

                <td class="border">

                    <span
                        name="venta_t[]"
                        class="venta_total">

                        ${valores.venta_total}

                    </span>

                    <input
                        type="hidden"
                        name="venta_t[]"
                        value="${valores.venta_total}">

                </td>


                <!-- IMPORTE -->

                <td class="border">

                    <span
                        name="importe[]"
                        class="importe">

                        ${valores.importe}

                    </span>

                    <input
                        type="hidden"
                        name="importe[]"
                        value="${valores.importe}">

                </td>

            </tr>

        `;
    }


    // ============================================================
    // CALCULAR VALORES
    // ============================================================

    function calcularValores(
        precio,
        cantidad,
        tributo
    ) {

        precio =
            parseFloat(precio) || 0;

        cantidad =
            parseInt(cantidad) || 1;


        let impuesto = 0;
        let ventaU = precio;
        let newPrice = precio;
        let ventaTotal = 0;
        let importe = 0;


        if (tributo === "IGV") {

            ventaU =
                precio / 1.18;

            impuesto =
                (precio - ventaU) *
                cantidad;

            ventaTotal =
                precio *
                cantidad;

            importe =
                ventaTotal;

            newPrice =
                precio;

        } else if (
            tributo === "EXO" ||
            tributo === "INA"
        ) {

            ventaU =
                precio;

            impuesto = 0;

            ventaTotal =
                precio *
                cantidad;

            importe =
                ventaTotal;

            newPrice =
                precio;

        } else if (
            tributo === "GRA"
        ) {

            ventaU =
                precio;

            impuesto = 0;

            ventaTotal = 0;

            importe = 0;

            newPrice =
                0;
        }


        return {

            VentaU:
                ventaU.toFixed(2),

            Impuesto:
                impuesto.toFixed(2),

            Newprice:
                newPrice.toFixed(2),

            venta_total:
                ventaTotal.toFixed(2),

            importe:
                importe.toFixed(2)
        };
    }


    // ============================================================
    // CAMBIO DE CANTIDAD
    // ============================================================

    $(document).on(
        "input",
        ".cantidad-input",
        function () {

            let cantidad =
                parseInt(
                    $(this).val()
                ) || 1;


            let stock =
                parseInt(
                    $(this).data("stock")
                ) || 0;


            if (
                cantidad < 1
            ) {

                cantidad = 1;
            }


            if (
                cantidad > stock
            ) {

                cantidad = stock;

                functions.toast_message(
                    "warning",
                    "La cantidad supera el stock disponible.",
                    "STOCK"
                );
            }


            $(this).val(
                cantidad
            );


            actualizarFila(
                $(this).closest("tr")
            );
        }
    );


    // ============================================================
    // CAMBIO DE PRECIO
    // ============================================================

    $(document).on(
        "input",
        ".price-input",
        function () {

            actualizarFila(
                $(this).closest("tr")
            );
        }
    );


    // ============================================================
    // CAMBIO DE TRIBUTO
    // ============================================================

    $(document).on(
        "change",
        ".tributo-select",
        function () {

            let productId =
                $(this).data(
                    "product-id"
                );

            let tributo =
                $(this).val();


            let row =
                $(
                    '#add_products tr[data-product-id="' +
                    productId +
                    '"]'
                );


            if (
                row.length > 0
            ) {

                row.attr(
                    "data-tributo",
                    tributo
                );

                row.find(
                    'span[name="tributo[]"]'
                ).text(
                    tributo
                );

                row.find(
                    'input[name="tributo[]"]'
                ).val(
                    tributo
                );

                actualizarFila(row);
            }
        }
    );


    // ============================================================
    // ACTUALIZAR FILA
    // ============================================================

    function actualizarFila(row) {

        let cantidad =
            parseInt(
                row.find(
                    ".cantidad-input"
                ).val()
            ) || 1;


        let precio =
            parseFloat(
                row.find(
                    ".price-input"
                ).val()
            ) || 0;


        let tributo =
            row.find(
                'input[name="tributo[]"]'
            ).val();


        let valores =
            calcularValores(
                precio,
                cantidad,
                tributo
            );


        row.find(
            'span[name="price_u[]"]'
        ).text(
            valores.VentaU
        );


        row.find(
            'input[name="price_u[]"]'
        ).val(
            valores.VentaU
        );


        row.find(
            ".impuesto-span"
        ).text(
            valores.Impuesto
        );


        row.find(
            'input[name="impuesto[]"]'
        ).val(
            valores.Impuesto
        );


        row.find(
            ".venta_total"
        ).text(
            valores.venta_total
        );


        row.find(
            'input[name="venta_t[]"]'
        ).val(
            valores.venta_total
        );


        row.find(
            ".importe"
        ).text(
            valores.importe
        );


        row.find(
            'input[name="importe[]"]'
        ).val(
            valores.importe
        );


        updateTotals();
    }


    // ============================================================
    // TOTALES
    // ============================================================

    function updateTotals() {

        let tv_gravado = 0;
        let tv_exonerado = 0;
        let tv_inafectas = 0;
        let tv_gratuitas = 0;

        let total_igv = 0;
        let total_importe = 0;


        $("#add_products tr").each(
            function () {

                const tributo =
                    $(this)
                        .find(
                            'span[name="tributo[]"]'
                        )
                        .text().trim().toUpperCase();


                const importe =
                    parseFloat(
                        $(this)
                            .find(".importe")
                            .text()
                    ) || 0;


                const impuesto =
                    parseFloat(
                        $(this)
                            .find(".impuesto-span")
                            .text()
                    ) || 0;


                const ventaU =
                    parseFloat(
                        $(this)
                            .find(
                                'input[name="price_u[]"]'
                            )
                            .val()
                    ) || 0;


                const cantidad =
                    parseInt(
                        $(this)
                            .find(
                                ".cantidad-input"
                            )
                            .val()
                    ) || 0;


                switch (tributo) {

                    case "IGV":

                        tv_gravado +=
                            importe - impuesto;

                        total_igv +=
                            impuesto;

                        break;


                    case "EXO":

                        tv_exonerado +=
                            importe;

                        break;


                    case "INA":

                        tv_inafectas +=
                            importe;

                        break;


                    case "GRA":

                        tv_gratuitas +=
                            ventaU *
                            cantidad;

                        break;
                }


                if (
                    tributo !== "GRA"
                ) {

                    total_importe +=
                        importe;
                }
            }
        );


        $("#totalg").text(
            tv_gravado.toFixed(2)
        );

        $("#tv_gravado").val(
            tv_gravado.toFixed(2)
        );


        $("#totale").text(
            tv_exonerado.toFixed(2)
        );

        $("#tv_exonerado").val(
            tv_exonerado.toFixed(2)
        );


        $("#totali").text(
            tv_inafectas.toFixed(2)
        );

        $("#tv_inafectas").val(
            tv_inafectas.toFixed(2)
        );


        $("#totalgt").text(
            tv_gratuitas.toFixed(2)
        );

        $("#tv_gratuitas").val(
            tv_gratuitas.toFixed(2)
        );


        $("#totaligv").text(
            total_igv.toFixed(2)
        );

        $("#total_igv").val(
            total_igv.toFixed(2)
        );


        $("#totalimp").text(
            total_importe.toFixed(2)
        );

        $("#total_importe").val(
            total_importe.toFixed(2)
        );
    }


    // ============================================================
    // ELIMINAR PRODUCTO
    // ============================================================

    $(document).on(
        "click",
        ".btn-delete-product",
        function () {

            $(this)
                .closest("tr")
                .remove();

            updateTotals();
        }
    );


    // ============================================================
    // CARGAR PRODUCTOS
    // ============================================================

    load_datatable_income_products();


    // ============================================================
    // FUNCIONES EXISTENTES
    // ============================================================

    get_payment_type();
    get_payment_method();
    get_voucher_type();
    get_business_name_cli();
    get_coins();
    get_igv();

    if (typeof get_date === "function") {
        get_date();
    }


    if (typeof get_campus === "function") {
        get_campus();
    }

});
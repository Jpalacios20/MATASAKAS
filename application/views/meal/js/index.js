$(document).ready(function () {

    let cart = [];

    // =========================================================
    // INICIO
    // =========================================================

    load_categories_comanda();
    load_meals_comanda();
    update_cart_ui();


    // =========================================================
    // 1. CARGAR CATEGORÍAS
    // =========================================================

    function load_categories_comanda() {

        $.ajax({
            url: BASE_URL + 'Meal/get_categories',
            type: 'GET',
            dataType: 'json',

            success: function (response) {

                if (
                    response.status === 'OK' &&
                    Array.isArray(response.data)
                ) {

                    let html = `
                        <button
                            type="button"
                            class="btn btn-outline-primary active btn-sm filter-cat mb-1"
                            data-id="all">
                            Todas
                        </button>
                    `;

                    response.data.forEach(function (cat) {

                        let catId =
                            cat.id_category ||
                            cat.id ||
                            '';

                        let catName =
                            cat.name_category ||
                            cat.description ||
                            '';

                        if (catName !== '') {

                            html += `
                                <button
                                    type="button"
                                    class="btn btn-outline-primary btn-sm filter-cat mb-1"
                                    data-id="${catId}">
                                    ${catName}
                                </button>
                            `;
                        }
                    });

                    $('#category_filters').html(html);

                } else {

                    console.log(
                        'No se encontraron categorías:',
                        response
                    );
                }
            },

            error: function (xhr, status, error) {

                console.error(
                    'Error al cargar categorías:',
                    error
                );
            }
        });
    }


    // =========================================================
    // 2. CARGAR COMIDAS + PRODUCTOS
    // =========================================================

    function load_meals_comanda() {

        $.ajax({

            // IMPORTANTE:
            // Antes estaba Meal/get_meal
            // Eso solo cargaba comidas.
            //
            // Ahora usamos get_meals_comanda
            // que carga comidas + productos.

            url: BASE_URL + 'Meal/get_meals_comanda',

            type: 'GET',

            dataType: 'json',

            success: function (response) {

                console.log(
                    'Respuesta productos/comidas:',
                    response
                );

                if (
                    response.status === 'OK' &&
                    Array.isArray(response.data)
                ) {

                    render_meals(response.data);

                } else {

                    $('#grid_meals_container').html(`
                        <div class="col-12 text-center text-muted py-3">
                            No hay productos disponibles.
                        </div>
                    `);
                }
            },

            error: function (xhr, status, error) {

                console.error(
                    'Error al cargar comidas y productos:',
                    xhr.responseText
                );

                $('#grid_meals_container').html(`
                    <div class="col-12 text-center text-danger py-3">
                        Error al cargar los productos.
                    </div>
                `);
            }
        });
    }


    // =========================================================
    // 3. MOSTRAR COMIDAS Y PRODUCTOS
    // =========================================================

    function render_meals(items) {

        let html = '';

        if (
            !items ||
            items.length === 0
        ) {

            $('#grid_meals_container').html(`
                <div class="col-12 text-center text-muted py-3">
                    No se encontraron productos.
                </div>
            `);

            return;
        }


        items.forEach(function (item) {

            let id =
                item.id_meal;

            let name =
                item.meal_name ||
                item.name_meal ||
                'Producto';


            let price =
                parseFloat(
                    item.meal_price ||
                    item.price_meal ||
                    0
                );


            let categoryId =
                item.id_category ||
                '';


            let categoryName =
                item.name_category ||
                item.description ||
                '';


            let itemType =
                item.item_type ||
                'MEAL';


            html += `

                <div
                    class="col-6 col-md-4 col-lg-3 meal-card mb-3"
                    data-category="${categoryId}"
                    data-name="${name.toLowerCase()}"
                    data-type="${itemType}"
                >

                    <div
                        class="card border text-center p-2 btn-add-item shadow-sm"
                        style="
                            cursor:pointer;
                            height:100%;
                        "

                        data-id="${id}"
                        data-name="${name}"
                        data-price="${price}"
                    >

                        <h6
                            class="fw-bold mb-1 text-truncate"
                            title="${name}">
                            ${name}
                        </h6>

                        <span
                            class="badge bg-light-primary mb-1">
                            ${categoryName}
                        </span>

                        <p
                            class="text-primary fw-bold mb-0">
                            S/ ${price.toFixed(2)}
                        </p>

                    </div>

                </div>

            `;
        });


        $('#grid_meals_container').html(html);
    }


    // =========================================================
    // 4. BUSCADOR
    // =========================================================

    $(document).on(
        'keyup',
        '#search_meal',
        function () {

            let query =
                $(this)
                    .val()
                    .toLowerCase()
                    .trim();


            $('.meal-card').each(
                function () {

                    let name =
                        String(
                            $(this).data('name') || ''
                        ).toLowerCase();


                    if (
                        name.includes(query)
                    ) {

                        $(this).show();

                    } else {

                        $(this).hide();
                    }
                }
            );
        }
    );


    // =========================================================
    // 5. FILTRAR POR CATEGORÍA
    // =========================================================

    $(document).on(
        'click',
        '.filter-cat',
        function () {

            $('.filter-cat')
                .removeClass('active');

            $(this)
                .addClass('active');


            let catId =
                String(
                    $(this).data('id')
                );


            $('.meal-card').each(
                function () {

                    let cat =
                        String(
                            $(this).data('category')
                        );


                    if (
                        catId === 'all' ||
                        cat === catId
                    ) {

                        $(this).show();

                    } else {

                        $(this).hide();
                    }
                }
            );
        }
    );


    // =========================================================
    // 6. AGREGAR PRODUCTO A LA COMANDA
    // =========================================================

    $(document).on(
        'click',
        '.btn-add-item',
        function () {

            let id =
                $(this).data('id');


            let name =
                $(this).data('name');


            let price =
                parseFloat(
                    $(this).data('price')
                );


            let existingItem =
                cart.find(
                    item => String(item.id) === String(id)
                );


            if (existingItem) {

                existingItem.quantity += 1;

            } else {

                cart.push({

                    id: id,

                    name: name,

                    price: price,

                    quantity: 1
                });
            }


            update_cart_ui();
        }
    );
$(document).ready(function () {

    const ESTADOS = {
        PENDIENTE: { col: '#col_pendientes',  next: 'EN_PREPARACION', label: 'Iniciar preparación', btn: 'btn-warning' },
        EN_PREPARACION: { col: '#col_preparacion', next: 'LISTO', label: 'Marcar listo', btn: 'btn-success' },
        LISTO: { col: '#col_listos', next: null, label: '', btn: '' }
    };

    load_kitchen_orders();

    // =========================================================
    // 1. CARGAR COMANDAS
    // =========================================================
    function load_kitchen_orders() {
        $.ajax({
            url: BASE_URL + 'Kitchen/get_orders',
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                if (response.status === 'OK' && Array.isArray(response.data)) {
                    render_kitchen(response.data);
                    $('#last_update').text(new Date().toLocaleTimeString());
                } else {
                    console.log('Error cocina:', response);
                }
            },
            error: function (xhr, status, error) {
                console.error('Error al cargar cocina:', xhr.responseText);
            }
        });
    }

    // =========================================================
    // 2. PINTAR TARJETAS
    // =========================================================
    function esc(t) {
        return $('<div>').text(t == null ? '' : t).html();
    }

    function render_kitchen(orders) {
        let cols = { PENDIENTE: '', EN_PREPARACION: '', LISTO: '' };

        orders.forEach(function (o) {
            let cfg = ESTADOS[o.status];
            if (!cfg) return;

            // TODO: confirmar nombres de campos con Kitchen_model.php
            let items = (o.items || []).map(function (i) {
                return `<li>${esc(i.quantity)}x ${esc(i.meal_name)}</li>`;
            }).join('');

            let boton = cfg.next ? `
                <button type="button"
                    class="btn btn-sm ${cfg.btn} w-100 btn-change-status"
                    data-id="${o.id_order}"
                    data-status="${cfg.next}">
                    ${cfg.label}
                </button>` : '';

            cols[o.status] += `
                <div class="card border shadow-sm mb-2">
                    <div class="card-body p-2">
                        <h6 class="fw-bold mb-1">#${esc(o.id_order)} - ${esc(o.table_name)}</h6>
                        <ul class="small mb-1 ps-3">${items}</ul>
                        ${o.observations ? `<p class="small text-muted mb-2">${esc(o.observations)}</p>` : ''}
                        ${boton}
                    </div>
                </div>`;
        });

        Object.keys(ESTADOS).forEach(function (k) {
            let html = cols[k] || '<div class="text-center text-muted py-3">Sin comandas</div>';
            $(ESTADOS[k].col).html(html);
        });
    }

    // =========================================================
    // 3. CAMBIAR ESTADO
    // =========================================================
    $(document).on('click', '.btn-change-status', function () {
        let $btn = $(this);
        $btn.prop('disabled', true);

        $.ajax({
            url: BASE_URL + 'Kitchen/update_status',
            type: 'POST',
            contentType: 'application/json; charset=utf-8',
            data: JSON.stringify({
                id_order: parseInt($btn.data('id')),
                status: $btn.data('status')
            }),
            dataType: 'json',
            success: function (response) {
                if (response.status === 'OK') {
                    load_kitchen_orders();
                } else {
                    alert(response.msg || 'No se pudo actualizar la comanda.');
                    $btn.prop('disabled', false);
                }
            },
            error: function (xhr) {
                console.error('Error update_status:', xhr.responseText);
                alert('Ocurrió un error al conectar con el servidor.');
                $btn.prop('disabled', false);
            }
        });
    });

    // =========================================================
    // 4. BOTÓN ACTUALIZAR
    // =========================================================
    $(document).on('click', '#btn_actualizar', load_kitchen_orders);
});

    // =========================================================
    // 7. ACTUALIZAR CARRITO
    // =========================================================

    function update_cart_ui() {

        let tbody = '';

        let total = 0;


        if (cart.length === 0) {

            tbody = `
                <tr>
                    <td
                        colspan="4"
                        class="text-center text-muted py-3">
                        No hay productos agregados.
                    </td>
                </tr>
            `;

            $('#comanda_total_amount')
                .text('S/ 0.00');


            $(
                '#table_comanda_items tbody, #detalle_comanda_body'
            ).html(tbody);

            return;
        }


        cart.forEach(
            function (item, index) {

                let subtotal =
                    item.price *
                    item.quantity;


                total += subtotal;


                tbody += `

                    <tr>

                        <td
                            class="text-start text-truncate"
                            style="max-width:110px;"
                            title="${item.name}">

                            ${item.name}

                        </td>


                        <td>

                            <div
                                class="input-group input-group-sm justify-content-center">

                                <button
                                    type="button"
                                    class="btn btn-outline-secondary btn-decrease px-1"
                                    data-index="${index}">
                                    -
                                </button>


                                <input
                                    type="text"
                                    class="form-control text-center px-1"
                                    value="${item.quantity}"
                                    readonly
                                    style="max-width:38px;"
                                >


                                <button
                                    type="button"
                                    class="btn btn-outline-secondary btn-increase px-1"
                                    data-index="${index}">
                                    +
                                </button>

                            </div>

                        </td>


                        <td class="fw-bold align-middle">

                            S/
                            ${subtotal.toFixed(2)}

                        </td>


                        <td class="align-middle">

                            <button
                                type="button"
                                class="btn btn-sm btn-icon text-danger btn-remove"
                                data-index="${index}">
                                &times;
                            </button>

                        </td>

                    </tr>
                `;
            }
        );


        $(
            '#table_comanda_items tbody, #detalle_comanda_body'
        ).html(tbody);


        $('#comanda_total_amount')
            .text(
                'S/ ' +
                total.toFixed(2)
            );
    }


    // =========================================================
    // 8. AUMENTAR CANTIDAD
    // =========================================================

    $(document).on(
        'click',
        '.btn-increase',
        function () {

            let index =
                $(this).data('index');


            if (
                cart[index]
            ) {

                cart[index].quantity += 1;

                update_cart_ui();
            }
        }
    );


    // =========================================================
    // 9. DISMINUIR CANTIDAD
    // =========================================================

    $(document).on(
        'click',
        '.btn-decrease',
        function () {

            let index =
                $(this).data('index');


            if (!cart[index]) {
                return;
            }


            if (
                cart[index].quantity > 1
            ) {

                cart[index].quantity -= 1;

            } else {

                cart.splice(
                    index,
                    1
                );
            }


            update_cart_ui();
        }
    );


    // =========================================================
    // 10. ELIMINAR PRODUCTO
    // =========================================================

    $(document).on(
        'click',
        '.btn-remove',
        function () {

            let index =
                $(this).data('index');


            cart.splice(
                index,
                1
            );


            update_cart_ui();
        }
    );


    // =========================================================
    // 11. CANCELAR / LIMPIAR
    // =========================================================

    $(document).on(
        'click',
        '#btn_cancel_comanda, .btn-cancel-comanda',
        function () {

            cart = [];


            $('#comanda_reference')
                .val('');


            $('#comanda_notes')
                .val('');


            update_cart_ui();
        }
    );


    // =========================================================
    // 12. GENERAR COMANDA
    // =========================================================

    $(document).on(
        'click',
        '#btn_generar_comanda',
        function (e) {

            e.preventDefault();


            if (
                cart.length === 0
            ) {

                alert(
                    'Por favor, agregue al menos un producto antes de generar la comanda.'
                );

                return;
            }


            let reference =
                $('#comanda_reference')
                    .val()
                    .trim();


            let notes =
                $('#comanda_notes')
                    .val()
                    .trim();


            if (
                reference === ''
            ) {

                reference =
                    'Mesa General';
            }


            let totalAmount =
                cart.reduce(
                    function (
                        sum,
                        item
                    ) {

                        return sum +
                            (
                                item.price *
                                item.quantity
                            );

                    },
                    0
                );


            /*
             * IMPORTANTE:
             *
             * Tu PHP espera:
             *
             * table
             * observations
             * items
             *
             * NO espera:
             *
             * table_name
             * details
             *
             */

            let comandaData = {

                table: reference,

                observations: notes,

                items: cart.map(
                    function (item) {

                        return {

                            id_meal:
                                parseInt(
                                    item.id
                                ),

                            quantity:
                                parseInt(
                                    item.quantity
                                )
                        };
                    }
                )
            };


            console.log(
                'Datos enviados:',
                comandaData
            );


            $.ajax({

                /*
                 * IMPORTANTE:
                 *
                 * NO usar:
                 * C_Meal/create_comanda
                 *
                 * Bootstrap ya agrega C_
                 */

                url:
                    BASE_URL +
                    'Meal/create_comanda',

                type: 'POST',

                contentType:
                    'application/json; charset=utf-8',

                data:
                    JSON.stringify(
                        comandaData
                    ),

                dataType:
                    'json',

                success:
                    function (response) {

                        console.log(
                            'Respuesta crear comanda:',
                            response
                        );


                        if (
                            response.status === 'OK'
                        ) {

                            let data =
                                response.data ||
                                response.result ||
                                {};


                            let idComanda =
                                data.id_comanda ||
                                '001';


                            imprimirTicketRecibo(

                                reference,

                                notes,

                                cart,

                                totalAmount,

                                idComanda
                            );


                            cart = [];


                            $('#comanda_reference')
                                .val('');


                            $('#comanda_notes')
                                .val('');


                            update_cart_ui();

                        } else {

                            alert(
                                response.msg ||
                                'No se pudo registrar la comanda.'
                            );
                        }
                    },


                error:
                    function (
                        xhr,
                        status,
                        error
                    ) {

                        console.error(
                            'Error AJAX:',
                            error
                        );


                        console.error(
                            'Respuesta servidor:',
                            xhr.responseText
                        );


                        alert(
                            'Ocurrió un error al conectar con el servidor.'
                        );
                    }
            });
        }
    );


    // =========================================================
    // 13. IMPRIMIR TICKET
    // =========================================================

    function imprimirTicketRecibo(
        mesa,
        notas,
        items,
        total,
        idComanda
    ) {

        let fechaHora =
            new Date().toLocaleString();


        let htmlItems = '';


        items.forEach(
            function (item) {

                let subtotal =
                    item.price *
                    item.quantity;


                htmlItems += `

                    <tr>

                        <td
                            style="text-align:left;">
                            ${item.quantity}x
                            ${item.name}
                        </td>

                        <td
                            style="text-align:right;">
                            S/
                            ${subtotal.toFixed(2)}
                        </td>

                    </tr>
                `;
            }
        );


        let ventanaTicket =
            window.open(
                '',
                '_blank',
                'width=340,height=550'
            );


        if (!ventanaTicket) {

            alert(
                'El navegador bloqueó la ventana de impresión. Permite las ventanas emergentes.'
            );

            return;
        }


        ventanaTicket.document.write(`

            <html>

                <head>

                    <title>
                        Ticket Comanda #${idComanda}
                    </title>


                    <style>

                        body {

                            font-family:
                                'Courier New',
                                Courier,
                                monospace;

                            font-size:
                                12px;

                            width:
                                280px;

                            margin:
                                0 auto;

                            padding:
                                10px;

                            color:
                                #000;
                        }


                        .text-center {
                            text-align:
                                center;
                        }


                        .bold {
                            font-weight:
                                bold;
                        }


                        table {

                            width:
                                100%;

                            border-collapse:
                                collapse;

                            margin-top:
                                8px;
                        }


                        th,
                        td {

                            padding:
                                3px 0;

                            border-bottom:
                                1px dashed #bbb;

                            font-size:
                                11px;
                        }


                        .total-section {

                            margin-top:
                                10px;

                            font-size:
                                13px;

                            text-align:
                                right;
                        }


                        .footer {

                            margin-top:
                                15px;

                            text-align:
                                center;

                            font-size:
                                10px;
                        }

                    </style>

                </head>


                <body>

                    <div
                        class="text-center bold"
                        style="font-size:15px;">
                        SISTEMA RESTAURANTE
                    </div>


                    <div class="text-center">
                        Comanda N°:
                        ${idComanda}
                    </div>


                    <div class="text-center">
                        ${fechaHora}
                    </div>


                    <hr
                        style="
                            border:1px dashed #000;
                        "
                    >


                    <div>
                        <strong>Mesa:</strong>
                        ${mesa}
                    </div>


                    <div>
                        <strong>Notas:</strong>
                        ${
                            notas
                                ? notas
                                : 'Ninguna'
                        }
                    </div>


                    <table>

                        <thead>

                            <tr>

                                <th
                                    style="text-align:left;">
                                    Cant / Plato
                                </th>


                                <th
                                    style="text-align:right;">
                                    Subtotal
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            ${htmlItems}

                        </tbody>

                    </table>


                    <div
                        class="total-section bold">

                        TOTAL:
                        S/
                        ${total.toFixed(2)}

                    </div>


                    <hr
                        style="
                            border:1px dashed #000;
                        "
                    >


                    <div class="footer">

                        ¡Comanda registrada con éxito!

                        <br>

                        --- TICKET DE CONTROL ---

                    </div>


                    <script>

                        window.onload =
                            function () {

                                window.print();

                            };

                    <\/script>

                </body>

            </html>

        `);


        ventanaTicket.document.close();
    }

});
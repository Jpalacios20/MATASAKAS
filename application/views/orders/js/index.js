$(document).ready(function () {

    let orders = [];

    const STATUS_LABELS = {
        PENDIENTE: 'Pendiente',
        EN_PREPARACION: 'En preparación',
        LISTO: 'Listo',
        ENTREGADO: 'Entregado',
        PAGADO: 'Pagado',
        ANULADO: 'Anulado'
    };

    const STATUS_COLORS = {
        PENDIENTE: 'bg-warning',
        EN_PREPARACION: 'bg-info',
        LISTO: 'bg-primary',
        ENTREGADO: 'bg-secondary',
        PAGADO: 'bg-success',
        ANULADO: 'bg-danger'
    };

    // Fecha de hoy por defecto (hora local)
    let now = new Date();
    let today = now.getFullYear() + '-' +
        String(now.getMonth() + 1).padStart(2, '0') + '-' +
        String(now.getDate()).padStart(2, '0');

    $('#order_date').val(today);

    load_orders();


    function esc(text) {
        return String(text === null || text === undefined ? '' : text)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }


    // =========================================================
    // 1. CARGAR COMANDAS
    // =========================================================

    function load_orders() {

        $.ajax({
            url: BASE_URL + 'Orders/get_orders',
            type: 'GET',
            data: { date: $('#order_date').val() },
            dataType: 'json',

            success: function (response) {

                orders = (response.status === 'OK' && Array.isArray(response.data))
                    ? response.data
                    : [];

                render_orders();
            },

            error: function (xhr) {
                console.error('Error al cargar pedidos:', xhr.responseText);
                $('#table_orders tbody').html(
                    '<tr><td colspan="6" class="text-center text-danger py-3">Error al cargar.</td></tr>'
                );
            }
        });
    }


    // =========================================================
    // 2. MOSTRAR (con filtros)
    // =========================================================

    function render_orders() {

        let status = String($('.order-filter.active').data('status') || 'all');
        let query = $('#order_search').val().toLowerCase().trim();

        let list = orders.filter(function (o) {

            let matchStatus = (status === 'all' || o.status === status);

            let text = (String(o.id) + ' ' + String(o.table_number || '')).toLowerCase();
            let matchText = text.includes(query);

            return matchStatus && matchText;
        });

        if (list.length === 0) {
            $('#table_orders tbody').html(
                '<tr><td colspan="6" class="text-center text-muted py-3">No hay comandas.</td></tr>'
            );
            return;
        }

        let html = '';

        list.forEach(function (o) {

            html += `
                <tr>
                    <td>#${esc(o.id)}</td>
                    <td>${esc(o.created_at)}</td>
                    <td>${esc(o.table_number)}</td>
                    <td>
                        <span class="badge ${STATUS_COLORS[o.status] || 'bg-secondary'}">
                            ${esc(STATUS_LABELS[o.status] || o.status)}
                        </span>
                    </td>
                    <td class="text-end">S/ ${parseFloat(o.total || 0).toFixed(2)}</td>
                    <td class="text-end">
                        <button type="button" class="btn btn-sm btn-outline-primary btn-view-order"
                            data-id="${esc(o.id)}">Ver</button>
                    </td>
                </tr>
            `;
        });

        $('#table_orders tbody').html(html);
    }


    $(document).on('click', '.order-filter', function () {
        $('.order-filter').removeClass('active');
        $(this).addClass('active');
        render_orders();
    });

    $(document).on('keyup', '#order_search', render_orders);
    $(document).on('change', '#order_date', load_orders);


    // =========================================================
    // 3. DETALLE
    // =========================================================

    $(document).on('click', '.btn-view-order', function () {

        let id = $(this).data('id');

        $.ajax({
            url: BASE_URL + 'Orders/get_order_detail',
            type: 'GET',
            data: { id: id },
            dataType: 'json',

            success: function (response) {

                if (response.status !== 'OK') {
                    alert(response.msg || 'No se pudo cargar la comanda.');
                    return;
                }

                let o = response.data;

                $('#order_id').val(o.id);
                $('#modal_order_title').text('Comanda #' + o.id);
                $('#order_table').text(o.table_number || '-');
                $('#order_notes').text(o.notes || 'Ninguna');
                $('#order_total').text('S/ ' + parseFloat(o.total || 0).toFixed(2));
                $('#order_status').val(o.status);

                let rows = '';

                (o.items || []).forEach(function (i) {
                    rows += `
                        <tr>
                            <td class="text-start">${esc(i.name)}</td>
                            <td>${esc(i.quantity)}</td>
                            <td class="text-end">S/ ${parseFloat(i.unit_price || 0).toFixed(2)}</td>
                            <td class="text-end">S/ ${parseFloat(i.total || 0).toFixed(2)}</td>
                        </tr>
                    `;
                });

                $('#order_items').html(rows);

                bootstrap.Modal
                    .getOrCreateInstance(document.getElementById('modal_order'))
                    .show();
            },

            error: function (xhr) {
                console.error('Error al cargar detalle:', xhr.responseText);
                alert('Ocurrió un error al conectar con el servidor.');
            }
        });
    });


    // =========================================================
    // 4. CAMBIAR ESTADO
    // =========================================================

    $(document).on('click', '#btn_save_status', function () {

        $.ajax({
            url: BASE_URL + 'Orders/change_status',
            type: 'POST',
            contentType: 'application/json; charset=utf-8',
            data: JSON.stringify({
                id: parseInt($('#order_id').val()),
                status: $('#order_status').val()
            }),
            dataType: 'json',

            success: function (response) {

                if (response.status === 'OK') {
                    bootstrap.Modal
                        .getOrCreateInstance(document.getElementById('modal_order'))
                        .hide();
                    load_orders();
                } else {
                    alert(response.msg || 'No se pudo actualizar el estado.');
                }
            },

            error: function (xhr) {
                console.error('Error al guardar estado:', xhr.responseText);
                alert('Ocurrió un error al conectar con el servidor.');
            }
        });
    });

});
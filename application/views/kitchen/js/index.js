$(document).ready(function () {

    load_kitchen();
    setInterval(load_kitchen, 15000);


    function esc(text) {
        return String(text === null || text === undefined ? '' : text)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function minutes_ago(dateStr) {
        let d = new Date(String(dateStr).replace(' ', 'T'));
        if (isNaN(d.getTime())) {
            return 0;
        }
        return Math.max(0, Math.floor((Date.now() - d.getTime()) / 60000));
    }


    // =========================================================
    // 1. CARGAR COMANDAS ACTIVAS
    // =========================================================

    function load_kitchen() {

        $.ajax({
            url: BASE_URL + 'Kitchen/get_orders',
            type: 'GET',
            dataType: 'json',

            success: function (response) {

                let list = (response.status === 'OK' && Array.isArray(response.data))
                    ? response.data
                    : [];

                render_kitchen(list);

                let n = new Date();
                $('#kitchen_updated').text(
                    String(n.getHours()).padStart(2, '0') + ':' +
                    String(n.getMinutes()).padStart(2, '0') + ':' +
                    String(n.getSeconds()).padStart(2, '0')
                );
            },

            error: function (xhr) {
                console.error('Error al cargar cocina:', xhr.responseText);
            }
        });
    }


    // =========================================================
    // 2. MOSTRAR TARJETAS POR COLUMNA
    // =========================================================

    function card_html(o) {

        let mins = minutes_ago(o.created_at);
        let late = (o.status !== 'LISTO' && mins >= 20) ? 'kitchen-late' : 'text-muted';

        let items = '';
        (o.items || []).forEach(function (i) {
            items += `<li><strong>${esc(i.quantity)}x</strong> ${esc(i.name)}</li>`;
        });

        let button = '';

        if (o.status === 'PENDIENTE') {
            button = `<button type="button" class="btn btn-info btn-sm w-100 btn-kitchen"
                        data-id="${esc(o.id)}" data-status="EN_PREPARACION">Preparar</button>`;
        } else if (o.status === 'EN_PREPARACION') {
            button = `<button type="button" class="btn btn-primary btn-sm w-100 btn-kitchen"
                        data-id="${esc(o.id)}" data-status="LISTO">Listo</button>`;
        } else {
            button = `<button type="button" class="btn btn-success btn-sm w-100 btn-kitchen"
                        data-id="${esc(o.id)}" data-status="ENTREGADO">Entregado</button>`;
        }

        return `
            <div class="card kitchen-card st-${esc(o.status)} p-1 mb-1 shadow-sm">
                <div class="d-flex justify-content-between">
                    <h6 class="fw-bold mb-0">#${esc(o.id)} · ${esc(o.table_number || 'Sin mesa')}</h6>
                    <small class="${late}">hace ${mins} min</small>
                </div>
                <ul class="mb-1 mt-1 ps-2">${items || '<li class="text-muted">Sin platos</li>'}</ul>
                ${o.notes ? `<p class="mb-1 small"><strong>Obs:</strong> ${esc(o.notes)}</p>` : ''}
                ${button}
            </div>
        `;
    }

    function render_kitchen(list) {

        let cols = { PENDIENTE: '', EN_PREPARACION: '', LISTO: '' };
        let counts = { PENDIENTE: 0, EN_PREPARACION: 0, LISTO: 0 };

        list.forEach(function (o) {
            if (cols[o.status] !== undefined) {
                cols[o.status] += card_html(o);
                counts[o.status]++;
            }
        });

        let empty = '<p class="text-muted text-center py-2">Sin comandas</p>';

        $('#col_pendiente').html(cols.PENDIENTE || empty);
        $('#col_preparacion').html(cols.EN_PREPARACION || empty);
        $('#col_listo').html(cols.LISTO || empty);

        $('#count_pendiente').text(counts.PENDIENTE);
        $('#count_preparacion').text(counts.EN_PREPARACION);
        $('#count_listo').text(counts.LISTO);
    }


    // =========================================================
    // 3. CAMBIAR ESTADO
    // =========================================================

    $(document).on('click', '.btn-kitchen', function () {

        let btn = $(this);
        btn.prop('disabled', true);

        $.ajax({
            url: BASE_URL + 'Kitchen/change_status',
            type: 'POST',
            contentType: 'application/json; charset=utf-8',
            data: JSON.stringify({
                id: parseInt(btn.data('id')),
                status: btn.data('status')
            }),
            dataType: 'json',

            success: function (response) {
                if (response.status !== 'OK') {
                    alert(response.msg || 'No se pudo actualizar la comanda.');
                }
                load_kitchen();
            },

            error: function (xhr) {
                console.error('Error al cambiar estado:', xhr.responseText);
                alert('Ocurrió un error al conectar con el servidor.');
                btn.prop('disabled', false);
            }
        });
    });

});
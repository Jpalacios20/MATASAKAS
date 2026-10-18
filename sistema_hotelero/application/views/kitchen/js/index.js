$(document).ready(function () {

    const ESTADOS = {
        PENDIENTE: { col: '#col_pendientes', next: 'EN_PREPARACION', label: 'Iniciar preparación', btn: 'btn-warning' },
        EN_PREPARACION: { col: '#col_preparacion', next: 'LISTO', label: 'Marcar listo', btn: 'btn-success' },
        LISTO: { col: '#col_listos', next: null, label: '', btn: '' }
    };

    // 1. Carga inicial al abrir la página
    load_kitchen_orders();

    // 2. Actualización automática cada 15 segundos (15000 ms)
    setInterval(load_kitchen_orders, 15000);

    // =========================================================
    // CARGAR COMANDAS
    // =========================================================
    function load_kitchen_orders() {
        $.ajax({
            url: BASE_URL + 'Kitchen/get_orders',
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                if (response.status === 'OK' && Array.isArray(response.result)) {
                    render_kitchen(response.result);
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
    // PINTAR TARJETAS
    // =========================================================
    function esc(t) {
        return $('<div>').text(t == null ? '' : t).html();
    }

    function render_kitchen(orders) {
        let cols = { PENDIENTE: '', EN_PREPARACION: '', LISTO: '' };

        orders.forEach(function (o) {
            let cfg = ESTADOS[o.status];
            if (!cfg) return;

            let items = (o.items || []).map(function (i) {
                return `<li>${esc(i.quantity)}x ${esc(i.name)}</li>`;
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
                        <h6 class="fw-bold mb-1">#${esc(o.id_order)} - ${esc(o.location)}</h6>
                        <ul class="small mb-1 ps-3">${items}</ul>
                        ${o.notes ? `<p class="small text-muted mb-2">${esc(o.notes)}</p>` : ''}
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
    // CAMBIAR ESTADO
    // =========================================================
    $(document).on('click', '.btn-change-status', function () {
        let $btn = $(this);$btn.prop('disabled', true);

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
    // BOTÓN ACTUALIZAR
    // =========================================================
    $(document).on('click', '#btn_actualizar', load_kitchen_orders);
});
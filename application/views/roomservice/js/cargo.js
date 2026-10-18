$(document).ready(function () {

    cargarPedidosParaCargo();

    $('#btn-cerrar-cargo').on(
        'click',
        cerrarModalCargo
    );

    $('#btn-confirmar-cargo').on(
        'click',
        confirmarCargo
    );

});


let pedidoSeleccionado = 0;


/*
|--------------------------------------------------------------------------
| CARGAR PEDIDOS ENTREGADOS
|--------------------------------------------------------------------------
*/

function cargarPedidosParaCargo() {

    $.ajax({

        url: BASE_URL + 'RoomService/get_pedidos_entregados',

        type: 'GET',

        dataType: 'json',

        success: function (response) {

            console.log(
                'PEDIDOS PARA CARGO:',
                response
            );

            if (
                response.status !== 'OK' ||
                !response.data ||
                response.data.length === 0
            ) {

                $('#lista-cargos').html(`
                    <div class="sin-cargos">

                        <h4>
                            No hay pedidos para cargar
                        </h4>

                        <p>
                            Los pedidos entregados
                            aparecerán aquí.
                        </p>

                    </div>
                `);

                return;
            }


            let html = '';


            $.each(
                response.data,
                function (index, pedido) {

                    html += construirCargo(
                        pedido
                    );

                }
            );


            $('#lista-cargos').html(html);


            if (
                typeof feather !== 'undefined'
            ) {

                feather.replace();

            }
        },


        error: function (xhr) {

            console.log(
                'ERROR:',
                xhr.responseText
            );

            $('#lista-cargos').html(`
                <div class="sin-cargos">

                    <h4>
                        Error al cargar los pedidos
                    </h4>

                    <p>
                        Revisa la consola del navegador.
                    </p>

                </div>
            `);
        }

    });
}


/*
|--------------------------------------------------------------------------
| CONSTRUIR PEDIDO
|--------------------------------------------------------------------------
*/

function construirCargo(pedido) {

    let itemsHtml = '';


    if (
        pedido.items &&
        pedido.items.length > 0
    ) {

        $.each(
            pedido.items,
            function (index, item) {

                itemsHtml += `
                    <tr>

                        <td>
                            ${item.name}
                        </td>

                        <td>
                            ${item.quantity}
                        </td>

                        <td>
                            S/
                            ${parseFloat(
                                item.unit_price
                            ).toFixed(2)}
                        </td>

                        <td>
                            S/
                            ${parseFloat(
                                item.total
                            ).toFixed(2)}
                        </td>

                    </tr>
                `;

            }
        );
    }


    return `
        <div class="cargo-card">

            <div class="cargo-header">

                <strong>
                    Pedido #${pedido.id}
                    — Habitación ${pedido.room_number}
                </strong>

                <span class="badge badge-warning">
                    ENTREGADO
                </span>

            </div>


            <div class="cargo-body">

                <div class="cargo-info">

                    <div class="cargo-info-item">

                        <span>
                            Huésped
                        </span>

                        <strong>
                            ${pedido.customer_name || 'Sin nombre'}
                        </strong>

                    </div>


                    <div class="cargo-info-item">

                        <span>
                            Fecha
                        </span>

                        <strong>
                            ${pedido.created_at}
                        </strong>

                    </div>


                    <div class="cargo-info-item">

                        <span>
                            Observaciones
                        </span>

                        <strong>
                            ${pedido.notes || 'Sin observaciones'}
                        </strong>

                    </div>

                </div>


                <div class="cargo-items">

                    <table class="table">

                        <thead>

                            <tr>

                                <th>
                                    PRODUCTO
                                </th>

                                <th>
                                    CANT.
                                </th>

                                <th>
                                    PRECIO
                                </th>

                                <th>
                                    TOTAL
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            ${itemsHtml}

                        </tbody>

                    </table>

                </div>


                <div class="cargo-total">

                    <strong>
                        Total:
                    </strong>

                    S/
                    ${parseFloat(
                        pedido.total
                    ).toFixed(2)}

                </div>


                <div class="cargo-actions">

                    <button
                        type="button"
                        class="btn btn-primary"
                        onclick="abrirCargo(${pedido.id})"
                    >

                        <i data-feather="credit-card"></i>

                        Cargar a habitación

                    </button>

                </div>

            </div>

        </div>
    `;
}


/*
|--------------------------------------------------------------------------
| ABRIR MODAL
|--------------------------------------------------------------------------
*/

function abrirCargo(orderId) {

    pedidoSeleccionado = orderId;

    $('#cargo-reservation').html(`
        <option value="">
            Cargando reservas...
        </option>
    `);

    $('#info-cargo').html(
        'Buscando reservas de la habitación...'
    );


    $('#modal-cargo').css(
        'display',
        'flex'
    );


    $.ajax({

        url:
            BASE_URL +
            'RoomService/get_reservaciones_para_cargo?order_id=' +
            orderId,

        type: 'GET',

        dataType: 'json',

        success: function (response) {

            console.log(
                'RESERVACIONES:',
                response
            );


            if (
                response.status !== 'OK' ||
                !response.data ||
                response.data.length === 0
            ) {

                $('#cargo-reservation').html(`
                    <option value="">
                        No existen reservas
                    </option>
                `);

                $('#info-cargo').html(
                    '<span style="color:#c00;">' +
                    response.msg +
                    '</span>'
                );

                return;
            }


            let html = `
                <option value="">
                    Seleccionar reserva
                </option>
            `;


            $.each(
                response.data,
                function (index, reserva) {

                    let guest =
                        reserva.guest_name ||
                        'Sin huésped';

                    let texto =
                        'Reserva #' +
                        reserva.id_reservation +
                        ' — ' +
                        guest +
                        ' — ' +
                        reserva.status +
                        ' — ' +
                        reserva.checkin_date;

                    html += `
                        <option
                            value="${reserva.id_reservation}"
                        >
                            ${texto}
                        </option>
                    `;
                }
            );


            $('#cargo-reservation').html(
                html
            );


            $('#info-cargo').html(
                'Selecciona la reserva correspondiente ' +
                'a la habitación del pedido.'
            );
        },


        error: function (xhr) {

            console.log(
                'ERROR RESERVACIONES:',
                xhr.responseText
            );

            $('#cargo-reservation').html(`
                <option value="">
                    Error cargando reservas
                </option>
            `);

            $('#info-cargo').html(
                'No fue posible cargar las reservas.'
            );
        }

    });
}


/*
|--------------------------------------------------------------------------
| CONFIRMAR CARGO
|--------------------------------------------------------------------------
*/

function confirmarCargo() {

    let reservationId =
        $('#cargo-reservation').val();


    if (!pedidoSeleccionado) {

        alert(
            'No se seleccionó ningún pedido.'
        );

        return;
    }


    if (!reservationId) {

        alert(
            'Selecciona una reserva.'
        );

        return;
    }


    if (
        !confirm(
            '¿Deseas cargar este pedido a la habitación?'
        )
    ) {

        return;
    }


    $.ajax({

        url:
            BASE_URL +
            'RoomService/cargar_pedido_habitacion',

        type: 'POST',

        contentType: 'application/json',

        dataType: 'json',

        data: JSON.stringify({

            order_id:
                pedidoSeleccionado,

            id_reservation:
                reservationId

        }),


        success: function (response) {

            console.log(
                'CARGO:',
                response
            );


            if (response.status === 'OK') {

                alert(
                    'Cargo registrado correctamente en la habitación.'
                );


                cerrarModalCargo();


                cargarPedidosParaCargo();

            } else {

                alert(
                    response.msg
                );
            }
        },


        error: function (xhr) {

            console.log(
                'ERROR CARGO:',
                xhr.responseText
            );

            alert(
                'No fue posible registrar el cargo.'
            );
        }

    });
}


/*
|--------------------------------------------------------------------------
| CERRAR MODAL
|--------------------------------------------------------------------------
*/

function cerrarModalCargo() {

    $('#modal-cargo').hide();

    pedidoSeleccionado = 0;

    $('#cargo-reservation').html(`
        <option value="">
            Seleccionar reserva
        </option>
    `);
}
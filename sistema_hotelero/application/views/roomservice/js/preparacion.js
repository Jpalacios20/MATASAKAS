$(document).ready(function () {
    cargarPedidosPreparacion();
});


function cargarPedidosPreparacion() {

    $.ajax({

        url: BASE_URL + 'RoomService/get_pedidos_preparacion',

        type: 'GET',

        dataType: 'json',

        success: function (response) {

            console.log(
                'PEDIDOS EN PREPARACIÓN:',
                response
            );


            if (
                response.status !== 'OK' ||
                !response.data ||
                response.data.length === 0
            ) {

                cargarPedidosListos();

                return;
            }


            let html = '';


            $.each(
                response.data,
                function (index, pedido) {

                    html += construirPedidoPreparacion(
                        pedido
                    );
                }
            );


            $('#lista-pedidos').html(html);


            cargarPedidosListos();


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


            $('#lista-pedidos').html(`
                <div class="sin-pedidos">
                    <h4>Error al cargar los pedidos</h4>
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
| PEDIDOS LISTOS
|--------------------------------------------------------------------------
*/

function cargarPedidosListos() {

    $.ajax({

        url: BASE_URL + 'RoomService/get_pedidos_listos',

        type: 'GET',

        dataType: 'json',

        success: function (response) {

            console.log(
                'PEDIDOS LISTOS:',
                response
            );


            let htmlActual = $('#lista-pedidos').html();


            if (
                response.status !== 'OK' ||
                !response.data ||
                response.data.length === 0
            ) {

                if (!htmlActual) {

                    $('#lista-pedidos').html(`
                        <div class="sin-pedidos">
                            <h4>No hay pedidos en preparación</h4>
                            <p>
                                Los pedidos enviados a preparación
                                aparecerán aquí.
                            </p>
                        </div>
                    `);
                }

                return;
            }


            let html = htmlActual || '';


            /*
            |--------------------------------------------------------------------------
            | TÍTULO DE PEDIDOS LISTOS
            |--------------------------------------------------------------------------
            */

            html += `
                <div class="separador-pedidos">
                    <h3>Pedidos listos para entregar</h3>
                </div>
            `;


            $.each(
                response.data,
                function (index, pedido) {

                    html += construirPedidoListo(
                        pedido
                    );
                }
            );


            $('#lista-pedidos').html(html);


            if (
                typeof feather !== 'undefined'
            ) {
                feather.replace();
            }
        },


        error: function (xhr) {

            console.log(
                'ERROR PEDIDOS LISTOS:',
                xhr.responseText
            );
        }
    });
}


/*
|--------------------------------------------------------------------------
| CONSTRUIR PEDIDO EN PREPARACIÓN
|--------------------------------------------------------------------------
*/

function construirPedidoPreparacion(pedido) {

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
        <div
            class="pedido-card"
            data-id="${pedido.id}"
        >

            <div class="pedido-header">

                <strong>
                    Pedido #${pedido.id}
                    — Habitación ${pedido.room_number}
                </strong>

                <span class="estado-pendiente">
                    EN PREPARACIÓN
                </span>

            </div>


            <div class="pedido-body">

                <div class="pedido-info">

                    <div class="pedido-info-item">

                        <span>
                            Huésped
                        </span>

                        <strong>
                            ${pedido.customer_name || 'Sin nombre'}
                        </strong>

                    </div>


                    <div class="pedido-info-item">

                        <span>
                            Fecha
                        </span>

                        <strong>
                            ${pedido.created_at}
                        </strong>

                    </div>


                    <div class="pedido-info-item">

                        <span>
                            Observaciones
                        </span>

                        <strong>
                            ${pedido.notes || 'Sin observaciones'}
                        </strong>

                    </div>

                </div>


                <div class="pedido-items">

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


                <div class="pedido-total">

                    <strong>
                        Total:
                    </strong>

                    S/
                    ${parseFloat(
                        pedido.total
                    ).toFixed(2)}

                </div>


                <div class="pedido-actions">

                    <button
                        type="button"
                        class="btn btn-success"
                        onclick="marcarListo(${pedido.id})"
                    >

                        <i data-feather="check"></i>

                        Marcar como listo

                    </button>

                </div>

            </div>

        </div>
    `;
}


/*
|--------------------------------------------------------------------------
| CONSTRUIR PEDIDO LISTO
|--------------------------------------------------------------------------
*/

function construirPedidoListo(pedido) {

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
        <div
            class="pedido-card"
            data-id="${pedido.id}"
        >

            <div class="pedido-header">

                <strong>
                    Pedido #${pedido.id}
                    — Habitación ${pedido.room_number}
                </strong>

                <span class="estado-pendiente">
                    LISTO
                </span>

            </div>


            <div class="pedido-body">

                <div class="pedido-info">

                    <div class="pedido-info-item">

                        <span>
                            Huésped
                        </span>

                        <strong>
                            ${pedido.customer_name || 'Sin nombre'}
                        </strong>

                    </div>


                    <div class="pedido-info-item">

                        <span>
                            Fecha
                        </span>

                        <strong>
                            ${pedido.created_at}
                        </strong>

                    </div>


                    <div class="pedido-info-item">

                        <span>
                            Observaciones
                        </span>

                        <strong>
                            ${pedido.notes || 'Sin observaciones'}
                        </strong>

                    </div>

                </div>


                <div class="pedido-items">

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


                <div class="pedido-total">

                    <strong>
                        Total:
                    </strong>

                    S/
                    ${parseFloat(
                        pedido.total
                    ).toFixed(2)}

                </div>


                <div class="pedido-actions">

                    <button
                        type="button"
                        class="btn btn-primary"
                        onclick="marcarEntregado(${pedido.id})"
                    >

                        <i data-feather="truck"></i>

                        Marcar como entregado

                    </button>

                </div>

            </div>

        </div>
    `;
}


/*
|--------------------------------------------------------------------------
| MARCAR COMO LISTO
|--------------------------------------------------------------------------
*/

function marcarListo(id) {

    if (
        !confirm(
            '¿Deseas marcar este pedido como listo?'
        )
    ) {
        return;
    }


    cambiarEstado(
        id,
        'LISTO',
        'Pedido marcado como listo.'
    );
}


/*
|--------------------------------------------------------------------------
| MARCAR COMO ENTREGADO
|--------------------------------------------------------------------------
*/

function marcarEntregado(id) {

    if (
        !confirm(
            '¿Deseas marcar este pedido como entregado?'
        )
    ) {
        return;
    }


    cambiarEstado(
        id,
        'ENTREGADO',
        'Pedido marcado como entregado.'
    );
}


/*
|--------------------------------------------------------------------------
| CAMBIAR ESTADO
|--------------------------------------------------------------------------
*/

function cambiarEstado(
    id,
    status,
    mensaje
) {

    $.ajax({

        url: BASE_URL + 'RoomService/cambiar_estado',

        type: 'POST',

        contentType: 'application/json',

        dataType: 'json',

        data: JSON.stringify({
            id: id,
            status: status
        }),


        success: function (response) {

            console.log(
                'CAMBIO ESTADO:',
                response
            );


            if (response.status === 'OK') {

                alert(mensaje);

                cargarPedidosPreparacion();

            } else {

                alert(response.msg);
            }
        },


        error: function (xhr) {

            console.log(
                'ERROR CAMBIANDO ESTADO:',
                xhr.responseText
            );

            alert(
                'No fue posible actualizar el pedido.'
            );
        }
    });
}
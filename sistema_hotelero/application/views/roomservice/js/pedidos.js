$(document).ready(function () {

    cargarPedidosPendientes();

});


function cargarPedidosPendientes() {

    $.ajax({

        url:
            BASE_URL +
            'RoomService/get_pedidos_pendientes',

        type: 'GET',

        dataType: 'json',

        success: function (response) {

            console.log(
                'PEDIDOS PENDIENTES:',
                response
            );


            if (
                response.status !== 'OK'
            ) {

                mostrarSinPedidos();

                return;
            }


            if (
                !response.data ||
                response.data.length === 0
            ) {

                mostrarSinPedidos();

                return;
            }


            let html = '';


            $.each(
                response.data,
                function (index, pedido) {

                    html += construirPedido(
                        pedido
                    );

                }
            );


            $('#lista-pedidos')
                .html(html);


            if (
                typeof feather !== 'undefined'
            ) {

                feather.replace();

            }

        },

        error: function (xhr) {

            console.log(
                'ERROR PEDIDOS:',
                xhr.responseText
            );


            $('#lista-pedidos').html(`

                <div class="sin-pedidos">

                    Error al cargar los pedidos.

                </div>

            `);

        }

    });
}


function construirPedido(pedido) {

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
                            S/ ${parseFloat(
                                item.unit_price
                            ).toFixed(2)}
                        </td>

                        <td>
                            S/ ${parseFloat(
                                item.total
                            ).toFixed(2)}
                        </td>

                    </tr>

                `;

            }
        );

    } else {

        itemsHtml = `

            <tr>

                <td colspan="4">
                    Sin productos
                </td>

            </tr>

        `;

    }


    let cliente =
        pedido.customer_name
        ? pedido.customer_name
        : 'Sin nombre';


    let notas =
        pedido.notes
        ? pedido.notes
        : 'Sin observaciones';


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
                    PENDIENTE
                </span>

            </div>


            <div class="pedido-body">

                <div class="pedido-info">

                    <div class="pedido-info-item">

                        <span>
                            Huésped
                        </span>

                        <strong>
                            ${cliente}
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
                            ${notas}
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
                        class="btn btn-primary btn-preparar"
                        onclick="marcarPreparacion(${pedido.id})"
                    >

                        <i
                            data-feather="play"
                        ></i>

                        Pasar a preparación

                    </button>

                </div>

            </div>

        </div>

    `;
}


function mostrarSinPedidos() {

    $('#lista-pedidos').html(`

        <div class="sin-pedidos">

            <i
                data-feather="check-circle"
                style="width:40px;height:40px;"
            ></i>

            <h4>
                No hay pedidos pendientes
            </h4>

            <p>
                Los nuevos pedidos de Room Service aparecerán aquí.
            </p>

        </div>

    `);


    if (
        typeof feather !== 'undefined'
    ) {

        feather.replace();

    }
}


function marcarPreparacion(id) {

    if (!confirm(
        '¿Deseas pasar este pedido a preparación?'
    )) {

        return;
    }


    $.ajax({

        url:
            BASE_URL +
            'RoomService/cambiar_estado',

        type: 'POST',

        contentType:
            'application/json',

        dataType:
            'json',

        data:
            JSON.stringify({

                id: id,

                status:
                    'EN_PREPARACION'

            }),

        success: function (response) {

            console.log(
                'CAMBIO ESTADO:',
                response
            );


            if (
                response.status === 'OK'
            ) {

                alert(
                    'Pedido enviado a preparación.'
                );


                cargarPedidosPendientes();

            } else {

                alert(
                    response.msg
                );

            }

        },

        error: function (xhr) {

            console.log(
                'ERROR ESTADO:',
                xhr.responseText
            );


            alert(
                'No fue posible actualizar el pedido.'
            );

        }

    });
}
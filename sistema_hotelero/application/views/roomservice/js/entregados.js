$(document).ready(function () {
    cargarPedidosEntregados();
});


function cargarPedidosEntregados() {

    $.ajax({
        url: BASE_URL + 'RoomService/get_pedidos_entregados',
        type: 'GET',
        dataType: 'json',

        success: function (response) {

            console.log('PEDIDOS ENTREGADOS:', response);

            if (
                response.status !== 'OK' ||
                !response.data ||
                response.data.length === 0
            ) {

                $('#lista-pedidos').html(`
                    <div class="sin-pedidos">
                        <h4>No hay pedidos entregados</h4>
                        <p>
                            Los pedidos marcados como entregados
                            aparecerán aquí.
                        </p>
                    </div>
                `);

                return;
            }

            let html = '';

            $.each(response.data, function (index, pedido) {

                html += construirPedidoEntregado(pedido);

            });

            $('#lista-pedidos').html(html);

            if (typeof feather !== 'undefined') {
                feather.replace();
            }
        },

        error: function (xhr) {

            console.log(
                'ERROR PEDIDOS ENTREGADOS:',
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


function construirPedidoEntregado(pedido) {

    let itemsHtml = '';

    if (
        pedido.items &&
        pedido.items.length > 0
    ) {

        $.each(pedido.items, function (index, item) {

            itemsHtml += `
                <tr>
                    <td>${item.name}</td>

                    <td>
                        ${item.quantity}
                    </td>

                    <td>
                        S/ ${parseFloat(item.unit_price).toFixed(2)}
                    </td>

                    <td>
                        S/ ${parseFloat(item.total).toFixed(2)}
                    </td>
                </tr>
            `;
        });
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
                    ENTREGADO
                </span>

            </div>


            <div class="pedido-body">

                <div class="pedido-info">

                    <div class="pedido-info-item">

                        <span>Huésped</span>

                        <strong>
                            ${pedido.customer_name || 'Sin nombre'}
                        </strong>

                    </div>


                    <div class="pedido-info-item">

                        <span>Fecha</span>

                        <strong>
                            ${pedido.created_at}
                        </strong>

                    </div>


                    <div class="pedido-info-item">

                        <span>Observaciones</span>

                        <strong>
                            ${pedido.notes || 'Sin observaciones'}
                        </strong>

                    </div>

                </div>


                <div class="pedido-items">

                    <table class="table">

                        <thead>

                            <tr>
                                <th>PRODUCTO</th>
                                <th>CANT.</th>
                                <th>PRECIO</th>
                                <th>TOTAL</th>
                            </tr>

                        </thead>


                        <tbody>

                            ${itemsHtml}

                        </tbody>

                    </table>

                </div>


                <div class="pedido-total">

                    <strong>Total:</strong>

                    S/
                    ${parseFloat(pedido.total).toFixed(2)}

                </div>


                <div class="pedido-actions">

                    <button
                        type="button"
                        class="btn btn-success"
                        onclick="marcarPagado(${pedido.id})"
                    >

                        <i data-feather="check-circle"></i>

                        Marcar como pagado

                    </button>

                </div>

            </div>

        </div>
    `;
}


function marcarPagado(id) {

    if (
        !confirm(
            '¿Deseas marcar este pedido como pagado?'
        )
    ) {
        return;
    }


    $.ajax({

        url: BASE_URL + 'RoomService/cambiar_estado',

        type: 'POST',

        contentType: 'application/json',

        dataType: 'json',

        data: JSON.stringify({
            id: id,
            status: 'PAGADO'
        }),

        success: function (response) {

            console.log(
                'CAMBIO A PAGADO:',
                response
            );


            if (response.status === 'OK') {

                alert(
                    'Pedido marcado como pagado.'
                );

                cargarPedidosEntregados();

            } else {

                alert(response.msg);
            }
        },

        error: function (xhr) {

            console.log(
                xhr.responseText
            );

            alert(
                'No fue posible actualizar el pedido.'
            );
        }
    });
}
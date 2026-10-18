$(document).ready(function () {

    cargarHabitaciones();

    cargarProductos();


    /*
    |--------------------------------------------------------------------------
    | BOTÓN AGREGAR
    |--------------------------------------------------------------------------
    */

    $('#btn-add-product').on('click', function () {

        agregarProducto();

    });


    /*
    |--------------------------------------------------------------------------
    | BOTÓN GUARDAR
    |--------------------------------------------------------------------------
    */

    $('#btn-save-room-service').on('click', function () {

        guardarPedido();

    });

});


/*
|--------------------------------------------------------------------------
| CARGAR HABITACIONES
|--------------------------------------------------------------------------
*/

function cargarHabitaciones() {

    $.ajax({

        url:
            BASE_URL +
            'RoomService/get_habitaciones',

        type: 'GET',

        dataType: 'json',

        success: function (response) {

            console.log(
                'HABITACIONES:',
                response
            );


            let select =
                $('#room-number');


            select.empty();


            select.append(
                '<option value="">Seleccionar habitación</option>'
            );


            if (response.status !== 'OK') {

                console.log(
                    'Error habitaciones:',
                    response.msg
                );

                return;
            }


            if (
                !response.data ||
                response.data.length === 0
            ) {

                console.log(
                    'No hay habitaciones.'
                );

                return;
            }


            $.each(
                response.data,
                function (index, room) {

                    select.append(

                        '<option value="' +
                        room.id_room +
                        '">' +
                        room.room_number +
                        '</option>'

                    );

                }
            );

        },

        error: function (xhr) {

            console.log(
                'ERROR HABITACIONES:',
                xhr.responseText
            );

        }

    });
}


/*
|--------------------------------------------------------------------------
| CARGAR PRODUCTOS
|--------------------------------------------------------------------------
*/

function cargarProductos() {

    $.ajax({

        url:
            BASE_URL +
            'RoomService/get_productos',

        type: 'GET',

        dataType: 'json',

        success: function (response) {

            console.log(
                'PRODUCTOS:',
                response
            );


            let select =
                $('#room-service-product');


            select.empty();


            select.append(
                '<option value="">Seleccionar producto</option>'
            );


            if (response.status !== 'OK') {

                console.log(
                    'Error productos:',
                    response.msg
                );

                return;
            }


            if (
                !response.data ||
                response.data.length === 0
            ) {

                console.log(
                    'No hay productos.'
                );

                return;
            }


            $.each(
                response.data,
                function (index, product) {

                    let precio =
                        parseFloat(
                            product.product_price
                        ) || 0;


                    let nombre =
                        product.product_name
                        || 'Producto';


                    select.append(

                        '<option ' +
                        'value="' +
                        product.id_product +
                        '" ' +
                        'data-price="' +
                        precio +
                        '" ' +
                        'data-name="' +
                        nombre +
                        '">' +

                        nombre +

                        ' - S/ ' +

                        precio.toFixed(2) +

                        '</option>'

                    );

                }
            );

        },

        error: function (xhr) {

            console.log(
                'ERROR PRODUCTOS:',
                xhr.responseText
            );

        }

    });
}


/*
|--------------------------------------------------------------------------
| AGREGAR PRODUCTO
|--------------------------------------------------------------------------
*/

function agregarProducto() {

    let select =
        $('#room-service-product');


    let option =
        select.find(
            'option:selected'
        );


    let id =
        option.val();


    if (!id) {

        alert(
            'Selecciona un producto.'
        );

        return;
    }


    let nombre =
        option.data('name');


    let precio =
        parseFloat(
            option.data('price')
        ) || 0;


    let cantidad =
        parseInt(
            $('#product-quantity').val()
        ) || 1;


    if (cantidad < 1) {
        cantidad = 1;
    }


    let total =
        precio * cantidad;


    let fila = `

        <tr
            data-id="${id}"
            data-price="${precio}"
        >

            <td>
                ${nombre}
            </td>

            <td>
                ${cantidad}
            </td>

            <td>
                S/ ${precio.toFixed(2)}
            </td>

            <td>
                S/ ${total.toFixed(2)}
            </td>

            <td>

                <button
                    type="button"
                    class="btn btn-danger btn-sm btn-eliminar-producto"
                >

                    <i
                        data-feather="trash-2"
                    ></i>

                </button>

            </td>

        </tr>

    `;


    $('#room-service-items')
        .append(fila);


    calcularTotal();


    $('#product-quantity')
        .val(1);


    $('#room-service-product')
        .val('');


    if (
        typeof feather !== 'undefined'
    ) {

        feather.replace();

    }
}


/*
|--------------------------------------------------------------------------
| ELIMINAR PRODUCTO
|--------------------------------------------------------------------------
*/

$(document).on(
    'click',
    '.btn-eliminar-producto',
    function () {

        $(this)
            .closest('tr')
            .remove();


        calcularTotal();

    }
);


/*
|--------------------------------------------------------------------------
| CALCULAR TOTAL
|--------------------------------------------------------------------------
*/

function calcularTotal() {

    let total = 0;


    $('#room-service-items tr')
        .each(function () {

            let subtotal =
                parseFloat(
                    $(this)
                        .find('td')
                        .eq(3)
                        .text()
                        .replace('S/', '')
                        .trim()
                ) || 0;


            total += subtotal;

        });


    $('#room-service-total')
        .text(
            total.toFixed(2)
        );
}


/*
|--------------------------------------------------------------------------
| GUARDAR PEDIDO
|--------------------------------------------------------------------------
*/

function guardarPedido() {

    let habitacion =
        $('#room-number').val();


    let cliente =
        $('#customer-name')
            .val()
            .trim();


    let notas =
        $('#order-notes')
            .val()
            .trim();


    /*
    |--------------------------------------------------------------------------
    | VALIDAR HABITACIÓN
    |--------------------------------------------------------------------------
    */

    if (!habitacion) {

        alert(
            'Selecciona una habitación.'
        );

        return;
    }


    /*
    |--------------------------------------------------------------------------
    | VALIDAR PRODUCTOS
    |--------------------------------------------------------------------------
    */

    let filas =
        $('#room-service-items tr');


    if (filas.length === 0) {

        alert(
            'Agrega al menos un producto.'
        );

        return;
    }


    /*
    |--------------------------------------------------------------------------
    | ARMAR ITEMS
    |--------------------------------------------------------------------------
    */

    let items = [];


    filas.each(function () {

        let fila =
            $(this);


        let id =
            parseInt(
                fila.data('id')
            );


        let cantidad =
            parseInt(
                fila
                    .find('td')
                    .eq(1)
                    .text()
            ) || 1;


        let precio =
            parseFloat(
                fila.data('price')
            ) || 0;


        items.push({

            product_id:
                id,

            quantity:
                cantidad,

            price:
                precio,

            kind:
                'PRODUCTO'

        });

    });


    /*
    |--------------------------------------------------------------------------
    | DATOS
    |--------------------------------------------------------------------------
    */

    let datos = {

        room_id:
            parseInt(habitacion),

        customer_name:
            cliente,

        notes:
            notas,

        items:
            items

    };


    console.log(
        'DATOS A ENVIAR:',
        datos
    );


    /*
    |--------------------------------------------------------------------------
    | ENVIAR
    |--------------------------------------------------------------------------
    */

    $.ajax({

        url:
            BASE_URL +
            'RoomService/crear_pedido',

        type:
            'POST',

        contentType:
            'application/json',

        dataType:
            'json',

        data:
            JSON.stringify(datos),


        success: function (response) {

            console.log(
                'RESPUESTA GUARDAR:',
                response
            );


            if (
                response.status === 'OK'
            ) {

                alert(
                    'Pedido registrado correctamente.'
                );


                location.reload();


            } else {

                alert(
                    response.msg
                );

            }

        },


        error: function (xhr) {

            console.log(
                'ERROR GUARDAR:',
                xhr.responseText
            );


            alert(
                'Error al registrar el pedido.'
            );

        }

    });
}
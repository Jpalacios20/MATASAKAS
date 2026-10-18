$(document).ready(function () {

    // =========================================================
    // VARIABLES
    // =========================================================

    let editingId = 0;


    // =========================================================
    // INICIO
    // =========================================================

    load_tables();


    // =========================================================
    // OBTENER MESAS
    // =========================================================

    function load_tables() {

        $.ajax({
            url: BASE_URL + 'RestaurantTables/get_tables',
            type: 'GET',
            dataType: 'json',

            success: function (response) {

                if (response.status === 'OK') {

                    render_tables(response.data);

                } else {

                    $('#tables_container').html(`
                        <div class="col-12">
                            <div class="alert alert-warning">
                                ${response.msg || 'No se encontraron mesas.'}
                            </div>
                        </div>
                    `);
                }
            },

            error: function (xhr) {

                console.error('Error al obtener mesas:', xhr.responseText);

                $('#tables_container').html(`
                    <div class="col-12">
                        <div class="alert alert-danger">
                            Error al cargar las mesas.
                        </div>
                    </div>
                `);
            }
        });
    }


    // =========================================================
    // MOSTRAR MESAS
    // =========================================================

    function render_tables(tables) {

        let html = '';

        if (!tables || tables.length === 0) {

            html = `
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        No hay mesas registradas.
                    </div>
                </div>
            `;

            $('#tables_container').html(html);
            return;
        }


        tables.forEach(function (table) {

            let status = String(table.status || 'LIBRE').toUpperCase();

            let statusClass = 'table-libre';
            let statusText = 'LIBRE';

            if (status === 'OCUPADA') {
                statusClass = 'table-ocupada';
                statusText = 'OCUPADA';
            }

            if (status === 'RESERVADA') {
                statusClass = 'table-reservada';
                statusText = 'RESERVADA';
            }

            if (status === 'INACTIVA') {
                statusClass = 'table-inactiva';
                statusText = 'INACTIVA';
            }


            html += `
                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 mb-4 table-item"
                     data-name="${String(table.table_name || '').toLowerCase()}"
                     data-status="${status}">

                    <div class="card shadow-sm h-100 ${statusClass}">

                        <div class="card-body text-center">

                            <div class="mb-2">
                                <i class="fas fa-utensils fa-2x"></i>
                            </div>

                            <h4 class="fw-bold mb-1">
                                ${escape_html(table.table_name)}
                            </h4>

                            <p class="mb-2 text-muted">
                                <i class="fas fa-users"></i>
                                ${table.seats || 0} personas
                            </p>

                            <span class="badge status-badge mb-3">
                                ${statusText}
                            </span>

                            <div class="d-flex justify-content-center gap-2">

                                <button
                                    type="button"
                                    class="btn btn-sm btn-primary btn-edit-table"
                                    data-id="${table.id}">
                                    <i class="fas fa-edit"></i>
                                    Editar
                                </button>

                                <button
                                    type="button"
                                    class="btn btn-sm btn-danger btn-delete-table"
                                    data-id="${table.id}"
                                    data-name="${escape_html(table.table_name)}">
                                    <i class="fas fa-trash"></i>
                                    Eliminar
                                </button>

                            </div>

                        </div>

                    </div>

                </div>
            `;
        });


        $('#tables_container').html(html);
    }


    // =========================================================
    // NUEVA MESA
    // =========================================================

    $(document).on('click', '#btn_new_table, #btn_nueva_mesa', function () {

        editingId = 0;

        $('#table_id').val('');
        $('#table_name').val('');
        $('#table_seats').val('');
        $('#table_status').val('LIBRE');

        $('#modal_table .modal-title').text('Nueva Mesa');

        $('#modal_table').modal('show');
    });


    // =========================================================
    // EDITAR MESA
    // =========================================================

    $(document).on('click', '.btn-edit-table', function () {

        let id = $(this).data('id');

        get_table_by_id(id);
    });


    function get_table_by_id(id) {

        $.ajax({
            url: BASE_URL + 'RestaurantTables/get_table_by_id',
            type: 'GET',
            data: {
                id: id
            },
            dataType: 'json',

            success: function (response) {

                if (response.status === 'OK') {

                    let table = response.data;

                    editingId = table.id;

                    $('#table_id').val(table.id);
                    $('#table_name').val(table.table_name);
                    $('#table_seats').val(table.seats);
                    $('#table_status').val(table.status);

                    $('#modal_table .modal-title').text('Editar Mesa');

                    $('#modal_table').modal('show');

                } else {

                    alert(response.msg || 'No se pudo obtener la mesa.');
                }
            },

            error: function (xhr) {

                console.error(
                    'Error al obtener mesa:',
                    xhr.responseText
                );

                alert('Error al obtener los datos de la mesa.');
            }
        });
    }


    // =========================================================
    // GUARDAR MESA
    // =========================================================

    $(document).on('click', '#btn_save_table', function () {

        let id = $('#table_id').val();
        let name = $.trim($('#table_name').val());
        let seats = parseInt($('#table_seats').val());
        let status = $('#table_status').val();


        // -----------------------------------------------------
        // VALIDACIONES
        // -----------------------------------------------------

        if (name === '') {

            alert('Ingrese el nombre de la mesa.');
            $('#table_name').focus();

            return;
        }


        if (!seats || seats <= 0) {

            alert('Ingrese una cantidad válida de personas.');
            $('#table_seats').focus();

            return;
        }


        if (status === '') {
            status = 'LIBRE';
        }


        let data = {
            table_name: name,
            seats: seats,
            status: status
        };


        // -----------------------------------------------------
        // EDITAR
        // -----------------------------------------------------

        if (id !== '') {

            data.id = id;

            $.ajax({
                url: BASE_URL + 'RestaurantTables/update_table',
                type: 'POST',
                data: data,
                dataType: 'json',

                success: function (response) {

                    if (response.status === 'OK') {

                        $('#modal_table').modal('hide');

                        alert(
                            response.msg ||
                            'Mesa actualizada correctamente.'
                        );

                        load_tables();

                    } else {

                        alert(
                            response.msg ||
                            'No se pudo actualizar la mesa.'
                        );
                    }
                },

                error: function (xhr) {

                    console.error(
                        'Error al actualizar:',
                        xhr.responseText
                    );

                    alert('Error al actualizar la mesa.');
                }
            });

        }

        // -----------------------------------------------------
        // CREAR
        // -----------------------------------------------------

        else {

            $.ajax({
                url: BASE_URL + 'RestaurantTables/create_table',
                type: 'POST',
                data: data,
                dataType: 'json',

                success: function (response) {

                    if (response.status === 'OK') {

                        $('#modal_table').modal('hide');

                        alert(
                            response.msg ||
                            'Mesa creada correctamente.'
                        );

                        load_tables();

                    } else {

                        alert(
                            response.msg ||
                            'No se pudo crear la mesa.'
                        );
                    }
                },

                error: function (xhr) {

                    console.error(
                        'Error al crear:',
                        xhr.responseText
                    );

                    alert('Error al crear la mesa.');
                }
            });
        }
    });


    // =========================================================
    // ELIMINAR MESA
    // =========================================================

    $(document).on('click', '.btn-delete-table', function () {

        let id = $(this).data('id');
        let name = $(this).data('name');


        if (!confirm(
            '¿Está seguro de eliminar la mesa "' +
            name +
            '"?'
        )) {

            return;
        }


        $.ajax({
            url: BASE_URL + 'RestaurantTables/delete_table',
            type: 'POST',
            data: {
                id: id
            },
            dataType: 'json',

            success: function (response) {

                if (response.status === 'OK') {

                    alert(
                        response.msg ||
                        'Mesa eliminada correctamente.'
                    );

                    load_tables();

                } else {

                    alert(
                        response.msg ||
                        'No se pudo eliminar la mesa.'
                    );
                }
            },

            error: function (xhr) {

                console.error(
                    'Error al eliminar:',
                    xhr.responseText
                );

                alert('Error al eliminar la mesa.');
            }
        });
    });


    // =========================================================
    // CAMBIAR ESTADO
    // =========================================================

    $(document).on('change', '.change-table-status', function () {

        let id = $(this).data('id');
        let status = $(this).val();


        $.ajax({
            url: BASE_URL + 'RestaurantTables/update_status',
            type: 'POST',
            data: {
                id: id,
                status: status
            },
            dataType: 'json',

            success: function (response) {

                if (response.status === 'OK') {

                    load_tables();

                } else {

                    alert(
                        response.msg ||
                        'No se pudo actualizar el estado.'
                    );
                }
            },

            error: function (xhr) {

                console.error(
                    'Error de estado:',
                    xhr.responseText
                );

                alert('Error al actualizar el estado.');
            }
        });
    });


    // =========================================================
    // BUSCADOR
    // =========================================================

    $(document).on(
        'keyup',
        '#search_table, #search_tables',
        function () {

            let search = $.trim(
                $(this).val().toLowerCase()
            );


            $('.table-item').each(function () {

                let name = $(this)
                    .data('name')
                    .toString()
                    .toLowerCase();


                if (name.indexOf(search) !== -1) {

                    $(this).show();

                } else {

                    $(this).hide();
                }
            });
        }
    );


    // =========================================================
    // FILTRO POR ESTADO
    // =========================================================

    $(document).on(
        'change',
        '#filter_status, #table_filter_status',
        function () {

            let status = $(this).val();

            $('.table-item').each(function () {

                let tableStatus = $(this)
                    .data('status')
                    .toString()
                    .toUpperCase();


                if (
                    status === '' ||
                    status === 'TODOS' ||
                    tableStatus === status
                ) {

                    $(this).show();

                } else {

                    $(this).hide();
                }
            });
        }
    );


    // =========================================================
    // LIMPIAR MODAL AL CERRAR
    // =========================================================

    $('#modal_table').on('hidden.bs.modal', function () {

        $('#table_id').val('');
        $('#table_name').val('');
        $('#table_seats').val('');
        $('#table_status').val('LIBRE');

        editingId = 0;
    });


    // =========================================================
    // ESCAPAR HTML
    // =========================================================

    function escape_html(text) {

        if (text === null || text === undefined) {
            return '';
        }

        return String(text)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

});
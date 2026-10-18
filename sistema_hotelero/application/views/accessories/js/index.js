// -- Funciones principales

// -- Destruir DataTable antes de recargar
function destroy_datatable() {
    $('#datatable-accessories').dataTable().fnDestroy();
}

// -- Refrescar DataTable
function refresh_datatable() {
    $('#datatable-accessories').DataTable().ajax.reload();
}

// -- Cargar DataTable con accesorios
function load_datatable() {
    destroy_datatable();

    let dataTable = $('#datatable-accessories').DataTable({
        ajax: {
            url: BASE_URL + 'Accessories/get_accessories',
            cache: false,
        },
        columns: [
            { data: 'sku_accessory' },
            { data: 'accessory_name' },
            { 
                data: 'unit_type_description',
                render: function (data) {
                    return data ? data :  '-';
                } 
            },
            { data: 'accessory_price' }, 
            { data: 'accessory_stock' },         
            {
                class: 'center',
                render: function (data, type, row) {
                    return (
                        '<button class="btn btn-sm btn-info btn-round btn-icon btn_update" data-process-key="'+ row.id_accessory +'">' +
                        feather.icons['edit'].toSvg({ class: 'font-small-4' }) +
                        '</button>' + ' ' + 
                        '<button class="btn btn-sm btn-danger btn-round btn-icon btn_delete" data-process-key="'+ row.id_accessory +'">' +
                        feather.icons['trash-2'].toSvg({ class: 'font-small-4' }) +
                        '</button>'
                    );
                }
            },
        ],
        dom: functions.head_datatable(),
        buttons: functions.custom_buttons_datatable([2], '#create_accessory_modal'), // -- Number of columns
        language: {
            url: BASE_URL + 'public/assets/json/languaje-es.json'
        }
    });

    dataTable.on('xhr', function() {
        var data = dataTable.ajax.json();
        functions.toast_message(data.type, data.msg, data.status);
    });
}

// -- Crear Accesorio
function create_accessory(form) {
    $('#btn_create_accessory').prop('disabled', true);

    let params = new FormData(form);

    $.ajax({
        url: BASE_URL + 'Accessories/create_accessory',
        type: 'POST',
        data: params,
        dataType: 'json',
        contentType: false,
        processData: false,
        cache: false,
        beforeSend: function () {
            console.log('Cargando...');
        },
        success: function (data) {
            functions.toast_message(data.type, data.msg, data.status);

            if (data.status === 'OK') {
                $('#create_accessory_modal').modal('hide');
                form.reset();
                refresh_datatable();
            } else {
                $('#btn_create_accessory').prop('disabled', false);
            }
        }
    });
}

// -- Actualizar Accesorio
function update_accessory(form) {
    $('#btn_update_accessory').prop('disabled', true);

    let params = new FormData(form);

    $.ajax({
        url: BASE_URL + 'Accessories/update_accessory',
        type: 'POST',
        data: params,
        dataType: 'json',
        contentType: false,
        processData: false,
        cache: false,
        beforeSend: function () {
            console.log('Cargando...');
        },
        success: function (data) {
            functions.toast_message(data.type, data.msg, data.status);

            if (data.status === 'OK') {
                $('#update_accessory_modal').modal('hide');
                form.reset();
                refresh_datatable();
            } else {
                $('#btn_update_accessory').prop('disabled', false);
            }
        }
    });
}

// -- Eliminar Accesorio
$(document).on('click', '.btn_delete', function () {
    let value = $(this).attr('data-process-key');
    let params = { 'id_accessory': value };

    Swal.fire({
        title: '¿Estás seguro?',
        text: '¡No podrás revertir esto!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar!',
        customClass: {
            confirmButton: 'btn btn-primary',
            cancelButton: 'btn btn-outline-danger ms-1'
        },
        buttonsStyling: false,
        preConfirm: _ => {
            return $.ajax({
                url: BASE_URL + 'Accessories/delete_accessory',
                type: 'POST',
                data: params,
                dataType: 'json',
                cache: false,
                success: function (data) {
                    functions.toast_message(data.type, data.msg, data.status);
                    if (data.status === 'OK') {
                        refresh_datatable();
                    }
                }
            });
        }
    }).then(result => {
        if (result.isConfirmed) {
            // Acción confirmada
        }
    });
});

// -- Cargar accesorio para editar
$(document).on('click', '.btn_update', function () {
    let value = $(this).attr('data-process-key');
    let params = { 'id_accessory': value };

    $.ajax({
        url: BASE_URL + 'Accessories/get_accessory_by_id',
        type: 'GET',
        data: params,
        dataType: 'json',
        success: function (data) {
            if (data.status === 'OK') {
                let item = data.data;

                // Llenar el formulario de actualización con los datos del accesorio
                $('#update_accessory_form :input[name=id_accessory]').val(item.id_accessory);
                $('#update_accessory_form :input[name=accessory_description]').val(item.accessory_description);
                $('#update_accessory_form :input[name=accessory_price]').val(item.accessory_price);
                $('#update_accessory_form :input[name=accessory_stock]').val(item.accessory_stock);
            } else {
                console.log('Error: No se encontraron datos del accesorio.');
            }
        },
        error: function () {
            console.log('Error al cargar los datos del accesorio.');
        }
    });

    $('#update_accessory_modal').modal('show');
});

// -- Validar formularios y activar acciones
$('#create_accessory_form').validate({
    submitHandler: function (form) {
        create_accessory(form);
    }
});

$('#update_accessory_form').validate({
    submitHandler: function (form) {
        update_accessory(form);
    }
});

// -- Resetear formularios al cerrar el modal
$('.modal').on('hidden.bs.modal', function () {
    $(this).find('form')[0].reset();
    $('#btn_create_accessory').prop('disabled', false);
    $('#btn_update_accessory').prop('disabled', false);
});

// -- Cargar DataTable al cargar la página
load_datatable();


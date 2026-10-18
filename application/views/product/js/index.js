// -- Destruir DataTable antes de recargar
function destroy_datatable() {
    $('#datatable-product').dataTable().fnDestroy();
  }
  // -- Refrescar DataTable
  function refresh_datatable() {
    $('#datatable-product').DataTable().ajax.reload();
  }
  
// -- Cargar DataTable con productos
function load_datatable() {
    destroy_datatable();
    
    let dataTable = $('#datatable-product').DataTable({
        ajax: {
            url: BASE_URL + 'Product/get_products',
            cache: false,
        },
        columns: [
            { data: 'product_sku' },
            { data: 'product_name' },
            { 
                data: 'unit_type_description' ,
                render: function (data) {
                    return data ? data : '-';
                }
            },
            { data: 'category_description' },
            { data: 'product_price' },
            { data: 'product_stock' },
            { data: 'expiration_date' },
            { 
                data: 'status_expiration_date',
                render: function (data) {
                    if (data === 1) {
                        return '<span class="badge rounded-pill badge-light-success">Vigente</span>';
                    } else if (data === 0) {
                        return '<span class="badge rounded-pill badge-light-danger">Expirado</span>';
                    }
                    return '<span class="badge rounded-pill badge-light-warning">Desconocido</span>';
                }
            },
            {
                class: 'center',
                render: function (data, type, row) {
                    return (
                        '<button class="btn btn-sm btn-info btn-round btn-icon btn_update" data-process-key="' + row.id_product + '">' +
                        feather.icons['edit'].toSvg({ class: 'font-small-4' }) +
                        '</button>' +
                        ' ' +
                        '<button class="btn btn-sm btn-danger btn-round btn-icon btn_delete" data-process-key="' + row.id_product + '">' +
                        feather.icons['trash-2'].toSvg({ class: 'font-small-4' }) +
                        '</button>'
                    );
                }
            },
        ],
        dom: functions.head_datatable(),
        buttons: functions.custom_buttons_datatable([2], '#create_product_modal'),
        language: {
            url: BASE_URL + 'public/assets/json/languaje-es.json'
        }
    });
  
    dataTable.on('xhr', function () {
        var data = dataTable.ajax.json();
        functions.toast_message(data.type, data.msg, data.status);
    });
  }

// --
// -- Crear Producto
function create_product(form) {
    $('#btn_create_product').prop('disabled', true);
  
    let params = new FormData(form);
  
    $.ajax({
        url: BASE_URL + 'Product/create_product',
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
                $('#create_product_modal').modal('hide');
                form.reset();
                refresh_datatable();
            } else {
                $('#btn_create_product').prop('disabled', false);
            }
        }
    });
  }

// -- Cargar categorías para el formulario de create y update
function load_categories(selectElement) {
    $.ajax({
        url: BASE_URL + 'Product/get_categories',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            if (data.status === 'OK') {
                let categories = data.data;
                let options = '<option value="">Seleccionar categoría</option>';
                categories.forEach(category => {
                    options += `<option value="${category.id}">${category.description}</option>`;
                });
                $(selectElement).html(options);
            } else {
                console.log('No se encontraron categorías.');
            }
        },
        error: function() {
            console.log('Error al cargar categorías.');
        }
    });
  }

// Manejo de actualización del producto
$(document).on('click', '.btn_update', function () {
    let productId = $(this).attr('data-process-key');
    let params = { 'id_product': productId };
  
    $.ajax({
        url: BASE_URL + 'Product/get_product_by_id',
        type: 'GET',
        data: params,
        dataType: 'json',
        success: function (data) {
            if (data.status === 'OK') {
                let item = data.data;
  
                // Llenar el formulario de actualización con los datos del producto
                $('#update_product_form :input[name=id_product]').val(item.id_product);
                $('#update_product_form :input[name=product_sku]').val(item.product_sku);
                $('#update_product_form :input[name=product_name]').val(item.product_name);
                $('#update_product_form :input[name=product_description]').val(item.product_description);
                $('#update_product_form :input[name=product_price]').val(item.product_price);
                $('#update_product_form :input[name=product_stock]').val(item.product_stock);
                $('#update_product_form :input[name=expiration_date]').val(item.expiration_date);
                // Puedes agregar más campos aquí según sea necesario
            }
        },
        error: function () {
            alert('Error al cargar los datos del producto');
        }
    });
  });
  // Evento para actualizar el producto en el formulario
$('#update_product_form').submit(function (e) {
    e.preventDefault();  // Prevenir el comportamiento por defecto del formulario

    let formData = $(this).serialize();  // Serializa los datos del formulario

    $.ajax({
        url: BASE_URL + 'Product/update_product',  // Endpoint para actualizar el producto
        type: 'POST',
        data: formData,
        success: function (response) {
            if (response.status === 'OK') {
                // Cerrar el modal y refrescar la lista o redirigir si es necesario
                $('#update_product_modal').modal('hide');
                location.reload();  // O usar un método para actualizar la lista de productos
            } else {
                alert('Error al actualizar el producto: ' + response.message);
            }
        },
        error: function () {
            alert('Hubo un problema al intentar actualizar el producto');
        }
    });
});

// Manejo de eliminación del producto
$(document).on('click', '.btn_delete', function () {
    let productId = $(this).attr('data-process-key');

    Swal.fire({
        title: '¿Estás seguro?',
        text: 'Esta acción no se puede deshacer.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: BASE_URL + 'Product/delete_product',
                type: 'POST',
                data: { id_product: productId },
                dataType: 'json',
                success: function (data) {
                    functions.toast_message(data.type, data.msg, data.status);
                    if (data.status === 'OK') {
                        refresh_datatable();
                    }
                },
                error: function () {
                    console.log('Error al eliminar el producto.');
                }
            });
        }
    });
});

// -- Resetear formularios al cerrar el modal
$('.modal').on('hidden.bs.modal', function () {
    $(this).find('form')[0].reset();
    $('#btn_create_product').prop('disabled', false);
    $('#btn_update_product').prop('disabled', false);
  });
  // -- Cargar categorías al abrir modales de creación y actualización
  $('#create_product_modal').on('show.bs.modal', function () {
    load_categories('#create_product_form select[name=id_category]');
  });
  
  $('#update_product_modal').on('show.bs.modal', function () {
    load_categories('#update_product_form select[name=id_category]');
  });
  
  // -- Validar formularios y activar acciones
  $('#create_product_form').validate({
    submitHandler: function (form) {
        create_product(form);
    }
  });
  
  $('#update_product_form').validate({
    submitHandler: function (form) {
        update_product(form);
    }
  });
load_datatable();


// -- Funciones

// Función para destruir la tabla de datos existente
function destroy_datatable() {
    $('#datatable-clients').dataTable().fnDestroy(); // Destruye la tabla actual para recargarla o reiniciarla
}

// Función para refrescar la tabla de datos
function refresh_datatable() {
    $('#datatable-clients').DataTable().ajax.reload(); // Recarga los datos en la tabla
}

function load_datatable() {
    destroy_datatable(); // Primero destruye la tabla existente
    let dataTable = $('#datatable-clients').DataTable({
        // Configuración de la fuente de datos
        ajax: {
            url: BASE_URL + 'Clients/get_clients', // URL de donde se obtienen los datos
            cache: false, // No almacenar en caché
        },
        columns: [ // Definición de las columnas y qué datos mostrar
            { data: 'name' }, // Nombre
            { data: 'document_type' }, // Tipo de documento
            { data: 'document_number' }, // Número de documento
            { data: 'nationality' }, // Nationality
            { data: 'phone' }, // Teléfono
            { data: 'email' }, // Correo electrónico
            { // Botones de acción (editar/eliminar)
                class: 'center',
                render: function (data, type, row, meta) {
                    return (
                        '<button class="btn btn-sm btn-info btn-round btn-icon btn_update" data-process-key="' + row.id_clients + '">' +
                        feather.icons['edit'].toSvg({ class: 'font-small-4' }) + // Ícono de edición
                        '</button>'
                        + ' ' +
                        // Nuevo botón de eliminar con estructura personalizada
                        '<button class="btn btn-sm btn-danger btn-round btn-icon btn_delete_custom" data-process-key="' + row.id_clients + '">' +
                        feather.icons['trash-2'].toSvg({ class: 'font-small-4' }) + // Ícono de eliminación
                        '</button>'
                    );
                }
            },
        ],
        dom: functions.head_datatable(), // Configuración del diseño de la tabla
        buttons: functions.custom_buttons_datatable([7], '#create_clients_modal'), // Botones personalizados
        language: {
            url: BASE_URL + 'public/assets/json/languaje-es.json' // Traducción al español
        }
    });

    // Evento cuando los datos son cargados desde el servidor
    dataTable.on('xhr', function () {
        let data = dataTable.ajax.json();

        
    });

    $('#datatable-clients').on('click', '.btn_delete_custom', function () {
        var id_client = $(this).data('process-key');  // Obtiene el ID del cliente
        console.log("ID del cliente a eliminar:", id_client); // Log para verificar el ID

        if (id_client) {
            Swal.fire({
                title: '¿Estás seguro de eliminar este cliente?',
                text: "¡Esta acción no se puede deshacer!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Realiza la solicitud AJAX para eliminar el cliente
                    $.ajax({
                        url: BASE_URL + 'Clients/delete_clients', // Verifica la URL
                        type: 'POST',
                        data: { id_clients: id_client },  // Asegúrate de enviar el parámetro correcto
                        success: function (response) {
                            console.log("Respuesta del servidor:", response); // Log de la respuesta
                            if (response.status === 'OK') {
                                Swal.fire('¡Eliminado!', 'El cliente ha sido eliminado exitosamente.', 'success');
                                dataTable.ajax.reload();  // Recarga la tabla
                            } else {
                                Swal.fire('Error', 'Hubo un error al eliminar el cliente.', 'error');
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error("Error AJAX: ", error);
                            Swal.fire('Error', 'Hubo un problema con la solicitud', 'error');
                        }
                    });
                }
            });
        } else {
            Swal.fire('Error', 'No se encontró el ID del cliente', 'error');
        }
    });

}


//----------------------------------------------------------------



//----------------------------------------------------------------
// Función para obtener los tipos de documentos
function get_document_types() {
    $.ajax({
        url: BASE_URL + 'Main/get_document_types', // URL de la API para obtener tipos de documentos
        type: 'GET',
        dataType: 'json',
        cache: false,
        beforeSend: function () {
            console.log('Cargando...'); // Mensaje en consola mientras se carga
        },
        success: function (data) {
            if (data.status === 'OK') { // Si la respuesta es exitosa
                var html = '<option value="">Seleccionar</option>';
                data.data.forEach(element => {
                    html += '<option value="' + element.id + '">' + element.description + '</option>'; // Opciones del select
                });
                $('#create_clients_form :input[name=document_type]').html(html); // Agregar opciones al formulario de creación
                $('#update_clients_form :input[name=document_type]').html(html); // Agregar opciones al formulario de actualización
            }
        }
    });
}

// Evento para obtener datos de empresa basados en el documento
$(document).on("click", ".btn_get_company_data", function () {
    console.log("CLICK OK")
    let documentType = $("#document_type").val(); // Tipo de documento
    let nroDoc = $("#document_number").val(); // Número de documento

    // Validaciones del número de documento
    if (!nroDoc) {
        functions.toast_message("error", "Ingrese un número de documento", "ERROR");
        return;
    }
    if (documentType == 1 && nroDoc.length !== 8) {
        functions.toast_message("error", "Ingrese un DNI válido (8 dígitos)", "ERROR");
        return;
    }
    if (documentType == 2 && nroDoc.length !== 11) {
        functions.toast_message("error", "Ingrese un RUC válido (11 dígitos)", "ERROR");
        return;
    }

    // Llamada a la API para obtener datos
    $.ajax({
    url: BASE_URL + "Clients/get_client_by_dni",
    type: "GET",
    data: { dni: nroDoc },
    dataType: "json",
    success: function (responseBD) {
        console.log(responseBD);

        if (responseBD.status === "OK") {

            let data = responseBD.data;

            $("#create_clients_modal :input[name=name]").val(data.name);
            $("#create_clients_modal :input[name=address]").val(data.address || "");

            functions.toast_message("success", "Datos cargados desde BD", "OK");

        } else {

            // 2. SI NO EXISTE → USA API
            $.ajax({
                url: BASE_URL + "Clients/get_company_data",
                type: "GET",
                data: { nroDoc: nroDoc },
                dataType: "json",
                success: function (response) {

                    if (response.status === "OK") {

                        let data = response.data;

                        if (documentType == 1) {
                            $("#create_clients_modal :input[name=name]")
                                .val(`${data.nombres} ${data.apellidoPaterno} ${data.apellidoMaterno}`);

                            $("#create_clients_modal :input[name=address]")
                                .val(data.direccion_completa || "");

                        } else if (documentType == 2) {

                            $("#create_clients_modal :input[name=name]").val(data.razonSocial);
                            $("#create_clients_modal :input[name=address]").val(data.direccion);
                        }

                        functions.toast_message("info", "Datos cargados desde API", "OK");

                    } else {
                        functions.toast_message("error", "No encontrado en BD ni API", "ERROR");
                    }
                }
            });

        }
    }
});
});
////////////////////////////////////////////////////////////////

// Función para crear nuevos clientes
function create_clients(form) {
    $('#btn_create_clients').prop('disabled', true); // Deshabilita el botón mientras se procesa
    let params = new FormData(form); // Captura los datos del formulario
    let documentType = $('#create_clients_form :input[name=document_type]').find('option:selected').text(); // Obtiene el texto del tipo de documento seleccionado
    params.append('description_document_type', documentType); // Agrega el tipo de documento a los parámetros

    // Asegurarse de que los campos opcionales estén presentes aunque estén vacíos
    const optionalFields = ['birth_date', 'email', 'phone', 'address', 'business_name'];
    optionalFields.forEach(field => {
        if (!params.has(field)) {
            params.append(field, ''); // Agrega el campo con valor vacío
        }
    });

    // Llamada AJAX para enviar los datos al servidor
    $.ajax({
        url: BASE_URL + 'Clients/create_clients', // URL para crear el cliente
        type: 'POST',
        data: params,
        dataType: 'json',
        contentType: false,
        processData: false,
        cache: false,
        beforeSend: function () {
            console.log('Cargando...'); // Mensaje en consola
        },
        success: function (data) {
            functions.toast_message(data.type, data.msg, data.status); // Notifica el resultado de la operación
            if (data.status === 'OK') { // Si fue exitoso
                $('#create_clients_modal').modal('hide'); // Cierra el modal
                form.reset(); // Limpia el formulario
                refresh_datatable(); // Refresca la tabla
            } else {
                $('#btn_create_clients').prop('disabled', false); // Habilita el botón si hubo un error
            }
        }
    });
}

// Función para actualizar un cliente existente
function update_clients(form) {
    $('#btn_update_clients').prop('disabled', true); // Deshabilita el botón mientras se procesa
    let params = new FormData(form); // Captura los datos del formulario
    let documentType = $('#update_clients_form :input[name=document_type]').find('option:selected').text(); // Obtiene el texto del tipo de documento seleccionado
    params.append('description_document_type', documentType); // Agrega el tipo de documento a los parámetros

    // Llamada AJAX para enviar los datos actualizados
    $.ajax({
        url: BASE_URL + 'Clients/update_clients', // URL para actualizar el cliente
        type: 'POST',
        data: params,
        dataType: 'json',
        contentType: false,
        processData: false,
        cache: false,
        beforeSend: function () {
            console.log('Cargando...'); // Mensaje en consola
        },
        success: function (data) {
            // Manejo de la respuesta del servidor
            functions.toast_message(data.type, data.msg, data.status); // Notifica el resultado de la operación
            if (data.status === 'OK') { // Si fue exitoso
                $('#update_clients_modal').modal('hide'); // Cierra el modal
                form.reset(); // Limpia el formulario
                refresh_datatable(); // Refresca la tabla
            } else {
                // Si hubo un error, habilita el botón
                $('#btn_update_clients').prop('disabled', false);
            }
        },
        error: function (xhr, status, error) {
            // Manejo de errores del servidor
            console.error('Error en la solicitud:', status, error);
            functions.toast_message('error', 'Hubo un problema al intentar actualizar el cliente. Intenta nuevamente.', 'ERROR');
            $('#btn_update_clients').prop('disabled', false); // Habilita el botón si hay error
        },
        complete: function () {
            console.log('Proceso de actualización finalizado.'); // Mensaje final
        }
    });
}


// Eventos

// Evento para cargar datos en el formulario de actualización
$(document).on('click', '.btn_update', function () {
    let value = $(this).attr('data-process-key'); // Obtiene el identificador del cliente
    let params = { 'id_clients': value }; // Prepara los parámetros

    // Llamada AJAX para obtener los datos del cliente
    $.ajax({
        url: BASE_URL + 'Clients/get_client_by_id', // URL para obtener los datos
        type: 'GET',
        data: params,
        dataType: 'json',
        success: function (data) {
            if (data.status === 'OK') { // Si fue exitoso
                let item = data.data;
                $('#update_clients_form :input[name=id_clients]').val(item.id_clients); // Rellena los campos con los datos obtenidos
                $('#update_clients_form :input[name=name]').val(item.name);
                $('#update_clients_form :input[name=document_number]').val(item.document_number);
                $('#update_clients_form :input[name=nationality]').val(item.nationality);
                $('#update_clients_form :input[name=birth_date]').val(item.birth_date);
                $('#update_clients_form :input[name=birth_place]').val(item.birth_place);
                $('#update_clients_form :input[name=address]').val(item.address);
                $('#update_clients_form :input[name=phone]').val(item.phone);
                $('#update_clients_form :input[name=email]').val(item.email);
                $('#update_clients_form :input[name=business_name]').val(item.business_name);
                $('#update_clients_form :input[name=document_type]').val(item.id_document_type).trigger('change'); // Actualiza el tipo de documento
            }
        }
    });

    $('#update_clients_modal').modal('show'); // Muestra el modal de actualización
});
//----------------------------------------------------------------
// Evento para eliminar un cliente
$(document).on('click', '.btn_delete', function () {
    let value = $(this).attr('data-process-key'); // Obtiene el identificador del cliente
    let params = { 'id_clients': value }; // Prepara los parámetros

    // Mostrar confirmación al usuario antes de eliminar
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
        preConfirm: () => {
            return $.ajax({
                url: BASE_URL + 'Clients/delete_clients', // URL para eliminar el cliente
                type: 'POST',
                data: params,
                dataType: 'json',
            }).done(function (data) {
                // Notifica el resultado de la eliminación
                functions.toast_message(data.type, data.msg, data.status);

                if (data.status === 'OK') {
                    refresh_datatable(); // Refresca la tabla si se eliminó con éxito
                }
            }).fail(function (jqXHR, textStatus, errorThrown) {
                // Maneja errores de la solicitud
                functions.toast_message('error', 'Error en la comunicación con el servidor.', 'ERROR');
            });
        }
    }).then(result => {
        if (result.isConfirmed) {
            console.log('Eliminación confirmada por el usuario.');
        } else {
            console.log('Eliminación cancelada por el usuario.');
        }
    });
});




////////////////////////////////////////////////////////////////




////////////////////////////////////////////////////////////////











//----------------------------------------------------------------

$(document).on('click', '.btn_delete', function () {
    let value = $(this).attr('data-process-key'); // Obtiene el identificador del cliente
    let params = { 'id_clients': value }; // Prepara los parámetros

    // Mostrar confirmación al usuario antes de eliminar
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
                url: BASE_URL + 'Clients/delete_clients', // URL para eliminar el cliente
                type: 'POST',
                data: params,
                dataType: 'json',
                success: function (data) {
                    // Mostrar mensaje de éxito o error según el estado
                    if (data.status === 'OK') {
                        functions.toast_message('success', 'El cliente se ha eliminado correctamente de la base de datos.', 'OK');
                        refresh_datatable(); // Refresca la tabla
                    } else {
                        functions.toast_message('error', data.msg || 'No se pudo eliminar el cliente.', 'ERROR');
                    }
                },
                error: function () {
                    functions.toast_message('error', 'Hubo un error al intentar eliminar el cliente. Verifique la conexión.', 'ERROR');
                }
            });
        }
    }).then(result => {
        if (result.isConfirmed) {
            // Opcional: Agregar acciones adicionales si se confirma
        }
    });
});


//----------------------------------------------------------------
// Evento para resetear formularios
$(document).on('click', '.reset', function () {
    $('#create_clients_form').validate().resetForm(); // Resetea las validaciones del formulario de creación
    $('#update_clients_form').validate().resetForm(); // Resetea las validaciones del formulario de actualización
});

// Validar y enviar el formulario de creación
$('#create_clients_form').validate({
    submitHandler: function (form) {
        create_clients(form); // Llama a la función de creación
    }
});

// Validar y enviar el formulario de actualización
$('#update_clients_form').validate({
    submitHandler: function (form) {
        update_clients(form); // Llama a la función de actualización
    }
});

// Evento para resetear formularios al cerrar un modal
$('.modal').on('hidden.bs.modal', function () {
    $(this).find('form')[0].reset(); // Limpia el formulario
    $('#btn_create_clients').prop('disabled', false); // Habilita el botón de creación
    $('#btn_update_clients').prop('disabled', false); // Habilita el botón de actualización
});

// Inicialización de la tabla y obtención de tipos de documentos al cargar la página
get_document_types(); // Carga los tipos de documentos
load_datatable(); // Carga la tabla de datos

// Validar duplicados cuando cambie el número de documento
$('#document_number').on('change', function() {
    let documentNumber = $(this).val();
    let documentType = $('#document_type').val(); // ID Numérico (1, 2)
    let documentTypeName = $('#document_type').find('option:selected').text(); // Texto legible (DNI, RUC)

    if (documentNumber.length > 0 && documentType !== "") {
        $.ajax({
            url: BASE_URL + 'Clients/check_duplicate', // Usando BASE_URL en mayúsculas como tu script
            type: 'POST',
            data: { 
                document_number: documentNumber,
                document_type: documentType 
            },
            dataType: 'json',
            success: function(response) {
                if (response.exists) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Documento Duplicado',
                        text: 'El tipo ' + documentTypeName + ' con Nº ' + documentNumber + ' ya se encuentra registrado.',
                        confirmButtonColor: '#ea5455'
                    });
                    
                    // Limpiamos el campo para que se le obligue a corregirlo
                    $('#document_number').val('');
                }
            }
        });
    }
});
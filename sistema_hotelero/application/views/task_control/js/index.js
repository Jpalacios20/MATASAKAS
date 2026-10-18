function destroy_datatable() {
    if ($.fn.DataTable.isDataTable('#datatable-task_control')) {
        $('#datatable-task_control').DataTable().destroy();
    }
}

// -- Refrescar DataTable
function refresh_datatable() {
    if ($.fn.DataTable.isDataTable('#datatable-task_control')) {
        $('#datatable-task_control').DataTable().ajax.reload(null, false); // false para mantener la página actual
    }
}
function load_datatable() {
    destroy_datatable();
    
    let dataTable = $('#datatable-task_control').DataTable({
        ajax: {
            url: BASE_URL + 'task_control/get_task_control',
            cache: false,
        },
        columns: [
            { "data": "entry_date" },
            { "data": "start_date" },
            { "data": "end_date" },
            { "data": "leader" },
            { "data": "project_name" },
            { "data": "service_status" },
            { "data": "payment_status" },
            { "data": "outstanding_balance" },
            {
                class: 'center',
                render: function (data, type, row) {
                    return (
                        // Botón para agregar un proceso de desarrollo
                        '<button class="btn btn-sm btn-success btn-round btn-icon btn_add_process" data-process-key="' + row.id + '">' +
                        feather.icons['refresh-cw'].toSvg({ class: 'font-small-4' }) + 
                        '</button>' +
                        ' ' +
                        // Botón para editar
                        '<button class="btn btn-sm btn-danger btn-round btn-icon btn_update" data-bs-toggle="modal" data-bs-target="#update_service_modal" data-process-key="' + row.id_task_control + '">' +
                        feather.icons['edit'].toSvg({ class: 'font-small-4' }) + // Ícono de edición
                        '</button>' +
                        ' '+

                        // Botón para funcionalidad relacionada con carrito o compra
                        '<button class="btn btn-sm btn-success btn-round btn-icon btn_cart" data-process-key="' + row.id_task_control + '">' +
                        feather.icons['shopping-cart'].toSvg({ class: 'font-small-4' }) + // Ícono de carrito
                        '</button>'
                    );
                }
            },            
            
        ],
        dom: functions.head_datatable(),
        buttons: functions.custom_buttons_datatable([2], '#create_task_control_modal'),
        language: {
            url: BASE_URL + 'public/assets/json/languaje-es.json'
        }
    });
  
    dataTable.on('xhr', function () {
        var data = dataTable.ajax.json();
        functions.toast_message(data.type, data.msg, data.status);
    });
  }

  $(document).on('click', '.btn_cart', function() {
    $('#create_payment_modal').modal('show');
});


//Este sirve para que ala hora de precionar el estudante te jale todos los estudiantes que estan en la bd
function get_nombres_apellidos() {
    $.ajax({
        url: BASE_URL + "Main/get_nombres_apellidos",
        type: "GET",
        dataType: "json",
        contentType: false,
        processData: false,
        cache: false,
        beforeSend: function () {
            console.log("Cargando...");
        },
        success: function (data) {
            if (data.status === "OK") {
                var html = '<option value="">Seleccionar</option>';
                data.data.forEach((element) => {
                    html +=
                        '<option value="' +
                        element.idpracticante +
                        '">' +
                        element.nombres_apellidos +
                        "</option>";
                });
                $("#estudiantesSelect").html(html); // Corregido el selector
            }
        },
    });
}
//Este sirve para que al seleccionar el estudiante se agregue de manera automatica los otros 3 campos
$("#estudiantesSelect").change(function() {
    var idpracticante = $(this).val();
    if (idpracticante) {
        $.ajax({
            url: BASE_URL + "Main/get_practicante_by_id",
            type: "GET",
            data: { idpracticante: idpracticante }, // Envía el ID del practicante
            dataType: "json",
            success: function (data) {
                if (data.status === "OK") {
                    $("input[name='documento']").val(data.data.dni);
                    $("input[name='sede']").val(data.data.sede);
                    $("input[name='telefono']").val(data.data.numero);
                } else {
                    // Manejar el error (limpiar los campos, mostrar un mensaje, etc.)
                }
            },
            error: function () {
                // Manejar el error de la petición AJAX
            }
        });
    } else {
        // Limpiar los campos si no se selecciona un estudiante
        $("input[name='documento']").val("");
        $("input[name='sede']").val("");
        $("input[name='telefono']").val("");
    }
});


/////////////////////////////////////////////////////////////////////////////////////////////////////////////
function calcularDiasRestantes(fechaInicio, fechaFin, diasRestantesElement, progresoDiaActualElement) {
    // Convertir las fechas a objetos Date en UTC
    const inicioParts = fechaInicio.split('-');
    const finParts = fechaFin.split('-');

    const inicio = new Date(Date.UTC(parseInt(inicioParts[0]), parseInt(inicioParts[1]) - 1, parseInt(inicioParts[2])));
    const fin = new Date(Date.UTC(parseInt(finParts[0]), parseInt(finParts[1]) - 1, parseInt(finParts[2])));

    const ahora = new Date();
    const ahoraUTC = new Date(Date.UTC(ahora.getFullYear(), ahora.getMonth(), ahora.getDate())); // Fecha actual en UTC

    // Calcular la diferencia en milisegundos
    const diferenciaTotal = fin - inicio;
    const diferenciaActual = ahoraUTC - inicio;

    // Calcular la diferencia en días
    const diasTotales = Math.ceil(diferenciaTotal / (1000 * 60 * 60 * 24));
    const diasTranscurridos = Math.ceil(diferenciaActual / (1000 * 60 * 60 * 24));

    // Asegurar que los días transcurridos no sean negativos
    const diasTranscurridosAjustado = Math.max(0, diasTranscurridos);

    // Calcular días restantes
    const diasRestantes = Math.max(0, diasTotales - diasTranscurridosAjustado);

    // Calcular porcentaje de progreso
    let porcentaje = 0;
    if (diasTotales > 0) {
        porcentaje = Math.min(100, (diasTranscurridosAjustado / diasTotales) * 100);
    } else {
        porcentaje = 100; // Si las fechas son iguales, el porcentaje es 100%
    }

    // Actualizar los elementos en el HTML
    diasRestantesElement.value = diasRestantes;
    progresoDiaActualElement.style.width = porcentaje + '%';
    progresoDiaActualElement.textContent = Math.round(porcentaje) + '%';
}

function agregarEventosCalculoFechas() {
    const processRows = document.querySelectorAll('#process_development_modal .process-row');

    processRows.forEach(row => {
        const fechaInicioInput = row.querySelector('.fecha-inicio');
        const fechaTerminoInput = row.querySelector('.fecha-termino');
        const diasRestantesInput = row.querySelector('.dias-restantes');
        const progresoDiaActualDiv = row.querySelector('.progreso-dia-actual');

        if (fechaInicioInput && fechaTerminoInput && diasRestantesInput && progresoDiaActualDiv) {
            fechaInicioInput.addEventListener('change', actualizarCalculos);
            fechaTerminoInput.addEventListener('change', actualizarCalculos);

            function actualizarCalculos() {
                if (fechaInicioInput.value && fechaTerminoInput.value) {
                    calcularDiasRestantes(
                        fechaInicioInput.value,
                        fechaTerminoInput.value,
                        diasRestantesInput,
                        progresoDiaActualDiv
                    );
                }
            }

            // Realizar el cálculo inicial si las fechas ya están presentes
            if (fechaInicioInput.value && fechaTerminoInput.value) {
                actualizarCalculos();
            }
        }
    });

    //Mantenimiento
    const fechaInicioMantenimiento = document.querySelector('.fecha-inicio-mantenimiento');
    const fechaTerminoMantenimiento = document.querySelector('.fecha-termino-mantenimiento');
    const diasRestantesMantenimiento = document.querySelector('.dias-restantes-mantenimiento');
    const progresoDiaActualMantenimiento = document.querySelector('.progreso-dia-actual-mantenimiento');

    if (fechaInicioMantenimiento && fechaTerminoMantenimiento && diasRestantesMantenimiento && progresoDiaActualMantenimiento) {
        fechaInicioMantenimiento.addEventListener('change', actualizarCalculosMantenimiento);
        fechaTerminoMantenimiento.addEventListener('change', actualizarCalculosMantenimiento);

        function actualizarCalculosMantenimiento() {
            if (fechaInicioMantenimiento.value && fechaTerminoMantenimiento.value) {
                calcularDiasRestantes(
                    fechaInicioMantenimiento.value,
                    fechaTerminoMantenimiento.value,
                    diasRestantesMantenimiento,
                    progresoDiaActualMantenimiento
                );
            }
        }

        if (fechaInicioMantenimiento.value && fechaTerminoMantenimiento.value) {
            actualizarCalculosMantenimiento();
        }
    }
}

//esto nose que es pero no se borra
$(document).ready(function() {
    $(document).on('click', '.btn_add_process', function() {
        var idTaskControl = $(this).data('process-key');

        $.ajax({
            url: BASE_URL + 'task_control/get_task_control_process_modal',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ id_task_control: idTaskControl }),
            success: function(response) {
                if (response.success) {
                    $('#estudiantes').val(response.estudiante);
                    $('#project_name').val(response.proyecto);
                    $('#project_name').trigger('change');
                    $('#process_development_modal').modal('show');

                    // Agregar event listeners y realizar cálculos después de mostrar el modal
                    agregarEventosCalculoFechas();
                } else {
                    alert(response.msg);
                }
            },
            error: function() {
                alert('Error al procesar la solicitud.');
            }
        });
    });
});
/////////////////////////////////////////////////////////////////////////////////////////////////////////////

//AGREGAR A LOS INTEGRANTES
function create_task(form) {
    $('#btn_create_task_control').prop('disabled', true);

    let params = new FormData(form);

    // Añadir campos faltantes
    const additionalFields = [
        'start_date', 'end_date'
    ];

    additionalFields.forEach(field => {
        if (!params.has(field)) {
            params.append(field, '');
        }
    });

    $.ajax({
        url: BASE_URL + 'C_task_control/create_task', 
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
            console.log('Respuesta del servidor:', data);
            functions.toast_message(data.type, data.msg, data.status);
            if (data.status === 'OK') {
                $('#create_task_control_modal').modal('hide');
                form.reset();
                refresh_datatable();
            } else {
                $('#btn_create_task_control').prop('disabled', false);
            }
        }
    });
}

//SIRVE PARA GUARDAR Y OTRA COSA XD
$(document).ready(function() {
    // Lista temporal para almacenar los integrantes
    let integrantes = [];

    // Evento para agregar integrantes
    $('#agregarIntegranteBtn').on('click', function () {
        const dni = $('#dniIntegrante').val().trim();

        if (dni) {
            $.ajax({
                url: BASE_URL + "Main/get_integrante_by_dni",
                type: "GET",
                data: { dni: dni },
                dataType: "json",
                success: function(data) {
                    if (data.status === "OK") {
                        const integrante = data.data;
                        const integranteTexto = `${integrante.nombres_apellidos} (${integrante.dni})`;

                        integrantes.push(integranteTexto); // Agrega el nuevo integrante a la lista

                        $('#integrantesAgregados').append(`<div class="integrante-item">${integranteTexto}</div>`);

                        Swal.fire({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: 'Se agregó el integrante correctamente.',
                            confirmButtonText: 'OK'
                        });

                        $('#dniIntegrante').val('');
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Integrante no encontrado.'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error al buscar el integrante.'
                    });
                }
            });
        } else {
            Swal.fire({
                icon: 'warning',
                title: 'Atención',
                text: 'Por favor, ingrese un DNI válido.'
            });
        }
    });

    $('#create_task_control_form').on('submit', function(e) {
        e.preventDefault();

        var formData = {
            nombres_apellidos: $('#estudiantesSelect option:selected').text(),
            documento: $('input[name="documento"]').val(),
            sede: $('input[name="sede"]').val(),
            telefono: $('input[name="telefono"]').val(),
            nombre_proyecto: $('input[placeholder="Nombre del proyecto"]').val(),
            costo_desarrollo: $('input[placeholder="Monto"]').val(),
            estado_servicio: $('select[name="estado_servicio"]').val(),
            estado_entrega: $('select[name="estado_entrega"]').val(),
            estado_pago: $('select[name="estado_pago"]').val(),
            integrantes: integrantes.join(', ') // Agrega los integrantes al formData
        };

        $.ajax({
            url: BASE_URL + 'task_control/create_task_control',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'OK') {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: response.msg,
                        confirmButtonText: 'OK'
                    });
                    $('#create_task_control_modal').modal('hide');
                    $('#create_task_control_form')[0].reset();
                    $('#integrantesAgregados').empty(); // Limpia la lista de integrantes
                    integrantes = []; // Reinicia la lista temporal
                    refresh_datatable();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.msg,
                        confirmButtonText: 'OK'
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al procesar la solicitud.',
                    confirmButtonText: 'OK'
                });
            }
        });
    });
});

//funciona para que en procesos aparesca el nombre y el nombre del proyecto
$(document).ready(function() {
    $(document).on('click', '.btn_add_process', function() {
        var idTaskControl = $(this).data('process-key');

        $.ajax({
            url: BASE_URL + 'task_control/get_task_control_process_modal',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ id_task_control: idTaskControl }),
            success: function(response) {
                if (response.success) {
                    $('#estudiantes').val(response.estudiante);
                    $('#project_name').val(response.proyecto);
                    $('#project_name').trigger('change'); // Forzar una actualización del DOM
                    $('#process_development_modal').modal('show');
                } else {
                    alert(response.msg);
                }
            },
            error: function() {
                alert('Error al procesar la solicitud.');
            }
        });
    });
});




$(document).ready(function() {
    $('#process_development_form').submit(function(e) {
        e.preventDefault();

        let data = {
            AN_start_date: $(this).find('.fecha-inicio').eq(0).val(),
            AN_end_date: $(this).find('.fecha-termino').eq(0).val(),
            AN_status: $(this).find('.form-select').eq(0).val(),
            AN_comment: $(this).find('textarea').eq(0).val(),
            DI_start_date: $(this).find('.fecha-inicio').eq(1).val(),
            DI_end_date: $(this).find('.fecha-termino').eq(1).val(),
            DI_status: $(this).find('.form-select').eq(1).val(),
            DI_comment: $(this).find('textarea').eq(1).val(),
            DE_start_date: $(this).find('.fecha-inicio').eq(2).val(),
            DE_end_date: $(this).find('.fecha-termino').eq(2).val(),
            DE_status: $(this).find('.form-select').eq(2).val(),
            DE_comment: $(this).find('textarea').eq(2).val(),
            IM_start_date: $(this).find('.fecha-inicio').eq(3).val(),
            IM_end_date: $(this).find('.fecha-termino').eq(3).val(),
            IM_status: $(this).find('.form-select').eq(3).val(),
            IM_comment: $(this).find('textarea').eq(3).val(),
            MAN_start_date: $(this).find('.fecha-inicio-mantenimiento').val(),
            MAN_end_date: $(this).find('.fecha-termino-mantenimiento').val(),
            MAN_status: $(this).find('.estado-mantenimiento').val(),
            MAN_comment: $(this).find('.comentario-mantenimiento').val()
        };

        $.ajax({
            url: BASE_URL + 'task_control/save_development_process',
            type: 'POST',
            data: JSON.stringify(data),
            contentType: 'application/json',
            success: function(response) {
                console.log('Respuesta del servidor:', response);
                functions.toast_message(response.type, response.msg, response.status);
                if (response.status === 'OK') {
                    $('#process_development_modal').modal('hide');
                    $('#process_development_form')[0].reset();
                }
            },
            error: function() {
                console.error('Error en la petición AJAX:', error);
                alert('Error al guardar los datos.');
            }
        });
    });
});
$(document).ready(function() {
    $(document).on('click', '.btn_add_process', function() {
        let developmentId = $(this).data('process-key'); // Asumiendo que tienes el ID aquí

        $.ajax({
            url: BASE_URL + 'task_control/get_development_process',
            type: 'POST',
            data: JSON.stringify({ development_id: developmentId }),
            contentType: 'application/json',
            success: function(response) {
                if (response.status === 'OK') {
                    // Cargar los datos en el modal
                    let data = response.data;
                    $('#AN_start_date').val(data.AN_start_date);
                    $('#AN_end_date').val(data.AN_end_date);
                    $('#AN_status').val(data.AN_status);
                    $('#AN_comment').val(data.AN_comment);
                    $('#DI_start_date').val(data.DI_start_date);
                    $('#DI_end_date').val(data.DI_end_date);
                    $('#DI_status').val(data.DI_status);
                    $('#DI_comment').val(data.DI_comment);
                    $('#DE_start_date').val(data.DE_start_date);
                    $('#DE_end_date').val(data.DE_end_date);
                    $('#DE_status').val(data.DE_status);
                    $('#DE_comment').val(data.DE_comment);
                    $('#IM_start_date').val(data.IM_start_date);
                    $('#IM_end_date').val(data.IM_end_date);
                    $('#IM_status').val(data.IM_status);
                    $('#IM_comment').val(data.IM_comment);
                    $('#MAN_start_date').val(data.MAN_start_date);
                    $('#MAN_end_date').val(data.MAN_end_date);
                    $('#MAN_status').val(data.MAN_status);
                    $('#MAN_comment').val(data.MAN_comment);

                    $('#process_development_modal').modal('show');
                } else {
                    alert(response.msg);
                }
            },
            error: function() {
                alert('Error al obtener los datos.');
            }
        });
    });

    $('#process_development_form').submit(function(e) {
        e.preventDefault();

        let data = {
            AN_start_date: $(this).find('.fecha-inicio').eq(0).val(),
            AN_end_date: $(this).find('.fecha-termino').eq(0).val(),
            AN_status: $(this).find('.form-select').eq(0).val(),
            AN_comment: $(this).find('textarea').eq(0).val(),
            DI_start_date: $(this).find('.fecha-inicio').eq(1).val(),
            DI_end_date: $(this).find('.fecha-termino').eq(1).val(),
            DI_status: $(this).find('.form-select').eq(1).val(),
            DI_comment: $(this).find('textarea').eq(1).val(),
            DE_start_date: $(this).find('.fecha-inicio').eq(2).val(),
            DE_end_date: $(this).find('.fecha-termino').eq(2).val(),
            DE_status: $(this).find('.form-select').eq(2).val(),
            DE_comment: $(this).find('textarea').eq(2).val(),
            IM_start_date: $(this).find('.fecha-inicio').eq(3).val(),
            IM_end_date: $(this).find('.fecha-termino').eq(3).val(),
            IM_status: $(this).find('.form-select').eq(3).val(),
            IM_comment: $(this).find('textarea').eq(3).val(),
            MAN_start_date: $(this).find('.fecha-inicio-mantenimiento').val(),
            MAN_end_date: $(this).find('.fecha-termino-mantenimiento').val(),
            MAN_status: $(this).find('.estado-mantenimiento').val(),
            MAN_comment: $(this).find('.comentario-mantenimiento').val()
        };

        $.ajax({
            url: BASE_URL + 'task_control/save_development_process',
            type: 'POST',
            data: JSON.stringify(data),
            contentType: 'application/json',
            success: function(response) {
                functions.toast_message(response.type, response.msg, response.status);
                if (response.status === 'OK') {
                    $('#process_development_modal').modal('hide');
                    $('#process_development_form')[0].reset();
                }
            },
            error: function() {
                alert('Error al guardar los datos.');
            }
        });
    });
});
//https://gemini.google.com/app/1b0f1f196ba9f2e7?hl=es







get_nombres_apellidos();
load_datatable();

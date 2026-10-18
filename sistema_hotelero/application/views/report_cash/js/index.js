$(document).ready(function() {
    console.log('Inicializando reporte de caja...');
    get_user();
    listar();
});

function get_user() {
    $.ajax({
        url: BASE_URL + 'Report_Cash/get_user',
        type: 'GET',  
        dataType: 'json',
        success: function (data) {
            console.log('Respuesta get_user:', data);
            if (data.status === "OK") {
                var html = '<option value="">Todos los usuarios</option>';
                data.data.forEach((element) => {
                    html += '<option value="' + element.id + '">' + element.full_name + '</option>';
                });
                $("#idusuario").html(html);
                
                // Verificar si selectpicker está disponible
                if (typeof $.fn.selectpicker !== 'undefined') {
                    $('#idusuario').selectpicker('refresh');
                }
            } else {
                console.error('Error al cargar usuarios:', data);
                functions.toast_message('warning', 'No se encontraron usuarios.', 'Error');
            }
        },
        error: function (xhr, status, error) {
            console.error("Error en get_user:", xhr.responseText);
            functions.toast_message('error', 'Error al cargar los usuarios.', 'Error');
        }
    });
}

function listar() {
    var fecha_inicio = $("#fecha_inicio").val();
    var fecha_fin = $("#fecha_fin").val();
    var id_user = $("#idusuario").val();

    console.log('Filtros:', {
        fecha_inicio: fecha_inicio,
        fecha_fin: fecha_fin,
        id_user: id_user
    });

    destroy_datatable();

    $("#tbllistado").DataTable({
      dom: 'Bfrtip', 
          buttons: [
              {
                  extend: 'copy',
                  text: '<i class="fas fa-copy"></i>',
                  className: 'btn btn-primary'
              },
              {
                  extend: 'excel',
                  text: '<i class="fas fa-file-excel"></i>',
                  className: 'btn btn-success'
              },
              {
                  extend: 'pdf',
                  text: '<i class="fas fa-file-pdf"></i>',
                  className: 'btn btn-danger'
              }
          ],
        "ajax": {
            "url": BASE_URL + "Report_Cash/get_report_data",
            "type": "GET",
            "data": {
                fecha_inicio: fecha_inicio,
                fecha_fin: fecha_fin,
                id_user: id_user
            },
            "dataSrc": function(json) {
                console.log('Respuesta del servidor:', json);
                if (json.status === "OK") {
                    calcular_totales(json.data);
                    return json.data;
                } else {
                    console.error('Error en respuesta:', json);
                    functions.toast_message('warning', json.msg || 'No se encontraron datos', 'Advertencia');
                    return [];
                }
            },
            "error": function(xhr, error, code) {
                console.error('Error AJAX:', xhr.responseText);
                functions.toast_message('error', 'Error al cargar los datos: ' + xhr.responseText, 'Error');
            }
        },
        "columns": [
            { "data": "id" },
            { 
                "data": "fecha_hora_apertura",
                "render": function(data) {
                    if (!data) return '';
                    // Formatear fecha
                    var date = new Date(data);
                    return date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
                }
            },
            { "data": "usuario" },
            { 
                "data": "monto_inicial",
                "render": function(data) {
                    return 'S/ ' + parseFloat(data || 0).toFixed(2);
                }
            },
            { 
                "data": "ingresos",
                "render": function(data) {
                    return 'S/ ' + parseFloat(data || 0).toFixed(2);
                }
            },
            { 
                "data": "egresos",
                "render": function(data) {
                    return 'S/ ' + parseFloat(data || 0).toFixed(2);
                }
            },
            { 
                "data": "monto_final",
                "render": function(data) {
                    return 'S/ ' + parseFloat(data || 0).toFixed(2);
                }
            },
            { 
                "data": "estado",
                "render": function(data) {
                    if (data === 'Abierta') {
                        return '<span class="badge bg-success">Abierta</span>';
                    } else if (data === 'Cerrada') {
                        return '<span class="badge bg-danger">Cerrada</span>';
                    } else {
                        return '<span class="badge bg-secondary">' + data + '</span>';
                    }
                }
            },
            { 
                "data": "fecha_hora_cierre",
                "render": function(data) {
                    if (!data) return '<span class="text-muted">No cerrada</span>';
                    var date = new Date(data);
                    return date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
                }
            },
            { 
                "data": "observaciones",
                "render": function(data) {
                    return data || '<span class="text-muted">Sin observaciones</span>';
                }
            }
        ],
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
        },
        "responsive": true,
        "pageLength": 25,
        "order": [[1, "desc"]],
        "processing": true,
        "serverSide": false
    });
}

function calcular_totales(data) {
    var total_ingresos = 0;
    var total_egresos = 0;
    var total_final = 0;
    var total_registros = data.length;

    data.forEach(function(item) {
        total_ingresos += parseFloat(item.ingresos || 0);
        total_egresos += parseFloat(item.egresos || 0);
        total_final += parseFloat(item.monto_final || 0);
    });

    $("#total_ingresos").text(total_ingresos.toFixed(2));
    $("#total_egresos").text(total_egresos.toFixed(2));
    $("#total_final").text(total_final.toFixed(2));
    $("#total_registros").text(total_registros);
}

function destroy_datatable() {
    if ($.fn.DataTable.isDataTable("#tbllistado")) {
        $("#tbllistado").DataTable().destroy();
    }
}

function resetear() {
    var today = new Date().toISOString().split('T')[0];
    $("#fecha_inicio").val(today);
    $("#fecha_fin").val(today);
    $("#idusuario").val("");
    
    if (typeof $.fn.selectpicker !== 'undefined') {
        $("#idusuario").selectpicker('refresh');
    }
    
    listar();
}
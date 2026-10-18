$(document).ready(function() {
    console.log('Inicializando reporte de clientes...');
    listar();
});

function listar() {
    console.log('Iniciando listado...');
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
            "url": BASE_URL + "Customer_Report/get_report_data",
            "type": "GET",
            "data": function(d) {
                const filters = {
                    document_number: $("#document_number").val(),
                    name: $("#name").val(),
                    status: $("#status").val()
                };
                console.log('Enviando filtros:', filters);
                return filters;
            },
            "dataSrc": function(json) {
                console.log('Respuesta del servidor:', json);
                if (json.status === "OK") {
                    if (json.data && json.data.length > 0) {
                        return json.data;
                    } else {
                        functions.toast_message('warning', 'No se encontraron registros', 'Información');
                        return [];
                    }
                } else {
                    functions.toast_message(json.type, json.msg, 'Error');
                    return [];
                }
            }
        },
        "columns": [
            { "data": "name" },
            { "data": "document_type" },
            { "data": "document_number" },
            { "data": "phone" },
            { "data": "email" },
            { "data": "address" },
            { 
                "data": "status",
                "render": function(data) {
                    if (data == 1) {
                        return '<span class="badge bg-success">Activo</span>';
                    } else {
                        return '<span class="badge bg-danger">Inactivo</span>';
                    }
                }
            }
        ],
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
        },
        "responsive": true,
        "pageLength": 25,
        "order": [[1, "asc"]],
        "processing": true,
        "serverSide": false
    });
}

function destroy_datatable() {
    if ($.fn.DataTable.isDataTable("#tbllistado")) {
        $("#tbllistado").DataTable().destroy();
    }
}

function resetear() {
    $("#document_number").val("");
    $("#name").val("");
    $("#status").val("");
    listar();
}
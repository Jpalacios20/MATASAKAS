$(document).ready(function() {
  get_name();
  
  load_datatable();
});

function get_name() {
  $.ajax({
    url: BASE_URL + "Clients/get_clients",
    type: "GET",
    dataType: "json",
    contentType: false,
    processData: false,
    cache: false,
    beforeSend: function () {
      
    },
    success: function (data) {
      if (data.status === "OK") {
        var html = '<option value="">Seleccionar cliente</option>';
        data.data.forEach((element) => {
          html +=
            '<option value="' +
            element.id_clients +
            '">' +
            element.name +
            "</option>";
        });
        $("#idusuario").html(html);
        
      }
    },
  });
}

function destroy_datatable() {
    $("#tbllistado").dataTable().fnDestroy();
}

function refresh_datatable() {
    $("#tbllistado").DataTable().ajax.reload();
}

function load_datatable() {
    destroy_datatable();
    let dataTable = $("#tbllistado").DataTable({
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
        ajax: {
            url: BASE_URL + "Report_Monthly/get_report_data",
            cache: false,
            dataSrc: function (json) {
                console.log("Datos recibidos desde el backend:", json); 
                if (json.status === "ERROR") {
                    console.error("Error al cargar datos: ", json.msg);
                    return [];
                }   
                return json.data;
                
            },
            
        },
        columns: [
            { data: "name", title: "Cliente" },
            { data: "document_number", title: "N° Documento" },
            { data: "date", title: "Fecha" },
            { data: "voucher_type_description", title: "Comprobante" },
            { data: "number_serial", title: "N° Comprobante" },
            { data: "total_purchase", title: "Total Venta" },
            { data: "origin", title: "Categoria" }, 
            { data: "status", title: "Estado", render: function (data, type, row) {
                if (row.status == "1") {
                    return '<span class="badge rounded-pill badge-light-warning">Pendiente</span>';
                } else if (row.status == "2") {
                    return '<span class="badge rounded-pill badge-light-success">Aceptado</span>';
                } else if (row.status == "3") {
                    return '<span class="badge rounded-pill badge-light-danger">Rechazado</span>';
                } else if (row.status == "Reservado") {
                    return '<span class="badge rounded-pill badge-light-info">Reservado</span>';
                }
            }},
        ],
        language: {
            url: BASE_URL + "public/assets/json/languaje-es.json",
        },
    });

    dataTable.on("xhr", function () {
        let data = dataTable.ajax.json();
        console.log("Datos procesados por DataTables:", data); 
    });
}

let originalData = null;

function listar() {
    const fechaInicio = $("#fecha_inicio").val();
    const fechaFin = $("#fecha_fin").val();
    const idUsuario = $("#idusuario option:selected").text().trim();
    const modulo = $("#modulo").val();

    const dataTable = $("#tbllistado").DataTable();

    if (!originalData) {
        originalData = dataTable.rows().data().toArray();
    }
    
    const filteredData = originalData.filter(row => {
        const fechaRow = row.date ? row.date.split(' ')[0] : '';

        const fechaValida = (!fechaInicio || fechaRow >= fechaInicio) && 
                           (!fechaFin || fechaRow <= fechaFin);
        
        const usuarioValido = !idUsuario || idUsuario === "Seleccionar cliente" || 
                             row.name.trim() === idUsuario;
        
        let categoriaValida = true;
        if (modulo !== "0") {
            if (modulo === "1") {
                categoriaValida = row.origin === "Reserva";
            } else if (modulo === "2") {
                categoriaValida = row.origin === "Venta";
            }
        }

        return fechaValida && usuarioValido && categoriaValida;
    });
    const totalVentas = filteredData.reduce((total, row) => {
        const monto = parseFloat(row.total_purchase) || 0;
        return total + monto;
    }, 0);

    $("#usuari").text(idUsuario === "Seleccionar cliente" ? "Todos" : idUsuario);
    $("#sumventa").text(totalVentas.toFixed(2));

    dataTable.clear();
    dataTable.rows.add(filteredData);
    dataTable.draw();

    if (filteredData.length === 0) {
        console.warn("No se encontraron datos con los filtros aplicados.");
        $("#sumventa").text("0.00");
    }
}

function resetear() {
    const dataTable = $("#tbllistado").DataTable();

    if (originalData) {
        dataTable.clear();
        dataTable.rows.add(originalData);
        dataTable.draw();
        console.log("Tabla reseteada a los datos originales.");
    }
    $("#fecha_inicio").val('');
    $("#fecha_fin").val('');
    $("#idusuario").prop("selectedIndex", 0);
    $("#modulo").val("0"); 
    $("#usuari").text("usuario");
    $("#sumventa").text("0.00");
}


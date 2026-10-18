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
              url: BASE_URL + "Report_Billing/get_report_data",
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
              { data: "issue_date", title: "Fecha" },
              { data: "clients", title: "Cliente" },
              { data: "voucher_type", title: "Comprobante" },
              { 
                  data: null, 
                  title: "Serie-Correlativo",
                  render: function(data, type, row) {
                      return row.series + '-' + row.correlative;
                  }
              },
              { data: "total_amount", title: "Total Venta" },
              { data: "status", title: "Estado", render: function (data, type, row) {
                  if (row.status == "1") {
                      return '<span class="badge rounded-pill badge-light-warning">Pendiente</span>';
                  } else if (row.status == "2") {
                      return '<span class="badge rounded-pill badge-light-success">Aceptado</span>';
                  } else if (row.status == "3") {
                      return '<span class="badge rounded-pill badge-light-danger">Rechazado</span>';
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
      const idUsuario = $("#idusuario").val();
      const modulo = $("#modulo").val();
  
      const dataTable = $("#tbllistado").DataTable();
  
      if (!originalData) {
          originalData = dataTable.rows().data().toArray();
      }
      
      const filteredData = originalData.filter(row => {
          // Convertir fecha de la fila al formato yyyy-mm-dd
          const fechaRow = row.issue_date.split(' ')[0];
  
          const fechaValida = (!fechaInicio || fechaRow >= fechaInicio) && 
                             (!fechaFin || fechaRow <= fechaFin);
          
          const usuarioValido = !idUsuario || 
                               idUsuario === "" || 
                               row.clients_id === idUsuario;
          
  
          return fechaValida && usuarioValido;
      });
  
      // Calcular el total de ventas
      const totalVentas = filteredData.reduce((total, row) => {
          const monto = parseFloat(row.total_amount) || 0;
          return total + monto;
      }, 0);
  
      // Actualizar el nombre del usuario y el total
      $("#usuari").text($("#idusuario option:selected").text() || "Todos");
      $("#sumventa").text(totalVentas.toFixed(2));
  
      // Actualizar la tabla
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


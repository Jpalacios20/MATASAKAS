function get_daily_income() {
    $.ajax({
        url: BASE_URL + "Closingcash/get_daily_income",
        type: "GET",
        dataType: "json",
        cache: false,
        success: function(data) {
            console.log('Respuesta:', data); 
            
            if (data.status === "OK" && data.data) {
                $("#closing_cash_data :input[name=income]").val(
                    parseFloat(data.data.total_income).toFixed(2)
                );
            } else {
                $("#closing_cash_data :input[name=income]").val("0.00");
                functions.toast_message(
                    "warning",
                    data.msg || "No hay ingresos registrados para hoy",
                    "Advertencia"
                );
            }
        },
        error: function(xhr, status, error) {
            console.log('Error completo:', xhr.responseText);
            $("#closing_cash_data :input[name=income]").val("0.00");
            functions.toast_message(
                "error",
                "Error al calcular los ingresos: " + xhr.responseText,
                "Error"
            );
        }
    });
}

function get_cash_closing_from_today() {
  $.ajax({
    url: BASE_URL + "Closingcash/get_cash_closing_form_today",
    type: "GET",
    dataType: "json",
    cache: false,
    success: function (data) {
      if (data.status === "OK") {
        const caja = data.data[0];

        $("#closing_cash_data :input[name=employee]").val(caja.first_name + ' ' + caja.last_name);
        $("#closing_cash_data :input[name=monto_inicial]").val(caja.monto_inicial);
        $("#fecha_hora_apertura").val(caja.fecha_hora_apertura);
        
        get_daily_income();
        
      } else {
        functions.toast_message("warning", "No hay una caja abierta", "Advertencia");
        $("#btn_closing_cash").prop("disabled", true);
      }
    },
    error: function (xhr, status, error) {
      functions.toast_message("error", "Error al obtener los datos", "Error");
    }
  });
}

function post_cash_closing(form){
  $("#btn_closing_cash").prop("disabled", true);
  
  // Obtener los datos del formulario
  let formData = new FormData(form);
  
  // Verificar que los campos obligatorios estén presentes
  const income = formData.get('income');
  const expenses = formData.get('expenses');
  const notes = formData.get('notes');
  
  if (!income || !expenses || notes === null || notes.trim() === '') {
    functions.toast_message("warning", "Por favor complete todos los campos obligatorios", "Advertencia");
    $("#btn_closing_cash").prop("disabled", false);
    return;
  }

  // Validar que los valores sean números válidos
  if (isNaN(parseFloat(income)) || isNaN(parseFloat(expenses))) {
    functions.toast_message("warning", "Los ingresos y egresos deben ser números válidos", "Advertencia");
    $("#btn_closing_cash").prop("disabled", false);
    return;
  }

  $.ajax({
    url: BASE_URL + "Closingcash/post_cash_closing",
    type: "POST",
    data: formData,
    dataType: "json",
    contentType: false,
    processData: false,
    cache: false,
    success: function (data) {
      $("#btn_closing_cash").prop("disabled", false);

      if (data.status === "OK") {
        functions.toast_message("success", data.msg || "Cierre de caja realizado correctamente", "Éxito");
        
        // Mostrar información del cierre si está disponible
        if (data.data && data.data.monto_final !== undefined) {
          console.log("Monto final de caja:", data.data.monto_final);
        }
        
        // Limpiar formulario y recargar
        form.reset();
        $("#btn_closing_cash").prop("disabled", true);
        
        // Actualizar historial
        setTimeout(function() {
          closing_cash_history();
        }, 1000);
        
      } else {
        functions.toast_message("error", data.msg || "Error al realizar el cierre de caja", "Error");
      }
    },
    error: function (xhr, status, error) {
      $("#btn_closing_cash").prop("disabled", false);
      console.log("Error completo:", xhr.responseText);
      
      let errorMsg = "Error al procesar la solicitud";
      try {
        const response = JSON.parse(xhr.responseText);
        errorMsg = response.msg || errorMsg;
      } catch(e) {
        errorMsg = xhr.responseText || errorMsg;
      }
      
      functions.toast_message("error", errorMsg, "Error");
    },
  });
}


$("#closing_cash_data").validate({
  // --

  submitHandler: function (form) {
      post_cash_closing(form);
  },
});


function closing_cash_history(){
  $.ajax({
    url: BASE_URL + "Closingcash/closing_cash_history",
    type: "GET",
    dataType: "json",
    cache: false,
    success: function (data) {
      if (data.status === "OK") {
        $("#datatable-closing-cash tbody").empty();

        $.each(data.data, function (index, row) {
          const balance = (parseFloat(row.monto_final) || 0).toFixed(2);

          $("#datatable-closing-cash tbody").prepend(`
            <tr>
              <td>${index + 1}</td>
              <td>${row.fecha_hora_cierre}</td>
              <td>${row.first_name} ${row.last_name}</td>
              <td>${row.ingresos || '0.00'}</td>
              <td>${row.egresos || '0.00'}</td>
              <td>${balance}</td>
              <td>${row.observaciones || ''}</td>
            </tr>
          `);
        });
      } 
    },
    error: function () {
      functions.toast_message("error", "Error al obtener el historial", "Error");
    }
  });
}


// function check_closing_cash_today() {
//   $.ajax({
//     url: BASE_URL + "Closingcash/check_closing_cash_today",
//     type: "GET",
//     dataType: "json",
//     success: function (data) {
//       if (data.status === "OK") {
//         if (data === 0) {
//           $("#btn_closing_cash").prop("disabled", false);
//         } else {
//           $("#btn_closing_cash").prop("disabled", true);
//           functions.toast_message("info", "Ya se realizó el cierre de caja para hoy.", "Cierre existente");
//         }
//       }
//     },
//     error: function () {
//       functions.toast_message("error", "Error al verificar cierre de caja", "Error");
//     }
//   });
// }





get_cash_closing_from_today();
closing_cash_history();
// check_closing_cash_today();

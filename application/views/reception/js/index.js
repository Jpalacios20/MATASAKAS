// WARNING: the updating of the rooms has an error in case the reservation has an error, the user is obliged to complete the missing fields

const colorsRooms = {

  Disponible: "#28C66F",
  Ocupado: "#EA5455",
  Limpieza: "#00C1FF",
  Reservado: "#FC912A",
  Libre: "#517FFF",

};

function generateRoomCardHTML(row) {

  const { room_status, id_room, type_name, person_limit, room_number } = row;

  const bgClass = colorsRooms[room_status] || "success";
  const isOccupied = room_status === "Ocupado";
  const isCleaning = room_status === "Limpieza";
  const isReservedOrOccupied = room_status === "Reservado" || isOccupied;
  const btnDisabled = room_status === "Libre" && "hidden";
  const btnDisabled2 = (room_status === "Ocupado" || room_status === "Reservado") ? "hidden" : "";   //variable nueva para ocultar la llave
  const btnDisabled3 = room_status === "Limpieza" ? "hidden" : ";"


  const btnTimerHTML = `<button class="btn bg-light text-dark btn-icon btn-md btn_timer" data-process-key="${id_room}">

                          <i class="fa-solid fa-clock"></i>

                        </button>`;

  const buttonCleanHTML = `<button class="btn bg-light text-dark btn-icon btn-md btn_clean ${isOccupied ? "hidden" : ""

    }" data-process-key="${id_room}" type="submit">

                              <i class="fa-solid fa-circle-check"></i>

                            </button>`;

  const stateCleaningHTML = isCleaning ? buttonCleanHTML : "";
  const statusButtonHTML = isReservedOrOccupied ? btnTimerHTML : "";

//Se agrego la variable btnDisabled2 en la linea 49

  return `<div class="card text-light" style="width: 18rem; background:${bgClass}; border-radius: 10px;">

              <div class="card-body">

                  <h5 class="card-title text-light" style="font-size: 1.5rem">${type_name}</h5>

                  <div class="d-flex align-items-center gap-1" style="font-size: 1.5rem">

                      <i class="fa-solid fa-people-group"></i>

                      <p class="card-text ml-2">${person_limit}</p>

                  </div>

                  <div class="text-center mt-2" style="font-size: 1.5rem">

                      ${room_status}

                  </div>
              </div>

              <div class="card-footer d-flex justify-content-between align-items-center" style="border-top: 1px solid rgba(255, 255, 255, 0.2); letter-spacing: 1px;">

                  <div class="d-flex align-items-center">

                      <i class="fa-solid fa-bed text-light" style="font-size: 2.3rem;"></i>

                      <span style="font-size: 1.5rem; margin-left: 5px;">${room_number}</span>

                  </div>

                  <div class="d-flex justify-content-center align-items-center gap-1">

                    <button class="btn bg-light text-dark btn-icon btn-md btn_update ${btnDisabled} ${btnDisabled2} ${btnDisabled3}" data-process-key="${id_room}">

                      <i class="fa-solid fa-key"></i>

                    </button>

                    ${statusButtonHTML}

                    ${stateCleaningHTML}

                  </div>

              </div>

          </div>`;

}

function displayRooms(data) {

  if (data.status === "OK") {

    $("#data-container").empty();
    
    const selectedFlat = $("#btn_room_flat").val();

    const rooms = selectedFlat == "0"
      ? data.data
      : data.data.filter((row) => String(row.id_flat) === String(selectedFlat));


    rooms.forEach((row) => {

      const roomCardHTML = generateRoomCardHTML(row);

      $("#data-container").append(roomCardHTML);

    });

  }

}

//Permite que el nuevo filtro trabaje junto al de estado
    function refreshRoomsWithFilters() {
      const status = $("#btn_room_status").val();
    
      if (status == "0") {
        fetchRooms();
      } else {
        fetchRoomsStatus(status);
      }
    }
//---------------------------------

//---------------------------------------------------------------------------

//(recupera el id de la habitacion correcta )



$(document).on("click", ".btn_timer", function () {

  let roomId = $(this).attr("data-process-key");

  if (roomId) {

    $("#create_timer_form :input[name=id_room]").val(roomId);

    $("#timer_modal").modal("show");

  } else {

    console.error("No se capturó el ID de la habitación.");

  }

});

//---------------------------------

//-----------------------------------------------------------------------------------

function fetchRooms() {

  $.ajax({

    url: BASE_URL + "Reception/get_rooms",

    type: "GET",

    dataType: "json",

    cache: false,

    success: function (data) {

      if (data.status === "OK") {

        functions.toast_message("success", "Habitaciones listadas correctamente", "Exito");

        displayRooms(data);

      }

    },

    error: function (xhr, status, error) {

      console.error("Fallo al obtener los datos.");

      functions.toast_message("error", "Error al obtener los datos", "Error");

    },

  });

}
//--------------------------------------
    function loadFlatFilterOptions() {
      $.ajax({
        url: BASE_URL + "Flats/get_flats",
        type: "GET",
        dataType: "json",
        cache: false,
        success: function (data) {
          if (data.status === "OK") {
            $("#btn_room_flat").empty();
            $("#btn_room_flat").append('<option value="0">Todos los pisos</option>');
    
            data.data.forEach((row) => {
              $("#btn_room_flat").append(`
                <option value="${row.id}">${row.description}</option>
              `);
            });
          }
        },
        error: function () {
          console.error("Fallo al obtener los pisos.");
        },
      });
    }
//--------------------------------------
function fetchRoomsStatus(status_room) {

  $.ajax({

    url: BASE_URL + "Reception/get_room_by_status",
    type: "GET",
    data: {
      room_status: status_room,

    },

    dataType: "json",
    cache: false,

    success: function (data) {

      if (data.status === "OK") {

        displayRooms(data);

      } else {

        functions.toast_message("warning", `No hay habitaciones en estado de ${status_room}`, "Advertencia");

        fetchRooms();

      }
    },

    error: function (xhr, status, error) {

      console.error("Fallo al obtener los datos.");

      functions.toast_message("error", "Error al obtener los datos", "Error");

    },
  });
}
    $("#btn_room_status").on("change", function () {
      refreshRoomsWithFilters();
    });
    
    $("#btn_room_flat").on("change", function () {
      refreshRoomsWithFilters();
    });
/*

function update_state_timer(form) {

  // --

  $("#btn_remove_reservation").prop("disabled", true);

  // --

  let params = new FormData(form);

  // --

  $.ajax({

    url: BASE_URL + "Reception/update_state_timer",
    type: "POST",
    data: params,
    dataType: "json",
    contentType: false,
    processData: false,
    cache: false,
    beforeSend: function () {
      console.log("Cargando...");

    },

    success: function (data) {

      // --

      if (data.status === "OK") {

        // --

        $("#timer_modal").modal("hide");

        form.reset();

        fetchRooms();

      } else {

        // --

        $("#btn_remove_reservation").prop("disabled", false);
      }
    },

    error: function (xhr, status, error) {

      functions.toast_message("error", "Error al obtener los datos", "Error");
    },
  });
}

*/

function create_reservation(form) {

  // --

  $("#btn_create_reservation").prop("disabled", true);

  // --

  let params = new FormData(form);

  // --

  $.ajax({

    url: BASE_URL + "Reception/create_reservation",
    type: "POST",
    data: params,
    dataType: "json",
    contentType: false,
    processData: false,
    cache: false,

    success: function (data) {

      // --

      if (data.status === "OK") {

        form.reset();

        // --

        $("#update_habitacion_modal").modal("hide");

        $("#btn_create_reservation").prop("disabled", false);

      } else {

        $("#btn_create_reservation").prop("disabled", false);

        functions.toast_message("error", "Error al obtener los datos", "Error");

      }
    },

    error: function (xhr, status, error) {

      console.log(error);

      functions.toast_message("error", "Error al obtener los datos", "Error");

    },
  });
}

function create_reservation_free(form) {

  // --

  $("#btn_create_reservation").prop("disabled", true);

  // --

  let params = new FormData(form);

  // --

  $.ajax({

    url: BASE_URL + "Reception/create_reservation_free",
    type: "POST",
    data: params,
    dataType: "json",
    contentType: false,
    processData: false,
    cache: false,

    success: function (data) {

      // --

      if (data.status === "OK") {

        form.reset();

        // --

        $("#update_habitacion_modal").modal("hide");

        $("#btn_create_reservation").prop("disabled", false);

      } else {

        $("#btn_create_reservation").prop("disabled", false);

        functions.toast_message("error", "Error al obtener los datos", "Error");

      }
    },

    error: function (xhr, status, error) {

      functions.toast_message("error", "Error al obtener los datos", "Error", error);

    },
  });
}

//----------------------------------------------------------------------

//nueva funcion creada

function update_state(form) {

  // --

  $("#btn_remove_reservation").prop("disabled", true);

  // --

  let params = new FormData(form);

  // --

  $.ajax({

    url: BASE_URL + "Reception/update_state",
    type: "POST",
    data: params,
    dataType: "json",
    contentType: false,
    processData: false,
    cache: false,

    success: function (data) {

      console.log(data);

      // --

      if (data.status === "ok") {

        // --

        $("#timer_modal").modal("hide");

        form.reset();
        fetchRooms();

        $("#btn_remove_reservation").prop("disabled", false);

      } else if(data.status === "ERROR"){

        Function.toast_message("error", data.msg || "error al actualizar la reserva", "error");

        $("#btn_remove_reservation").prop("disabled", false);

      } else {

        functions.toast_message("error", "Error al actualizar", "Error");

        $("#btn_remove_reservation").prop("disabled", false);
      }
    },

    error: function (xhr, status, error) {

      console.log(error);
      functions.toast_message("error", "Error al actualizar los datos", "Error");

    },

  });

}

//-------------------

//-----------------------------------------------------------------------

function update_room(form) {

  // --

  $("#btn_create_reservation").prop("disabled", true);

  // --

  let params = new FormData(form);

  // --

  $.ajax({

    url: BASE_URL + "Reception/update_state_reservation",
    type: "POST",
    data: params,
    dataType: "json",
    contentType: false,
    processData: false,
    cache: false,

    beforeSend: function () {

      console.log("Cargando...");

    },

    success: function (data) {

      // --

      if (data.status === "OK") {

        // --

        $("#update_habitacion_modal").modal("hide");

        $("#btn_create_reservation").prop("disabled", false);

        form.reset();

      } else {

        // --

        $("#btn_create_reservation").prop("disabled", false);

      }

    },

    error: function (xhr, status, error) {

      console.log(error);
      functions.toast_message("error", "Error al obtener los datos", "Error");
    }

  });

}

$("#create_reservation_form").validate({

  // --

  submitHandler: function (form) {
        const room_status = $("#room_status_reservation").val();

        if (room_status === "Ocupado" || room_status === "Reservado") {
            create_reservation(form);
            registrarVentaDesdeRecepcion(form); // La dejamos comentada para que no te salga el cartel de "Error"
        } else {
            create_reservation_free(form);
        }
        
        update_room(form);

        // Esto refresca los colores de los cuadritos después de guardar
        setTimeout(function() {
            if (typeof fetchRooms === "function") {
                fetchRooms(); 
            }
        }, 200); 
    } // Aquí se cierra el submitHandler
});

$('#room_status_reservation').change(function () {

  const checkout_date = document.querySelector('.checkout_date');
  const checkout_time = document.querySelector('.checkout_time');
  const pre_payment = document.querySelector('.pre_payment')
  const all_payment = document.querySelector('.all_payment');
  const status = document.getElementById("status");
  status.value = this.value;

  if (this.value === "Reservado" || this.value === "Ocupado") {

    checkout_date.classList.remove('hidden')
    checkout_time.classList.remove('hidden')
    pre_payment.classList.remove('hidden')
    all_payment.classList.remove('hidden')

    $('input[name="checkin_date"]').on("change", () => {
      valDate("checkin_date", "checkin_time");

    });

    $('input[name="checkin_time"]').on("change", () => {
      valDate("checkin_time");

    });

    $('input[name="checkout_date"]').on("change", () => {
      valDate("checkout_date", "checkout_time");

    });

    $('input[name="checkout_time"]').on("change", () => {
      valDate("checkout_time");

    });

  } else {
    checkout_date.classList.add('hidden')
    checkout_time.classList.add('hidden')
    pre_payment.classList.add('hidden')
    all_payment.classList.add('hidden')
  }

});

var allPricesRoom = [];

function valDate(input_name, input_nameAux) {

    const valCheckin = $("input[name=checkin_date]").val() + " " + $("input[name=checkin_time]").val();
    const valCheckout = $("input[name=checkout_date]").val() + " " + $("input[name=checkout_time]").val();
    const valRoom = $("input[name=id_room]").val();

    $.ajax({
        url: BASE_URL + "Reception/date_reservation",
        cache: false,
        data: {
            id_room: valRoom,
            checkin_date: valCheckin,
            checkout_date: valCheckout,
        },

        success: function (data) {
    // 1. Limpiamos lo rojo siempre, aunque el servidor chille
    $("input[name=" + input_name + "]").removeClass("is-invalid").removeAttr("data-error");
    $("input[name=" + input_nameAux + "]").removeClass("is-invalid").removeAttr("data-error");
    $("#fechaFin, #hora_termino").removeClass("is-invalid error").css("border-color", "#ced4da");
    $(".error").remove();

    // 2. EJECUTAR EL PAGO SIEMPRE (Esto actualizará el Monto Total)
    // Sacamos la función fuera del IF para que no dependa del status "OK"
    paymentReservation(valCheckin, valCheckout, allPricesRoom, 1);
    
    if (data.status == "OK") {
        console.log("Servidor conforme con la fecha");
    }
},
    });
}

function paymentReservation(dateIn, dateOut, prices) {
  let fechaInicio = new Date(dateIn);
  let fechaFin = new Date(dateOut);
  
  // Calculamos la diferencia total en minutos
  let minutes = (fechaFin.getTime() - fechaInicio.getTime()) / (1000 * 60);

  // Si la fecha es inválida o al revés, ponemos 0
  if (minutes < 0) {
      $("input[name=payment_room]").val("0.00");
      return;
  }

  let finalPayment = 0;

  // CASO A: Menos de 4 horas (Uso corto)
  if (minutes <= 240) {
    finalPayment = parseFloat(prices[0]);

  // CASO B: Entre 4 y 12 horas (Medio día)
  } else if (minutes > 240 && minutes <= 720) {
    finalPayment = parseFloat(prices[1]);

  // CASO C: Más de 12 horas (Por días)
  } else {
    let dias = Math.floor(minutes / 1440);
    let minutesRes = minutes % 1440;
    
    // Multiplicamos precio de día por cantidad de días
    finalPayment = parseFloat(prices[2]) * dias;

    // Sumamos el excedente de tiempo si existe
    if (minutesRes > 0 && minutesRes <= 240) {
      finalPayment += parseFloat(prices[0]);
    } else if (minutesRes > 240 && minutesRes <= 720) {
      finalPayment += parseFloat(prices[1]);
    } else if (minutesRes > 720) {
      finalPayment += parseFloat(prices[2]);
    }
  }

  // IMPORTANTE: .toFixed(2) evita que salga null o números raros
  return $("input[name=payment_room]").val(finalPayment.toFixed(2));
}

function create_guest(form) {

  $("#btn_create_guest_reservation").prop("disabled", true);

  // --
  let params = new FormData(form);

  // --

  $.ajax({

    url: BASE_URL + "Reception/create_guest_reservation",
    type: "POST",
    data: params,
    dataType: "json",
    contentType: false,
    processData: false,
    cache: false,

    beforeSend: function () {

      console.log("Cargando...");

    },

    success: function (data) {

      // --

      if (data.status === "OK") {

        // --

        $("#create_guest_reservation_modal").modal("hide");

        form.reset();

        $("#btn_create_guest_reservation").prop("disabled", false);
        filterOptions();
        fetchRooms();

      } else {

        // --

        $("#btn_create_guest_reservation").prop("disabled", false);

      }
    },
  });
}

$(document).on("click", ".btn_timer", function () {

  // --
  let value = $(this).attr("data-process-key");

  // --
  let params = { id_room: value };

  // --

  $.ajax({

    url: BASE_URL + "Reception/get_room_by_id",
    type: "GET",
    data: params,
    dataType: "json",
    contentType: false,
    processData: true,
    cache: false,

    success: function (data) {

      // --
      // console.log(data);

      if (data.status === "OK") {

        // --
        let item = data.data;

        // --
        let id_timer_room = item.id_room;

        listReservation(id_timer_room);

        $("#create_timer_form :input[name=id_room]").val(item.id_room);
        $("#create_timer_form :input[name=room_number]").val(item.room_number);
        $("#create_timer_form :input[name=room_status]").val(item.room_status);
        $("#create_timer_form :input[name=type_name]").val(item.type_name);
      }
    },

    error: function () {

      functions.toast_message("error", "Error al obtener los datos", "Error");
    },
  });
  // --

  $("#timer_modal").modal("show");

});

// -- Funciones

function filterOptions() {

  const client_document_type = $("#client_document_type").val();

  $.ajax({

    url: BASE_URL + "Reception/get_guest",
    type: "GET",
    data: { document_type: client_document_type },
    dataType: "json", // Espera una respuesta JSON
    cache: false,
    success: function (data) {

      if (data.status === "OK") {

        $(".opcionesSelect").empty();

        $.each(data.data, function (index, row) {

          const optionText =
            client_document_type === "DNI" && row.document_type === "DNI"
              ? `${row.first_names} ${row.last_names}`
              : row.company_name;

          $(".opcionesSelect").append(`

                  <option value=${row.id_guest}>
                    ${optionText}
                  </option >
        `);
        });

      } else {
      }
    },
  });
}

/* (lista de reserva)

function listReservation(room_id) {

  let id_room = room_id;

  $.ajax({

    url: BASE_URL + "Reception/get_reservation_room",
    type: "GET",
    data: { id_room: id_room },
    dataType: "json",
    cache: false,
    success: function (data) {
      if (data.status === "OK") {
        $.each(data.data, function (index, row) {
          const guestNames = row.first_names + " " + row.last_names;
          const condition = row.company_name ? row.company_name : guestNames;
          $(".optionReservation").append(`

            <option value=${row.id_reservation}>
            ${condition}
            </option>
            `);
        });
      } else {
        $(".optionReservation").empty();

      }
    },
    error: function (xhr, status, error) {
      functions.toast_message("error", "Error al obtener los datos", "Error");
    },

  });

}

*/

function clean_rooms(form) {

  $("#btn_clean_rooms").prop("disabled", true);

  // --

  let params = new FormData(form);

  // --

  $.ajax({

    url: BASE_URL + "Reception/clean_rooms",
    type: "POST",
    data: params,
    dataType: "json",
    contentType: false,
    processData: false,
    cache: false,

    beforeSend: function () {
      console.log("Cargando...");
    },

    success: function (data) {
      $("#clean_modal").modal("hide");
      $("#btn_clean_rooms").prop("disabled", false);
      filterOptions();
      fetchRooms();

    },

    error: function (xhr, status, error) {

      functions.toast_message("error", "Error al obtener los datos", "Error");
    }
  });
}

$(document).on("click", ".btn_clean", function () {

  let value = $(this).attr("data-process-key");
  $("#clean_form :input[name=id_room]").val(value);
  $("#clean_modal").modal("show");

});

$("#clean_form").validate({

  submitHandler: function (form) {
    clean_rooms(form);
  },
});

//------------------------------

$(document).on("click", ".btn_update", function () {

  // --
  let value = $(this).attr("data-process-key");

  // --
  let params = { id_room: value };

  // --

  $.ajax({
    url: BASE_URL + "Reception/get_room_by_id",
    type: "GET",
    data: params,
    dataType: "json",
    contentType: false,
    processData: true,
    cache: false,

    success: function (data) {

      // --

      if (data.status === "OK") {

        // --

        let item = data.data;

        // --

        $("#create_reservation_form :input[name=room_number]").val(

          item.room_number,

        );

        $("#create_reservation_form :input[name=type_name]").val(

          item.type_name,
        );

        $("#create_reservation_form :input[name=person_limit]").val(

          item.person_limit,
        );

        $("#create_reservation_form :input[name=bed_type]").val(item.bed_type);
        $("#create_reservation_form :input[name=price_temporary]").val(

          item.price_temporary,
        );

        $("#create_reservation_form :input[name=price_half]").val(

          item.price_half,

        );
        $("#create_reservation_form :input[name=price_day]").val(
          item.price_day,

        );
        $("#create_reservation_form :input[name=id_room]").val(item.id_room);

        allPricesRoom.push(item.price_temporary);

        allPricesRoom.push(item.price_half);

        allPricesRoom.push(item.price_day);
      }
    },

    error: function () {

      functions.toast_message("error", "Error al obtener los datos", "Error");
    },
  });

  // --

 // LIMPIAR CLIENTE AL ABRIR MODAL

$("#client_document_type").val("0").trigger("change");
$("#document_number_reservation").val("");

// limpiar select de clientes PERO NO BLOQUEARLO

$("#id_guest")

  .empty()
  .append('<option value="">Seleccionar un resultado</option>')
  .prop("disabled", false)   // ✅
  .val("")
  .trigger("change");

  $("#update_habitacion_modal").modal("show");

});

function reservationDocument() {
  const documentType = document.getElementById("client_document_type");
  const document_number = document.getElementById(
    "document_number_reservation",
  );

  documentType.addEventListener("change", function () {

    const documentNumberInput = document.getElementById(

      "document_number_reservation",

    );

    const selectedDocumentType = this.value;

    if (selectedDocumentType === "DNI") {
      documentNumberInput.maxLength = 8;

    } else if (selectedDocumentType === "RUC") {
      documentNumberInput.maxLength = 11;

    } else {

      documentNumberInput.maxLength = 8;
    }
  });

  document_number.addEventListener("input", function () {
    this.value = this.value.replace(/[^0-9]/g, "");

  });

}

// -- Reset forms

$(document).on("click", ".reset", function () {
  $("#create_reservation_form").validate().resetForm();

  // --

  $("#create_guest_reservation_form").validate().resetForm();
  $("#create_timer_form").validate().resetForm();
  $("#clean_form").validate().resetForm();

});

// -- Validate form

$("#create_guest_reservation_form").validate({

  // --

  submitHandler: function (form) {
    create_guest(form);
    fetchRooms();

  },
});

$("#create_timer_form").validate({

  // --

  submitHandler: function (form) {
    update_state_timer(form);
    fetchRooms();
  },
});

var inputStartDate = document.getElementById("fechaInicio");
var inputEndDate = document.getElementById("fechaFin");

// 2. Calculamos la fecha de hoy en horario local (Perú)

var hoy = new Date();
var anio = hoy.getFullYear();
var mes = ("0" + (hoy.getMonth() + 1)).slice(-2);
var dia = ("0" + hoy.getDate()).slice(-2);
var fechaHoy = anio + "-" + mes + "-" + dia;

// 3. Aplicamos la configuración para que el mínimo sea hoy

if (inputStartDate) {
    inputStartDate.setAttribute("min", fechaHoy);
}

// if (inputEndDate) {

//     inputEndDate.setAttribute("min", fechaHoy);
// }

function guestDocument() {

  const documentType = document.getElementById("client_document");
  documentType.addEventListener("change", function () {

    const documentNumberInput = document.getElementById("document_number");
    const selectedDocumentType = this.value;

    if (selectedDocumentType === "DNI") {
      documentNumberInput.maxLength = 8;

    } else {

      documentNumberInput.maxLength = 11;
    }
  });

  document_number.addEventListener("input", function () {

    this.value = this.value.replace(/[^0-9]/g, "");

  });
}

//TODO: MEJORA PENDIENTE

function get_api() {
  const number_document =
    document.getElementById("document_number_reservation")?.value?.trim() ||
    document.getElementById("document_number")?.value?.trim();

  const guestDocumentType =
    document.getElementById("client_document_type")?.value ||
    document.getElementById("client_document")?.value;

  // Validaciones
  if (!guestDocumentType || guestDocumentType === "0") {
    toastr.warning("Seleccione el tipo de documento");
    return;
  }

  if (!number_document) {
    toastr.warning("Ingrese el número de documento");
    return;
  }

  if (guestDocumentType === "DNI" && number_document.length !== 8) {
    toastr.warning("El DNI debe tener 8 dígitos");
    return;
  }

  if (guestDocumentType === "RUC" && number_document.length !== 11) {
    toastr.warning("El RUC debe tener 11 dígitos");
    return;
  }

  // ===== CONSULTA DNI =====
  if (guestDocumentType === "DNI") {
    fetch("https://consultardoc.ceatec.com.pe/dni/" + number_document)
      .then((response) => {
        if (!response.ok) {
          throw new Error("Error en la consulta DNI");
        }
        return response.json();
      })
      .then(function (data) {
        console.log("Respuesta API DNI:", data);

        if (data && data.nombres) {
          let first_names = data.nombres || "";
          let last_names = `${data.apellido_paterno || ""} ${data.apellido_materno || ""}`.trim();
          let nombreCompleto = `${first_names} ${last_names}`.trim();

          $("#id_guest").prop("disabled", false);

          let nuevaOpcion = new Option(nombreCompleto, "NUEVO", true, true);
          $("#id_guest").empty().append(nuevaOpcion).trigger("change");

          const firstNameDocument = document.getElementById("nombre");
          const lastNameDocument = document.getElementById("apellido");

          if (firstNameDocument) firstNameDocument.value = first_names;
          if (lastNameDocument) lastNameDocument.value = last_names;

          toastr.success("Encontrado: " + nombreCompleto);
        } else {
          $('#errorModal').modal('show');
        }
      })
      .catch(function (error) {
        console.error("Error DNI:", error);
        $('#errorModal').modal('show');
      });
  }

  // ===== CONSULTA RUC =====
  else if (guestDocumentType === "RUC") {
    fetch("https://dniruc.apisperu.com/api/v1/ruc/" + number_document + "?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJlbWFpbCI6ImRlbm9ibzU2OThAbG9jYXdpbi5jb20ifQ.EyjRFR8bKyCk6kFslAqpFp4Lu4p7VdixEjZy8NEJDRI")
      .then((response) => {
        if (!response.ok) {
          throw new Error("Error en la consulta RUC");
        }
        return response.json();
      })
      .then((data) => {
        console.log("Respuesta API RUC:", data);

        if (data && data.razonSocial) {
          $("#id_guest").prop("disabled", false);

          let nuevaOpcion = new Option(data.razonSocial, "NUEVO", true, true);
          $("#id_guest").empty().append(nuevaOpcion).trigger("change");

          const razon_social = document.getElementById("razon_social");
          if (razon_social) razon_social.value = data.razonSocial;

          toastr.success("Encontrado: " + data.razonSocial);
        } else {
          $('#errorModal').modal('show');
        }
      })
      .catch((error) => {
        console.error("Error RUC:", error);
        $('#errorModal').modal('show');
      });
  }
}

// Evento para redirigir al módulo de clientes

document.getElementById("btn_add_client").addEventListener("click", function () {

  window.location.href = BASE_URL + "Clients/create_clients_form"; // Redirige al formulario para agregar cliente

});

document.getElementById("client_document")

  .addEventListener("change", function () {

    const guestDocumentType = document.getElementById("client_document").value;
    const div_razon_social = document.getElementById("div_razon_social");
    const div_first_names = document.getElementById("div_nombre");
    const div_last_names = document.getElementById("div_apellido");



    if (guestDocumentType === "DNI") {

      div_razon_social.style.display = "none";
      div_last_names.style.display = "block";
      div_first_names.style.display = "block";

    } else if (guestDocumentType === "RUC") {

      div_last_names.style.display = "none";
      div_first_names.style.display = "none";
      div_razon_social.style.display = "block";

    }
  });


$("#buscar_huesped").on("click", function (e) {

  e.preventDefault();

  get_api();

});

// ===============================

// BUSCAR CLIENTE POR DOCUMENTO

// ===============================


function buscarClientePorDocumento() {

    const tipo = $("#client_document_type").val();
    const numero = $("#document_number_reservation").val().trim();
    const valido = (tipo === "DNI" && numero.length === 8) || (tipo === "RUC" && numero.length === 11);

    if (!valido) return;

    $.ajax({

        url: BASE_URL + "Reception/get_guest",
        type: "GET",
        dataType: "json",
        cache: false,
        data: {

            document_type: tipo,

            document_number: numero
        },

        beforeSend: function() {
            $("#id_guest").empty().append('<option value="">Buscando...</option>').prop("disabled", false);
        },

        success: function (data) {
            if (data.status === "OK" && data.data.length > 0) {

                $("#id_guest").empty().prop("disabled", false); // Habilitamos para que puedas seleccionar
                
                data.data.forEach((row) => {

                    const texto = row.document_type === "DNI"
                        ? `${row.first_names} ${row.last_names}`
                        : row.company_name;

                    $("#id_guest").append(`<option value="${row.id}">${texto}</option>`);
                });

                // $("#id_guest").val(data.data[0].id).trigger("change");
                $("#no_client_found").addClass("d-none");

            } else {

                // 1. Avisamos que vamos a buscar afuera

                $("#id_guest").empty().append('<option value="">No está en el sistema. Buscando en RENIEC/SUNAT...</option>');    

                get_api();

                const campoNacionalidad = document.getElementById("nacionalidad");

                if(campoNacionalidad){
                    campoNacionalidad.value = "Peruano";
                }

                $("#no_client_found").removeClass("d-none");
            }
        }
    });
}

// --- 1. VARIABLES GLOBALES ---
let habitacionesAlertadas = new Set();

// --- 2. FUNCIONES LÓGICAS ---
async function revisarHabitacionesVencidas() {
    try {
        const response = await fetch('/sistema_hotelero/Reception/get_reservations'); 
        if (!response.ok) return; 
        
        const data = await response.json();

        if (data && data.data) { 
            data.data.forEach(reserva => {
                const id = reserva.id;
                if ((reserva.estado === 'Vencido' || reserva.estado === 'Ocupado') && !habitacionesAlertadas.has(id)) {
                    toastr.warning(`Habitación ${reserva.num_habitacion || id} requiere atención`, "Alerta de Tiempo", {
                        "positionClass": "toast-bottom-right",
                        "timeOut": "15000",
                        "progressBar": true
                    });
                    habitacionesAlertadas.add(id); 
                }
                if (reserva.estado !== 'Vencido' && reserva.estado !== 'Ocupado') {
                    habitacionesAlertadas.delete(id);
                }
            });
        }
    } catch (e) {
        // Silenciamos el error para no laguear la consola
    }
}

// --- 3. EVENTOS Y ARRANQUE (EL ORDEN IMPORTA) ---
    $(document).ready(function () {
     //Paso 0, "agregado tras agregar al sistema el dato de pisos" : Se cargan las opciones de piso 
        if (typeof loadFlatFilterOptions === "function") {
            loadFlatFilterOptions();
        }
        //-----   
    // PASO 1: Cargamos lo visual primero (Habitaciones)
    if (typeof fetchRooms === "function") {
        fetchRooms(); 
    }

    // PASO 2: Esperamos 3 segundos antes de activar al Vigilante
    // Esto evita que las 4 peticiones se peleen en el Network al mismo tiempo
    setTimeout(function() {
        revisarHabitacionesVencidas();
    }, 3000); 

    // PASO 3: Repetimos el vigilante cada 45 segundos para no saturar
    setInterval(revisarHabitacionesVencidas, 45000);

    // --- BOTÓN CONSULTAR BD (Sin cambios) ---
    $(document).on("click", "#btn_buscar_bd", function (e) {
          console.log("CLICK FUNCIONANDO");

        e.preventDefault();
        const tipo = $("#client_document_type").val();
        const numero = $("#document_number_reservation").val();
        // toastr.options = { "positionClass": "toast-bottom-right", "timeOut": "3000" };

        $.ajax({
            url: BASE_URL + "Reception/get_guest",
            type: "GET",
            data: { document_type: tipo, document_number: numero },
            dataType: "json",
            success: function(response) {
              console.log("RESPONSE:", response);
              
              let lista = response.data || [];
                  console.log("LISTA:", lista);

    if (response.status === "OK" && lista && lista.length > 0) {
        console.table(lista);

        const cliente_real = lista[0];
        
        // CAMBIO AQUÍ: Usamos .name porque en 'clients' está todo el nombre ahí
        const nombreBD = cliente_real.first_names + " " + cliente_real.last_names;
        const idReal = cliente_real.id_guest;

        console.log("ID detectado:", idReal);
        console.log("Nombre detectado:", nombreBD);

        toastr.success('Encontrado: ' + nombreBD); 

        // Creamos la opción para el select
        const newOption = new Option(nombreBD, idReal, true, true);
        $('#id_guest').empty().append(newOption).trigger('change');
    } else {
        toastr.error('No se encontró en la base de datos.');
    }
}
        });
    });

    // --- VALIDACIÓN DE FECHAS (Sin cambios) ---
    const inputInicio = document.getElementById("fechaInicio");
    const inputFin = document.getElementById("fechaFin");

    const hoyLocal = new Date();
    const anio = hoyLocal.getFullYear();
    const mes = String(hoyLocal.getMonth() + 1).padStart(2, '0');
    const dia = String(hoyLocal.getDate()).padStart(2, '0');
    const fechaHoy = `${anio}-${mes}-${dia}`;

    // Solo para el inicio, para no reservar en el pasado
    if (inputInicio) inputInicio.setAttribute("min", fechaHoy);

    // UN SOLO EVENTO PARA TODO EL BLOQUE DE TIEMPO
    $(document).on("change", "#fechaInicio, #fechaFin, #hora_termino", function() {
    
    // 1. Si es la fecha de inicio, actualizamos el mínimo de la final
    if (this.id === "fechaInicio" && this.value && inputFin) {
        inputFin.min = this.value;
    }

    // 2. LIMPIEZA DE ERRORES (Solo al cambiar, para que no moleste mientras escribes)
    $(this).removeClass("is-invalid error").css("border-color", "");
    $("#" + this.id + "-error").remove();
    $(this).siblings(".error, .invalid-feedback").remove();

    // 3. Reset del validador de jQuery (El del circulito rojo)
    var form = $(this).closest("form");
    if (form.data("validator")) {
        form.validate().resetElements($(this));
    }

    // 4. Validación del servidor
    if (typeof valDate === "function") {
        valDate("checkout_date", "checkout_time");
    }

    // 5. Recalcular Monto
    if (typeof calcularMontoTotal === "function") {
        calcularMontoTotal();
    }
});

// --- BOTÓN API () ---
$(document).on("click", "#btn_buscar_api", function (e) {
    e.preventDefault();
    if (typeof get_api === "function") {
        get_api();
    }
});
});

function registrarVentaDesdeRecepcion(form) {
    let params = new FormData(form);

    // 1. Obtenemos el ID del huésped
    let idGuest = $('#id_guest').val(); 

    // 2. REFUERZO: Si el Select2 está actuando raro, sacamos el ID del objeto interno
    // Esto es lo que asegura que use el "7" y no el texto "33562458"
    let dataSelect = $('#id_guest').select2('data');
    if (dataSelect && dataSelect.length > 0) {
        idGuest = dataSelect[0].id; 
    }

    console.log("ID que se va a guardar:", idGuest); // Mira esto en la consola antes de que se cierre

    // Validación estricta
    if (!idGuest || idGuest === "" || isNaN(idGuest)) {
        toastr.error("Error: El ID del huésped no es válido (" + idGuest + ")");
        return;
    }

    // Sobrescribimos en params para estar 100% seguros
    params.set("id_guest", idGuest); 
    params.append("status", "1");

    $.ajax({
        url: BASE_URL + "Income_Products/save_income_from_reception", 
        type: "POST",
        data: params,
        dataType: "json",
        contentType: false,
        processData: false,
        success: function (data) {
            if (data.status === "OK") {
                // CONFIGURACIÓN PARA QUE SALGA ABAJO
                toastr.options = { 
                    "positionClass": "toast-bottom-right", 
                    "timeOut": "3000" 
                };

                // CERRAMOS EL MODAL (Sin F5)
                $(".modal").modal("hide"); 
                
                // SOLO UN MENSAJE DE ÉXITO
                toastr.success("¡Reserva guardada con éxito!");

                // RECARGAMOS SOLO LAS HABITACIONES (Sin recargar toda la web)
                if (typeof fetchRooms === "function") {
                    // Tip: Si fetchRooms lanza sus propios mensajes, 
                    // podrías comentar la línea de toastr.success de arriba.
                    fetchRooms(); 
                }
            } else {
                toastr.error("Error: " + data.msg);
            }
        },
        error: function(xhr) {
          console.log("ERROR REAL:", xhr.responseText);
        }

    });
}

// BUSCA ESTO AL FINAL DE TU INDEX.JS
// Asegúrate de que NO esté dentro de otra función como registrarVentaDesdeRecepcion

$(document).on("click", "#btn_guardar_bd", function (e) {
    e.preventDefault();

    // Convertimos "DNI" a 1 y "RUC" a 2 para que coincida con tu columna id_document_type
    let docTypeStr = $("#client_document_type").val();
    let docTypeId = (docTypeStr === "DNI") ? 1 : (docTypeStr === "RUC" ? 2 : 0);

    let dni_nombre = $("#nombre").val() || "";
    let ruc_razon = $("#razon_social").val() || "";

    const datos = {
        document_type: docTypeId, // Enviamos el ID numérico
        document_number: $("#document_number_reservation").val().trim(),
        // Combinamos nombre y apellido aquí o lo mandamos separado para que el PHP lo junte
        first_names: (docTypeId === 2) ? ruc_razon : dni_nombre,
        last_names: (docTypeId === 2) ? "" : ($("#apellido").val() || ""),
        company_name: ruc_razon,
        address: $("#direccion").val() || ""
    };

    if (!datos.document_number || (!datos.first_names && !datos.company_name)) {
        toastr.warning("Faltan datos. Primero consulta la API para cargar el nombre.");
        return;
    }

    $.ajax({
        url: BASE_URL + "Reception/create_guest_reservation",
        type: "POST",
        data: datos,
        dataType: "json",
        success: function (response) {
            console.log("Respuesta:", response);
            if (response.status === "OK") {
                toastr.success("¡Cliente registrado en la tabla PERSON!");
                $("#btn_buscar_bd").click(); 
            } else {
                // Si el PHP devuelve un error (como que el DNI ya existe)
                toastr.error("Aviso: " + (response.msg || "No se pudo guardar"));
            }
        },
        error: function(xhr) {
            console.error("Detalle del error:", xhr.responseText);
            toastr.error("Error de conexión con el servidor.");
        }
    });
});
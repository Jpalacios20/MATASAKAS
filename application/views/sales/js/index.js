// Función para cargar las reservas y actualizar la tabla
function load_data() {
  $.ajax({
    url: BASE_URL + "Reservation/get_reservations",
    type: "GET",
    dataType: "json",
    cache: false,
    success: function (data) {
      if (data.status === "OK") {
        // Limpia el contenido de la tabla
        if ($.fn.DataTable.isDataTable('#datatable-Product')) {
    $('#datatable-Product').DataTable().destroy();
}
        $("#datatable-Product tbody").empty();
        

        // Agrega una fila por cada reservación//////////////
        $.each(data.data, function (index, row) {
          if ((row.room_status ?? '') !== "Limpieza") {
            $("#datatable-Product tbody").append(`
<tr 
  id="row-${row.id_reservation}"
  data-id="${row.id_reservation}"
  data-total="${row.payment_total ?? row.payment_room ?? '0.00'}"
>
  <td>${row.checkin_date ?? ''}</td>
  <td>${row.cliente ?? 'Sin cliente'}</td>
  <td>${row.room_number ?? ''}</td>
  <td>${row.checkin_time ?? ''}</td>
  <td>${row.checkout_date ?? ''}</td>
  <td>${row.checkout_time ?? ''}</td>
  <td>S/ ${row.payment_total ?? row.payment_room ?? '0.00'}</td>
  <td>${
  row.status === 'Pendiente' ? 'Pago Pendiente' :
  row.status === 'Reservado' ? 'Pago Confirmado' :
  row.status === 'Ocupado' ? 'Reserva Ocupada' :
  row.status === 'Libre' ? 'Pago Libre' :
  row.status ?? ''
}</td>
  <td>
    <button class="btn bg-light text-dark btn-icon btn-md btn_sales" data-process-key="${row.id_reservation}">
      <i class="fa-solid fa-cart-shopping"></i>
    </button>
    <button class="btn bg-light text-dark btn-icon btn-md btn_new_action" data-process-key="${row.id_reservation}">
      <i class="fa-solid fa-clock"></i>
    </button>
    <button class="btn btn-sm btn-danger btn-round btn-icon btn_delete_sales" data-id="${row.id_reservation}">
      <i class="fa-solid fa-trash"></i>
    </button>
  </td>
</tr>
`);
          }
        });

// Muestra mensaje de éxito o error
$("#datatable-Product").DataTable({

  pageLength: 10,
  lengthMenu: [10, 25, 50, 100],
  order: [[0, "desc"]],
  language: {
    lengthMenu: "Mostrar _MENU_ registros",
    search: "Buscar:",
    zeroRecords: "No se encontraron resultados",
    info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
    infoEmpty: "Mostrando 0 registros",
    infoFiltered: "(filtrado de _MAX_ registros)",
    paginate: {
      next: "Siguiente",
      previous: "Anterior"
    }
  }
});
        functions.toast_message(data.type, data.msg, data.status);
      } else {
        functions.toast_message(data.type, data.msg, data.status);
      }
    },
    error: function () {
      console.error("Fallo al obtener los datos.");
      functions.toast_message("error", "Error al obtener los datos", "Error");
    },      
  });
}

// Prevenir el comportamiento predeterminado del botón de eliminar
$(document).on('click', '.btn_delete_sales', function() {
    // Obtener el ID de la reservación desde el atributo data-id
    let value = $(this).attr('data-id');
    // Configurar los parámetros para la solicitud AJAX
    let params = {
        'id_reservation': value
    };
    // Mostrar el cuadro modal de confirmación con SweetAlert2
    Swal.fire({
        title: '¿Estás seguro?',
        text: '¡No podrás revertir esto!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Si, eliminar!',
        cancelButtonText: 'No, cancelar!',
        customClass: {
            confirmButton: 'btn btn-primary',
            cancelButton: 'btn btn-outline-danger ms-1'
        },
        buttonsStyling: false,
        preConfirm: () => {
            // Realizar la solicitud AJAX para eliminar la reservación
            return $.ajax({
                url: BASE_URL + 'Sales/delete_reservation',
                type: 'POST',
                data: params,
                dataType: 'json',
                cache: false,
                success: function(data) {
                    if (data.status === 'OK') {
                        // Eliminar la fila correspondiente de la tabla
                        $(`#row-${value}`).remove();
                        Swal.fire('¡Eliminado!', 'La reservación ha sido eliminada correctamente.', 'success');
                    } else {
                        Swal.fire('Error', data.msg, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Error al intentar eliminar la reservación.', 'error');
                }
            });
        }
    }).then(result => {
        if (result.isConfirmed) {
            console.log('Eliminación confirmada.');
        } else {
            console.log('Eliminación cancelada.');
        }
    });
});

function update_sales_products(form) {
  let params = new FormData(form);

  if (item && item.id_reservation && item.id_payment) {
    params.append("id_reservation", item.id_reservation);
    params.append("id_payment", item.id_payment);
  }

  // Construir la URL con los parámetros adicionales
  const url =
    BASE_URL +
    "Reservation/product_sales_details" +
    "?id_reservation=" +
    encodeURIComponent(item.id_reservation) +
    "&id_payment=" +
    encodeURIComponent(item.id_payment);

  for (let [key, value] of params.entries()) {
    console.log(`${key}: ${value}`);
  }

  $.ajax({
    url: url,
    type: "POST",
    data: params,
    processData: false,
    contentType: false,
    dataType: "json",
    success: function (response) {
      if (response.status === "OK") {
        functions.toast_message(response.type, response.msg, response.status);
      } else {
        functions.toast_message(response.type, response.msg, response.status);
      }
    },
    error: function (xhr, status, error) {
      console.error("Error en la solicitud: ", error);
      console.log("Respuesta completa del servidor:", xhr.responseText);
      functions.toast_message(
        "error",
        "Error en la conexión con el servidor.",
        "ERROR"
      );
    },
  });
}

$(document).ready(function () {
  $("#btn_create_guest_reservation").on("click", function (e) {
    e.preventDefault();
    $("#sales_form").submit();
    actualizarTotalPago();
  });
  // Call calcularTotal on document ready to update total display
  calcularTotal();
});

$(document).ready(function () {
  $("#sales_form").validate({
    submitHandler: function (form) {
      update_sales_products(form);
    },
  });
});

$(document).on("click", ".btn_sales", function () {
  let value = $(this).attr("data-process-key");

  $.ajax({
    url: BASE_URL + "Reservation/get_reservation",
    type: "GET",
    data: { id_reservation: value },
    dataType: "json",
    success: function (data) {
      if (data.status === "OK") {
        item = data.data;

        $("#reservation_payment_form :input[name='id_reservation']").val(
          item.id_reservation
        );
        $("#reservation_payment_form :input[name='id_payment']").val(
          item.id_payment
        );

        $("#reservation_payment_form").show();
      }
    },
    error: function () {
      console.error("Fallo al obtener los datos.");
    },
  });
});
//------------------------
//--------------------------------

////////////////
function loadSalesHistory(idReservation) {
  // Limpiar el contenido del historial antes de cargar nuevos datos
  $("#sales_history_body").empty();
  $("#total-sales-history").text("S/ 0.00");

  $.ajax({
    url: BASE_URL + "Sales/sales_product_history",
    type: "GET",
    data: { id_reservation: idReservation },
    dataType: "json",
    success: function (response) {
      if (response.status === "OK") {
        const salesData = response.data;
        let rows = "";

        salesData.forEach((sale) => {
          let cantidad = parseFloat(sale.cantidad) || 0;
          let precioUnitario = parseFloat(sale.product_price) || 0;
          let total = cantidad * precioUnitario;

          rows += `
                      <tr>
                          <input type="hidden" name="id_reservation" value="${sale.id_reservation}">
                          <td>${sale.fecha_venta}</td>
                          <td>${sale.product_name}</td>
                          <td>${cantidad}</td>
                          <td>S/ ${precioUnitario.toFixed(2)}</td>
                          <td>S/ ${total.toFixed(2)}</td>
                      </tr>
                  `;
        });

        // Insertar las filas en la tabla
        $("#sales_history_body").html(rows);
        calcularTotalHistorial();
        actualizarTotalPago();
      } else {
        $("#sales_history_body").html(
          "<tr><td colspan='5'>No hay datos disponibles.</td></tr>"
        );
      }
    },
    error: function () {
      $("#sales_history_body").html(
        "<tr><td colspan='5'>Error al cargar los datos.</td></tr>"
      );
    },
  });
}

function calcularTotalHistorial() {
  var total = 0;

  // Calcular el total iterando sobre las filas de la tabla
  $("#sales_history_body tr").each(function () {
    var totalPrice = parseFloat(
      $(this).find("td:eq(4)").text().replace(/[^\d.-]/g, "")
    );
    total += isNaN(totalPrice) ? 0 : totalPrice;
  });

  // Formatear el total para mostrarlo en la tabla
  var totalFormatted = total.toLocaleString("es-PE", {
    style: "currency",
    currency: "PEN",
  });

  // Mostrar el total en la tabla
  $("#total-sales-history").text(totalFormatted);
}

function actualizarTotalPago() {
  var payment_sales = 0;

  // Calcular el total de ventas iterando sobre las filas de la tabla
  $("#sales_history_body tr").each(function () {
    var totalPriceText = $(this).find("td:eq(4)").text(); // Obtén el texto del total
    var totalPrice = parseFloat(
      totalPriceText.replace(/[^0-9.,]/g, "").replace(",", ".")
    ); // Elimina "S/" y otros caracteres no numéricos, ajusta decimal
    payment_sales += isNaN(totalPrice) ? 0 : totalPrice; // Suma al total
  });

  // Actualizar el valor en allTotalPrice.sales para que se refleje en paymentAuto()
  allTotalPrice.sales = payment_sales;

  // Obtener el ID de la reserva
  var id_reservation = $("input[name='id_reservation']").first().val();

  if (!id_reservation) {
    console.error("ID de reserva no definido.");
    alert("No se encontró el ID de reserva. Verifica los datos.");
    return;
  }

  if (payment_sales === 0) {
    console.warn("El total de ventas es 0. Verifica los datos de la tabla.");
  }

  console.log("Datos enviados:", { payment_sales, id_reservation });

  // Enviar la solicitud AJAX
  $.ajax({
    url: BASE_URL + "Sales/update_payment_total", // Ajusta el endpoint según tu backend
    type: "POST",
    contentType: "application/json",
    data: JSON.stringify({
      payment_sales: payment_sales,
      id_reservation: id_reservation,
    }),
    dataType: "json",
    success: function (response) {
      console.log("Respuesta del servidor:", response);
      if (response.status === "OK") {
      } else {
      }
    },
    error: function (xhr, status, error) {
      console.error("Error al enviar la solicitud al servidor:", error);
      alert("Error al enviar la solicitud al servidor.");
    },
  });

  // Actualizar los totales en la interfaz
  paymentAuto();
}

////////////////////////////////////////////////////

$(document).on("click", ".btn_sales", function () {
  $("#sales_modal").on("shown.bs.modal", function () {
    console.log("Modal principal abierto");

    // Escucha el cambio de tab "Historial de Ventas"
    $("#nav-history-tab").on("shown.bs.tab", function () {
      console.log("Tab Historial de Ventas activado");

      // Cargar historial al cambiar de tab
      const idReservation = $("input[name='id_reservation']").val();
      if (idReservation) {
        loadSalesHistory(idReservation);
      }
    });

    // Detecta el cambio de registro y actualiza el historial automáticamente
    $("input[name='id_reservation']").on("change", function () {
      const idReservation = $(this).val();
      if (idReservation && $("#nav-history-tab").hasClass("active")) {
        loadSalesHistory(idReservation);
      }
    });

    $(".btn_sales").on("click", function () {
      const idReservation = $("input[name='id_reservation']").val();
      if (idReservation && $("#nav-history-tab").hasClass("active")) {
        loadSalesHistory(idReservation);
      }
    });
  });
});

//------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------

//--------------------------
//funciones para el baton jalado de reservacion

//funcion get---

$(document).on("click", ".btn_new_action", function () {
  var id_reservation = $(this).data("process-key");

  $("#update_reservation_modal").modal("show");

  get_reservation_details(id_reservation);
});

//--

function get_reservation_details(id_reservation) {
  $.ajax({
    url: BASE_URL + "Reservation/get_reservation",
    cache: false,
    data: { id_reservation: id_reservation },
    success: function (data) {
      const element = data.data;

      let numReservation = element.id_reservation.toString().padStart(2, "0");

      $("input[name=id_reservation]").val(element.id_reservation);
      $(".idReservation").text(numReservation);

      $("input[name=id_room]").val(element.id_room);
      $("#numRoom").text(element.room_number);
      $("input[name=type_room]").val(element.type_name);
      $("input[name=bed_name]").val(element.bed_type);
      $("input[name=person_limit]").val(element.person_limit);
      $(".detalle-numH").text(element.room_number);
      $(".detalle-tipoH").text(element.type_name);
      $(".detalle-tipoC").text(element.bed_type);

      $("input[name=checkin_date]").val(element.checkin_date);
      $("input[name=checkin_time]").val(element.checkin_time);
      $("input[name=checkout_date]").val(element.checkout_date);
      $("input[name=checkout_time]").val(element.checkout_time);
      $("input[name=checkout_date]").attr("min", element.checkin_date);
      $("input[name=checkin_date]").attr("max", element.checkout_date);
      $(".detalle-entrada").text(
        `${element.checkin_date}  ${element.checkin_time}`
      );
      $(".detalle-salida").text(
        `${element.checkout_date}  ${element.checkout_time}`
      );

      $(".detalle-nombres").text(capitalizeName(element.first_names));
      $(".detalle-apellidos").text(capitalizeName(element.last_names));
      $(".detalle-razonSc").text(capitalizeName(element.company_name));

      $(".detalle-tipoDoc").text(element.document_type);
      $(".detalle-numDoc").text(element.document_number);
      $(".detalle-direccion").text(element.address);
      /*
          $('input[name=id_reservation]').val(element.id_reservation);
          $('input[name=id_room]').val(element.id_room);
          $('input[name=type_room]').val(element.type_name);
          $('input[name=bed_name]').val(element.bed_type);
          $('input[name=person_limit]').val(element.person_limit);
          $('input[name=checkin_date]').val(element.checkin_date);
          $('input[name=checkin_time]').val(element.checkin_time);
          $('input[name=checkout_date]').val(element.checkout_date);
          $('input[name=checkout_time]').val(element.checkout_time);
          $('input[name=departure_date]').val(element.departure_date);
          $('input[name=departure_time]').val(element.departure_time);
          $('input[name=id_payment]').val(element.id_payment);
          $('.idReservation').text(numReservation);
          $('.detalle-entrada').text(`${element.checkin_date}  ${element.checkin_time}`);
          $('.detalle-salida').text(`${element.checkout_date}  ${element.checkout_time}`);
          $('.detalle-numH').text(element.room_number);
          $('.detalle-tipoH').text(element.type_name);
          $('.detalle-tipoC').text(element.bed_type);
          $('.detalle-tipoDoc').text(element.document_type);
          $('.detalle-numDoc').text(element.document_number);
          $('.detalle-direccion').text(element.address);
          $('.detalle-nombres').text(capitalizeName(element.first_names));
          $('.detalle-apellidos').text(capitalizeName(element.last_names));
          $('.detalle-razonSc').text(capitalizeName(element.company_name));
          */
      //--
      if (element.last_names.length > 1 && element.first_names.length > 1) {
        // Ocultar el RUC y dejar el DNI visible
        $(".huesped-ruc").hide().css("position", "absolute");
        $(".huesped-dni").show();
      } else {
        // Ocultar el DNI y dejar el RUC visible
        $(".huesped-dni").hide().css("position", "absolute");
        $(".huesped-ruc").show();
      }
      /*
          $('input[name=payment_room]').val(decimalNumber(element.payment_room));
          $('input[name=payment_extra]').val(decimalNumber(element.payment_extra));
          $('input[name=payment_discount]').val(decimalNumber(element.payment_discount));
          $('input[name=payment_total]').val(decimalNumber(element.payment_total));
          $('input[name=payment_cancelled]').val(decimalNumber(element.pre_payment));
          */
      //--
      var select = $("#mySelect");
      if (element.status == "Ocupado") {
        select.prop("selectedIndex", 0);
      } else if (element.status == "Reservado") {
        select.prop("selectedIndex", 1);
      } else if (element.status == "Libre") {
        select.prop("selectedIndex", 2);
      }

      allPricesRoom = [];
      allPricesRoom.push(element.price_temporary);
      allPricesRoom.push(element.price_half);
      allPricesRoom.push(element.price_day);
      paymentReservation(
        element.checkin_date + " " + element.checkin_time,
        element.checkout_date + " " + element.checkout_time,
        allPricesRoom,
        0
      );
      element.payment_room == null || element.payment_room < 1
        ? paymentReservation(
            element.checkin_date + " " + element.checkin_time,
            element.checkout_date + " " + element.checkout_time,
            allPricesRoom,
            1
          )
        : ($("input[name=payment_room]").val(
            decimalNumber(element.payment_room)
          ),
          (allTotalPrice.room = parseFloat(element.payment_room)));
      $("input[name=id_payment]").val(element.id_payment);
      element.payment_extra == null || element.payment_extra < 1
        ? $("input[name=payment_extra]").val("0.00")
        : ($("input[name=payment_extra]").val(
            decimalNumber(element.payment_extra)
          ),
          (allTotalPrice.extra = parseFloat(element.payment_extra)));
      element.payment_discount == null || element.payment_discount < 1
        ? $("input[name=payment_discount]").val("0.00")
        : ($("input[name=payment_discount]").val(
            decimalNumber(element.payment_discount)
          ),
          (allTotalPrice.discount = parseFloat(element.payment_discount)));
      element.pre_payment == null || element.pre_payment < 1
        ? $("input[name=payment_cancelled]").val("0.00")
        : ($("input[name=payment_cancelled]").val(
            decimalNumber(element.pre_payment)
          ),
          (allTotalPrice.cancelled = parseFloat(element.pre_payment)));
      element.payment_total == null || element.payment_total < 1
        ? $("input[name=payment_total]").val("0.00")
        : $("input[name=payment_total]").val(
            decimalNumber(element.payment_total)
          );
      paymentAuto();

      $("input[name=departure_date]").val(element.departure_date);
      $("input[name=departure_time]").val(element.departure_time);

      $(".detalle-salidaF").text(`Fecha: ${element.departure_date}`);
      $(".detalle-salidaH").text(`Hora: ${element.departure_time}`);
      $(".detalle-pagoHab").text(`S/ ${element.payment_room}`);
      $(".detalle-pagoExtra").text(`S/ ${element.payment_extra}`);
      $(".detalle-subTotal").text(
        `S/ ${decimalNumber(
          parseFloat(element.payment_extra) + parseFloat(element.payment_room)
        )}`
      );
      $(".detalle-descuento").text(`S/ ${element.payment_discount}`);
      $(".detalle-totalPagar").text(
        `S/ ${decimalNumber(
          parseFloat(element.payment_extra) +
            parseFloat(element.payment_room) -
            parseFloat(element.payment_discount)
        )}`
      );
    },
  });
}
//---------------------------------------------------------------
//--
function capitalizeName(name) {
  let parts = name.split(" ");
  let result = "";
  for (let part of parts) {
    if (part.length > 0) {
      result += part[0].toUpperCase() + part.slice(1).toLowerCase() + " ";
    }
  }
  return result.trim();
}

var allPricesRoom = [];
var allTotalPrice = {
  room: 0,
  extra: 0,
  sales: 0,
  discount: 0,
  cancelled: 0,
};

// -- Date payment --

function paymentReservation(dateIn, dateOut, prices, type) {
  const fechaInicio = new Date(dateIn);
  const fechaFin = new Date(dateOut);
  const milisegundosInicio = fechaInicio.getTime();
  const milisegundosFin = fechaFin.getTime();

  let minutes = (milisegundosFin - milisegundosInicio) / (1000 * 60);
  let days = Math.floor(minutes / 1440);
  let minutesRes = minutes % 1440;
  let hours = Math.floor(minutesRes / 60);
  let minutesLeft = minutesRes % 60;

  let paymentRoom = 0;

  // Calculate time and price
  if (minutes <= 240) {
    paymentRoom = parseFloat(prices[0]);
    $(".detalle-tiempo").text(`
          ${
            hours < 1
              ? " "
              : hours == 1
              ? hours + " hora"
              : hours + " horas"
          }
          ${
            minutesLeft < 1
              ? " "
              : minutesLeft == 1
              ? minutesLeft + " minuto"
              : minutesLeft + " minutos"
          }
      `);
  } else if (minutes > 240 && minutes <= 720) {
    paymentRoom = parseFloat(prices[1]);
    $(".detalle-tiempo").text(`
          ${
            hours < 1
              ? " "
              : hours == 1
              ? hours + " hora"
              : hours + " horas"
          }
          ${
            minutesLeft < 1
              ? " "
              : minutesLeft == 1
              ? minutesLeft + " minuto"
              : minutesLeft + " minutos"
          }
      `);
  } else {
    let payment = parseInt(prices[2]) * days;
    let paymentMinutes = 0;

    // --
    if (minutesRes > 0 && minutesRes <= 240) {
      paymentMinutes = parseInt(prices[0]);
    } else if (minutesRes > 240 && minutesRes <= 720) {
      paymentMinutes = parseInt(prices[1]);
    } else {
      paymentMinutes = parseInt(prices[2]);
    }
    payment += paymentMinutes;

    paymentRoom = payment;
    $(".detalle-tiempo").text(`
          ${(days + "").padStart(2, "0")} ${
      days == 1 ? "día" : "días"
    }
          ${
            hours < 1
              ? " "
              : hours == 1
              ? hours + " hora"
              : hours + " horas"
          }
          ${
            minutesLeft < 1
              ? " "
              : minutesLeft == 1
              ? minutesLeft + " minuto"
              : minutesLeft + " minutos"
          }
      `);
  }

  if (type == 1) {
    // set room price
    allTotalPrice.room = parseFloat(paymentRoom);
    $("input[name=payment_room]").val(decimalNumber(paymentRoom));
  } else if (type == 2) {
    // price extra
    let price_extra = 0;
    $.ajax({
      url: BASE_URL + "Reservation/get_payment_extra",
      cache: false,
      success: function (data) {
        for (let value of data.data) {
          const minutes_define =
            parseInt(value.extra_time.split(":")[0], 10) * 60 +
            parseInt(value.extra_time.split(":")[1], 10);

          if (minutes > minutes_define) {
            price_extra = value.price_extra;
          }
        }
        allTotalPrice.extra = parseFloat(price_extra);
        $("input[name=payment_extra]").val(decimalNumber(price_extra));

        paymentAuto();
      },
    });
  } else if ((type = 3)) {
    // reservation on time
    if (minutes > 0) {
      return true;
    } else {
      return false;
    }
  }
}

// -- Payments changes --

function paymentAuto() {
  let payment_subTotal = allTotalPrice.room + allTotalPrice.extra;
  $("input[name=payment_subTotal]").val(decimalNumber(payment_subTotal));

  let payment_lack =
    payment_subTotal +
    allTotalPrice.sales -
    (allTotalPrice.discount + allTotalPrice.cancelled);
  $("input[name=payment_lack]").val(decimalNumber(payment_lack));

  let payment_total =
    payment_subTotal + allTotalPrice.sales - allTotalPrice.discount;
  $("input[name=payment_total]").val(decimalNumber(payment_total));
}

$("input[name=payment_discount]").on("input", function () {
  let discount = $("input[name=payment_discount]").val();
  parseFloat(discount) < 1 || Number.isNaN(parseFloat(discount))
    ? (discount = 0)
    : "";
  allTotalPrice.discount = parseFloat(discount);
  paymentAuto();
});

$("input[name=payment_extra]").on("input", function () {
  let extra = $("input[name=payment_extra]").val();
  parseFloat(extra) < 1 || Number.isNaN(parseFloat(extra))
    ? ((extra = 0), $("input[name=payment_extra]").val(extra))
    : " ";
  allTotalPrice.extra = parseFloat(extra);
  paymentAuto();
});

$("input[name=payment_cancelled]").on("input", function () {
  let cancelled = $("input[name=payment_cancelled]").val();
  let lack = $("input[name=payment_lack]").val();
  parseFloat(cancelled) < 1 || Number.isNaN(parseFloat(cancelled))
    ? ((cancelled = 0), $("input[name=payment_cancelled]").val(cancelled))
    : "";
  allTotalPrice.cancelled = parseFloat(cancelled);
  paymentAuto();
});

$("payment_total").prop("readonly", true);

$("input[type=number]").on("focusout", function () {
  let val = $(this).val();
  $(this).val(decimalNumber(val));
});

function decimalNumber(val) {
  var text = Number(val).toFixed(2);
  return text;
}

// -- Date --

$("#departure_btn").on("click", function () {
  if (statusLibre) {
    $("input[name=departure_date], input[name=checkout_date]").val(
      currentTime("d")
    );
    $("input[name=departure_time], input[name=checkout_time]").val(
      currentTime("t")
    );
  } else {
    $("input[name=departure_date]").val(currentTime("d"));
    $("input[name=departure_time]").val(currentTime("t"));
  }
  // $('#departure_btn').attr('disabled', false);
  let valCheckout =
    $("input[name=checkout_date]").val() +
    " " +
    $("input[name=checkout_time]").val();
  let valDepurate = currentTime("d") + " " + currentTime("t");

  paymentReservation(valCheckout, valDepurate, allPricesRoom, 2);
});

// --
var statusLibre = false;
function valDate(input_name, input_nameAux) {
  let valCheckout;
  let valCheckin;
  if (statusLibre) {
    valCheckin =
      $("input[name=checkin_date]").val() +
      " " +
      $("input[name=checkin_time]").val();
    valCheckout = currentTime("d") + " " + currentTime("t");
  } else {
    valCheckin =
      $("input[name=checkin_date]").val() +
      " " +
      $("input[name=checkin_time]").val();
    valCheckout =
      $("input[name=checkout_date]").val() +
      " " +
      $("input[name=checkout_time]").val();
  }

  const valDateInitial = $("input[name=checkin_date]").val();
  $("input[name=checkout_date]").attr("min", valDateInitial);

  $("input[name=checkout_date]").attr(
    "min",
    $("input[name=checkin_date]").val()
  );
  $("input[name=checkin_date]").attr(
    "max",
    $("input[name=checkout_date]").val()
  );

  const valRoom = $("input[name=id_room]").val();
  const valReservation = $("input[name=id_reservation]").val();

  $.ajax({
    url: BASE_URL + "Reservation/date_reservation",
    cache: false,
    data: {
      id_room: valRoom,
      checkin_date: valCheckin,
      checkout_date: valCheckout,
      id_reservation: valReservation,
    },
    success: function (data) {
      if (data.status == "ERROR") {
        $("input[name=" + input_name + "]")
          .addClass("is-invalid")
          .attr("data-error", data.msg);
        $("input[name=" + input_nameAux + "]")
          .addClass("is-invalid")
          .attr("data-error", data.msg);
        functions.toast_message(
          data.type,
          data.msg + data.data,
          data.status
        );
      } else if (data.status == "OK") {
        $("input[name=" + input_name + "]")
          .removeClass("is-invalid")
          .removeAttr("data-error");
        $("input[name=" + input_nameAux + "]")
          .removeClass("is-invalid")
          .removeAttr("data-error");
        paymentReservation(valCheckin, valCheckout, allPricesRoom, 1);
      }
    },
  });
}

$("input[name='checkin_date']").on("change", () => {
  valDate("checkin_date", "checkin_time");
});
$("input[name='checkin_time']").on("change", () => {
  valDate("checkin_time", "checkin_date");
});
$("input[name='checkout_date']").on("change", () => {
  valDate("checkout_date", "checkout_time");
});
$("input[name='checkout_time']").on("change", () => {
  valDate("checkout_time", "checkout_date");
});

//  -- Update Status --

$("select[name='room_status']").on("change", () => {
  const selectedIndex = $("select[name='room_status']").prop("selectedIndex");
  if (selectedIndex === 0 || selectedIndex === 1) {
    $("input[name=checkout_date]").attr("readonly", false);
    $("input[name=checkout_time]").attr("readonly", false);
  } else {
    $("input[name=checkout_date]").attr("readonly", true);
    $("input[name=checkout_time]").attr("readonly", true);
  }
});

// -- Cancel --

$(".cancel_update").click(function () {
  $("input[type='date']")
    .removeClass("is-invalid")
    .removeAttr("data-error");
  $("input[type='time']")
    .removeClass("is-invalid")
    .removeAttr("data-error");
  $("input").removeClass("error");
  $(".huesped-ruc").hide().css("position", "");
  $(".huesped-dni").hide().css("position", "");
});

//-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

// Función para actualizar una reservación
function update_reservation(form, id_reservation) {
  $("#update_reservation_form").prop("disabled", true);

  let params = new FormData(form);

  $.ajax({
    url: BASE_URL + "Reservation/update_reservation",
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
      functions.toast_message(data.type, data.msg, data.status);

      if (data.status === "OK") {
        $("#update_reservation_modal").modal("hide");

        if (id_reservation) {
          get_reservation_details(id_reservation);
        }
      } else {
        $("#btn_update_reservation").prop("disabled", false);
      }
    },
    complete: function () {
      $("#update_reservation_form").prop("disabled", false);
    },
  });
}

// Función para actualizar el pago
function update_payment(form, id_reservation) {
  $("#departure_reservation_form").prop("disabled", true);

  let params = new FormData(form);

  $.ajax({
    url: BASE_URL + "Reservation/update_payment",
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
      functions.toast_message(data.type, data.msg, data.status);

      if (data.status === "OK" && id_reservation) {
        get_reservation_details(id_reservation);
      }
    },
    complete: function () {
      $("#departure_reservation_form").prop("disabled", false);
    },
  });
}

// Validación del formulario de actualización
$("#update_reservation_form").validate({
  submitHandler: function (form) {
    let invalidInputs = $("input[type='date'], input[type='time']").hasClass(
      "is-invalid"
    );

    if (invalidInputs) {
      functions.toast_message(
        "error",
        "La actualización no se ha realizado porque uno de los campos es incorrecto.",
        "ERROR"
      );
    } else {
      const id_reservation = $(form).find("input[name='id_reservation']").val();
      update_reservation(form, id_reservation);
    }
  },
});

// Validación del formulario de salida
$("#departure_reservation_form").validate({
  submitHandler: function (form) {
    const id_reservation = $(form).find("input[name='id_reservation']").val();
    update_payment(form, id_reservation);
  },
});

//-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
//-------------------------------------------

//--

$(document).on("click", ".btn_add", function () {
  let value = $(this).attr("data-process-key");
  let params = { id_product: value };

  $.ajax({
    url: BASE_URL + "Product/get_product_by_id",
    type: "GET",
    data: params,
    dataType: "json",
    cache: false,
    success: function (data) {
      console.log("Respuesta cruda del servidor:", data);

      let item = data.data ? data.data : data;

      if (item) {
        addProductToTable(item);
      } else {
        console.error("El servidor no devolvió un producto válido:", data);
      }
    },
    error: function (jqXHR, textStatus, errorThrown) {
      console.error("Error AJAX:", textStatus, errorThrown);
      console.log("Respuesta de texto:", jqXHR.responseText);
    }
  });
});

function addProductToTable(product) {
  let table = $("#add_products tbody");
  
  if (table.length === 0) {
    console.error("❌ No se encontró la tabla '#add_products tbody' en el HTML.");
    return;
  }

  let existingRow = table.find(`input[name='id_product[]'][value='${product.id_product}']`).closest("tr");

  if (existingRow.length > 0) {
    // Si el producto ya existe, sumar la cantidad
    let quantityInput = existingRow.find("input[name='cantidad[]']");
    let currentQuantity = parseInt(quantityInput.val()) || 0;
    quantityInput.val(currentQuantity + 1);

    // Recalcular el subtotal
    calcularSubtotal(quantityInput);
  } else {
    // Si el producto no existe, agregar una nueva fila
    let newRow = getHtml(product);
    table.append(newRow);
    
    // Corregido: Pasamos el input de cantidad de la nueva fila para que calcule bien
    calcularSubtotal(table.find("tr:last input[name='cantidad[]']"));
  }
}

function getHtml(item) {
  var cont = $(".btn_add").length + 1;
  console.log(cont);
  // Calcular el subtotal inicial basado en cantidad 1 y precio del producto
  var initialSubtotal = parseFloat(item.product_price).toLocaleString("es-PE", {
    style: "currency",
    currency: "PEN",
  });
  return `
    <tr class="text-center">
      <td>
        <button class="btn btn-danger btn-sm btn-delete-product" type="button">
          <i class="fa fa-trash"></i>
        </button>
      </td>
      <td>${item.product_name}</td>
      <td>
        <input type="number" name="cantidad[]" value="1" class="form-control" oninput="calcularSubtotal(this)">
      </td>
      <td>
        <input type="number" name="product_price[]" value="${item.product_price}" min="0.1" class="form-control" readonly>
      </td>
      <td>
        <select name="price_percentage[]" class="form-control" onchange="calcularSubtotal(this)">
          <option value="0">0%</option>
          <option value="10">10%</option>
          <option value="15">15%</option>
          <option value="20">20%</option>
          <option value="25">25%</option>
          <option value="30">30%</option>
          <option value="40">40%</option>
          <option value="50">50%</option>
        </select>
      </td>
      <td>
        <span class="subtotal" data-value="1">${initialSubtotal}</span>
      </td>
      <input type="hidden" name="id_product[]" value="${item.id_product}">
      <input type="hidden" name="fecha_venta[]" value="${new Date().toISOString().split('T')[0]}">
      <input type="hidden" name="product_sku[]" value="${item.product_sku}">
      <input type="hidden" name="product_name[]" value="${item.product_name}">
    </tr>
  `;
}

$(document).on("click", "#btn_create_guest_reservation", function (e) {
  e.preventDefault();

  // Validar si hay productos en la tabla antes de enviar
  let rowCount = $("#add_products tbody tr").length;
  if (rowCount === 0) {
    functions.toast_message("error", "Debe agregar al menos un producto a la lista.", "Atención");
    return;
  }

  // Obtener los datos serializados de los inputs de la reserva y los productos
  let formData = $("#sales_form").find(
    "input[name='id_reservation'], input[name='id_payment'], input[name^='id_product'], input[name^='cantidad'], input[name^='product_price'], input[name^='price_percentage'], input[name^='fecha_venta'], input[name^='product_sku'], input[name^='product_name']"
  ).serialize();

  $.ajax({
    url: BASE_URL + "Reservation/product_sales_details", // Ruta en tu backend que procesará e insertará los productos
    type: "POST",
    data: formData,
    dataType: "json",
    beforeSend: function () {
      console.log("Guardando productos de la reserva...");
    },
    success: function (data) {
      if (data.status === "OK") {
        functions.toast_message("success", data.msg || "Productos agregados correctamente.", "Éxito");
        
        // Limpiar la tabla de productos elegidos y el total
        $("#add_products tbody").empty();
        $("#total-sales-price").text("S/ 0.00");

        // Cerrar el modal
        $("#sales_modal").modal("hide");

        // Recargar los datos principales si tu función existe
        if (typeof load_data === "function") {
          load_data();
        }
      } else {
        functions.toast_message("error", data.msg || "Error al guardar los datos.", "Error");
      }
    },
    error: function (jqXHR, textStatus, errorThrown) {
      console.error("Error en la comunicación con el servidor:", textStatus, errorThrown);
      functions.toast_message("error", "Error en la comunicación con el servidor.", "Error");
    },
  });
});

//----------------------------
//---------------------

function calcularSubtotal(element) {
  var row = $(element).closest("tr");
  var stock = parseInt(row.find('input[name="cantidad[]"]').val());
  var purchasePrice = parseFloat(
    row.find('input[name="product_price[]"]').val()
  );
  var percentage = parseInt(
    row.find('select[name="price_percentage[]"]').val()
  );
  var subtotal = stock * purchasePrice;
  var discountAmount = (subtotal * percentage) / 100;
  var discountedSubtotal = subtotal - discountAmount;
  var soles = discountedSubtotal.toLocaleString("es-PE", {
    style: "currency",
    currency: "PEN",
  });
  row.find("span.subtotal").text(soles);
  console.log("Subtotal updated:", soles);
  calcularTotal();
}

function calcularTotal() {
  var total = 0;
  $(".subtotal").each(function () {
    var subtotalValue = parseFloat($(this).text().replace(/[^\d.-]/g, ""));
    total += isNaN(subtotalValue) ? 0 : subtotalValue;
  });
  var totalFormatted = total.toLocaleString("es-PE", {
    style: "currency",
    currency: "PEN",
  });
  $("#total-sales-price").text(totalFormatted);
}

function getPercentage(cont) {
  return `
    <td>
      <select name="price_percentage[]" class="form-control" onchange="calcularSubtotal($(this).closest('tr'))">
        <option value="0">0%</option>
        <option value="10">10%</option>
        <option value="15">15%</option>
        <option value="20">20%</option>
        <option value="25">25%</option>
        <option value="30">30%</option>
        <option value="40">40%</option>
        <option value="50">50%</option>
      </select>
    </td>`;
}

function destroy_datatable_income_products() {
  // --
  $("#datatables-income-products").dataTable().fnDestroy();
}

function load_datatable_income_products() {
  // --
  destroy_datatable_income_products();
  // --
  let dataTable = $("#datatables-income-products").DataTable({
    // --
    ajax: {
      url: BASE_URL + "Product/get_product",
      cache: false,
    },
    columns: [
      {
        class: "center",
        render: function (data, type, row) {
          // --
          return (
            '<button class="btn btn-sm btn-info btn-round btn-icon btn_add" data-process-key="' +
            row.id_product +
            '">' +
            feather.icons["plus"].toSvg({ class: "font-small-4" }) +
            "</button>"
          );
        },
      },
      { data: "product_sku" },
      { data: "product_name" },
    ],
    // dom: functions.head_datatable(),
    // buttons: functions.custom_buttons_datatable([2], '#create_product_modal'), // -- Number of columns
    language: {
      url: BASE_URL + "public/assets/json/languaje-es.json",
    },
  });

  // --
  dataTable.on("xhr", function () {
    // --
    var data = dataTable.ajax.json();
    // --
    functions.toast_message(data.type, data.msg, data.status);
  });
}
load_datatable_income_products();

let item;

$(document).on("click", ".btn_sales", function () {
  // --
  let value = $(this).attr("data-process-key");
  // --
  console.log(value);
  // --
  $.ajax({
    url: BASE_URL + "Reservation/get_reservation",
    type: "GET",
    data: { id_reservation: value },
    dataType: "json",
    contentType: false,
    processData: true,
    cache: false,
    success: function (data) {
      // --
      console.log(data);

      if (data.status === "OK") {
        // console.log(data + "fdgxd");
        // // --
        let item = data.data;
        // // --
        console.log(data.data);
        $("#create_sales_form :input[name=id_reservation]").val(
          item.id_reservation
        );
        $("#create_sales_form :input[name=id_payment]").val(item.id_payment);

        $("#sales_form :input[name=id_reservation]").val(item.id_reservation);
        $("#sales_form :input[name=id_payment]").val(item.id_payment);
        // console.log(item);
        $("#create_sales_form :input[name=room_number]").val(item.room_number);
        $("#create_sales_form :input[name=type_name]").val(item.type_name);
        $("#create_sales_form :input[name=document_type]").val(
          item.document_type
        );
        $("#create_sales_form :input[name=document_number]").val(
          item.document_number
        );
        if (item.document_type === "DNI") {
          $("#company_names").hide();
          $("#first_names").show();
          $("#last_names").show();
          $("#create_sales_form :input[name=first_names]").val(item.first_names);
          $("#create_sales_form :input[name=last_names]").val(item.last_names);
          $("#create_sales_form :input[name=address]").val(item.address);
        } else {
          $("#company_names").show();
          $("#first_names").hide();
          $("#last_names").hide();
          $("#create_sales_form :input[name=address]").val(item.address);
          $("#create_sales_form :input[name=company_name]").val(
            item.company_name
          );
        }
        // hola mundo xd
        // $("#create_sales_form :input[name=id_room]").val(item.id_room);
        // -- Otras asignaciones de valores para los campos de actualización
      }
    },
    error: function () {
      console.error("Fallo al obtener los datos.");

      functions.toast_message("error", "Error al obtener los datos", "Error");
    },
  });

  $("#nav-home-tab").tab("show");

  // --
  $("#sales_modal").modal("show");
});
$(document).on("click", ".btn-delete-product", function () {
  // Delete the row corresponding to the button "x" clicked
  $(this).closest("tr").remove();
});

$(document).on("click", "#btn_create_guest_reservation", function () {
  // Obtener el ID de la reservación
  const idReservation = $("input[name='id_reservation']").val();

  // Calcular el nuevo total de ventas
  let totalSales = 0;
  $("#add_products tbody tr").each(function () {
    const quantity = parseFloat($(this).find("input[name='cantidad[]']").val()) || 0;
    const price = parseFloat($(this).find("input[name='product_price[]']").val()) || 0;
    totalSales += quantity * price;
  });

  // Obtener el monto de la reservación
  const reservationAmount = parseFloat($(`tr:has(td:contains('${idReservation}')) td:nth-child(7)`).text().replace(/[^\d.-]/g, "")) || 0;

  // Sumar el monto de la reservación con el total de ventas
  const updatedTotal = reservationAmount + totalSales;

  // Actualizar la columna "Costo Total" en la tabla principal
  const row = $(`#datatable-Product tbody tr:has(td:contains('${idReservation}'))`);
  if (row.length > 0) {
    row.find("td:eq(6)").text(`S/ ${updatedTotal.toFixed(2)}`);
  }

  // Limpiar la tabla de productos elegidos después de agregar la venta
  $("#add_products tbody").empty();
  $("#total-sales-price").text("S/ 0.00");

  console.log("Venta agregada y tabla principal actualizada con el monto total.");
});

function sayHi() {
  console.log("Hola mundo!");
}

sayHi();

load_data();

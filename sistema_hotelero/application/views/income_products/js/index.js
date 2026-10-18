// ===================== LISTADO DATATABLE =====================

function destroy_datatable() {
  $("#datatable-income-products").dataTable().fnDestroy();
}

function refresh_datatable() {
  $("#datatable-income-products").DataTable().ajax.reload();
}

function load_datatable() {
  destroy_datatable();

  let dataTable = $("#datatable-income-products").DataTable({
    order: [[0, "desc"]],
    ajax: {
      url: BASE_URL + "Income_Products/get_income_products",
      cache: false,
      dataSrc: function (json) {
        if (json.warning && json.warning.show) {
          showWarningAlert(json.warning);
        }
        return json.data;
      },
    },
    columns: [
      { data: "data_time", defaultContent: "" },
      { data: "name", defaultContent: "" },
      { data: "user_name", defaultContent: "" },
      { data: "voucher_type_description", defaultContent: "" },
      {
        data: null,
        render: function (data, type, row) {
          return `${row.voucher_series ?? ""} - ${row.number_serial ?? ""}`;
        },
      },
      { data: "payment_type_description", defaultContent: "" },
      { data: "total_purchase", defaultContent: "0.00" },
      {
        className: "center",
        render: function (data, type, row) {
          let buttons = "";

          buttons += `<button class="btn btn-sm btn-primary btn_details" data-process-key="${row.id_income_products}">
            ${feather.icons["eye"].toSvg({ class: "font-small-2" })}
          </button> `;

          // buttons += `<button class="btn btn-sm btn-warning btn_update" data-process-key="${row.id_income_products}">
          //   ${feather.icons["edit"].toSvg({ class: "font-small-2" })}
          // </button> `;

          buttons += `<button class="btn btn-sm btn-danger btn_delete_custom" data-process-key="${row.id_income_products}">
            ${feather.icons["trash-2"].toSvg({ class: "font-small-2" })}
          </button> `;

          buttons += `<button class="btn btn-sm btn-success">
            Descargar
          </button>`;

          return buttons;
        },
      },
    ],
    dom: functions.head_datatable(),
    buttons: functions.custom_buttons_datatable([7], "#create_income_products_modal"),
    language: { url: BASE_URL + "public/assets/json/languaje-es.json" },
  });

  dataTable.on("xhr", function () {
    let data = dataTable.ajax.json();
  });
}

// ===================== MODAL VER DETALLE =====================

$("#datatable-income-products").on("click", ".btn_details", function () {
  var id_income_products = $(this).data("process-key");

  $.ajax({
    url: BASE_URL + "Income_Products/income_products_details",
    type: "GET",
    data: { id_income_products: id_income_products },
    dataType: "json",
    success: function (response) {
      if (response.status === "OK") {
        var data = response.result;

        $("#name_client").val(data.client_name);
        $("#sale_date").val(data.sale_date || "Fecha no disponible");
        $("#voucher_type").val(data.voucher_type);
        $("#payment_type").val(data.payment_type);
        $("#series").val(data.series);
        $("#number_serial").val(data.number_serial);
        $("#payment_shape").val(data.payment_shape);

        $("#incomeProductDetails").empty();

        data.products.forEach(function (product) {
          var cantidad    = parseInt(product.quantity) || 0;
          var precio      = parseFloat(product.full_purchase) || 0;
          var precioTotal = parseFloat(product.subtotal) || 0;

          var row = `
          <tr>
             <td>${product.product_name}</td>
             <td>${cantidad}</td>
             <td>${product.expiration_date}</td>
             <td>S/ ${precio.toFixed(2)}</td>
             <td>S/ ${parseFloat(product.selling_price || 0).toFixed(2)}</td>
             <td>S/ ${precioTotal.toFixed(2)}</td>
          </tr>`;
          $("#incomeProductDetails").append(row);
        });

        $("#incomeProductModal").modal("show");
      } else {
        alert("No se encontraron detalles para este ingreso de productos.");
      }
    },
    error: function () {
      alert("Ocurrió un error al obtener los detalles.");
    },
  });
});

// ===================== UPDATE =====================

$("#datatable-income-products").on("click", ".btn_update", function () {
  var id_income_products = $(this).data("process-key");
  window.location.href = BASE_URL + "Income_Products_Update/index.php?id=" + id_income_products;
});

// ===================== DELETE =====================

$("#datatable-income-products").on("click", ".btn_delete_custom", function () {
  var id_income_products = $(this).data("process-key");

  if (id_income_products) {
    Swal.fire({
      title: "¿Estás seguro de eliminar este ingreso?",
      text: "Esta acción no se puede deshacer.",
      icon: "error",
      showCancelButton: true,
      confirmButtonText: "Sí, eliminar",
      cancelButtonText: "Cancelar",
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: BASE_URL + "Income_Products/delete_income_products",
          type: "POST",
          data: { id_income_product: id_income_products },
          success: function (response) {
            if (response.status === "OK") {
              Swal.fire("¡Eliminado!", "El registro ha sido eliminado.", "success");
              location.reload();
            } else {
              Swal.fire("Error", "Hubo un problema al eliminar.", "error");
            }
          },
          error: function () {
            Swal.fire("Error", "No se pudo completar la solicitud.", "error");
          },
        });
      }
    });
  } else {
    Swal.fire("Error", "ID no encontrado", "error");
  }
});

// ===================== ALERTA PENDIENTES =====================

function showWarningAlert(warning) {
  Swal.mixin({
    toast: true,
    position: "bottom-end",
    showConfirmButton: true,
    showCancelButton: true,
    confirmButtonText: "Ver pendientes",
    cancelButtonText: "Cerrar",
    timer: 15000,
    timerProgressBar: true,
    customClass: {
      confirmButton: "btn btn-warning btn-sm",
      cancelButton: "btn btn-outline-secondary btn-sm ms-1",
      container: "p-20",
    },
    buttonsStyling: false,
  }).fire({
    icon: "warning",
    title: '<i class="fas fa-exclamation-triangle"></i> Registros Pendientes!',
    html: `
      <div class="text-justify" style="max-width: 300px">
        <p class="mb-2">${warning.message}</p>
        <hr class="my-2">
        <p class="text-muted small mb-0">
          <i class="fas fa-info-circle me-1"></i>
          Por favor: Tenga en cuenta que los registros pendientes que no sean aceptados dentro del plazo establecido serán rechazados.
        </p>
      </div>`,
  }).then((result) => {
    if (result.isConfirmed) {
      window.location.assign(BASE_URL + "Income_Products_Pending");
    }
  });
}

// ===================== MODAL CREAR - POBLAR SELECTS =====================

function get_name() {
  $.ajax({
    url: BASE_URL + "Clients/get_name",
    type: "GET",
    dataType: "json",
    contentType: false,
    processData: false,
    cache: false,
    success: function (data) {
      if (data.status === "OK") {
        var html = '<option value="">Seleccionar</option>';
        data.data.forEach((element) => {
          html += `<option value="${element.id}">${element.name}</option>`;
        });
        $("#create_income_products_details_form :input[name=name]").html(html);
      }
    },
  });
}

function get_voucher_type() {
  $.ajax({
    url: BASE_URL + "Main/get_voucher_type",
    type: "GET",
    dataType: "json",
    contentType: false,
    processData: false,
    cache: false,
    success: function (data) {
      if (data.status === "OK") {
        var html = '<option value="">Seleccionar</option>';
        data.data.forEach((element) => {
          html += `<option value="${element.id}">${element.description}</option>`;
        });
        $("#create_income_products_details_form :input[name=vt_description]").html(html);
      }
    },
  });
}

function get_payment_type() {
  $.ajax({
    url: BASE_URL + "Main/get_payment_type",
    type: "GET",
    dataType: "json",
    contentType: false,
    processData: false,
    cache: false,
    success: function (data) {
      if (data.status === "OK") {
        var html = '<option value="">Seleccionar</option>';
        data.data.forEach((element) => {
          html += `<option value="${element.id}">${element.description}</option>`;
        });
        $("#create_income_products_details_form :input[name=pt_description]").html(html);
      }
    },
  });
}

function get_payment_shape() {
  $.ajax({
    url: BASE_URL + "Main/get_payment_shape",
    type: "GET",
    dataType: "json",
    contentType: false,
    processData: false,
    cache: false,
    success: function (data) {
      if (data.status === "OK") {
        var html = '<option value="">Seleccionar</option>';
        data.data.forEach((element) => {
          html += `<option value="${element.id}">${element.description}</option>`;
        });
        $("#create_income_products_details_form :input[name=pm_description]").html(html);
      }
    },
    error: function (xhr, status, error) {
      console.error("Error al obtener formas de pago: " + error);
    },
  });
}

// ===================== DATATABLE PRODUCTOS (modal selección) =====================

function destroy_datatable_income_products() {
  $("#datatables-income-products").dataTable().fnDestroy();
}

function refresh_datatable_income_products() {
  $("#datatables-income-products").DataTable().ajax.reload();
}

function load_datatable_income_products() {
  destroy_datatable_income_products();
  let dataTable = $("#datatables-income-products").DataTable({
    ajax: {
      url: BASE_URL + "Product/get_product",
      cache: false,
    },
    columns: [
      {
        class: "center",
        render: function (data, type, row) {
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
      { data: "product_description" },
    ],
    language: { url: BASE_URL + "public/assets/json/languaje-es.json" },
  });

  dataTable.on("xhr", function () {
    var data = dataTable.ajax.json();
    functions.toast_message(data.type, data.msg, data.status);
  });
}

// ===================== AGREGAR PRODUCTO A LA TABLA =====================

$(document).on("click", ".btn_add", function () {
  let value = $(this).attr("data-process-key");
  $.ajax({
    url: BASE_URL + "Product/get_product_by_id",
    type: "GET",
    data: { id_product: value },
    dataType: "json",
    contentType: false,
    processData: true,
    cache: false,
    success: function (data) {
      if (data.status === "OK") {
        let item       = data.data;
        let productRow = $(`tr[data-product-id="${item.id_product}"]`);

        if (productRow.length > 0) {
          let quantityInput = productRow.find('input[name="stock[]"]');
          let newQuantity   = parseInt(quantityInput.val()) + 1;
          quantityInput.val(newQuantity);
          calcularSubtotal(quantityInput);
        } else {
          $("#detalle_productos_cuerpo").append(getHtml(item));
          actualizarTotalGeneral();
        }
      }
    },
  });
});

function getHtml(item) {
  let stock         = 1;
  let purchase_price = parseFloat(item.product_price) || 0;
  let productCode   = item.product_sku || "";
  let productName   = item.product_name || "";

  return `
  <tr class="product-row" data-product-id="${item.id_product}">
      <td><button type="button" class="btn btn-danger btn-delete-product btn-sm">x</button></td>
      <td class="product-code" data-code="${productCode}">${productCode}</td>
      <td>${productName}</td>
      <td><input type="date" name="product_expiration_date[]" class="form-control form-control-sm"></td>
      <td>
          <input type="number" name="stock[]" value="${stock}"
                 class="form-control form-control-sm input-quantity"
                 oninput="calcularSubtotal(this)" min="1">
      </td>
      <td>
          <input type="number" name="price[]" value="${purchase_price.toFixed(2)}"
                 class="form-control form-control-sm input-price"
                 oninput="calcularSubtotal(this)" step="0.01">
      </td>
      <td>
          <input type="number" name="selling_price[]"
                 class="form-control form-control-sm input-selling-price"
                 value="" step="0.01" placeholder="Precio Venta">
      </td>
      <td>
          <span class="row-subtotal" data-value="${purchase_price.toFixed(2)}">
              S/ ${purchase_price.toFixed(2)}
          </span>
      </td>
  </tr>`;
}

function calcularSubtotal(element) {
  let row      = $(element).closest("tr");
  let cantidad = parseFloat(row.find(".input-quantity").val()) || 0;
  let precio   = parseFloat(row.find(".input-price").val()) || 0;
  let subtotal = cantidad * precio;

  row.find(".row-subtotal")
     .text("S/ " + subtotal.toFixed(2))
     .attr("data-value", subtotal.toFixed(2));

  actualizarTotalGeneral();
}

function actualizarTotalGeneral() {
  let granTotal = 0;
  $(".row-subtotal").each(function () {
    granTotal += parseFloat($(this).attr("data-value")) || 0;
  });
  $("#gran_total").text(granTotal.toFixed(2));
}

$(document).on("click", ".btn-delete-product", function () {
  $(this).closest("tr").remove();
  actualizarTotalGeneral();
});

// ===================== LIMPIAR MODAL AL CERRAR =====================

$("#create_income_products_modal").on("hidden.bs.modal", function () {
  $("#create_income_products_details_form")[0].reset();
  $("#detalle_productos_cuerpo").empty();
  $("#gran_total").text("0.00");
});

// ===================== GUARDAR NUEVO INGRESO =====================

$(document).ready(function () {

  $("#btn_guardar_product").click(function (event) {
    event.preventDefault();

    let rowCount = $("#detalle_productos_cuerpo tr").length;

    if (rowCount === 0) {
      Swal.fire({ icon: "warning", title: "No hay productos", text: "Debe agregar al menos un producto." });
      return;
    }

    let id_client        = $("select[name='name']").val();
    let id_voucher_type  = $("select[name='vt_description']").val();
    let id_payment_type  = $("select[name='pt_description']").val();
    let id_payment_shape = $("select[name='pm_description']").val();
    let series           = $("input[name='series']").val();
    let number_serial    = $("input[name='number_serial']").val();
    let data_time = $("input[name='data_time']").val();

    let errores = [];
    if (!id_client)        errores.push("Proveedor");
    if (!id_voucher_type)  errores.push("Tipo de Comprobante");
    if (!id_payment_type)  errores.push("Método de Pago");
    if (!id_payment_shape) errores.push("Forma de Pago");
    if (!series)           errores.push("Serie");
    if (!number_serial)    errores.push("Número de Serie");

    if (errores.length > 0) {
      Swal.fire({ icon: "warning", title: "Campos incompletos", text: "Falta llenar: " + errores.join(", ") });
      return;
    }

    let purchase_total = 0;
    $("#detalle_productos_cuerpo .row-subtotal").each(function () {
      purchase_total += parseFloat($(this).attr("data-value")) || 0;
    });

    let formData = {
      id_client:        id_client,
      id_voucher_type:  id_voucher_type,
      id_payment_type:  id_payment_type,
      id_payment_shape: id_payment_shape,
      series:           series,
      number_serial:    number_serial,
      purchase_total:   purchase_total,
      data_time: data_time,
    };

    $.ajax({
      url: BASE_URL + "Income_Products/create_income_products",
      type: "POST",
      data: JSON.stringify(formData),
      contentType: "application/json",
      dataType: "json",
      success: function (response) {
        if (response.status === "OK") {
          enviarProductos(response.id);
        } else {
          Swal.fire({ icon: "error", title: "Error", text: response.msg || "No se pudo crear el ingreso." });
        }
      },
      error: function () {
        Swal.fire({ icon: "error", title: "Error de conexión", text: "Hubo un error al intentar guardar el ingreso." });
      },
    });
  });
});

function enviarProductos(idIngreso) {
  let productos = [];

  $("#detalle_productos_cuerpo tr").each(function () {
    let productId      = $(this).attr("data-product-id");
    let productCode    = $(this).find(".product-code").attr("data-code") || "";
    let stock          = parseInt($(this).find('input[name="stock[]"]').val()) || 0;
    let purchasePrice  = parseFloat($(this).find('input[name="price[]"]').val()) || 0;
    let sellingPrice   = parseFloat($(this).find('input[name="selling_price[]"]').val()) || 0;
    let expirationDate = $(this).find('input[name="product_expiration_date[]"]').val() || null;
    let subtotal       = parseFloat($(this).find(".row-subtotal").attr("data-value")) || 0;

    if (productId && stock > 0 && purchasePrice > 0) {
      productos.push({
        id_income_products:      idIngreso,
        id_product:              productId,
        product_code:            productCode,
        product_expiration_date: expirationDate,
        quantity:                stock,
        full_purchase:           purchasePrice,
        selling_price:           sellingPrice,
        subtotal:                subtotal,
      });
    }
  });

  if (productos.length > 0) {
    $.ajax({
      url: BASE_URL + "Income_Products/create_income_products_details",
      type: "POST",
      data: JSON.stringify({ productos: productos }),
      contentType: "application/json",
      dataType: "json",
      success: function (response) {
        if (response.status === "OK") {
          Swal.fire({ icon: "success", title: "Compra registrada", text: "Los productos han sido guardados exitosamente." })
            .then(() => {
              $("#create_income_products_modal").modal("hide");
              refresh_datatable();
            });
        } else {
          Swal.fire({ icon: "error", title: "Error", text: response.msg || "Error al guardar los productos." });
        }
      },
      error: function () {
        Swal.fire({ icon: "error", title: "Error de conexión", text: "Hubo un error al intentar guardar los productos." });
      },
    });
  } else {
    Swal.fire({ icon: "warning", title: "Productos inválidos", text: "Verifique que todos los productos tengan cantidad y precio válidos." });
  }
}

// ===================== CARGAR AL ABRIR MODAL CREAR =====================

$("#create_income_products_modal").on("show.bs.modal", function () {
  get_name();
  get_voucher_type();
  get_payment_type();
  get_payment_shape();
  load_datatable_income_products();
});

// ===================== INIT =====================

load_datatable();
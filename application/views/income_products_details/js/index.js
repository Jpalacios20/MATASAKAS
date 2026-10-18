function destroy_datatable_income() {
  $("#datatables-income").dataTable().fnDestroy();
}

function refresh_datatable_income() {
  $("#datatables-income").DataTable().ajax.reload();
}

// -- Sin Uso
function load_datatable_income() {
  destroy_datatable_income();
  let dataTable = $("#datatables-income").DataTable({
    ajax: {
      url: BASE_URL + "",
      cache: false,
    },
    columns: [{ data: "first_name" }],
    // dom: functions.head_datatable(),
    // buttons: functions.custom_buttons_datatable([0,1], '#create_user_modal'), // -- Number of columns
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

function destroy_datatable_income_products() {
  $("#datatables-income-products").dataTable().fnDestroy();
}

function refresh_datatable_income_products() {
  $("#datatables-income-products").DataTable().ajax.reload();
}

// --Carga Productos
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
    // dom: functions.head_datatable(),
    // buttons: functions.custom_buttons_datatable([2], '#create_product_modal'), // -- Number of columns
    language: {
      url: BASE_URL + "public/assets/json/languaje-es.json",
    },
  });

    dataTable.on("xhr", function () {
    var data = dataTable.ajax.json();
    functions.toast_message(data.type, data.msg, data.status);
  });
}

// --Campo Nombre Cliente
function get_name() {
  // --
  $.ajax({
    url: BASE_URL + "Clients/get_name",
    type: "GET",
    dataType: "json",
    contentType: false,
    processData: false,
    cache: false,
    beforeSend: function () {
      
    },
    success: function (data) {
      // --
      if (data.status === "OK") {
        
        // --
        var html = '<option value="">Seleccionar</option>';
        // --
        data.data.forEach((element) => {
          html +=
            '<option value="' +
            element.id +
            '">' +
            element.name +
            "</option>";
        });
        // -- Set values for select
        $("#create_income_products_details_form :input[name=name]").html(html);
      }
    },
  });
}

// -- Campo Voucher
function get_voucher_type() {
  // --
  $.ajax({
    url: BASE_URL + "Main/get_voucher_type",
    type: "GET",
    dataType: "json",
    contentType: false,
    processData: false,
    cache: false,
    beforeSend: function () {
      
    },
    success: function (data) {
      // --
      if (data.status === "OK") {
        // --
        var html = '<option value="">Seleccionar</option>';
        // --
        data.data.forEach((element) => {
          html +=
            '<option value="' +
            element.id +
            '">' +
            element.description +
            "</option>";
        });
        // -- Set values for select
        $("#create_income_products_details_form :input[name=vt_description]").html(html);
      }
    },
  });
}

// -- Campo Tipo-Pago
function get_payment_type() {
  // --
  $.ajax({
    url: BASE_URL + "Main/get_payment_type",
    type: "GET",
    dataType: "json",
    contentType: false,
    processData: false,
    cache: false,
    beforeSend: function () {
      
    },
    success: function (data) {
      // --
      if (data.status === "OK") {
        // --
        var html = '<option value="">Seleccionar</option>';
        // --
        data.data.forEach((element) => {
          html +=
            '<option value="' +
            element.id +
            '">' +
            element.description +
            "</option>";
        });
        // -- Set values for select
        $("#create_income_products_details_form :input[name=pt_description]").html(html);
      }
    },
  });
}

// Campos payment - shape
function get_payment_shape() {
  $.ajax({
      url: BASE_URL + "Main/get_payment_shape",  
      type: "GET",
      dataType: "json",
      contentType: false,
      processData: false,
      cache: false,
      beforeSend: function () {
        
      },
      success: function (data) {
          if (data.status === "OK") {
              var html = '<option value="">Seleccionar</option>';
              data.data.forEach((element) => {
                  html += '<option value="' + element.id + '">' + element.description + "</option>";
              });
              // Set valores para el select
              $("#create_income_products_details_form :input[name=pm_description]").html(html);
          }
      },
      error: function (xhr, status, error) {
          console.error("Error al obtener los datos de formas de pago: " + error);
      }
  });
}

//---------------------------------ESTO ES PARA INGRESAR EL AGREGAR PRODUCTO--------------------------------
//--sirve para que al precionar baje el producto - IMPORTANTE
$(document).on("click", ".btn_add", function () {
  // --
  let value = $(this).attr("data-process-key");
  // --
  let params = { id_product: value };
  // --
  $.ajax({
    url: BASE_URL + "Product/get_product_by_id",
    type: "GET",
    data: params,
    dataType: "json",
    contentType: false,
    processData: true,
    cache: false,
    beforeSend: function () {
     // console.log("Cargando...");
    },
    success: function (data) {
      if (data.status === "OK") {
        let item = data.data;
        let productRow = $(`tr[data-product-id="${item.id_product}"]`);
  
        if (productRow.length > 0) {
          // Si el producto ya existe, aumentar la cantidad
          let quantityInput = productRow.find('input[name="stock[]"]');
          let newQuantity = parseInt(quantityInput.val()) + 1;
          quantityInput.val(newQuantity);
          calcularSubtotal(quantityInput); // Recalcular subtotal
        } else {
          // Si el producto no está en la tabla, agregar una nueva fila
          $("#detalle_productos_cuerpo").append(getHtml(item));
          calcularSubtotal();
          actualizarTotalGeneral();
        }
      }
    },
  });
});


function getHtml(item) {
    let stock = 1;
    let purchase_price = parseFloat(item.product_price) || 0;
    let productCode = item.product_sku || '';
    let productName = item.product_name || '';

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
                   value="${purchase_price.toFixed(2)}" step="0.01">
        </td>
        <td>
            <span class="row-subtotal" data-value="${purchase_price.toFixed(2)}">
                S/ ${purchase_price.toFixed(2)}
            </span>
        </td>
    </tr>`;
}

// 2. Función de cálculo (Busca las clases definidas arriba)
function calcularSubtotal(element) {
    let row = $(element).closest("tr");
    let cantidad = parseFloat(row.find('.input-quantity').val()) || 0;
    let precio = parseFloat(row.find('.input-price').val()) || 0;

    let subtotal = cantidad * precio;

    // Actualizamos el precio de venta (input) y el subtotal (span)
    row.find('.input-selling-price').val(subtotal.toFixed(2));
    row.find('.row-subtotal').text("S/ " + subtotal.toFixed(2)).attr("data-value", subtotal.toFixed(2));

    actualizarTotalGeneral();
}

// 3. Función del Gran Total
function actualizarTotalGeneral() {
    let granTotal = 0;
    $(".row-subtotal").each(function() {
        granTotal += parseFloat($(this).attr("data-value")) || 0;
    });
    $("#gran_total").text(granTotal.toFixed(2));
}

// 4. Evento para eliminar
$(document).on("click", ".btn-delete-product", function () {
    $(this).closest("tr").remove();
    actualizarTotalGeneral();
});

$(document).ready(function () {
  // Debugging al iniciar
  console.log("=== PÁGINA CARGADA ===");
  console.log("Form ID:", $("#create_income_products_details_form").attr("id"));
  console.log("Selects encontrados:", $("select").length);
  console.log("Inputs encontrados:", $("input").length);
  
  // Mostrar nombres de todos los inputs
  $("select").each(function(index) {
      console.log("Select " + index + ":", $(this).attr("name"), "| Value:", $(this).val());
  });
  
  $("#btn_guardar_product").click(function (event) {
      event.preventDefault();
      
      console.log("\n=== BOTÓN GUARDAR PRESIONADO ===");
      
      // Validar que haya productos agregados
      let rowCount = $("#detalle_productos_cuerpo tr").length;
      console.log("Cantidad de productos en tabla:", rowCount);
      
      if (rowCount === 0) {
          Swal.fire({
              icon: "warning",
              title: "No hay productos",
              text: "Debe agregar al menos un producto."
          });
          return;
      }

      // INTENTA Múltiples selectores para encontrar los valores
      // Opción 1: Selector original
      let id_client = $("select[name='name']").val();
      let id_voucher_type = $("select[name='vt_description']").val();
      let id_payment_type = $("select[name='pt_description']").val();
      let id_payment_shape = $("select[name='pm_description']").val();
      
      // Si no encuentra con nombres, intenta buscar por posición
      if (!id_client) {
          id_client = $("#create_income_products_details_form select").eq(0).val();
          console.warn("id_client no encontrado por nombre, buscando por posición: ", id_client);
      }
      if (!id_voucher_type) {
          id_voucher_type = $("#create_income_products_details_form select").eq(1).val();
          console.warn("id_voucher_type no encontrado por nombre, buscando por posición: ", id_voucher_type);
      }
      if (!id_payment_type) {
          id_payment_type = $("#create_income_products_details_form select").eq(2).val();
          console.warn("id_payment_type no encontrado por nombre, buscando por posición: ", id_payment_type);
      }
      if (!id_payment_shape) {
          id_payment_shape = $("#create_income_products_details_form select").eq(3).val();
          console.warn("id_payment_shape no encontrado por nombre, buscando por posición: ", id_payment_shape);
      }
      
      let series = $("input[name='series']").val();
      let number_serial = $("input[name='number_serial']").val();
      let expiration_date = $("input[name='expiration_date']").val();

      // LOGUEO DETALLADO PARA DEBUGGING
      console.log("=== VALORES CAPTURADOS ===");
      console.log("id_client:", id_client, "| Tipo:", typeof id_client, "| Vacío:", !id_client);
      console.log("id_voucher_type:", id_voucher_type, "| Tipo:", typeof id_voucher_type, "| Vacío:", !id_voucher_type);
      console.log("id_payment_type:", id_payment_type, "| Tipo:", typeof id_payment_type, "| Vacío:", !id_payment_type);
      console.log("id_payment_shape:", id_payment_shape, "| Tipo:", typeof id_payment_shape, "| Vacío:", !id_payment_shape);
      console.log("series:", series, "| Tipo:", typeof series, "| Vacío:", !series);
      console.log("number_serial:", number_serial, "| Tipo:", typeof number_serial, "| Vacío:", !number_serial);
      console.log("expiration_date:", expiration_date, "| Tipo:", typeof expiration_date, "| Vacío:", !expiration_date);

      // Validación con más información
      let errores = [];
      
      if (!id_client) errores.push("Cliente");
      if (!id_voucher_type) errores.push("Tipo de Comprobante");
      if (!id_payment_type) errores.push("Método de Pago");
      if (!id_payment_shape) errores.push("Forma de Pago");
      if (!series) errores.push("Serie");
      if (!number_serial) errores.push("Número de Serie");
      if (!expiration_date) errores.push("Fecha");
      
      if (errores.length > 0) {
          console.log("Campos faltantes:", errores);
          Swal.fire({
              icon: "warning",
              title: "Campos incompletos",
              text: "Falta llenar: " + errores.join(", ")
          });
          return;
      }

      let purchase_total = 0;
$("#detalle_productos_cuerpo .row-subtotal").each(function () {
    purchase_total += parseFloat($(this).attr("data-value")) || 0;
});

let formData = {
    id_client: id_client,
    id_voucher_type: id_voucher_type,
    id_payment_type: id_payment_type,
    id_payment_shape: id_payment_shape,
    series: series,
    number_serial: number_serial,
    expiration_date: expiration_date,
    purchase_total: purchase_total
};

      console.log("Datos a enviar (Maestro):", formData);

      $.ajax({
          url: BASE_URL + "Income_Products/create_income_products",
          type: "POST",
          data: JSON.stringify(formData),
          contentType: "application/json",
          dataType: "json",
          success: function (response) {
              console.log("Respuesta POST 1:", response);
              if (response.status === "OK") {
                  let insertedId = response.id;
                  console.log("Ingreso creado con ID:", insertedId);
                  enviarProductos(insertedId);
              } else {
                  Swal.fire({
                      icon: "error",
                      title: "Error",
                      text: response.msg || "No se pudo crear el ingreso."
                  });
              }
          },
          error: function (xhr, status, error) {
              console.error("Error en POST 1:", error);
              console.error("Respuesta del servidor:", xhr.responseText);
              Swal.fire({
                  icon: "error",
                  title: "Error de conexión",
                  text: "Hubo un error al intentar guardar el ingreso."
              });
          }
      });
  });
});

function enviarProductos(idIngreso) {
  let productos = [];

  $("#detalle_productos_cuerpo tr").each(function () {
    let productId = $(this).attr("data-product-id");
    let productCode = $(this).find('.product-code').attr("data-code") || '';
    let stock = parseInt($(this).find('input[name="stock[]"]').val()) || 0;
    let purchasePrice = parseFloat($(this).find('input[name="price[]"]').val()) || 0;
    let sellingPrice = parseFloat($(this).find('input[name="selling_price[]"]').val()) || 0;
    let expirationDate = $(this).find('input[name="product_expiration_date[]"]').val() || null;
    let subtotal = parseFloat($(this).find(".row-subtotal").attr("data-value")) || 0;

    console.log("Producto encontrado:", {
        productId, productCode, stock, purchasePrice, sellingPrice, expirationDate, subtotal
    });

    if (productId && stock > 0 && purchasePrice > 0) {
        productos.push({
            id_income_products: idIngreso,
            id_product: productId,
            product_code: productCode,
            product_expiration_date: expirationDate,
            quantity: stock,
            full_purchase: purchasePrice,
            selling_price: sellingPrice,
            subtotal: subtotal
        });
    }
  });

  console.log("Array de productos a enviar:", productos);

  if (productos.length > 0) {
      $.ajax({
          url: BASE_URL + "Income_Products/create_income_products_details",
          type: "POST",
          data: JSON.stringify({ productos: productos }),
          contentType: "application/json",
          dataType: "json",
          success: function (response) {
              console.log("Respuesta POST 2:", response);
              if (response.status === "OK") {
                  Swal.fire({
                      icon: "success",
                      title: "Compra registrada",
                      text: "Los productos han sido guardados exitosamente."
                  }).then(() => {
                      window.location.href = BASE_URL + "Income_Products/index.php";
                  });
              } else {
                  Swal.fire({
                      icon: "error",
                      title: "Error",
                      text: response.msg || "Error al guardar los productos."
                  });
              }
          },
          error: function (xhr, status, error) {
              console.error("Error en POST 2:", error);
              console.error("Respuesta del servidor:", xhr.responseText);
              Swal.fire({
                  icon: "error",
                  title: "Error de conexión",
                  text: "Hubo un error al intentar guardar los productos. Ver consola."
              });
          }
      });
  } else {
      Swal.fire({
          icon: "warning",
          title: "Productos inválidos",
          text: "Verifique que todos los productos tengan cantidad y precio válidos."
      });
  }
}

$(document).on("click", ".btn-delete-product", function () {
  // Delete the row corresponding to the button "x" clicked
  $(this).closest("tr").remove();
  actualizarTotalGeneral();
});

load_datatable_income();
load_datatable_income_products();
get_payment_type();
get_payment_shape();
get_name();
get_voucher_type();

function destroy_datatable_income() {
  $("#datatables-income").dataTable().fnDestroy();
}
function refresh_datatable_income() {
  $("#datatables-income").DataTable().ajax.reload();
}
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
      { data: "product_description" },
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
function get_name() {
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
        $("#update_income_products_details_form :input[name=name]").html(html);
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
    beforeSend: function () {
      
    },
    success: function (data) {
      if (data.status === "OK") {
        var html = '<option value="">Seleccionar</option>';
        data.data.forEach((element) => {
          html +=
            '<option value="' +
            element.id +
            '">' +
            element.description +
            "</option>";
        });
        $("#update_income_products_details_form :input[name=vt_description]").html(html);
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
    beforeSend: function () {  
    },
    success: function (data) {
      if (data.status === "OK") {
        var html = '<option value="">Seleccionar</option>';
        data.data.forEach((element) => {
          html +=
            '<option value="' +
            element.id +
            '">' +
            element.description +
            "</option>";
        });
        $("#update_income_products_details_form :input[name=pt_description]").html(html);
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
      beforeSend: function () {
      },
      success: function (data) {
          if (data.status === "OK") {
              var html = '<option value="">Seleccionar</option>';
              data.data.forEach((element) => {
                  html += '<option value="' + element.id + '">' + element.description + "</option>";
              });
              $("#update_income_products_details_form :input[name=pm_description]").html(html);
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
  let value = $(this).attr("data-process-key");
  let params = { id_product: value };
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
          let quantityInput = productRow.find(".product-quantity");
          let newQuantity = parseInt(quantityInput.val()) + 1;
          quantityInput.val(newQuantity);
          calcularSubtotal(quantityInput); // Recalcular subtotal
        } else {
          $("#add_products").append(getHtml(item));
          calcularSubtotal();
        }
      }
    },
  });
});
 //---------------------------------ESTO ES PARA INGRESAR EL AGREGAR PRODUCTO--------------------------------
function getHtml(item) {
  var stock = 1;
  var purchase_price = parseFloat(item.product_price) || 0;;
  var subtotal = stock * purchase_price;
  var soles = subtotal.toLocaleString("es-PE", {
    style: "currency",
    currency: "PEN",
  });
  return ` <tr class="text-center" data-product-id="${item.id_product}">
    <td><button class="btn btn-danger btn-delete-product"><i class="fa fa-x"></i></button></td>
    <td class="product-name">${item.product_name}</td>
<td style="width:85px; text-align:center;">
    <input type="number" name="stock[]" value="${stock}"
    class="form-control product-quantity text-center"
    style="max-width:70px; padding:3px;"
    oninput="calcularSubtotal(this)">
</td>
    <td class="purchase-price product-price" data-value="${purchase_price}">${purchase_price}</td> 
    <td><span class="subtotal product-subtotal" data-value="${subtotal}">${soles}</span></td>
    </tr>`;
}
//----------------------------------------------
function calcularSubtotal(element) {
  var row = $(element).closest("tr");
  var stock = parseInt(row.find('input[name="stock[]"]').val()) || 0;
  var purchasePrice = parseFloat(row.find(".purchase-price").attr("data-value")) || 0;
  var subtotal = stock * purchasePrice;

  row.find("span.subtotal").text("S/" + subtotal.toFixed(2));
  row.find("span.subtotal").attr("data-value", subtotal.toFixed(2));

}
//------------------------------------------------------------------------------------
function eliminarProductos(idIngreso, callback) {
    //console.log("Eliminando productos antiguos para el ID de ingreso:", idIngreso); 
    $.ajax({
        url: BASE_URL + "Income_Products/delete_income_products_details",
        type: "POST",
        data: JSON.stringify({ id_income_products: idIngreso }),
        contentType: "application/json",
        dataType: "json",
        success: function (response) {
            //console.log("Respuesta de eliminación de productos:", response); 
            if (response.status === "OK") {
                //console.log("Productos antiguos eliminados.");
                callback(); 
            } else {
                alert("Error al eliminar los productos antiguos.");
            }
        },
        error: function (xhr, status, error) {
            console.error("Error al eliminar productos:", error);
        }
    });
}

$(document).ready(function () {

  function llenarSelect(selectName, textValue) {
    let $select = $("#update_income_products_details_form select[name='" + selectName + "']");
    if ($select.length > 0) {
        let found = false;
        $select.find("option").each(function () {
            if ($(this).text().trim() === textValue.trim()) {
                $(this).prop("selected", true).trigger("change");
                found = true;
            }
        });
        if (!found) {  
            $select.append(new Option(textValue, textValue, true, true)).trigger("change");
        }
    } else {
        console.warn("Select no encontrado:", selectName);
    }
  }

  function cargarDatos(id_income_products) {
    $.ajax({
        url: BASE_URL + "Income_Products/income_products_details",
        type: "GET",
        data: { id_income_products: id_income_products },
        dataType: "json",
        success: function (response) {
            if (response.status === "OK") {
                let data = response.result;

                $("#update_income_products_details_form :input[name='series']").val(data.series);
                $("#update_income_products_details_form :input[name='number_serial']").val(data.number_serial);
                $("#update_income_products_details_form :input[name='expiration_date']").val(data.expiration_date);
                llenarSelect("name", data.client_name);
                llenarSelect("vt_description", data.voucher_type);
                llenarSelect("pt_description", data.payment_type);
                llenarSelect("pm_description", data.payment_shape);

            
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.error("Error en la petición AJAX:", textStatus, errorThrown);
            alert("Ocurrió un error al obtener los detalles.");
        }
    });
  }

  $("#btn_update_product").click(function (event) {
      event.preventDefault();

      let urlParams = new URLSearchParams(window.location.search);
      let id_income = urlParams.get('id');
     // console.log("ID de la compra a actualizar:", id_income);

      if (!id_income) {
          console.error("El valor de id_income no está disponible.");
          return;
      }
    
          let formData = {
          id_income_product: id_income,
          id_client: $("select[name='name']").val(),
          id_voucher_type: $("select[name='vt_description']").val(),
          id_payment_type: $("select[name='pt_description']").val(),
          id_payment_shape: $("select[name='pm_description']").val(),
          series: $("input[name='series']").val(),
          number_serial: $("input[name='number_serial']").val(),
          expiration_date: $("input[name='expiration_date']").val(),
          products: []
      };
      cargarDatos();
      $("#add_products tr").each(function () {
          let productId = $(this).attr("data-product-id");
          let stock = parseInt($(this).find('input[name="stock[]"]').val()) || 0;
          let purchasePrice = parseFloat($(this).find(".purchase-price").attr("data-value")) || 0;
          let subtotal = parseFloat($(this).find(".subtotal").attr("data-value")) || 0;

          if (productId && stock > 0 && purchasePrice > 0 && subtotal > 0) {
              formData.products.push({
                  id_product: productId,
                  quantity: stock,
                  subtotal: subtotal,
                  full_purchase: purchasePrice
              });
          }
      });
      if (formData.series === "" || formData.number_serial === "" ) {
        Swal.fire({
            icon: "error",
            title: "Campos Obligatorios",
            text: "Rellene todos los campos del formulario"
        })
        return;
      }
      if (formData.products.length === 0) {
        Swal.fire({
          icon: "error",
          title: "Campos Obligatorios",
          text: "Debe agregar al menos un producto para actualizar la compra."
       });
        return;
     }
    
      $.ajax({
          url: BASE_URL + "Income_Products/update_income_product",
          type: "POST",
          data: JSON.stringify(formData),
          contentType: "application/json",
          dataType: "json",
          success: function (response) {

              if (response.status === "OK") {
                Swal.fire({
                  icon: "success",
                  title: "Compra Actualizada",
                  text: "Todos los registros han sido actualizados correctamente"
              }).then(() => {
                  window.location.href = BASE_URL + "Income_Products/index.php";
              });
              } else {
                Swal.fire({
                  icon: "error",
                  title: "Error",
                  text: response.msg
              });
              if (response.data && response.data.error_log) {
                  console.error(" Error en el servidor:", response.data.error_log);
              }
              }
          },
          error: function (xhr, status, error) {
              console.error("Error en la petición AJAX de actualización:", error);
          }
      });
  });

//Para cargar los datos
  let urlParams = new URLSearchParams(window.location.search);
      let id_income = urlParams.get('id');
      if (id_income) {
          cargarDatos(id_income);
      }
});

$(document).on("click", ".btn-delete-product", function () {
  // Delete the row corresponding to the button "x" clicked
  $(this).closest("tr").remove();
});

load_datatable_income();
load_datatable_income_products();
get_payment_type();
get_payment_shape();
get_name();
get_voucher_type();

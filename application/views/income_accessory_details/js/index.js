// -- Functions
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
    language: {
      url: BASE_URL + "public/assets/json/languaje-es.json",
    },
  });

  dataTable.on("xhr", function () {
    var data = dataTable.ajax.json();
    functions.toast_message(data.type, data.msg, data.status);
  });
}

function destroy_datatable_income_accessories() {
  $("#datatables-income-accessories").dataTable().fnDestroy();
}

function refresh_datatable_income_accessories() {
  $("#datatables-income-accessories").DataTable().ajax.reload();
}

function load_datatable_income_accessories() {
  destroy_datatable_income_accessories();
  let dataTable = $("#datatables-income-accessories").DataTable({
    ajax: {
      url: BASE_URL + "Accessories/get_accessories",
      cache: false,
    },
    columns: [
      {
        class: "center",
        render: function (data, type, row) {
          return (
            '<button class="btn btn-sm btn-info btn-round btn-icon btn_add" data-process-key="' +
            row.id_accessory +
            '">' +
            feather.icons["plus"].toSvg({ class: "font-small-4" }) +
            "</button>"
          );
        },
      },
      { data: "id_accessory" },
      { data: "accessory_description" },
      { data: "accessory_price" },
      { data: "accessory_stock" },
    ],
    language: {
      url: BASE_URL + "public/assets/json/languaje-es.json",
    },
  });

  dataTable.on("xhr", function () {
    var data = dataTable.ajax.json();
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
    success: function (data) {
      if (data.status === "OK") {
        var html = '<option value="">Seleccionar</option>';
        data.data.forEach((element) => {
          html +=
            '<option value="' +
            element.id +
            '">' +
            element.name +
            "</option>";
        });
        $("#create_income_accessory_details_form :input[name=name]").html(html); 
        
        checkEditMode();
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
          html +=
            '<option value="' +
            element.id +
            '">' +
            element.description +
            "</option>";
        });
        $("#create_income_accessory_details_form :input[name=vt_description]").html(html);
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
          html +=
            '<option value="' +
            element.id +
            '">' +
            element.description +
            "</option>";
        });
        $("#create_income_accessory_details_form :input[name=pt_description]").html(html);
      }
    },
  });
}

// --- Aumentar Cantidad ---
$(document).on("click", ".btn_add", function () {
  let value = $(this).attr("data-process-key");
  let params = { id_accessory: value };
  
  $.ajax({
    url: BASE_URL + "accessories/get_accessories_by_id",
    type: "GET",
    data: params,
    dataType: "json",
    contentType: false,
    processData: true,
    cache: false,
    success: function (data) {
      if (data.status === "OK" && data.data.length > 0) {
        let item = data.data[0];
        let tableBody = $("#table_body"); // CORREGIDO: Usar el tbody
        let productRow = $(`tr[data-accessory-id="${item.id_accessory}"]`);
        
        if (productRow.length > 0) {
            let quantityInput = productRow.find('input[name="stock"]');
            let newQuantity = parseInt(quantityInput.val()) + 1;
            quantityInput.val(newQuantity);
            calcularSubtotal(quantityInput[0]);
        } else {
            tableBody.append(getHtml(item)); // CORREGIDO: Agregar al tbody
            calcularSubtotal();
        }
      } else {
        console.warn("No se encontró el accesorio.");
      }
    },
  });
});

function getHtml(item) {
  var purchase_price = parseFloat(item.accessory_price) || 0; 
  var stock = 1;
  var subtotal = stock * purchase_price;
  var soles = subtotal.toLocaleString("es-PE", { style: "currency", currency: "PEN" });

  return `<tr class="text-center" data-accessory-id="${item.id_accessory}">
        <td><button class="btn btn-danger btn-delete-accessories"><i class="fa fa-x"></i></button></td>
        <input type="hidden" name="id_accessory" value="${item.id_accessory}">
        <td>${item.accessory_description}</td> 
        <td><input type="number" name="stock" value="${stock}" min="1" step="1" class="form-control" oninput="calcularSubtotal(this)"></td>
        <td><input type="number" step="any" name="purchase_price" value="${purchase_price.toFixed(2)}" min="0" class="form-control" oninput="calcularSubtotal(this)"></td>
        <td><span class="subtotal" data-value="${subtotal}">${soles}</span></td>
    </tr>`;
}

function calcularSubtotal(element) {
  var row = $(element).closest("tr");
  var stock = parseInt(row.find('input[name="stock"]').val()) || 0;
  var purchasePrice = parseFloat(row.find('input[name="purchase_price"]').val()) || 0;
  var subtotal = stock * purchasePrice;

  row.find("span.subtotal").text("S/ " + subtotal.toFixed(2));
  row.find("span.subtotal").attr("data-value", subtotal.toFixed(2));
}

$(document).on("click", ".btn-delete-accessories", function () {
  $(this).closest("tr").remove();
});

// --- GUARDADO ---
$(document).ready(function () {
  $("#btn_guardar_accessory").click(function (event) {
    event.preventDefault();

    let idEdit = $('#id_income_accessory').val(); 
    
    let isUpdate = (idEdit && idEdit !== '0' && idEdit !== '');
    let urlMaster = isUpdate
        ? BASE_URL + "Income_Accessory_Details/update_income_accessory" 
        : BASE_URL + "Income_Accessory_Details/create_income_accessory";

    let formData = {
      master: { 
        id_income_accessory: isUpdate ? idEdit : null,
        id_client: $("select[name='name']").val(),
        id_voucher_type: $("select[name='vt_description']").val(),
        id_payment_type: $("select[name='pt_description']").val(),
        proof_series: $("input[name='proof_series']").val(),
        voucher_series: $("input[name='voucher_series']").val(),
        date: $("input[name='proof_date']").val(),
      },
      details: []
    };

    // CORREGIDO: Leer desde #table_body
    $("#table_body tr").each(function () { 
        let row = $(this);
        let id_accessory = row.find('input[name="id_accessory"]').val();
        if (id_accessory) {
            formData.details.push({
                id_accessory: id_accessory,
                stock: parseInt(row.find('input[name="stock"]').val()) || 0,
                purchase_price: parseFloat(row.find('input[name="purchase_price"]').val()) || 0,
                sale_price: 0, 
                serie: ''      
            });
        }
    });
    
     if (!formData.master.id_client || !formData.master.date) {
        Swal.fire({ icon: "error", title: "Error", text: "Complete los campos obligatorios."});
        return;
    }
    
    if (formData.details.length === 0) {
        Swal.fire({ icon: "error", title: "Error", text: "Debe agregar al menos un accesorio."});
        return;
    }

    $.ajax({
      url: urlMaster,
      type: "POST",
      data: JSON.stringify(formData),
      contentType: "application/json",
      dataType: "json",
      success: function (response) {
        if (response.status === "OK") {
          Swal.fire({
            icon: "success",
            title: "Éxito",
            text: response.msg || "Operación realizada correctamente.",
          }).then(() => {
            window.location.href = BASE_URL + "Income_Accessory";
          });
        } else {
          Swal.fire({ icon: "error", title: "Error", text: response.msg });
        }
      },
      error: function (xhr, status, error) {
        console.error("Error AJAX:", error, xhr.responseText);
        Swal.fire({ icon: "error", title: "Error de conexión", text: "Ver consola: " + xhr.responseText });
      },
    });
  });
});

// --- LÓGICA DE EDICIÓN (CARGAR DATOS) ---
function checkEditMode() {
    let idToEdit = $('#id_income_accessory').val(); 
    
    if (idToEdit && idToEdit != '0') {
        console.log("Modo Edición. ID:", idToEdit);
        $('#btn_guardar_accessory').text('Actualizar Ingreso');

        $.ajax({
            url: BASE_URL + 'Income_Accessory_Details/get_data_for_edit',
            type: 'POST',
            data: { id: idToEdit },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'OK') {
                    let m = response.data.master;
                    let d = response.data.details;

                    $('select[name="name"]').val(m.id_client).trigger('change'); 
                    $('select[name="vt_description"]').val(m.id_voucher_type).trigger('change');
                    $('select[name="pt_description"]').val(m.id_payment_type).trigger('change');
                    
                    $('input[name="proof_series"]').val(m.proof_series);
                    $('input[name="voucher_series"]').val(m.voucher_series);
                    $('input[name="proof_date"]').val(m.date);

                    // CORREGIDO: Limpiar solo el cuerpo de la tabla
                    $('#table_body').html(''); 
                    
                    d.forEach(item => {
                        let rowHtml = getHtml({
                            id_accessory: item.id_accessory,
                            accessory_description: item.accessory_description,
                            accessory_price: item.purchase_price 
                        });
                        let $row = $(rowHtml);
                        $row.find('input[name="stock"]').val(item.stock);
                        $row.find('input[name="purchase_price"]').val(item.purchase_price);
                        
                        // CORREGIDO: Agregar al cuerpo de la tabla
                        $('#table_body').append($row);
                        calcularSubtotal($row.find('input[name="stock"]')[0]); 
                    });
                }
            }
        });
    }
}

// --- Carga inicial ---
load_datatable_income();
load_datatable_income_accessories();
get_payment_type();
get_voucher_type();
get_name();
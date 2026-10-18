// ===================== LISTADO DATATABLE =====================

function destroy_datatable() {
    $("#datatable-income-accessory").dataTable().fnDestroy();
}

function refresh_datatable() {
    $("#datatable-income-accessory").DataTable().ajax.reload();
}

function load_datatable() {
    destroy_datatable();

    let dataTable = $("#datatable-income-accessory").DataTable({
        order: [[0, "desc"]],
        ajax: {
            url: BASE_URL + "Income_Accessory/get_income_accessory",
            cache: false,
            dataSrc: function (json) {
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

                    buttons += `<button class="btn btn-sm btn-primary btn_details" data-process-key="${row.id_income_accessory}">
                        ${feather.icons["eye"].toSvg({ class: "font-small-2" })}
                    </button> `;

                    buttons += `<button class="btn btn-sm btn-danger btn_delete_custom" data-process-key="${row.id_income_accessory}">
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
        buttons: functions.custom_buttons_datatable([7], "#create_income_accessory_modal"),
        language: { url: BASE_URL + "public/assets/json/languaje-es.json" },
    });

    dataTable.on("xhr", function () {
        let data = dataTable.ajax.json();
    });
}

// ===================== MODAL VER DETALLE =====================

$("#datatable-income-accessory").on("click", ".btn_details", function () {
    var id_income_accessory = $(this).data("process-key");

    $.ajax({
        url: BASE_URL + "Income_Accessory/income_accessory_details",
        type: "GET",
        data: { id_income_accessory: id_income_accessory },
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

                $("#incomeAccessoryDetails").empty();

                data.accesorios.forEach(function (item) {
                    var cantidad = parseInt(item.quantity) || 0;
                    var precio   = parseFloat(item.full_purchase) || 0;
                    var precioV  = parseFloat(item.selling_price || 0);
                    var subtotal = parseFloat(item.subtotal) || 0;

                    var row = `
                    <tr>
                        <td>${item.accessory_name}</td>
                        <td class="text-center">${cantidad}</td>
                        <td class="text-end">S/ ${precio.toFixed(2)}</td>
                        <td class="text-end">S/ ${precioV.toFixed(2)}</td>
                        <td class="text-end">S/ ${subtotal.toFixed(2)}</td>
                    </tr>`;
                    $("#incomeAccessoryDetails").append(row);
                });

                $("#incomeAccessoryModal").modal("show");
            } else {
                Swal.fire("Error", "No se encontraron detalles para este ingreso.", "error");
            }
        },
        error: function () {
            Swal.fire("Error", "Ocurrió un error al obtener los detalles.", "error");
        },
    });
});

// ===================== DELETE =====================

$("#datatable-income-accessory").on("click", ".btn_delete_custom", function () {
    var id_income_accessory = $(this).data("process-key");

    if (id_income_accessory) {
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
                    url: BASE_URL + "Income_Accessory/delete_income_accessory",
                    type: "POST",
                    data: { id_income_accessory: id_income_accessory },
                    success: function (response) {
                        if (response.status === "OK") {
                            Swal.fire("¡Eliminado!", "El registro ha sido eliminado.", "success");
                            refresh_datatable();
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
                $("#create_income_accessory_details_form :input[name=name]").html(html);
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
                    html += `<option value="${element.id}">${element.description}</option>`;
                });
                $("#create_income_accessory_details_form :input[name=pt_description]").html(html);
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
                $("#create_income_accessory_details_form :input[name=pm_description]").html(html);
            }
        },
        error: function (xhr, status, error) {
            console.error("Error al obtener formas de pago: " + error);
        },
    });
}

// ===================== DATATABLE ACCESORIOS (modal selección) =====================

function destroy_datatable_select_accessories() {
    if ($.fn.dataTable.isDataTable("#datatables-select-accessories")) {
        $("#datatables-select-accessories").dataTable().fnDestroy();
    }
}

function load_datatable_select_accessories() {
    destroy_datatable_select_accessories();

    $("#datatables-select-accessories").DataTable({
        ajax: {
            url: BASE_URL + "Accessories/get_accessories",
            cache: false,
            dataSrc: function (json) {
                return json.data;
            },
        },
        columns: [
            {
                class: "center",
                orderable: false,
                render: function (data, type, row) {
                    return (
                        '<button type="button" class="btn btn-sm btn-info btn-round btn-icon btn_add_accessory" data-id="' +
                        row.id_accessory + '" data-name="' + row.accessory_description +
                        '" data-price="' + row.accessory_price + '">' +
                        feather.icons["plus"].toSvg({ class: "font-small-4" }) +
                        "</button>"
                    );
                },
            },
            { data: "id_accessory", defaultContent: "" },
            { data: "accessory_description", defaultContent: "" },
            { data: "accessory_price", defaultContent: "0.00" },
        ],
        language: { url: BASE_URL + "public/assets/json/languaje-es.json" },
    });
}

// ===================== AGREGAR ACCESORIO A LA TABLA =====================
// IMPORTANTE: delegación en document para que funcione con DataTables paginado

$(document).on("click", ".btn_add_accessory", function () {
    let id_accessory   = $(this).data("id");
    let code           = $(this).data("code");
    let name           = $(this).data("name");
    let price          = parseFloat($(this).data("price")) || 0;

    let accesorioRow = $(`tr[data-accessory-id="${id_accessory}"]`);

    if (accesorioRow.length > 0) {
        // Ya existe: incrementar cantidad
        let quantityInput = accesorioRow.find('input[name="stock[]"]');
        let newQuantity   = parseInt(quantityInput.val()) + 1;
        quantityInput.val(newQuantity);
        calcularSubtotal(quantityInput[0]);
    } else {
        // No existe: agregar nueva fila
        let item = {
            id_accessory:          id_accessory,
            accessory_description: name,
            accessory_price:       price,
        };
        $("#accessory_detail_body").append(getHtml(item));
        actualizarTotalGeneral();
    }

    $("#select_accessory_modal").modal("hide");
});

function getHtml(item) {
    var stock          = 1;
    var purchase_price = parseFloat(item.accessory_price) || 0;
    var accessory_id   = item.id_accessory || "";
    var accessory_name = item.accessory_description || "";

    return `
    <tr class="accessory-row" data-accessory-id="${accessory_id}">
        <td>
        <button type="button" class="btn btn-danger btn-delete-accessories btn-sm">x</button>
        <input type="hidden" name="id_accessory" value="${accessory_id}">
        </td>
        <td>${accessory_id}</td>
        <td>${accessory_name}</td>
        <td>
            <input type="number" name="stock[]" value="${stock}"
                   class="form-control form-control-sm input-quantity"
                   oninput="calcularSubtotal(this)" min="1">
        </td>
        <td>
            <input type="number" name="purchase_price[]" value="${purchase_price.toFixed(2)}"
                   class="form-control form-control-sm input-price"
                   oninput="calcularSubtotal(this)" step="0.01">
        </td>
        <td>
            <input type="number" name="sale_price[]" value="" min="0"
                   class="form-control form-control-sm" placeholder="0.00">
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
    $("#grand_total").text(granTotal.toFixed(2));
}

$(document).on("click", ".btn-delete-accessory", function () {
    $(this).closest("tr").remove();
    actualizarTotalGeneral();
});

// ===================== LIMPIAR MODAL AL CERRAR =====================

$("#create_income_accessory_modal").on("hidden.bs.modal", function () {
    $("#create_income_accessory_details_form")[0].reset();
    $("#accessory_detail_body").empty();
    $("#grand_total").text("0.00");
});

// ===================== GUARDAR NUEVO INGRESO =====================

$(document).ready(function () {

    $("#btn_save_accessory").click(function (event) {
        event.preventDefault();

        let rowCount = $("#accessory_detail_body tr").length;

        if (rowCount === 0) {
            Swal.fire({ icon: "warning", title: "No hay accesorios", text: "Debe agregar al menos un accesorio." });
            return;
        }

        let id_client        = $("#create_income_accessory_details_form select[name='name']").val();
        let id_voucher_type  = $("#create_income_accessory_details_form select[name='vt_description']").val();
        let id_payment_type  = $("#create_income_accessory_details_form select[name='pt_description']").val();
        let id_payment_shape = $("#create_income_accessory_details_form select[name='pm_description']").val();
        let series           = $("#create_income_accessory_details_form input[name='series']").val();
        let number_serial    = $("#create_income_accessory_details_form input[name='number_serial']").val();
        let data_time        = $("#create_income_accessory_details_form input[name='data_time']").val();

        let errores = [];
        if (!id_client)        errores.push("Proveedor");
        if (!id_voucher_type)  errores.push("Tipo de Comprobante");
        if (!id_payment_type)  errores.push("Método de Pago");
        if (!id_payment_shape) errores.push("Forma de Pago");
        if (!series)           errores.push("Serie");
        if (!number_serial)    errores.push("Número de Serie");
        if (!data_time)        errores.push("Fecha de Compra");

        if (errores.length > 0) {
            Swal.fire({ icon: "warning", title: "Campos incompletos", text: "Falta llenar: " + errores.join(", ") });
            return;
        }

        let purchase_total = 0;
        $("#accessory_detail_body .row-subtotal").each(function () {
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
            data_time:        data_time,
        };

        $.ajax({
            url: BASE_URL + "Income_Accessory/create_income_accessory",
            type: "POST",
            data: JSON.stringify(formData),
            contentType: "application/json",
            dataType: "json",
            success: function (response) {
                if (response.status === "OK") {
                    enviarAccesorios(response.id);
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

function enviarAccesorios(idIngreso) {
    let accesorios = [];

    $("#accessory_detail_body tr").each(function () {
        let id_accessory  = $(this).find('input[name="id_accessory"]').val();
        let stock         = parseInt($(this).find('input[name="stock[]"]').val()) || 0;
        let purchasePrice = parseFloat($(this).find('input[name="purchase_price[]"]').val()) || 0;
        let sellingPrice  = parseFloat($(this).find('input[name="sale_price[]"]').val()) || 0;
        let subtotal      = parseFloat($(this).find(".row-subtotal").attr("data-value")) || 0;

        if (id_accessory && stock > 0 && purchasePrice > 0) {
            accesorios.push({
                id_income_accessory: idIngreso,
                id_accessory:        id_accessory,
                quantity:            stock,
                full_purchase:       purchasePrice,
                selling_price:       sellingPrice,
                subtotal:            subtotal,
            });
        }
    });

    if (accesorios.length > 0) {
        $.ajax({
            url: BASE_URL + "Income_Accessory/create_income_accessory_details",
            type: "POST",
            data: JSON.stringify({ accesorios: accesorios }),
            contentType: "application/json",
            dataType: "json",
            success: function (response) {
                if (response.status === "OK") {
                    Swal.fire({ icon: "success", title: "Compra registrada", text: "Los accesorios han sido guardados exitosamente." })
                        .then(() => {
                            $("#create_income_accessory_modal").modal("hide");
                            refresh_datatable();
                        });
                } else {
                    Swal.fire({ icon: "error", title: "Error", text: response.msg || "Error al guardar los accesorios." });
                }
            },
            error: function () {
                Swal.fire({ icon: "error", title: "Error de conexión", text: "Hubo un error al intentar guardar los accesorios." });
            },
        });
    } else {
        Swal.fire({ icon: "warning", title: "Accesorios inválidos", text: "Verifique que todos los accesorios tengan cantidad y precio válidos." });
    }
}

// ===================== CARGAR AL ABRIR MODALES =====================

$("#create_income_accessory_modal").on("show.bs.modal", function () {
    get_name();
    get_voucher_type();
    get_payment_type();
    get_payment_shape();
});

$("#select_accessory_modal").on("shown.bs.modal", function () {
    load_datatable_select_accessories();
});

// ===================== INIT =====================

load_datatable();


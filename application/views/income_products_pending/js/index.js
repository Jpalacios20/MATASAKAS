// ------------------- DESTROY DATATABLE -------------------
function destroy_datatable() {
// --
$("#datatable-income-products-pending").dataTable().fnDestroy();
}

// ------------------- REFRESH DATATABLE -------------------
function refresh_datatable() {
// --
$("#datatable-income-products-pending").DataTable().ajax.reload();
}

// ------------------- LISTADO DATATABLE -------------------

let cambiosPendientes = {};

function load_datatable() {
    destroy_datatable();

    let dataTable = $("#datatable-income-products-pending").DataTable({
        ajax: {
            url: BASE_URL + "Income_Products_Pending/get_income_products_pending",
            cache: false,
            dataSrc: "data",
            error: function () {
                alert("Error al cargar datos.");
            },
        },
        columns: [
            { data: "id_income_products" },
            { data: "name" },
            { data: "sale_date" },
            { data: "voucher_type_description" },
            { data: "payment_type_description" },
            { data: "total_purchase" },
            { 
                data: "status",
                title: "Estado",
                class: "text-center",
                render: function (data, type, row) {
                    let estado = cambiosPendientes[row.id_income_products] ?? row.status;
                    let badgeClass = estado == "1" ? "badge-light-warning" : "badge-light-success";
                    let badgeText = estado == "1" ? "Pendiente" : "Activo";

                    return `<span class="badge rounded-pill ${badgeClass}" id="estado-${row.id_income_products}">${badgeText}</span>`;
                }
            },
            { 
                title: "Acciones",
                class: "text-center",
                render: function (data, type, row) {
                    let estado = cambiosPendientes[row.id_income_products] ?? row.status;
                    let checked = estado == "2" ? "checked" : "";  
                    let switchColor = estado == "2" ? "bg-success" : "bg-warning"; 

                    return `
                        <div class="d-flex align-items-center justify-content-center">
                            <div class="form-check form-switch">
                                <input class="form-check-input switch-status ${switchColor}" type="checkbox" ${checked} data-id="${row.id_income_products}">
                            </div>
                            <button class="btn btn-sm btn-primary btn_details ms-2" data-process-key="${row.id_income_products}">
                                ${feather.icons["eye"].toSvg({ class: "font-small-3" })}
                            </button>
                        </div>`;
                }
            }
        ],
        dom: functions.head_datatable(),
        buttons: [],
        language: { url: BASE_URL + "public/assets/json/languaje-es.json" }
    });

    // Evento para actualizar el estado cuando se cambia el switch
    $(document).on("change", ".switch-status", function () {
        let id = $(this).data("id");
        let isChecked = $(this).prop("checked");

        // Actualizar cambiosPendientes
        cambiosPendientes[id] = isChecked ? "2" : "1"; 

        // Actualizar visualmente la etiqueta de estado
        let estadoBadge = $(`#estado-${id}`);
        let switchInput = $(this);

        if (isChecked) {
            estadoBadge.removeClass("badge-light-warning").addClass("badge-light-success").text("Activo");
            switchInput.removeClass("bg-warning").addClass("bg-success");
        } else {
            estadoBadge.removeClass("badge-light-success").addClass("badge-light-warning").text("Pendiente");
            switchInput.removeClass("bg-success").addClass("bg-warning");
        }
    });

    $(document).on("click", "#register_active", function () {
        if (Object.keys(cambiosPendientes).length === 0) {
            alert("No hay cambios pendientes.");
            return;
        }
    });    
}

// --- status: 'Active'---
$(document).on("click", "#register_active", function () {
    if (Object.keys(cambiosPendientes).length === 0) {
        alert("No hay cambios pendientes.");
        return;
    }

    // Filtrar los IDs que han cambiado a estado "Activo" (estado "2")
    let registrosActivos = Object.keys(cambiosPendientes).filter(id => cambiosPendientes[id] === "2");

    if (registrosActivos.length === 0) {
        alert("No hay registros activados.");
        return;
    }

    //console.log("Registros cambiados a 'Activo':", registrosActivos);

    $.ajax({
        url: BASE_URL + "Income_Products_Pending/update_income_products_status", // Ruta del controlador
        type: "POST",
        data: { registros: registrosActivos }, // Enviar array de IDs
        dataType: "json",
        success: function(response){
            if (response.status === "OK") {
                Swal.fire({
                    icon: "success",
                    title: "Estados Actualizado",
                    text: "Los registros han cambiado de estado correctamente."
                });
                cambiosPendientes = {}; // Limpiar cambios pendientes
                load_datatable(); 
            }else{
                alert("Error: " + response.result);
            }
        },
        error: function (xhr, status, error) {
            console.error("Error en la solicitud AJAX:", error);
            alert("Hubo un error en la actualización.");
        }
    });
});



    // ------------------- DETAILS -------------------
$("#datatables-income-products-pending").on("click", ".btn_details", function () {
    var id_income_products = $(this).data("process-key");

    $.ajax({
        url: BASE_URL + "Income_Products/income_products_details",
        type: "GET",
        data: { id_income_products: id_income_products },
        dataType: "json",
        success: function (response) {
            if (response.status === "OK") {
                var data = response.result;

                // Llenar los campos del formulario
                $("#name_client").val(data.client_name);
                $("#sale_date").val(data.sale_date || "Fecha no disponible");
                $("#voucher_type").val(data.voucher_type);
                $("#payment_type").val(data.payment_type);
                $("#series").val(data.series);
                $("#number_serial").val(data.number_serial);
                $("#expiration_date").val(data.expiration_date);
                $("#payment_shape").val(data.payment_shape);

                // Limpiar la tabla antes de agregar nuevos datos
                $("#incomeProductDetails").empty();
                var total = 0;

                // Agregar los productos a la tabla
                data.products.forEach(function (product) {
                    // Obtener directamente el subtotal de la base de datos
                    var cantidad = parseInt(product.quantity) || 0;  // Asegurarse que es un número
                    var precio = parseFloat(product.full_purchase) || 0; // Asegurarse que es un número
                    var precioTotal = parseFloat(product.subtotal) || 0; // Usar el valor del subtotal directamente

                    console.log(`Producto: ${product.product_name}, Cantidad: ${cantidad}, Precio: ${precio}, Subtotal: ${precioTotal}`);

                    total += precioTotal; // Sumar al total general

                    var row = `
                        <tr>
                            <td>${product.product_name}</td>
                            <td>${cantidad}</td>
                            <td>S/ ${precio.toFixed(2)}</td>
                            <td>S/ ${precioTotal.toFixed(2)}</td> <!-- Subtotal que ya existe en la DB -->
                        </tr>
                    `;
                    $("#incomeProductDetails").append(row);
                });

                // Mostrar total corregido
                $("#full_purchase").val("S/ " + total.toFixed(2));

                // Mostrar la modal
                $("#incomeProductModal").modal("show");
            } else {
                alert("No se encontraron detalles para este ingreso de productos.");
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.error("Error en la petición AJAX:", textStatus, errorThrown);
            alert("Ocurrió un error al obtener los detalles.");
        }
    });
});







/*
function load_datatable() {
destroy_datatable(); // Elimina la tabla existente antes de recargar

let dataTable = $("#datatables-income-products-pending").DataTable({
    ajax: {
    url: BASE_URL + "Income_Products_Pending/get_income_products_pending",
    cache: false,
    dataSrc: "data", // <- Asegura que tome la clave "data"
    error: function () {
        alert("Error al cargar datos.");
    },
    },
    columns: [
    { data: "id_income_products" },
    { data: "name" },
    { data: "sale_date" },
    { data: "voucher_type_description" },
    { data: "payment_type_description" },
    { data: "total_purchase" },
    { data: "status",
        title: "Estado",
        class: "center",
        render: function (data, type, row){
            if (row.status == "1") {
                return `<div class="d-inline-flex align-items-center">
                            <span class="badge rounded-pill badge-light-warning">Pendiente</span>
                        </div>`;
            }
        }
    },
    {
        class: "center",
        render: function (data, type, row) {
        return (
            '<button class="btn btn-sm btn-warning btn_update" data-process-key="' +
            row.id_income_products +
            '">' +
            feather.icons["edit"].toSvg({ class: "font-small-2" }) +
            "</button>" +
            " " +

            '<button class="btn btn-sm btn-primary btn_details" data-process-key="' +
            row.id_income_products +
            '">' +
            feather.icons["eye"].toSvg({ class: "font-small-2" }) +
            "</button>"
        );
        },
    },
    ],
    dom: functions.head_datatable(),
        buttons: [],
        language: { url: BASE_URL + "public/assets/json/languaje-es.json" }
    });

    dataTable.on("xhr", function () {
        let data = dataTable.ajax.json();
        console.log("Datos recibidos:", data);
    });
}*/

load_datatable();
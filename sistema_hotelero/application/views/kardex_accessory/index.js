let detail = [];

// AGREGAR FILA
function addAccessory() {

    let code = $('#code').val();
    let product = $('#product option:selected').text();
    let category = $('#category option:selected').text();
    let entry = $('#entry_stock').val();
    let exit = $('#exit_stock').val();
    let balance = $('#balance').val();
    let id_accessory = $('#product').val();

    // VALIDACIONES
    if (id_accessory === null || id_accessory === "") {

        alert("Seleccione un producto");
        return;
    }

    if (entry === "" && exit === "") {

        alert("Ingrese entrada o salida");
        return;
    }

    // GUARDAR EN ARRAY
    detail.push({
        id_accessory: id_accessory,
        code: code,
        product: product,
        category: category,
        entry_stock: entry || 0,
        exit_stock: exit || 0,
        balance: balance || 0
    });

    // CREAR FILA
    let row = `

        <tr>

            <td>${code}</td>

            <td>${product}</td>

            <td>${category}</td>

            <td>${entry || 0}</td>

            <td>${exit || 0}</td>

            <td>${balance || 0}</td>

        </tr>

    `;

    $('#detail_accessory').append(row);

    clearInputs();
}

// LIMPIAR INPUTS
function clearInputs() {

    $('#code').val('');
    $('#product').val('');
    $('#category').val('');
    $('#entry_stock').val('');
    $('#exit_stock').val('');
    $('#balance').val('');
}

// CALCULAR SALDO
function calculateBalance() {

    let entry = parseInt($('#entry_stock').val()) || 0;

    let exit = parseInt($('#exit_stock').val()) || 0;

    let currentStock = parseInt(
        $('#balance').data('stock')
    ) || 0;

    let total = currentStock + entry - exit;

    $('#balance').val(total);
}

// EVENTOS
$(document).ready(function () {

    // AGREGAR
    $('#btn_add_accessory').click(function () {

        addAccessory();
    });

    // CALCULAR SALDO
    $('#entry_stock, #exit_stock').on('keyup change', function () {

        calculateBalance();
    });

});
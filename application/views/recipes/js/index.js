$(document).ready(function () {

    let ingredients = {};

    load_ingredients();
    load_recipes();


    function esc(text) {
        return String(text === null || text === undefined ? '' : text)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }


    // =========================================================
    // 1. INGREDIENTES
    // =========================================================

    function load_ingredients() {
        $.ajax({
            url: BASE_URL + 'Recipes/get_ingredients',
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                ingredients = {};
                if (response.status === 'OK' && Array.isArray(response.data)) {
                    response.data.forEach(function (i) {
                        ingredients[i.id_ingredient] = i;
                    });
                }
            },
            error: function (xhr) {
                console.error('Error al cargar ingredientes:', xhr.responseText);
            }
        });
    }


    // =========================================================
    // 2. LISTA DE PLATOS
    // =========================================================

    function load_recipes() {
        $.ajax({
            url: BASE_URL + 'Recipes/get_recipes',
            type: 'GET',
            dataType: 'json',
            success: function (response) {

                if (response.status !== 'OK' || !Array.isArray(response.data) || response.data.length === 0) {
                    $('#table_recipes tbody').html(
                        '<tr><td colspan="6" class="text-center text-muted py-3">No hay platos.</td></tr>'
                    );
                    return;
                }

                let html = '';

                response.data.forEach(function (m) {
                    html += `
                        <tr class="recipe-row" data-name="${esc(String(m.meal_name).toLowerCase())}">
                            <td>${esc(m.meal_name)}</td>
                            <td>${esc(m.name_category)}</td>
                            <td class="text-center">${esc(m.total_ingredients)}</td>
                            <td class="text-end">S/ ${parseFloat(m.cost || 0).toFixed(2)}</td>
                            <td class="text-end">S/ ${parseFloat(m.meal_price || 0).toFixed(2)}</td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-outline-primary btn-open-recipe"
                                    data-id="${esc(m.id_meal)}"
                                    data-name="${esc(m.meal_name)}">Receta</button>
                            </td>
                        </tr>
                    `;
                });

                $('#table_recipes tbody').html(html);
            },
            error: function (xhr) {
                console.error('Error al cargar recetas:', xhr.responseText);
                $('#table_recipes tbody').html(
                    '<tr><td colspan="6" class="text-center text-danger py-3">Error al cargar.</td></tr>'
                );
            }
        });
    }


    // =========================================================
    // 3. BUSCADOR
    // =========================================================

    $(document).on('keyup', '#recipe_search', function () {
        let q = $(this).val().toLowerCase().trim();
        $('.recipe-row').each(function () {
            $(this).toggle(String($(this).data('name')).includes(q));
        });
    });


    // =========================================================
    // 4. FILAS DEL MODAL DE RECETA
    // =========================================================

    function ingredient_options(selectedId) {
        let html = '<option value="">Seleccione...</option>';
        Object.keys(ingredients).forEach(function (id) {
            let sel = String(id) === String(selectedId) ? 'selected' : '';
            html += `<option value="${esc(id)}" ${sel}>${esc(ingredients[id].name)} (${esc(ingredients[id].unit)})</option>`;
        });
        return html;
    }

    function unit_options(selected) {
        return ['und', 'kg', 'g', 'l', 'ml'].map(function (u) {
            return `<option value="${u}" ${u === selected ? 'selected' : ''}>${u}</option>`;
        }).join('');
    }

    function add_row(item) {
        item = item || {};
        $('#recipe_rows').append(`
            <tr class="ingredient-row">
                <td><select class="form-select form-select-sm ing-product">${ingredient_options(item.id_ingredient)}</select></td>
                <td><input type="number" class="form-control form-control-sm ing-qty" min="0" step="0.001" value="${esc(item.quantity || '')}"></td>
                <td><select class="form-select form-select-sm ing-unit">${unit_options(item.unit || 'und')}</select></td>
                <td class="text-end"><button type="button" class="btn btn-sm btn-outline-danger btn-remove-ing">&times;</button></td>
            </tr>
        `);
    }

    function update_cost() {
        let total = 0;
        $('.ingredient-row').each(function () {
            let ing = ingredients[$(this).find('.ing-product').val()];
            let qty = parseFloat($(this).find('.ing-qty').val()) || 0;
            if (ing) {
                total += qty * parseFloat(ing.cost || 0);
            }
        });
        $('#recipe_cost').text('S/ ' + total.toFixed(2));
    }

    // Al elegir un ingrediente, la unidad se pone igual a la de compra
    $(document).on('change', '.ing-product', function () {
        let ing = ingredients[$(this).val()];
        if (ing) {
            $(this).closest('tr').find('.ing-unit').val(ing.unit);
        }
        update_cost();
    });

    $(document).on('keyup change', '.ing-qty', update_cost);

    $(document).on('click', '#btn_add_ingredient', function () {
        add_row();
    });

    $(document).on('click', '.btn-remove-ing', function () {
        $(this).closest('tr').remove();
        update_cost();
    });


    // =========================================================
    // 5. ABRIR RECETA DE UN PLATO
    // =========================================================

    $(document).on('click', '.btn-open-recipe', function () {

        let idMeal = $(this).data('id');

        $('#modal_recipe_title').text('Receta: ' + $(this).data('name'));
        $('#recipe_id_meal').val(idMeal);
        $('#recipe_rows').empty();
        $('#recipe_cost').text('S/ 0.00');

        $.ajax({
            url: BASE_URL + 'Recipes/get_recipe',
            type: 'GET',
            data: { id_meal: idMeal },
            dataType: 'json',
            success: function (response) {

                if (response.status === 'OK' && Array.isArray(response.data)) {
                    response.data.forEach(function (row) {
                        add_row(row);
                    });
                }

                if ($('.ingredient-row').length === 0) {
                    add_row();
                }

                update_cost();

                bootstrap.Modal
                    .getOrCreateInstance(document.getElementById('modal_recipe'))
                    .show();
            },
            error: function (xhr) {
                console.error('Error al cargar la receta:', xhr.responseText);
                alert('No se pudo cargar la receta.');
            }
        });
    });


    // =========================================================
    // 6. GUARDAR RECETA
    // =========================================================

    $(document).on('click', '#btn_save_recipe', function () {

        let items = [];

        $('.ingredient-row').each(function () {
            let id = $(this).find('.ing-product').val();
            let qty = parseFloat($(this).find('.ing-qty').val());

            if (id && qty > 0) {
                items.push({
                    id_ingredient: parseInt(id),
                    quantity: qty,
                    unit: $(this).find('.ing-unit').val()
                });
            }
        });

        $.ajax({
            url: BASE_URL + 'Recipes/save_recipe',
            type: 'POST',
            contentType: 'application/json; charset=utf-8',
            data: JSON.stringify({
                id_meal: parseInt($('#recipe_id_meal').val()),
                items: items
            }),
            dataType: 'json',
            success: function (response) {
                if (response.status === 'OK') {
                    bootstrap.Modal
                        .getOrCreateInstance(document.getElementById('modal_recipe'))
                        .hide();
                    load_recipes();
                } else {
                    alert(response.msg || 'No se pudo guardar la receta.');
                }
            },
            error: function (xhr) {
                console.error('Error al guardar:', xhr.responseText);
                alert('Ocurrió un error al conectar con el servidor.');
            }
        });
    });


    // =========================================================
    // 7. NUEVO INGREDIENTE
    // =========================================================

    $(document).on('click', '#btn_new_ingredient', function () {
        $('#ing_name').val('');
        $('#ing_unit').val('kg');
        $('#ing_cost').val('');

        bootstrap.Modal
            .getOrCreateInstance(document.getElementById('modal_ingredient'))
            .show();
    });

    $(document).on('click', '#btn_save_ingredient', function () {

        let data = {
            name: $('#ing_name').val().trim(),
            unit: $('#ing_unit').val(),
            cost: $('#ing_cost').val()
        };

        if (data.name === '' || data.cost === '' || parseFloat(data.cost) < 0) {
            alert('Escribe el nombre y un costo válido.');
            return;
        }

        $.ajax({
            url: BASE_URL + 'Recipes/create_ingredient',
            type: 'POST',
            contentType: 'application/json; charset=utf-8',
            data: JSON.stringify(data),
            dataType: 'json',
            success: function (response) {
                if (response.status === 'OK') {
                    bootstrap.Modal
                        .getOrCreateInstance(document.getElementById('modal_ingredient'))
                        .hide();
                    load_ingredients();
                } else {
                    alert(response.msg || 'No se pudo crear el ingrediente.');
                }
            },
            error: function (xhr) {
                console.error('Error al crear ingrediente:', xhr.responseText);
                alert('Ocurrió un error al conectar con el servidor.');
            }
        });
    });

});
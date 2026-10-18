$(document).ready(function () {

    let menuItems = {};   // plato por id, para editar sin otra consulta

    load_menu_categories();
    load_menu();


    function esc(text) {
        return String(text === null || text === undefined ? '' : text)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }


    // =========================================================
    // 1. CATEGORÍAS (filtros + select del modal)
    // =========================================================

    function load_menu_categories() {

        $.ajax({
            url: BASE_URL + 'Meal/get_categories',
            type: 'GET',
            dataType: 'json',

            success: function (response) {

                if (response.status === 'OK' && Array.isArray(response.data)) {

                    let html = `
                        <button type="button"
                            class="btn btn-outline-primary active btn-sm menu-filter-cat mb-1"
                            data-id="all">Todas</button>
                    `;

                    let options = '<option value="">Seleccione...</option>';

                    response.data.forEach(function (cat) {

                        let catId = cat.id_category || cat.id || '';
                        let catName = cat.name_category || cat.description || '';

                        if (catName !== '') {
                            html += `
                                <button type="button"
                                    class="btn btn-outline-primary btn-sm menu-filter-cat mb-1"
                                    data-id="${esc(catId)}">${esc(catName)}</button>
                            `;
                            options += `<option value="${esc(catId)}">${esc(catName)}</option>`;
                        }
                    });

                    $('#menu_category_filters').html(html);
                    $('#meal_category').html(options);
                }
            },

            error: function (xhr) {
                console.error('Error al cargar categorías:', xhr.responseText);
            }
        });
    }


    // =========================================================
    // 2. CARGAR LA CARTA
    // =========================================================

    function load_menu() {

        $.ajax({
            url: BASE_URL + 'Meal/get_meal',
            type: 'GET',
            dataType: 'json',

            success: function (response) {

                menuItems = {};

                if (response.status === 'OK' && Array.isArray(response.data)) {

                    response.data.forEach(function (item) {
                        menuItems[item.id_meal] = item;
                    });

                    render_menu(response.data);
                    apply_menu_filters();

                } else {

                    $('#menu_container').html(
                        '<p class="text-muted text-center py-3">No hay platos en la carta.</p>'
                    );
                }
            },

            error: function (xhr) {
                console.error('Error al cargar la carta:', xhr.responseText);
                $('#menu_container').html(
                    '<p class="text-danger text-center py-3">Error al cargar la carta.</p>'
                );
            }
        });
    }


    // =========================================================
    // 3. MOSTRAR LA CARTA AGRUPADA POR CATEGORÍA
    // =========================================================

    function render_menu(items) {

        let groups = {};

        items.forEach(function (item) {

            let catId = item.id_category || 0;

            if (!groups[catId]) {
                groups[catId] = {
                    name: item.name_category || 'Sin categoría',
                    items: []
                };
            }

            groups[catId].items.push(item);
        });

        let html = '';

        Object.keys(groups).forEach(function (catId) {

            let group = groups[catId];

            html += `
                <div class="menu-category-block" data-category="${esc(catId)}">
                    <h4 class="menu-category-title">${esc(group.name)}</h4>
                    <div class="row g-2">
            `;

            group.items.forEach(function (item) {

                let price = parseFloat(item.meal_price || 0);
                let name = item.meal_name || 'Plato';
                let desc = item.meal_description || '';

                html += `
                    <div class="col-12 col-md-6 col-lg-4 menu-item"
                         data-name="${esc(name.toLowerCase())}"
                         data-category="${esc(catId)}">

                        <div class="card border p-2 menu-item-card shadow-sm h-100">

                            <div class="d-flex justify-content-between">
                                <h6 class="fw-bold mb-1">${esc(name)}</h6>
                                <span class="text-primary fw-bold">S/ ${price.toFixed(2)}</span>
                            </div>

                            <small class="text-muted flex-grow-1">
                                ${esc(desc) || 'Sin descripción'}
                            </small>

                            <div class="text-end mt-1">
                                <button type="button"
                                    class="btn btn-sm btn-outline-primary btn-edit-meal"
                                    data-id="${esc(item.id_meal)}">Editar</button>
                                <button type="button"
                                    class="btn btn-sm btn-outline-danger btn-delete-meal"
                                    data-id="${esc(item.id_meal)}">Eliminar</button>
                            </div>

                        </div>

                    </div>
                `;
            });

            html += '</div></div>';
        });

        $('#menu_container').html(html);
    }


    // =========================================================
    // 4. FILTROS
    // =========================================================

    function apply_menu_filters() {

        let catId = String($('.menu-filter-cat.active').data('id') || 'all');
        let query = $('#menu_search').val().toLowerCase().trim();

        $('.menu-item').each(function () {

            let matchCat = (catId === 'all' || String($(this).data('category')) === catId);
            let matchName = String($(this).data('name') || '').includes(query);

            $(this).toggle(matchCat && matchName);
        });

        $('.menu-category-block').each(function () {
            $(this).toggle($(this).find('.menu-item:visible').length > 0);
        });
    }

    $(document).on('click', '.menu-filter-cat', function () {
        $('.menu-filter-cat').removeClass('active');
        $(this).addClass('active');
        apply_menu_filters();
    });

    $(document).on('keyup', '#menu_search', apply_menu_filters);


    // =========================================================
    // 5. MODAL: NUEVO / EDITAR
    // =========================================================

    function open_meal_modal(item) {

        $('#modal_meal_title').text(item ? 'Editar plato' : 'Nuevo plato');

        $('#meal_id').val(item ? item.id_meal : '');
        $('#meal_name').val(item ? item.meal_name : '');
        $('#meal_description').val(item ? item.meal_description : '');
        $('#meal_category').val(item ? item.id_category : '');
        $('#meal_price').val(item ? item.meal_price : '');
        $('#meal_sku').val(item ? item.meal_sku : '');

        bootstrap.Modal
            .getOrCreateInstance(document.getElementById('modal_meal'))
            .show();
    }

    $(document).on('click', '#btn_new_meal', function () {
        open_meal_modal(null);
    });

    $(document).on('click', '.btn-edit-meal', function () {
        let item = menuItems[$(this).data('id')];
        if (item) {
            open_meal_modal(item);
        }
    });


    // =========================================================
    // 6. GUARDAR
    // =========================================================

    $(document).on('click', '#btn_save_meal', function () {

        let data = {
            id_meal: $('#meal_id').val(),
            meal_name: $('#meal_name').val().trim(),
            meal_description: $('#meal_description').val().trim(),
            id_category: $('#meal_category').val(),
            meal_price: $('#meal_price').val(),
            meal_sku: $('#meal_sku').val().trim()
        };

        if (data.meal_name === '' || data.id_category === '' || !(parseFloat(data.meal_price) > 0)) {
            alert('Completa nombre, categoría y un precio mayor a 0.');
            return;
        }

        $.ajax({
            url: BASE_URL + 'Meal/save_meal',
            type: 'POST',
            contentType: 'application/json; charset=utf-8',
            data: JSON.stringify(data),
            dataType: 'json',

            success: function (response) {

                if (response.status === 'OK') {
                    bootstrap.Modal
                        .getOrCreateInstance(document.getElementById('modal_meal'))
                        .hide();
                    load_menu();
                } else {
                    alert(response.msg || 'No se pudo guardar el plato.');
                }
            },

            error: function (xhr) {
                console.error('Error al guardar:', xhr.responseText);
                alert('Ocurrió un error al conectar con el servidor.');
            }
        });
    });


    // =========================================================
    // 7. ELIMINAR
    // =========================================================

    $(document).on('click', '.btn-delete-meal', function () {

        let id = $(this).data('id');
        let item = menuItems[id];

        if (!item || !confirm('¿Eliminar "' + item.meal_name + '" de la carta?')) {
            return;
        }

        $.ajax({
            url: BASE_URL + 'Meal/delete_meal',
            type: 'POST',
            contentType: 'application/json; charset=utf-8',
            data: JSON.stringify({ id_meal: id }),
            dataType: 'json',

            success: function (response) {

                if (response.status === 'OK') {
                    load_menu();
                } else {
                    alert(response.msg || 'No se pudo eliminar el plato.');
                }
            },

            error: function (xhr) {
                console.error('Error al eliminar:', xhr.responseText);
                alert('Ocurrió un error al conectar con el servidor.');
            }
        });
    });

});
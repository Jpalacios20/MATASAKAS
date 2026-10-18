<!-- BEGIN: Content -->
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>

    <div class="content-wrapper container-xxl p-0">

        <div class="content-header row mb-1">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">

                        <h2 class="content-header-title float-start mb-0">
                            Menu / Carta
                        </h2>

                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">

                                <li class="breadcrumb-item">
                                    <a href="#">
                                        <?php echo isset($selected_menu) ? $selected_menu : 'Módulo'; ?>
                                    </a>
                                </li>

                                <li class="breadcrumb-item active">
                                    <span>
                                        <?php echo isset($selected_sub_menu) ? $selected_sub_menu : 'Menu / Carta'; ?>
                                    </span>
                                </li>

                            </ol>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="content-body">

            <div class="row">

                <div class="col-12">

                    <div class="card p-2">

                        <!-- CATEGORÍAS -->
                        <div class="mb-2">

                            <label class="form-label">
                                Categorías:
                            </label>

                            <div id="menu_category_filters"
                                 class="d-flex flex-wrap gap-1">

                                <button
                                    type="button"
                                    class="btn btn-outline-primary active btn-sm menu-filter-cat"
                                    data-id="all">
                                    Todas
                                </button>

                            </div>

                        </div>

                        <div class="mb-2 text-end">
                            <button type="button" id="btn_new_meal" class="btn btn-success btn-sm">
                                + Nuevo plato
                            </button>
                        </div>

                        <!-- BUSCADOR -->
                        <div class="mb-2">
                            <input
                                type="text"
                                id="menu_search"
                                class="form-control"
                                placeholder="Buscar plato...">
                        </div>

                        <!-- CARTA (se carga con AJAX, agrupada por categoría) -->
                        <div id="menu_container">
                            <p class="text-muted text-center py-3">
                                Cargando carta...
                            </p>
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>
</div>

<!-- MODAL NUEVO/EDITAR PLAN -->
<div class="modal fade" id="modal_meal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="modal_meal_title">Nuevo plato</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <input type="hidden" id="meal_id">

                <div class="mb-1">
                    <label class="form-label">Nombre *</label>
                    <input type="text" id="meal_name" class="form-control" maxlength="100">
                </div>

                <div class="mb-1">
                    <label class="form-label">Descripción</label>
                    <textarea id="meal_description" class="form-control" rows="2"></textarea>
                </div>

                <div class="row">
                    <div class="col-6 mb-1">
                        <label class="form-label">Categoría *</label>
                        <select id="meal_category" class="form-select"></select>
                    </div>
                    <div class="col-6 mb-1">
                        <label class="form-label">Precio (S/) *</label>
                        <input type="number" id="meal_price" class="form-control" min="0" step="0.10">
                    </div>
                </div>

                <div class="mb-1">
                    <label class="form-label">SKU (opcional)</label>
                    <input type="text" id="meal_sku" class="form-control" placeholder="Se genera solo si lo dejas vacío">
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" id="btn_save_meal" class="btn btn-success">Guardar</button>
            </div>

        </div>
    </div>
</div>
<!-- END: Content -->

<style>
    .form-label {
        font-weight: bold;
    }

    .breadcrumb-wrapper {
        margin-top: 10px;
    }

    .menu-category-title {
        border-bottom: 2px solid #7367f0;
        padding-bottom: 4px;
        margin: 15px 0 10px 0;
    }

    .menu-item-card {
        margin-bottom: 0;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .menu-item-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
</style>
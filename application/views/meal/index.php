<!-- BEGIN: Content -->
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>

    <div class="content-wrapper container-xxl p-0">

        <!-- HEADER Y BREADCRUMBS -->
        <div class="content-header row mb-1">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-start mb-0">Nueva Comanda</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="#"><?php echo isset($selected_menu) ? $selected_menu : 'Módulo'; ?></a>
                                </li>
                                <li class="breadcrumb-item active">
                                    <span><?php echo isset($selected_sub_menu) ? $selected_sub_menu : 'Nueva Comanda'; ?></span>
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-body mb-3">
            <div class="row">

                <!-- SECCIÓN IZQUIERDA: PRODUCTOS -->
                <div class="col-md-7 col-lg-8">
                    <div class="card p-2">

                        <!-- CATEGORÍAS -->
                        <div class="mb-2">
                            <label class="form-label">Categorías:</label>
                            <div id="category_filters" class="d-flex flex-wrap gap-1">
                                <button type="button" class="btn btn-outline-primary active btn-sm filter-cat" data-id="all">
                                    Todas
                                </button>
                            </div>
                        </div>

                        <!-- BUSCADOR -->
                        <div class="mb-2">
                            <input type="text" id="search_meal" class="form-control" placeholder="Buscar plato o bebida...">
                        </div>

                        <!-- GRID DE PRODUCTOS -->
                        <div class="row g-2" id="grid_meals_container" style="max-height: 550px; overflow-y: auto;">
                            <!-- Los productos se cargan mediante AJAX -->
                        </div>

                    </div>
                </div>

                <!-- SECCIÓN DERECHA: DETALLE DE COMANDA -->
                <div class="col-md-5 col-lg-4">
                    <div class="card p-2">

                        <h4 class="card-title text-center mb-2">Detalle de Comanda</h4>

                        <!-- MESA / REFERENCIA -->
                        <div class="mb-2">
                            <label class="form-label">Mesa / Referencia</label>
                            <input type="text" id="comanda_reference" class="form-control" placeholder="Ej: Mesa 4 / Para llevar">
                        </div>

                        <!-- TABLA DE PRODUCTOS SELECCIONADOS -->
                        <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                            <table class="table table-sm text-center" id="table_comanda_items">
                                <thead>
                                    <tr>
                                        <th style="width: 45%;">Item</th>
                                        <th style="width: 25%;">Cant.</th>
                                        <th style="width: 20%;">Total</th>
                                        <th style="width: 10%;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr id="empty_cart_msg">
                                        <td colspan="4" class="text-muted py-3">No hay productos agregados</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <hr>

                        <!-- TOTAL -->
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-bold fs-5">Total:</span>
                            <span class="fw-bold fs-4 text-success" id="comanda_total_amount">S/ 0.00</span>
                        </div>

                        <!-- OBSERVACIONES -->
                        <div class="mb-2">
                            <textarea id="comanda_notes" class="form-control" rows="2" placeholder="Observaciones (ej: sin cebolla)..."></textarea>
                        </div>

                        <!-- BOTONES -->
                        <div class="d-grid gap-1">
                            <button type="button" id="btn_generar_comanda" class="btn btn-success btn-lg">Generar Comanda</button>
                            <button type="button" id="btn_cancel_comanda" class="btn btn-outline-danger">Cancelar / Limpiar</button>
                        </div>

                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
<!-- END: Content -->

<style>
    .container-fluid {
        width: 100%;
        padding: 0;
        margin: 0;
    }

    .content-wrapper {
        padding-left: 0px;
        padding-right: 0px;
    }

    .btn-add-item {
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .btn-add-item:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        border-color: #7367f0 !important;
    }

    .form-label {
        font-weight: bold;
    }

    .breadcrumb-wrapper {
        margin-top: 10px;
    }
</style>
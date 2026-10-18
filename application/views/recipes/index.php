<!-- BEGIN: Content -->
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>

    <div class="content-wrapper container-xxl p-0">

        <div class="content-header row mb-1">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-start mb-0">Recetas</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-body">
            <div class="card p-2">

                <div class="d-flex gap-1 mb-2">
                    <input type="text" id="recipe_search" class="form-control" placeholder="Buscar plato...">
                    <button type="button" id="btn_new_ingredient" class="btn btn-success text-nowrap">
                        + Ingrediente
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-sm align-middle" id="table_recipes">
                        <thead>
                            <tr>
                                <th>Plato</th>
                                <th>Categoría</th>
                                <th class="text-center">Ingredientes</th>
                                <th class="text-end">Costo estimado</th>
                                <th class="text-end">Precio venta</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td colspan="6" class="text-center text-muted py-3">Cargando...</td></tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

    </div>
</div>
<!-- END: Content -->


<!-- MODAL: RECETA DE UN PLATO -->
<div class="modal fade" id="modal_recipe" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="modal_recipe_title">Receta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <input type="hidden" id="recipe_id_meal">

                <table class="table table-sm align-middle">
                    <thead>
                        <tr>
                            <th style="width:45%;">Ingrediente</th>
                            <th style="width:20%;">Cantidad</th>
                            <th style="width:20%;">Unidad</th>
                            <th style="width:15%;"></th>
                        </tr>
                    </thead>
                    <tbody id="recipe_rows"></tbody>
                </table>

                <button type="button" id="btn_add_ingredient" class="btn btn-outline-primary btn-sm">
                    + Agregar ingrediente
                </button>

                <div class="d-flex justify-content-between mt-2">
                    <span class="fw-bold">Costo estimado:</span>
                    <span class="fw-bold text-success" id="recipe_cost">S/ 0.00</span>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" id="btn_save_recipe" class="btn btn-success">Guardar receta</button>
            </div>

        </div>
    </div>
</div>


<!-- MODAL: NUEVO INGREDIENTE -->
<div class="modal fade" id="modal_ingredient" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Nuevo ingrediente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="mb-1">
                    <label class="form-label">Nombre *</label>
                    <input type="text" id="ing_name" class="form-control" maxlength="100">
                </div>
                <div class="row">
                    <div class="col-6 mb-1">
                        <label class="form-label">Unidad de compra</label>
                        <select id="ing_unit" class="form-select">
                            <option value="kg">kg</option>
                            <option value="g">g</option>
                            <option value="l">l</option>
                            <option value="ml">ml</option>
                            <option value="und">und</option>
                        </select>
                    </div>
                    <div class="col-6 mb-1">
                        <label class="form-label">Costo por unidad (S/) *</label>
                        <input type="number" id="ing_cost" class="form-control" min="0" step="0.01">
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" id="btn_save_ingredient" class="btn btn-success">Guardar</button>
            </div>

        </div>
    </div>
</div>


<style>
    .form-label {
        font-weight: bold;
    }
</style>
<!-- BEGIN: Content -->
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>

    <div class="content-wrapper container-xxl p-0">

        <div class="content-header row mb-1">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-start mb-0">Pedidos</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-body">
            <div class="card p-2">

                <div class="row g-1 mb-2">
                    <div class="col-md-3">
                        <label class="form-label">Fecha</label>
                        <input type="date" id="order_date" class="form-control">
                    </div>
                    <div class="col-md-9">
                        <label class="form-label">Buscar</label>
                        <input type="text" id="order_search" class="form-control" placeholder="Mesa o N° de comanda...">
                    </div>
                </div>

                <div id="order_status_filters" class="d-flex flex-wrap gap-1 mb-2">
                    <button type="button" class="btn btn-outline-primary btn-sm order-filter active" data-status="all">Todos</button>
                    <button type="button" class="btn btn-outline-primary btn-sm order-filter" data-status="PENDIENTE">Pendiente</button>
                    <button type="button" class="btn btn-outline-primary btn-sm order-filter" data-status="EN_PREPARACION">En preparación</button>
                    <button type="button" class="btn btn-outline-primary btn-sm order-filter" data-status="LISTO">Listo</button>
                    <button type="button" class="btn btn-outline-primary btn-sm order-filter" data-status="ENTREGADO">Entregado</button>
                    <button type="button" class="btn btn-outline-primary btn-sm order-filter" data-status="PAGADO">Pagado</button>
                    <button type="button" class="btn btn-outline-primary btn-sm order-filter" data-status="ANULADO">Anulado</button>
                </div>

                <div class="table-responsive">
                    <table class="table table-sm align-middle" id="table_orders">
                        <thead>
                            <tr>
                                <th>N°</th>
                                <th>Fecha y hora</th>
                                <th>Mesa</th>
                                <th>Estado</th>
                                <th class="text-end">Total</th>
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


<div class="modal fade" id="modal_order" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="modal_order_title">Comanda</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <input type="hidden" id="order_id">

                <p class="mb-1"><strong>Mesa:</strong> <span id="order_table"></span></p>
                <p class="mb-1"><strong>Observaciones:</strong> <span id="order_notes"></span></p>

                <table class="table table-sm text-center">
                    <thead>
                        <tr>
                            <th class="text-start">Plato</th>
                            <th>Cant.</th>
                            <th class="text-end">P. unit.</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody id="order_items"></tbody>
                </table>

                <div class="d-flex justify-content-between mb-2">
                    <span class="fw-bold fs-5">Total:</span>
                    <span class="fw-bold fs-5 text-success" id="order_total">S/ 0.00</span>
                </div>

                <label class="form-label">Estado</label>
                <select id="order_status" class="form-select">
                    <option value="PENDIENTE">Pendiente</option>
                    <option value="EN_PREPARACION">En preparación</option>
                    <option value="LISTO">Listo</option>
                    <option value="ENTREGADO">Entregado</option>
                    <option value="PAGADO">Pagado</option>
                    <option value="ANULADO">Anulado</option>
                </select>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" id="btn_save_status" class="btn btn-success">Guardar estado</button>
            </div>

        </div>
    </div>
</div>


<style>
    .form-label {
        font-weight: bold;
    }
</style>
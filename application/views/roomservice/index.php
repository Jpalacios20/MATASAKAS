<style>
    /* =====================================================
       ROOM SERVICE - NUEVO PEDIDO
       ===================================================== */

    .roomservice-page *,
    .roomservice-page *::before,
    .roomservice-page *::after {
        box-sizing: border-box;
    }

    .roomservice-page .card {
        width: 100%;
        margin-bottom: 15px;
        border-radius: 8px;
    }

    .roomservice-page .card-header {
        padding: 12px 15px;
    }

    .roomservice-page .card-header .card-title {
        font-size: 17px;
        margin: 0;
    }

    .roomservice-page .card-body {
        padding: 15px;
    }

    .roomservice-page .form-label {
        display: block;
        font-size: 13px;
        font-weight: bold;
        margin-bottom: 5px;
    }

    .roomservice-page .form-control,
    .roomservice-page .form-select {
        width: 100%;
        min-width: 0;
        height: 38px;
        font-size: 13px;
    }

    .roomservice-page textarea.form-control {
        height: 85px;
        min-height: 85px;
        resize: vertical;
    }

    /* PRODUCTO */

    .producto-row {
        display: flex;
        align-items: flex-end;
        gap: 10px;
        width: 100%;
    }

    .producto {
        flex: 1 1 auto;
        min-width: 0;
    }

    .cantidad {
        width: 120px;
        flex: 0 0 120px;
    }

    .boton-agregar {
        width: 50px;
        flex: 0 0 50px;
    }

    .boton-agregar button {
        height: 38px;
    }

    /* TABLA */

    .roomservice-table {
        width: 100%;
        overflow-x: auto;
        margin-top: 15px;
    }

    .roomservice-table table {
        width: 100%;
        margin-bottom: 0;
    }

    .roomservice-table th,
    .roomservice-table td {
        padding: 8px;
        font-size: 13px;
        vertical-align: middle;
    }

    .roomservice-table th {
        white-space: nowrap;
    }

    /* TOTAL */

    .roomservice-total {
        text-align: right;
        font-size: 18px;
        margin-top: 15px;
        margin-bottom: 12px;
    }

    .roomservice-actions {
        text-align: right;
    }

    .roomservice-actions .btn {
        min-width: 155px;
    }

    @media (max-width: 600px) {

        .producto-row {
            display: block;
        }

        .producto,
        .cantidad,
        .boton-agregar {
            width: 100%;
            margin-bottom: 8px;
            flex: none;
        }

        .boton-agregar button {
            width: 100%;
        }

        .roomservice-actions {
            text-align: center;
        }

        .roomservice-actions .btn {
            width: 100%;
        }
    }
</style>


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
                            Room Service - Nuevo Pedido
                        </h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-body roomservice-page">

            <div class="row">

                <!-- DATOS DEL PEDIDO -->
                <div class="col-md-4 col-12">

                    <div class="card">

                        <div class="card-header">
                            <h4 class="card-title">Datos del pedido</h4>
                        </div>

                        <div class="card-body">

                            <div class="mb-2">
                                <label class="form-label">Habitación</label>
                                <select class="form-select" id="room-number">
                                    <option value="">Seleccionar habitación</option>
                                </select>
                            </div>

                            <div class="mb-2">
                                <label class="form-label">Cliente / Huésped</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    id="customer-name"
                                    placeholder="Nombre del huésped">
                            </div>

                            <div class="mb-2">
                                <label class="form-label">Notas</label>
                                <textarea
                                    class="form-control"
                                    id="order-notes"
                                    placeholder="Observaciones del pedido"></textarea>
                            </div>

                        </div>

                    </div>

                </div>


                <!-- PRODUCTOS -->
                <div class="col-md-8 col-12">

                    <div class="card">

                        <div class="card-header">
                            <h4 class="card-title">Productos</h4>
                        </div>

                        <div class="card-body">

                            <div class="producto-row">

                                <div class="producto">
                                    <label class="form-label">Producto</label>
                                    <select class="form-select" id="room-service-product">
                                        <option value="">Seleccionar producto</option>
                                    </select>
                                </div>

                                <div class="cantidad">
                                    <label class="form-label">Cantidad</label>
                                    <input
                                        type="number"
                                        class="form-control"
                                        id="product-quantity"
                                        value="1"
                                        min="1">
                                </div>

                                <div class="boton-agregar">
                                    <label class="form-label">&nbsp;</label>
                                    <button
                                        type="button"
                                        class="btn btn-primary w-100"
                                        id="btn-add-product">
                                        +
                                    </button>
                                </div>

                            </div>

                            <div class="roomservice-table">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>PRODUCTO</th>
                                            <th>CANT.</th>
                                            <th>PRECIO</th>
                                            <th>TOTAL</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody id="room-service-items"></tbody>
                                </table>
                            </div>

                            <div class="roomservice-total">
                                <strong>Total:</strong>
                                S/ <span id="room-service-total">0.00</span>
                            </div>

                            <div class="roomservice-actions">
                                <button
                                    type="button"
                                    class="btn btn-success"
                                    id="btn-save-room-service">
                                    Guardar Pedido
                                </button>
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>
</div>
<!-- END: Content -->


<script>
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
</script>
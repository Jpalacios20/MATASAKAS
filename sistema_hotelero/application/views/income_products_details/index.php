<!-- BEGIN: Content-->
<div class="app-content content ">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper container-xxl p-0">
        <div class="content-header row"></div>
        <div class="content-body">
            <section id="income_products_details">

                <!-- Header title -->
                <div class="content-header row">
                    <div class="content-header-left col-md-9 col-12 mb-2">
                        <div class="row breadcrumbs-top">
                            <div class="col-12">
                                <h2 class="content-header-title float-start mb-0">Lista de ingresos productos</h2>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Header table-->

                <div class="card">
                    <div class="card-body">

                        <form method="POST" enctype="multipart/form-data" id="create_income_products_details_form" class="row" onsubmit="return false">

                            <!-- FILA 1 -->
                            <div class="row mb-3">

                                <div class="col-md-4">
                                    <label class="form-label">Cliente</label>
                                    <select name="name" class="form-select select2" required></select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Serie</label>
                                    <input name="series" type="text" class="form-control" maxlength="8"
                                           pattern="[A-Za-z0-9]{1,8}" title="Solo se permiten letras y números">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Número de Serie</label>
                                    <input name="number_serial" type="text" class="form-control" maxlength="8"
                                           pattern="[A-Za-z0-9]{1,8}" title="Solo se permiten letras y números">
                                </div>
                            </div>

                            <!-- FILA 2 REORDENADA -->
                            <div class="row mb-3">

                                <div class="col-md-3">
                                    <label class="form-label">Tipo de Comprobante</label>
                                    <select name="vt_description" class="form-select select2" required></select>
                                </div>

                                <!-- Método de Pago toma el lugar de Tipo de Pago -->
                                <div class="col-md-3">
                                    <label class="form-label">Método de Pago</label>
                                    <select name="pm_description" class="form-select select2" required></select>
                                </div>

                                <!-- Fecha pasa donde estaba Método -->
                                <div class="col-md-3">
                                    <label class="form-label">Fecha</label>
                                    <input name="expiration_date" type="date" class="form-control">
                                </div>

                                <!-- Botón sube arriba -->
                                <div class="col-md-3 d-flex align-items-end">
                                    <button type="button" class="btn btn-primary w-100"
                                            data-bs-toggle="modal" data-bs-target="#create_income_product_modal">
                                        Agregar productos
                                    </button>
                                </div>
                            </div>

                            <!-- TABLA -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <table class="table" id="add_products">
                                            <thead>
                                                <tr>
                                                    <th>Acciones</th>
                                                    <th>Codigo</th>
                                                    <th>Producto</th>
                                                    <th>F. Vencimiento</th>
                                                    <th>Cantidad</th>
                                                    <th>Precio Compra</th>
                                                    <th>Precio de Venta</th>
                                                    <th>Sub total</th>
                                                    <th>Total</th>
                                                </tr>
                                            </thead>

                                            <tbody id="detalle_productos_cuerpo">
                                            </tbody>
                                            <tfoot>
                                                <tr class="table-light">
                                                    <td colspan="8" class="text-end fw-bold">TOTAL:</td>
                                                    <td class="fw-bold">
                                                        S/ <span id="gran_total">0.00</span>
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- BOTONES -->
                            <div class="col-12">
                                <button id="btn_guardar_product" type="submit" class="btn btn-primary">Guardar</button>
                                <button type="button" class="btn btn-secondary"
                                        onclick="window.location.href='Income_Products/index.php'">Cancelar</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- MODAL -->
                <div class="modal fade" id="create_income_product_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered modal-edit-product">
                        <div class="modal-content">
                            <div class="modal-header bg-transparent">
                                <button type="reset" class="btn-close reset" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body px-sm-5 pb-5">
                                <div class="text-center mb-2">
                                    <h1 class="mb-1">Selecionar Producto</h1>
                                </div>
                                <table class="table" id="datatables-income-products">
                                    <thead>
                                        <tr>
                                            <th>Acción</th>
                                            <th>Código</th>
                                            <th>Producto</th>
                                            <th>Descripción</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </section>
        </div>
    </div>
</div>

<!-- BEGIN: Content -->
<div class="app-content content ">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper container-xxl p-0">
        <div class="content-header row"></div>
        <div class="content-body">
            <section id="income_products_details">

                <div class="content-header row">
                    <div class="content-header-left col-md-9 col-12 mb-2">
                        <div class="row breadcrumbs-top">
                            <div class="col-12">
                                <h2 class="content-header-title float-start mb-0">Actualización de Compra</h2>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Formulario de Actualización -->
                <div class="card">
                    <div class="card-body">

                        <form method="POST" enctype="multipart/form-data" id="update_income_products_details_form" class="row">

                            <!-- Fila 1 CORRECTAMENTE REORDENADA -->
                            <div class="row mb-3">

                                <div class="col-md-4">
                                    <label class="form-label">Cliente</label>
                                    <select name="name" class="form-select select2" required></select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Tipo de Comprobante</label>
                                    <select name="vt_description" class="form-select select2" required></select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Serie</label>
                                    <input name="series" type="text" class="form-control" maxlength="8" 
                                    pattern="[A-Za-z0-9]{1,8}" title="Solo se permiten letras y números (máx. 8 caracteres)" required>
                                </div>

                            </div>

                            <!-- Fila 2 — Número Serie ahora va aquí -->
                            <div class="row mb-3">

                                <div class="col-md-3">
                                    <label class="form-label">Número de Serie</label>
                                    <input name="number_serial" type="text" class="form-control" maxlength="8" 
                                    pattern="[A-Za-z0-9]{1,8}" title="Solo se permiten letras y números (máx. 8 caracteres)" required>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Método de Pago</label>
                                    <select name="pm_description" class="form-select select2" required></select>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Fecha de vencimiento</label>
                                    <input name="expiration_date" type="date" class="form-control">
                                </div>

                                <div class="col-md-3 d-flex align-items-end">
                                    <button type="button" class="btn btn-primary w-100" 
                                        data-bs-toggle="modal" data-bs-target="#update_income_product_modal">
                                        Agregar productos
                                    </button>
                                </div>

                            </div>

                            <!-- Tabla de productos -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <table class="table" id="add_products">
                                            <thead>
                                                <tr>
                                                    <th>Acciones</th>
                                                    <th>Producto</th>
                                                    <th style="width:120px">Cantidad</th>
                                                    <th>Precio Compra</th>
                                                    <th>Sub total</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Botón de actualización -->
                            <div class="col-12">
                                <button id="btn_update_product" type="submit" class="btn btn-primary">Actualizar</button>
                                <button type="button" class="btn btn-secondary" onclick="window.location.href='/gliese/Income_Products/index.php'">Cancelar</button>
                            </div>

                        </form>
                    </div>
                </div>

                <!-- MODAL SELECCIONAR PRODUCTOS -->
                <div class="modal fade" id="update_income_product_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered modal-edit-product">
                        <div class="modal-content">
                            <div class="modal-header bg-transparent">
                                <button type="reset" class="btn-close reset" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body px-sm-5 pb-5">
                                <div class="text-center mb-2">
                                    <h1 class="mb-1">Seleccionar Producto</h1>
                                </div>
                                <table class="table table-responsive" id="datatables-income-products">
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

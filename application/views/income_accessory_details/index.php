<!-- BEGIN: Content-->
<div class="app-content content ">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper container-xxl p-0">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <!-- Campus Starts -->
                <section id="income_accessory_details">

                    <!-- Header title -->
                    <div class="content-header row">
                        <div class="content-header-left col-md-9 col-12 mb-2">
                            <div class="row breadcrumbs-top">
                                <div class="col-12">
                                    <h2 class="content-header-title float-start mb-0">Lista de ingresos accesorios</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /Header table-->

                    <!-- Container for adding products -->
                    <div class="card">
                        <div class="card-body">
                            <form method="POST" enctype="multipart/form-data" id="create_income_accessory_details_form" class="row" onsubmit="return false">
                                <input type="hidden" id="id_income_accessory" value="<?php echo isset($_GET['id']) ? $_GET['id'] : '0'; ?>">
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <div>
                                            <label class="form-label">Proveedor</label>
                                            <select name="name" class="form-select select2" data-msg="" required>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Fecha</label>
                                        <input name="proof_date" type="date" class="form-control">
                                    </div>
                                    <div class="col-md-4">
                                        <div>
                                            <label class="form-label">Tipo de Comprobante</label>
                                            <select name="vt_description" class="form-select select2" data-msg="" required>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Serie</label>
                                        <input name="proof_series" type="text" class="form-control" placeholder="B001">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Numero De Correlativo</label>
                                        <input name="voucher_series" type="text" class="form-control" placeholder="00000001">
                                    </div>
                                    <div class="col-md-4">
                                        <div>
                                            <label class="form-label">Tipo de Pago</label>
                                            <select name="pt_description" class="form-select select2" data-msg="" required>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-12">
                                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#create_income_accessories_modal">Agregar Accessorios</button>
                                    </div>
                                </div>

                                <!-- Table -->
                                <div class="row">
                                    <div class="col-12">
                                        <div class="card">
                                            <table class="table" id="add_accessories">
                                                <thead>
                                                    <tr>
                                                        <th>Acciones</th>
                                                        <th>Producto</th>
                                                        <th>Cantidad</th>
                                                        <th>Precio compra</th>
                                                        <th>Sub total</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="table_body">
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <!-- /Table -->

                                <div class="col-12">
                                    <button id="btn_guardar_accessory" type="submit" class="btn btn-secondary">Guardar</button>
                                    <a href="<?php echo BASE_URL; ?>Income_Accessory" class="btn btn-secondary">Cancelar</a>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- End Container to add products -->





                    <div class="modal fade" id="create_income_accessories_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered modal-edit-accessories">
                            <div class="modal-content">
                                <div class="modal-header bg-transparent">
                                    <button type="reset" class="btn-close reset" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body px-sm-5 pb-5">
                                    <div class="text-center mb-2">
                                        <h1 class="mb-1">Seleccionar Accessorios</h1>
                                    </div>
                                    <table class="table table-responsive" id="datatables-income-accessories">
                                        <thead>
                                            <tr>
                                                <th>Acción</th>
                                                <th>Codigo</th>
                                                <th>Accesorio</th>
                                                <th>Precio</th>
                                                <th>Stock</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>








                    <!-- Create Income Products Modal -->

                    <!-- End Create Income Products Modal -->

                </section>
                <!-- Permissions ends -->
            </div>
        </div>
    </div>
    <!-- END: Content-->
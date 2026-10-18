<!-- BEGIN: Content-->
<div class="app-content content ">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper container-xxl p-0">
        <div class="content-header row"></div>
        <div class="content-body">
            <section id="income_accessory">

                <!-- Header title -->
                <div class="content-header row">
                    <div class="content-header-left col-md-9 col-12 mb-2">
                        <div class="row breadcrumbs-top">
                            <div class="col-12">
                                <h2 class="content-header-title float-start mb-0">Lista de <?php echo strtolower($selected_sub_menu); ?></h2>
                                <div class="breadcrumb-wrapper">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="#"><?php echo $selected_menu; ?></a></li>
                                        <li class="breadcrumb-item active"><span><?php echo $selected_sub_menu; ?></span></li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TABLE - LISTADO -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="table-responsive">
                                <table class="table table-hover table-sm align-middle mb-0" id="datatable-income-accessory" style="width:100%;">
                                    <thead>
                                        <tr>
                                            <th style="width:120px; white-space:nowrap; text-align:center;">Fecha</th>
                                            <th style="width:180px; white-space:nowrap; text-align:center;">Proveedor</th>
                                            <th style="width:120px; white-space:nowrap; text-align:center;">Usuario</th>
                                            <th style="width:120px; white-space:nowrap; text-align:center;">Documento</th>
                                            <th style="width:90px;  white-space:nowrap; text-align:center;">Número</th>
                                            <th style="width:130px; white-space:nowrap; text-align:center;">Método de Pago</th>
                                            <th style="width:70px;  white-space:nowrap; text-align:center;">Total</th>
                                            <th style="width:100px; white-space:nowrap; text-align:center;">Acciones</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!--NOTI-->
            <div id="toastContainer" class="position-fixed bottom-0 end-0 p-3" style="z-index: 1050;"></div>

            <!-- ==================== MODAL: VER DETALLE DE REGISTRO ==================== -->
            <div class="modal fade" id="incomeAccessoryModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
                <div class="modal-dialog modal-lg modal-dialog-centered modal-edit-user">
                    <div class="modal-content">
                        <div class="modal-header bg-transparent">
                            <button type="reset" class="btn-close reset" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body pb-5 px-sm-5 pt-50">
                            <div class="text-center mb-2">
                                <h1 class="mb-1">Detalle de Registro</h1>
                            </div>
                            <form method="GET" enctype="multipart/form-data" id="details_form" class="row g-2" onsubmit="return false">
                                <div class="col-md-6">
                                    <label class="form-label">Proveedor</label>
                                    <input type="text" id="name_client" class="form-control form-control-sm bg-light fw-bold" disabled />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Fecha de Compra</label>
                                    <input type="text" id="sale_date" class="form-control form-control-sm bg-light fw-bold" disabled />
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Serie</label>
                                    <input type="text" id="series" class="form-control form-control-sm bg-light fw-bold" disabled />
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Número de Serie</label>
                                    <input type="text" id="number_serial" class="form-control form-control-sm bg-light fw-bold" disabled />
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Tipo de Comprobante</label>
                                    <input type="text" id="voucher_type" class="form-control form-control-sm bg-light fw-bold" disabled />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Método de Pago</label>
                                    <input type="text" id="payment_type" class="form-control form-control-sm bg-light fw-bold" disabled />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Forma de Pago</label>
                                    <input type="text" id="payment_shape" class="form-control form-control-sm bg-light fw-bold" disabled />
                                </div>

                                <div class="col-12 mt-2">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm align-middle mb-0">
                                            <thead>
                                                <tr>
                                                    <th class="bg-primary text-white fw-bold">Accesorio</th>
                                                    <th class="bg-primary text-white fw-bold" style="width:80px; text-align:center;">Cantidad</th>
                                                    <th class="bg-primary text-white fw-bold" style="width:80px; text-align:center;">Precio Compra</th>
                                                    <th class="bg-primary text-white fw-bold" style="width:80px; text-align:center;">Precio Venta</th>
                                                    <th class="bg-primary text-white fw-bold" style="width:110px; text-align:center;">Subtotal</th>
                                                </tr>
                                            </thead>
                                            <tbody id="incomeAccessoryDetails"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ==================== MODAL: CREAR NUEVO INGRESO ==================== -->
            <div class="modal fade" id="create_income_accessory_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered" style="max-width: 76% !important; margin-left: auto !important; margin-right: 2% !important;">
                    <div class="modal-content">
                        <div class="modal-header bg-transparent">
                            <h5 class="modal-title">Ingreso de Accesorios</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body px-4 pb-4">

                            <form method="POST" enctype="multipart/form-data" id="create_income_accessory_details_form" class="row g-2" onsubmit="return false">

                                <!-- FILA 1: Proveedor, Serie, Número de Serie -->
                                <div class="col-md-4">
                                    <label class="form-label">Proveedor</label>
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

                                <!-- FILA 2: Tipo Comprobante, Método, Forma, Fecha -->
                                <div class="col-md-3">
                                    <label class="form-label">Tipo de Comprobante</label>
                                    <select name="vt_description" class="form-select select2" required></select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Método de Pago</label>
                                    <select name="pt_description" class="form-select select2" required></select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Forma de Pago</label>
                                    <select name="pm_description" class="form-select select2" required></select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Fecha de Compra</label>
                                    <input name="data_time" type="date" class="form-control">
                                </div>

                                <!-- FILA 3: Botón agregar -->
                                <div class="col-12 mb-1">
                                    <button type="button" class="btn btn-primary"
                                            data-bs-toggle="modal" data-bs-target="#select_accessory_modal">
                                        Agregar accesorios
                                    </button>
                                </div>

                                <!-- TABLA DE ACCESORIOS AGREGADOS -->
                                <div class="col-12">
                                    <div class="table-responsive">
                                        <table class="table table-sm" id="add_accessories" style="white-space: nowrap;">
                                            <thead>
                                                <tr>
                                                    <th>Acciones</th>
                                                    <th>Codigo</th>
                                                    <th>Accesorio</th>
                                                    <th>Cantidad</th>
                                                    <th>P. Compra</th>
                                                    <th>P. Venta</th>
                                                    <th>Subtotal</th>
                                                </tr>
                                            </thead>
                                            <tbody id="accessory_detail_body"></tbody>
                                            <tfoot>
                                                <tr class="table-light">
                                                    <td colspan="6" class="text-end fw-bold">TOTAL:</td>
                                                    <td class="fw-bold">S/ <span id="grand_total">0.00</span></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>

                                <!-- BOTONES -->
                                <div class="col-12 mt-1">
                                    <button id="btn_save_accessory" type="submit" class="btn btn-primary">Guardar</button>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ==================== MODAL: SELECCIONAR ACCESORIO ==================== -->
            <div class="modal fade" id="select_accessory_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered" style="max-width: 60% !important; margin-left: auto !important; margin-right: 2% !important;">
                    <div class="modal-content">
                        <div class="modal-header bg-transparent">
                            <button type="reset" class="btn-close reset" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body px-sm-5 pb-5">
                            <div class="text-center mb-5">
                                <h1 class="mb-1">Seleccionar Accesorio</h1>
                            </div>
                            <table class="table" id="datatables-select-accessories">
                                <thead>
                                    <tr>
                                        <th>Acción</th>
                                        <th>Código</th>
                                        <th>Accesorio</th>
                                        <th>Precio</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- BEGIN: Content-->
<div class="app-content content ">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper container-xxl p-0">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <!-- Campus Starts -->
                <section id="income_products">

                    <!-- Header title -->
                    <div class="content-header row">
                        <div class="content-header-left col-md-9 col-12 mb-2">
                            <div class="row breadcrumbs-top">
                                <div class="col-12">
                                <h2 class="content-header-title float-start mb-0">Lista de Registros Pendientes</h2>
                                    <div class="breadcrumb-wrapper">
                                        <ol class="breadcrumb">
                                            <li class="breadcrumb-item"><a href="#"><?php echo $selected_menu; ?></a>
                                            </li>
                                            <li class="breadcrumb-item active"><span><?php echo $selected_sub_menu; ?></span>
                                            </li>
                                            <li class="breadcrumb-item active"><span>Registros de Productos Pendientes</span>
                                            </li>
                                        </ol>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /Header table-->

                <!-- TABLE - LISTADO -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="d-flex justify-content-end px-2">
                                    <button class="btn btn-sm btn-primary ms-2 mt-2 p-1" id="register_active">Registrar Cambios</button>
                                    <button class="btn btn-sm btn-secondary ms-2 mt-2 p-1" onclick="window.location.href='Income_Products/index.php'">Regresar</button>
                                </div>
                                <table class="table" id="datatable-income-products-pending">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Cliente</th>            
                                            <th>Fecha Registro</th>
                                            <th>Tipo de Pago</th>
                                            <th>Tipo de Voucher</th>
                                            <th>Total</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </section>

                <!--DETAILS-->
                <div class="modal fade" id="incomeProductModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
                    <div class="modal-dialog modal-lg modal-dialog-centered modal-edit-user">
                        <div class="modal-content">
                            <div class="modal-header bg-transparent">
                                <button type="reset" class="btn-close reset" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body pb-5 px-sm-5 pt-50">
                                <div class="text-center mb-2">
                                    <h1 class="mb-1">Detalle de Registro</h1>
                                </div>

                                <form method="GET" enctype="multipart/form-data" id="details_form" class="row" onsubmit="return false">
                                    <div class="mb-1 col-md-6">
                                        <label class="form-label">Cliente</label>
                                        <input type="text" id="name_client" class="form-control bg-light fw-bold" disabled />
                                    </div>

                                    <div class="mb-1 col-md-6">
                                        <label class="form-label">Fecha de Compra</label>
                                        <input type="text" id="sale_date" class="form-control bg-light fw-bold" disabled />
                                    </div>

                                    <div class="mb-1 col-md-6">
                                        <label class="form-label">Serie</label>
                                        <input type="text" id="series" class="form-control bg-light fw-bold" disabled />
                                    </div>

                                    <div class="mb-1 col-md-6">
                                        <label class="form-label">Número de Serie</label>
                                        <input type="text" id="number_serial" class="form-control bg-light fw-bold" disabled />
                                    </div>

                                    <div class="mb-1 col-md-6">
                                        <label class="form-label">Fecha de Vencimiento</label>
                                        <input type="text" id="expiration_date" class="form-control bg-light fw-bold" disabled />
                                    </div>

                                    <div class="mb-1 col-md-6">
                                        <label class="form-label">Tipo de Comprobante</label>
                                        <input type="text" id="voucher_type" class="form-control bg-light fw-bold" disabled />
                                    </div>

                                    <div class="mb-1 col-md-6">
                                        <label class="form-label">Tipo de Pago</label>
                                        <input type="text" id="payment_type" class="form-control bg-light fw-bold" disabled />
                                    </div>

                                    <div class="mb-1 col-md-6">
                                        <label class="form-label">Shape de Pago</label>
                                        <input type="text" id="payment_shape" class="form-control bg-light fw-bold" disabled />
                                    </div>

                                    <div class="mb-1 col-md-12">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th class="bg-primary text-white fw-bold">Producto</th>
                                                    <th class="bg-primary text-white fw-bold">Cantidad</th>
                                                    <th class="bg-primary text-white fw-bold">Subtotal</th>
                                                    <th class="bg-primary text-white fw-bold">Precio Total</th>
                                                </tr>
                                            </thead>
                                            <tbody id="incomeProductDetails">
                                                <!-- Productos se llenarán aquí -->
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="mb-1 col-md-6">
                                        <label class="form-label">Importe Total</label>
                                        <input type="text" id="full_purchase" class="form-control bg-light fw-bold" disabled />
                                    </div>


                                    
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
<!-- BEGIN: Content -->
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper container-xxl p-0">
        <div class="content-header row">
            <!-- Header title -->
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-start mb-0">Lista de Productos</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="#"><?php echo $selected_menu; ?></a>
                                </li>
                                <li class="breadcrumb-item active">
                                    <span><?php echo $selected_sub_menu; ?></span>
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-body">
            <!-- Tabla de productos -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="table-responsive" style="width:100%; overflow-x:auto;">
                            <table class="table" id="datatable-product">
                             <thead>
                             <tr>
                                    <th style="width:80px; white-space:nowrap; text-align:center;">SKU</th>
                                    <th style="width:200px; white-space:nowrap; text-align:center;">Nombre</th>
                                    <th style="width:150px; white-space:nowrap; text-align:center;">Unidad de medida</th>
                                    <th style="width:150px; white-space:nowrap; text-align:center;">Categoría</th>
                                    <th style="width:150px; white-space:nowrap; text-align:center;">Precio</th>
                                    <th style="width:150px; white-space:nowrap; text-align:center;">Stock</th>
                                    <th style="width:130px; white-space:nowrap; text-align:center;">Fecha de expiración</th>
                                    <th style="width:100px; white-space:nowrap; text-align:center;">Estado</th>
                                    <th style="width:100px; white-space:nowrap; text-align:center;">Acciones</th>
                                </tr>
                            </thead>
                        </table>
                        <style>
        /* SKU  */
    #datatable-product th:nth-child(1),
    #datatable-product td:nth-child(1) {
        width: 12% !important;
        white-space: normal !important;
    }
    /* Nombre */
    #datatable-product th:nth-child(2),
    #datatable-product td:nth-child(2) {
        width: 40% !important;
        white-space: normal !important;
    }
        /* Descripción */
    #datatable-product th:nth-child(3),
    #datatable-product td:nth-child(3) {
      width: 10% !important;   
        white-space: normal !important;
    }
     /* Cetegoria */
    #datatable-product th:nth-child(4),
    #datatable-product td:nth-child(4) {
     width: 12% !important;    
        white-space: normal !important;
    }   
    /* Precio más */
    #datatable-product th:nth-child(5),
    #datatable-product td:nth-child(5) {
        width: 12% !important;
        text-align: center;
    }

    /* Stock  */
    #datatable-product th:nth-child(6),
    #datatable-product td:nth-child(6) {
        width: 8% !important;
        text-align: center;
    }

    /* Fecha de expiración */
    #datatable-product th:nth-child(7),
    #datatable-product td:nth-child(7) {
        width: 13% !important;
        text-align: center;
    }

    #datatable-product {
        table-layout: fixed !important;
    }
</style>

                    </div>
                </div>
            </div>
            <!-- /Tabla de productos -->

            <!-- Modal para Crear Producto -->
            <div class="modal fade" id="create_product_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
                <div class="modal-dialog modal-lg" modal-dialog-centered modal-dialog-scrollable;"> 
                    <div class="modal-content">
                        <div class="modal-header bg-transparent">
                            <h5 class="modal-title"></h5>
                            <button type="button" class="btn-close reset" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body px-sm-5 pb-5">
                            <div class="text-center mb-2">
                                <h1 class="mb-1">Agregar nuevo producto</h1>
                            </div>
                            <form method="POST" enctype="multipart/form-data" id="create_product_form" class="row" onsubmit="return false">
                                <div class="col-12">
                                    <label class="form-label">SKU</label>
                                    <input type="text" name="product_sku" class="form-control" placeholder="SKU" autofocus required />
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Nombre</label>
                                    <input type="text" name="product_name" class="form-control" placeholder="Nombre del producto" required />
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Descripción</label>
                                    <textarea name="product_description" class="form-control" placeholder="Descripción"></textarea>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Categoría</label>
                                    <select name="id_category" class="form-control" required>
                                        <!-- Aquí se llenará dinámicamente con las categorías disponibles -->
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Precio</label>
                                    <input type="number" name="product_price" class="form-control" placeholder="Precio" readonly />
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Stock</label>
                                    <input type="number" name="product_stock" class="form-control" placeholder="Cantidad en stock" readonly />
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Fecha de expiración</label>
                                    <input type="date" name="expiration_date" class="form-control" required />
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Estado de expiración</label>
                                    <select name="status_expiration_date" class="form-control" required>
                                        <option value="1">Vigente</option>
                                        <option value="0">Expirado</option>
                                    </select>
                                </div>
                                <div class="col-12 text-center mt-3">
                                    <button id="btn_create_product" type="submit" class="btn btn-primary mt-2 me-1">Guardar</button>
                                    <button type="reset" class="btn btn-outline-secondary mt-2 reset" data-bs-dismiss="modal">Cancelar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <style>

            </style>
            <!-- Modal para Actualizar Producto -->
            <div class="modal fade" id="update_product_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
                <div class="modal-dialog modal-lg" modal-dialog-centered modal-dialog-scrollable;"> 
                    <div class="modal-content">
                        <div class="modal-header bg-transparent">
                            <button type="reset" class="btn-close reset" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body px-sm-5 pb-5">
                            <div class="text-center mb-2">
                                <h1 class="mb-1">Actualizar producto</h1>
                            </div>
                            <form id="update_product_form" class="row" onsubmit="return false">
                                <input type="hidden" name="id_product" />
                                <div class="col-12">
                                    <label class="form-label">SKU</label>
                                    <input type="text" name="product_sku" class="form-control" required />
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Nombre</label>
                                    <input type="text" name="product_name" class="form-control" required />
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Descripción</label>
                                    <textarea name="product_description" class="form-control"></textarea>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Categoría</label>
                                    <select name="id_category" class="form-control" required>
                                        <!-- Aquí se llenará dinámicamente con las categorías disponibles -->
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Precio</label>
                                    <input type="number" name="product_price" class="form-control" readonly />
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Stock</label>
                                    <input type="number" name="product_stock" class="form-control" readonly />
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Fecha de expiración</label>
                                    <input type="date" name="expiration_date" class="form-control" required />
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Estado de expiración</label>
                                    <select name="status_expiration_date" class="form-control" required>
                                        <option value="1">Vigente</option>
                                        <option value="0">Expirado</option>
                                    </select>
                                </div>
                                <div class="col-10 text-center mt-3">
                                    <button id="btn_update_product" type="submit" class="btn btn-primary mt-2 me-1">Guardar</button>
                                    <button type="reset" class="btn btn-outline-secondary mt-2 reset" data-bs-dismiss="modal">Cancelar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Modal Actualizar Producto -->
        </div>
    </div>
</div>
<!-- END: Content -->

<!-- BEGIN: Content-->
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper container-xxl p-0">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-start mb-0">Lista de Accesorios</h2>
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
        <div class="content-body">
            <!-- Tabla de Accesorios -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <table class="table" id="datatable-accessories">
                            <thead>
                                <tr>
                                    <th style="width:80px; white-space:nowrap; text-align:center;">SKU</th>
                                    <th style="width:180px; white-space:nowrap; text-align:center;">Nombre</th>
                                    <th style="width:200px; white-space:nowrap; text-align:center;">Unidad de medida</th>
                                    <th style="width:150px; white-space:nowrap; text-align:center;">Precio</th>
                                    <th style="width:150px; white-space:nowrap; text-align:center;">Stock</th>
                                    <th style="width:200px; white-space:nowrap; text-align:center;">Acciones</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            <!-- /Tabla de Accesorios -->

            <!-- Modal para Crear Accesorio -->
            <div class="modal fade" id="create_accessory_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-transparent">
                            <button type="reset" class="btn-close reset" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body px-sm-5 pb-5">
                            <div class="text-center mb-2">
                                <h1 class="mb-1">Agregar nuevo accesorio</h1>
                            </div>
                            <form method="post">
                                <label for="palabra">Ingrese una palabra:</label>
                                <input type="text" id="palabra" name="palabra" required>
                                <button type="submit">Enviar</button>
                            </form>
                            <form method="POST" enctype="multipart/form-data" id="create_accessory_form" class="row" onsubmit="return false">
                                <div class="col-12">
                                    <label class="form-label">ID</label>
                                    <input type="number" name="id_accessory" class="form-control" placeholder="ID" required />
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Descripción</label>
                                    <input type="text" name="accessory_description" class="form-control" placeholder="Descripción" required />
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Precio</label>
                                    <input type="number" name="accessory_price" class="form-control" placeholder="Precio" required />
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Stock</label>
                                    <input type="number" name="accessory_stock" class="form-control" placeholder="Cantidad en stock" required />
                                </div>
                                <div class="col-12 text-center">
                                    <button id="btn_create_accessory" type="submit" class="btn btn-primary mt-2 me-1">Guardar</button>
                                    <button type="reset" class="btn btn-outline-secondary mt-2 reset" data-bs-dismiss="modal">Cancelar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Modal Crear Accesorio -->

            <!-- Modal para Actualizar Accesorio -->
            <div class="modal fade" id="update_accessory_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-transparent">
                            <button type="reset" class="btn-close reset" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body px-sm-5 pb-5">
                            <div class="text-center mb-2">
                                <h1 class="mb-1">Actualizar accesorio</h1>
                            </div>
                            <form method="POST" enctype="multipart/form-data" id="update_accessory_form" class="row" onsubmit="return false">
                                <div class="col-12">
                                    <label class="form-label">ID</label>
                                    <input type="text" name="id_accessory" class="form-control" placeholder="ID" readonly />
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Descripción</label>
                                    <input type="text" name="accessory_description" class="form-control" placeholder="Descripción" required />
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Precio</label>
                                    <input type="number" name="accessory_price" class="form-control" placeholder="Precio" readonly />
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Stock</label>
                                    <input type="number" name="accessory_stock" class="form-control" placeholder="Cantidad en stock" readonly />
                                </div>
                                <div class="col-12 text-center">
                                    <button id="btn_update_accessory" type="submit" class="btn btn-primary mt-2 me-1">Guardar</button>
                                    <button type="reset" class="btn btn-outline-secondary mt-2 reset" data-bs-dismiss="modal">Cancelar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Modal Actualizar Accesorio -->
        </div>
    </div>
</div>
<!-- END: Content-->
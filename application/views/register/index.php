<!-- BEGIN: Content-->
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper container-xxl p-0">
        
        <div class="content-header row">
        </div>

        <div class="content-body">
            
            <!-- BOTÓN NUEVO REGISTRO (BIEN ARRIBA) -->
            <div class="row mb-3">
                <div class="col-12 text-end" style="text-align: right !important;">
                    <a href="<?php echo base_url('c_register/nuevo'); ?>" class="btn btn-primary" style="padding: 10px 20px; font-weight: bold;">
                        + Nuevo Registro
                    </a>
                </div>
            </div>

            <!-- CONTENEDOR DE LA TABLA -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>FECHA CHECK-IN</th>
                                    <th>CLIENTE</th>
                                    <th>Nº HABITACIÓN</th>
                                    <th>HORA CHECK-IN</th>
                                    <th>FECHA CHECK-OUT</th>
                                    <th>HORA CHECK-OUT</th>
                                    <th>COSTO</th>
                                    <th>ESTADO</th>
                                    <th>ACCIONES</th>
                                </tr>
                            </thead>
                            <tbody id="tbody_registro">
                                <!-- Los datos que ya traía tu sistema -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<!-- END: Content-->
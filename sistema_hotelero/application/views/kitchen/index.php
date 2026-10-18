<!-- BEGIN: Content -->
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>

    <div class="content-wrapper container-xxl p-0">

        <!-- HEADER Y BREADCRUMBS -->
        <div class="content-header row mb-1">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-start mb-0">Cocina</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="#"><?php echo isset($selected_menu) ? $selected_menu : 'Módulo'; ?></a>
                                </li>
                                <li class="breadcrumb-item active">
                                    <span><?php echo isset($selected_sub_menu) ? $selected_sub_menu : 'Cocina'; ?></span>
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-body">

            <!-- BARRA: ACTUALIZAR + ÚLTIMA ACTUALIZACIÓN -->
            <div class="card p-2 mb-2">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-1">
                    <span class="text-muted">
                        Última actualización: <strong id="last_update">--:--:--</strong>
                    </span>
                    <button type="button" id="btn_actualizar" class="btn btn-primary">Actualizar</button>
                </div>
            </div>

            <!-- TRES COLUMNAS -->
            <div class="row">

                <div class="col-md-4">
                    <div class="card p-2">
                        <h4 class="card-title text-center mb-2 text-danger">Pendientes</h4>
                        <div id="col_pendientes" class="kitchen-col"></div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card p-2">
                        <h4 class="card-title text-center mb-2 text-warning">En preparación</h4>
                        <div id="col_preparacion" class="kitchen-col"></div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card p-2">
                        <h4 class="card-title text-center mb-2 text-success">Listos</h4>
                        <div id="col_listos" class="kitchen-col"></div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
<!-- END: Content -->

<style>
    .container-fluid {
        width: 100%;
        padding: 0;
        margin: 0;
    }

    .content-wrapper {
        padding-left: 0px;
        padding-right: 0px;
    }

    .form-label {
        font-weight: bold;
    }

    .breadcrumb-wrapper {
        margin-top: 10px;
    }

    .kitchen-col {
        min-height: 300px;
        max-height: 550px;
        overflow-y: auto;
    }
</style>
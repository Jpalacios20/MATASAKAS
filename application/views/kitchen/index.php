<!-- BEGIN: Content -->
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>

    <div class="content-wrapper container-xxl p-0">

        <div class="content-header row mb-1">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-start mb-0">Cocina</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-12 text-md-end">
                <small class="text-muted">Se actualiza cada 15 s · <span id="kitchen_updated">--:--:--</span></small>
            </div>
        </div>

        <div class="content-body">
            <div class="row">

                <div class="col-lg-4 mb-2">
                    <h5 class="text-warning">Pendientes <span class="badge bg-warning" id="count_pendiente">0</span></h5>
                    <div id="col_pendiente"></div>
                </div>

                <div class="col-lg-4 mb-2">
                    <h5 class="text-info">En preparación <span class="badge bg-info" id="count_preparacion">0</span></h5>
                    <div id="col_preparacion"></div>
                </div>

                <div class="col-lg-4 mb-2">
                    <h5 class="text-primary">Listos <span class="badge bg-primary" id="count_listo">0</span></h5>
                    <div id="col_listo"></div>
                </div>

            </div>
        </div>

    </div>
</div>
<!-- END: Content -->


<style>
    .kitchen-card {
        border-left: 5px solid #ccc;
    }
    .kitchen-card.st-PENDIENTE { border-left-color: #ff9f43; }
    .kitchen-card.st-EN_PREPARACION { border-left-color: #00cfe8; }
    .kitchen-card.st-LISTO { border-left-color: #7367f0; }
    .kitchen-late { color: #ea5455; font-weight: bold; }
</style>
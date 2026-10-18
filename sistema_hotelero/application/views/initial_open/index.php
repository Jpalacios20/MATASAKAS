<!-- BEGIN: Content -->
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper container-xxl p-0">
        <div class="content-header row"></div>
        <div class="content-body">
            <!-- Users Starts -->
            <section id="clients">
                <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">


                <!-- Header title -->
                <div class="content-header row">
                    <div class="content-header-left col-md-9 col-12 mb-2">
                        <div class="row breadcrumbs-top">
                            <div class="col-12">
                                <h2 class="content-header-title mb-0">Apertura de caja</h2>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow rounded-4" id="initial_open_modal">
                    <div class="card-body">
                        <form id="form_initial_open" method="post">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">📅 Fecha y hora de apertura</label>
                                    <input type="datetime-local" class="form-control" name="fecha_hora_apertura" id="fecha_hora_apertura" value="<?php echo date('Y-m-d\TH:i'); ?>" required>
                                </div>
                                <div class="form-group col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                    <label>Usuario</label>
                                    <select class="form-control selectpicker" name="id_user" id="idusuario" data-live-search="true" required>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">💰 Monto inicial</label>
                                    <input type="number" step="0.01" class="form-control" name="monto_inicial" id="monto_inicial" placeholder="S/ 0.00" required>
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label">📝 Observaciones</label>
                                    <textarea class="form-control" name="observaciones" id="observaciones" rows="2" placeholder="Ejemplo: caja recibida con sencillo"></textarea>
                                </div>

                                <div class="d-grid mt-3">
                                    <button type="submit" id="btn_initial_open" class="btn btn-success btn-lg rounded-pill">Confirmar Apertura</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
<!-- END: Content -->
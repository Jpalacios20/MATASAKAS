<!-- BEGIN: Content-->
<div class="app-content content">
  <div class="content-overlay"></div>
  <div class="header-navbar-shadow"></div>
  <div class="content-wrapper container-xxl p-0">
    <div class="content-header row">
    </div>
    <div class="content-body">
      <!-- Report Cash Section -->
      <section id="report-cash">
        <!-- Header title -->
        <div class="content-header row">
          <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
              <div class="col-12">
                <h2 class="content-header-title float-start mb-0">Reporte de Caja</h2>
                <div class="breadcrumb-wrapper">
                  <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Reportes</a></li>
                    <li class="breadcrumb-item active">Reporte de Caja</li>
                  </ol>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- /Header title -->

        <!-- Filters -->
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-body">
                <div class="row">
                  <!-- Fecha inicio -->
                  <div class="form-group col-lg-3 col-md-6 col-sm-12">
                    <label for="fecha_inicio">Fecha inicio</label>
                    <input type="date" class="form-control" name="fecha_inicio" id="fecha_inicio" value="<?php echo date("Y-m-d"); ?>">
                  </div>

                  <!-- Fecha fin -->
                  <div class="form-group col-lg-3 col-md-6 col-sm-12">
                    <label for="fecha_fin">Fecha fin</label>
                    <input type="date" class="form-control" name="fecha_fin" id="fecha_fin" value="<?php echo date("Y-m-d"); ?>">
                  </div>
                  
                  <!-- Usuario -->
                  <div class="form-group col-lg-4 col-md-6 col-sm-12">
                    <label>Usuario</label>
                    <select class="form-control selectpicker" name="id_user" id="idusuario" data-live-search="true">
                      <option value="">Todos los usuarios</option>
                    </select>
                  </div>

                  <!-- Botones -->
                  <div class="form-group col-lg-2 col-md-12 col-sm-12 d-flex align-items-end">
                    <div class="d-grid gap-2 w-100">
                      <button class="btn btn-success waves-effect waves-float waves-light" onclick="listar();">
                        <i class="fas fa-search me-1"></i> Mostrar
                      </button>
                      <button class="btn btn-danger waves-effect waves-float waves-light" onclick="resetear();">
                        <i class="fas fa-times me-1"></i> Limpiar
                      </button>
                    </div>
                  </div>

                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- /Filters -->

        <!-- Table -->
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-body">
                <div class="table-responsive">
                  <table id="tbllistado" class="table table-striped table-bordered table-condensed table-hover">
                    <thead>
                      <tr>
                        <th>ID</th>
                        <th>Fecha Apertura</th>
                        <th>Usuario</th>
                        <th>Monto Inicial</th>
                        <th>Ingresos</th>
                        <th>Egresos</th>
                        <th>Monto Final</th>
                        <th>Estado</th>
                        <th>Fecha Cierre</th>
                        <th>Observaciones</th>
                      </tr>
                    </thead>
                    <tbody></tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- /Table -->

        <!-- Totales -->
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-body">
                <div class="row">
                  <div class="col-md-3">
                    <div class="alert alert-info text-center">
                      <strong>Total Ingresos</strong><br>
                      <span id="total_ingresos">0.00</span> Soles
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="alert alert-warning text-center">
                      <strong>Total Egresos</strong><br>
                      <span id="total_egresos">0.00</span> Soles
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="alert alert-success text-center">
                      <strong>Total Final</strong><br>
                      <span id="total_final">0.00</span> Soles
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="alert alert-primary text-center">
                      <strong>Registros</strong><br>
                      <span id="total_registros">0</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- /Totales -->
      </section>
      <!-- /Report Cash Section -->
    </div>
  </div>
</div>
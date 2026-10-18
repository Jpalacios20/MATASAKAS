<div class="app-content content">
  <div class="content-overlay"></div>
  <div class="header-navbar-shadow"></div>
  <div class="content-wrapper container-xxl p-0">
    <div class="content-header row">
    </div>
    <div class="content-body">
      <!-- Report Billing Section -->
      <section id="report-billing">
        <!-- Header title -->
        <div class="content-header row">
          <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
              <div class="col-12">
                <h2 class="content-header-title float-start mb-0">Consulta mensual de facturas</h2>
                <div class="breadcrumb-wrapper">
                  <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Reportes</a></li>
                    <li class="breadcrumb-item active">Consulta facturas</li>
                  </ol>
                </div>
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
                  <div class="form-group col-lg-4 col-md-4 col-sm-6 col-xs-12">
                    <label>Usuario</label>
                    <select class="form-control selectpicker" name="idusuario" id="idusuario" data-live-search="true" required>
                    </select>
                  </div>

                  <!-- Módulo -->
                  <div class="form-group col-lg-2 col-md-6 col-sm-12">
                    <label for="modulo"></label>
                    <select class="form-control selectpicker" name="modulo" id="modulo" required>
                      <option value="0"></option>
                      <option value="1"></option>
                      <option value="2"></option>
                    </select>
                  </div>

                  <!-- Botón Mostrar -->
                  <div class="form-group col-lg-2 col-md-6 col-sm-12">
                    <label>&nbsp;</label>
                    <button class="btn btn-success form-control" onclick="listar();">Mostrar</button>
                  </div>
                  <div class="form-group col-lg-2 col-md-6 col-sm-12">
                    <label>&nbsp;</label>
                    <button class="btn btn-danger form-control" onclick="resetear();">Limpiar</button>
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
                        <th>Fecha</th>
                        <th>Cliente</th>
                        <th>Comprobante</th>
                        <th>Serie-Correlativo</th>
                        <th>Total Venta</th>
                        <th>Estado</th>
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

        <!-- Venta Total -->
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-body text-center">
                <div class="alert alert-danger" style="font-size: large; font-weight: 500;">
                  <label>Venta Total del usuario </label>
                  <span id="usuari">usuario</span>
                  <p>
                    <span id="sumventa">0.00</span><span> Soles</span>
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- /Venta Total -->
      </section>
      <!-- /Report Billing Section -->
    </div>
  </div>
</div>

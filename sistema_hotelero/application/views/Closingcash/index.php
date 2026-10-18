<!-- BEGIN: Content-->
    <div class="app-content content ">
      <div class="content-overlay"></div>
      <div class="header-navbar-shadow"></div>
      <div class="content-wrapper container-xxl p-0">
        <div class="content-header row">
          <!-- Header title -->
          <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
              <div class="row breadcrumbs-top">
                <div class="col-12">
                  <h2 class="content-header-title float-start mb-0">Cierre de caja</h2>
                  <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="#"><?php echo $selected_menu; ?></a>
                      </li>
                      <li class="breadcrumb-item active"><span><?php echo $selected_sub_menu; ?></span>
                      </li>
                    </ol>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="content-body">
          <!-- Campus Starts -->


          <!-- Header title -->

          <!-- /Header table-->

          <!-- Table -->
                    <!-- /Header table-->

                    <div class="card p-3">
                        <div class="row">
                            <h2 class="mb-1">Cierre de caja actual:</h2>
                            <div class="mb-1 col-10">
                                <form method="POST" id="closing_cash_data" class="row" onsubmit="return false">
                                    <div class="col-12">
                                        <label class="form-label">Usuario</label>
                                        <input type="text" name="employee" class="form-control" readonly/>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label class="form-label">📅 Fecha y hora de apertura</label>
                                        <input type="datetime-local" class="form-control" name="start" 
                                               id="fecha_hora_apertura" readonly>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">📅 Fecha y hora de cierre</label>
                                        <input type="datetime-local" class="form-control" name="end" 
                                               id="fecha_hora_cierre" value="<?php echo date('Y-m-d\TH:i'); ?>" readonly>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label">💰 Monto Inicial</label>
                                        <input type="number" name="monto_inicial" class="form-control" readonly/>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label">💰 Ingresos</label>
                                        <input type="number" name="income" class="form-control" 
                                               step="0.01" min="0" placeholder="0.00" required/>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label">💰 Egresos</label>
                                        <input type="number" name="expenses" class="form-control" 
                                               step="0.01" min="0" value="0" placeholder="0.00" required/>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label">📝 Observaciones</label>
                                        <textarea name="notes" class="form-control" 
                                                  placeholder="Ingrese observaciones del cierre..." required></textarea>
                                    </div>

                                    <div class="col-12">
                                        <button class="btn btn-primary mt-2" id="btn_closing_cash" type="submit">
                                            Cerrar Caja
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

        </div>
      </div>
    </div>
    <!-- END: Content-->

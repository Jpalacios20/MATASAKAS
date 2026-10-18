<!-- BEGIN: Content-->
    <div class="app-content content ">
      <div class="content-overlay"></div>
      <div class="header-navbar-shadow"></div>
      <div class="content-wrapper container-xxl p-0">
        <div class="content-header row">
          <!-- Header title -->
          <div class="content-header row w-100 m-0">
            <div class="content-header-left col-md-9 col-12 mb-2">
              <div class="row breadcrumbs-top">
                <div class="col-12">
                  <h2 class="content-header-title float-start mb-0">Ventas</h2>
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
            <!-- Botón de Nuevo Registro agregado a la derecha -->
            <div class="content-header-right text-md-end col-md-3 col-12 d-md-block d-none">
              <div class="form-group breadcrumb-right">
                <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#sales_modal">
                  <i data-feather="plus"></i> Nuevo Registro
                </button>
              </div>
            </div>
          </div>
        </div>
        <div class="content-body">
          <!-- Campus Starts -->


          <!-- Header title -->

          <!-- /Header table-->

          <!-- Table -->
          <!-- Table -->
          <div class="row">
            <div class="col-12">
                <div class="card">
                    <table class="table table-responsive" id="datatable-Product">
                    <thead>
                           <tr>
                            <th>Fecha Check-in</th>
                            <th>Cliente</th>
                            <th>N° Habitación</th>
                            <th>Hora Check-in</th>
                            <th>Fecha Check-out</th>
                            <th>Hora Check-out</th>
                            <th>Costo</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                           </tr>
                    </thead>
                        <tbody></tbody> <!-- Asegúrate de tener un <tbody> aquí -->
                    </table>
                </div>
            </div>
        </div>

          <!-- /Table -->

          <!--/ Update Categories Modal -->




          <!--  -->

          <!--  -->
















          <div class="modal fade" id="sales_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
            <div class="modal-dialog modal-xl modal-dialog-centered">
              <div class="modal-content mx-auto">
                <div class="modal-header bg-transparent">
                  <button type="reset" class="btn-close reset" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-sm-5 pb-5">
                  <nav>
                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                      <button class="nav-link active" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-home" type="button" role="tab" aria-controls="nav-home" aria-selected="true">
                        Detalles
                      </button>
                      <button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-profile" type="button" role="tab" aria-controls="nav-profile" aria-selected="false">
                        Ventas
                      </button>
                      <button class="nav-link" id="nav-history-tab" data-bs-toggle="tab" data-bs-target="#nav-history" type="button" role="tab" aria-controls="nav-history" aria-selected="false">
                        Historial de Ventas
                      </button>
                    </div>
                  </nav>
                  <div class="tab-content" id="nav-tabContent">
                    <!-- Details Modal -->
                    <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                      <form method="POST" enctype="multipart/form-data" id="create_sales_form" class="row" onsubmit="return false">
                        <div class="text-center mb-2">
                          <h1 class="mb-1 mt-4">Datos de Reservas</h1>
                        </div>
                        <!--  nombres agregados -->
                        <input type="hidden" name="id_reservation">
                        <input type="hidden" name="id_payment">
                        <!-- -->
                        <div class="col-12">
                          <label class="form-label">Número de Habitación</label>
                          <input type="text" name="room_number" class="form-control" data-msg="" required />
                        </div>
                        <div class="col-12">
                          <label class="form-label">Tipo de Habitación</label>
                          <input type="text" name="type_name" class="form-control" data-msg="" required />
                        </div>
                        <div class="col-12">
                          <label class="form-label">Tipo de Documento</label>
                          <input type="text" name="document_type" class="form-control" data-msg="" required />
                        </div>
                        <div class="col-12">
                          <label class="form-label">Numero de Documento</label>
                          <input type="text" name="document_number" class="form-control" data-msg="" required />
                        </div>
                        <div class="col-12" id="first_names">
                          <label class="form-label">Nombre</label>
                          <input type="text" name="first_names" class="form-control" />
                        </div>
                        <div class="col-12" id="last_names">
                          <label class="form-label">Apellido</label>
                          <input type="text" name="last_names" class="form-control" data-msg="" />
                        </div>
                        <div class="col-12">
                          <label class="form-label">Dirección</label>
                          <input type="text" name="address" class="form-control" data-msg="" />
                        </div>
                        <div class="col-12" id="company_names">
                          <label class="form-label">Razón Social</label>
                          <input type="text" name="company_name" class="form-control" data-msg="" />
                        </div>
                      </form>
                    </div>

                    <!-- Sales Modals -->
                    <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                      <form method="POST" enctype="multipart/form-data" id="sales_form" class="row" onsubmit="return false">
                        <div class="col-12 text-center">
                          <h1 class="mb-1 mt-4">Ventas</h1>
                          <button class="btn btn-primary my-2" data-bs-toggle="modal" data-bs-target="#create_income_product_modal">Agregar productos</button>
                        </div>
                        <div class="col-12">
                          <div class="row">
                            <div class="col-12">
                              <div class="card">
                                <table class="table p-2" id="add_products">
                                <form method="POST" enctype="multipart/form-data" id="reservation_payment_form" class="row" onsubmit="return false">
                                  <div>
                                    <input type="hidden" name="id_reservation" />
                                    <input type="hidden" name="id_payment" />
                                  </div>
                                </form>
                                  <thead>
                                    <tr>
                                      <th>Acciones</th>
                                      <th>Producto</th>
                                      <th>Cantidad</th>
                                      <th>Precio</th>
                                      <th>Descuento</th>
                                      <th>Total</th>
                                    </tr>
                                  </thead>
                                  <tfoot style="font-size: 1.8rem;">
                                    <tr>
                                      <th></th>
                                      <th></th>
                                      <th></th>
                                      <th></th>
                                      <th>Total</th>
                                      <th>
                                        <span class="text-center" id="total-sales-price">S/ 00.00</span>
                                      </th>
                                    </tr>
                                  </tfoot>
                                </table>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-12">
                          <button class="btn btn-primary mt-2 me-2" id="btn_create_guest_reservation" type="submit">Agregar Venta</button>
                          <button type="reset" class="btn btn-outline-secondary mt-2 reset" data-bs-dismiss="modal" aria-label="Close">
                            <span>Cancelar</span>
                          </button>
                        </div>
                      </form>
                    </div>

                    <div class="tab-pane fade" id="nav-history" role="tabpanel" aria-labelledby="nav-history-tab">
                      <div class="col-12 text-center">
                        <h1 class="mb-1 mt-4">Historial de Ventas</h1>
                      </div>
                      <div class="col-12">
                        <table class="table" id="datatable-sales">
                          <thead>
                            <tr>
                              <th>Fecha</th>
                              <th>Producto</th>
                              <th>Cantidad</th>
                              <th>Precio Unitario</th>
                              <th>Total</th>
                            </tr>
                          </thead>
                          <tfoot style="font-size: 1.8rem;">
                                    <tr>
                                      <th></th>
                                      <th></th>
                                      <th></th>
                                      <th></th>
                                      <th></th>
                                      <th>Total</th>
                                      <th>
                                        <span class="text-center" id="total-sales-history">S/ 00.00</span>
                                      </th>
                                    </tr>
                                  </tfoot>
                          <tbody id="sales_history_body">
                            <!-- Aquí se cargará el historial dinámicamente -->
                          </tbody>
                        </table>
                      </div>
                    </div>


                  </div>
                </div>
              </div>
            </div>
          </div>



          <!-- reservation  button modal -->

                     <div class="modal fade" id="update_reservation_modal" data-bs-backdrop="static" data-bs-target="#" data-bs-keyboard="false" tabindex="-1">
                        <div class="modal-dialog modal-fullscreen-lg-down modal-xl modal-dialog-centered">
                            <div class="modal-content mx-auto">
                                <div class="modal-header bg-transparent">
                                    <div aria-label="breadcrumb">
                                        <ol class="breadcrumb">
                                            <li class="breadcrumb-item">Reserva</li>
                                            <li class="breadcrumb-item active idReservation" aria-current="page"></li>
                                        </ol>
                                    </div>

                                    <button type="reset" class="btn-close reset cancel_update" style="margin-bottom: 2px;" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body px-sm-5 pb-5">
                                
                                    <!-- Pestañas -->
                                    <ul class="nav nav-tabs" id="tabContenido">
                                        <li class="nav-item">
                                            <a class="nav-link active" data-bs-toggle="tab" href="#modal-detalles">Detalles</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-bs-toggle="tab" href="#modal-modificacon">Modificación</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-bs-toggle="tab" href="#modal-salida">Salida y pago</a>
                                        </li>
                                    </ul>

                                    <!-- Contenido de las pestañas -->
                                    <div class="tab-content">

                                        <!-- Cont 1 -->
                                        <div class="tab-pane fade show active col-12" id="modal-detalles">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="card col-12">
                                                        <div class="card-header text-muted">Datos hospedaje</div>
                                                        <div class="card-body">
                                                            <p class="lead fw-bold">Entrada:</p>
                                                            <p class="card-text mb-2 detalle-entrada"></p>
                                                            <p class="lead fw-bold">Salida:</p>
                                                            <p class="card-text mb-2 detalle-salida"></p>
                                                            <p class="lead fw-bold">Tiempo estimado:</p>
                                                            <p class="card-text mb-2 detalle-tiempo"></p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="card col-12">
                                                        <div class="card-header text-muted">Información de la habitación</div>
                                                        <div class="card-body">
                                                            <p class="lead fw-bold">Número de habitación:</p>
                                                            <p class="card-text mb-2 detalle-numH"></p>
                                                            <p class="lead fw-bold">Tipo de habitación:</p>
                                                            <p class="card-text mb-2 detalle-tipoH"></p>
                                                            <p class="lead fw-bold">Tipo cama:</p>
                                                            <p class="card-text mb-2 detalle-tipoC"></p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="card">
                                                        <div class="card-header text-muted">Información del huésped</div>
                                                        <div class="card-body d-flex flex-wrap">
                                                            <div class="col-md-6 mb-1 mb-lg-0" id="salida-huesped">
                                                                <p class="lead fw-bold huesped-dni">Nombres:</p>
                                                                <p class="card-text mb-2 detalle-nombres huesped-dni"></p>
                                                                <p class="lead fw-bold huesped-dni">Apellidos:</p>
                                                                <p class="card-text mb-2 detalle-apellidos huesped-dni"></p>
                                                                <p class="lead fw-bold huesped-ruc">Razón social:</p>
                                                                <p class="card-text mb-2 detalle-razonSc huesped-ruc"></p>
                                                                <p class="lead fw-bold">Dirección:</p>
                                                                <p class="card-text mb-2 detalle-direccion"></p>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <p class="lead fw-bold">Tipo documento:</p>
                                                                <p class="card-text mb-2 detalle-tipoDoc"></p>
                                                                <p class="lead fw-bold">Número de documento:</p>
                                                                <p class="card-text mb-2 detalle-numDoc"></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Cont 2 -->
                                        <div class="tab-pane fade" id="modal-modificacon">
                                            <!-- ROOM -->
                                            <div class="col-12 mb-2 d-flex flex-wrap justify-content-center">
                                                <h1 class="text-center mb-1">Habitación <span class="fs-4" id="numRoom"></span></h1>       
                                            </div>
                                            <!--  -->
                                            <form method="POST" enctype="multipart/form-data" id="update_reservation_form" class="row" onsubmit="return false">
                                                <div class="col-6 col-lg-4">
                                                    <label class="form-label">Tipo de Habitación</label>
                                                    <input type="text" name="type_room" class="form-control" placeholder="Tipo de Habitación" autofocus data-msg="" disabled />
                                                </div>
                                                <div class="col-6 col-lg-4">
                                                    <label class="form-label">Tipo de Cama</label>
                                                    <input type="text" name="bed_name" class="form-control" placeholder="Tipo de Cama" data-msg="" disabled />
                                                </div>
                                                <div class="col-12 col-lg-4">
                                                    <label class="form-label">Límite de Personas</label>
                                                    <input type="text" name="person_limit" class="form-control" placeholder="Límite de Personas" data-msg="" disabled />
                                                </div>
                                                <div class="col-12 col-lg-6">
                                                    <label class="form-label">Fecha de Inicio</label>
                                                    <input type="date" name="checkin_date" class="form-control" placeholder="Fecha Inicio" data-msg="" required />
                                                </div>
                                                <div class="col-12 col-lg-6">
                                                    <label class="form-label">Fecha de Termino</label>
                                                    <input type="date" name="checkout_date" class="form-control" placeholder="Fecha Final" data-msg="" required />
                                                </div>
                                                <div class="col-12 col-lg-6">
                                                    <label class="form-label">Hora de Inicio</label>
                                                    <input type="time" name="checkin_time" class="form-control" placeholder="Fecha Inicio" data-msg="" required />
                                                </div>
                                                <div class="col-12 col-lg-6">
                                                    <label class="form-label">Hora de Termino</label>
                                                    <input type="time" name="checkout_time" class="form-control" placeholder="Fecha Final" data-msg="" required />
                                                </div>
                                                <div class="col-12 col-lg-6">
                                                    <label class="form-label">Estado de Habitación</label>
                                                    <select name="room_status" id="mySelect" class="form-select">
                                                        <option value="Ocupado">
                                                            Ocupado
                                                        </option>
                                                        <option value="Reservado">
                                                            Reservado
                                                        </option>
                                                        <option value="Libre">
                                                            Libre
                                                        </option>
                                                    </select>
                                                </div>
                                                <div class="col-12 col-lg-6">
                                                    <label class="form-label">Precio Habitación</label>
                                                    <input type="number" name="payment_room" class="form-control" placeholder="Precio" data-msg="" required/>
                                                </div>

                                                <!--  -->
                                                <input type="hidden" name="id_payment">
                                                <input type="hidden" name="id_reservation">
                                                <input type="hidden" name="id_room">
                                                <br>
                                                <div class="col-12 text-center mt-2">
                                                    <button id="btn_update_reservation" type="submit" class="btn btn-primary mt-2 me-1">Guardar Reserva</button>
                                                    <button type="reset" class="btn btn-outline-secondary mt-2 reset cancel_update" data-bs-dismiss="modal" aria-label="Close">
                                                        <span>Cancelar</span>
                                                    </button>
                                                </div>
                                            </form>
                                        </div>

                                        <!-- Cont 3 -->
                                        <div class="tab-pane fade" id="modal-salida">
                                            <form method="POST" enctype="multipart/form-data" id="departure_reservation_form" class="row" onsubmit="return false">
                                                <div class="row">
                                                    <div class="card p-1">
                                                        <p class="text-muted">Información</p>
                                                        <!-- info -->
                                                        <div class="row mb-2">
                                                            <div class="col-md-4 d-flex flex-wrap align-items-end gap-2 mb-1">
                                                                <p class="card-subtitle fw-bold">Entrada:</p>
                                                                <span class="card-text detalle-entrada"></span>
                                                            </div>
                                                            <div class="col-md-4 d-flex flex-wrap align-items-end gap-2 mb-1">
                                                                <p class="card-subtitle fw-bold mx">Salida:</p>
                                                                <span class="card-text detalle-salida"></span>
                                                            </div>
                                                            <div class="col-md-4 d-flex flex-wrap align-items-end gap-2 mb-1">
                                                                <p class="card-subtitle fw-bold">Habitación:</p>
                                                                <span class="card-text detalle-numH"></span>
                                                                <span class="card-text detalle-tipoH"></span>
                                                            </div>
                                                            <div class="col-md-4 d-flex flex-wrap align-items-end gap-2 mb-1 huesped-dni">
                                                                <p class="card-subtitle fw-bold huesped-dni">Apellidos:</p>
                                                                <span class="card-text detalle-apellidos huesped-dni"></span>
                                                            </div>
                                                            <div class="col-md-4 d-flex flex-wrap align-items-end gap-2 mb-1 huesped-dni">
                                                                <p class="card-subtitle fw-bold huesped-dni">Nombres:</p>
                                                                <span class="card-text detalle-nombres huesped-dni"></span>
                                                            </div>
                                                            <div class="col-md-8 d-flex flex-wrap align-items-end gap-2 mb-1 huesped-ruc">
                                                                <p class="card-subtitle fw-bold huesped-ruc">Razón social:</p>
                                                                <span class="card-text detalle-razonSc huesped-ruc"></span>
                                                            </div>
                                                            <div class="col-md-4 d-flex flex-wrap align-items-end gap-2 mb-1">
                                                                <p class="card-subtitle fw-bold">Documento:</p>
                                                                <span class="card-text detalle-numDoc"></span>
                                                            </div>
                                                        </div>
                                                        <!-- payment -->
                                                        <div class="row">
                                                            <p class="text-muted">Costo de alojamiento</p>
                                                            <div class="col-sm-4 mb-1">
                                                                Monto habitación<input type="number" name="payment_room" class="form-control" placeholder="0,00" data-msg="" required/>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                     </div>
      </div>
    </div>
</div>
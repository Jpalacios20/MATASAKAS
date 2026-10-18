
    <!-- BEGIN: Content-->
    <div class="app-content content ">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper container-xxl p-0">
            <style>

            @media (max-width: 767.98px) {

                /* 1. CONTROL GENERAL DEL TEXTO */
                /* Reduce el tamaño de fuente de todo el calendario para que los datos quepan en pantallas estrechas */
                #calendar {
                    font-size: 11px !important;
                    
                }

                /* 2. ADAPTABILIDAD DE LA REJILLA */
                /* Fuerza al contenedor principal a ajustar su altura dinámicamente según el contenido */
                /* Asegura que el área de vista del calendario sea visible */
                .fc .fc-view-harness {
                    height: auto !important;
                    min-height: 400px !important;
                }

                /* Asegura que la tabla del calendario ocupe exactamente el 100% del ancho disponible sin salirse */
                .fc .fc-scrollgrid {
                    border: 1px solid #ddd !important;
                    width: 100% !important;
                }

                /* 3. CABECERAS DE LOS DÍAS (lun, mar, mié...) */
                /* Reduce el espacio interno (padding) de las letras de los días y los centra para que no se encima el texto */
                .fc-col-header-cell-cushion {
                    padding: 4px 1px !important;
                    font-weight: 600;
                    text-transform: capitalize;
                    display: block;
                    text-align: center;
                }

                /* 4. NÚMEROS DE LOS DÍAS EN EL MES */
                /* Quita los alineados flotantes nativos y centra los números de los días dentro de su celda cuadrada */
                .fc-daygrid-day-number {
                    padding: 2px 4px !important;
                    float: none !important;
                    text-align: center;
                    display: block;
                }

                /* 5. BARRA SUPERIOR DE FULLCALENDAR (Flechas, Título, Botones de vista) */
                /* Cambia la distribución de horizontal a vertical (columna). Apila los elementos para evitar que choquen */
                .fc .fc-toolbar {
                    flex-direction: column !important;
                    gap: 8px !important;
                    margin-bottom: 1rem !important;
                }

                /* Reduce ligeramente el tamaño del título del mes (ej: "julio de 2026") y lo centra perfectamente */
                .fc .fc-toolbar-title {
                    font-size: 1.3rem !important;
                    text-align: center;
                }

                /* Reduce el relleno interno y la fuente de los botones [Mes] [Semana] [Día] para que se vean compactos */
                .fc .fc-button {
                    padding: 0.4rem 0.6rem !important;
                    font-size: 11px !important;
                }

                /* 6. OPTIMIZACIÓN DE ESPACIOS DE LA TARJETA CONTENEDORA */
                /* Reduce los márgenes blancos internos de la Card de Bootstrap para regalarle más píxeles útiles al calendario */
                .card-body,
                #reservations .card {
                    padding: 0.6rem !important;
                }

                /* 7. CONTROL DE FILTROS SUPERIORES (Filtro de Estado y Selector de Vista) */
                /* Al igual que la barra, los fuerza a ponerse uno abajo del otro en pantallas medianas y chicas */
                .d-flex.justify-content-between.align-items-center {
                    flex-direction: column !important;
                    gap: 12px !important;
                    align-items: center !important;
                }

                /* Asegura que los contenedores de los filtros se expandan a lo ancho y mantengan sus elementos centrados */
                .d-flex.justify-content-between.align-items-center>div {
                    width: 100%;
                    text-align: center;
                    display: flex;
                    justify-content: center;
                }
        </style>
            <div class="content-header row">
            </div>
            <div class="content-body">
                <!-- Reservations Starts -->
                <section id="reservations">
                    
                    <!-- Header title -->
                    <div class="content-header row">
                        <div class="content-header-left col-md-8 col-12 mb-2">
                            <div class="row breadcrumbs-top"> 
                                <div class="col-12">
                                <h2 class="content-header-title float-start mb-0">Lista de <?php echo strtolower($selected_sub_menu); ?></h2>
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
                    <!-- /Header title-->

                    <!-- Container -->
                    <div class="row">
                        <div class="col-12">
                            <div id="container_calendar" class="card p-2">
                                <div class="col-12 mb-2">
                                <div class="d-grid d-md-flex justify-content-md-center gap-2" style="grid-template-columns: 1fr 1fr;">
                                    <div class="d-flex align-items-center gap-1">
                                        <p class="mb-0">Finalizado</p>
                                        <div id="guiaColorF" class="rounded" style="width: 20px; height: 20px; background:#FF7AD9;"></div>
                                        </div>
                                        <div class="d-flex align-items-center gap-1">
                                        <p class="mb-0">Reservado</p>
                                        <div id="guiaColorR" class="rounded" style="width: 20px; height: 20px;background:#FF7AD9;"></div>
                                        </div>
                                       <div class="d-flex align-items-center gap-2">
                                        <p class="mb-0">Ocupado</p>
                                        <div id="guiaColorO" class="rounded" style="width: 20px; height: 20px; background:#FF7AD9;"></div>
                                        </div>
                                        <div class="d-flex align-items-center gap-1">
                                        <p class="mb-0">Libre</p>
                                        <div id="guiaColorL" class="rounded" style="width: 20px; height: 20px; background:#FF7AD9; "></div>
                                        <div class="d-flex align-items-center gap-1">
                                        <p class="mb-0">Pendiente</p>
                                        <div id="guiaColorP" class="rounded" style="width: 20px; height: 20px; background:#FF7AD9;"></div>
                                     </div>
                                    </div>
                                </div>
                                <div id="filter" class="d-flex flex-wrap align-items-center mb-2 col-12 justify-content-center">
                                    <div class="form-inline d-flex align-items-center flex-wrap flex-md-nowrap col-12 ">
                                        <h4 class="mb-0 me-2">Mostrar</h4>
                                        <div class="form-inline d-flex align-items-center flex-wrap flex-sm-nowrap gap-1">
                                            <h6 class="mb-0 ">Finalizados</h6>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckChecked">
                                            </div>
                                            <h6 class="mb-0">con</h6>   
                                            <select class="form-select" id="viewMode" style="min-width: 130px;">
                                                <option value="0" selected>Todos</option>
                                                <option value="Ocupado">Ocupados</option>
                                                <option value="Reservado">Reservados</option>
                                                <option value="Libre">Libre</option>
                                                <option value="Pendiente">Pendientes</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div id='calendar'></div>
                            </div>
                        </div>
                    </div>
                    <!-- /Table -->

                    <!-- Update Permission Modal -->
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
                                                        <!-- table sales -->
                                                        <!-- <p class="text-muted">Costo de las ventas</p>
                                                        <div class="container overflow-auto mb-2">
                                                            <div class="col-12">
                                                                <table class="table" id="datatable-sales">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Descripción</th> 
                                                                            <th>Precio Unitario</th>         
                                                                            <th>Cantidad</th>
                                                                            <th>Estado</th> 
                                                                            <th>Importe</th> 
                                                                            
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody id="tbody-sales">
                                                                        <tr>
                                                                            <td>Agua</td>
                                                                            <td>S/ 1,20</td>
                                                                            <td>2</td>
                                                                            <td class="text-success">Pagado</td>
                                                                            <td>S/ 2,40</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td colspan="4"><b>Total</b></td>
                                                                            <td><b>S/ 2,40</b></td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div> -->
                                                        <!-- payment -->
                                                        <div class="row">
                                                            <p class="text-muted">Costo de alojamiento</p>
                                                            <div class="col-sm-4 mb-1">
                                                                Monto haitación<input type="number" name="payment_room" class="form-control" placeholder="0,00" data-msg="" disabled required/>
                                                            </div>
                                                            <div class="col-sm-4 mb-1">
                                                                Monto extra<input type="number" name="payment_extra" class="form-control" placeholder="0,00" data-msg="" required/>
                                                            </div>
                                                            <div class="col-sm-4 mb-1">
                                                                Sub total<input type="number" name="payment_subTotal" class="form-control text-end" placeholder="0,00" data-msg="" disabled required/>
                                                            </div>
                                                            <div class="col-sm-4 offset-sm-8">
                                                                Descuento <input type="number" name="payment_discount" class="form-control text-end" placeholder="0,00" data-msg=""/>
                                                                <hr>
                                                            </div>
                                                            
                                                            <div class="col-sm-4 col-6 mb-1">
                                                                Adelanto<input type="number" name="payment_cancelled" class="form-control" placeholder="0,00" data-msg="" required/>
                                                            </div>
                                                            <div class="col-sm-4 col-6 mb-1">
                                                                Falta Pagar<input type="number" name="payment_lack" class="form-control" placeholder="0,00" data-msg="" disabled/>
                                                            </div>
                                                            <div class="col-sm-4 mb-1">
                                                                Tota a pagar<input type="number" name="payment_total" class="form-control text-end" data-msg=""/>
                                                            </div>
                                                            <div class="col-6 col-lg-4 mt-2">
                                                                Fecha Salida<input type="date" name="departure_date" class="form-control" placeholder="Fecha Final" data-msg=""/>
                                                            </div>
                                                            <div class="col-6 col-lg-4 mt-2">
                                                                Hora Salida<input type="time" name="departure_time" class="form-control" placeholder="Fecha Inicio" data-msg=""/>
                                                            </div>
                                                            <div class="col-lg-4 mt-2 text-center">
                                                                <button id="departure_btn" type="button" class="btn btn-secondary mt-2 me-1">Marcar salida</button>
                                                            </div>
                                                        </div>
                                                        
                                                        <!-- buttons -->
                                                        <input type="hidden" name="id_reservation">
                                                        <input type="hidden" name="id_payment">
                                                        <input type="hidden" name="id_room">
                                                        <div class="col-12 text-center my-2">
                                                            <button id="btn_update_payment" type="submit" class="btn btn-primary mt-2 me-1">Guardar cambios</button>
                                                            <button type="reset" class="btn btn-outline-secondary mt-2 reset" data-bs-dismiss="modal" aria-label="Close">
                                                                <span>Cancelar</span>
                                                            </button>
                                                            <button id="btn_finish_reservation" type="button" class="btn btn-danger mt-2 mx-1">Finalizar Reserva</button>
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
                    <!--/ Update Permission Modal -->


                    <!-- Completed Modal -->
                    <div class="modal fade" id="completed_modal" data-bs-backdrop="static" data-bs-target="#" data-bs-keyboard="false" tabindex="-1">
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

                                    <!-- Contenido de las pestañas -->
                                    <div class="tab-content">

                                        <!-- Cont 1 -->
                                        <div class="tab-pane fade show active col-12" id="modal-detalles">
                                            <!-- Completed -->
                                            <div class="row">
                                                <div class="card p-2 mt-2">
                                                    <!-- Info -->
                                                    <div class="row mb-2">
                                                        <div class="col-md-6 mb-1 mb-lg-0">
                                                            <p class="text-muted">Hospedaje</p>
                                                            <p><span class="card-subtitle fw-bold">Entrada: </span><span class="card-text detalle-entrada"></span></p>
                                                            <p><span class="card-subtitle fw-bold mx">Salida: </span><span class="card-text detalle-salida"></span></p>
                                                            <p><span class="card-subtitle fw-bold mx">Tiempo estimado:</span><span class="card-text detalle-tiempo"></span></p>
                                                        </div>
                                                        <div class="col-md-6 mb-1 mb-lg-0">
                                                            <p class="text-muted">Habitación</p>
                                                            <p><span class="card-subtitle fw-bold">Número: </span><span class="card-text detalle-numH"></span></p>
                                                            <p><span class="card-subtitle fw-bold">Tipo: </span><span class="card-text detalle-tipoH"></span></p>
                                                            <p><span class="card-subtitle fw-bold">Cama: </span><span class="card-text detalle-tipoC"></span></p>
                                                        </div>
                                                        <p class="text-muted mt-1">Huésped</p>
                                                        <div class="col-md-6 mb-lg-0">
                                                            <p><span class="fw-bold huesped-dni">Nombres: </span><span class="card-text mb-1 detalle-nombres"></span></p>
                                                            <p><span class="fw-bold huesped-dni">Apellidos: </span><span class="card-text mb-1 detalle-apellidos"></span></p>
                                                            <p><span class="fw-bold huesped-ruc">Razon social: </span><span class="card-text mb-1 detalle-razonSc"></span></p>
                                                            <p><span class="fw-bold">Dirección: </span><span class="card-text mb-1 detalle-direccion"></span></p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <p><span class="fw-bold">Tipo documento: </span><span class="card-text mb-1 detalle-tipoDoc"></span></p>
                                                            <p><span class="fw-bold">Número de documento: </span><span class="card-text mb-1 detalle-numDoc"></span></p>
                                                        </div>

                                                    <!-- Table sales -->
                                                    <!-- <p class="text-muted mt-2">Costo de las ventas</p> -->
                                                    <!-- <div class="container overflow-auto mb-2"> -->
                                                        <!-- <div class="table-responsive-lg mb-3">
                                                            <table class="table table-bordered" id="datatable-sales">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Descripción</th> 
                                                                        <th>Precio Unitario</th>         
                                                                        <th>Cantidad</th>
                                                                        <th>Estado</th> 
                                                                        <th>Importe</th> 
                                                                        
                                                                    </tr>
                                                                </thead>
                                                                <tbody id="tbody-sales">
                                                                    <tr>
                                                                        <td>Agua</td>
                                                                        <td>S/ 1,20</td>
                                                                        <td>2</td>
                                                                        <td class="text-success">Pagado</td>
                                                                        <td>S/ 2,40</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td colspan="4"><b>Total</b></td>
                                                                        <td><b>S/ 2,40</b></td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div> -->
                                                    <!-- </div> -->
                                                    <!-- Payment -->
                                                    <p class="text-muted mt-1">Pagos</p>
                                                    <p><span class="card-subtitle fw-bold mx-1">Salida del huésped: </span><span class="card-text detalle-salidaF mx-1"></span><span class="card-text detalle-salidaH mx-1"></span></p>
                                                    <table class="table table-bordered table-responsive">
                                                        <tbody>
                                                            <tr>
                                                                <td><p class="lead fw-bold">Pago Habitación: </p><span class="card-text detalle-pagoHab"></span></td>
                                                                <td><p class="lead fw-bold">Pago Extra: </p><span class="card-text detalle-pagoExtra"></span></td>
                                                                <td><p class="lead fw-bold">Sub Total: </p><span class="card-text detalle-subTotal"></span></td>
                                                            </tr>
                                                            <tr>
                                                                <th colspan="2" class="text-end">Descuento</th>
                                                                <td><span class="card-text detalle-descuento"></span></td>
                                                            </tr>
                                                            <tr>
                                                                <th colspan="2" class="text-end">Total</th>
                                                                <td><span class="card-text detalle-totalPagar"></span></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                    <!-- <div class="row">
                                                        <p class="text-muted">Pagos</p>
                                                        <div class="col-md-6 mb-1 mb-lg-0">
                                                            <p><span class="card-subtitle fw-bold">Salida: </span><span class="card-text detalle-salida"></span></p>
                                                            <p><span class="card-subtitle fw-bold mx">Pago Habitación: </span><span class="card-text detalle-pagoHab"></span></p>
                                                            <p><span class="card-subtitle fw-bold mx">Pago Extra: </span><span class="card-text detalle-pagoExtra"></span></p>
                                                        </div>
                                                        <div class="col-md-6 mb-1 mb-lg-0">
                                                            <p><span class="card-subtitle fw-bold">Sub Total: </span><span class="card-text detalle-subTotal"></span></p>
                                                            <p><span class="card-subtitle fw-bold">Descuento: </span><span class="card-text detalle-descuento"></span></p>
                                                            <p><span class="card-subtitle fw-bold">Total a pagar: </span><span class="card-text detalle-totalPagar"></span></p>
                                                        </div>
                                                    </div> -->

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <!--/ Completed Modal -->

                </section>
                <!-- Reservations ends -->

            </div>
        </div>
    </div>
    <!-- END: Content-->
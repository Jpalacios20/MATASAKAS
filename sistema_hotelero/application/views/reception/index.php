<!-- BEGIN: Content-->
<div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper container-xxl p-0">
            <div class="content-header row">
                <div class="content-header row">
                    <div class="content-header-left col-md-9 col-12 mb-2">
                        <div class="row breadcrumbs-top">
                            <div class="col-12">
                                <h2 class="content-header-title float-start mb-0">Lista de Reservas</h2>
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
            <!--Mostrar numero de habitaciones-->
            <div class="col-12">
                <div class="card pb-3">
                    <div class="card-header my-2 d-flex gap-2 justify-content-center align-items-center flex-wrap">
                        <div class="d-flex justify-content-center align-items-center mx-2">
                            <select id="btn_room_flat" class="form-select px-2 me-2" style="min-width: 180px;">
                                <option value="0">Todos los pisos</option>
                            </select>
                            <select name="room_status" id="btn_room_status" class="form-select px-2">
                                <option value="0">Todos</option>
                                <option value="Disponible">Disponible</option>
                                <option value="Ocupado">Ocupado</option>
                                <option value="Reservado">Reservado</option>
                                <option value="Limpieza">Limpieza</option>
                                <!--
                                <option value="Libre">Libre</option>
-->
                            </select>
                        </div>
                        <div class="d-flex justify-content-center align-items-center gap-2 flex-wrap">
                            <div class="d-flex justify-content-center align-items-center gap-1">
                                <div class="rounded" style="width: 20px; height: 20px; background:#FC912A;"></div>
                                Reservado
                            </div>
                            <div class="d-flex justify-content-center align-items-center gap-1">
                                <div class="rounded" style="width: 20px; height: 20px; background:#EA5455;"></div>
                                Ocupado
                            </div>
                            <div class="d-flex justify-content-center align-items-center gap-1">
                                <div class="rounded" style="width: 20px; height: 20px; background:#00C1FF;"></div>
                                Limpieza
                            </div>
                            <div class="d-flex justify-content-center align-items-center gap-1">
                                <div class="rounded" style="width: 20px; height: 20px; background:#28C66F;"></div>
                                Disponible
                            </div>
                            <!--
                            <div class="d-flex justify-content-center align-items-center gap-1">
                                <div class="rounded" style="width: 20px; height: 20px; background:#517FFF;"></div>
                                Libre
                            </div>
-->
                        </div>
                        <div class="d-flex justify-content-center align-items-center">
                            <!-- Botón para agregar cliente -->
                            <button id="btn_add_client" class="btn btn-primary">
                                <i class="bi bi-person-plus"></i> Agregar Cliente
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap justify-content-center align-items-center gap-2" id="data-container">
                            <!-- Aquí se mostrarán tus datos -->
                        </div>
                        <div id="pagination"></div>
                    </div>
                </div>
            </div>
            <!--Crear Reserva de habitaciones-->

            <!-- Header title -->
            <div class="modal fade" id="update_habitacion_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
<div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-transparent">
                            <button type="reset" class="btn-close reset" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body px-sm-5 pb-5">
                            <form method="POST" enctype="multipart/form-data" id="create_reservation_form" class="row" onsubmit="return false">
                                <div class="text-center mb-2">
                                    <h1 class="mb-1">Habitación</h1>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Numero de Habitación</label>
                                    <input type="text" name="room_number" class="form-control" placeholder="Numero de Habitación" autofocus data-msg="" disabled />
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Tipo de Habitación</label>
                                    <input type="text" name="type_name" id="type_name" class="form-control" placeholder="Tipo de Habitación" autofocus data-msg="" disabled />
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Límite de Personas</label>
                                    <input type="text" name="person_limit" class="form-control" placeholder="Límite de Personas" data-msg="" disabled />
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Tipo de Cama</label>
                                    <input type="text" name="bed_type" class="form-control" placeholder="Tipo de Cama" data-msg="" disabled />
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Estado</label>
                                    <select name="room_status" class="form-select" id="room_status_reservation" required>
                                        <option value="0" selected>
                                            Seleccione estado de la habitacion
                                        </option>
                                        <option value="Ocupado">
                                            Ocupado
                                        </option>
                                        <option value="Reservado">
                                            Reservado
                                        </option>
                                        <!--
                                        <option value="Libre">
                                            Libre
                                        </option>
-->
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Tipo de Documento</label>
                                    <select class="form-select" id="client_document_type" required>
                                        <option value="0" selected disabled>
                                            Tipo de Documento
                                        </option>
                                        <option value="DNI">
                                            DNI
                                        </option>
                                        <option value="RUC">
                                            RUC
                                        </option>
                                    </select>
                                </div>
<div class="col-12 mb-1">
    <label class="form-label">Número de Documento</label>
    <input type="text" name="document_number" class="form-control mb-1" id="document_number_reservation" placeholder="Ingrese número de documento" required>
    
    <div class="d-flex gap-1">
        <button type="button" class="btn btn-info text-white flex-grow-1" id="btn_buscar_bd">
            <i class="fa fa-database"></i> Consultar BD
        </button>
        <button type="button" class="btn btn-primary flex-grow-1" id="btn_buscar_api">
            <i class="fa fa-globe"></i> Consultar API
        </button>
    </div>
</div>


                                <div class="col-12" id="searchForm">
    <label>Cliente:</label>
    <select class="form-select select2 mb-1 opcionesSelect" name="id_guest" id="id_guest" required>
        <option value="">Seleccionar un resultado</option>
    </select>
        <!-- Nuevo botón Guardar en BD -->
    <div class="d-grid gap-2 mt-1">
        <button type="button" class="btn btn-success" id="btn_guardar_bd">
            <i class="fa fa-save"></i> Guardar BD
        </button>
    </div>
</div>

                                <div class="col-12">
                                    <label class="form-label">Fecha de Inicio</label>
                                    <input type="date" name="checkin_date" class="form-control" id="fechaInicio" required />                                </div>
                                <div class="col-12">
                                    <label class="form-label">Hora de Inicio</label>
                                    <input type="time" name="checkin_time" class="form-control" id="horaInicio" data-msg="" required />
                                </div>
                                <div class="col-12 checkout_date">
                                    <label class="form-label">Fecha de Termino</label>
                                    <input type="date" name="checkout_date" class="form-control" id="fechaFin"/>                       
                                </div>
                                <div class="col-12 checkout_time">
                                    <label class="form-label">Hora de Termino</label>
                                    <input type="time" name="checkout_time" class="form-control" id="horaFin" data-msg="" />
                                </div>
                                <!-- <div class="col-12 mb-2">
                                    <label class="form-label mt-2">Accesorios de Cortesía</label>
                                    <table class="table table-responsive table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Descripción</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Papel Higienico <input type="text" value="1.und" class="form-control" readonly></td>
                                            </tr>
                                            <tr>
                                                <td>Jabon <input type="text" value="1.und" class="form-control" readonly></td>
                                            </tr>
                                            <tr>
                                                <td>Toalla <input type="text" value="1.und" class="form-control" readonly></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div> -->
                                <div class="pre_payment">
                                    <label class="form-label">Monto Total</label>
                                    <input type="text" name="payment_room" class="form-control" data-msg="">
                                </div>
                                <div class="all_payment">
                                    <label class="form-label d-flex pt-1 gap-1">Pago adelantado</label>
                                    <input type="text" name="pre_payment" class="form-control" data-msg="" id="pre_payment" value="0.00">
                                </div>
                                <div class="col-12 pt-1">
                                    <label class="form-label">
                                        <div class="d-flex align-items-center gap-1">
                                            Estado de Reserva
                                            <i class="fa-solid fa-circle-info cursor-pointer text-danger bs-popover bs-popover-bottom" data-bs-trigger="hover focus" style="font-size: 1.2rem;" data-bs-content="Si no hay ningun pago adelantado, el estado de la reserva debe de ser pendiente."></i>
                                        </div>
                                    </label>
                                    <select name="status" class="form-select" id="status">
                                        <option value="0" selected>Seleccione el estado de la reserva</option>
                                        <option value="Pendiente">Pago Pendiente</option>
                                        <option value="Reservado">Pago Confirmado</option>
                                        <option value="Ocupado">Reserva Ocupada</option>
                                        <option value="Libre">Pago Libre</option>
                                    </select>
                                </div>
                                <input type="hidden" name="id_room">
                                <div class="col-12">
                                    <button class="btn btn-primary mt-2 me-2 btn-update-room-reservation" id="btn_create_reservation" type="submit">Agregar Reserva</button>
                                    <button type="reset" class="btn btn-outline-secondary mt-2 reset" data-bs-dismiss="modal" aria-label="Close">
                                        <span>Cancelar</span>
                                    </button>
                                </div>
                                <br>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!--Crear Reserva de habitaciones-->



            <!-- Header title -->
            <div class="modal fade" id="create_guest_reservation_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
                <div class="modal-dialog modal-xl modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-transparent">
                            <button type="reset" class="btn-close reset" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body px-sm-5 pb-5">
                            <form method="POST" enctype="multipart/form-data" id="create_guest_reservation_form" class="row" onsubmit="return false">
                                <div class="text-center mb-2">
                                    <h1 class="mb-1 mt-4">Huespéd</h1>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Tipo de Documento</label>
                                    <select name="document_type" class="form-select" id="client_document" required>
                                        <option value="0" selected>
                                            Seleccione el tipo de documento
                                        </option>
                                        <option value="DNI">
                                            DNI
                                        </option>
                                        <option value="RUC">
                                            RUC
                                        </option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Numero de Documento</label>
                                    <input type="text" name="document_number" class="form-control" placeholder="Busca el numero de documento" data-msg="" id="document_number" required />
                                </div>
                                <div class="col-12">
                                    <button class="btn btn-primary mt-2 me-2 mb-2" id="buscar_huesped">Consultar</button>
                                </div>
                                <div class="col-12" id="div_nombre">
                                    <label class="form-label" for="nombre">Nombre</label>
                                    <input type="text" name="first_names" class="form-control" placeholder="Nombre" id="nombre" />
                                </div>
                                <div class="col-12" id="div_apellido">
                                    <label class="form-label">Apellido</label>
                                    <input type="text" name="last_names" class="form-control" placeholder="Apellido" data-msg="" id="apellido" />
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Dirección</label>
                                    <input type="text" name="address" class="form-control" placeholder="Dirección" data-msg="" id="direccion" />
                                </div>
                                <div class="col-12" id="div_razon_social">
                                    <label class="form-label">Razón Social</label>
                                    <input type="text" name="company_name" class="form-control" placeholder="Razón Social" data-msg="" id="razon_social" />
                                </div>
                                <div class="col-12">
                                    <button class="btn btn-primary mt-2 me-2" id="btn_create_guest_reservation" type="submit">Agregar Huespéd</button>
                                    <button type="reset" class="btn btn-outline-secondary mt-2 reset" data-bs-dismiss="modal" aria-label="Close">
                                        <span>Cancelar</span>
                                    </button>
                                </div>
                                <br>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
            <!--Crear Reserva de habitaciones-->


            <div class="modal fade" id="timer_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
<div class="modal-dialog modal-sm modal-dialog-centered">
                    <div class="modal-content mx-auto">
                        <div class="modal-header bg-transparent">
                            <button type="reset" class="btn-close reset" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body px-sm-5 pb-5">
                            <form method="POST" enctype="multipart/form-data" id="create_timer_form" class="row" onsubmit="return false">
                                <div class="text-center mb-2">
                                    <h1 class="mb-1 mt-4">Sala de espera</h1>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Estado de Habitación</label>
                                    <select name="room_status" class="form-select" required>

                                    <option value="Disponible" selected>
                                            Disponible
                                        </option>

                                      
                                        <option value="Ocupado">
                                            Ocupado
                                        </option>
                                        <option value="Reservado">
                                            Reservado
                                        </option>
                                
                                    
                                        <option value="Limpieza">
                                            Limpieza
                                        </option>
                                    </select>
                                </div>
                                <!--
                                <div class="col-12">
                                    <label class="form-label">Lista de Reservas</label>
                                    <select class="form-select select2 mb-1 optionReservation" name="id_reservation" required>
                                        <option value="0">Seleccionar un resultado</option>
                                    </select>
                                </div>
                                -->

                                <!--Nuevo div vreado  -->
                                <!--(borrado)
                                <div class="col-12">
                                    <label for="reservation_id" class="form-label">Seleccionar ID de Reservación</label>
                                    <select id="reservation_id" class="form-select" name="id_reservation" required>
                                        <option value="">Seleccione una reservación</option>
                                    </select>
                                </div>
-->
                                <div class="col-12">
                                    <label for="id_room" class="form-label">ID de Habitación</label>
                                    <input type="text" id="id_room" class="form-control" name="id_room" readonly >
                                </div>
                                <!-- -->



                                <!--
                                <div class="col-12">
                                    <label for="" class="form-label">
                                        Número de Habitación
                                    </label>
                                    <input type="text" class="form-control" name="room_number" readonly>
                                </div>
                               
                                <div class="col-12 hidden">
                                    <label for="" class="form-label">
                                        ID de Habitacion
                                    </label>
                                    <input type="text" class="form-control" name="id_room">
                                </div>
                                <div class="col-12 hidden">
                                    <label for="" class="form-label">
                                        Tipo de Habitación
                                    </label>
                                    <input type="text" class="form-control" name="type_name">
                                </div>
                                -->
                                <div class="col-12">
                                    <button class="btn btn-primary mt-2 me-2" id="btn_remove_reservation" onclick="update_state(this.form)" type="submit">Actualizar Reserva</button>
                                    <button type="reset" class="btn btn-outline-secondary mt-2 reset" data-bs-dismiss="modal" aria-label="Close">
                                        <span>Cancelar</span>
                                    </button>
                                </div>
                                <br>
                            </form>

                        </div>
                    </div>
                </div>
            </div>



            <div class="modal fade" id="clean_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
                <div class="modal-dialog modal-sm modal-dialog-centered">
                    <div class="modal-content mx-auto">
                        <div class="modal-header bg-transparent">
                            <button type="reset" class="btn-close reset" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body px-sm-5 pb-5">
                            <form method="POST" enctype="multipart/form-data" id="clean_form" class="row" onsubmit="return false">
                                <div class="text-center mb-2">
                                    <h2 class="mb-1 mt-4">Desea terminar la  limpieza de esta habitación</h2>
                                </div>
                                <input type="hidden" name="id_room">
                                <div class="col-12 d-flex justify-content-center">
                                    <button class="btn btn-primary mt-2 me-2" id="btn_clean_rooms" type="submit">Si Terminar</button>
                                    <button type="reset" class="btn btn-outline-secondary mt-2 reset" data-bs-dismiss="modal" aria-label="Close">
                                        <span>Cancelar</span>
                                    </button>
                                </div>
                                <br>
                            </form>

                        </div>
                    </div>
                </div>
            </div>


<!-- Modal -->
<div class="modal fade" id="errorModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
                <div class="modal-dialog modal-sm modal-dialog-centered">
                    <div class="modal-content mx-auto">
                        <div class="modal-header bg-danger ">
                            <h5 class="modal-title text-light" id="errorModalLabel">Error</h5>
                            <button type="reset" class="btn-close reset" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body px-sm-5 py-3 d-flex justify-content-center align-items-center">
                            <div class="text-center">
                                No se encontraron los datos. Por favor, llene los campos manualmente.
                            </div>
                        </div>  
                        <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
                    </div>
                </div>
            </div>


            <div class="content-body">
            </div>
        </div>
    </div>

    

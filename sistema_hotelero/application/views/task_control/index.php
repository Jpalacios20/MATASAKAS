<!-- BEGIN: Content -->
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper container-xxl p-0">
        <div class="content-header row">
            <!-- Header title -->
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-start mb-0">Registro de tareas de Alumnos</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="#"><?php echo $selected_menu; ?></a>
                                </li>
                                <li class="breadcrumb-item active">
                                    <span><?php echo $selected_sub_menu; ?></span>
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-body">
            <!-- Tabla de productos -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <table class="table table-responsive" id="datatable-task_control">
                            <thead>
                                <tr>
                                    <th>Fecha de ingreso</th>
                                    <th>Fecha Inicio</th>
                                    <th>Fecha Termino</th>
                                    <th>Estudiante</th>
                                    <th>Nombre Proyecto</th>
                                    <th>Estado servicio</th>
                                    <th>Estado pago</th>
                                    <th>Saldo a pagar</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
             <!-- Modal para Registro de Servicios de Desarrollo -->
             <div class="modal fade" id="create_task_control_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-transparent">
                            <button type="reset" class="btn-close reset" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body px-sm-4 pb-4">
                            <div class="text-center mb-3">
                                <h1 class="mb-2">Registro de Servicios de Desarrollo</h1>
                            </div>
                            <form id="create_task_control_form" class="row g-1" onsubmit="return false">
                                <div class="col-12">
                                    <h5 class="mb-0">Datos del Estudiante</h5>
                                </div>
                                <div class="col-12 col-md-6 mb-0">
                                    <label class="form-label">Estudiantes</label>
                                    <select name="nombres_apellidos" id="estudiantesSelect" class="form-select select2" data-msg="" required></select>
                                </div>
                                <div class="col-12 col-md-6 mb-0">
                                    <label class="form-label">Nº de documento</label>
                                    <input name="documento" type="text" class="form-control">
                                </div>
                                <div class="col-12 col-md-6 mb-0">
                                    <label class="form-label mb-0">Sede</label>
                                    <input name="sede" type="text" class="form-control">
                                </div>
                                <div class="col-12 col-md-6 mb-0">
                                    <label class="form-label mb-0">Teléfono</label>
                                    <input name="telefono" type="text" class="form-control">
                                </div>

                                <div class="col-12 mt-1">
                                    <h5 class="mb-0">Datos del Proyecto</h5>
                                </div>
                                <div class="col-12 col-md-6 mb-0">
                                    <label class="form-label mb-0">Nombre Proyecto</label>
                                    <input type="text" class="form-control form-control-sm" placeholder="Nombre del proyecto" required />
                                </div>
                                <div class="col-12 col-md-6 mb-0">
                                    <label class="form-label mb-0">Costo del Desarrollo: Total: S/</label>
                                    <input type="text" class="form-control form-control-sm" placeholder="Monto" required />
                                </div>

                                <div class="col-12 mt-1">
                                    <h5 class="mb-0">Estados</h5>
                                </div>
                                <div class="col-12 col-md-4 mb-0">
                                    <label class="form-label mb-0">Estado - Servicio</label>
                                    <select class="form-control form-control-sm" name="estado_servicio" required> 
                                        <option value="">Seleccionar</option>
                                        <option value="Pendiente">Pendiente</option>
                                        <option value="En Proceso">En Proceso</option>
                                        <option value="Completado">Completado</option>
                                    </select>
                                </div>
                                <div class="col-12 col-md-4 mb-0">
                                    <label class="form-label mb-0">Estado - Entrega</label>
                                    <select class="form-control form-control-sm" name="estado_entrega" required> 
                                        <option value="">Seleccionar</option>
                                        <option value="Pendiente">Pendiente</option>
                                        <option value="Entregado">Entregado</option>
                                    </select>
                                </div>
                                <div class="col-12 col-md-4 mb-0">
                                    <label class="form-label mb-0">Estado - Pago</label>
                                    <select class="form-control form-control-sm" name="estado_pago" required> 
                                        <option value="">Seleccionar</option>
                                        <option value="Pendiente">Pendiente</option>
                                        <option value="Pagado">Pagado</option>
                                        <option value="Vencido">Vencido</option>
                                    </select>
                                </div>

                                <div class="col-12 mt-1">
                                    <h5 class="mb-0">Integrantes</h5>
                                </div>
                                <div class="col-12 mb-0">
                                    <div class="input-group">
                                        <input type="text" id="dniIntegrante" class="form-control form-control-sm" placeholder="Agregar Integrantes (DNI)" required />
                                        <button id="agregarIntegranteBtn" class="btn btn-outline-primary btn-sm" type="button">
                                            <i data-feather="plus"></i>
                                        </button>
                                    </div>
                                </div>

                                <div id="integrantesAgregados" class="mt-2">
                                    </div>
                                <div class="col-12 text-center mt-3">
                                <button id="btn_save_task_control" type="submit" class="btn btn-primary btn-sm mt-2 me-1" aria-label="Guardar datos del formulario">
                                    <i data-feather="save"></i> Guardar 
                                </button>
                                    <button type="reset" class="btn btn-outline-secondary btn-sm mt-2 reset" data-bs-dismiss="modal">
                                        Cancelar
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Modal Registro de Servicios de Desarrollo -->


            <div class="modal fade" id="process_development_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header d-flex justify-content-between align-items-center">
                            <h3 class="modal-title fs-5">Proceso de Desarrollo</h3>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="process_development_form" onsubmit="return false">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="client_name" class="form-label">Nombre del Estudiante:</label>
                                        <input type="text" class="form-control" id="estudiantes" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="project_name" class="form-label">Nombre del Proyecto:</label>
                                        <input type="text" class="form-control" id="project_name" readonly> 
                                    </div>
                                </div>

                                <!-- ANÁLISIS -->
                                <h5>ANÁLISIS</h5>
                                <div class="row process-row align-items-end">
                                    <div class="col-md-2">
                                        <label>Fecha de Inicio:</label>
                                        <input type="date" class="form-control fecha-inicio">
                                    </div>
                                    <div class="col-md-2">
                                        <label>Fecha de Término:</label>
                                        <input type="date" class="form-control fecha-termino">
                                    </div>
                                    <div class="col-md-2">
                                        <label>Días Restantes:</label>
                                        <input type="text" class="form-control dias-restantes" readonly>
                                    </div>
                                    <div class="col-md-2">
                                        <label>Día Actual:</label>
                                        <div class="progress">
                                            <div class="progress-bar progreso-dia-actual" role="progressbar" style="width: 0%;">0%</div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <label>Estado:</label>
                                        <select class="form-select">
                                            <option value="pendiente">Pendiente</option>
                                            <option value="en_progreso">En Progreso</option>
                                            <option value="completado">Completado</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label>Comentario:</label>
                                        <textarea class="form-control" rows="1"></textarea>
                                    </div>
                                </div>
                                <hr>

                                <!-- DISEÑO -->
                                <h5>DISEÑO</h5>
                                <div class="row process-row align-items-end">
                                    <div class="col-md-2">
                                        <label>Fecha de Inicio:</label>
                                        <input type="date" class="form-control fecha-inicio">
                                    </div>
                                    <div class="col-md-2">
                                        <label>Fecha de Término:</label>
                                        <input type="date" class="form-control fecha-termino">
                                    </div>
                                    <div class="col-md-2">
                                        <label>Días Restantes:</label>
                                        <input type="text" class="form-control dias-restantes" readonly>
                                    </div>
                                    <div class="col-md-2">
                                        <label>Día Actual:</label>
                                        <div class="progress">
                                            <div class="progress-bar progreso-dia-actual" role="progressbar" style="width: 0%;">0%</div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <label>Estado:</label>
                                        <select class="form-select">
                                            <option value="pendiente">Pendiente</option>
                                            <option value="en_progreso">En Progreso</option>
                                            <option value="completado">Completado</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label>Comentario:</label>
                                        <textarea class="form-control" rows="1"></textarea>
                                    </div>
                                </div>
                                <hr>

                                <!-- DESARROLLO -->
                                <h5>DESARROLLO</h5>
                                <div class="row process-row align-items-end">
                                    <div class="col-md-2">
                                        <label>Fecha de Inicio:</label>
                                        <input type="date" class="form-control fecha-inicio">
                                    </div>
                                    <div class="col-md-2">
                                        <label>Fecha de Término:</label>
                                        <input type="date" class="form-control fecha-termino">
                                    </div>
                                    <div class="col-md-2">
                                        <label>Días Restantes:</label>
                                        <input type="text" class="form-control dias-restantes" readonly>
                                    </div>
                                    <div class="col-md-2">
                                        <label>Día Actual:</label>
                                        <div class="progress">
                                            <div class="progress-bar progreso-dia-actual" role="progressbar" style="width: 0%;">0%</div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <label>Estado:</label>
                                        <select class="form-select">
                                            <option value="pendiente">Pendiente</option>
                                            <option value="en_progreso">En Progreso</option>
                                            <option value="completado">Completado</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label>Comentario:</label>
                                        <textarea class="form-control" rows="1"></textarea>
                                    </div>
                                </div>
                                <hr>

                                <!-- IMPLEMENTACIÓN -->
                                <h5>IMPLEMENTACIÓN</h5>
                                <div class="row process-row align-items-end">
                                    <div class="col-md-2">
                                        <label>Fecha de Inicio:</label>
                                        <input type="date" class="form-control fecha-inicio">
                                    </div>
                                    <div class="col-md-2">
                                        <label>Fecha de Término:</label>
                                        <input type="date" class="form-control fecha-termino">
                                    </div>
                                    <div class="col-md-2">
                                        <label>Días Restantes:</label>
                                        <input type="text" class="form-control dias-restantes" readonly>
                                    </div>
                                    <div class="col-md-2">
                                        <label>Día Actual:</label>
                                        <div class="progress">
                                            <div class="progress-bar progreso-dia-actual" role="progressbar" style="width: 0%;">0%</div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <label>Estado:</label>
                                        <select class="form-select">
                                            <option value="pendiente">Pendiente</option>
                                            <option value="en_progreso">En Progreso</option>
                                            <option value="completado">Completado</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label>Comentario:</label>
                                        <textarea class="form-control" rows="1"></textarea>
                                    </div>
                                </div>
                                <hr>

                                <h5>MANTENIMIENTO</h5>
                                <div class="row process-row align-items-end">
                                    <div class="col-md-2">
                                        <label>Fecha de Inicio:</label>
                                        <input type="date" class="form-control fecha-inicio-mantenimiento">
                                    </div>
                                    <div class="col-md-2">
                                        <label>Fecha de Término:</label>
                                        <input type="date" class="form-control fecha-termino-mantenimiento">
                                    </div>
                                    <div class="col-md-2">
                                        <label>Días Restantes:</label>
                                        <input type="text" class="form-control dias-restantes-mantenimiento" readonly>
                                    </div>
                                    <div class="col-md-2">
                                        <label>Día Actual:</label>
                                        <div class="progress">
                                            <div class="progress-bar progreso-dia-actual-mantenimiento" role="progressbar" style="width: 0%;">0%</div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <label>Estado:</label>
                                        <select class="form-select estado-mantenimiento">
                                            <option value="pendiente">Pendiente</option>
                                            <option value="en_progreso">En Progreso</option>
                                            <option value="completado">Completado</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label>Comentario:</label>
                                        <textarea class="form-control comentario-mantenimiento" rows="1"></textarea>
                                    </div>
                                </div>
                
                                <div class="col-12 text-center">
                                    <button id="btn_save_task_control" type="submit" class="btn btn-primary btn-sm mt-2 me-1" aria-label="Guardar datos del formulario">
                                        <i data-feather="save"></i> Guardar Datos
                                    </button>
                                        <button type="reset" class="btn btn-outline-secondary btn-sm mt-2 reset" data-bs-dismiss="modal">
                                            Cancelar
                                        </button>
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
<!-- END: Content -->

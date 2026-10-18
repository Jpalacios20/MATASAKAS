<!-- BEGIN: Content -->

<div class="app-content content">

    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>

    <div class="content-wrapper container-xxl p-0">

        <!-- ===================================================== -->
        <!-- CABECERA -->
        <!-- ===================================================== -->

        <div class="content-header row mb-1">

            <div class="content-header-left col-md-9 col-12 mb-2">

                <div class="row breadcrumbs-top">

                    <div class="col-12">

                        <h2 class="content-header-title float-start mb-0">
                            Mesas
                        </h2>

                        <div class="breadcrumb-wrapper">

                            <ol class="breadcrumb">

                                <li class="breadcrumb-item">
                                    <a href="#">
                                        Restaurante
                                    </a>
                                </li>

                                <li class="breadcrumb-item active">
                                    Mesas
                                </li>

                            </ol>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        <!-- ===================================================== -->
        <!-- CONTENIDO -->
        <!-- ===================================================== -->

        <div class="content-body">

            <div class="card">

                <!-- ================================================= -->
                <!-- CABECERA -->
                <!-- ================================================= -->

                <div class="card-header">

                    <div>

                        <h4 class="card-title mb-0">
                            Mesas del Restaurante
                        </h4>

                        <small class="text-muted">
                            Administra las mesas y su estado.
                        </small>

                    </div>

                    <button
                        type="button"
                        id="btn_new_table"
                        class="btn btn-primary">

                        <i data-feather="plus"></i>

                        Nueva Mesa

                    </button>

                </div>


                <!-- ================================================= -->
                <!-- FILTROS -->
                <!-- ================================================= -->

                <div class="card-body border-bottom">

                    <div class="row">

                        <div class="col-md-4 mb-1">

                            <label class="form-label">
                                Buscar mesa
                            </label>

                            <input
                                type="text"
                                id="search_table"
                                class="form-control"
                                placeholder="Ej: M01">

                        </div>


                        <div class="col-md-4 mb-1">

                            <label class="form-label">
                                Estado
                            </label>

                            <select
                                id="filter_status"
                                class="form-select">

                                <option value="TODAS">
                                    Todas
                                </option>

                                <option value="LIBRE">
                                    Libre
                                </option>

                                <option value="OCUPADA">
                                    Ocupada
                                </option>

                                <option value="RESERVADA">
                                    Reservada
                                </option>

                                <option value="INACTIVA">
                                    Inactiva
                                </option>

                            </select>

                        </div>

                    </div>

                </div>


                <!-- ================================================= -->
                <!-- CONTENEDOR DE MESAS -->
                <!-- ================================================= -->

                <div class="card-body">

                    <div
                        id="tables_container"
                        class="row g-2">

                        <div class="col-12 text-center py-5">

                            <div
                                class="spinner-border text-primary"
                                role="status">

                            </div>

                            <div class="mt-2 text-muted">
                                Cargando mesas...
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<!-- END: Content -->


<!-- ============================================================= -->
<!-- MODAL NUEVA / EDITAR MESA -->
<!-- ============================================================= -->

<div
    class="modal fade"
    id="modal_table"
    tabindex="-1"
    aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <!-- HEADER -->

            <div class="modal-header">

                <h5
                    class="modal-title"
                    id="modal_table_title">

                    Nueva Mesa

                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Cerrar">
                </button>

            </div>


            <!-- BODY -->

            <div class="modal-body">

                <input
                    type="hidden"
                    id="table_id"
                    value="">


                <!-- NOMBRE -->

                <div class="mb-2">

                    <label class="form-label">
                        Nombre / Número de Mesa
                    </label>

                    <input
                        type="text"
                        id="table_name"
                        class="form-control"
                        placeholder="Ej: M01">

                </div>


                <!-- CAPACIDAD -->

                <div class="mb-2">

                    <label class="form-label">
                        Cantidad de personas
                    </label>

                    <input
                        type="number"
                        id="table_seats"
                        class="form-control"
                        min="1"
                        value="2">

                </div>


                <!-- ESTADO -->

                <div class="mb-2">

                    <label class="form-label">
                        Estado
                    </label>

                    <select
                        id="table_status"
                        class="form-select">

                        <option value="LIBRE">
                            Libre
                        </option>

                        <option value="OCUPADA">
                            Ocupada
                        </option>

                        <option value="RESERVADA">
                            Reservada
                        </option>

                        <option value="INACTIVA">
                            Inactiva
                        </option>

                    </select>

                </div>

            </div>


            <!-- FOOTER -->

            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-outline-secondary"
                    data-bs-dismiss="modal">

                    Cancelar

                </button>

                <button
                    type="button"
                    id="btn_save_table"
                    class="btn btn-primary">

                    <i data-feather="save"></i>

                    Guardar Mesa

                </button>

            </div>

        </div>

    </div>

</div>


<!-- ============================================================= -->
<!-- ESTILOS -->
<!-- ============================================================= -->

<style>

    /* ========================================================= */
    /* TARJETA DE MESA */
    /* ========================================================= */

    .table-card {

        border: 1px solid #ebe9f1;

        border-radius: 10px;

        padding: 20px;

        background: #ffffff;

        transition: all .2s ease;

        height: 100%;

        position: relative;

    }


    .table-card:hover {

        transform: translateY(-3px);

        box-shadow: 0 5px 15px rgba(0, 0, 0, .08);

    }


    /* ========================================================= */
    /* NOMBRE */
    /* ========================================================= */

    .table-number {

        font-size: 28px;

        font-weight: 700;

        margin-bottom: 5px;

    }


    /* ========================================================= */
    /* CAPACIDAD */
    /* ========================================================= */

    .table-seats {

        color: #6e6b7b;

        font-size: 14px;

    }


    /* ========================================================= */
    /* ICONO */
    /* ========================================================= */

    .table-icon {

        width: 55px;

        height: 55px;

        border-radius: 50%;

        display: flex;

        align-items: center;

        justify-content: center;

        margin: 0 auto 12px;

    }


    .table-icon svg {

        width: 28px;

        height: 28px;

    }


    /* ========================================================= */
    /* ESTADO LIBRE */
    /* ========================================================= */

    .table-card.libre {

        border-top: 4px solid #28c76f;

    }


    .table-icon.libre {

        background: #e8f8ef;

        color: #28c76f;

    }


    .status-badge.libre {

        background: #e8f8ef;

        color: #28c76f;

    }


    /* ========================================================= */
    /* ESTADO OCUPADA */
    /* ========================================================= */

    .table-card.ocupada {

        border-top: 4px solid #ea5455;

    }


    .table-icon.ocupada {

        background: #fde8e8;

        color: #ea5455;

    }


    .status-badge.ocupada {

        background: #fde8e8;

        color: #ea5455;

    }


    /* ========================================================= */
    /* ESTADO RESERVADA */
    /* ========================================================= */

    .table-card.reservada {

        border-top: 4px solid #ff9f43;

    }


    .table-icon.reservada {

        background: #fff1e5;

        color: #ff9f43;

    }


    .status-badge.reservada {

        background: #fff1e5;

        color: #ff9f43;

    }


    /* ========================================================= */
    /* ESTADO INACTIVA */
    /* ========================================================= */

    .table-card.inactiva {

        border-top: 4px solid #82868b;

        opacity: .7;

    }


    .table-icon.inactiva {

        background: #eeeeee;

        color: #82868b;

    }


    .status-badge.inactiva {

        background: #eeeeee;

        color: #82868b;

    }


    /* ========================================================= */
    /* BADGE */
    /* ========================================================= */

    .status-badge {

        display: inline-block;

        padding: 5px 12px;

        border-radius: 20px;

        font-size: 12px;

        font-weight: 600;

    }


    /* ========================================================= */
    /* BOTONES */
    /* ========================================================= */

    .table-actions {

        margin-top: 15px;

        display: flex;

        gap: 5px;

        justify-content: center;

    }


    .table-actions button {

        flex: 1;

    }


    /* ========================================================= */
    /* ESTADO VACÍO */
    /* ========================================================= */

    .empty-tables {

        padding: 60px 20px;

        text-align: center;

        color: #6e6b7b;

    }


    .empty-tables svg {

        width: 55px;

        height: 55px;

        margin-bottom: 15px;

        opacity: .5;

    }

</style>
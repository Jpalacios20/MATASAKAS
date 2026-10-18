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
                                <h2 class="content-header-title float-start mb-0">Lista de Personas</h2>
                                <div class="breadcrumb-wrapper">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item">
                                            <a href="<?php echo BASE_URL; ?>Administracion">Administración</a>
                                        </li>
                                        <li class="breadcrumb-item active">
                                            <span>Personas</span>
                                        </li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Header title -->

                <!-- Table -->
                <div class="row">
                    <div class="card">
                        <table class="table" id="datatable-clients">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Tipo de documento</th>
                                    <th>Nº de documento</th>            
                                    <th>Nacionalidad</th>
                                    <th>Teléfono</th>
                                    <th>Email</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Aquí se cargarán dinámicamente los datos con JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- /Table -->
                
            </section>
            <!-- Users ends -->

            <!-- Create Client Modal -->
                <div class="modal fade" id="create_clients_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header bg-transparent">
                                <button type="reset" class="btn-close reset" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body px-sm-5 pb-5">
                                <div class="text-center mb-2">
                                    <h1 class="mb-1">Agregar una nueva Persona</h1>
                                </div>
                                <form method="POST" enctype="multipart/form-data" id="create_clients_form" class="row" onsubmit="return false">
                                    <!-- Tipo de documento -->
                                    <div class="col-12">
                                        <div>
                                            <label class="form-label">Tipo de documento <span class="text-danger">*</span></label>
                                            <select id="document_type" name="document_type" class="form-select select2" required>
                                                <!-- Este select será llenado dinámicamente desde el JS -->
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Número de documento -->
                                    <div class="col-12">
                                        <label class="form-label">Nº de Documento <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="number" id="document_number" name="document_number" class="form-control" placeholder="Nº de Documento" required />
                                            <button type="button" class="btn btn-primary btn_get_company_data">
                                                <i class="bi bi-search"></i> <!-- Ícono de búsqueda -->
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Nombre -->
                                    <div class="col-12">
                                        <label class="form-label">Nombre <span class="text-danger">*</span></label>
                                        <input type="text" id="name" name="name" class="form-control" placeholder="Nombre" required />
                                    </div>

                                    <!-- Nacionalidad -->
                                    <div class="col-12">
                                        <label class="form-label">Nacionalidad <span class="text-danger">*</span></label>
                                        <input type="text" id="nationality" name="nationality" class="form-control" placeholder="Nacionalidad" required />
                                    </div>

                                    <!-- Email -->
                                    <div class="col-12">
                                        <div>
                                            <label class="form-label">Email</label>
                                            <input type="email" name="email" class="form-control" placeholder="user@example.com" />
                                        </div>
                                    </div>

                                    <!-- Teléfono -->
                                    <div class="col-12">
                                        <div>
                                            <label class="form-label">Teléfono</label>
                                            <input type="text" name="phone" class="form-control" placeholder="Teléfono" />
                                        </div>
                                    </div>

                                    <!-- Dirección -->
                                    <div class="col-12">
                                        <div>
                                            <label class="form-label">Dirección</label>
                                            <input type="text" id="address" name="address" class="form-control" placeholder="Dirección" />
                                        </div>
                                    </div>

                                    <!-- Referencia -->
                                    <div class="col-12">
                                        <div>
                                            <label class="form-label">Referencia</label>
                                            <textarea name="reference" class="form-control" id="reference" cols="2" rows="2" style="max-height: 68px;" placeholder="Referencia"></textarea>
                                        </div>
                                    </div>

                                    <!-- Botones -->
                                    <div class="col-12 text-center">
                                        <button id="btn_create_clients" type="submit" class="btn btn-primary mt-2 me-1">
                                            Guardar
                                        </button>
                                        <button type="reset" class="btn btn-outline-secondary mt-2 reset" data-bs-dismiss="modal" aria-label="Close">
                                            <span>Cancelar</span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Create Client Modal -->


                <!-- Update Client Modal -->
                    <div class="modal fade" id="update_clients_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="updateClientsModalLabel">
                        <div class="modal-dialog modal-dialog-centered modal-edit-clients">
                            <div class="modal-content">
                                <div class="modal-header bg-transparent">
                                    <h5 class="modal-title" id="updateClientsModalLabel"></h5>
                                    <button type="button" class="btn-close reset" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body pb-5 px-sm-5 pt-50">
                                    <div class="text-center mb-2">
                                        <h1 class="mb-1">Actualizar Persona</h1>
                                    </div>
                                    <form method="POST" enctype="multipart/form-data" id="update_clients_form" class="row" onsubmit="update_clients(this); return false;">
                                        <div class="col-12">
                                            <label class="form-label">Tipo de documento <span class="text-danger">*</span></label>
                                            <select name="document_type" class="form-select select2" data-msg="Seleccione un tipo de documento" required>
                                                <!-- Opciones dinámicas cargadas desde el backend -->
                                            </select>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Nº de Documento <span class="text-danger">*</span></label>
                                            <input type="number" name="document_number" class="form-control" placeholder="Nº de Documento" data-msg="Este campo es obligatorio" required />
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Nombre <span class="text-danger">*</span></label>
                                            <input type="text" name="name" class="form-control" placeholder="Nombre" data-msg="Este campo es obligatorio" required />
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Nacionalidad <span class="text-danger">*</span></label>
                                            <input type="text" name="nationality" class="form-control" placeholder="Nacionalidad" data-msg="Este campo es obligatorio" required />
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Email</label>
                                            <input type="email" name="email" class="form-control" placeholder="user@example.com" />
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Teléfono</label>
                                            <input type="tel" name="phone" class="form-control" placeholder="Teléfono" />
                                        </div>
                                        
                                        <div class="col-12">
                                            <label class="form-label">Referencia</label>
                                            <textarea name="reference" class="form-control" cols="2" rows="2" style="max-height: 68px;" placeholder="Referencia"></textarea>
                                        </div>
                                        <input type="hidden" name="id_clients">
                                        <div class="col-12 text-center mt-2 pt-50">
                                            <button id="btn_update_clients" type="submit" class="btn btn-primary me-1">Guardar</button>
                                            <button type="reset" class="btn btn-outline-secondary reset" data-bs-dismiss="modal">Cancelar</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /Update Client Modal -->

        </div>
    </div>
</div>
<!-- END: Content -->
<style>
/* Contenedor que permite desplazamiento horizontal */
.table-wrapper {
    overflow-x: auto;  /* Permite desplazamiento horizontal */
    -webkit-overflow-scrolling: touch;  /* Mejora el desplazamiento en dispositivos móviles */
    width: 100%;  /* Hace que la tabla ocupe todo el ancho disponible */
}

/* Tabla con estilo */
.table {
    width: 100%; /* Asegura que la tabla ocupe todo el ancho disponible */
    table-layout: fixed; /* Asegura que las columnas se distribuyan de manera uniforme */
}




/* Celdas de la tabla */
.table td {
    padding: 12px 8px;
    font-size: 13px;
    vertical-align: middle;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: normal;  /* Cambiado a normal para permitir múltiples líneas */
    word-wrap: break-word; /* Permite el salto de línea cuando el texto es largo */
    max-width: 200px; /* Limita el ancho máximo de las celdas */
    height: auto; /* Deja que la altura de las celdas crezca según el contenido */
}

/* Ajustes de columnas específicas */
.table th:nth-child(1), .table td:nth-child(1) {
    width: 20%; /* Ajuste para la columna 'Nombre' */
}

.table th:nth-child(2), .table td:nth-child(2) {
    width: 10%; /* Ajuste para la columna 'Tipo de documento' */
}

.table th:nth-child(3), .table td:nth-child(3) {
    width: 12%; /* Ajuste para la columna 'Nº de documento' */
}

.table th:nth-child(4), .table td:nth-child(4) {
    width: 10%; /* Ajuste para la columna 'Nacionalidad' */
}

.table th:nth-child(5), .table td:nth-child(5) {
    width: 10%; /* Ajuste para la columna 'Teléfono' */
}

.table th:nth-child(6), .table td:nth-child(6) {
    width: 16%; /* Ajuste para la columna 'Email' */
}

.table th:nth-child(7), .table td:nth-child(7) {
    width: 10%; /* Ajuste para la columna 'Acciones' */
}

/* Hacer que el texto largo se ajuste en las celdas sin desbordarse */
.table td {
    max-width: 200px; /* Limita el ancho máximo de las celdas */
    overflow: hidden;
    text-overflow: ellipsis; /* Muestra los puntos suspensivos cuando el texto es largo */
    height: auto; /* Permite que las celdas se expandan en altura */
}

/* Agregar bordes suaves para una mejor apariencia */
.table-bordered {
    border: 1px solid #ddd;
}

.table-bordered th, .table-bordered td {
    border: 1px solid #ddd;
}

/* Fondo alterno para filas */
.table-striped tbody tr:nth-child(odd) {
    background-color: #f9f9f9;
}

/*        */
/* Aseguramos que la tabla ocupe el 100% del ancho disponible */
.table {
    width: 100%;
    table-layout: fixed; /* Esto asegura que las celdas tengan un ancho fijo */
}

/* Ajuste para las celdas de la tabla */
.table td, .table th {
    padding: 10px; /* Espaciado interno de las celdas */
    word-wrap: break-word; /* Permite que el contenido largo se ajuste dentro de las celdas */
    overflow: hidden; /* Evita que el contenido se desborde fuera de las celdas */
    text-overflow: ellipsis; /* Añade puntos suspensivos si el texto es largo */
    white-space: normal; /* Permite que el texto se ajuste en varias líneas */
    max-width: 250px; /* Limita el ancho de las celdas a un valor máximo (puedes ajustarlo según sea necesario) */
}

/* Permite que las celdas se expandan para mostrar todo el texto */
.table td {
    word-wrap: break-word;
    white-space: normal;
    width: auto;
}

/* Aseguramos que el contenido dentro de botones se ajuste correctamente */
.table td button {
    padding: 6px 12px;
    font-size: 14px;
    border-radius: 5px;
    border: 1px solid #ddd;
    background-color: #f1f1f1;
    color: #333;
    cursor: pointer;
    white-space: nowrap;  /* Evita que el texto del botón se divida en varias líneas */
    text-overflow: ellipsis;  /* Añade puntos suspensivos si el texto es largo */
    overflow: hidden; /* Evita que los botones se desborden */
}

/* Asegura que los campos de texto (input, select, textarea) ocupen el ancho completo de las celdas */
input, select, textarea {
    width: 100%;  /* Hace que todos los campos de entrada ocupen todo el ancho disponible */
    box-sizing: border-box;  /* Incluye padding y border en el ancho total */
    word-wrap: break-word; /* Permite que el texto largo dentro de los campos se ajuste */
    white-space: normal; /* Permite que el texto se ajuste y no se corte */
}

/* Ajustes específicos para select */
select {
    width: 100%; /* Hace que el select ocupe el 100% de la celda */
    padding: 8px 12px;
    font-size: 14px;
    border: 1px solid #ddd;
    border-radius: 4px;
    background-color: #f8f8f8;
    white-space: normal; /* Permite que el texto dentro del select se ajuste */
    word-wrap: break-word; /* Permite que el texto dentro de las opciones se ajuste */
}

/* Asegura que el ícono dentro del botón esté alineado y no desborde */
.table td button i {
    font-size: 16px;
    margin-right: 8px;
}

/* Asegura que la tabla se ajuste bien al tamaño de la pantalla */
@media screen and (max-width: 768px) {
    .table {
        font-size: 12px; /* Reduce el tamaño de fuente en pantallas más pequeñas */
    }
}
/*          */ 

/* Estilo para las celdas con imágenes (puedes especificar más selectores si es necesario) */
.table td img {
    max-width: 100%; /* Asegura que la imagen se ajuste dentro de la celda */
    height: auto; /* Mantiene la proporción de la imagen */
}

/* Ajuste para la celda que contiene la imagen y texto */
.table td {
    padding: 12px 8px;
    font-size: 13px; /* Aquí puedes cambiar el tamaño de la fuente según sea necesario */
    vertical-align: middle;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: normal; /* Cambiado a normal para permitir múltiples líneas */
    word-wrap: break-word; /* Permite el salto de línea cuando el texto es largo */
    max-width: 200px; /* Limita el ancho máximo de las celdas */
    height: auto; /* Deja que la altura de las celdas crezca según el contenido */
}

/* Si deseas cambiar específicamente la fuente o el tamaño de letra de las celdas con texto */
.table td span {
    font-size: 20px; /* Puedes cambiar este valor para ajustar el tamaño de la letra */
    color: #333; /* Aquí puedes cambiar el color del texto */
}

/* Si las celdas contienen texto, puedes también ajustar el tamaño de la fuente en ellas */
.table td p {
    font-size: 16px; /* Cambia el tamaño de la letra para los párrafos dentro de las celdas */
    color: #333; /* Cambia el color si lo deseas */
}
/*              */


/* Ajuste para el tamaño de la letra en los encabezados de la tabla */
.table th {
    font-size: 20px; /* Cambia este valor para ajustar el tamaño de la letra en los encabezados */
    font-weight: bold; /* Puedes mantener la negrita para los encabezados */
    text-align: left; /* Alineación del texto en los encabezados (puedes cambiarla a 'center' si lo prefieres) */
    padding: 12px 8px; /* Ajusta el espaciado dentro de los encabezados */
    background-color: #f4f4f4; /* Fondo gris claro para los encabezados (opcional) */
}

</style>
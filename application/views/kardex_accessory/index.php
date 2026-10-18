<!-- BEGIN: Content-->
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>

    <div class="content-wrapper container-xxl p-0">

        <div class="content-body">

            <section id="kardex_accessories">

                <!-- TITULO -->
                <div class="content-header row">
                    <div class="content-header-left col-md-9 col-12 mb-2">
                        <div class="row breadcrumbs-top">
                            <div class="col-12">
                                <h2 class="content-header-title float-start mb-0">
                                    Kardex Accesorios
                                </h2>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CARD -->
                <div class="card">
                    <div class="card-body">

                        <form>

                            <!-- FILA 1 -->
                            <div class="row g-3 mb-3">

                                <!-- CODIGO -->
                                <div class="col-md-3">
                                    <label class="form-label">
                                        Código
                                    </label>

                                    <input type="text"
                                           id="code"
                                           class="form-control"
                                           placeholder="COD001">
                                </div>

                                <!-- PRODUCTO -->
                                <div class="col-md-5">
                                    <label class="form-label">
                                        Producto
                                    </label>

                                    <select id="product"
                                            class="form-select">

                                        <option selected disabled>
                                            Seleccionar producto
                                        </option>

                                    </select>
                                </div>

                                <!-- CATEGORIA -->
                                <div class="col-md-4">
                                    <label class="form-label">
                                        Categoría
                                    </label>

                                    <select id="category"
                                            class="form-select">

                                        <option selected disabled>
                                            Seleccionar categoría
                                        </option>

                                    </select>
                                </div>

                            </div>

                            <!-- FILA 2 -->
                            <div class="row g-3 mb-4">

                                <!-- ENTRADA -->
                                <div class="col-md-2">
                                    <label class="form-label">
                                        Entrada
                                    </label>

                                    <input type="number"
                                           id="entry_stock"
                                           class="form-control">
                                </div>

                                <!-- SALIDA -->
                                <div class="col-md-2">
                                    <label class="form-label">
                                        Salida
                                    </label>

                                    <input type="number"
                                           id="exit_stock"
                                           class="form-control">
                                </div>

                                <!-- SALDO -->
                                <div class="col-md-2">
                                    <label class="form-label">
                                        Saldo
                                    </label>

                                    <input type="number"
                                           id="balance"
                                           class="form-control">
                                </div>

                                <!-- BOTON -->
                                <div class="col-md-6 d-flex align-items-end">

                                    <button type="button"
                                            id="btn_add_accessory"
                                            class="btn btn-primary w-100">
                                        Agregar Accesorio
                                    </button>

                                </div>

                            </div>

                            <!-- TABLA -->
                            <div class="table-responsive mb-3">

                                <table class="table table-bordered">

                                    <thead class="table-light">
                                        <tr>

                                            <th>CÓDIGO</th>
                                            <th>PRODUCTO</th>
                                            <th>CATEGORÍA</th>
                                            <th>ENTRADA</th>
                                            <th>SALIDA</th>
                                            <th>SALDO</th>

                                        </tr>
                                    </thead>

                                    <tbody id="detail_accessory">
                                    </tbody>

                                </table>

                            </div>

                            <!-- BOTONES -->
                            <div class="d-flex gap-1">

                                <button type="submit"
                                        class="btn btn-primary">
                                    Guardar
                                </button>

                                <button type="button"
                                        class="btn btn-secondary">
                                    Cancelar
                                </button>

                            </div>

                        </form>

                    </div>
                </div>

            </section>

        </div>
    </div>
</div>
<!-- END: Content-->
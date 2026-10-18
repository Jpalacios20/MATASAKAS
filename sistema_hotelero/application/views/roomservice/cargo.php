<style>
    .cargo-page *,
    .cargo-page *::before,
    .cargo-page *::after {
        box-sizing: border-box;
    }

    .cargo-card {
        width: 100%;
        margin-bottom: 15px;
        border-radius: 8px;
        background: #fff;
        box-shadow: 0 2px 10px rgba(0,0,0,.08);
        overflow: hidden;
    }

    .cargo-header {
        padding: 15px 18px;
        border-bottom: 1px solid #eee;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 15px;
    }

    .cargo-header strong {
        font-size: 17px;
    }

    .cargo-body {
        padding: 18px;
    }

    .cargo-info {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 15px;
        margin-bottom: 15px;
    }

    .cargo-info-item span {
        display: block;
        font-size: 13px;
        margin-bottom: 4px;
        color: #777;
    }

    .cargo-info-item strong {
        font-size: 14px;
    }

    .cargo-items {
        width: 100%;
        overflow-x: auto;
    }

    .cargo-items table {
        width: 100%;
        margin-bottom: 15px;
    }

    .cargo-items th,
    .cargo-items td {
        padding: 9px;
        font-size: 13px;
        vertical-align: middle;
    }

    .cargo-total {
        text-align: right;
        font-size: 19px;
        margin-bottom: 15px;
    }

    .cargo-actions {
        text-align: right;
    }

    .cargo-actions .btn {
        min-width: 190px;
    }

    .sin-cargos {
        background: #fff;
        border-radius: 8px;
        padding: 45px 20px;
        text-align: center;
        box-shadow: 0 2px 10px rgba(0,0,0,.06);
    }

    .sin-cargos h4 {
        margin-bottom: 8px;
    }

    .modal-cargo {
        display: none;
        position: fixed;
        z-index: 9999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,.45);
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .modal-cargo-box {
        width: 100%;
        max-width: 550px;
        background: #fff;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 5px 25px rgba(0,0,0,.2);
    }

    .modal-cargo-title {
        font-size: 19px;
        margin-bottom: 15px;
        font-weight: 500;
    }

    .modal-cargo-box select {
        width: 100%;
        height: 40px;
        margin-bottom: 15px;
    }

    .modal-cargo-buttons {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    @media (max-width: 991px) {
        .cargo-info {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 600px) {
        .cargo-actions {
            text-align: center;
        }

        .cargo-actions .btn {
            width: 100%;
        }

        .modal-cargo-buttons {
            flex-direction: column;
        }

        .modal-cargo-buttons button {
            width: 100%;
        }
    }
</style>


<!-- BEGIN: Content -->
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>

    <div class="content-wrapper container-xxl p-0">

        <div class="content-header row mb-1">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-start mb-0">
                            Room Service - Cargo a Habitación
                        </h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-body cargo-page">

            <div id="lista-cargos">
                <div class="sin-cargos">
                    <h4>Cargando pedidos...</h4>
                </div>
            </div>

        </div>

    </div>
</div>
<!-- END: Content -->


<div
    id="modal-cargo"
    class="modal-cargo"
>

    <div class="modal-cargo-box">

        <div class="modal-cargo-title">
            Seleccionar reserva
        </div>

        <div id="info-cargo">
            Selecciona la reserva correspondiente
            a la habitación.
        </div>

        <br>

        <select
            id="cargo-reservation"
            class="form-control"
        >
            <option value="">
                Seleccionar reserva
            </option>
        </select>

        <div class="modal-cargo-buttons">

            <button
                type="button"
                class="btn btn-secondary"
                id="btn-cerrar-cargo"
            >
                Cancelar
            </button>

            <button
                type="button"
                class="btn btn-success"
                id="btn-confirmar-cargo"
            >
                Confirmar cargo
            </button>

        </div>

    </div>

</div>


<script>
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
</script>
<style>

.roomservice-pedidos *,
.roomservice-pedidos *::before,
.roomservice-pedidos *::after {
    box-sizing: border-box;
}

.pedido-card {
    background: #fff;
    border-radius: 8px;
    margin-bottom: 15px;
    box-shadow: 0 2px 8px rgba(0,0,0,.08);
    overflow: hidden;
}

.pedido-header {
    padding: 12px 15px;
    border-bottom: 1px solid #eee;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.pedido-header strong {
    font-size: 17px;
}

.estado-pendiente {
    background: #ffc107;
    color: #000;
    padding: 5px 10px;
    border-radius: 5px;
    font-size: 12px;
    font-weight: 600;
}

.pedido-body {
    padding: 15px;
}

.pedido-info {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 15px;
    margin-bottom: 15px;
}

.pedido-info-item {
    font-size: 13px;
}

.pedido-info-item span {
    display: block;
    color: #777;
    margin-bottom: 3px;
}

.pedido-info-item strong {
    font-size: 14px;
}

.pedido-items {
    width: 100%;
    overflow-x: auto;
}

.pedido-items table {
    width: 100%;
    margin-bottom: 15px;
}

.pedido-items th,
.pedido-items td {
    padding: 8px;
    font-size: 13px;
}

.pedido-total {
    text-align: right;
    font-size: 18px;
    margin-bottom: 12px;
}

.pedido-actions {
    text-align: right;
}

.btn-preparar {
    min-width: 170px;
}

.sin-pedidos {
    background: #fff;
    border-radius: 8px;
    padding: 40px 20px;
    text-align: center;
    color: #777;
}

@media (max-width: 991px) {
    .pedido-info {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 600px) {

    .pedido-header {
        display: block;
    }

    .estado-pendiente {
        display: inline-block;
        margin-top: 8px;
    }

    .pedido-actions .btn {
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
                            Room Service - Pedidos
                        </h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-body roomservice-pedidos">

            <div id="lista-pedidos">
                <div class="sin-pedidos">
                    Cargando pedidos...
                </div>
            </div>

        </div>

    </div>
</div>
<!-- END: Content -->


<script>

if (typeof feather !== 'undefined') {
    feather.replace();
}

</script>
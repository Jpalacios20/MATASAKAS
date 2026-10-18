<?php /* Vista POS: Historial de ventas. */ ?>

<style>
.pos-wrap{color:#243247}
.pos-wrap *, .pos-wrap *::before, .pos-wrap *::after{box-sizing:border-box}
.pos-card{background:#fff;border:1px solid #dce3eb;border-radius:10px;padding:18px;margin-bottom:18px;box-shadow:0 2px 8px #102b4610}
.pos-head{display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:12px}
.pos-field{display:flex;flex-direction:column;gap:6px;margin-bottom:10px}
.pos-field label{font-weight:600;font-size:13px}
.pos-field input,.pos-field select,.pos-field textarea{padding:10px;border:1px solid #cbd5e1;border-radius:6px;width:100%;background:#fff}
.pos-btn{border:0;border-radius:6px;padding:10px 14px;background:#17477d;color:#fff;cursor:pointer;font-weight:600}
.pos-btn:hover{filter:brightness(.93)}
.pos-btn.secondary{background:#64748b}
.pos-table-wrap{overflow:auto}
.pos-table{width:100%;border-collapse:collapse;font-size:14px}
.pos-table th,.pos-table td{padding:10px;border-bottom:1px solid #e2e8f0;text-align:left;white-space:nowrap}
.pos-table th{background:#f1f5f9;color:#243247}
.pos-msg{display:none;padding:10px 12px;border-radius:6px;margin:10px 0}
.pos-msg.show{display:block}
.pos-msg.ok{background:#dcfce7;color:#166534}
.pos-msg.error{background:#fee2e2;color:#991b1b}
.pos-muted{color:#64748b;font-size:13px}
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
                        <h2 class="content-header-title float-start mb-0">Historial de ventas</h2>
                        <span class="pos-muted ms-2">Ventas y Servicios · POS</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-body pos-wrap">

            <div id="pos-message" class="pos-msg" role="status"></div>

            <div class="pos-card">
                <div class="pos-head">
                    <div class="pos-field" style="min-width:240px;margin:0">
                        <label for="pos-search">Buscar</label>
                        <input id="pos-search" placeholder="Venta, tipo, habitación, cliente">
                    </div>
                    <button id="pos-refresh" class="pos-btn secondary" type="button">Actualizar</button>
                </div>

                <div class="pos-table-wrap">
                    <table class="pos-table">
                        <thead>
                            <tr>
                                <th>N.º</th>
                                <th>Fecha</th>
                                <th>Tipo</th>
                                <th>Habitación</th>
                                <th>Cliente</th>
                                <th>Unidades</th>
                                <th>Total</th>
                                <th>Estado</th>
                                <th>Observaciones</th>
                            </tr>
                        </thead>
                        <tbody id="pos-history">
                            <tr><td colspan="9">Cargando...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </div>
</div>
<!-- END: Content -->

<script>window.POS_BASE_URL = <?php echo json_encode(defined('BASE_URL') ? BASE_URL : '/'); ?>;</script>
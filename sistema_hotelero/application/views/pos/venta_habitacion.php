<?php /* Vista POS: Venta a habitación. */ ?>

<style>
.pos-wrap{color:#243247}
.pos-wrap *, .pos-wrap *::before, .pos-wrap *::after{box-sizing:border-box}
.pos-card{background:#fff;border:1px solid #dce3eb;border-radius:10px;padding:18px;margin-bottom:18px;box-shadow:0 2px 8px #102b4610}
.pos-head{display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:12px}
.pos-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:14px}
.pos-field{display:flex;flex-direction:column;gap:6px;margin-bottom:10px}
.pos-field label{font-weight:600;font-size:13px}
.pos-field input,.pos-field select,.pos-field textarea{padding:10px;border:1px solid #cbd5e1;border-radius:6px;width:100%;background:#fff}
.pos-btn{border:0;border-radius:6px;padding:10px 14px;background:#17477d;color:#fff;cursor:pointer;font-weight:600}
.pos-btn:hover{filter:brightness(.93)}
.pos-btn.secondary{background:#64748b}
.pos-btn.danger{background:#b42318}
.pos-table-wrap{overflow:auto}
.pos-table{width:100%;border-collapse:collapse;font-size:14px}
.pos-table th,.pos-table td{padding:10px;border-bottom:1px solid #e2e8f0;text-align:left;white-space:nowrap}
.pos-table th{background:#f1f5f9;color:#243247}
.pos-total{font-size:20px;font-weight:700;color:#173d70}
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
                        <h2 class="content-header-title float-start mb-0">Venta a habitación</h2>
                        <span class="pos-muted ms-2">Ventas y Servicios · POS</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-body pos-wrap">

            <div id="pos-message" class="pos-msg" role="status"></div>

            <div class="pos-card">
                <div class="pos-grid">
                    <div class="pos-field">
                        <label for="pos-room">Habitación ocupada</label>
                        <select id="pos-room"><option value="">Cargando habitaciones...</option></select>
                    </div>
                    <div class="pos-field">
                        <label for="pos-product">Producto</label>
                        <select id="pos-product"><option value="">Cargando productos...</option></select>
                    </div>
                    <div class="pos-field">
                        <label for="pos-quantity">Cantidad</label>
                        <input id="pos-quantity" type="number" min="1" step="1" value="1">
                    </div>
                    <div class="pos-field" style="justify-content:end">
                        <button id="pos-add" class="pos-btn" type="button">Agregar al consumo</button>
                    </div>
                </div>
                <div class="pos-field">
                    <label for="pos-notes">Observaciones (opcional)</label>
                    <input id="pos-notes" placeholder="Indicaciones">
                </div>
            </div>

            <div class="pos-card">
                <div class="pos-table-wrap">
                    <table class="pos-table">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Precio</th>
                                <th>Cantidad</th>
                                <th>Subtotal</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="pos-cart">
                            <tr><td colspan="5">Sin productos.</td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="pos-head" style="margin-top:16px">
                    <span class="pos-total">Total: S/ <span id="pos-total">0.00</span></span>
                    <button id="pos-save" class="pos-btn" type="button">Registrar consumo</button>
                </div>
            </div>

        </div>

    </div>
</div>
<!-- END: Content -->

<script>window.POS_BASE_URL = <?php echo json_encode(defined('BASE_URL') ? BASE_URL : '/'); ?>;</script>
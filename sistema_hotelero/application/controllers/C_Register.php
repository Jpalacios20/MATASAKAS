<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!-- BEGIN: Content-->
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper container-xxl p-0">
        
        <div class="content-header row">
        </div>

        <div class="content-body">
            
            <!-- BOTÓN NUEVO REGISTRO -->
            <div class="row mb-3">
                <div class="col-12 text-end" style="text-align: right !important;">
                    <a href="<?php echo base_url('c_register/nuevo'); ?>" class="btn btn-primary" style="padding: 10px 20px; font-weight: bold;">
                        + Nuevo Registro
                    </a>
                </div>
            </div>

            <!-- CONTENEDOR DE LA TABLA -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>FECHA CHECK-IN</th>
                                    <th>CLIENTE</th>
                                    <th>Nº HABITACIÓN</th>
                                    <th>HORA CHECK-IN</th>
                                    <th>FECHA CHECK-OUT</th>
                                    <th>HORA CHECK-OUT</th>
                                    <th>COSTO</th>
                                    <th>ESTADO</th>
                                    <th>ACCIONES</th>
                                </tr>
                            </thead>
                            <tbody id="tbody_registro">
                                <?php if(!empty($registros)): ?>
                                    <?php foreach($registros as $fila): ?>
                                        <tr>
                                            <td><?php echo isset($fila['checkin_date']) ? $fila['checkin_date'] : ''; ?></td>
                                            <!-- Aquí se imprime dinámicamente el nombre y apellido del cliente -->
                                            <td><?php echo !empty($fila['cliente']) ? $fila['cliente'] : 'Sin cliente'; ?></td>
                                            <td><?php echo isset($fila['id_room']) ? $fila['id_room'] : ''; ?></td>
                                            <td><?php echo isset($fila['checkin_time']) ? $fila['checkin_time'] : ''; ?></td>
                                            <td><?php echo isset($fila['checkout_date']) ? $fila['checkout_date'] : ''; ?></td>
                                            <td><?php echo isset($fila['checkout_time']) ? $fila['checkout_time'] : ''; ?></td>
                                            <td><?php echo isset($fila['costo']) ? $fila['costo'] : ''; ?></td>
                                            <td><?php echo isset($fila['status']) ? $fila['status'] : ''; ?></td>
                                            <td>
                                                <!-- Puedes agregar tus botones de acciones aquí si los tenías -->
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="9" class="text-center">No hay registros disponibles</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<!-- END: Content-->
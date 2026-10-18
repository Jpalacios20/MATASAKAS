    <!-- BEGIN: Functions JS-->
    <script src="<?php echo BASE_URL ?>application/core/Functions.js"></script>
    <!-- END Functions JS-->

    <!-- BEGIN: Config JS-->
    <script src="<?php echo BASE_URL ?>application/config/Config.js"></script>
    <!-- END Config JS-->

    <!-- BEGIN: Vendor JS-->
    <script src="<?php echo BASE_URL ?>public/app-assets/vendors/js/vendors.min.js"></script>
    <!-- BEGIN Vendor JS-->

    <!-- BEGIN: Page Vendor JS-->
    <script src="<?php echo BASE_URL ?>public/app-assets/vendors/js/lottie/lottie-player.js"></script>
    <script src="<?php echo BASE_URL ?>public/app-assets/vendors/js/tables/datatable/jquery.dataTables.min.js"></script>
    <script src="<?php echo BASE_URL ?>public/app-assets/vendors/js/tables/datatable/dataTables.bootstrap5.min.js"></script>
    <script src="<?php echo BASE_URL ?>public/app-assets/vendors/js/tables/datatable/dataTables.responsive.min.js"></script>
    <script src="<?php echo BASE_URL ?>public/app-assets/vendors/js/tables/datatable/responsive.bootstrap5.min.js"></script>
    <script src="<?php echo BASE_URL ?>public/app-assets/vendors/js/tables/datatable/datatables.checkboxes.min.js"></script>
    <script src="<?php echo BASE_URL ?>public/app-assets/vendors/js/tables/datatable/datatables.buttons.min.js"></script>
    <script src="<?php echo BASE_URL ?>public/app-assets/vendors/js/tables/datatable/jszip.min.js"></script>
    <script src="<?php echo BASE_URL ?>public/app-assets/vendors/js/tables/datatable/pdfmake.min.js"></script>
    <script src="<?php echo BASE_URL ?>public/app-assets/vendors/js/tables/datatable/vfs_fonts.js"></script>
    <script src="<?php echo BASE_URL ?>public/app-assets/vendors/js/tables/datatable/buttons.html5.min.js"></script>
    <script src="<?php echo BASE_URL ?>public/app-assets/vendors/js/tables/datatable/buttons.print.min.js"></script>
    <script src="<?php echo BASE_URL ?>public/app-assets/vendors/js/tables/datatable/dataTables.rowGroup.min.js"></script>
    <script src="<?php echo BASE_URL ?>public/app-assets/vendors/js/charts/apexcharts.min.js"></script>
    <script src="<?php echo BASE_URL ?>public/app-assets/vendors/js/extensions/toastr.min.js"></script>
    <script src="<?php echo BASE_URL ?>public/app-assets/vendors/js/forms/select/select2.full.min.js"></script>
    <script src="<?php echo BASE_URL ?>public/app-assets/vendors/js/forms/validation/jquery.validate.min.js"></script>
    <script src="<?php echo BASE_URL ?>public/app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js"></script>
    <script src="<?php echo BASE_URL ?>public/app-assets/vendors/js/extensions/sweetalert2.all.min.js"></script>
    <script src="<?php echo BASE_URL ?>public/app-assets/js/scripts/extensions/ext-component-sweet-alerts.js"></script>
    <!-- END: Page Vendor JS-->

    <!-- BEGIN: Theme JS-->
    <script src="<?php echo BASE_URL ?>public/app-assets/js/core/app-menu.js"></script>
    <script src="<?php echo BASE_URL ?>public/app-assets/js/core/app.js"></script>
    <!-- END: Theme JS-->

    <!-- Library Calendar -->
    <script src="<?php echo BASE_URL ?>public/app-assets/js/scripts/calendar/index.global.js"></script>
    <!-- Library Calendar -->



    <!-- BEGIN: Index JS -->
    <?php
    if (isset($params['js']) && count($params['js'])) {
        foreach ($params['js'] as $js) { ?>
            <script src="<?php echo  $js; ?>"></script>
    <?php
        }
    }
    ?>
    <!-- END: Index JS-->

    <!-- BEGIN: Main JS-->
    <script src="<?php echo BASE_URL ?>application/views/main/js/index.js"></script>
    <!-- END: Main JS-->

    <!-- BEGIN: Lenguajes JS-->
    <script src="<?php echo BASE_URL ?>public/app-assets/js/scripts/lenguajes.js"></script>
    <!-- END: Lenguajes JS-->

    <!-- -->
    <script>
        $(window).on('load', function() {
            if (feather) {
                feather.replace({
                    width: 14,
                    height: 14
                });
            }
            // --
        })
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            console.log("ANDO VIVO");

        })

        function get_category() {
            // --
            $.ajax({
                url: BASE_URL + 'Categories/get_categories',
                type: 'GET',
                dataType: 'json',
                contentType: false,
                processData: false,
                cache: false,
                beforeSend: function() {
                    console.log('Cargando...');
                },
                success: function(data) {
                    // --
                    if (data.status === 'OK') {
                        // --
                        var html = '<option value="">Seleccionar</option>';
                        // var html = '';
                        // --
                        data.data.forEach(element => {
                            html += '<option value="' + element.id + '">' + element.description + '</option>';
                        });
                        // -- Set values for select
                        $('#create_product_form :input[name=category]').html(html);
                        $('#update_product_form :input[name=category]').html(html);
                    }
                }
            })
        }


        function update_product(form) {
            // --
            $('#btn_update_product').prop('disabled', true);
            // --
            let params = new FormData(form);
            // --
            $.ajax({
                url: BASE_URL + 'RoomType/update_room_type', // Reemplaza 'update_habitacion' con la ruta correcta de actualización de habitaciones
                type: 'POST',
                data: params,
                dataType: 'json',
                contentType: false,
                processData: false,
                cache: false,
                beforeSend: function() {
                    console.log('Cargando...');
                },
                success: function(data) {
                    // --
                    if (data.status === 'OK') {
                        // --
                        $('#update_product_modal').modal('hide');
                        form.reset();
                        refresh_datatable();

                    } else {
                        // --
                        $('#btn_update_product').prop('disabled', false);
                    }
                }
            })
        }
        // -- Eventos

        // --
        $(document).on('click', '.btn_update', function() {
            // --
            let value = $(this).attr('data-process-key');
            console.log(value);

            $('#update_product_modal').modal('show');
            // --
            let params = {
                'id_habitacion': value
            } // Asegúrate de que coincida con el nombre correcto del parámetro
            // --
            // $.ajax({
            //     url: BASE_URL + 'Habitacion/get_habitacion_by_id', // Reemplaza 'get_habitacion_by_id' con la ruta correcta para obtener habitaciones por ID
            //     type: 'GET',
            //     data: params,
            //     dataType: 'json',
            //     contentType: false,
            //     processData: true,
            //     cache: false,
            //     success: function(data) {
            //         // --
            //         if (data.status === 'OK') {
            //             // --
            //             let item = data.data
            //             // --
            //             $('#update_habitacion_form :input[name=id_habitacion]').val(item.id);
            //             $('#update_habitacion_form :input[name=description]').val(item.description);
            //             // -- Otras asignaciones de valores para los campos de actualización
            //         }
            //     }
            // })
            // --
        });

        // -- Otras funciones y eventos necesarios
        // $(document).on('click', '.btn_delete', function() {
        //     --
        //     let value = $(this).attr('data-process-key');
        //     --
        //     let params = {
        //         'id_product': value
        //     }
        //     --
        //     Swal.fire({
        //         title: '¿Estás seguro causa?',
        //         text: '¡No podrás revertir esto!',
        //         icon: 'warning',
        //         showCancelButton: true,
        //         confirmButtonText: 'Si, eliminar!',
        //         cancelButtonText: 'No, cancelar!',
        //         customClass: {
        //             confirmButton: 'btn btn-primary',
        //             cancelButton: 'btn btn-outline-danger ms-1'
        //         },
        //         buttonsStyling: false,
        //         preConfirm: _ => {
        //             return $.ajax({
        //                 url: BASE_URL + 'Product/delete_product',
        //                 type: 'POST',
        //                 data: params,
        //                 dataType: 'json',
        //                 cache: false,
        //                 success: function(data) {
        //                     --
        //                     --
        //                     if (data.status === 'OK') {
        //                         --
        //                         refresh_datatable();
        //                     }
        //                 }
        //             })
        //         }
        //     }).then(result => {
        //         if (result.isConfirmed) {}
        //     });
        // })

        // get_category()
    </script>
    <!-- -->
    </body>
    <!-- END: Body-->

    </html>
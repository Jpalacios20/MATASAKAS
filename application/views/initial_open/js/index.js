function get_initial_open() {
    
  $('#btn_initial_open').prop('disabled', true);
    // --
    let params = new FormData(form);
    // --
    $.ajax({
        url: BASE_URL + 'Initial_open',
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
            functions.toast_message(data.type, data.msg, data.status);
            // --
            if (data.status === 'OK') {
                // --
                $('#initial_open_modal').modal('hide');
            } else {
                // --
                $('#btn_initial_open').prop('disabled', false);
            }
        }
    })
}

$(document).ready(function() {
    get_user();
    
    $('#form_initial_open').on('submit', function(e) {
        e.preventDefault();
        save_initial_open();
    });
});

function save_initial_open() {
    let form = document.getElementById('form_initial_open');
    let params = new FormData(form);
    
    $('#btn_initial_open').prop('disabled', true);
    
    $.ajax({
        url: BASE_URL + 'Initial_open/save_initial_open',
        type: 'POST',
        data: params,
        dataType: 'json',
        contentType: false,
        processData: false,
        cache: false,
        beforeSend: function() {
            console.log('Guardando apertura de caja...');
        },
        success: function(response) {
            console.log('Respuesta:', response);
            
            if (response && response.status) {
                functions.toast_message(
                    response.status === 'OK' ? 'success' : 'error',
                    response.result,
                    response.status
                );
                
                if (response.status === 'OK') {
                    form.reset();
                    $('#idusuario').selectpicker('refresh');
                }
            }
        },
        error: function(xhr, status, error) {
            console.error('Error:', error);
            functions.toast_message('error', 'Error al guardar la apertura', 'ERROR');
        },
        complete: function() {
            $('#btn_initial_open').prop('disabled', false);
        }
    });
}

function get_user() {
  $.ajax({
    url: BASE_URL + 'Initial_open/get_user',
    type: 'GET',  // Cambiado de POST a GET
    dataType: 'json',
    success: function (data) {
      if (data.status === "OK") {
        var html = '<option value="">Seleccionar usuario</option>';
        data.data.forEach((element) => {
          html +=
            '<option value="' + element.id + '">' + element.full_name + '</option>';
        });
        $("#idusuario").html(html);
        $('#idusuario').selectpicker('refresh');
      } else {
        functions.toast_message('warning', 'No se encontraron usuarios.', 'Error');
      }
    },
    error: function (xhr, status, error) {
      console.error("Error en la solicitud AJAX:", error);
      functions.toast_message('error', 'Error al cargar los usuarios.', 'Error');
    }
  });
}


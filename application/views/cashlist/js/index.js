$(document).ready(function() {
    load_cashlist();
});

function load_cashlist() {
    $.ajax({
        url: BASE_URL + 'Cashlist/get_cashlist',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.status === 'OK') {
                let html = '';
                response.data.forEach(function(row) {
                    html += '<tr>' +
                        '<td>' + row.id + '</td>' +
                        '<td>' + (row.usuario || '-') + '</td>' +
                        '<td>' + (row.monto_inicial !== null ? 'S/ ' + parseFloat(row.monto_inicial).toFixed(2) : '-') + '</td>' +
                        '<td>' + (row.monto_final !== null ? 'S/ ' + parseFloat(row.monto_final).toFixed(2) : '-') + '</td>' +
                        '<td>' + (row.fecha_hora_apertura || '-') + '</td>' +
                        '<td>' + (row.fecha_hora_cierre || '-') + '</td>' +
                        '<td>' + (row.observaciones || '-') + '</td>' +
                        '<td>' + (row.estado || '-') + '</td>' +
                        '</tr>';
                });
                $('#cashlist_table tbody').html(html);
            } else {
                $('#cashlist_table tbody').html('<tr><td colspan="8" class="text-center">No se encontraron registros.</td></tr>');
            }
        },
        error: function(xhr, status, error) {
            $('#cashlist_table tbody').html('<tr><td colspan="8" class="text-center text-danger">Error al cargar los datos.</td></tr>');
        }
    });
}

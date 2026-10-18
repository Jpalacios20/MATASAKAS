
var events_calendar = [];
document.addEventListener('DOMContentLoaded', upload_calendar());


// -- Data view and get --

async function upload_calendar() { 
    await load_data(); 
    // --
    function load_data() {
        return new Promise(function(resolve, reject) {
            $.ajax({
                url: BASE_URL + 'Reservation/get_reservations',
                cache: false,
                success: function (data) {
                    let datas = data.data;
                    for (const element of datas) {
                        // --

                        let colorEvents;
                        if(element.status == 'Ocupado'){
                            colorEvents = '#EA5455';
                        }else if(element.status == 'Reservado'){
                            colorEvents = '#FC912A';
                        }else if(element.status == "Libre"){
                            colorEvents = '#4390E2';
                            element.checkout_date = currentTime("d");
                            element.checkout_time = currentTime("t");
                        }else if(
                            element.status == "Pendiente" ||
                            element.status == "Pago Pendiente" ||
                            element.status == "Reserva Pendiente"
                        ){
                            colorEvents = '#FF7AD9';
                        }

                        $('#guiaColorF').css('background-color', '#2C3E50');
                        $('#guiaColorR').css('background-color', '#FC912A');
                        $('#guiaColorO').css('background-color', '#EA5455');
                        $('#guiaColorL').css('background-color', '#4390E2');
                        $('#guiaColorP').css('background-color', '#FF7AD9');


                        let showFinalized = true;
                        let viewMode = true;
                        if(element.status == "Finalizado"){
                            colorEvents = '#2c3e50';
                            showFinalized = $("#flexSwitchCheckChecked").prop("checked");
                        }else{
                            viewMode = $("#viewMode").val() == 0 ? true : (element.status == $("#viewMode").val());
                        }

                        // --
                        if(showFinalized && viewMode){
                            let new_element = {
                                id: `${element.id_reservation}`,
                                title: `${element.room_number} - ${element.type_name} | Huésped: ${element.first_names} ${element.last_names} ${element.company_name}`,
                                start: element.checkin_date + " " + element.checkin_time,
                                end: element.checkout_date + " " + element.checkout_time,  
                                color: colorEvents
                            };
                            events_calendar.push(new_element);
                        }
                }
                resolve();
            }
        });
    });
}
    // --
    var calendarEl = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {
        nextDayThreshold: '00:00:00',
        displayEventTime: false,
        eventDisplay: 'block',
        locale: 'es',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,dayGridWeek,dayGridDay,listWeek'
        },
        initialDate: currentTime("d"),
        navLinks: true, // can click day/week names to navigate views
        editable: false,
        nowIndicator: true,
        dayMaxEvents: true, // allow "more" link when too many events
        events: events_calendar,
        eventClick: function form_information(info) {
            for (let key in allTotalPrice) {
                allTotalPrice[key] = 0;
            }

            var eventObj = info.event;
            if (eventObj.id) {

                $.ajax({
                    url: BASE_URL + 'Reservation/get_reservation',
                    cache: false,
                    data: {id_reservation : eventObj.id},
                    success: function (data){
                        const element = data.data;
                        let modal;

                        if(element.status === "Finalizado"){
                            modal = ('#completed_modal');
                        }else{
                            modal = ('#update_reservation_modal');
                        }
                        const modalInstance = new bootstrap.Modal(modal);
                        modalInstance.show();

                        let numReservation = element.id_reservation.toString().padStart(2, '0');
                        // --
                        
                        $('input[name=id_reservation]').val(element.id_reservation);
                        $('.idReservation').text(numReservation);

                        // --
                        $('input[name=id_room]').val(element.id_room);
                        $('#numRoom').text(element.room_number);
                        $('input[name=type_room]').val(element.type_name);
                        $('input[name=bed_name]').val(element.bed_type);
                        $('input[name=person_limit]').val(element.person_limit);
                        $('.detalle-numH').text(element.room_number);
                        $('.detalle-tipoH').text(element.type_name);
                        $('.detalle-tipoC').text(element.bed_type);
                        
                        // --

                        if(element.status == "Libre"){
                            element.checkout_date = currentTime("d");
                            element.checkout_time = currentTime("t");
                            element.payment_room = null;
                            statusLibre = true;
                            $('input[name=checkout_date], input[name=checkout_time]').attr({"readonly": true, "required": false});
                            $('input[name=checkout_date], input[name=checkout_time]').val('');
                            if(paymentReservation(element.checkin_date + " " + element.checkin_time, element.checkout_date + " " + element.checkout_time, allPricesRoom, 3)){
                                $('input[name=checkout_date]').val(element.checkout_date);
                                $('input[name=checkout_time]').val(element.checkout_time);
                                $('.detalle-salida').text(`${element.checkout_date}  ${element.checkout_time}`);
                            }
                        }else{
                            statusLibre = false;
                            $('input[name=checkout_date], input[name=checkout_time]').attr({"readonly": false, "required": true});
                            $('input[name=checkout_date]').val(element.checkout_date);
                            $('input[name=checkout_time]').val(element.checkout_time);
                            $('.detalle-salida').text(`${element.checkout_date}  ${element.checkout_time}`);
                        }

                        $('input[name=checkin_date]').val(element.checkin_date);
                        $('input[name=checkin_time]').val(element.checkin_time);
                        $('input[name=checkout_date]').attr("min", element.checkin_date);
                        $('input[name=checkin_date]').attr("max", element.checkout_date);
                        $('.detalle-entrada').text(`${element.checkin_date}  ${element.checkin_time}`);
                        
                        
                        // --
                        $('.detalle-nombres').text(capitalizeName(element.first_names));
                        $('.detalle-apellidos').text(capitalizeName(element.last_names));
                        $('.detalle-razonSc').text(capitalizeName(element.company_name));

                        if (element.last_sname.length > 1 && element.first_names.length > 1) {
                            // Ocultar el RUC y dejar el DNI visible
                            $('.huesped-ruc').hide().css('position', 'absolute');
                            $('.huesped-dni').show();
                        } else {
                            // Ocultar el DNI y dejar el RUC visible
                            $('.huesped-dni').hide().css('position', 'absolute');
                            $('.huesped-ruc').show();
                        }
                        
                        
                        $('.detalle-tipoDoc').text(element.document_type);
                        $('.detalle-numDoc').text(element.document_number);
                        $('.detalle-direccion').text(element.address);


                        var select = $("#mySelect");
                        if(element.status == "Ocupado"){
                            select.prop("selectedIndex", 0);
                        }else if(element.status == "Reservado"){
                            select.prop("selectedIndex", 1);
                        }else if(element.status == "Libre"){
                            select.prop("selectedIndex", 2);
                        }

                        allPricesRoom = [];
                        allPricesRoom.push(element.price_temporary);
                        allPricesRoom.push(element.price_half);
                        allPricesRoom.push(element.price_day);
                        paymentReservation(element.checkin_date + " " + element.checkin_time, element.checkout_date + " " + element.checkout_time, allPricesRoom, 0)

                        element.payment_room == null || element.payment_room < 1 ? paymentReservation(element.checkin_date + " " + element.checkin_time, element.checkout_date + " " + element.checkout_time, allPricesRoom, 1) : ($('input[name=payment_room]').val(decimalNumber(element.payment_room)), allTotalPrice.room = parseFloat(element.payment_room));
                        
                        $('input[name=id_payment]').val(element.id_payment);
                        element.payment_extra == null || element.payment_extra < 1 ? $('input[name=payment_extra]').val("0.00") : ($('input[name=payment_extra]').val(decimalNumber(element.payment_extra)), allTotalPrice.extra = parseFloat(element.payment_extra));
                        element.payment_discount == null || element.payment_discount < 1 ? $('input[name=payment_discount]').val("0.00") : ($('input[name=payment_discount]').val(decimalNumber(element.payment_discount)), allTotalPrice.discount = parseFloat(element.payment_discount));
                        element.pre_payment == null || element.pre_payment < 1 ? $('input[name=payment_cancelled]').val("0.00") : ($('input[name=payment_cancelled]').val(decimalNumber(element.pre_payment)), allTotalPrice.cancelled = parseFloat(element.pre_payment));
                        element.payment_total == null || element.payment_total < 1 ? $('input[name=payment_total]').val("0.00") : $('input[name=payment_total]').val(decimalNumber(element.payment_total));
                        paymentAuto();

                        $('input[name=departure_date]').val(element.departure_date);
                        $('input[name=departure_time]').val(element.departure_time);


                        $('.detalle-salidaF').text(`Fecha: ${element.departure_date}` );
                        $('.detalle-salidaH').text(`Hora: ${element.departure_time}` );
                        $('.detalle-pagoHab').text(`S/ ${element.payment_room}`);
                        $('.detalle-pagoExtra').text(`S/ ${element.payment_extra}`);
                        $('.detalle-subTotal').text(`S/ ${decimalNumber(parseFloat(element.payment_extra) + parseFloat(element.payment_room))}`);
                        $('.detalle-descuento').text(`S/ ${element.payment_discount}`);
                        $('.detalle-totalPagar').text(`S/ ${decimalNumber(parseFloat(element.payment_extra) + parseFloat(element.payment_room) - parseFloat(element.payment_discount))}`);
                        
                        
                    }
                })
            }
        }

        });

    calendar.render();
};

// --


function currentTime(e){
    let today = new Date();
    let day = today.getDate();
    let previousTime = today.toString();
    if(e == "d"){
        return `${today.getFullYear()}-${(today.getMonth() + 1).toString().padStart(2, '0')}-${day.toString().padStart(2, '0')}`;
    }else if(e == "t"){
        let formattedPreviousTime = previousTime.split(" ");
        return formattedPreviousTime[4].substring(0, 5) + ":00";
    }
}

function capitalizeName(name) {
    let parts = name.split(" ");
    let result = "";
    for (let part of parts) {
        if (part.length > 0) {
            result += part[0].toUpperCase() + part.slice(1).toLowerCase() + " ";
        }
    }
    return result.trim();
    }

var allPricesRoom = [];
var allTotalPrice = {
    room: 0,
    extra: 0,
    sales: 0,
    discount: 0,
    cancelled: 0,
}; 


// -- Date payment --

function paymentReservation(dateIn, dateOut, prices, type){
    const fechaInicio = new Date(dateIn);
    const fechaFin = new Date(dateOut);
    const milisegundosInicio = fechaInicio.getTime();
    const milisegundosFin = fechaFin.getTime();

    let minutes = (milisegundosFin - milisegundosInicio) / (1000 * 60);
    let days = Math.floor(minutes / 1440);
    let minutesRes = minutes % 1440;
    let hours = Math.floor(minutesRes / 60);
    let minutesLeft = minutesRes % 60;

    let paymentRoom = 0;

    // Calculate time and price
    if (minutes <= 240) {
        paymentRoom = parseFloat(prices[0]);
        $('.detalle-tiempo').text(`
            ${hours < 1 ? ' ' : (hours == 1 ? hours + ' hora' : hours + ' horas')}
            ${minutesLeft < 1 ? ' ' : (minutesLeft == 1 ? minutesLeft + ' minuto' : minutesLeft + ' minutos')}
        `);
    } else if (minutes > 240 && minutes <= 720) {
        paymentRoom = parseFloat(prices[1]);
        $('.detalle-tiempo').text(`
            ${hours < 1 ? ' ' : (hours == 1 ? hours + ' hora' : hours + ' horas')}
            ${minutesLeft < 1 ? ' ' : (minutesLeft == 1 ? minutesLeft + ' minuto' : minutesLeft + ' minutos')}
        `);
    } else {
        let payment = parseInt(prices[2]) * days;
        let paymentMinutes = 0;
    
        // --
        if (minutesRes > 0 && minutesRes <= 240) {
            paymentMinutes = parseInt(prices[0]);
        } else if (minutesRes > 240 && minutesRes <= 720) {
            paymentMinutes = parseInt(prices[1]);
        } else {
            paymentMinutes = parseInt(prices[2]);
        }
        payment += paymentMinutes;

        paymentRoom = payment;
        $('.detalle-tiempo').text(`
            ${(days + '').padStart(2, '0')} ${days == 1 ? 'día' : 'días'}
            ${hours < 1 ? ' ' : (hours == 1 ? hours + ' hora' : hours + ' horas')}
            ${minutesLeft < 1 ? ' ' : (minutesLeft == 1 ? minutesLeft + ' minuto' : minutesLeft + ' minutos')}
        `);
    }

    if(type == 1){ // set room price 
        allTotalPrice.room = parseFloat(paymentRoom);
        $('input[name=payment_room]').val(decimalNumber(paymentRoom));
    }else if(type == 2){    // price extra
        let price_extra = 0;
        $.ajax({
            url: BASE_URL + 'Reservation/get_payment_extra',
            cache: false,
            success: function(data){
                for(let value of data.data){
                    const minutes_define = (parseInt(value.extra_time.split(":")[0], 10) * 60) + parseInt(value.extra_time.split(":")[1], 10);
    
                    if(minutes > minutes_define){
                        price_extra = value.price_extra;
                    }
                }
                allTotalPrice.extra = parseFloat(price_extra);
                $('input[name=payment_extra]').val(decimalNumber(price_extra));

                paymentAuto()
            }
        })
    }else if(type = 3){ // reservation on time
        if(minutes > 0 ){
            return true;
        }else{
            return false;
        }
    }
} 


// -- Payments changes --

function paymentAuto(){
    let payment_subTotal = allTotalPrice.room + allTotalPrice.extra;
    $('input[name=payment_subTotal]').val(decimalNumber(payment_subTotal));

    let payment_lack = (payment_subTotal + allTotalPrice.sales) - (allTotalPrice.discount + allTotalPrice.cancelled);
    $('input[name=payment_lack]').val(decimalNumber(payment_lack));

    let payment_total = (payment_subTotal  + allTotalPrice.sales) - (allTotalPrice.discount);
    $('input[name=payment_total]').val(decimalNumber(payment_total));
}

$('input[name=payment_discount]').on('input', function (){
    let discount = $('input[name=payment_discount]').val();
    parseFloat(discount) < 1 || Number.isNaN(parseFloat(discount)) ? discount = 0 : "";
    allTotalPrice.discount = parseFloat(discount);
    paymentAuto();
})

$('input[name=payment_extra]').on('input', function (){
    let extra = $('input[name=payment_extra]').val()
    parseFloat(extra) < 1 || Number.isNaN(parseFloat(extra)) ? (extra = 0, $('input[name=payment_extra]').val(extra)) : " ";
    allTotalPrice.extra = parseFloat(extra);
    paymentAuto();
})

$('input[name=payment_cancelled]').on('input', function (){
    let cancelled = $('input[name=payment_cancelled]').val()
    let lack = $('input[name=payment_lack]').val();
    parseFloat(cancelled) < 1 || Number.isNaN(parseFloat(cancelled)) ? (cancelled = 0, $('input[name=payment_cancelled]').val(cancelled)) : "";
    allTotalPrice.cancelled = parseFloat(cancelled);
    paymentAuto();
})

$("payment_total").prop("readonly", true);

$('input[type=number]').on('focusout', function(){
    let val = $(this).val();
    $(this).val(decimalNumber(val));
})


function decimalNumber(val){
    var text = Number(val).toFixed(2);
    return text;
}

// -- Date --

$('#departure_btn').on('click', function(){
    if(statusLibre){
        $('input[name=departure_date], input[name=checkout_date]').val(currentTime("d"));
        $('input[name=departure_time], input[name=checkout_time]').val(currentTime("t"));
    }else{
        $('input[name=departure_date]').val(currentTime("d"));
        $('input[name=departure_time]').val(currentTime("t"));
    }
    // $('#departure_btn').attr('disabled', false);
    let valCheckout= $('input[name=checkout_date]').val() + " " + $('input[name=checkout_time]').val();
    let valDepurate = currentTime("d") + " " +  currentTime("t");

    paymentReservation(valCheckout, valDepurate, allPricesRoom, 2);
})

// -- 
var statusLibre = false;
function valDate (input_name, input_nameAux){
    
    let valCheckout;
    let valCheckin;
    if(statusLibre){
        valCheckin = $('input[name=checkin_date]').val() + " " + $('input[name=checkin_time]').val();
        valCheckout = currentTime("d")  + " " + currentTime("t");
    }else{
        valCheckin = $('input[name=checkin_date]').val() + " " + $('input[name=checkin_time]').val();
        valCheckout = $('input[name=checkout_date]').val() + " " + $('input[name=checkout_time]').val();
    }

    const valDateInitial = $('input[name=checkin_date]').val()
    $('input[name=checkout_date]').attr("min", valDateInitial);
    

    $('input[name=checkout_date]').attr("min", $('input[name=checkin_date]').val());
    $('input[name=checkin_date]').attr("max", $('input[name=checkout_date]').val());

    const valRoom = $('input[name=id_room]').val();
    const valReservation = $('input[name=id_reservation]').val();
    
    $.ajax({
        url: BASE_URL + 'Reservation/date_reservation',
        cache: false,
        data: {id_room : valRoom, checkin_date: valCheckin, checkout_date: valCheckout, id_reservation: valReservation},
        success: function (data) {
            if(data.status == 'ERROR'){
                $('input[name=' + input_name +']').addClass('is-invalid').attr('data-error', data.msg);
                $('input[name=' + input_nameAux +']').addClass('is-invalid').attr('data-error', data.msg);
                functions.toast_message(data.type, data.msg + data.data, data.status);
            }else if(data.status == 'OK'){
                $('input[name=' + input_name +']').removeClass('is-invalid').removeAttr('data-error');
                $('input[name=' + input_nameAux +']').removeClass('is-invalid').removeAttr('data-error');
                paymentReservation(valCheckin, valCheckout, allPricesRoom, 1);
            }
        }
    });
}

$('input[name="checkin_date"]').on('change', ()=>{ valDate("checkin_date", "checkin_time") });
$('input[name="checkin_time"]').on('change', ()=>{ valDate("checkin_time", "checkin_date") });
$('input[name="checkout_date"]').on('change', ()=>{ valDate("checkout_date", "checkout_time") });
$('input[name="checkout_time"]').on('change', ()=>{ valDate("checkout_time", "checkout_date") });

//  -- Update Status -- 

$('select[name="room_status"]').on('change', () => {
    const selectedIndex = $('select[name="room_status"]').prop('selectedIndex');
    if (selectedIndex === 0 || selectedIndex === 1) {
        $('input[name=checkout_date]').attr('readonly', false);
        $('input[name=checkout_time]').attr('readonly', false);
    } else {
        $('input[name=checkout_date]').attr('readonly', true);
        $('input[name=checkout_time]').attr('readonly', true);
    }
});


// -- Cancel --

$('.cancel_update').click(function(){ 
    $('input[type="date"]').removeClass('is-invalid').removeAttr('data-error');
    $('input[type="time"]').removeClass('is-invalid').removeAttr('data-error');
    $('input').removeClass('error');
    $('.huesped-ruc').hide().css('position', '');
    $('.huesped-dni').hide().css('position', '');
});

//-- Update -- 
function update_reservation(form) {
    // --
    $('#update_reservation_form').prop('disabled', true);
    // --
    let params = new FormData(form);
    // --
    $.ajax({
        url: BASE_URL + 'Reservation/update_reservation',
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
                $('#update_reservation_modal').modal('hide');
                events_calendar = [];
                upload_calendar();

            } else {
                // --
                $('#btn_update_reservation').prop('disabled', false);
            }
        }
    })
}

// --
function update_payment(form) {
    // --
    $('#departure_reservation_form').prop('disabled', true);
    // --
    let params = new FormData(form);
    // --
    $.ajax({
        url: BASE_URL + 'Reservation/update_payment',
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
            if(data.status === 'ERROR'){
                finishCorrect = false
            }

            if (data.status === 'OK') {
                // --
                events_calendar = [];
                upload_calendar();

            } else {
                // --
                $('#btn_update_reservation').prop('disabled', false);
            }
        }
    })
}

var finishCorrect = true;
$('#update_reservation_form').validate({
    // --
    submitHandler: function(form) {
        let invalidInputs = $('input[type="date"]' || 'input[type="time"]').hasClass('is-invalid');
        
        if (invalidInputs) {
            functions.toast_message("error", "La actualización no se ha realizado porque uno de los campos es incorrecto.", "ERROR");
            return false;
        } else {
            update_reservation(form);
        }
    }
});

// -- Validate form
$('#departure_reservation_form').validate({
    // --
    submitHandler: function(form) {
        update_payment(form);
    }
})


$(document).on('click', '#btn_finish_reservation', function() {
    //  --
    $('#btn_update_payment').trigger('click');

    if(!finishCorrect) return;

    let valueReservation = $('input[name=id_reservation]').val();
    let valueRoom = $('input[name=id_room]').val();

    let params = {
        'id_reservation' : valueReservation,
        'id_room' : valueRoom,
    }

    Swal.fire({
        title: '¿Estás seguro?',
        text: 'Una vez finalizada la reserva, no se podrán realizar cambios en la misma. Los detalles de la reserva se podrán consultar en cualquier momento.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Si, finalizar!',
        customClass: {
            confirmButton: 'btn btn-primary',
            cancelButton: 'btn btn-outline-danger ms-1'
        },
        buttonsStyling: false,
        preConfirm: _ => {
            return $.ajax({
                url: BASE_URL + 'Reservation/finish_reservation',
                type: 'POST',
                data: params,
                dataType: 'json',
                cache: false,
                success: function(data) {
                    // --
                    functions.toast_message(data.type, data.msg, data.status);
                    // --
                    if (data.status === 'OK') {
                        // --
                        $('#update_reservation_modal').modal('hide');
                        events_calendar = [];
                        upload_calendar();
                    }
                }
            })
        }

    }).then(result => {
        if (result.isConfirmed) {
        }
    });
})


// -- Filters --

$('#flexSwitchCheckChecked').on("change", () => {
    events_calendar = [];
    upload_calendar();
    
    if ($('#flexSwitchCheckChecked').prop('checked') == true) {
    localStorage.setItem("state", "true");
    } else {
    localStorage.setItem("state", "false");
    }
});

const savedState = localStorage.getItem("state");
if (savedState === "true") {
    $('#flexSwitchCheckChecked').prop('checked', true);
} else {
    $('#flexSwitchCheckChecked').prop('checked', false);
}

// --
$('#viewMode').on("change", (e) => {
    let select = $(e.target);
    let selectedOption = select.find(":selected");
    let selectedOptionValue = selectedOption.val();
    localStorage.setItem("mode", selectedOptionValue);

    events_calendar = [];
    upload_calendar();
});

const savedMode = localStorage.getItem("mode");
if (savedMode) {
    $('#viewMode').find(`option[value="${savedMode}"]`).prop("selected", true);
}


// -- Functions
$('.main-loading').addClass('hidden-loading');


/**
 * Initialize all selectors with class select2
 */
function initializeSelectors() {
    // --
    var select = $('.select2');
    // -- 
    select.each(function () {
        // --
        var $this = $(this);
        // --
        $this.select2({
            // --
            placeholder: 'Seleccionar',
            language: {
                noResults: function (params) {
                    return 'No se encontraron resultados';
                }
            },
            dropdownParent: $this.parent(),
            allowClear: true,
            width: '100%'
        })
        .change(function () {
            $(this).valid();
        });
    });
    // --
    $('.select2-search__field').css('width', '100%');
}


// -- Events


/**
 * Logout
 */
// --
$(document).on('click', '#dropdown-logout', function() {
    // --
    logout()
})

// --
function logout() {
    // --
    $.ajax({
        url: BASE_URL + 'Main/close_session',
        type: 'POST',
        dataType:'json',
        cache: false,
        beforeSend: function() {
            console.log('Cargando...');
        },
        success: function(data) {
            // --
            if (data.status === 'OK') {
                // --
                localStorage.clear();
                window.location.replace(BASE_URL + 'Login');
            }
        }
    })
}

// --
initializeSelectors();


// --- NOTIFICATIONS ---

// --
document.addEventListener('DOMContentLoaded', ()=>{
    setInterval(notifications_reservation, 60 * 1000);
    notificationRequirement();
    get_notifications() 
});

var processedNotifications = {};

// Show notifications
function get_notifications() {
    let icon, bg_class;
    $.ajax({
        url: BASE_URL + 'Notifications/get_notifications',
        type: 'GET',
        data: {continue : notificationList},
        dataType: 'json',
        cache: false,
        success: function (data){
            const elements = data.data;
            // functions.toast_message(data.type, data.msg, data.status);
            for(const element of elements){

                let notificationKey = element.date_notification + element.time_notification + element.type + element.id_reservation;

                element.status_notification == "New" ? contNewNotification++ : ' ';

                if(element.type == "Limpieza"){ 
                    (icon = `<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-key avatar-icon"><path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"></path></svg>`,
                    bg_class = `bg-light-success`)
                }else{
                    (icon = `<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-log-out avatar-icon"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>`,
                    bg_class = `bg-light-danger`);
                }

                const currentTime = new Date();
                const day = currentTime.getDate();
                const formattedPreviousDay = `${currentTime.getFullYear()}-${(currentTime.getMonth() + 1).toString().padStart(2, '0')}-${day.toString().padStart(2, '0')}`;

                let notification_day = formattedPreviousDay == element.date_notification ? "today" : "previous";
                let reservation_day =  notification_day == "today" ? "hoy" : showFinalization(element.sku_notification, "d");

                if (!processedNotifications[notificationKey]) {
                    processedNotifications[notificationKey] = true;
                    
                    if(element.status_notification == "New"){
                        $('#' + notification_day + '-notification-list').prepend(`
                            <div class="list-item d-flex align-items-start">
                                <div class="me-1">
                                    <div class="avatar ${bg_class}">
                                        <div class="avatar-content">${icon}</div>
                                    </div>
                                    ${element.status_notification == "New" ? `<div class="rounded-pill bg-primary mx-auto mt-1 indicatorNew" style="width: 5px; height: 5px"></div>` : ' '}
                                </div>
                                <div class="list-item-body flex-grow-1">
                                    <p class="media-heading"><span class="fw-bolder">Habitación ${element.room_number}.</span></p>
                                    <small class="notification-text">${element.type == 'Finalizando' ? 'Finalizará ' + reservation_day + ' a las ' + showFinalization(element.sku_notification, "t") : 'Necesita limpieza'}.</small>
                                    <small class="d-block notification-text fw-light lh-lg" style="color: #8888; font-size: 12px; margin: 5px 0 0 0"> Hace ${calculateTime(element.date_notification + " " + element.time_notification)} </small>
                                </div>
                            </div>
                        `);
                    }else{
                        $('#' + notification_day + '-notification-list').append(`
                            <div class="list-item d-flex align-items-start">
                                <div class="me-1">
                                    <div class="avatar ${bg_class}">
                                        <div class="avatar-content">${icon}</div>
                                    </div>
                                </div>
                                <div class="list-item-body flex-grow-1">
                                    <p class="media-heading"><span class="fw-bolder">Habitación ${element.room_number}.</span></p>
                                    <small class="notification-text">${element.type == 'Finalizando' ? 'Finalizará ' + reservation_day +  ' a las ' + showFinalization(element.sku_notification, "t")  : 'Necesita limpieza'}.</small>
                                    <small class="d-block notification-text fw-light lh-lg" style="color: #8888; font-size: 12px; margin: 5px 0 0 0"> Hace ${calculateTime(element.date_notification + " " + element.time_notification)}</small>
                                </div>
                            </div>
                        `);
                    }
                }
            }

            contNewNotification > 0 ? 
                ($('#view-notification').append(`<span class="badge rounded-pill bg-danger badge-up notification-badge">${contNewNotification}</span>`),
                $('#new-notifications-count').text(contNewNotification)) : "";
        }
    });
}

// Verification notifications
function notifications_reservation() {
    // --
    $.ajax({
        url: BASE_URL + 'Notifications/get_reservations',
        cache: false,
        success: function (data){
            const elements = data.data;
            for (const element of elements) {
                const checkoutDate = new Date(element.checkout_date + " " + element.checkout_time);
                const currentTime = new Date();

                const day = currentTime.getDate();
                const formattedPreviousDay = `${currentTime.getFullYear()}-${(currentTime.getMonth() + 1).toString().padStart(2, '0')}-${day.toString().padStart(2, '0')}`;
                const previousTime = currentTime.toString();
                let formattedPreviousTime = previousTime.split(" ");
                formattedPreviousTime=formattedPreviousTime[4].substring(0, 5)

                const timeToCheckout = Math.round(((checkoutDate - currentTime) / 1000) / 60);
                if (timeToCheckout <= 15 && timeToCheckout >= 0) {
                    const type = "Finalizando"; 
                    const sku = element.id_reservation + type + "/" + element.checkout_date + "/" + element.checkout_time;
                    let newNotifications = {date_notification: formattedPreviousDay, time_notification: formattedPreviousTime, type: type, id_reservation: element.id_reservation, sku_notification: sku}  
                    create_notifications(newNotifications, element);
                }
                
                timeToCheckout % 5 == 0 && timeToCheckout <= -5? 
                    (functions.toast_message("warning", `La reserva de la habitación ${element.room_number}, finalizó hace ${calculateTime(Math.abs(timeToCheckout))}.`, "Salida"), 
                    notification_windows("Salida", `La reserva de la habitación ${element.room_number}, finalizó hace ${calculateTime(Math.abs(timeToCheckout))}.`))
                    : " ";
            }
        }
    });
    $.ajax({
        url: BASE_URL + 'Notifications/get_cleaning',
        cache: false,
        success: function (data){
            const elements = data.data;
            for (const element of elements) {
                const checkoutDate = new Date(element.departure_date + " " + element.departure_time);
                const currentTime = new Date();

                const day = currentTime.getDate();
                const formattedPreviousDay = `${currentTime.getFullYear()}-${(currentTime.getMonth() + 1).toString().padStart(2, '0')}-${day.toString().padStart(2, '0')}`;
                const previousTime = currentTime.toString();
                let formattedPreviousTime = previousTime.split(" ");
                formattedPreviousTime=formattedPreviousTime[4].substring(0, 5)

                const timeToCheckout = Math.round(((currentTime - checkoutDate) / 1000) / 60);

                if (timeToCheckout >= 5) {
                    const type = "Limpieza";
                    const sku = element.id_reservation + type + "/" + element.departure_date + "/" + element.departure_time;
                    let newNotifications = {date_notification: formattedPreviousDay, time_notification: formattedPreviousTime, type: type, id_reservation: element.id_reservation, sku_notification: sku}  
                    create_notifications(newNotifications, element);
                }

                timeToCheckout % 5 == 0 ? 
                    (functions.toast_message("warning", `La habitación ${element.room_number}, necesita limpieza.`, "Limpieza"), 
                    notification_windows("Limpieza", `La habitación ${element.room_number}, necesita limpieza.`))
                    : " ";
            }
        }
    });
}

// Create notifications
function create_notifications(notification, element){
    let content = notification.type == "Finalizando" ? `La habiatación ${element.room_number}, finalizará hoy a las ${element.checkout_time}.` :  `La habiatación ${element.room_number}, necesita limpieza.`
    $.ajax({
        url: BASE_URL + 'Notifications/create_notifications',
        type: 'POST',
        data: notification,
        dataType: 'json',
        cache: false,
        success: function(data) {
            // --
            
            if (data.status === 'OK') {
                notification_windows("La reserva finalizará pronto", content);
                functions.toast_message("success", "" , "Nueva notificación");

                get_notifications();
            }
        }
    })
}

// Time finalization 
function showFinalization(t, tp){
    let parts = t.split("/")
    if(tp === "d"){
        return parts[1];
    }else if(tp === "t"){
        // let hm = parts[2].substr(0, 5)
        // return hm;
        return parts[2];
    }
}

// Past time
function calculateTime(t){
    let min;
    if (typeof t === 'string') {
        const timeNoti = new Date(t);
        const currentTime = new Date();
        const  mlNoti = timeNoti.getTime();
        const  mlCurrent = currentTime.getTime();
        min = (mlCurrent - mlNoti) / (1000 * 60);
    } else if (typeof t === 'number') {
        min = t;
    }


    let time
    if(min < 60){
        if(min < 1 ){
            return " segundos";
        }else{
            min = Math.floor(min)
            time = min > 1 ? " minutos" : " minuto";
            return min + time;
        }
    }else{
        let hours  = Math.floor(min/60)
        if(hours < 24){
            time = hours > 1 ? " horas" : " hora";  
            return hours + time;
        }else{
            let day = Math.floor(hours/24)
            if(day < 7){
                time = day > 1 ? " días" : " día";  
                return day + time;
            }else{
                let week = Math.floor(day/7)
                if(week < 4){
                    time = week > 1 ? " semanas" : " semana";  
                    return week + time;
                }else{
                    let month = Math.floor(week/4)
                    if(month < 12){
                        time = month > 1 ? " meses" : " mes";  
                        return month + time;
                    }else{
                        let year = Math.floor(month/12)
                        time = year > 1 ? " años" : " año";  
                        return year + time;
                    }
                }
            }
        }
    }
}

//  Scroll --
var notificationList = 0
$('#notification-list').scroll(function() {
    var elementHeight = $(this).height();
    var scrollPosition = $(this).scrollTop();
    var contentHeight = $(this)[0].scrollHeight;

    if (elementHeight + scrollPosition >= contentHeight - 30) {
        setTimeout(function() {
            notificationList += 10;
            get_notifications();
        }, 500);
    }
});

let contNewNotification = 0;
//  Notification Open
$('#container-notification').on('click', function(){
    $('#today-notification-list, #previous-notification-list').empty();
    processedNotifications = {}; // no repeat 
    contNewNotification = 0; // show new (count) 
    notificationList = 0; //  list limiter
    get_notifications();

    $("#notification-list").scrollTop(0);
    $.ajax({
        url: BASE_URL + 'Notifications/update_notification',
        type: 'POST',
        dataType: 'json',
        cache: false,
        success: function(data) {
            // --
            if(data.status === 'OK'){
                get_notifications()
            }
        }
    });
});

//  Notification Close
$('#container-notification').on('hidden.bs.dropdown', function() {
    $('.notification-badge').remove();
    $('.indicatorNew').remove();
    setTimeout(()=>{
        $('#new-notifications-count').text(contNewNotification);
    }, 10000)
});



// Notification windows permission
function notification_windows(affair, content){
    if(Notification.permission === 'granted'){
        new Notification(affair, {
            icon: ' ',
            body: content,
        });
    }
}

// Permission notification --
$('#systemNotification').on('click', function(){
    notificationRequirement();
})

function notificationRequirement(){
    Notification.requestPermission().then(result => {
    if(result == "granted"){
        $('#systemNotification').prop('checked', true);
    }else{
        $('#systemNotification').prop('checked', false);
    }
    })
}


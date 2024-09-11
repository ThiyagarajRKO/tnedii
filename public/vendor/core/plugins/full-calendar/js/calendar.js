"use strict";

let fullCalendarUtils = {
    calendarEl: document.getElementById('calendar'),
    formEl:$('#impiger-full-calendar-forms-full-calendar-form'),
    calendar: {},
    init: function () {
        this.loadFullCalendar();
        this.bindEvents();
    },
    bindEvents: function () {
        $('.filter').on('change', function () {
            fullCalendarUtils.calendar.refetchEvents();
        });
    },
    loadFullCalendar: function () {
        if (!$('#calendar').length) {
            return false;
        }

        let todayDate = moment().startOf('day'); 
        let TODAY = todayDate.format('YYYY-MM-DD');
        let calendarDate = new Date();
        let calendarMonth = calendarDate.getMonth() + 1;
        let calendarYear = calendarDate.getFullYear();

        fullCalendarUtils.calendar = new FullCalendar.Calendar(fullCalendarUtils.calendarEl, {
//                plugins: ['interaction', 'dayGrid', 'timeGrid', 'list','googleCalendarPlugin'],
            
            
            headerToolbar: {
                // left: 'prev,next today',
                left: 'next today',
                center: 'title',
                // right: 'dayGridMonth,timeGridWeek,timeGridDay'
                right: ''
            },

            height: 700,
            contentHeight: 680,
            aspectRatio: 3, // see: https://fullcalendar.io/docs/aspectRatio

            nowIndicator: true,
            now: calendarDate, 
            
           buttonText: {
                    today: "Today" 
            },
            
            views: {
                dayGridMonth: {buttonText: 'Month'},
                timeGridWeek: {buttonText: 'Week'},
                timeGridDay: {buttonText: 'Day'}
            },

            initialView: 'dayGridMonth',
            initialDate: TODAY,
            eventTextColor: "#FFF",
            editable: true,
            dayMaxEvents: true, // allow "more" link when too many events
            navLinks: true,
            allDaySlot: false,
//            displayEventTime: false,
            eventTimeFormat: { // like '7pm'
                    hour: 'numeric',
                    minute: '2-digit',
                    meridiem: 'short'
            },
            
            // googleCalendarApiKey: (googleCalendar) ? googleCalendarApiKey : '',
            googleCalendarApiKey: '',

            eventSources: [
                {
                    // url: 'full-calendars/getevents',
                    url: 'get-annual-action-plan',
                    method: 'GET',

                    failure: function () {
                    },
                    textColor: 'white' // an option!
                },
                {
                    // googleCalendarId: (googleCalendar) ? googleCalendarId : ''
                    googleCalendarId: ''
                },
            ],
            eventDidMount: function (info) {
                let element = $(info.el);
                if (info.event.url) {
                    var url = new URL(info.event.url);
                    if (url.host == 'www.google.com') {
                        info.event.setExtendedProp('type', 'google_calendar');
                    }
                }

                console.log("eventDidMount");
                console.log(info.event);
                console.log(info.event.extendedProps);

                // if (info.event.extendedProps && info.event.extendedProps.type) {
                    // if (info.event.extendedProps.type != 'google_calendar') {
                        element.attr('data-fancybox',true);
                        element.attr({'data-type':'ajax','data-src': 'annual-action-plan/view-detail/' + info.event.id,
                            'data-targetElm': 'viewDetail',
                        });
                    // }

                    // let category = info.event.extendedProps.type;
                    // $.each(categories, function (index, value) {
                    //     if (value.slug == category) {
                    //         element.css('background-color', value.color);
                    //         element.css('border-color', value.color);
                    //         element.css('color', "#fff");
                    //     }
                    // });
                // }
                if (info.event.extendedProps && info.event.extendedProps.description) {
                    if (element.hasClass('fc-day-grid-event')) {
                        element.data('content', info.event.extendedProps.description);
                        element.data('placement', 'top');
                    } else if (element.hasClass('fc-time-grid-event')) {
                        element.find('.fc-title').append('<div class="fc-description">' + info.event.extendedProps.description + '</div>');
                    } else if (element.find('.fc-list-item-title').lenght !== 0) {
                        element.find('.fc-list-item-title').append('<div class="fc-description">' + info.event.extendedProps.description + '</div>');
                    }
                }
            }
     
        });

        fullCalendarUtils.calendar.render();
    },

    
    calendarFormHandling:function(){
        if(fullCalendarUtils.formEl.length){
            let eventType =  fullCalendarUtils.formEl.find('#type').val();
            fullCalendarUtils.hideEventPlaceElm(eventType);
            fullCalendarUtils.formEl.on('change','#type',function(){
                let type = $(this).val();
                fullCalendarUtils.hideEventPlaceElm(type);
            })
        }
    },
    hideEventPlaceElm:function(value){
        if(value == 'events'){
            fullCalendarUtils.formEl.find('#place').parent().show();
            fullCalendarUtils.formEl.find('#place').attr('disabled',false);
        }else{
            fullCalendarUtils.formEl.find('#place').parent().hide();
            fullCalendarUtils.formEl.find('#place').attr('disabled',true);
        }
    }
};

jQuery(document).ready(function () {
    fullCalendarUtils.init();
});


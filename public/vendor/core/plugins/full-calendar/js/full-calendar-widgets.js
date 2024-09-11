"use strict";

var categories = [{ slug: 'holidays', name: 'Holidays', color: '#d9534f' },
{ slug: 'events', name: 'Events', color: '#5cb85c' },
{ slug: 'others', name: 'Others', color: '#0275d8' }];
currentUrl = 'admin/full-calendars';

let dashboardCalendarUtils = {
    calendarEl: document.getElementById('calendar'),
    calendar: {},
    init: function () {
        dashboardCalendarUtils.loadFullCalendar();
        // this.calendarFormHandling();
    },
    bindEvents: function () {
    },
    loadFullCalendar: function () {
        if (!$('#calendar').length) {
            return false;
        }
        var response = [{title: 'title2', start: '2021-09-10',end:'2021-09-10'}, {title: 'title2', start: '2021-09-10',end:'2021-09-10'}]

        let todayDate = moment().startOf('day');
        let TODAY = todayDate.format('YYYY-MM-DD');
        let calendarDate = new Date();

        dashboardCalendarUtils.calendar = new FullCalendar.Calendar(dashboardCalendarUtils.calendarEl, {
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
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
                dayGridMonth: { buttonText: 'Month' },
                timeGridWeek: { buttonText: 'Week' },
                timeGridDay: { buttonText: 'Day' }
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

            events: response,

            eventSources: [
                {
                    url: 'admin/full-calendars/getevents',
                    method: 'GET',

                    failure: function () {
                    },
                    textColor: 'white' // an option!
                }
            ],
            eventDidMount: function (info) {
                let element = $(info.el);
                if (info.event.url) {
                    var url = new URL(info.event.url);
                    if (url.host == 'www.google.com') {
                        info.event.setExtendedProp('type', 'google_calendar');
                    }
                }

                if (info.event.extendedProps && info.event.extendedProps.type) {
                    if (info.event.extendedProps.type != 'google_calendar') {
                        element.attr('data-fancybox', true);
                        element.attr({
                            'data-type': 'ajax', 'data-src': 'admin/full-calendars/viewdetail/' + info.event.id,
                            'data-targetElm': 'viewDetail',
                        });
                    }

                    let category = info.event.extendedProps.type;
                    $.each(categories, function (index, value) {
                        if (value.slug == category) {
                            element.css('background-color', value.color);
                            element.css('border-color', value.color);
                            element.css('color', "#fff");
                        }
                    });
                }
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
                /* Apply Filters */
                let eventTypes = ['holidays', 'events', 'others'];
                if (eventTypes && eventTypes.length > 0) {
                    if (info.event.extendedProps.type && eventTypes.indexOf(info.event.extendedProps.type) < 0) {
                        info.event.setProp('display', 'none')
                    }
                }


            },
            dateClick: function (info) {
                if (info.dateStr.indexOf("T") > -1) {
                    let dateTime = info.dateStr.split('T');
                    let date = dateTime[0];
                    let time = dateTime[1];
                    console.log(dateTime);
                    location.replace(currentUrl + '/create?date=' + date + '&time=' + time);
                } else {
                    location.replace(currentUrl + '/create?date=' + info.dateStr);
                }
            },
            eventDrop: function (info) {
                dashboardCalendarUtils.draggingEvents(info);
            }
        });

        dashboardCalendarUtils.calendar.render();
    },
    draggingEvents: function (info) {
        let newStartDate = info.event.start;
        let newEndDate = info.event.end;
        let start = moment(newStartDate).format('YYYY-MM-DD');
        let end = (newEndDate) ? moment(newEndDate).format('YYYY-MM-DD') : start;
        let eventId = info.event.id;
        let requestData = {
            'id': eventId,
            'start_date': start,
            'end_date': end,
        }
        $.ajax({
            url: currentUrl + '/drag_events/' + eventId,
            type: "POST",
            data: requestData,
            dataType: 'json',
            success: (response) => {
                if (response.error) {
                    Impiger.showError(response.message);
                } else {
                    Impiger.showSuccess(response.message);
                }
            },
            error: (data) => {
                Impiger.handleError(data);
            },
        });
    }
};

jQuery(document).ready(function () {
    dashboardCalendarUtils.init();
});


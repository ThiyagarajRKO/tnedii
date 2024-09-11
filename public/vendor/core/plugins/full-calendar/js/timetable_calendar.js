"use strict";

let fullCalendarUtils = {
    calendarEl: document.getElementById('calendar'),
    calendar: {},
    timetableRowData: {},
    init: function () {
        this.loadFullCalendar();
        this.validateTimetableFilter();
        this.bindEvents();
        this.initSelect2();
        this.initTimePicker();
    },
    bindEvents: function () {
        $('.btn-apply').on('click', function () {
            fullCalendarUtils.timetableRowData = {};
            
            if ($('.filter-form').valid()) {
                $('.resetBtn').removeClass('hidden');
                fullCalendarUtils.calendar.currentData.eventSources[13].meta.extraParams = fullCalendarUtils.getFilterParams();
                fullCalendarUtils.calendar.refetchEvents();
            } else {
                if(!$('.resetBtn').hasClass('hidden')) {
                    $('.resetBtn').addClass('hidden');
                }
            }
        });

        $('[name="institute_id"]').on('change', function () {
            let requestData = {
                'institute_id': $(this).val()
            }
            $.ajax({
                url: currentUrl + '/get_trainers',
                type: "POST",
                data: requestData,
                dataType: 'json',
                success: (response) => {
                    if (!response.error) {
                        CustomScript.initCustomSelect2($('[name="created_by"]').select2('destroy').empty().prepend('<option selected=""></option>'), { data: response });
                    }
                },
                error: (data) => {
                    Impiger.handleError(data);
                },
            });
        });
    },

    initTimePicker: function () {
        if(!$('.timepicker-24').length) {
            return false;
        }
        $('.timepicker-24').timepicker({
            autoclose: true,
            minuteStep: 5,
            showSeconds: false,
            showMeridian: false,
            defaultTime: false,
            minTime: '8',
            maxTime: '10pm'
        });
    },

    firstSundayOfYear: function (year = new Date().getFullYear()) {
        let date = moment().set('year', year).set('month', 0).set('date', 0).isoWeekday(7)
        if (date.date() > 7) { //
            date = date.isoWeekday(-6)
        }

        return date;
    },

    validateTimetableFilter: function () {
        $('.filter-form').validate({
            errorElement: 'span', //default input error message container
            errorClass: 'invalid-feedback', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            rules: {
                academic_year_id: {
                    required: true
                },
                institute_id: {
                    required: true
                },
                training_program_id: {
                    required: true
                },
                intake_id: {
                    required: true
                },
                term: {
                    required: true
                },
                created_by: {
                    required: true
                }
            },
        });
    },

    getFilterParams: function () {
        return {
            'ay_id': $('[name="academic_year_id"]').val(),
            'ins_id': $('[name="institute_id"]').val(),
            'tp_id': $('[name="training_program_id"]').val(),
            'intake': $('[name="intake_id"]').val(),
            'created_by': $('[name="created_by"]').val(),
            'term': $('[name="term"]').val(),
        };
    },

    loadFullCalendar: function () {
        if (!$('#calendar').length) {
            return false;
        }

        let firstSundayDate = fullCalendarUtils.firstSundayOfYear();
        firstSundayDate = firstSundayDate.format('YYYY-MM-DD');

        fullCalendarUtils.calendar = new FullCalendar.Calendar(fullCalendarUtils.calendarEl, {
            //                plugins: ['interaction', 'dayGrid', 'timeGrid', 'list','googleCalendarPlugin'],
            initialView: 'timeGridWeek',
            headerToolbar: {
                left: '',
                center: '',
                right: ''
            },
            dayHeaderFormat: { weekday: 'short' },
            columnFormat: 'dddd',
            allDaySlot: false,
            defaultView: 'agendaWeek', // display week view
            // hiddenDays: [0, 6], // hide Saturday and Sunday
            weekNumbers: false, // don't show week numbers
            // slotMinTime: '08:00:00',
            // slotMaxTime: '23:00:00',
            aspectRatio: 3,
            editable: false,
            dayMaxEvents: true, // allow "more" link when too many events,
            googleCalendarApiKey: (googleCalendar) ? googleCalendarApiKey : '',
            initialDate: firstSundayDate,
            eventSources: [
                {
                    url: 'timetables/getsessions',
                    extraParams: fullCalendarUtils.getFilterParams(),
                    method: 'GET',
                    failure: function () {
                    },
                    textColor: 'white' // an option!
                },
                {
                    googleCalendarId: (googleCalendar) ? googleCalendarId : ''
                },
            ],
            eventSourceSuccess: function (content, xhr) {
                let id = content['id'] || null;
                fullCalendarUtils.timetableRowData = content;

                if (id) {
                    if ($('.editRowBtn').length == 0) {
                        if($.inArray(content.timetable_status, content.workflow_meta) !== -1 && editPermissions == "1") {
                            $('.timetable-actions a').after('<a class="btn btn-sm btn-primary editRowBtn" href="timetables/edit/' + id + '">Edit</a>');
                        } else {
                            $('.editRowBtn').remove();
                        }
                        $('.workflowStatus').html(content.workflow);
                    }
                    if(!$('.workflowStatus ul.dropdown-menu').hasClass('dropdown-menu-right') &&
                    $('.timetable-actions a').length <= 0) {
                        $('.workflowStatus ul.dropdown-menu').addClass('dropdown-menu-right');
                    }
                } else {
                    $('.timetable-actions a.editRowBtn').remove();
                    $('.workflowStatus').html('');
                }
                return content.session_data || [];
            },
            eventDidMount: function (info) {
                let element = $(info.el);
                if (info.event.extendedProps) {
                    if (info.event.extendedProps.time) {
                        element.find('.fc-event-time').text(info.event.extendedProps.time);
                    }

                    element.find('.fc-event-main').prepend(`<div class="btn-group float-right" role="group">
                    <div class="dropdown">
                        <button class="btn dropdown-toggle" type="button" data-toggle="dropdown">
                            <i class="fa fa-ellipsis-h"></i>  
                        </button>
                        <ul class="dropdown-menu">
                              <li>
                              <a data-fancybox="" data-type="ajax" data-src="timetable-details/viewdetail/`+ info.event.id + `" href="javascript:void(0);" class="" data-toggle="tooltip" data-original-title="View"><i class="fa fa-eye"></i> View</a>
                              </li>
                        </ul>
                    </div>
                </div>`)
                    let trainer = info.event.extendedProps.trainer || "";
                    element.find('.fc-event-title').after('<div class="fc-trainer">' + trainer + '</div>');
                }
            }
        });

        fullCalendarUtils.calendar.render();
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
    },
    initSelect2: function () {
        $('.filterSelect').select2({
            placeholder: 'Select an option'
        });
    },
};

jQuery(document).ready(function () {
    fullCalendarUtils.init();

    $(document).on('shown.bs.dropdown', '.dropdown', function(e) {
        $('.fc-timegrid-col-frame').css('z-index','unset')
        $(this).parents('.fc-timegrid-col-frame:first').css('z-index',1000);
        $(this).parents('.fc-timegrid-event-harness:first').css('z-index',100);
    });
    $(document).on('hide.bs.dropdown', '.dropdown', function(e) {
        $(this).parents('.fc-timegrid-event-harness:first').css('z-index',1);
    });
});


let attendanceFilterUtils = {
    //formEl : $('#impiger-institution-forms-institution-form'),
    init: function () {
        this.filterUtils();
    },

    filterUtils: function () {
        if($('.filter-form').length) {
            $(document).ajaxSend(function(){
                if(!$('.dataTables_wrapper .dataTables_processing:visible').length) {
                    $('#custom-ajax-loader').show();
                }
            });
            $(document).ajaxComplete(function(){
                $('#custom-ajax-loader').hide();
            });
        }
        /*
        $('.filter-form').on('change', '.term, .session_type_id, #attendance_date,[name="attendance_startdate"],[name="attendance_enddate"],.trainer_id', function () {
            let date = new Date($('[name="attendance_date"]:visible').val());
            let weekday = ["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];
            let day = weekday[date.getDay()];
            let startDate = new Date($('[name="attendance_startdate"]:visible').val());
            let endDate = new Date($('[name="attendance_enddate"]:visible').val());
            let startDay = weekday[startDate.getDay()];
            let endDay = weekday[endDate.getDay()];
            let type = $('.session_type_id').val();
            let academicYearId = $('.academic_year_id').val();
            let programTypeId = $('.program_type_id').val();
            let instituteId = $('.institute_id').val();
            let trainingProgramId = $('.training_program_id').val();
            let intakeId = $('.intake_id').val();
            let term = $('.term').val();
            let trainerId = $('.trainer_id').val();
            if (day) {
                let requestData = {
                    'day': day,
                    'type':type,
                    'academic_year_id': academicYearId,
                    'program_type_id': programTypeId,
                    'institute_id': instituteId,
                    'training_program_id':trainingProgramId,
                    'intake_id': intakeId,
                    'term': term,
                    'trainer_id':trainerId
                };
    
                $.ajax({
                    url: '/admin/attendances/sessiondetails',
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || $('[name="_token"]').val()
                    },
                    data: requestData,
                    dataType: 'json',
                    success: (response) => {
                        if (!response.error) {
                            let ddSelector = $('.course_unit_id');
                            if ($(ddSelector).data('select2')) {
                                CustomScript.initCustomSelect2($(ddSelector).select2('destroy').empty().prepend('<option selected=""></option>'), { data: response });
                                crudUtils.setDependentSelectedValue($(ddSelector));
                            }
                        }
                    },
                    error: (res) => {
                        Impiger.handleError(res);
                    },
                });
            }

            if (instituteId && startDate && endDate) {
                let requestData = {
                    'startDay': startDay,
                    'endDay': endDay,
                    'type':type,
                    'academic_year_id': academicYearId,
                    'program_type_id': programTypeId,
                    'institute_id': instituteId,
                    'training_program_id':trainingProgramId,
                    'intake_id': intakeId,
                    'term': term,
                    'trainer_id':trainerId
                };
    
                $.ajax({
                    url: '/admin/attendances/coursedetails',
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || $('[name="_token"]').val()
                    },
                    data: requestData,
                    dataType: 'json',
                    success: (response) => {
                        if (!response.error) {
                            let ddSelector = $('.course_unit_id');
                            if ($(ddSelector).data('select2')) {
                                CustomScript.initCustomSelect2($(ddSelector).select2('destroy').empty().prepend('<option selected=""></option>'), { data: response });
                                crudUtils.setDependentSelectedValue($(ddSelector));
                            }
                        }
                    },
                    error: (res) => {
                        Impiger.handleError(res);
                    },
                });
            }

            if (!instituteId) {
                let requestData = {
                    'startDay': startDay,
                    'endDay': endDay,
                    'type':type,
                    'academic_year_id': academicYearId,
                    'term': term
                };
    
                $.ajax({
                    url: '/admin/attendances/studentcoursedetails',
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || $('[name="_token"]').val()
                    },
                    data: requestData,
                    dataType: 'json',
                    success: (response) => {
                        if (!response.error) {
                            let ddSelector = $('.course_unit_id');
                            if ($(ddSelector).data('select2')) {
                                CustomScript.initCustomSelect2($(ddSelector).select2('destroy').empty().prepend('<option selected=""></option>'), { data: response });
                                crudUtils.setDependentSelectedValue($(ddSelector));
                            }
                        }
                    },
                    error: (res) => {
                        Impiger.handleError(res);
                    },
                });
            }
        });
        */
        $('.filter-form').on('change', '.financial_year_id', function () {
            let requestData = {
                'financial_year_id': $('.filter-form .financial_year_id').val()
            }
            $.ajax({
                url: '/admin/attendances/get_annual_action_plan_list',
                type: "POST",
                data: requestData,
                dataType: 'json',
                success: (response) => {
                    if (!response.error) {
                        let ddSelector = $('.annual_action_plan_id');
                        if ($(ddSelector).data('select2')) {
                            CustomScript.initCustomSelect2($(ddSelector).select2('destroy').empty().prepend('<option selected=""></option>'), { data: response });
                            crudUtils.setDependentSelectedValue($(ddSelector));
                        }
                    }
                },
                error: (data) => {
                    Impiger.handleError(data);
                },
            });
        });

        $('.filter-form').on('change', '.annual_action_plan_id', function () {
            let requestData = {
                'annual_action_plan_id': $('.filter-form .annual_action_plan_id').val()
            }
            $.ajax({
                url: '/admin/attendances/get_training_program_list',
                type: "POST",
                data: requestData,
                dataType: 'json',
                success: (response) => {
                    if (!response.error) {
                        let ddSelector = $('.training_title_id');
                        if ($(ddSelector).data('select2')) {
                            CustomScript.initCustomSelect2($(ddSelector).select2('destroy').empty().prepend('<option selected=""></option>'), { data: response });
                            crudUtils.setDependentSelectedValue($(ddSelector));
                        }
                    }
                },
                error: (data) => {
                    Impiger.handleError(data);
                },
            });
        });


        $('.filter-form').on('change', '.training_title_id', function () {
            let requestData = {
                // 'annual_action_plan_id': $('.filter-form .annual_action_plan_id').val(),
                'id': $(this).val()
            }
            $.ajax({
                url: '/admin/attendances/get_training_program_schedule',
                type: "GET",
                data: requestData,
                dataType: 'json',
                success: (response) => {
                    if (!response.error) {
                        let ddSelector = $('#attendance_date');
                        console.log('get_training_program_schedule -> response');
                        console.log(response);
                        initAttendanceDatePicker(response[0].start_date, response[0].end_date)
                    }
                },
                error: (data) => {
                    Impiger.handleError(data);
                },
            });
        });
    }
}

$(document).ready(function () {
    attendanceFilterUtils.init();  
    $("<style>.dataTables_scroll{width: 100% !important;}.dataTables_scrollHead{width: 100% !important;}.dataTables_scrollHeadInner{width: 100% !important;}.dataTables_processing {display: none !important;}.dataTables_scrollBody{width: auto !important;}.buttons-colvis{display:none;}</style>").appendTo("body"); 
    $('table th').css('outline','0');
});

function initAttendanceDatePicker(start, end) {
    var urlData = window.location.href.split("/");
    let element = $(document).find(".datepicker");
    // let currentDate = new Date();
    start = new Date(start);
    end = new Date(end);
    let today = new Date();
    if(urlData[urlData.length - 1] == 'attendances') {
        console.log("attendances");
        let start_date = new Date();
        let end_date = new Date();
        if(start > today && end > today) {
            console.log("both are big");
            start_date = today;
            end_date = today;
        } else if(start < today && end < today){
            console.log("both are small");
            start_date = start;
            end_date = end;
        } else if(start > today) {
            start_date = today;
            end_date = today;
        } else if(end > today && start <= today) {
            start_date = start;
            end_date = today;
        }

        start = start_date;
        end = end_date;
    }
    console.log("start date = ", start);
    console.log("end date = ", end);
    if (jQuery().bootstrapDP) {
        let format = element.data("date-format");
        $(element).bootstrapDP("destroy");
        $(element).bootstrapDP({
            maxDate: 0,
            // changeYear: true,
            autoclose: true,
            dateFormat: format,
            // startView: 2,
            startDate: start,
            endDate: end,
        });

        let attendance_startdate = $("#attendance_startdate");
        let attendance_enddate = $("#attendance_enddate");
        $(attendance_startdate).bootstrapDP("destroy");
        $(attendance_enddate).bootstrapDP("destroy");

        $(attendance_startdate).bootstrapDP({
            maxDate: 0,
            autoclose: true,
            dateFormat: format,
            startDate: start,
            endDate: start,
            setDate:start
        });
        $(attendance_startdate).bootstrapDP('setDate',start)
        $(attendance_startdate).bootstrapDP('update');

        $(attendance_enddate).bootstrapDP({
            maxDate: 0,
            autoclose: true,
            dateFormat: format,
            startDate: end,
            endDate: end,
        });

        $(attendance_enddate).bootstrapDP('setDate',end)
        $(attendance_enddate).bootstrapDP('update');

    }
}



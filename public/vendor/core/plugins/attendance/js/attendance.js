let TraineeAttendanceUtils = {
    init: function () {
        TraineeAttendanceUtils.bindEvents();
    },

    bindEvents: function () {        
        
        $(document).on('click', '.dataTables_wrapper .dt-buttons button.action-item', this.saveAttendanceDetails);
    },
    saveAttendanceDetails: function (e) {
        let target = $(e.currentTarget);
        console.log("target");
        // console.log(target);
        target.addClass("button-loading");
        let action = target.find('span[data-action]').data('action');
        if (action == 'save') {
            console.log('if save');
            $(document).ajaxSend(function () {
                if (!$('.dataTables_wrapper .dataTables_processing:visible').length) {
                    $('#custom-ajax-loader').show();
                }
            });
            $(document).ajaxComplete(function () {
                $('#custom-ajax-loader').hide();
            });
            
            let tableName = target.attr('aria-controls');
            let cls = $(document).find('[data-class-item]:first').data('class-item');
            let present = crudUtils.selectedPresentRowCache;
            let absent = crudUtils.selectedAbsentRowCache;
            let attendanceDate = $('[name="attendance_date"]:visible').val();
            let date = new Date(attendanceDate);
            let weekday = ["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];
            let day = weekday[date.getDay()];
            // let trainerId = $('.trainer_id').val();
            let remarks = {};
            // if(present && present.length > 0) {
            //     present.each((ind, val) => {
            //         remarks[ind] = $('textarea[name="remark['+ ind +'][]"').val();
            //     });
            // }

            let customDataTable = $('table.dataTable');
            let checkboxes = customDataTable.find('input[name="id[]"]');
            if(checkboxes && checkboxes.length > 1) {
                checkboxes.each((index, chk) => { 
                    // console.log("inside each");                    
                    remarks[$(chk).val()] = $('textarea[name="remark['+ $(chk).val() +'][]"').val();
                });
            } else if(checkboxes && checkboxes.length == 1) {
                // console.log("inside else if");
                remarks[checkboxes.val()] = $('textarea[name="remark['+ checkboxes.val() +'][]"').val();
            } 
            

            let requestData = {
                'class': cls,
                'present': present,
                'absent': absent,
                'attendance_date': attendanceDate,
                'day':day,
                'financial_year_id':$('.filter-form .financial_year_id').val(),
                'annual_action_plan_id':$('.filter-form .annual_action_plan_id').val(),
                'training_title_id':$('.filter-form .training_title_id').val(),
                'remarks': remarks,
                'filters': crudUtils.getUrlVars()
            };
            // console.log('if save present');
            // console.log(present);
            // console.log('if save absent');
            // console.log(absent);
            // console.log(requestData);
            let url = 'attendances/saveattendance?'+window.location.href.slice(window.location.href.indexOf('?') + 1);
            if(!jQuery.isEmptyObject(present) || !jQuery.isEmptyObject(absent)){
                $.ajax({
                    url: url,
                    type: "POST",
                    data: requestData,
                    dataType: 'json',
                    success: (data) => {
                        if (data.error) {
                            Impiger.showError(data.message);
                        } else {
                            Impiger.showSuccess(data.message);
                            $('.checkboxes,.table-check-all,.present-check-all,.absent-check-all,.custom-radio').prop('checked', false);
                        }
                        target.removeClass("button-loading");
                    },
                    error: (data) => {
                        Impiger.handleError(data);
                        target.removeClass("button-loading");
                    },
                });
            }else{
                console.log('if save');
                target.removeClass("button-loading");
            }
        }
    }
}

$(document).ready(function () {
    TraineeAttendanceUtils.init();
})

function validateStaticFilterForm() {
    $(".filter-form .invalid-feedback").hide();
    $(".filter-form").on("submit", function (e) {
        var bSubmit = true;

        $(".filter-form label.required").each(function () {

            let parentElm = $(this).next(".ui-select-wrapper");
            let ele = parentElm.find("select");

            if (!parentElm.find("select").val() && ele.prop('nodeName') == 'SELECT') {
                parentElm.find(".invalid-feedback").show();
                if (bSubmit) {
                    bSubmit = false;
                }
            } else {
                parentElm.find(".invalid-feedback").hide();
            }
        });

        // console.log($('input[name="filter_table_id"]').val());
        if($('input[name="filter_table_id"]').val() == 'plugins-attendance-table') {
            if($('#attendance_date') && !$('#attendance_date').val()){
                bSubmit = false;
                $('.input-group').find(".invalid-feedback").show();
            } else {
                $('.input-group').find(".invalid-feedback").hide();
                // bSubmit = true;
            }
        }

        // if($('input[name="filter_table_id"]').val() == 'plugins-view-attendance-table') {

        //     if(!$('#attendance_startdate').val()){
        //         bSubmit = false;
        //         $('.input-group').find(".invalid-feedback").show();
        //     } else {
        //         $('.input-group').find(".invalid-feedback").hide();
        //         // bSubmit = true;
        //     }

        //     if(!$('#attendance_enddate').val()){
        //         bSubmit = false;
        //         $('.input-group').find(".invalid-feedback").show();
        //     } else {
        //         $('.input-group').find(".invalid-feedback").hide();
        //         // bSubmit = true;
        //     }
        // }
        
        
       
        if (!bSubmit) {
            e.preventDefault();
            return false;
        }
    });
}

if (typeof crudUtils.validateStaticFilterForm === 'function') {
    crudUtils.validateStaticFilterForm = validateStaticFilterForm;
}


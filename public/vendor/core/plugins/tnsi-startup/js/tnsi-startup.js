"use strict";

let tnsiStartupUtils = {
    formEl: $("#impiger-tnsi-startup-forms-tnsi-startup-form"),

    init: function () {
        this.bindEvents();
    },
    bindEvents: function () {
        tnsiStartupUtils.getSpokeStudentNew();
//        Impiger.initMediaIntegrate();
        $(document).on('change',"#region_id",function(e){
            tnsiStartupUtils.getHubInstitutes(e);
        })
    },
    getSpokeStudentNew: function() {
        $(document).on("change", ".repeater-group .repeater-item-group .form-group input[type='hidden'][value='email_id']+label+input", function () {
            let emailId = $(this).val();
            let repeaterItemEl = $(this).parents('.repeater-item-group:first');
                    if (!emailId) {
                        return false;
                    }
                    let requestData = {
                        email: emailId,
                    };

                    $.ajax({
                        url: "/cruds/get_spoke_student",
                        type: "POST",
                        headers: {
                            "X-CSRF-TOKEN":
                                    $('meta[name="csrf-token"]').attr("content") ||
                                    $('[name="_token"]').val(),
                        },
                        data: requestData,
                        dataType: "json",
                        success: (response) => {
                            if (response && !response.error) {
                                let studentDetails = response;
                                
                                console.log(repeaterItemEl);
                                repeaterItemEl.find('.form-group').each(function () {
                                    let labelEl = $(this).find('label:first');
                                    if (labelEl.length) {
                                        let name = labelEl.prev("input[type='hidden']").val();
                                        if(studentDetails[name]){
                                            $(labelEl).next('input').val(studentDetails[name]);
                                            $(labelEl).next('input').attr("readonly",true);
                                        }                                        
                                    }
                                });
                               
                            } else {
                                if (response && response.error) {
                                    Impiger.showError(response.message);
                                    return false;
                                }
                            }
                        },
                        error: (data) => {
                            tnsiStartupUtils.formEl.trigger("reset");
                            tnsiStartupUtils.formEl.attr("action", tnsiStartupUtils.formAction);
                            
                        },
                    });
        });
    },
    getSpokeStudent: function () {
        $(document).find('.repeater-group > .form-group').each(function () {
            let repeaterItemEl = $(this).find('.repeater-item-group .form-group');
            if (repeaterItemEl.find('[name$="[key]"]').val() == "email_id") {
                let idAttr = repeaterItemEl.find('[name$="[key]"]').next('label').attr('for');
                $(document).on("change", "#" + idAttr, function () {
                    let emailId = $(this).val();
                    if (!emailId) {
                        return false;
                    }
                    let requestData = {
                        email: emailId,
                    };

                    $.ajax({
                        url: "/cruds/get_spoke_student",
                        type: "POST",
                        headers: {
                            "X-CSRF-TOKEN":
                                    $('meta[name="csrf-token"]').attr("content") ||
                                    $('[name="_token"]').val(),
                        },
                        data: requestData,
                        dataType: "json",
                        success: (response) => {
                            if (response && !response.error) {
                                let studentDetails = response;
                                repeaterItemEl.each(function () {
                                    let labelEl = $(this).find('label:first');
                                    if (labelEl.length) {
                                        let idAttr = labelEl.attr('for');
                                        let name = labelEl.prev("input[type='hidden']").val();
                                        if(studentDetails[name]){
                                            $('#' + idAttr).val(studentDetails[name]);
                                            $('#' + idAttr).attr("readonly",true);
                                        }                                        
                                    }
                                });
                               
                            } else {
                                if (response && response.error) {
                                    Impiger.showError(response.message);
                                    return false;
                                }
                            }
                        },
                        error: (data) => {
                            tnsiStartupUtils.formEl.trigger("reset");
                            tnsiStartupUtils.formEl.attr("action", tnsiStartupUtils.formAction);
                            
                        },
                    });
                })
            }
        });
    },
    getHubInstitutes:function(e){
        let region_id = e.target.value;
        $.ajax({
            url: "/cruds/get_hubs_by_region",
            type: "POST",
            headers: {
                "X-CSRF-TOKEN":
                        $('meta[name="csrf-token"]').attr(
                        "content"
                        ) || $('[name="_token"]').val(),
            },
            data: {'region_id':region_id},
            dataType: "json",
            beforeSend: () => {
                // _self.showAjaxLoading();
            },
            success: (response) => {
                if (response.error) {
                    // crudUtils.notifyMessageError(data.message);
                   
                }
                if ($("#hub_institution_id").data("select2")) {
                    CustomScript.initCustomSelect2(
                            $("#hub_institution_id")
                            .select2("destroy")
                            .empty()
                            .prepend(
                                    '<option value="">select</option>'
                                    ),
                            {data: response}
                    );
                }
            },
            complete: () => {
                // _self.hideAjaxLoading();
            },
            error: (data) => {
                // crudUtils.notifyMessageError(data.message);
                // MessageService.handleError(data);
            },
        });
    }
}

jQuery(document).ready(function () {
    tnsiStartupUtils.init();
});



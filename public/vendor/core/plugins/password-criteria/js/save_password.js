"use strict";
let criteria ={'id':1};
let savePasswordUtils = {
    formEl: $('#password-form'),
    getPwdCriteriaAPIUrl: baseUrl + '/get_criteria',

    init: function () {
        this.getPasswordCriteria();
        this.bindEvents();
    },

    bindEvents: function () {
        
    },
    getPasswordCriteria: function () {
        $.ajax({
            url: savePasswordUtils.getPwdCriteriaAPIUrl,
            type: 'get',
            success: response => {
                if (response.error) {
//                    CommonUtils.notifyMessageError(data.message);
                }
                criteria = response;
                savePasswordUtils.initValidation();
            },
            error: data => {
                return false;
            }
        });
    },
    initValidation: function () {
        CommonUtils.extendPasswordCriteriaValidator(criteria);
        let rules = {
            email: {
                    required: true
                },
            old_password: {
                required: true
            },
            password: {
                required: true
            },
            password_confirmation: {
                required: true,
                equalTo: "#password"
            }
        };

        if (!$.isEmptyObject(criteria)) {
            rules.password.rangelength = [criteria['min_length'], criteria['max_length']];
            rules.password.numberCount = criteria['has_number'];
            rules.password.alphabetsRequired = criteria['has_alphabet'];
            rules.password.specialCharCount = criteria['has_special_char'];
        }

        savePasswordUtils.validator = savePasswordUtils.formEl.validate({
            ignore: ":hidden",
            rules: rules,
            invalidHandler: function (event, validator) {
                var alert = $('#add_forgot_form_1_msg');
                alert.removeClass('kt--hide').show();

            },
            submitHandler: function (form) {
                form.submit();
            }
        });
    },

}


$(document).ready(function () {
    let formId = $(document).find('form').attr('id');
    if(formId){
       savePasswordUtils.formEl = $("#"+formId);
    }
    savePasswordUtils.init();
});

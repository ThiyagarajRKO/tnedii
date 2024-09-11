"use strict";

let pwdCriteriaUtils = {
    formEl: $('#pwd_criteria_form'),
    saveAPIUrl: baseUrl + '/admin/password-criterias/save',
    getPwdCriteriaAPIUrl: baseUrl + '/admin/password-criterias/get_pwd_criteria',

    init: function () {
        this.initTouchSpin();
        this.initBootstrapSwitch();
        CustomScript.initCustomSelect2();
        this.initValidation();
        this.bindEvents();
        this.getPasswordCriteria();
        this.removeIsValidClassInEmptyElement();
    },

    bindEvents: function () {
        pwdCriteriaUtils.formEl.on('change', '#logout_format', pwdCriteriaUtils.changeLogoutDuration.bind(this));
        pwdCriteriaUtils.formEl.on('change', '#unlock_format', pwdCriteriaUtils.changeUnlockDuration.bind(this));
    },

    initTouchSpin: function () {// vertical buttons with custom icons:
        $('.kt_touchspin').TouchSpin({
            buttondown_class: 'btn btn-secondary',
            buttonup_class: 'btn btn-secondary',
            verticalbuttons: true,
            verticalup: '<i class="fa fa-angle-up"></i>',
            verticaldown: '<i class="fa fa-angle-down"></i>',
            min:1
        });
    },

    initBootstrapSwitch: function () {
        $('[data-switch=true]').bootstrapSwitch();
    },
    removeIsValidClassInEmptyElement: function () {
        setTimeout(function () {
            pwdCriteriaUtils.formEl.find('.is-valid').each(function (index, element) {
                if (!$(element).val()) {
                    $(element).removeClass('is-valid');
                }
            });
        }, 1000);
    },

    getPasswordCriteria: function () {
        pwdCriteriaUtils.formEl.trigger("reset");
        pwdCriteriaUtils.formEl.find('[selected]').each(function () {
            $(this).removeAttr('selected');
        });
        $.ajax({
            url: pwdCriteriaUtils.getPwdCriteriaAPIUrl,
            type: 'get',
            success: response => {
                if (response.error) {
                    Impiger.showSuccess(data.message);
                }
                let data = response;
                pwdCriteriaUtils.prefilFormData(data);
                pwdCriteriaUtils.formEl.valid();
            },
            error: data => {
                Impiger.showSuccess(data.message);
            }
        });
    },

    enableBootstrapSwitch: function (data, field) {
        if (data[field] <= 0) {
            return false;
        }

        $("[name='" + field + "']").bootstrapSwitch('state', true);
    },

    changeUnlockDuration: function (e) {
        var format = $("#unlock_format").val();
        var values;
        var total = 59;
        if (format == "Hour") {
            total = 24;
        }
        var duration = [];
        for (var i = 1; i <= total; i++) {
            duration.push({
                id: i,
                text: i
            });
        }

        values = JSON.stringify(duration);
        $('#unlock_time').select2({
            placeholder: "Select an option",
            data: JSON.parse(values)
        })

    },
    changeLogoutDuration: function (e) {
        var format = $("#logout_format").val();
        var values;
        var total = 59;
        if (format == "Hour") {
            total = 24;
        }
        var duration = [];
        for (var i = 1; i <= total; i++) {
            duration.push({
                id: i,
                text: i
            });
        }

        values = JSON.stringify(duration);
        $('#logout_time').select2({
            placeholder: "Select an option",
            data: JSON.parse(values)
        });
    },

    prefilFormData: function (data) {
        if ($.isEmptyObject(data)) {
            return false
        }

        pwdCriteriaUtils.enableBootstrapSwitch(data, 'has_alphabet');
        pwdCriteriaUtils.enableBootstrapSwitch(data, 'has_number');
        pwdCriteriaUtils.enableBootstrapSwitch(data, 'has_special_char');
        pwdCriteriaUtils.enableBootstrapSwitch(data, 'has_pwd_expiry');
        pwdCriteriaUtils.enableBootstrapSwitch(data, 'reuse_pwd');
        pwdCriteriaUtils.enableBootstrapSwitch(data, 'auto_lock');
        pwdCriteriaUtils.enableBootstrapSwitch(data, 'auto_unlock');
        pwdCriteriaUtils.enableBootstrapSwitch(data, 'auto_logout');
        pwdCriteriaUtils.setDuration(data, 'unlock_time');
        pwdCriteriaUtils.setDuration(data, 'logout_time');
        pwdCriteriaUtils.formEl.loadDataFromJSON(data);
    },

    initValidation: function () {
        CommonUtils.extendValidatorMethod();

        jQuery.validator.addMethod("minLengthCheck", function (value, element, params) {
            let alphabetLength = ($('[name="has_alphabet"]').prop('checked')) ? parseInt($('[name="alphabet_cnt"]').val()) || 0 : 0;
            let splCharLength = ($('[name="has_special_char"]').prop('checked')) ? parseInt($('[name="special_char_count"]').val()) || 0 : 0;
            let numLength = ($('[name="has_number"]').prop('checked')) ? parseInt($('[name="number_min_count"]').val()) || 0 : 0;
            let totalMinLength = alphabetLength + splCharLength + numLength;
            return isNaN(value) && isNaN(totalMinLength) || (Number(value) >= Number(totalMinLength));
        }, 'Must be greater than all the summation of min length.');

        pwdCriteriaUtils.validator = pwdCriteriaUtils.formEl.validate({
            // Validate only visible fields
            ignore: ":hidden",

            // Validation rules
            rules: {
                //= Step 1
               
                min_length: {
                    number: true,
                    requiredNonZero: true,
                    minLengthCheck: true
                },
                max_length: {
                    number: true,
                    requiredNonZero: true,
                    greaterThanOrEqualTo: "#min_length"
                },
                alphabet_count: {
                    notEqualToEmpty: $('[name="has_alphabet"]')
                },
                number_min_count: {
                    notEqualToEmpty: $('[name="has_number"]')
                },
                alphabet_type: {
                    notEqualToEmpty: $('[name="has_alphabet"]')
                },
                special_char_count: {
                    notEqualToEmpty: $('[name="has_special_char"]')
                },
                allowed_spec_char: {
                    notEqualToEmpty: $('[name="has_special_char"]')
                },
                validity_period: {
                    notEqualToEmpty: $('[name="has_pwd_expiry"]')
                },
                reuse_after_x_times: {
                    notEqualToEmpty: $('[name="reuse_pwd"]')
                },
                invalid_attempt_allowed_time: {
                    notEqualToEmpty: $('[name="auto_lock"]')
                },
                unlock_format: {
                    notEqualToEmpty: $('[name="auto_unlock"]')
                },
                unlock_time: {
                    notEqualToEmpty: $('[name="auto_unlock"]')
                },
                logout_format: {
                    notEqualToEmpty: $('[name="auto_logout"]')
                },
                logout_time: {
                    notEqualToEmpty: $('[name="auto_logout"]')
                },
            },
            // Display error
            invalidHandler: function (event, validator) {
                validator = validator || {};

                if (!$.isEmptyObject(validator.invalid)) {
                    setTimeout(function () {
                        pwdCriteriaUtils.formEl.find('[name="' + Object.keys(validator.invalid)[0] + '"]').focus();
                    }, 100);

                }
            },

            // Submit valid form
            submitHandler: function (form) {
                let data = $(form).getFormDataToJSON();
                data['number_min_count'] = $('[name="number_min_count"]').val();
                data['has_alphabet'] = $('[name="has_alphabet"]').prop('checked') ? 1 : 0;
                data['has_number'] = $('[name="has_number"]').prop('checked') ? 1 : 0;
                data['has_special_char'] = $('[name="has_special_char"]').prop('checked') ? 1 : 0;
                data['has_pwd_expiry'] = $('[name="has_pwd_expiry"]').prop('checked') ? 1 : 0;
                data['reuse_pwd'] = $('[name="reuse_pwd"]').prop('checked') ? 1 : 0;
                data['alphabet_type'] = $('[name="alphabet_type"]').val();
                data['auto_lock'] = $('[name="auto_lock"]').prop('checked') ? 1 : 0;
                data['auto_unlock'] = $('[name="auto_unlock"]').prop('checked') ? 1 : 0;
                data['auto_logout'] = $('[name="auto_logout"]').prop('checked') ? 1 : 0;
                $.ajax({
                    url: pwdCriteriaUtils.saveAPIUrl,
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: data,
                    dataType: 'json',
                    success: response => {
                        if (response.error) {
                            Impiger.showError(response.message);
                        }
                        Impiger.showSuccess(response.message);      
                        pwdCriteriaUtils.getPasswordCriteria();

                    },
                    
                    error: data => {
                        Impiger.showError();
                    }
                });
            }
        });
    },

    setDuration: function (data, id) {
        data = data || {};
        var total = 59;
        var updateData = data.logout_format;
        if (id == "unlock_time") {
            updateData = data.unlock_format;
        }
        if (updateData == 'Hour') {
            total = 24;
        }
        var duration = [];
        var values;
        for (var i = 1; i <= total; i++) {
            duration.push({
                id: i,
                text: i
            });
        }
        values = JSON.stringify(duration);
        $('#'+id).select2({
            placeholder: "Select an option",
            data: JSON.parse(values)
        });
    }
}

$("input[name='invalid_attempt_allowed_time']").TouchSpin({
    buttondown_class: 'btn btn-secondary',
    buttonup_class: 'btn btn-secondary',
    verticalbuttons: true,
    verticalup: '<i class="fa fa-angle-up"></i>',
    verticaldown: '<i class="fa fa-angle-down"></i>',
    max: 5
});

$(document).ready(function () {
    pwdCriteriaUtils.init();
});

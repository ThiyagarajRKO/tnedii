/**
 * @desc will hold common functions for ui related
 *
 */

 $(document).ready(function () {
    const urlSearchParams = new URLSearchParams(window.location.search);
    const params = Object.fromEntries(urlSearchParams.entries());
    if (params.user_navigation) {
        $('.nav-tabs li.nav-item').eq(0).find('a').trigger('click');
        $('.user-profile .crop-avatar').hide();
    }

    $(document).on('click', 'a.backBtn', function (e) {
        e.preventDefault();
        if (params.user_navigation) {
            location.href = '/admin/users/edit/' + params.user_navigation;
        } else {
            location.href = '/admin/system/users/';
        }
    })
});

class CommonUtils {
    init() {
        this.bindEvents();
    }

    bindEvents() {
    }
    static extendValidatorMethod() {
        $.validator.addMethod("customAlphanumeric", function (value, element) {
            return this.optional(element) || /^[a-zA-Z0-9-_.\&\s]+$/.test(value);
        }, "Please use only letters, numbers, space, -, _ ,. and &");

        jQuery.validator.addMethod("allowedImgFileTypes", function (value, element) {
            if (!value) {
                return true;
            }
            let ext = value.split('.').pop().toLowerCase();
            return ($.inArray(ext, ['gif', 'png', 'jpg', 'jpeg']) != -1);
        }, "Please enter valid file format.");

        jQuery.validator.addMethod("allowedFileTypes", function (value, element) {
            if (!value) {
                return true;
            }
            let ext = value.split('.').pop().toLowerCase();
            return ($.inArray(ext, ['gif', 'png', 'jpg', 'jpeg', 'pdf', 'doc', 'docx']) != -1);
        }, "Please enter valid file format.");

        jQuery.validator.addMethod("allowedDocFileTypes", function (value, element) {
            if (!value) {
                return true;
            }
            let ext = value.split('.').pop().toLowerCase();
            return ($.inArray(ext, ['pdf', 'doc', 'docx', 'txt']) != -1);
        }, "Please enter valid file format.");

        jQuery.validator.addMethod("emailAllowBlank", function (value, element) {
            if (!value) {
                return true;
            } else if (/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/.test(value)) {
                return true;
            } else {
                return false;
            }
        }, "Please enter a valid Email.");

        jQuery.validator.addMethod("greaterEndDateThanStartDate", function (value, element, params) {
            if (!/Invalid|NaN/.test(new Date(value))) {
                return new Date(value) >= new Date($(params).val());
            }

            return isNaN(value) && isNaN($(params).val())
                || (Number(value) > Number($(params).val()));
        }, "End date must be greater than start date.");



        jQuery.validator.addMethod("notEqualToEmpty", function (value, element, params) {
            if ($(params).prop('checked')) {
                if (parseInt(value) > 0 || value) {
                    return true;
                }

                return false;
            }

            return true;
        }, "This is required field.");

        jQuery.validator.addMethod("requiredNonZero", function (value, element, param) {
            return value && parseInt(value) > 0;
        }, 'This is required field.');


        $.validator.addMethod('fileSize', function (value, element, param) {
            return this.optional(element) || (Math.round(element.files[0].size / (1024 * 1024)) <= param)
        }, 'File size must be less than {0} MB');
        $.validator.addMethod('validMonths', function (value, element) {
            return this.optional(element) || value <= 11;
        }, 'Month must be less than or equal to 11');
        $.validator.addMethod('validWeeks', function (value, element) {
            return this.optional(element) || value <= 4;
        }, 'Month must be less than or equal to 4');
    }
    static extendPasswordCriteriaValidator(criteria = {}) {
        criteria = criteria || {};
        $.validator.addMethod("numberCount", function (value, element) {
            if (criteria['has_number'] <= 0) {
                return true;
            }

            var regx = new RegExp('[0-9]{' + criteria['number_min_count'] + '}');
            return this.optional(element) || regx.test(value);
        }, "You must enter atleast " + criteria['number_min_count'] + " number");

        $.validator.addMethod("specialCharCount", function (value, element) {
            if (criteria['has_special_char'] <= 0) {
                return true;
            }

            var regx = new RegExp('[' + criteria['allowed_spec_char'] + ']{' + criteria['special_char_count'] + '}');
            return this.optional(element) || regx.test(value);
        }, "You must enter atleast " + criteria['special_char_count'] + " " + criteria['allowed_spec_char'] + " of these symbols");

        let type = 3;
        let config = {
            1: {
                regex: 'A-Z',
                label: ' Upper Case'
            },
            2: {
                regex: 'a-z',
                label: ' Lower Case'
            },
            3: {
                regex: 'a-zA-Z',
                label: ''
            }
        };

        if (CommonUtils.isValidArray(criteria['alphabet_type'])) {
            if (criteria['alphabet_type'].length > 1) {
                type = 3;
            } else if (criteria['alphabet_type'][0] == 1) {
                type = 1;
            } else if (criteria['alphabet_type'][0] == 2) {
                type = 2;
            }
        }

        $.validator.addMethod("alphabetsRequired", function (value, element) {
            if (criteria['has_alphabet'] <= 0) {
                return true;
            }

            var regx = new RegExp('[' + config[type]['regex'] + ']{' + criteria['alphabet_count'] + '}');
            return this.optional(element) || regx.test(value);
        }, "You must enter atleast " + criteria['alphabet_count'] + config[type]['label'] + " alphabet(s)");
    }
    static isValidArray(inputArray) {
        if (inputArray && $.isArray(inputArray) && inputArray.length > 0) {
            return true;
        }

        return false;
    }
    notifyMessageError(message) {
        $.toast({
            heading: "error",
            text: message,
            position: "top-right",
            icon: "error",
            hideAfter: 3000,
            stack: 6,
        });
    }
    notifyMessageSuccess(message) {
        $.toast({
            heading: "success",
            text: message,
            position: "top-right",
            icon: "success",
            hideAfter: 3000,
            stack: 6,
        });
    }
    static convertToRoman(num) {
        var romanMatrix = [
            [1000, 'M'], [900, 'CM'], [500, 'D'], [400, 'CD'], [100, 'C'], [90, 'XC'],
            [50, 'L'], [40, 'XL'], [10, 'X'], [9, 'IX'], [5, 'V'], [4, 'IV'], [1, 'I']
        ];
        if (num === 0) {
            return '';
        }
        for (var i = 0; i < romanMatrix.length; i++) {
            if (num >= romanMatrix[i][0]) {
                return romanMatrix[i][1] + CommonUtils.convertToRoman(num - romanMatrix[i][0]);
            }
        }
    }
}

$(document).ready(() => {
    (new CommonUtils()).init();
    window.CommonUtils = CommonUtils;
});

$.fn.getFormDataToJSON = function (skipEmpty = false) {
    let $form = $(this)
    let unIndexedArray = $form.serializeArray();
    let indexedArray = {};

    $.map(unIndexedArray, function (n, i) {
        if (skipEmpty) {
            if (n['value']) {
                indexedArray[n['name']] = n['value'];
            }
        } else {
            if (indexedArray[n['name']] !== undefined) {
                if (!indexedArray[n['name']].push) {
                    indexedArray[n['name']] = [indexedArray[n['name']]];
                }
                indexedArray[n['name']].push(n['value'] || '');
            } else {
                indexedArray[n['name']] = n['value'] || '';
            }
        }

    });

    return indexedArray;
};

$.fn.loadDataFromJSON = function (data, ignoreDateFormatCheck = false, excludeInputType = []) {
        let $form = $(this)

        excludeInputType = ($.isArray(excludeInputType)) ? excludeInputType : [];

        if (typeof (data) == "string" || $.isEmptyObject(data)) {
            return false;
        }

        $.each(data, function (key, value) {
            let $elem = $('[name="' + key + '"]', $form);
            let type = $elem.attr('type');

            if ($.inArray(type, excludeInputType) === -1) {
                if (type == 'radio' || type == 'checkbox') {
                    $('[name="' + key + '"][value="' + value + '"]').prop('checked', true)
                } else if (type == 'checkbox' && (value == true || value == 'true')) {
                    $('[name="' + key + '"]').prop('checked', true)
                } else {
                    if (type == 'file') {
                        return;
                    }
                 else if ($elem.is('.custom_date_picker,.common_date_picker,#dob,.custom_month_picker,.exp_date_picker,#expiry_date')) {
                    let dateValue = (ignoreDateFormatCheck) ? value : value; //CommonUtils.formatDate(value);
                    dateValue = (dateValue == '0000-00-00' || dateValue == 'NaN-NaN-NaN') ? '' : dateValue;
                    $elem.val(dateValue);
                    $elem.bootstrapDP('update')
                } else if ($elem.is('.attachment-url')) {
                    $elem.next().find('a').text(value);
                    $elem.next().find('a').attr('href', $elem.next().find('a').attr('href') + value);
                } else if ($elem.is('select.ui-select')) {
                        if (value && $.isArray(value) && value.length > 0) {
                            $elem.val();
                            $elem.val(value);
                        } else if (value && typeof value == "string") {
                            value = value.split(",");
                            $elem.val(value);
                        } else {
                            $elem.val(value);
                        }
                        $elem.trigger('change', true);
                    } else if($elem.is('[name="geo_coordinates"]')) {

                } else {
                    $elem.val(value)
                }
            }
        }
    })
};
$.fn.loadViewDataFromJSON = function (data) {
    let $form = $(this)
    if (typeof (data) == "string" || $.isEmptyObject(data)) {
        return false;
    }

    $.each(data, function (key, value) {
        let $elem = $('[name="' + key + '"]', $form);
        $elem.text(value);
    });
};
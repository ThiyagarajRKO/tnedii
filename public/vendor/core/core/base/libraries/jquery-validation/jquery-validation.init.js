"use strict";
var valGetParentContainer = function (element) {
    var element = $(element);

    if ($(element).closest('.form-group-sub').length > 0) {
        return $(element).closest('.form-group-sub')
    } else if ($(element).closest('.bootstrap-select').length > 0) {
        return $(element).closest('.bootstrap-select')
    } else {
        return $(element).closest('.form-group');
    }
}

jQuery.validator.setDefaults({
    errorElement: 'div', //default input error message container
    focusInvalid: false, // do not focus the last invalid input
    ignore: "",  // validate all fields including form hidden input

    errorPlacement: function (error, element) { // render error placement for each input type
        var element = $(element);

        var group = valGetParentContainer(element);
        var help = group.find('.form-text');

        if (group.find('.valid-feedback, .invalid-feedback').length !== 0) {
            return;
        }

        element.addClass('is-invalid');
        error.addClass('invalid-feedback');

        if (help.length > 0) {
            help.before(error);
        } else {
            if (element.closest('.bootstrap-select').length > 0) {     //Bootstrap select
                element.closest('.bootstrap-select').find('.bs-placeholder').parent().after(error);
            } 
            else if (element.closest('.input-group').length > 0) {   //Bootstrap group
                element.closest('.input-group').after(error);
            }
             else {                                                   //Checkbox & radios
                if (element.is(':checkbox')) {
                    element.closest('.kt-checkbox').find('> span').after(error);
                } else {
                    element.after(error);
                }
            }            
        }
    },

    highlight: function (element) { // hightlight error inputs
        var group = valGetParentContainer(element);
        group.addClass('validate');
        group.addClass('is-invalid');
        $(element).removeClass('is-valid');
    },

    unhighlight: function (element) { // revert the change done by hightlight
        var group = valGetParentContainer(element);
        group.removeClass('validate');
        group.removeClass('is-invalid');
        $(element).removeClass('is-invalid');
        $(element).addClass('is-valid');

    },

    success: function (label, element) {
        var group = valGetParentContainer(element);
        group.removeClass('validate');
        group.find('.invalid-feedback').remove();
    }
});

jQuery.validator.addMethod("email", function (value, element) {
    if (/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/.test(value)) {
        return true;
    } else {
        return false;
    }
}, "Please enter a valid Email.");

jQuery.validator.addMethod("greaterThan10Years",
    function (value, element, params) {

        if (!/Invalid|NaN/.test(new Date(value))) {
            let currentDate = new Date(value);
            currentDate.setFullYear(currentDate.getFullYear() - 10);
            return currentDate > new Date($(params).val());
        }

        return isNaN(value) && isNaN($(params).val())
            || (Number(value) > Number($(params).val()));
    }, 'Must be 10 years greater than {0}.');



$.validator.addMethod("alphabets", function (value, element) {
    return this.optional(element) || /^[a-zA-Z-_.\&\s]+$/.test(value);
}, "Please use only letters, space,-,_ ,. and &");

$.validator.addMethod("customAlphanumeric", function (value, element) {
    return this.optional(element) || /^[a-zA-Z0-9-_.\&\s]+$/.test(value);
}, "Please use only letters, numbers, space, -, _ ,. and &");

$.validator.addMethod("alphanumeric", function (value, element) {
    return this.optional(element) || /^[a-zA-Z0-9]+$/.test(value);
}, "Please use only letters & numbers");

$.validator.addMethod("validMarks", function (value, element) {
    return this.optional(element) || /^\+?[0-9]*\.?[0-9]+$/.test(value);
}, "Please use only positive numbers");

$.validator.addMethod("validExamMarks", function (value, element, zero) {
    return !(value <= zero);
},
    "Please enter greater than 0");
    
$.validator.addMethod("greaterThanMaxMark", function (value, element, maxMark) {
    return isNaN(value) && isNaN($(maxMark).val())
        || (Number(value) <= Number($(maxMark).val()));
}, function (maxMark, element) {
    return "Please enter marks within 0 - " + $(maxMark).val();
});

$.validator.addMethod("alphanumericspace", function(value, element) {
   return this.optional(element) || /^[a-zA-Z0-9\s]+$/.test(value);
}, "Please use only letters , numbers & spaces");

$.validator.addMethod("greaterStart", function (value, element, params) {
    return this.optional(element) || new Date(value) >= new Date($(params).val());
},'Must be greater than start date.');

$.validator.addMethod(
        "validDOB",
        function(value, element) {
        if(value!=""){
            var from = value.split("-"); // MM DD YYYY
            var day = from[0];
            var month = from[1];
            var year = from[2];
            var age = 18;
            var mydate = new Date();

            mydate.setFullYear(year, month-1, day);

            var currdate = new Date();
            var setDate = new Date();
            setDate.setFullYear(mydate.getFullYear() + age, month-1, day);

            if ((currdate - setDate) > 0){
                return true;
            }else{
                return false;
            }
            }
            else{
                return true;
            }

        },
        "Sorry, DOB must be greater than 18 years of age"
    );
$.validator.addMethod("alphanumericSpecialChar", function(value, element) {
   return this.optional(element) || /^[a-zA-Z0-9-_.,&@#?|/\s]+$/.test(value);
}, "Please use only letters , numbers & spaces, -, _, .,&,/,#@");
$.validator.addMethod("numbersOnly", function(value, element) {
   return this.optional(element) || /^[1-9\d*]+$/.test(value);
}, "Please use only numbers");
$.validator.addMethod("greaterThan", function(value, element, params) {
    var enDate = new Date(value);
    enDate.setFullYear(value.substr(6,4),(value.substr(3,2)-1),value.substr(0,2));
    var stDate = new Date($(params).val());
    stDate.setFullYear($(params).val().substr(6,4),($(params).val().substr(3,2)-1),$(params).val().substr(0,2));
    if (!/Invalid|NaN/.test(stDate)) {
        return enDate>stDate;
    }
    return isNaN(value) && isNaN($(params).val())
        || (Number(value) > Number($(params).val()));
},'Must be greater than {0}.');
$.validator.addMethod("greaterThanOrEqualTo", function(value, element, params) {
    var enDate = new Date(value);
    enDate.setFullYear(value.substr(6,4),(value.substr(3,2)-1),value.substr(0,2));
    var stDate = new Date($(params).val());
    stDate.setFullYear($(params).val().substr(6,4),($(params).val().substr(3,2)-1),$(params).val().substr(0,2));
    if (!/Invalid|NaN/.test(stDate)) {
        return enDate>=stDate;
    }
    return isNaN(value) && isNaN($(params).val())
        || (Number(value) >= Number($(params).val()));
},'Must be greater Than or Equal to {0}.');
$.validator.addMethod("validDate", function(value, element) {
        return this.optional(element) || moment(value,"DD-MM-YYYY").isValid();
}, "Please enter a valid date in the format DD-MM-YYYY");


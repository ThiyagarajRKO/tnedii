"use strict"
let academicUtils = {
    init: function () {
        this.bindEvents();
    },
    bindEvents: function () {
        if ($("#impiger-financial-year-forms-financial-year-form").length) {
            $(document).on("change", "#session_start", function () {
                var validator = $("#impiger-financial-year-forms-financial-year-form").validate();
                validator.resetForm();
            });
            $(document).on("change", "#session_end", function () {
                var validator = $("#impiger-financial-year-forms-financial-year-form").validate();
                validator.resetForm();
            });
        }
    }
}
$(document).ready(function () {
    academicUtils.init();
});
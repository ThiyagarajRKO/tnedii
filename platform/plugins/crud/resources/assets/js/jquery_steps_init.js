$(document).ready(function () {
    $(".submitted-button").hide()

    if ($("#wizard-step").length) {
        let form = $("#wizard-step").parents('form:first');
        let isViewForm = (form.hasClass('viewForm')) ? true : false;
        $("#wizard-step").steps({
            headerTag: "h3",
            bodyTag: "section",
            transitionEffect: "fade",
            titleTemplate: "<span class='step'>#index#</span> #title#",
            autoFocus: true,
            enableAllSteps: (isViewForm) ? true : false,
            enableContentCache: false,
            labels: {
                finish: (isViewForm) ? "Back" : "Save"
            },
            onInit: function () {
                hideFormActionOnWizard();
            },
            onFinished: function (event, currentIndex) {
                if (isViewForm) {
                    window.history.back();
                } else {
                    if ($('#impiger-student-forms-student-form #wizard-step-p-4:visible').length && studentUtils && !studentUtils.validatePaymentSlips()) {
                        return false;
                    }
                    if (form.valid()) {
                        $('.btn-set [name="submit"][value="save"]').trigger('click');
                    }
                }
            },
            onStepChanging: function (event, currentIndex, newIndex) {
                $('.applyBtn').hide();
                // Always allow going backward even if the current step contains invalid fields!
                if (currentIndex > newIndex) {
                    return true;
                }

                form.validate().settings.ignore = ":disabled,:hidden";
                let isFormValid = form.valid();
                if ($('#impiger-student-forms-student-form #wizard-step-p-2:visible').length && studentUtils && !studentUtils.validateRelationDetail()) {
                    return false;
                }

                if ($('#impiger-student-forms-student-form #wizard-step-p-3:visible').length && studentUtils && !studentUtils.validateAcademicCertificates()) {
                    return false;
                }

                return isFormValid;
            },
            onStepChanged: function (event, currentIndex) {
                if (isViewForm) {
                    return false;
                }
                let lastStepIndex = $('#wizard-step ul[role="tablist"] li.last').index();
                if (lastStepIndex == currentIndex) {
                    $('ul[aria-label=Pagination] li a[href="#finish"]').html('<i class="fa fa-save"></i> Save');
                    if (($('#impiger-user-forms-user-form').length == 0 || !crudUtils.lastPart.startsWith('create')) && !crudUtils.frontEndForm) {
                    let $input = $('<li aria-hidden="false" class="applyBtn"><a class="btnSuccess" href="#apply" role="menuitem"><i class="fa fa-check-circle"></i>Save &amp; Edit</a></li>');
                    $input.appendTo($('ul[aria-label=Pagination]'));
                    }
                }
            },
            onFinishing: function (event, currentIndex) {
                form.validate().settings.ignore = ":disabled";
                return form.valid();
            },
        });
        $(".steps ul > li > a span").removeClass("number")

        form.submit(function (e) {
            if (!form.valid()) {
                let wizardContentElId = $('.is-invalid:first').parents('section:first').attr('id');
                if ($('ul[role="tablist"] [aria-controls="' + wizardContentElId + '"]').length) {
                    $('ul[role="tablist"] [aria-controls="' + wizardContentElId + '"]').trigger('click');
                    setTimeout(function () {
                        $('form').valid()
                        $('.btn_remove_attachment').show();
                    }, 100);
                    e.preventDefault();
                    return false;
                }
            }
            
            setTimeout(function () {
                $('.btn_remove_attachment').show();
            }, 100);
        });

        $(document).on('click', '.applyBtn [href="#apply"]', function () {
            if ($('#impiger-student-forms-student-form #wizard-step-p-4:visible').length && studentUtils && !studentUtils.validatePaymentSlips()) {
                return false;
            }
            $('.btn-set [name="submit"][value="apply"]:first').trigger('click');
        });
    }

});

function hideFormActionOnWizard() {
    if (!$('#wizard-step').length) {
        return false;
    }

    $('.form-actions').hide();
    let lastStepIndex = $('#wizard-step ul[role="tablist"] li.last').index();
    let currentStepIndex = $('#wizard-step ul[role="tablist"] li.current').index();
    if (lastStepIndex == currentStepIndex) {
        $('.form-actions').show();
    }
}
$(document).ready(function () {
    $(".submitted-button").hide()

    if ($("#wizard-step").length) {
        let form = $("#wizard-step").parents('form:first');
        let isViewForm = (form.hasClass('viewForm')) ? true : false;
        renderWizard(form,isViewForm);
        $(".steps ul > li > a span").removeClass("number");
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
            if ($('#impiger-user-forms-user-form #wizard-step-p-4:visible').length && userUtils && !userUtils.validateCurriculumVitae()) {
                return false;
            }
            $('.btn-set [name="submit"][value="apply"]:first').trigger('click');
        });
        
        $(document).on('click', '.continueBtn', function () {
            if ($('#impiger-vendor-request-forms-vendor-request-form #wizard-step-p-5:visible').length && vendorRequestUtils && !vendorRequestUtils.validateDocumentUploads()) {
                  return false;
            }
            destroyWizard();
        }); 
        $(document).on('click', '#previousBtn', function () {      
            $('#wizard-step').show();
            $('.previewForm').html("");
            $('.form-actions-reset').hide();
            $(document).find('#printPreview').remove();
        });
    }
    
});

function hideFormActionOnWizard() {
    if (!$('#wizard-step').length) {
        return false;
    }
    var actionClass = (resetAction) ? '.form-actions-reset' : '.form-actions';
    $(actionClass).hide();
    let lastStepIndex = $('#wizard-step ul[role="tablist"] li.last').index();
    let currentStepIndex = $('#wizard-step ul[role="tablist"] li.current').index();
    if (lastStepIndex == currentStepIndex) {        
        $(actionClass).show();
    }

      
      let $input = $('<li aria-hidden="false" class="draftBtn"><input type="button" name="draft" class="btn btnDraft cancel" formnovalidate="formnovalidate" id="saveDraft" value="Save as Draft" role="menuitem"></li>');
      $input.appendTo($('ul[aria-label=Pagination]'));

}

function renderWizard(form,isViewForm,preventPreview = false){
    $("#wizard-step").steps({
            headerTag: "h3",
            bodyTag: "section",
            transitionEffect: "fade",
            titleTemplate: "<span class='step'>#index#</span> #title#",
            autoFocus: true,
            enableCancelButton:(resetAction && !crudUtils.frontEndForm) ? true : false,
            enableAllSteps: (isViewForm) ? true : false,
            enableContentCache: false,
            labels: {
                finish: (isViewForm) ? "Back" : "Save"
            },
            onInit: function () {
                hideFormActionOnWizard();                
            },
            onCanceled:function(){
                window.history.back();
            },
            onFinished: function (event, currentIndex) {
                if (isViewForm) {
                    window.history.back();
                } else {
                    if (form.valid()) {
                        $('.btn-set [name="submit"][value="save"]').trigger('click');
                    }
                }
            },
            onStepChanging: function (event, currentIndex, newIndex) {
                $('.applyBtn').hide();
                $('.continueBtn').hide();
                // Always allow going backward even if the current step contains invalid fields!
                if (currentIndex > newIndex) {
                    return true;
                }
                if (resetAction && !crudUtils.frontEndForm && $('.cancelBtn').length) {
                    $(document).on('click','.cancelBtn',function(){
                        window.history.back();
                    });
                }
                form.validate().settings.ignore = ":disabled,:hidden";
                let isFormValid = form.valid();
                if (!isViewForm) {                    
                    if ($('#impiger-vendor-request-forms-vendor-request-form #wizard-step-p-2:visible').length && vendorRequestUtils && !vendorRequestUtils.validateHoldingPercentage()) {
                        return false;
                    }                   
                    if ($('#impiger-vendor-request-forms-vendor-request-form #wizard-step-p-2:visible').length && vendorRequestUtils && !vendorRequestUtils.validateAuthorizedSignature()) {
                        return false;
                    }                   
                }
                return isFormValid;
            },
            onStepChanged: function (event, currentIndex) {
                if (isViewForm) {
                    return false;
                }
                let lastStepIndex = $('#wizard-step ul[role="tablist"] li.last').index();
                if (lastStepIndex == currentIndex) {
                    let buttonName = (crudUtils.frontEndForm) ? 'Submit' : '<i class="fa fa-save"></i> Save';
                    $('ul[aria-label=Pagination] li a[href="#finish"]').html(buttonName);
                    if (!resetAction && ($('#impiger-user-forms-user-form').length == 0 || !crudUtils.lastPart.startsWith('create')) && !crudUtils.frontEndForm) {
                        let $input = $('<li aria-hidden="false" class="applyBtn"><a class="btnSuccess" href="#apply" role="menuitem"><i class="fa fa-check-circle"></i>Save &amp; Edit</a></li>');
                        $input.appendTo($('ul[aria-label=Pagination]'));
                    }
                    if ($('#impiger-vendor-request-forms-vendor-request-form').length) {
                        $('ul[aria-label=Pagination] li a[href="#finish"]').hide();
                        let $input = $('<li aria-hidden="false" class="continueBtn"><a role="menuitem">Continue</a></li>');
                        $input.appendTo($('ul[aria-label=Pagination]'));
                    }                    
                }
            },
            onFinishing: function (event, currentIndex) {
                form.validate().settings.ignore = ":disabled";
                return form.valid();
            },
        });
}

function destroyWizard() {
    $('#wizard-step').hide();
    if(!$('.previewForm').length) {
        $("<div class='previewForm' id='printPreviewForm'>mydiv</div>").insertBefore('#wizard-step');
    }
    let html = getPreviewDetailsHtml();
    $('.previewForm').html(html);
    $(document).find('#main').prepend("<a class='btn btn-info' id='printPreview'><i class='fa fa-print'></i> Print </button>");
     $('.form-actions-reset').show();
     $(document).find('#previousBtn').show();
     $(document).find('.cancelBtn').show();
     $(document).find('#resetBtn').hide();
     $(document).find('.form-actions > .form-actions-fixed-bottom > .btn-set').css('margin-left', '70%');
     $(document).find('.current-info').addClass('hidden');  
}

function getPreviewDetailsHtml() {
    let html = "";
    $('#wizard-step .content section.wizardLayout').each(function () {
        let title = $('#wizard-step ul li').find('[aria-controls="' + $(this).attr('id') + '"]').html();
        html += `<div class="col-md-12 grouppedLayout">
        <fieldset><legend class="grouppedLegend legendTitle"> ` + title + `</legend>`;
        let sectionDetail = $(this).clone();
        let subFormRepeaterEl = $(sectionDetail).find('.subFormRepeater');

        $(sectionDetail).find('.subFormRepeater > .form-group').each(function () {
            let legendTitle = ($(this).find('legend.subTitle').length) ? $(this).find('legend.subTitle').html() : $(this).parents('fieldset').find('> legend.subTitle').html();
            if (legendTitle != undefined) {
                html += `<legend class="grouppedLegend">` + legendTitle + `</legend>`;
            }
            html += `<div class="row">`;
            $(this).find('.row > .form-group').each(function (i) {
                let labelEl = $(this).find('label:first');
                if (labelEl.length) {
                    let imageEl = $(this).find('.image-box');
                    let multiFileEl = $(this).find('.list-photos-gallery');
                    html += (multiFileEl.length) ? `<div class="form-group col-md-12">` : `<div class="form-group col-md-4">`
                    let idAttr = labelEl.attr('for');
                    html += `<label class="control-label highlight">` + labelEl.text() + `</label>`;
                    if (imageEl.length) {
                        let imgVal = (imageEl.find('.preview-image-wrapper').length) ? imageEl.find('.preview-image-wrapper').html() : imageEl.find('.attachment-details').html();
                        html += `<div class="image-box">` + imgVal + `</div>`;
                    } else if (multiFileEl.length) {
                        html += `<div class="form-group multi-file-upload-container col-md-8">` + multiFileEl.html() + `</div>`;
                    } else {
                        let value = $('#' + idAttr).val() || $(this).find('[name="' + idAttr + '"]').val();
                        let inputEl = document.getElementById(idAttr);
                        if ($('[name="' + idAttr + '"]').getType() == "select" && $('[name="' + idAttr + '"]').val()) {
                            value = $('[name="' + idAttr + '"] option:selected').text();
                        }
                        value = value || "";
                        html += `<div class="customStaticCls">` + value + `</div>`;
                    }
                    html += `</div>`;
                }
            });
            html += `</div>`;
        });
        $(sectionDetail).find('fieldset').each(function () {
            let legendTitle = ($(this).find('legend.subTitle').length) ? $(this).find('legend.subTitle').html() : $(this).parents('fieldset').find('> legend.subTitle').html();
            if (!subFormRepeaterEl.length) {
                if (legendTitle != undefined) {
                    html += `<legend class="grouppedLegend">` + legendTitle + `</legend>`;
                }
                html += `<div class=row>`;
            }
            $(this).find('.form-group').each(function () {
                let labelEl = $(this).find('label:first');

                if (!subFormRepeaterEl.length && labelEl.length) {
                    let imageEl = $(this).find('.image-box');
                    html += `<div class="form-group col-md-3">`
                    let idAttr = labelEl.attr('for');
                    html += `<label class="control-label highlight">` + labelEl.text() + `</label>`;
                    if (imageEl.length) {
                        html += `<div class="image-box">` + imageEl.find('.preview-image-wrapper').html() + `</div>`;
                    } else {
                        let value = $('#' + idAttr).val() || $(this).find('[name="' + idAttr + '"]').val();
                        let inputEl = document.getElementById(idAttr);
                        if ($('[name="' + idAttr + '"]').getType() == "select") {
                            value = $('[name="' + idAttr + '"] option:selected').text();
                        }
                        value = value || "";
                        html += `<div class="customStaticCls">` + value + `</div>`;
                    }
                    html += `</div>`;
                }
            })
            if (!subFormRepeaterEl.length) {
                html += `</div>`;
            }
        });
        html += `</div>`;
    })

    return html;
}

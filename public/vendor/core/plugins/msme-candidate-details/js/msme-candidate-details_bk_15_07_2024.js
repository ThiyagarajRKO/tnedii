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

                    if ($elem.is('select.ui-select')) {
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
                    } else {
                        $elem.val(value)
                    }
                }
            }
        })
    };
"use strict";

let msmeUtils = {
    formEl: $("#impiger-msme-candidate-details-forms-msme-candidate-details-form"),
    init: function () {
        this.bindEvent();
    },
    bindEvent: function () {
        $(document).on('change', 'input[name="candidate_msme_ref_id"]', function (e) {
            msmeUtils.getMsmeCandidateData(e);            
        });        
    },
    getMsmeCandidateData:function(e){
        let target = e.target;
        let msmeId = $(target).val();
        let scheme = $("#scheme option:selected").text();
        if(!msmeId && !scheme){
            Impiger.showError("Required Param Missing");
        }
        let requestData = {
          "canditateId" : msmeId,  
          "scheme" : scheme,  
        };
        $.ajax({
            url:"/cruds/get_msme_candidate_details",
            type:"POST",
            headers: {
                            "X-CSRF-TOKEN":
                                    $('meta[name="csrf-token"]').attr("content") ||
                                    $('[name="_token"]').val(),
                        },
                        data: requestData,
                        dataType: "json",
                        success: (response) => {
                            if (response && !response.error) {
                                let candidateDetails = response;
                                msmeUtils.formEl.loadDataFromJSON(candidateDetails);
                                if(candidateDetails.email){
                                    msmeUtils.formEl.find('#email').attr('readonly',true);
                                }
                                if(candidateDetails.mobile_no){
                                    msmeUtils.formEl.find('#mobile_no').attr('readonly',true);
                                }                               
                               msmeUtils.formEl.find('.preview_image').attr('src',candidateDetails.photo);
                            } else {
                                if (response && response.error) {
                                    Impiger.showError(response.message);
                                    return false;
                                }
                            }
                        },
                        error: (data) => {
                            msmeUtils.formEl.trigger("reset");
                            msmeUtils.formEl.attr("action", msmeUtils.formAction);
                            
                        },
        })
    },
    disabledKnowledgePartner: function (disabled = true) {
        $(document).find(".ivp_knowledge_partner .form-group").each(function () {
            let labelEl = $(this).find('label:first');
            if (labelEl.length) {
                let parentEl = labelEl.parent();
                let inputEl = labelEl.next();
                if (disabled) {
                    if (labelEl.attr('for') == 'ivp_knowledge_partners[attachment]') {
                        labelEl.removeClass('required');
                    }
                    inputEl.attr('disabled', true);
                } else {
                    if (labelEl.attr('for') == 'ivp_knowledge_partners[attachment]') {
                        labelEl.addClass('required');
                    }
                    inputEl.attr('disabled', false);
                }
            }
        })
    },
    uploadDocumentsView() {
        let data = [];
        if (typeof (formData) != "undefined" && !$.isEmptyObject(formData)) {
            data = formData;
            setTimeout(function () {
                let inputEl = $(document).find('[name="attachments"]');
                let fileContainer = inputEl.next('.list-photos-gallery');
                inputEl.val(data.attachments);
                let files = JSON.parse(data.attachments);
                $.each(files, function (index, file) {
                    let fileTemplate = CustomScript.getFileTemplate(file, true);
                    fileContainer.find('.row').append('<div class="col-sm-4 photo-gallery-item mb-2" data-id="' + (index) + '" data-file="' + file.file + '">' + fileTemplate + '</div>');
                });
            }, 500);
            if(msmeUtils.formEl.hasClass('viewForm')){
                $(document).find('#main').prepend("<a class='btn btn-info' id='printPreview'><i class='fa fa-print'></i>  Export PDF </button>");
                if(data.identified_knowledge_partner && data.identified_knowledge_partner == 1){
                   $(document).find(".ivp_knowledge_partner").show();
                }
            }
            
        }
    },
    printPreview: function () {
        $(document).on('click', '#printPreview', function () {
            var styles = $('head').html();
            var printContent = document.getElementById('printPreviewForm').innerHTML;
            var win = window.open('', 'PrintWindow');
            win.document.write("<html><head>" + styles + "</head>");
            win.document.write("<div class='viewForm'>" + printContent + "</div></html>");
            setTimeout(function () {
                win.document.close();
                win.focus();
                win.print();
                win.close();
            }, 700);
        });
    }
};

$(document).ready(function () {
    msmeUtils.init();
});






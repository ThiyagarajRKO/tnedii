"use strict";

let ivpUtils = {
    formEl: $("#impiger-innovation-voucher-program-forms-innovation-voucher-program-form"),
    init: function () {
        try{
        //$(document).find(".ivp_knowledge_partner").hide();
        //this.disabledKnowledgePartner();
        this.bindEvent();
        this.uploadDocumentsView();
        this.printPreview();
    } catch (err) {
        console.log('ERR',err);
    }
    },
    bindEvent: function () {
        // $(document).on('change', 'input[name="identified_knowledge_partner"]', function () {
        //     let knowledgePartner = $(this).val();
        //     if (knowledgePartner == 1) {
        //         $(document).find(".ivp_knowledge_partner").show();
        //         ivpUtils.disabledKnowledgePartner(false);
        //     } else {
        //         $(document).find(".ivp_knowledge_partner").hide();
        //         ivpUtils.disabledKnowledgePartner();
        //     }
        // });
        $(document).on('change', '#voucher_type', function () {
            if ($(this).val()) {
                let selectedText = $('#voucher_type option:selected').text();
                let applicationNumber = $("#application_number").val();
                applicationNumber = applicationNumber.split("-");
                selectedText = selectedText.split(' ').map(word => word[0]).join('');
                $("#application_number").val(selectedText + '-' + applicationNumber[1]);
            }
        });
        if (ivpUtils.formEl.length) {
            $(document)
                    .find(".attachment-details a")
                    .each(function () {
                        $(this).attr(
                                "href",
                                "/storage/" + $(this).attr("href")
                                );
                    });
        }
    },
    documentValidation:function(){
        $(document).on("click", 'button[name="submit"]', function (event) {
            ivpUtils.formEl.valid();
            let that = $(this);
            if (ivpUtils.formEl.find(".attachment-details").length) {
                ivpUtils.formEl.find(".attachment-details").each(function () {
                    var nameAttr = $(this).prev().attr("name");
                    var isRequired = $(this)
                        .parent()
                        .prev()
                        .hasClass("required");                        
                    
                    if ($(this).val()) {
                        var filePath = $(this).val();
                        var nameAttr = $(this).attr("name");
                        var allowedExtensions = /(\.pdf|\.jpeg|\.png|\.gif|\.doc|\.xls|\.csv)$/i;
                        if(nameAttr == 'estimated_cost'){
                            allowedExtensions = /(\.pdf)$/i;
                        }else if(nameAttr == 'presentation'){
                            allowedExtensions = /(\.ppt)$/i;
                        }
                        let fileType = {"estimated_cost":"PDF","presentation":"PPT"};
                        if (!allowedExtensions.exec(filePath)) {
                            Impiger.showError(
                                "Please upload " +
                                    nameAttr +
                                    " having extensions "+fileType[nameAttr]+" only."
                            );
                            CustomScript.enableButton(that);
                            event.preventDefault();
                            return false;
                        }
                    }
                });
            }
        });
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
            if(ivpUtils.formEl.hasClass('viewForm')){
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
            printContent = printContent.replace(/singleCol col-md-4/g, "col-md-12"); 
            var win = window.open('', 'PrintWindow');
            var applicationNumber = $('#application_number').text();
            var projectTitle = $('#project_title').text();
            var title='IVP_'+applicationNumber+'_'+projectTitle;
            win.document.write("<html><head>" + styles + "</head>");
            win.document.title = title;
            win.document.write("<div class='viewForm'>" + printContent + "</div></html>");
            setTimeout(function () {
                win.document.close();
                win.focus();
                win.print();
                win.close();
            }, 1500);
        });
    }
};

$(document).ready(function () {
    ivpUtils.init();
});



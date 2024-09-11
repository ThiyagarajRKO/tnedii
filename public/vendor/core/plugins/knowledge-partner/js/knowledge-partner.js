"use strict";

let kpUtils = {
    formEl: $("#impiger-knowledge-partner-forms-knowledge-partner-form"),
    init: function () {
        try{
            this.bindEvent();
            this.uploadDocumentsView();
            this.printPreview();
        } catch (err) {
            console.log('ERR',err);
        }
    },
    bindEvent: function () {
        
    },
    documentValidation:function(){
        
    },
    uploadDocumentsView() {
        let data = [];
        if (typeof (formData) != "undefined" && !$.isEmptyObject(formData)) {
            data = formData;
            setTimeout(function () {
                let multipleInputEl = $(document).find('.multi-file-upload-container input[type="hidden"]');
        		$(multipleInputEl).each(function() {
        			let inputEl = $(this);
        			let fileContainer = inputEl.next('.list-photos-gallery');
        			let field_value = inputEl.val();
        			if(field_value != "")
        			{
            			//console.log(JSON.parse(field_value));
            			let files = JSON.parse(field_value);
            			$.each(files, function (index, file) {
            			    let fileTemplate = CustomScript.getFileTemplate(file, true);
            			    fileContainer.find('.row').append('<div class="col-sm-6 photo-gallery-item mb-2" data-id="' + (index) + '" data-file="' + file.file + '">' + fileTemplate + '</div>');
            			});
        		    }
        		});
            }, 500);
            if(kpUtils.formEl.hasClass('viewForm')){
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
    kpUtils.init();
});



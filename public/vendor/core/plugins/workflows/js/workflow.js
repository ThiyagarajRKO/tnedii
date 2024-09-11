

(function ($) {
    $.utils = {
        // http://stackoverflow.com/a/8809472
        createUUID: function () {
            var d = new Date().getTime();
            if (window.performance && typeof window.performance.now === "function") {
                d += performance.now(); //use high-precision timer if available
            }
            var uuid = 'dynamicModal'.replace(/[xy]/g, function (c) {
                var r = (d + Math.random() * 16) % 16 | 0;
                d = Math.floor(d / 16);
                return (c == 'x' ? r : (r & 0x3 | 0x8)).toString(16);
            });
            return uuid;
        }
    }

    $.fn.dialogue = function (options) {
        var defaults = {
            title: "", content: $("<p />"),
            closeIcon: false, id: $.utils.createUUID(), open: function () { }, buttons: []
        };
        var settings = $.extend(true, {}, defaults, options);
        var modalTitle = '<i class="til_img"></i><strong>' + settings.title + '</strong>';
        // create the DOM structure
        var $modal = $("<div />").attr("id", settings.id).attr("role", "dialog").addClass("modal fade dynamicModal")
                .append($("<div />").addClass("modal-dialog")
                        .append($("<div />").addClass("modal-content")
                                .append($("<div />").addClass("modal-header bg-info")
                                        .append($("<h4 />").addClass("modal-title").html(modalTitle)))
                                .append($("<div />").addClass("modal-body")
                                        .append(settings.content))
                                .append($("<div />").addClass("modal-footer")
                                        )
                                )
                        );
        $modal.shown = false;
        $modal.dismiss = function () {
            // loop until its shown
            // this is only because you can do $.fn.alert("utils.js makes this so easy!").dismiss(); in which case it will try to remove it before its finished rendering
            if (!$modal.shown) {
                window.setTimeout(function () {
                    $modal.dismiss();
                }, 50);
                return;
            }

            // hide the dialogue
            $modal.modal("hide");
            // remove the blanking
            $modal.prev().remove();
            // remove the dialogue
            $modal.empty().remove();

            $("body").removeClass("modal-open");
        }

        if (settings.closeIcon)
            $modal.find(".modal-header").append($("<button />").attr("type", "button").addClass("close").html("&times;").click(function () {
                $modal.dismiss()
            }));

        // add the buttons
        var $footer = $modal.find(".modal-footer");
        for (var i = 0; i < settings.buttons.length; i++) {
            (function (btn) {
                let btnClass = (btn.class) ? btn.class : "btn btn-default";
                $footer.prepend($("<button />").addClass(btnClass)
                        .attr("id", btn.id)
                        .attr("type", "button")
                        .text(btn.text)
                        .click(function () {
                            btn.click($modal)
                        }))
            })(settings.buttons[i]);
        }

        settings.open($modal);

        $modal.on('shown.bs.modal', function (e) {
            $modal.shown = true;
        });
        // show the dialogue
        $modal.modal("show");
        window.setTimeout(function () {
            Impiger.initDatePicker($(document).find('.datepicker'));
        }, 1000)
        return $modal;
    };
})(jQuery);


class Workflow {
    selectEl = [];
    constructor() {
        this.$body = $('body');
    }

    init() {
        this.changeFilter();

        $(document).on('click', 'a[href="#mail"]', function () {
            if (typeof CodeMirror !== "undefined" && !$('.CodeMirror').length) {
                Impiger.initCodeEditor("mail-template-editor");
            }
        });
        $(document).on('click', '.nav-link', function () {
            if ($(document).find('#mail').hasClass('active')) {
                $(document).find('.searchState').parent().hide();
            } else {
                $(document).find('.searchState').parent().show();
            }
        });
        this.attachmentField();
    }

    changeFilter() {
        let _self = this;
        _self.$body.on('click', '.apply-workflow-process', event => {
            event.preventDefault();
            let $current = $(event.currentTarget);
            let $parent = $current.closest('ul');
            let data = $current.data();
            _self.doModal(data);
            setTimeout(function(){
                CustomScript.initCustomSelect2();
            if(_self.selectEl){
                $.each(_self.selectEl,function(index,value){
                    let condition=(value.condn)?value.condn:'';
                    Workflow.getOptions(value.table,value.elm,condition);
                })
            }
            },500)
            
        });
    }

    doModal(data) {
        let _self = this;
        let title = 'Approval Process - ' + data.from + ' to ' + data.to;
        var input = [];
        if (data.custom_input) {
            $.each(data.custom_input, function (index, value) {
                let inputType = value.type;
                let inputName = value.field;
                let storeTable = value.store_table;
                let labelName = value.title;
                var inputEl = "";
                if (inputType == 'text') {
                    inputEl += `<div class='form-group'>
                        <label class="control-label required">` + labelName + `</label>
                        <input type ='text' class='form-control' name='` + inputName + `' data-store='` + storeTable + `'
                        placeHolder="Enter ` + labelName + `.."/>
                </div>`;
                }
                if (inputType == 'date') {
                    inputEl += `<div class='form-group'>
                        <div class="input-group">
        <input class="form-control required datepicker" data-date-start-date='0d' data-date-format="yyyy-mm-dd" required="required" name="` + inputName + `" type="text" value="" autocomplete="off" aria-required="true"
        data-store='` + storeTable + `' placeHolder="Enter ` + inputName.replace('_', " ") + `..">
        <span class="input-group-prepend">
            <button class="btn default" type="button">
                <i class="fa fa-calendar"></i>
            </button>
        </span>
    </div>    </div>`;
                }
                if(inputType == 'select'){
                    inputEl += `<div class='form-group'>
                        <label class="control-label required">` + labelName + `</label>
                        <div class="ui-select-wrapper form-group">
                        <select class='form-control select-full ui-select' name='` + inputName + `' id='` + inputName + `' data-store='` + storeTable + `'
                        >`+
                        `</select>
                        <svg class="svg-next-icon svg-next-icon-size-16">
                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#select-chevron"></use>
                </svg>
                    </div>
                </div>`;
                }
                input.push(inputEl)
                let condition=(value.condn)?value.condn:'';
                _self.selectEl.push({'table':value.choices,elm:inputName,condn:condition});            });
        }
        input.push($("<label/>", {
            class: "control-label required",
            text: 'Comments'
        }));
        input.push($("<textarea/>", {
            class: "form-control",
            name: "approver_comments",
            placeholder: "Enter Comments Here.."
        }));
//            CustomScript.initCustomSelect2();
        return $.fn.dialogue({
            title: title,
            content: input,
            closeIcon: true,
            buttons: [

                {
                    text: "Submit", class: "float right btn btn-info workflowSubmit", id: $.utils.createUUID(), click: function ($modal) {
                            _self.approveWorkflow(data, $modal);

                    }
                },
                {
                    text: "Close", class: "float left btn btn-warning", id: $.utils.createUUID(), click: function ($modal) {
                        $modal.dismiss();
                    },
                },
            ]
        });


    }

    approveWorkflow(data, $modal) {
        let _self = this;
        let tableElm = $('.dataTable[role="grid"]');
        data.approver_comments = $modal.find('[name="approver_comments"]').val();
        $.each($modal.find('.form-group input,select,textarea'), function (i, v) {
            if(!$(v).val() && $(v).is(":visible")){
                let label=$(v).prev('label').text();
                label=(label)?label:$(v).parent().prev().text();
                CustomScript.showValidationError($modal.find(v),label+' field is required.');
                _self.removeClass("button-loading");
                return false;
            }
            data[$(v).attr('name')] = $(v).val();
            
        });

        if (!_self.isOnAjaxLoading()) {
            $.ajax({
                url: '/workflows/apply_workflow',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: data,
                dataType: 'json',
                beforeSend: () => {
                    $modal.find('.workflowSubmit').addClass("button-loading");
                    _self.showAjaxLoading();
                },
                success: res => {
                    if (res.error) {
                        $modal.find('.workflowSubmit').removeClass("button-loading");
                        Impiger.showError(res.message)
                    }
                    $modal.dismiss();
                    let tableElm = $('.dataTable[role="grid"]');
                    if (tableElm.length == 0) {
                        window.location.reload();
                    }
                    tableElm.DataTable().ajax.reload(null, false);
                    Impiger.showSuccess(res.message);
                },
                complete: () => {
                    _self.hideAjaxLoading();
//                     $modal.dismiss();
                    tableElm.DataTable().ajax.reload(null, false);
                },
                error: data => {
                    var error = eval("(" + data.responseText + ")");
                    $modal.find('.workflowSubmit').removeClass("button-loading");
                    Impiger.showError(error.message)
                }
            });
        }

    }

    static setupSecurity() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    }

    showAjaxLoading($element = $('.dataTable[role="grid"]')) {
        $element
                .addClass('on-loading')
                .append($('#rv_media_loading').html());
    }

    hideAjaxLoading($element = $('.dataTable[role="grid"]')) {
        $element
                .removeClass('on-loading')
                .find('.loading-wrapper').remove();
    }

    isOnAjaxLoading($element = $('.dataTable[role="grid"]')) {
        return $element.hasClass('on-loading');
    }

    attachmentField() {
        $('.listBoxContainer').each(function (i) {
            $('#repeater_' + i).find('> .form-group').each(function (index) {
                let transKey = $('#repeater_' + i).data('key');
                $('#repeater_' + i).on('change', 'select[name="attachment_field[' + index + ']"]', function () {
                    let key = $(this).val();
                    let contentEl = $('#repeater_' + i).find('textarea[name="configs[' + index + '][' + transKey + '][attachment_content]"]');
                    let previousContent = contentEl.val();
                    let currentContent = previousContent + '{' + key + '}'
                    contentEl.val(currentContent);
                })
            });
        })
    }
    static initDatePicker(element) {
        if (jQuery().bootstrapDP) {
            let format = $(document).find(element).data("date-format");
            if (!format) {
                format = "yyyy-mm-dd";
            }
            $(document).find(element).bootstrapDP({
                maxDate: 0,
                changeMonth: true,
                changeYear: true,
                autoclose: true,
                dateFormat: format,
            });
        }
    }
    static getOptions(table,elm,condn=''){
        if(!table){
            return [];
        }
        let options='';
        let data={'table':table};
        if(condn)
        {
            data.condn=condn;
        }
       
         $.ajax({
             'url':'/workflows/getOptions',
            'type':'POST',
            'data':data,
            headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },                
            dataType: 'json',
            success:function(res){
                if (res.error) {
                        Impiger.showError(res.message)
                    }
                    CustomScript.initCustomSelect2($('#'+elm).select2('destroy').empty().prepend('<option selected=""></option>'), { data: res });
            }
            
         });
     
    }
}

$(document).ready(() => {
    Workflow.setupSecurity();
    new Workflow().init();
});

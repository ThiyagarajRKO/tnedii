var defaultFontSize = curSize = 14,currentFontSize = 14;
class CustomScript {    
    init() {
        $(document).on("change", ".filter-column-value", function () {
            let parentEl = $(this).parents(".form-filter:first");
            let key = parentEl.find(".filter-column-key").val() || "";
            if (key.includes("entity_type")) {
                $(".filter-column-key").each(function () {
                    if ($(this).val().includes("entity_id")) {
                        $(this).trigger("change");
                    }
                    let keyEl = $(".filter-column-key").val();
                });
            }
        });

          CustomScript.menuToggler();  
          
        $(document).on("change", '[type="number"]', function (e) {
            let maxValue = parseFloat($(e.target).attr("max")) || 0;

            if (maxValue) {
                if (parseFloat($(e.target).val()) < 0) $(e.target).val(0);
                if (parseFloat($(e.target).val()) > maxValue)
                    $(e.target).val(maxValue);
            }
        });

        let inputPatternSettings = {
            allowedInputPatterns: [
                "1-9",
                "0-9",
                "0-9",
                "0-9",
                "-",
                "0-3",
                "0-9",
                "-",
                "0-1",
                "0-9",
            ],
            autoTypeCharacters: [" ", "-", ":"],
            ignoreKeyCodes: [13],
            defaultTypingCharacter: "0",
            placeHolder: "0000-00-00",
        };
        $("input[type='text'].datepicker").inputPattern(inputPatternSettings);
        if ($(".dataTable").length > 0) {
            setTimeout(function () {
                $(".table-actions a").tooltip();
            }, 1000);
        }
        $(document).on("click", ".dataTable tbody tr td", function (event) {
            $(".table-actions a").tooltip();
        });
        $(document).on('click', 'button[name="submit"]', function (event) {
            if($('form[action$="postdata"]').length){
                $('form[action$="postdata"]').submit();
            }
            $('.text-danger:not(.btn_remove_attachment)').hide();
            let formEl = $(this).parents("form");
            let btnEl = $(this);
                if (formEl.length && formEl.valid()) {
                    if($('.invalid-repeater-feedback:visible').length) {
                        event.preventDefault();
                        return false;
                    }
                   if (formEl.hasClass('submitted')) {
                        event.preventDefault();
                    } else {
                        $(this).find('i').addClass("fa fa-spinner fa-spin");
                        formEl.find(':submit').addClass('disabled');
                        $(this).addClass('submitted-inprogress');
                        formEl.addClass('submitted');
                        formEl.find('[type="reset"]').addClass('disabled');
                        formEl.find('.cancelBtn').addClass('disabled');
                        formEl.find('#previousBtn').addClass('disabled');
                        setTimeout(function(){
                            if(formEl.find('.error_border:visible').length > 0) {
                                CustomScript.resetFormLoading(formEl, btnEl);
                            }
                        },500);
                    }

                    if(formEl.find('.is-invalid:visible').length > 0) {
                        CustomScript.resetFormLoading(formEl, btnEl);
                    }

                    if (formEl.length) {
                        $(document).ajaxSend(function(){
                            $('#custom-ajax-loader').show();
                        });
                        $(document).ajaxComplete(function(){
                            $('#custom-ajax-loader').hide();                            

                            if (formEl.hasClass('submitted')) {
                                formEl.removeClass('submitted');
                                $('.btn-set [name="submit"].submitted-inprogress:first').trigger('click');
                            }

                            if(formEl.find('.is-invalid:visible').length > 0) {
                                formEl.addClass('submitted');
                                CustomScript.resetFormLoading(formEl, btnEl);
                                btnEl.trigger('click');
                            }
                        });
                    }
                }
            if(typeof(crudUtils) != "undefined"){
                crudUtils.formEl.find('.attachment-details').each(function () {
                    let parentEl = $(this).parents('.form-group:first');
                    if(parentEl.find('label').hasClass('required')) {
                        let targetElm = $(parentEl).find('.attachment-details');
                        let nameAttr = $(targetElm).prev().attr("name");
                        let elm = $(targetElm).find('a');

                        if(!elm.length) {
                            $(targetElm).append('<a></a>');
                            elm = $(targetElm).find('a');
                        }
//                        if(nameAttr.indexOf('specimen_signature')==-1 && !elm.text()) {
//                            CustomScript.resetFormLoading(formEl, btnEl);
//                            $('<span class="invalid-feedback customError" style="display: inline;">The attachment field is required.</span>').appendTo($(parentEl).find('.attachment-wrapper')); 
//                            event.preventDefault();
//                            return false;
//                        }
                        $(parentEl).find('.customError').remove();
                    }
                });
        }
            CustomScript.removeIsValidClassInEmptyElement();
        });
        
        $(".input-group-prepend").click(function () {
            let inputEl = $(this).prev();
            if (inputEl.hasClass("datepicker")) {
                inputEl.bootstrapDP("show");
            }
            if (inputEl.hasClass("time-picker")) {
                inputEl.timepicker();
            }
        });
        $(document).find(".datepicker").attr("autocomplete", "off");
        CustomScript.removeIsValidClassInEmptyElement();
        CustomScript.initCustomSelect2();
        //Bootstrap modal – disable closing with ESC key or mouse
        $(document)
            .find(".modal")
            .attr({ backdrop: "static", keyboard: false });
        // Select option values based on the data
        let entityEl = $(document).find(
            '#institute_id option:not([value=""]), #entity_id  option:not([value=""]),#organization_id option:not([value=""])'
        );
        if (entityEl.length == 1) {
            entityEl.prop("selected", true);
            entityEl.trigger("change");
        }

        $(document)
            .find("#refresh-captcha")
            .click(function () {
                $.ajax({
                    type: "GET",
                    url: "/refresh-captcha",
                    success: function (data) {
                        $(".customcaptcha span").html(data.customcaptcha);
                    },
                });
            });
        $(document).on("click", '[type="reset"]', function (e) {
            let formEl = $(e.target).closest("form");
            if(!formEl.find("[name='id']").val()){
                if (formEl.find(".draggable-right").length) {
                    formEl.find(".transferAllFrom").trigger("click");
                }
                if (formEl.find("select").length) {
                    formEl.find("select").val("").trigger("change");
                }
                if (formEl.find(".preview-image-wrapper > img").length) {
                    let defaultImg = baseUrl + '/vendor/core/core/base/images/placeholder.png'
                    formEl.find(".preview-image-wrapper > img").attr("src", defaultImg);
                }
                var validator = formEl.validate();
                validator.resetForm();
            }            
        });
        $(document).on("hidden.bs.modal", ".modal", function (e) {
            $("body").removeClass("show-admin-bar");
        });
        if ($('#userPermissionMapping').length && localStorage.getItem("CURRENT_TAB") == "#tab_1_5") {
            $(document).find(localStorage.getItem("CURRENT_TAB")).prev('.tab-pane').removeClass('active')
            $(document).find('a[data-toggle="tab"]').removeClass('active');
            $(document).find('a[href="'+localStorage.getItem("CURRENT_TAB")+'"]').addClass('active')
            $(document).find(localStorage.getItem("CURRENT_TAB")).addClass('active');
        }else{
           localStorage.removeItem("CURRENT_TAB");
        }
        $("li a[data-toggle='tab']").click(function () {
                localStorage.setItem("CURRENT_TAB", $(this).attr('href'));
        });
        
//        $(document).on('click','#decfont',function(){
//            let decFontSize = parseInt(currentFontSize) - 1;
//            currentFontSize = decFontSize;alert(currentFontSize);
//            $('body').css('font-size',currentFontSize+'px');
//        });
//        $(document).on('click','#normfont',function(){
//            currentFontSize = defaultFontSize;alert(currentFontSize);
//            $('body').css('font-size',currentFontSize+'px');
//        });
//        $(document).on('click','#incfont',function(){
//            let incFontSize = parseInt(currentFontSize) + 1;
//            currentFontSize = incFontSize;   alert(currentFontSize);   
//            $('body').css('font-size',currentFontSize+'px');
//        });
        $('#incfont').click(function () {
            curSize = parseInt($('html').css('font-size')) + 2;
            if (curSize <= 20)
                $('html').css('font-size', curSize);
            $('li > a').css('font-size', curSize);
            $('h3 > a').css('font-size', curSize);
            $('.btn-sm').css('font-size', curSize);
        });
        $('#decfont').click(function () {
            curSize = parseInt($('html').css('font-size')) - 2;
            if (curSize >= 12)
                $('html').css('font-size', curSize);
            $('li > a').css('font-size', curSize);
            $('h3 > a').css('font-size', curSize);
            $('.btn').css('font-size', curSize);
        });
        $('#normfont').click(function () {
            curSize = defaultFontSize;
            $('html').css('font-size', curSize);
            $('li > a').css('font-size', curSize);
            $('h3 > a').css('font-size', curSize);
            $('.btn-sm').css('font-size', curSize);
        })
    }
    static resetFormLoading(formEl, btnEl) {
        if (formEl.hasClass('submitted')) {
            formEl.removeClass('submitted');
            btnEl.find('i').removeClass("fa-spinner fa-spin");
            if(btnEl.val() == 'save') {
                btnEl.find('i').addClass("fa-check-circle");
            } else {
                btnEl.find('i').addClass("fa-check-circle");
            }
            formEl.find(':submit').removeClass('disabled');
            btnEl.removeClass('submitted-inprogress');
            formEl.find('[type="reset"]').removeClass('disabled');
            formEl.find('.cancelBtn').removeClass('disabled');
            formEl.find('#previousBtn').removeClass('disabled');
        }
    }
    static menuToggler() {
        if ($('body').hasClass('page-sidebar-closed')) {
            $('.page-logo > a > img').show();
            $('.page-logo > a > img').attr('src', '/storage/mask-group-1.png');
            $('.page-logo > a > img').css('min-width', '25px');
            $('.page-footer').css('margin-left', '45px');
            if ($('body').hasClass('page-sidebar-fixed')) {
                $('.page-sidebar-menu').trigger('mouseleave');
            }
        }else{
            $('.form-actions.form-actions-fixed-top').css('left','235px!important');
        }

        $(window).trigger('resize');
    }
    static initYearDatePicker() {
        let element = $(document).find(".yearpicker");
        let currentDate = new Date();
        if (jQuery().bootstrapDP) {
            let format = element.data("date-format");
            if (!format) {
                format = "yyyy";
            }
            $(element).bootstrapDP("destroy");
            $(element).bootstrapDP({
                maxDate: 0,
                changeYear: true,
                autoclose: true,
                dateFormat: format,
                startView: 2,
                endDate: currentDate,
            });
        }
    }

    static removeIsValidClassInEmptyElement() {
        setTimeout(function () {
            $(".main-form")
                .parents("form:first")
                .find(".is-valid")
                .each(function (index, element) {
                    if (!$(element).val()) {
                        $(element).removeClass("is-valid");
                    }
                });
        }, 1000);
    }

    static doModal(config) {
        let bodyContent = "";
        let messageClasses = {
            error: "alert-danger",
            success: "alert-success",
            info: "alert-info",
        };

        if (messageClasses[config.messageType]) {
            bodyContent += '<div class="modal-alert-msg">';
            bodyContent += config.content;
            bodyContent += "</div>";
        }

        let footerBtn =
            '<button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>';

        if (config.confirm) {
            let confirmButton = config.confirm_button
                ? config.confirm_button
                : "Okay";
            let cancelButton = config.cancel_button
                ? config.cancel_button
                : "Cancel";
            let btnClass = config.bulk
                ? "custom-crud-many-entry"
                : "custom-crud-entry";
            let url = config.url || "";
            footerBtn =
                `<button class="float-left btn btn-warning" data-dismiss="modal">` +
                cancelButton +
                `</button>
                <button data-url="` +
                url +
                `" class="float-right btn btn-danger ` +
                btnClass +
                ` ">` +
                confirmButton +
                `</button>`;
        }

        let html =
            `<div class="modal fade" data-backdrop="static" data-keyboard="false" id="modalWindow" tabindex="-1" role="dialog" aria-labelledby="messageModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modal-title">` +
            config.title +
            `</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    ` +
            bodyContent +
            `
                                </div>
                                <div class="modal-footer">
                                    ` +
            footerBtn +
            `
                                </div>
                        </div>
                    </div>`;
        $("#messageModal").html(html);
        $("#modalWindow").modal();
    }

    static hideAjaxLoading() {
        $("#ajaxLoader").css("display", "none");
    }

    static showInfoMessage(config) {
        let title = config.title ? config.title : "Info";
        config = $.extend(config, {
            messageType: "info",
            title: title,
            width: "400px",
        });
        CustomScript.doModal(config);
    }

    static initCustomSelect2(el, config = {}) {
        el = el ? el : $(".select-full,.select-search-full");
        config = $.extend(config, {
            width: "100%",
            placeholder: "Select",
            allowClear: true,
        });
        if ($(document).find(el).length) {
            $(el).select2(config);
        }
    }

    static initCustomSelect2WithTags(el, config = {}) {
        el = el ? el : $(".ui-select-tags");
        config = $.extend(config, {
            width: "100%",
            placeholder: "Select",
            tags: true,
        });
        if ($(document).find(el).length) {
            $(el).select2(config);
        }
    }

    static enableButton(el) {
        setTimeout(function () {
            $("form").find('button[name="submit"]').removeClass("disabled");
            el.find("i").removeClass("fa-spinner fa-spin");
            $("form").removeClass("submitted");
        }, 1000);
    }
   
    static showValidationError(el, error) {
        if (!el) {
            return false;
        }
        let nameAttr = $(el).attr("name");
        let errorSpan =
            "<span id='" +
            nameAttr +
            "-error' class='invalid-feedback' style='display: inline;'>" +
            error +
            "</span>";
        if (!el.hasClass("is-invalid") && !$(el).val()) {
            $(el)
                .closest(".form-control")
                .removeClass("is-valid")
                .addClass("is-invalid");
            if (!$("#" + nameAttr + "-error").length) {
                $(el).parent().append(errorSpan);
            }
        }
    }

    static createHashMap(input, hashKey) {
        if ((typeof input != "undefined", Array.isArray(input))) {
            let result = input.reduce(function (map, obj) {
                map[obj[hashKey]] = obj;
                return map;
            }, {});
            return result;
        }
        return [];
    }

    static inputRestriction() {
        $(document).on("keypress", ".allowNumberOnly", function (event) {
            let val = event.which;
            if (val != 8 && val != 0 && (val < 48 || val > 57)) return false;
        });

        $(document).on("keydown", ".allowNumberOnly", function (event) {
            if (event.shiftKey == true) {
                event.preventDefault();
            }

            if (
                (event.keyCode >= 48 && event.keyCode <= 57) ||
                (event.keyCode >= 96 && event.keyCode <= 105) ||
                event.keyCode == 8 ||
                event.keyCode == 9 ||
                event.keyCode == 37 ||
                event.keyCode == 39 ||
                event.keyCode == 46 ||
                event.keyCode == 190
            ) {
            } else {
                event.preventDefault();
            }
        });
    }

    static addDatePickerOptions(el, options) {
        if (!el && !options) {
            return false;
        }
        if (typeof options == "object") {
            if (jQuery().bootstrapDP) {
                $.each(options, function (key, value) {
                    let option =
                        "set" + key.charAt(0).toUpperCase() + key.slice(1);
                    $(el).bootstrapDP(option, value);
                });
            }
        }
    }

    static getOptions(table, elm) {
        if (!table) {
            return [];
        }
        let options = "";
        let data = { table: table };

        $.ajax({
            url: "/cruds/getOptions",
            type: "POST",
            data: data,
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            dataType: "json",
            success: function (res) {
                if (res.error) {
                    Impiger.showError(res.message);
                }
                CustomScript.initCustomSelect2(
                    elm
                        .select2("destroy")
                        .empty()
                        .prepend('<option selected=""></option>'),
                    { data: res }
                );
            },
        });
    }
    static isValidArray(inputArray) {
        if (inputArray && $.isArray(inputArray) && inputArray.length > 0) {
            return true;
        }

        return false;
    }
    static  bytesToSize(bytes) {
        var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
        if (bytes == 0)
            return '0 Byte';
        var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
        return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
    }
    static getFileTemplate(file,isView = false){
        if(!file){
            return false;
        }
        let fileExtensions = 'pdf';
        let filePath = file.file;
        let fileSize = CustomScript.bytesToSize(file.size);
        let imgExtensions =  /(\.jpg|\.jpeg|\.png|\.gif)$/i;
        let sheetExtensions =  /(\.xls|\.csv)$/i;
        let docExtensions =  /(\.doc|\.docx|\.txt)$/i;
        if(imgExtensions.exec(filePath)){
            fileExtensions='img';
        }else if(sheetExtensions.exec(filePath)){
            fileExtensions='xls';
        }else if(docExtensions.exec(filePath)){
            fileExtensions='doc';
        }
        let closeIcon = (isView) ? "" :  `<div class="close-icon"></div>`;
        let fileTemplate =`<a href="${baseUrl}/storage/${file.file}" target="_blank"><div class="attachement-file_v">
                                <div class="file-left">
                                    <div class="attachment-icon">
                                        <img src="${baseUrl}/vendor/core/core/base/images/files/${fileExtensions}.png" draggable="false">
                                    </div>
                                    <div class="file-area">
                                        <div class="file-name">${filePath}</div>                                              
                                    </div>
                                    ${closeIcon}
                                </div>
                            </div></a>`;
        
        return fileTemplate;
    }
}
$(document).ready(function () {
    if ($('form[action$="postdata"]').length == 0 && window.BDashboard) {
        window.BDashboard.initSortable();
    }
    new CustomScript().init();
    bsCustomFileInput.init();
    if (window.location.pathname == "/form-response") {
        $(document).on("click", "#backBtn", function () {
            window.location.href = document.referrer;
        });
        //            setTimeout(function(){
        //                 window.location.href = document.referrer;
        //            },5000);
    }
    if ($('[data-counter="counterup"]').length > 0) {
        $('[data-counter="counterup"]').counterUp({
            delay: 10,
            time: 1000,
        });
    }

    if (window.location.pathname == "/admin/theme/options") {
        CustomScript.inputRestriction();
    }

    $("th.text-left.no-sort.sorting_disabled").attr("title", "check all");

    $("th.text-left.no-sort.sorting_disabled").on("click", function () {
        $(this).attr("title", function (e, title) {
            return title === "check all" ? "uncheck all" : "check all";
        });
    });

    var $div = $("<div />").appendTo("body");
    $div.attr("id", "custom-ajax-loader");
    $("#custom-ajax-loader").hide();
    $("#custom-ajax-loader").prepend(
        '<img id="ajax-loader-gif" src="/vendor/core/core/base/images/table-loading.gif" />'
    );
});

(function (g) {
    "function" === typeof define && define.amd
        ? define(
              ["jquery", "datatables.net", "datatables.net-buttons"],
              function (d) {
                  return g(d, window, document);
              }
          )
        : "object" === typeof exports
        ? (module.exports = function (d, e) {
              d || (d = window);
              if (!e || !e.fn.dataTable) e = require("datatables.net")(d, e).$;
              e.fn.dataTable.Buttons || require("datatables.net-buttons")(d, e);
              return g(e, d, d.document);
          })
        : g(jQuery, window, document);
})(function (g, d, e, h) {
    d = g.fn.dataTable;
    if (!d) {
        return false;
    }
    var _buildUrl = function (dt, action, onlyVisibles) {
        let url = dt.ajax.url() || "";
        let params = dt.ajax.params();
        params.action = action;
        if (onlyVisibles) {
            var visibleColumns = _getVisibleColumns();
            var hiddenColumns = _getHiddenColumns();
            var columns = [];
            var decryptColumns = CustomEncryption.base64Helper().decode(
                params.columns
            );
            decryptColumns = JSON.parse(decryptColumns);
            g.each(decryptColumns, function (key, col) {
                if (g.inArray(col.name, visibleColumns) !== -1) {
                    columns.push(col);
                }
            });
            params.columns = CustomEncryption.base64Helper().encode(
                JSON.stringify(columns)
            );
            params.hidden_columns = hiddenColumns;
        } else {
            params.visible_columns = null;
        }
        if (url.indexOf("?") > -1) {
            return url + "&" + g.param(params);
        }

        return url + "?" + g.param(params);
    };
    var _getVisibleColumns = function () {
        var visibleColumns = [];
        g.each(d.settings[0].aoColumns, function (key, col) {
            if (col.bVisible) {
                visibleColumns.push(col.name);
            }
        });

        return visibleColumns;
    };
    var _getHiddenColumns = function () {
        var hiddenColumns = [];
        g.each(d.settings[0].aoColumns, function (key, col) {
            if (!col.bVisible) {
                hiddenColumns.push(col.name);
            }
        });

        return hiddenColumns;
    };
    g.extend(d.ext.buttons, {
        reload:function(a, b){
            return false;
        },
        colvis: function (a, b) {
            return {
                extend: "collection",
                text: function (a) {
                    return (
                        '<i class="fa fa-eye"></i> ' +
                        a.i18n("buttons.colvis", "Column")
                    );
                },
                className: "buttons-colvis",
                buttons: [
                    {
                        extend: "columnsToggle",
                        columns: b.columns,
                    },
                ],
            };
        },
        print: function (a, b) {
            return {
                className: "buttons-print",
                text: function (a) {
                    return (
                        '<i class="fa fa-print"></i> ' +
                        a.i18n(
                            "buttons.print",
                            ImpigerVariables.languages.tables.print
                        )
                    );
                },
                action: function (e, a) {
                    window.location = _buildUrl(a, "print", true);
                },
            };
        },
        csv: function (a, b) {
            return {
                className: "buttons-csv",
                text: function (a) {
                    return (
                        '<i class="fas fa-file-csv"></i> ' +
                        a.i18n(
                            "buttons.csv",
                            ImpigerVariables.languages.tables.csv
                        )
                    );
                },
                action: function (e, a) {
                    window.location = _buildUrl(a, "csv", true);
                },
            };
        },
        excel: function (a, b) {
            return {
                className: "buttons-excel",
                text: function (a) {
                    return (
                        '<i class="far fa-file-excel"></i> ' +
                        a.i18n(
                            "buttons.excel",
                            ImpigerVariables.languages.tables.excel
                        )
                    );
                },
                action: function (e, a) {
                    window.location = _buildUrl(a, "excel", true);
                },
            };
        },
    });
    return d.Buttons;
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
$(window).load(function() {
        
    $('.carousel--nav').owlCarousel({
      lazyLoad: true
    });
  });   

$('.carousel--nav').parent().addClass("main-carousel-parent"); 
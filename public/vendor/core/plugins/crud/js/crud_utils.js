$.fn.getType = function () {
    return this[0].tagName == "INPUT"
        ? this[0].type.toLowerCase()
        : this[0].tagName.toLowerCase();
};

let crudUtils = {
    gridEditedIds: {},
    gridRowIndex: 0,
    localDDData: {},
    selectedPresentRowCache: {},
    selectedAbsentRowCache: {},
    urlParts: $(location).attr("href").split("/"),
    lastPart: "",
    frontEndForm: $('form[action$="postdata"]').length,
    formEl: $(".main-form").length
        ? $(".main-form").parents("form:first")
        : $('form[action$="postdata"]'),
    viewFormEl : $("form.viewForm"),
    init: function () {
        this.bindEvents();
        this.dynamicValidationUI();
        this.bindSubscriptionMethod();
        if (crudUtils.frontEndForm) {
            crudUtils.formEl.find('button[name="submit"]').text("Submit");
        }
        $(document).on("afterShow.fb", function (e, instance, slide) {
            let href = $(slide.$content)
                .find(".attachment-details a")
                .attr("href");
            if (href.indexOf("storage") == -1) {
                $(slide.$content)
                    .find(".attachment-details a")
                    .attr("href", "/storage/" + href);
            }
        });
        if ($(".ui-select[multiple]").length) {
            $('.ui-select[multiple] option[value=""]').remove();
        }
        this.viewRepeaterFields();
    },

    bindEvents: function () {
        this.triggerDependentDropdown();
        this.triggerAcademicDropdown();
        this.loadRepeaterToolbarBtns();
        this.validateStaticFilterForm();
        $(document).on('click', 'form.filter-form .btn-apply', function() {
            let dataTableID = $('.filter-data-table-id').val();
            var avoidAjaxCall = ['plugins-attendance-table','plugins-view-attendance-table'];
            console.log($.inArray(dataTableID, avoidAjaxCall));
            if($.inArray(dataTableID, avoidAjaxCall) == -1) {
                if(LaravelDataTables[dataTableID]) {
                    LaravelDataTables[dataTableID].ajax.reload();
                }
            } 
            
            if($(this).prev("a.hidden").length){
                $(this).prev("a").removeClass("hidden");
            }
        });
        $(document).on("click", "[data-action='bulk-upload']", function () {
            $(document).find("#bulkUpload").modal();
        });
        $(document).on("change", "#entity_type", function () {
            crudUtils.getEntityData(this);
        });
        $(document).on('change', '.present-check-all', this.presentAllToggle);
        $(document).on('change', '.absent-check-all', this.absentAllToggle);
        $(document).on('change', '.radio-present,.radio-absent', this.presentAbsentToggle);

        $(document).on(
            "change",
            "table.dataTable input:not(.table-check-all),table.dataTable select,table.dataTable textbox",
            this.captureAllEditableRowIds
        );
        $(document).on(
            "click",
            ".dataTables_wrapper .dt-buttons button.action-item",
            this.saveInlineGridDetails
        );
        this.updateRowActivation();
        $(document).on("change", ".saveAsCopy", this.copyAddressData);
        let url =
            window.location.protocol +
            "//" +
            window.location.host +
            window.location.pathname;
        $(document).on(
            "change",
            'select[restrict_based_on="true"]',
            function () {
                let urlEncode = btoa($(this).val().join("|"));
                let formKey =
                    $(this).parents("form:first").attr("name") +
                    "_" +
                    $(this).attr("name");
                // let prevRole = localStorage.getItem(formKey);
                // localStorage.setItem(formKey, urlEncode);
                if (
                    window.location.search !=
                    "?restricted_roleid=" + urlEncode
                ) {
                    location.href = url + "?restricted_roleid=" + urlEncode;
                }
            }
        );

        if ($('select[restrict_based_on="true"]').length) {
            $(window).off("beforeunload");
        }

        crudUtils.lastPart = crudUtils.urlParts[crudUtils.urlParts.length - 1];
        let preventClearForm = [];

        if (crudUtils.lastPart.startsWith("create") || crudUtils.frontEndForm) {
            if (
                crudUtils.urlParts[crudUtils.urlParts.length - 2] !=
                "minutes-of-meetings"
            ) {
                setTimeout(function () {
                    if (
                        crudUtils.formEl.length &&
                        $.isFunction($.fn.saveStorage)
                    ) {
                        crudUtils.formEl.saveStorage({
                            exclude: ["password", "hidden"],
                            preventClear:
                                $.inArray(
                                    crudUtils.formEl.attr("id"),
                                    preventClearForm
                                ) !== -1
                                    ? true
                                    : false,
                        });
                    }
                }, 700);
            }
        } else {
            if (
                $.inArray(crudUtils.formEl.attr("id"), preventClearForm) !==
                    -1 &&
                typeof formData != "undefined" &&
                formData.id
            ) {
                var pathArr = window.location.pathname.split("/");
                pathArr.pop();
                pathArr.pop();
                pathArr.push("create");
                pathArr = pathArr.join("/");
                crudUtils.formEl.resetSaveStorage(pathArr);
            }

            if (crudUtils.formEl.length) {
                setTimeout(function () {
                    crudUtils.formEl
                        .find(".attachment-details a")
                        .each(function () {
                            $(this).attr(
                                "href",
                                "/storage/" + $(this).attr("href")
                            );
                        });
                    if (!crudUtils.frontEndForm) {
                        crudUtils.formEl.valid();
                    }
                }, 300);
            }
        }
        crudUtils.viewDetailInPopup();
        $(document).on("click", ".viewGallery #list-photo .item", function () {
            parent.$.fancybox.close();
        });
        crudUtils.customValidation();
        crudUtils.repeaterGroupValidation();

        if ($('[disabled="1"] .mt-radio-list').length) {
            $("[disabled] .mt-radio-list input").attr("disabled", "disabled");
        }

        if (crudUtils.formEl.length) {
            $(document).ajaxSend(function () {
                $("#custom-ajax-loader").show();
            });
            $(document).ajaxComplete(function () {
                $("#custom-ajax-loader").hide();
            });
        }
		setTimeout(function(){
                $('.lead-label').not(':eq(0)').text('Team Member Name');
        },500)
    },

    bindSubscriptionMethod: function () {
        if ($(".dataTable").length) {
            $(document).on("click", ".rowSubscriptionDialog", (event) => {
                $(".customError").hide();
                $("#institute_ids option").removeAttr("disabled");
                $("#institute_ids").select2().trigger("change");
                event.preventDefault();
                let _self = $(event.currentTarget);
                let data = _self.data() || {};
                let instituteIds = data.subscribed_institute_ids || "";
                instituteIds = instituteIds.toString().split(",");
                $("#institute_ids").val(instituteIds);
                if (instituteIds.length > 0) {
                    $.each(instituteIds, function (k, val) {
                        $('#institute_ids option[value="' + val + '"]').prop(
                            "disabled",
                            "disabled"
                        );
                    });
                }

                $("#institute_ids").trigger("change");
                $(".row-subscription-crud-entry")
                    .data("parent-table", _self.closest(".table").prop("id"))
                    .data("data", _self.data());
                // let modalCls =  (crudUtils.lastPart.startsWith('organizations')) ? ".modal-confirm-subscription-with-detail" : ".modal-confirm-subscription";
                $(".modal-confirm-subscription").modal("show");
            });

            $("body").on(
                "hidden.bs.modal",
                ".modal-confirm-subscription",
                function () {
                    $(this).removeData("bs.modal");
                    $("#institute_ids option").removeAttr("disabled");
                    $("#institute_ids").val([]).select2().trigger("change");
                    // location.href = location.href;
                }
            );

            $(document).on("click", ".row-subscription-crud-entry", (event) => {
                $(".customError").hide();
                event.preventDefault();
                let instituteId = $("#institute_ids").val();
                let mouAttachment = $('[name="mou_attachment"]').val();
                let areaOfPartner = $('[name="area_of_partnership"]').val();
                let _self = $(event.currentTarget);
                if (!$.isArray(instituteId) || instituteId.length <= 0) {
                    $(
                        '<span class="invalid-feedback customError" style="display: inline;">Please select atleast one institute for subscription.</span>'
                    ).insertAfter($("#institute_ids"));
                    return false;
                }
                _self.addClass("button-loading");
                let data = _self.data("data");
                let activateURL = "/admin/cruds/subscription/" + data.item_id;
                let requestData = {
                    institute_ids: instituteId,
                    model_class: data.model_class || "",
                    mou_attachment: mouAttachment,
                    area_of_partnership: areaOfPartner,
                };

                $.ajax({
                    url: baseUrl + activateURL,
                    type: "POST",
                    data: requestData,
                    dataType: "json",
                    success: (response) => {
                        if (response.error) {
                            Impiger.showError(response.message);
                        } else {
                            window.LaravelDataTables[_self.data("parent-table")]
                                .row(
                                    $(
                                        'a[data-section="' + activateURL + '"]'
                                    ).closest("tr")
                                )
                                .draw();
                            Impiger.showSuccess(response.message);
                        }

                        _self.closest(".modal").modal("hide");
                        _self.removeClass("button-loading");
                    },
                    error: (data) => {
                        Impiger.handleError(data);
                        _self.removeClass("button-loading");
                    },
                });
            });
        }
    },

    validateStaticFilterForm: function () {
        $(".filter-form .invalid-feedback").hide();
        $(".filter-form").on("submit", function (e) {
            var bSubmit = true;

            $(".filter-form label.required").each(function () {
                let parentElm = $(this).next(".ui-select-wrapper");
                if (!parentElm.find("select").val()) {
                    parentElm.find(".invalid-feedback").show();
                    if (bSubmit) {
                        bSubmit = false;
                    }
                } else {
                    parentElm.find(".invalid-feedback").hide();
                }
            });

            if (!bSubmit) {
                e.preventDefault();
                return false;
            }
        });
    },
    
    triggerAcademicDropdown: function () {
        $('.filter-form').on('change', '.hub_institution_id', function() {
            let deferred = $.Deferred();
            let academicOption = $(this).data('academic_option');
            let dependentDD = $(this).data('dependent');
            let requestData = {
                'hub_institution_id': $('.filter-form .hub_institution_id').val(),
               
                'academic_option': academicOption
            }

            $.ajax({
                url: '/cruds/get_academic_options',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || $('[name="_token"]').val()
                },
                data: requestData,
                dataType: 'json',
                beforeSend: () => {
                    // _self.showAjaxLoading();
                },
                success: response => {
                    if (response.error) {
                        // crudUtils.notifyMessageError(data.message);
                        deferred.reject();
                    }
                    let ddSelector = $('.filter-form .'+dependentDD);
                    if ($(ddSelector).data('select2')) {
                        response.unshift({'id': 0, 'text': 'Select'});
                        CustomScript.initCustomSelect2($(ddSelector).select2('destroy').empty().prepend('<option value="">select</option>'), { data: response });
                        crudUtils.setDependentSelectedValue($(ddSelector));
                    }
                    deferred.resolve([]);
                },
                complete: () => {
                    // _self.hideAjaxLoading();
                },
                error: data => {
                    deferred.reject();
                }
            });

            return deferred;
            
        })
    },
    
    triggerDependentDropdown: function () {
        $(document)
            .find("select[data-dd_parentkey]")
            .each(function () {
                let childDDName = $(this).attr("name");
                let childDDSelector = "select[name='" + childDDName + "']";
                let data = $(this).data() || {};
                let ddName = data.dd_parentkey || "";

                let relation = $(this)
                    .attr("name")
                    .replace(/\[/g, ".")
                    .replace(/\]/g, "");
                let relationWithDot = relation.match(/\./g);

                if (relationWithDot) {
                    let dotLength = relationWithDot.length;
                    let submoduleStr = relation.split(".");

                    if (dotLength == 2) {
                        ddName =
                            submoduleStr[0] +
                            "[" +
                            submoduleStr[1] +
                            "][" +
                            ddName +
                            "]";
                    } else if (dotLength == 1) {
                        ddName = submoduleStr[0] + "[" + ddName + "]";
                    }
                }

                let ddSelector = "select[name='" + ddName + "']";

                if (!$(ddSelector).length) {
                    return;
                }

                $(document).on(
                    "change",
                    ddSelector,
                    function (e, wasTriggered) {
                        // if(wasTriggered) {
                        //     return false;
                        // }
                        let deferred = $.Deferred();
                        let requestData = {
                            dd_filterkey: data.dd_qry_filterkey,
                            dd_key: data.dd_key,
                            dd_lookup: data.dd_lookup,
                            dd_table: data.dd_table,
                        };
                        let formEl = $(this).parents("form:first");
                        let instituteDD = '[name="institute_id"]';
                        let deptDD =
                            '[name="department_id"],[name="department"]';
                        let fyDD = '[name="financial_year_id"]';
                        let divisionDD = '[name="division_id"]';
                        if (relationWithDot) {
                            let dotLength = relationWithDot.length;
                            let submoduleStr = relation.split(".");
                            if (dotLength == 2) {
                                instituteDD =
                                    submoduleStr[0] +
                                    "[" +
                                    submoduleStr[1] +
                                    "][institute_id]";
                                deptDD =
                                    submoduleStr[0] +
                                    "[" +
                                    submoduleStr[1] +
                                    "][department_id]";
                            } else if (dotLength == 1) {
                                instituteDD =
                                    submoduleStr[0] + "[institute_id]";
                                deptDD = submoduleStr[0] + "[department_id]";
                            }
                            instituteDD = '[name="' + instituteDD + '"]';
                            deptDD = '[name="' + deptDD + '"]';
                        }
                        if (formEl.length > 0 && formEl.find(instituteDD)) {
                            requestData["institute_id"] = formEl
                                .find(instituteDD)
                                .val();
                        }
                        if (formEl.length > 0 && formEl.find(deptDD)) {
                            requestData["department_id"] = formEl
                                .find(deptDD)
                                .val();
                        }
                        if (formEl.length > 0 && formEl.find(divisionDD)) {
                            requestData["division_id"] = formEl
                                .find(divisionDD)
                                .val();
                        }
                        if (formEl.length > 0 && formEl.find(fyDD)) {
                            requestData["financial_year_id"] = formEl
                                .find(fyDD)
                                .val();
                        }

                        if (data.dd_qry_filterkey == "entity_id") {
                            requestData.dd_entitytypekey = "entity_type";
                            requestData.dd_entitytypeValue =
                                $("#entity_type").val();
                        }
                        requestData.value = $(this).val();

                        if (!crudUtils.localDDData[ddSelector]) {
                            crudUtils.localDDData[ddSelector] =
                                JSON.stringify(requestData);
                        } else if (
                            crudUtils.localDDData[ddSelector] ==
                            JSON.stringify(requestData)
                        ) {
                            crudUtils.setDependentSelectedValue(
                                $(childDDSelector)
                            );
                            return true;
                        } else {
                            crudUtils.localDDData[ddSelector] =
                                JSON.stringify(requestData);
                        }

                        $.ajax({
                            url: "/cruds/get_dependant_dd_options",
                            type: "POST",
                            headers: {
                                "X-CSRF-TOKEN":
                                    $('meta[name="csrf-token"]').attr(
                                        "content"
                                    ) || $('[name="_token"]').val(),
                            },
                            data: requestData,
                            dataType: "json",
                            beforeSend: () => {
                                // _self.showAjaxLoading();
                            },
                            success: (response) => {
                                if (response.error) {
                                    // crudUtils.notifyMessageError(data.message);
                                    deferred.reject();
                                }
                                if ($(childDDSelector).data("select2")) {
                                    CustomScript.initCustomSelect2(
                                        $(childDDSelector)
                                            .select2("destroy")
                                            .empty()
                                            .prepend(
                                                '<option selected=""></option>'
                                            ),
                                        { data: response }
                                    );
                                    crudUtils.setDependentSelectedValue(
                                        $(childDDSelector)
                                    );
                                }
                                deferred.resolve(data);
                            },
                            complete: () => {
                                // _self.hideAjaxLoading();
                            },
                            error: (data) => {
                                deferred.reject();
                                // crudUtils.notifyMessageError(data.message);
                                // MessageService.handleError(data);
                            },
                        });

                        return deferred;
                    }
                );
            });
    },

    notifyMessageError: function (message) {
        $.toast({
            heading: "error",
            text: message,
            position: "top-right",
            icon: "error",
            hideAfter: 3000,
            stack: 6,
        });
    },

    loadRepeaterToolbarBtns: function () {
        let formEl = crudUtils.formEl;
        if (
            formEl.hasClass("viewForm") ||
            !$(".subFormRepeater > .form-group").length
        ) {
            return false;
        }

        $(".subFormRepeater > .form-group").each(function () {
            if (!$(this).find(".repeaterBtns").length) {
                $(this)
                    .prepend(`<div class='row repeaterBtns'><a href='#' class='removeFieldLine' title='Remove this line'><i class='fa fa-minus' aria-hidden='true'></i></a>
                <a href='#' class='addFieldLine' style='display:none;' title='Add this line'><i class='fa fa-plus-circle' aria-hidden='true'></i></a></div>`);
            }
        });

        crudUtils.initRepeater();
        crudUtils.hideRemoveBtn();
    },

    initRepeater: function () {
        $(document).on("click", ".addFieldLine", crudUtils.addRowElement);
        $(document).on("click", ".removeFieldLine", crudUtils.removeRowElement);
    },

    addRowElement: function (e) {
        e.preventDefault();
        let target = $(e.target);
        let rowElm = target.parents(".form-group:first");
        if (rowElm.find("select").data("select2")) {
            rowElm.find("select").select2("destroy");
        }

        let container = rowElm.parents(".subFormRepeater:first");
        let rowCnt = container.find("> .form-group").length;
        let rowTemplate = rowElm.clone();
        let lastRowEl = container.find("> .form-group:last");
        let inputElm = lastRowEl.find("input:first").attr("name");
        var matches = inputElm.match(/\[(.*?)\]/);
        let rowIndex = (CommonUtils.isValidArray(matches)) ? parseInt(matches[1]) : 0;
        let newIndex = rowIndex + 1;
        $(rowTemplate)
            .find("span.invalid-feedback")
            .each(function () {
                let attr = $(this).attr("class");
                let idAttr = $(this).attr("id");
                $(this).attr("class", attr.replace(rowIndex, newIndex));
                if(idAttr){
                    $(this).attr("id", idAttr.replace(rowIndex, newIndex));
                }                
            });
        $(rowTemplate)
            .find("input,textarea,select,a,label")
            .each(function () {
                let attr = $(this).attr("name");
                if (attr) {
                    $(this).attr("id", attr.replace(rowIndex, newIndex));
                    $(this).attr("name", attr.replace(rowIndex, newIndex));
                    let aAttr = $(this).attr("aria-describedby");
                    if (aAttr) {
                        $(this).attr(
                            "aria-describedby",
                            aAttr.replace(rowIndex, newIndex)
                        );
                    }
                }

                if ($(this).is("label")) {
                    let forAttr = $(this).attr("for");
                    if(forAttr){
                        $(this).attr("for", forAttr.replace(rowIndex, newIndex));
                    }                    
                }

                if ($(this).getType() == "select") {
                    $(this).val("");
                    // CustomScript.initCustomSelect2($(this));
                } else if ($(this).hasClass("datepicker")) {
                    $(this).bootstrapDP("update");
                } else if ($(this).hasClass("time-picker")) {
                    crudUtils.refreshTimePicker($(this));
                }
                $(this).val("");
            });
        rowTemplate.find(".removeFieldLine").show();
        container.append(rowTemplate);
        CustomScript.initCustomSelect2($(rowElm).find(".select-full"));
        CustomScript.initCustomSelect2($(rowTemplate).find(".select-full"));
        crudUtils.triggerDependentDropdown();
        crudUtils.refreshRVMedia(rowTemplate);
        crudUtils.hideRemoveBtn(container);
        crudUtils.hideRequiredValidation(rowTemplate);
    },

    refreshTimePicker: function (el) {
        $(el).timepicker({
            autoclose: true,
            minuteStep: 5,
            showSeconds: false,
            showMeridian: false,
            defaultTime: false,
        });
    },

    removeRowElement: function (e) {
        e.preventDefault();
        let target = $(e.target);
        let rowElm = target.parents(".form-group:first");
        let container = rowElm.parents(".subFormRepeater:first");
        let rowCnt = container.find("> .form-group").length;
        if (rowCnt == 1) {
            $(rowElm)
                .find("input,textarea,select,a")
                .each(function () {
                    $(this).val("");
                });
        } else {
            rowElm.remove();
        }
        crudUtils.hideRemoveBtn(container);
    },

    hideRemoveBtn: function (el) {
        el = el ? el : $(".subFormRepeater");
        el.find(" > .form-group .addFieldLine").hide();
        el.find(" > .form-group .addFieldLine:last").show();
    },
    hideRequiredValidation: function (el) {
        let hideRequiredGroupClass = ['.vendor_authorized_signature_details','.vendor_key_contact_details','.vendor_bank_details'] 
        $.each(hideRequiredGroupClass,function(i,v){
            if(el.find(v).length){
                el.find("label").removeClass('required');
            }            
        });
        
    },

    getEntityData: function (el) {
        let deferred = $.Deferred();
        let requestData = { entity_id: $(el).val() };
        $.ajax({
            url: "/cruds/get_entity_options",
            type: "POST",
            headers: {
                "X-CSRF-TOKEN":
                    $('meta[name="csrf-token"]').attr("content") ||
                    $('[name="_token"]').val(),
            },
            data: requestData,
            dataType: "json",
            success: (response) => {
                if (response.error) {
                    deferred.reject();
                }
                CustomScript.initCustomSelect2(
                    $("#entity_id")
                        .select2("destroy")
                        .empty()
                        .prepend('<option selected=""></option>'),
                    { data: response }
                );
                deferred.resolve(response);
            },

            error: (data) => {
                CustomScript.initCustomSelect2(
                    $("#entity_id")
                        .select2("destroy")
                        .empty()
                        .prepend('<option selected=""></option>'),
                    { data: [] }
                );
                deferred.reject();
            },
        });

        return deferred;
    },

    presentAllToggle: function (e) {
        console.log("presentAllToggle");
        let target = $(e.target);
        let customDT = target.parents('table.dataTable');
        $(".absent-check-all").prop('checked', false);
        let set = customDT.find('td .radio-present');
        let checked = $(target).is(':checked');

        $(set).each(function () {
            let value = 0;
            if (checked) {
                $(this).prop('checked', true);
                value = 1;
            } else {
                $(this).prop('checked', false);
            }
            let id = $(this).data('key');
            crudUtils.selectedPresentRowCache[id] = value;
            crudUtils.selectedAbsentRowCache[id] = !value;

        });
    },

    absentAllToggle: function (e) {
        let target = $(e.target);
        let customDT = target.parents('table.dataTable');
        $(".present-check-all").prop('checked', false);
        let set = customDT.find('td .radio-absent');
        let checked = $(target).is(':checked');

        $(set).each(function () {
            let value = 0;
            if (checked) {
                $(this).prop('checked', true);
                value = 1;
            } else {
                $(this).prop('checked', false);
            }
            let id = $(this).data('key');
            crudUtils.selectedAbsentRowCache[id] = value;
            crudUtils.selectedPresentRowCache[id] = !value;

        });
    },

    presentAbsentToggle: function (e) {

        console.log('presentAbsentToggle');
        let target = $(e.target);
        let checked = $(target).is(':checked');
        let cls = "";
        let id = $(target).data('key');
        crudUtils.selectedPresentRowCache[id] = 0;
        crudUtils.selectedAbsentRowCache[id] = 0;

        if (checked && $(target).hasClass('radio-present')) {
            $('.absent-check-all').prop('checked', false);
            cls = 'radio-present';
            crudUtils.selectedPresentRowCache[id] = 1;
        } else if (checked && $(target).hasClass('radio-absent')) {
            $('.present-check-all').prop('checked', false);
            cls = 'radio-absent';
            crudUtils.selectedAbsentRowCache[id] = 1;
        }

        crudUtils.updateGroupSelectionAttendanceUI(e, cls);
    },

    updateGroupSelectionAttendanceUI: function (e, cls) {
        console.log('updateGroupSelectionAttendanceUI -> cls');
        console.log(cls);
        let target = $(e.target);
        let customDT = target.parents('table.dataTable');
        let overAllLength = customDT.find('tbody .' + cls).length;
        let overAllCheckedLength = customDT.find('tbody .' + cls + ':checked').length;
        let overAllChecked = (overAllCheckedLength > 0 && overAllCheckedLength == overAllLength);
        customDT.find('thead .custom-check-all[data-set="' + cls + '"]').prop("checked", overAllChecked);
    },

    populateAttendanceDataFromCache: function () {
        crudUtils.loadAttendanceData(crudUtils.selectedPresentRowCache, 'radio-present');
        crudUtils.loadAttendanceData(crudUtils.selectedAbsentRowCache, 'radio-absent');
    },

    loadAttendanceData: function (data, cls) {
        if (!$.isEmptyObject(data)) {
            $.map(data, function (val, rowId) {
                if (val) {
                    $('table.dataTable td .' + cls + '[data-key="' + rowId + '"]').prop('checked', true);
                } else {
                    $('table.dataTable td .' + cls + '[data-key="' + rowId + '"]').prop('checked', false);
                }
            });
        }
    },

    getAllSelectedRows: function (e) {
        let data = [];

        $("table.dataTable")
            .find("td .table-checkbox .checkboxes")
            .each(function () {
                if ($(this).is(":checked")) {
                    data.push($(this).val());
                }
            });

        return data;
    },

    captureAllEditableRowIds: function (e) {
        let target = $(e.target);
        let row = target.parents("tr:first");
        let id = row.find('[name="id[]"]').val();
        crudUtils.gridEditedIds[id] = {};
        let data = {};

        $(row)
            .find("input:not([type=hidden]),select,textarea")
            .each(function () {
                let name = $(this).attr("name");
                if (name) {
                    name = name.replace("[]", "");
                    data[name] = $(this).val();
                }
            });
        if (id) {
            data["update"] = true;
            crudUtils.gridEditedIds[id] = data;
        } else {
            crudUtils.gridEditedIds["row_index_" + crudUtils.gridRowIndex] =
                data;
        }
        crudUtils.gridRowIndex += 1;
    },
    captureRowData: function (target) {
        let table = $("#" + target);
        crudUtils.gridEditedIds = {};
        crudUtils.gridRowIndex = 0;
        $.each(table.find("tbody tr"), function (index, row) {
            let id = $(row).find('[name="id[]"]').val();
            crudUtils.gridEditedIds[id] = {};
            let data = {};

            //            $(row).find('input:not([type=hidden]),select,textarea').each(function () {
            $(row)
                .find("input,select,textarea")
                .each(function () {
                    let name = $(this).attr("name");
                    if (name) {
                        name = name.replace("[]", "");
                        data[name] = $(this).val();
                    }
                });
            if (id) {
                data["update"] = true;
                crudUtils.gridEditedIds[id] = data;
            } else {
                crudUtils.gridEditedIds["row_index_" + crudUtils.gridRowIndex] =
                    data;
            }
            crudUtils.gridRowIndex += 1;
        });
    },
    saveInlineGridDetails: function (e) {
        let target = $(e.currentTarget);
        let action = target.find("span[data-action]").data("action");

        if (action == "save") {
            let tableName = target.attr("aria-controls");
            let urlMap = {
                "plugins-attendance-table": { modal: ".modal-attendance" },
                "plugins-staff-attendance-table": {
                    modal: ".modal-attendance",
                    hide_classroom_photo: true,
                },
                "plugins-exam-attendance-table": {
                    url: "exam-attendances/saveattendance",
                    modal: ".modal-confirm-attendance",
                },
                "plugins-headed-paper-table": { modal: ".modal-headed_paper" },
            };

            if (
                $.inArray(tableName, [
                    "plugins-attendance-table",
                    "plugins-staff-attendance-table",
                    "plugins-exam-attendance-table",
                ]) != -1
            ) {
                if ($.isEmptyObject(crudUtils.gridEditedIds)) {
                    Impiger.showError(
                        "Please give attendance before submitting"
                    );
                    return;
                }

                $(urlMap[tableName]["modal"]).modal("show");
                if (urlMap[tableName]["hide_classroom_photo"]) {
                    $(urlMap[tableName]["modal"]).on(
                        "shown.bs.modal",
                        function (e) {
                            $(".visible-on-student-attendance").hide();
                        }
                    );
                }
                return;
            } else if (tableName == "plugins-headed-paper-table") {
                let selectedRow = crudUtils.getAllSelectedRows();
                if (!CustomScript.isValidArray(selectedRow)) {
                    Impiger.showError(
                        "Please select at least one record to perform this action!"
                    );
                    return;
                }
                $(urlMap[tableName]["modal"]).modal("show");
                return;
            }

            let updateMarksTable = [
                "plugins-submitted-assignments-table",
                "plugins-coursework-test-marks-table",
            ];
            if ($.inArray(tableName, updateMarksTable) !== -1) {
                crudUtils.captureRowData(tableName);
            }

            if ($.isEmptyObject(crudUtils.gridEditedIds)) {
                Impiger.showError(
                    "Please edit at least one record to perform this action!"
                );
                return;
            }

            let cls = $(document)
                .find("[data-class-item]:first")
                .data("class-item");

            $.ajax({
                url: "/tables/bulk-change/inlinesave",
                type: "POST",
                data: { data: crudUtils.gridEditedIds, class: cls },
                success: (data) => {
                    if (data.error) {
                        if (!$.isEmptyObject(data.message)) {
                            let msgs = [];
                            $.map(data.message, function (msg) {
                                msgs.push(msg);
                            });
                            data.message = msgs.join("\n");
                        }
                        Impiger.showError(data.message);
                    } else {
                        Impiger.showSuccess(data.message);
                    }
                },
                error: (data) => {
                    Impiger.handleError(data);
                },
            });
        }
    },

    isValidArray: function (inputArray) {
        if (inputArray && $.isArray(inputArray) && inputArray.length > 0) {
            return true;
        }
    },

    validateStaticFilterForm: function () {
        $(".filter-form .invalid-feedback").hide();
        $(".filter-form").on("submit", function (e) {
            var bSubmit = true;

            $(".filter-form label.required").each(function () {

                let parentElm = $(this).next(".ui-select-wrapper");
                if (!parentElm.find("select").val()) {
                    parentElm.find(".invalid-feedback").show();
                    if (bSubmit) {
                        bSubmit = false;
                    }
                } else {
                    parentElm.find(".invalid-feedback").hide();
                }
            });

            

            if (!bSubmit) {
                e.preventDefault();
                return false;
            }
        });
    },
    updateRowActivation: function () {
        $(document).on("click", ".rowActivationDialog", (event) => {
            event.preventDefault();
            let _self = $(event.currentTarget);

            $(".row-activation-crud-entry")
                .data("parent-table", _self.closest(".table").prop("id"))
                .data("data", _self.data());

            let modalContent =
                "Do you really want to " +
                _self.data("originalTitle").toLowerCase() +
                "  this record?";
            $(".modal-confirm-activation")
                .find(".modal-body")
                .text(modalContent);
            $(".modal-confirm-activation").modal("show");
        });
        $(".row-activation-crud-entry").on("click", (event) => {
            event.preventDefault();
            let _self = $(event.currentTarget);

            _self.addClass("button-loading");

            let data = _self.data("data");
            let activateURL = data.section;
            let requestData = {
                value: data.value,
                model: data.model,
                module: data.module,
            };

            $.ajax({
                url: baseUrl + activateURL,
                type: "POST",
                data: requestData,
                dataType: "json",
                success: (response) => {
                    if (response.error) {
                        Impiger.showError(response.message);
                    } else {
                        window.LaravelDataTables[_self.data("parent-table")]
                            .row(
                                $(
                                    'a[data-section="' + activateURL + '"]'
                                ).closest("tr")
                            )
                            .draw();
                        Impiger.showSuccess(response.message);
                    }

                    _self.closest(".modal").modal("hide");
                    _self.removeClass("button-loading");
                },
                error: (data) => {
                    Impiger.handleError(data);
                    _self.removeClass("button-loading");
                },
            });
        });
    },

    copyAddressData: function (e) {
        let targetEl = e.target;
        let permanentAddressEl = $(targetEl).parents(".grouppedLayout:first");
        let presentAddressEl = permanentAddressEl.prev();
        let checked = targetEl.checked;
        let formEl = permanentAddressEl.parents("form:first");
        let pathName = window.location.pathname.replace(/\//g, "");
        let key = "imp_ls_" + $(formEl).attr("id") + "_" + pathName + "_old";
        let data = {};

        $(presentAddressEl)
            .find("input,textarea,select")
            .each(function () {
                let fieldIndex = $(this).data("field_index");
                let elm = $(permanentAddressEl).find(
                    '[data-field_index="' + fieldIndex + '"]'
                );
                let value = checked ? $(this).val() : "";
                data[$(elm).attr("name")] = value;
                localStorage.setItem(key, JSON.stringify(data));

                if ($(this).getType() == "select") {
                    if (!value) {
                        elm.val(value);
                        elm.val(value).trigger("change", true);
                    } else {
                        var $options = $(this).find(" > option").clone();
                        elm.empty();
                        CustomScript.initCustomSelect2(elm.append($options));
                        if (
                            crudUtils.lastPart.startsWith("create") ||
                            crudUtils.frontEndForm
                        ) {
                            elm.val(value).select2().trigger("change", true);
                        }
                    }
                } else {
                    elm.val(value);
                    elm.val(value).trigger("change", true);
                }
            });
    },

    setDependentSelectedValue: function (el) {
        let formEl = el.parents("form:first");
        let pathName = window.location.pathname.replace(/\//g, "");
        let key = "imp_ls_" + $(formEl).attr("id") + "_" + pathName + "_old";
        if (localStorage.getItem(key)) {
            let data = JSON.parse(localStorage.getItem(key)) || {};

            let name = $(el).attr("name");
            let value = data[name] || "";
            $(el).val(value).trigger("change", true);
        }
    },

    dynamicValidationUI: function () {
        if (crudUtils.formEl.hasClass("viewForm")) {
            return false;
        }

        $("#impiger-user-forms-user-form").on(
            "change",
            "#if_refugee",
            function () {
                let cardNoText = $('label[for="card_number"]').text();
                cardNoText = cardNoText.replace("*", "");
                if ($(this).prop("checked")) {
                    crudUtils.appendRequiredSymbol("card_number", cardNoText);
                } else {
                    $('label[for="card_number"]').text(cardNoText);
                }
            }
        );

        setTimeout(function () {
            $("#impiger-user-forms-user-form").on(
                "change",
                '[name="user_addresses[present_country]"],[name="user_addresses[permanent_country]"]',
                function () {
                    $('[name="user_addresses[permanent_phonecode]"]')
                        .val(
                            $('[name="user_addresses[permanent_phonecode]"]')
                                .find("option")
                                .eq(1)
                                .val()
                        )
                        .trigger("change", true);
                    $('[name="user_addresses[present_phonecode]"]')
                        .val(
                            $('[name="user_addresses[present_phonecode]"]')
                                .find("option")
                                .eq(1)
                                .val()
                        )
                        .trigger("change", true);
                }
            );
        }, 500);
    },

    appendRequiredSymbol: function (key, text) {
        if (!$('label[for="' + key + '"] span').length) {
            $('label[for="' + key + '"]').html(
                text + "<span class='required-field'>*</span>"
            );
        }
    },

    refreshRVMedia: function (el) {
        $(el).find(".attachment-details a").html("");
        $(el).find(".list-photos-gallery .row").html("");
        Impiger.initMediaIntegrate();
        if ($('.btn_select_file').length && multiFileUploadUtils) {
            multiFileUploadUtils.init();
        }
    },

    viewDetailInPopup: function () {
        $(document).on("click", ".viewRowData", (event) => {
            event.preventDefault();
            event.stopPropagation();
            let _self = $(event.currentTarget);
            let data = _self.data();
            $.ajax({
                type: "GET",
                cache: false,
                url: data.src,
                success: (res) => {
                    if (!res.error) {
                        $(document)
                            .find("#" + data.targetelm)
                            .loadViewDataFromJSON(res);
                        $(document)
                            .find("#" + data.targetelm)
                            .modal("toggle");
                    }
                    $(event.currentTarget).removeClass("button-loading");
                },
                error: (res) => {
                    $(event.currentTarget).removeClass("button-loading");
                    Impiger.handleError(res);
                },
            });
        });
    },

    customValidation: function () {
        if (crudUtils.formEl.find('[name="g-recaptcha-response"]').length || crudUtils.formEl.find(".image-data").length) {
        $(document).on("click", 'button[name="submit"]', function (event) {
            crudUtils.formEl.valid();
            let that = $(this);
            if (crudUtils.formEl.find('[name="g-recaptcha-response"]').length) {
                if (
                    !crudUtils.formEl
                        .find('[name="g-recaptcha-response"]')
                        .val()
                ) {
                    Impiger.showError("Captcha is required");
                    CustomScript.enableButton(that);
                    event.preventDefault();
                    return false;
                }
            }
            if (crudUtils.formEl.attr('id') != "impiger-msme-candidate-details-forms-msme-candidate-details-form" && crudUtils.formEl.find(".image-data").length) {
                crudUtils.formEl.find(".image-data").each(function () {
                    if ($(this).val()) {
                        var filePath = $(this).val();
                        var nameAttr = $(this).attr("name");
                        var allowedExtensions = /(\.jpg|\.jpeg|\.png|\.gif)$/i;
                        if (!allowedExtensions.exec(filePath)) {
                            Impiger.showError(
                                "Please upload " +
                                    nameAttr +
                                    " having extensions .jpeg/.jpg/.png/.gif only."
                            );
                            CustomScript.enableButton(that);
                            event.preventDefault();
                            return false;
                        }
                    }
                });
            }
            if (crudUtils.formEl.find(".attachment-details").length) {
                crudUtils.formEl.find(".attachment-details").each(function () {
                    var nameAttr = $(this).prev().attr("name");
                    var isRequired = $(this)
                        .parent()
                        .prev()
                        .hasClass("required");
                        
                    if (nameAttr.indexOf('specimen_signature')==-1 &&
                        !crudUtils.formEl.find(".wizard") &&
                        isRequired &&
                        !$(this).prev().val()
                    ) {
                        Impiger.showError(
                            "The " + nameAttr + " field is required."
                        );
                        CustomScript.enableButton(that);
                        event.preventDefault();
                        return false;
                    }
                });
            }
        });
        }
    },

    getUrlVars: function () {
        var vars = [],
            hash;
        var hashes = window.location.href
            .slice(window.location.href.indexOf("?") + 1)
            .split("&");
        for (var i = 0; i < hashes.length; i++) {
            hash = hashes[i].split("=");
            vars.push(hash[0]);
            vars[hash[0]] = hash[1];
        }
        return vars;
    },
    viewRepeaterFields:function(){
        if (crudUtils.viewFormEl.length) {
            if ($(document).find('.repeater-group').length) {
                $(document).find('.repeater-group > button').hide();
                $(document).find('.repeater-group > .form-group > span.remove-item-button').hide();
                $(document).find('.repeater-group > .form-group').each(function(){
                    let html = "";
                    $(this).find('.repeater-item-group .form-group').each(function () {
                        let labelEl = $(this).find('label:first');
                        let divClass= $(this).attr('class');
                        html += `<div class="${divClass}">`
                        if (labelEl.length) {
                            let imageEl = $(this).find('.image-box');
                            
                            let idAttr = labelEl.attr('for');
                            html += `<label class="control-label highlight">` + labelEl.text() + `</label>`;
                            if (imageEl.length) {
                                html += `<div class="image-box">` + imageEl.find('.preview-image-wrapper').html() + `</div>`;
                            } else {
                                let value = $('#' + idAttr).val() || $(this).find('[name="' + idAttr + '"]').val();
                                let inputEl = document.getElementById(idAttr);
//                                if ($('[name="' + idAttr + '"]').getType() == "select") {
//                                    value = $('[name="' + idAttr + '"] option:selected').text();
//                                }
                                value = value || "";
                                html += `<div class="customStaticCls">` + value + `</div>`;
                            }
                            
                        }
                        html += `</div>`;
                    });
                    $(this).find('.repeater-item-group ').html("<div class='row'>"+html+"</div>");
                    });
            }
        }
    },

    toggleRepeaterGroupBtn: function(rowLength, elm) {
        if(rowLength >= 3) {
            $(elm).hide();
        } else {
            $(elm).show();
        }
    },

    repeaterGroupValidation(){
        if ($(document).find('.repeater-group').length) {   
            $(".team_member_repeater .repeater-group .remove-item-button:first").hide();
			            
			$(document).on("click", ".team_member_repeater .btn-info", function() {
                let repeaterGroup = $(this).parents('.repeater-group:first');
                let repeaterRow = repeaterGroup.find('> .form-group');
                crudUtils.toggleRepeaterGroupBtn(repeaterRow.length, $(this));
                $('.lead-label').not(':eq(0)').text('Team Member Name');
            });

            if( $(".team_member_repeater").length) {
                
            $(document).on("click", ".remove-item-button", function() {
                let repeaterGroup = $(this).parents('.repeater-group:first');
                let repeaterRow = repeaterGroup.find('> .form-group');
                crudUtils.toggleRepeaterGroupBtn(repeaterRow.length, $(".team_member_repeater .btn-info"));
            });
            }

            
            $(document).on("click", 'button[name="submit"]', function (event) {
                $(document).find('.repeater-group > .form-group').each(function(){
                    $(this).find('.repeater-item-group .form-group').each(function () {
                        let labelEl = $(this).find('label:first');
                        let parentEl = labelEl.parent();
                        let errorPlaceHolder = parentEl.find(".invalid-repeater-feedback");
                        if (labelEl.length && labelEl.hasClass('required')) {
                            let inputVal = labelEl.next().val();
                            if(!inputVal){ 
                                errorPlaceHolder.html('This field is required.');
                                errorPlaceHolder.show();
                                    return;
                            } else{
                                errorPlaceHolder.hide();
                            }                           
                        }
                        
                        let formCtrlValidation = $(this).find('.form-control:first[data-rules]');
                        if(formCtrlValidation.length) {
                            let rules = formCtrlValidation.data('rules');
                            let rulesArr = rules.split("|");
                            let inputValue = formCtrlValidation.val();

                            $.each(rulesArr, function(k, rule) {
                                if(rule == "numeric") {
                                    if(!$.isNumeric(inputValue)) {
                                        errorPlaceHolder.html('This field must be a number.');
                                        errorPlaceHolder.show();
                                        return false;
                                    } else {
                                        errorPlaceHolder.hide();
                                        return;
                                    }
                                } else if (rule.startsWith("digits:")) {
                                    let digitArr = rule.split(":");
                                    let digitLen = digitArr[1] || 0;
                                    let inpLen = inputValue.toString().length;
                                    console.log(digitLen, inpLen)

                                    if(digitLen != inpLen) {
                                        errorPlaceHolder.html('This field  must be '+digitLen+' digits.');
                                        errorPlaceHolder.show();
                                        return false;
                                    } else {
                                        errorPlaceHolder.hide();
                                        return;
                                    }

                                }

                            }) 
                        }
                    });
                    
                    });
            });
        }
    }
};

$(document).ready(function () {
    crudUtils.init();
});

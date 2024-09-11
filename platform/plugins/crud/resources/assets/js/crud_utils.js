$.fn.getType = function () { return this[0].tagName == "INPUT" ? this[0].type.toLowerCase() : this[0].tagName.toLowerCase(); }


let crudUtils = {
    gridEditedIds: {},
    gridRowIndex:0,
    localDDData: {},
    selectedPresentRowCache: {},
    selectedAbsentRowCache: {},
    urlParts: $(location).attr('href').split("/"),
    lastPart: "",
    frontEndForm: $('form[action$="postdata"]').length,
    formEl: ($('.main-form').length) ? $('.main-form').parents('form:first') : $('form[action$="postdata"]'),
    init: function () {
        this.bindEvents();
        this.dynamicValidationUI();
        this.bindSubscriptionMethod();
        if (crudUtils.frontEndForm) {
            crudUtils.formEl.find('button[name="submit"]').text('Submit');
        }
    },

    bindEvents: function () {
        this.triggerDependentDropdown();
        this.triggerAcademicDropdown();
        this.loadRepeaterToolbarBtns();
        $(document).on("click", "[data-action='bulk-upload']", function () {
            $(document).find("#bulkUpload").modal();
        });
        $(document).on('change', '#entity_type', function () {
            crudUtils.getEntityData(this);
        })
        $(document).on('change', '.present-check-all', this.presentAllToggle);
        $(document).on('change', '.absent-check-all', this.absentAllToggle);
        $(document).on('change', '.radio-present,.radio-absent', this.presentAbsentToggle);
        $(document).on('change', 'table.dataTable input:not(.table-check-all),table.dataTable select,table.dataTable textbox', this.captureAllEditableRowIds);
        $(document).on('click', '.dataTables_wrapper .dt-buttons button.action-item', this.saveInlineGridDetails);
        this.updateRowActivation();
        $(document).on('change', '.saveAsCopy', this.copyAddressData);
        let url = window.location.protocol + "//" + window.location.host + window.location.pathname;
        $(document).on('change', 'select[restrict_based_on="true"]', function () {
            let urlEncode = btoa($(this).val().join('|'));
            let formKey = $(this).parents('form:first').attr('name') + "_" + $(this).attr('name');
            // let prevRole = localStorage.getItem(formKey);
            // localStorage.setItem(formKey, urlEncode);
            if (window.location.search != "?restricted_roleid=" + urlEncode) {
                location.href = url + "?restricted_roleid=" + urlEncode;
            }
        });

        if ($('select[restrict_based_on="true"]').length) {
            $(window).off('beforeunload');
        }

        crudUtils.lastPart = crudUtils.urlParts[crudUtils.urlParts.length - 1];

        if (crudUtils.lastPart.startsWith('create') || crudUtils.frontEndForm) {
            setTimeout(function () {
                if (crudUtils.formEl.length && $.isFunction($.fn.saveStorage)) {
                    crudUtils.formEl.saveStorage({
                        exclude: ['password', 'hidden']
                    });
                }
            }, 700)
        } else {
            if (crudUtils.formEl.length) {
                setTimeout(function () {
                    crudUtils.formEl.find('.attachment-details a').each(function () {
                        $(this).attr("href", "/storage/" + $(this).attr('href'));
                    });
                    if (!crudUtils.frontEndForm) {
                        crudUtils.formEl.valid();
                    }
                }, 300)
            }
        }
        crudUtils.viewDetailInPopup();
        $(document).on("click", ".viewGallery #list-photo .item", function () {
            parent.$.fancybox.close();
        });
        crudUtils.customValidation();

        if($('[disabled="1"] .mt-radio-list').length) {
            $('[disabled] .mt-radio-list input').attr('disabled', 'disabled');
        }
    },

    bindSubscriptionMethod: function () {
        if ($('.dataTable').length) {
            $(document).on("click", ".rowSubscriptionDialog", (event) => {
                $('.customError').hide();
                $('#institute_ids option').removeAttr('disabled');
                $('#institute_ids').select2().trigger('change');
                event.preventDefault();
                let _self = $(event.currentTarget);
                let data = _self.data() || {};
                let instituteIds = data.subscribed_institute_ids || "";
                instituteIds = instituteIds.toString().split(',');
                $('#institute_ids').val(instituteIds);
                if (instituteIds.length > 0) {
                    $.each(instituteIds, function (k, val) {
                        $('#institute_ids option[value="' + val + '"]').prop('disabled', 'disabled');
                    })
                }

                $('#institute_ids').trigger('change');
                $(".row-subscription-crud-entry")
                    .data("parent-table", _self.closest(".table").prop("id"))
                    .data("data", _self.data());
                // let modalCls =  (crudUtils.lastPart.startsWith('organizations')) ? ".modal-confirm-subscription-with-detail" : ".modal-confirm-subscription";
                $(".modal-confirm-subscription").modal("show");
            });

            $('body').on('hidden.bs.modal', '.modal-confirm-subscription', function () {
                $(this).removeData('bs.modal');
                $('#institute_ids option').removeAttr('disabled');
                $('#institute_ids').val([]).select2().trigger('change');
                // location.href = location.href;
            });

            $(document).on("click", ".row-subscription-crud-entry", (event) => {
                $('.customError').hide();
                event.preventDefault();
                let instituteId = $('#institute_ids').val();
                let mouAttachment = $('[name="mou_attachment"]').val();
                let areaOfPartner = $('[name="area_of_partnership"]').val();
                let _self = $(event.currentTarget);
                if (!$.isArray(instituteId) || instituteId.length <= 0) {
                    $('<span class="invalid-feedback customError" style="display: inline;">Please select atleast one institute for subscription.</span>').insertAfter($('#institute_ids'));
                    return false;
                }
                _self.addClass("button-loading");
                let data = _self.data("data");
                let activateURL = '/admin/cruds/subscription/' + data.item_id;
                let requestData = {
                    'institute_ids': instituteId,
                    'model_class': data.model_class || "",
                    'mou_attachment': mouAttachment,
                    'area_of_partnership': areaOfPartner
                };

                $.ajax({
                    url: baseUrl + activateURL,
                    type: "POST",
                    data: requestData,
                    dataType: 'json',
                    success: (response) => {
                        if (response.error) {
                            Impiger.showError(response.message);
                        } else {
                            window.LaravelDataTables[_self.data("parent-table")]
                                .row(
                                    $(
                                        'a[data-section="' + activateURL + '"]'
                                    ).closest("tr")
                                ).draw();
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

    triggerAcademicDropdown: function () {
        $('.filter-form').on('change', '.institute_id,.department_id,.program_type_id,.training_program_id, .intake_id', function() {
            let deferred = $.Deferred();
            let academicOption = $(this).data('academic_option');
            let dependentDD = $(this).data('dependent');
            let requestData = {
                'institute_id': $('.filter-form .institute_id').val(),
                'department_id': $('.filter-form .department_id').val(),
                'program_type_id': $('.filter-form .program_type_id').val(),
                'training_program_id': $('.filter-form .training_program_id').val(),
                'intake_id': $('.filter-form .intake_id').val(),
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
                        CustomScript.initCustomSelect2($(ddSelector).select2('destroy').empty().prepend('<option selected=""></option>'), { data: response });
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
        $(document).find("select[data-dd_parentkey]").each(function () {
            let childDDName = $(this).attr('name');
            let childDDSelector = "select[name='" + childDDName + "']";
            let data = $(this).data() || {};
            let ddName = data.dd_parentkey || "";

            let relation = $(this).attr('name').replace(/\[/g, ".").replace(/\]/g, "");
            let relationWithDot = relation.match(/\./g);

            if (relationWithDot) {
                let dotLength = relationWithDot.length;
                let submoduleStr = relation.split('.');

                if (dotLength == 2) {
                    ddName = submoduleStr[0] + "[" + submoduleStr[1] + "][" + ddName + "]";
                } else if (dotLength == 1) {
                    ddName = submoduleStr[0] + "[" + ddName + "]";
                }
            }

            let ddSelector = "select[name='" + ddName + "']";

            if (!$(ddSelector).length) {
                return;
            }

            $(document).on('change', ddSelector, function (e, wasTriggered) {
                // if(wasTriggered) {
                //     return false;
                // }
                let deferred = $.Deferred();
                let requestData = {
                    dd_filterkey: data.dd_qry_filterkey,
                    dd_key: data.dd_key,
                    dd_lookup: data.dd_lookup,
                    dd_table: data.dd_table
                }
                if (data.dd_qry_filterkey == 'entity_id') {
                    requestData.dd_entitytypekey = 'entity_type';
                    requestData.dd_entitytypeValue = $("#entity_type").val();
                }
                requestData.value = $(this).val();

                if (!crudUtils.localDDData[ddSelector]) {
                    crudUtils.localDDData[ddSelector] = JSON.stringify(requestData);
                }
                else if (crudUtils.localDDData[ddSelector] == JSON.stringify(requestData)) {
                    crudUtils.setDependentSelectedValue($(childDDSelector));
                    return true;
                } else {
                    crudUtils.localDDData[ddSelector] = JSON.stringify(requestData);
                }

                $.ajax({
                    url: '/cruds/get_dependant_dd_options',
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
                        if ($(childDDSelector).data('select2')) {
                            CustomScript.initCustomSelect2($(childDDSelector).select2('destroy').empty().prepend('<option selected=""></option>'), { data: response });
                            crudUtils.setDependentSelectedValue($(childDDSelector));
                        }
                        deferred.resolve(data);
                    },
                    complete: () => {
                        // _self.hideAjaxLoading();
                    },
                    error: data => {
                        deferred.reject();
                        // crudUtils.notifyMessageError(data.message);
                        // MessageService.handleError(data);
                    }
                });

                return deferred;
            });
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
        if (formEl.hasClass('viewForm') || !$('.subFormRepeater > .form-group').length) {
            return false;
        }

        $('.subFormRepeater > .form-group').each(function () {
            if (!$(this).find('.repeaterBtns').length) {
                $(this).prepend(`<div class='row repeaterBtns'><a href='#' class='removeFieldLine' title='Remove this line'><i class='fa fa-minus' aria-hidden='true'></i></a>
                <a href='#' class='addFieldLine' style='display:none;' title='Add this line'><i class='fa fa-plus-circle' aria-hidden='true'></i></a></div>`);
            }
        });

        crudUtils.initRepeater();
        crudUtils.hideRemoveBtn();
    },

    initRepeater: function () {
        $(document).on('click', '.addFieldLine', crudUtils.addRowElement);
        $(document).on('click', '.removeFieldLine', crudUtils.removeRowElement);
    },

    addRowElement: function (e) {
        e.preventDefault();
        let target = $(e.target);
        let rowElm = target.parents('.form-group:first');
        if (rowElm.find('select').data('select2')) {
            rowElm.find('select').select2('destroy');
        }

        let container = rowElm.parents('.subFormRepeater:first');
        let rowCnt = container.find('> .form-group').length;
        let rowTemplate = rowElm.clone();
        let lastRowEl = container.find('> .form-group:last');
        let inputElm = lastRowEl.find('input:first').attr('name');
        var matches = inputElm.match(/\[(.*?)\]/);
        let rowIndex = parseInt(matches[1]);
        let newIndex = rowIndex + 1;
        $(rowTemplate).find("input,textarea,select,a").each(function () {
            let attr = $(this).attr('name');
            if (attr) {
                $(this).attr('id', attr.replace(rowIndex, newIndex));
                $(this).attr('name', attr.replace(rowIndex, newIndex));
            }

            if ($(this).getType() == "select") {
                $(this).val('');
                // CustomScript.initCustomSelect2($(this));
            }
            else if ($(this).hasClass('datepicker')) {
                $(this).bootstrapDP('update')
            } else if ($(this).hasClass('time-picker')) {
                crudUtils.refreshTimePicker($(this));
            }
            $(this).val('');
        });
        rowTemplate.find('.removeFieldLine').show();
        container.append(rowTemplate);
        CustomScript.initCustomSelect2($(rowElm).find(".select-full"));
        CustomScript.initCustomSelect2($(rowTemplate).find(".select-full"));
        crudUtils.triggerDependentDropdown();
        crudUtils.refreshRVMedia(rowTemplate);
        crudUtils.hideRemoveBtn(container);
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
        let rowElm = target.parents('.form-group:first');
        let container = rowElm.parents('.subFormRepeater:first');
        let rowCnt = container.find('> .form-group').length;
        if (rowCnt == 1) {
            $(rowElm).find("input,textarea,select,a").each(function () {
                $(this).val('');
            });
        } else {
            rowElm.remove();
        }
        crudUtils.hideRemoveBtn(container);
    },

    hideRemoveBtn: function (el) {
        el = (el) ? el : $('.subFormRepeater');
        el.find(' > .form-group .addFieldLine').hide();
        el.find(' > .form-group .addFieldLine:last').show();
    },

    getEntityData: function (el) {
        let deferred = $.Deferred();
        let requestData = { 'entity_id': $(el).val() };
        $.ajax({
            url: '/cruds/get_entity_options',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || $('[name="_token"]').val()
            },
            data: requestData,
            dataType: 'json',
            success: response => {
                if (response.error) {
                    deferred.reject();
                }
                CustomScript.initCustomSelect2($("#entity_id").select2('destroy').empty().prepend('<option selected=""></option>'), { data: response });
                deferred.resolve(response);
            },

            error: data => {
                CustomScript.initCustomSelect2($("#entity_id").select2('destroy').empty().prepend('<option selected=""></option>'), { data: [] });
                deferred.reject();
            }
        });

        return deferred;
    },

    presentAllToggle: function (e) {
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

        });
    },

    presentAbsentToggle: function (e) {
        let target = $(e.target);
        let checked = $(target).is(':checked');
        let cls = "";
        let id = $(target).data('key');
        console.log(id);
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
        let target = $(e.target);
        let customDT = target.parents('table.dataTable');
        let overAllLength = customDT.find('tbody .' + cls).length;
        let overAllCheckedLength = customDT.find('tbody .' + cls + ':checked').length;
        let overAllChecked = (overAllCheckedLength > 0 && overAllCheckedLength == overAllLength);
        customDT.find('thead .custom-check-all[data-set="' + cls + '"]').prop("checked", overAllChecked);
    },

    populateAttendanceDataFromCache: function() {
        crudUtils.loadAttendanceData(crudUtils.selectedPresentRowCache, 'radio-present');
        crudUtils.loadAttendanceData(crudUtils.selectedAbsentRowCache, 'radio-absent');
    },

    loadAttendanceData: function(data, cls) {
        if(!$.isEmptyObject(data)) {
            $.map(data, function(val, rowId) {
                if(val) {
                    $('table.dataTable td .'+cls+'[data-key="'+rowId+'"]').prop('checked', true);
                } else {
                    $('table.dataTable td .'+cls+'[data-key="'+rowId+'"]').prop('checked', false);
                }
            });
        }
    },

    captureAllEditableRowIds: function (e) {
        let target = $(e.target);
        let row = target.parents('tr:first');
        let id = row.find('[name="id[]"]').val();
        crudUtils.gridEditedIds[id] = {};
        let data = {};

        $(row).find('input:not([type=hidden]),select,textarea').each(function () {
            let name = $(this).attr('name');
            if(name) {
                name = name.replace("[]", "");
                data[name] = $(this).val();
            }
        });
        if(id){
            data['update'] = true;
            crudUtils.gridEditedIds[id] = data;
        }else{
            crudUtils.gridEditedIds["row_index_"+crudUtils.gridRowIndex] = data;
        }
        crudUtils.gridRowIndex+=1
    },

    saveInlineGridDetails: function (e) {
        let target = $(e.currentTarget);
        let action = target.find('span[data-action]').data('action');

        if (action == 'save') {
            let tableName = target.attr('aria-controls');

            if(tableName == 'plugins-attendance-table') {
                if ($.isEmptyObject(crudUtils.gridEditedIds)) {
                    Impiger.showError('Please give attendance before submitting');
                    return;
                }

                $(".modal-attendance").modal("show");
                return;
            }

            if ($.isEmptyObject(crudUtils.gridEditedIds)) {
                Impiger.showError('Please edit at least one record to perform this action!');
                return;
            }

            let cls = $(document).find('[data-class-item]:first').data('class-item');

            $.ajax({
                url: '/tables/bulk-change/inlinesave',
                type: "POST",
                data: { 'data': crudUtils.gridEditedIds, 'class': cls },
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

    updateRowActivation: function () {
        $(document).on("click", ".rowActivationDialog", (event) => {
            event.preventDefault();
            let _self = $(event.currentTarget);

            $(".row-activation-crud-entry")
                .data("parent-table", _self.closest(".table").prop("id"))
                .data("data", _self.data());

            let modalContent = "Do you really want to " + _self.data('originalTitle').toLowerCase() + "  this record?"
            $(".modal-confirm-activation").find('.modal-body').text(modalContent);
            $(".modal-confirm-activation").modal("show");
        });
        $(".row-activation-crud-entry").on("click", (event) => {
            event.preventDefault();
            let _self = $(event.currentTarget);

            _self.addClass("button-loading");

            let data = _self.data("data");
            let activateURL = data.section;
            let requestData = {
                'value': data.value,
                'model': data.model,
                'module': data.module,
            };

            $.ajax({
                url: baseUrl + activateURL,
                type: "POST",
                data: requestData,
                dataType: 'json',
                success: (response) => {
                    if (response.error) {
                        Impiger.showError(response.message);
                    } else {
                        window.LaravelDataTables[_self.data("parent-table")]
                            .row(
                                $(
                                    'a[data-section="' + activateURL + '"]'
                                ).closest("tr")
                            ).draw();
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
        let permanentAddressEl = $(targetEl).parents('.grouppedLayout:first');
        let presentAddressEl = permanentAddressEl.prev();
        let checked = targetEl.checked;
        let formEl = permanentAddressEl.parents('form:first');
        let pathName = window.location.pathname.replace(/\//g, "");
        let key = 'imp_ls_' + $(formEl).attr('id') + '_' + pathName + '_old';
        let data = {};

        $(presentAddressEl).find("input,textarea,select").each(function () {
            let fieldIndex = $(this).data('field_index');
            let elm = $(permanentAddressEl).find('[data-field_index="' + fieldIndex + '"]');
            let value = (checked) ? $(this).val() : "";
            data[$(elm).attr('name')] = value;
            localStorage.setItem(key, JSON.stringify(data));

            if ($(this).getType() == "select") {
                if (!value) {
                    elm.val(value);
                    elm.val(value).trigger('change', true);
                } else {
                    var $options = $(this).find(" > option").clone();
                    elm.empty();
                    CustomScript.initCustomSelect2(elm.append($options));
                    if (crudUtils.lastPart.startsWith('create') || crudUtils.frontEndForm) {
                        elm.val(value).select2().trigger('change', true);
                    }
                }
            } else {
                elm.val(value);
                elm.val(value).trigger('change', true);
            }
        })
    },

    setDependentSelectedValue: function (el) {
        let formEl = el.parents('form:first');
        let pathName = window.location.pathname.replace(/\//g, "");
        let key = 'imp_ls_' + $(formEl).attr('id') + '_' + pathName + '_old';
        if (localStorage.getItem(key)) {
            let data = JSON.parse(localStorage.getItem(key)) || {};

            let name = $(el).attr('name');
            let value = data[name] || "";
            $(el).val(value).trigger('change', true);
        }
    },

    dynamicValidationUI: function () {
        if (crudUtils.formEl.hasClass('viewForm')) {
            return false;
        }
        $('#impiger-user-forms-user-form').on('change', '#nationality', function () {
            let NINText = $('label[for="identity_number"]').text();
            let passportNumberText = $('label[for="passport_number"]').text();
            NINText = NINText.replace("*", "");
            passportNumberText = passportNumberText.replace("*", "");

            if ($(this).val() == defaultCountryId) {
                crudUtils.appendRequiredSymbol("identity_number", NINText);
                $('label[for="passport_number"]').text(passportNumberText);
            } else {
                $('label[for="identity_number"]').text(NINText);
                crudUtils.appendRequiredSymbol("passport_number", passportNumberText);
            }
        })

        $('#impiger-user-forms-user-form').on('change', '#if_refugee', function () {
            let cardNoText = $('label[for="card_number"]').text();
            cardNoText = cardNoText.replace("*", "");
            if ($(this).prop('checked')) {
                crudUtils.appendRequiredSymbol("card_number", cardNoText);
            } else {
                $('label[for="card_number"]').text(cardNoText);
            }
        });

        setTimeout(function () {
            $('#impiger-user-forms-user-form').on('change', '[name="user_addresses[present_country]"],[name="user_addresses[permanent_country]"]', function () {
                $('[name="user_addresses[permanent_phonecode]"]').val($('[name="user_addresses[permanent_phonecode]"]').find('option').eq(1).val()).trigger('change', true)
                $('[name="user_addresses[present_phonecode]"]').val($('[name="user_addresses[present_phonecode]"]').find('option').eq(1).val()).trigger('change', true)
            });
        }, 500)
    },

    appendRequiredSymbol: function (key, text) {
        if (!$('label[for="' + key + '"] span').length) {
            $('label[for="' + key + '"]').html(text + "<span class='required-field'>*</span>");
        }
    },

    refreshRVMedia: function (el) {
        $(el).find('.attachment-details a').html("");
        Impiger.initMediaIntegrate();
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
                        $(document).find("#" + data.targetelm).loadViewDataFromJSON(res);
                        $(document).find("#" + data.targetelm).modal('toggle');
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
        $(document).on('click', 'button[name="submit"]', function (event) {
            crudUtils.formEl.valid();
            let that = $(this);
            if (crudUtils.formEl.find('[name="g-recaptcha-response"]').length) {
                if (!crudUtils.formEl.find('[name="g-recaptcha-response"]').val()) {
                    Impiger.showError('Captcha is required');
                    CustomScript.enableButton(that);
                    event.preventDefault();
                    return false;
                }
            }
            if (crudUtils.formEl.find('.image-data').length) {
                crudUtils.formEl.find('.image-data').each(function () {
                    if ($(this).val()) {
                        var filePath = $(this).val();
                        var nameAttr = $(this).attr('name');
                        var allowedExtensions = /(\.jpg|\.jpeg|\.png|\.gif)$/i;
                        if (!allowedExtensions.exec(filePath)) {
                            Impiger.showError('Please upload ' + nameAttr + ' having extensions .jpeg/.jpg/.png/.gif only.');
                            CustomScript.enableButton(that);
                            event.preventDefault();
                            return false;
                        }
                    }
                })
            }
        });
    }
};

$(document).ready(function () {
    crudUtils.init();
})


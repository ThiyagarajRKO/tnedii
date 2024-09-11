"use strict";

$.fn.customElementIncrementer = function (options) {
  let widget = this;
  widget.config = {};
  widget.defOptions = {
    addBtnSelector: '.add_row_btn',
    removeBtnSelector: '.remove_row_btn',
    addBtnInnerSelector: '.add_inner_row_btn',
    removeBtnInnerSelector: '.remove_inner_row_btn',
    listBlockSelector: '.custom_list_block',
    originRowTemplateId: 'custom_row_template',
    listInnerBlockSelector: '.custom_list_inner_block',
    originInnerRowTemplateId: 'custom_inner_row_template',
    titleNoSelector: '.title_no',
    maxRowLength: false,
    tableRowWise: false,
    isDefaultDate: false,
    returnErrorMsg: false,
    errorMsgObj: {},
    data: [],
    deleteCallback: function () { },
    addRowCallback: function (elm) { },
    initContactAddressMap: false,
    validationRequired: false,
    requiredFields: [],
    triggerSelect2Component: false
  };

  widget.init = function (options) {
    let widget = this;
    widget.config = $.extend({}, widget.defOptions, options);
    widget.config.originRowTemplateSelector = (widget.config.originRowTemplateClass) ? "." + widget.config.originRowTemplateClass + ":first" : "#" + widget.config.originRowTemplateId;
    widget.config.listBlockSelector = widget.find(widget.config.listBlockSelector);
    widget.addRowTemplateOriginClass();
    widget.loadRowTemplate();
    widget.bindEvents();
    widget.resetValidation();

    if (widget.config.innerRowTemplateCallback) {
      setTimeout(function () {
        widget.config.innerRowTemplateCallback();
      }, 300)
    }
  }

  widget.bindEvents = function () {
    let widget = this;
    widget.on('click', widget.config.addBtnSelector, widget.addRowTemplate.bind(this));
    widget.on('click', widget.config.removeBtnSelector, widget.removeRowTemplate.bind(this));
    widget.on('click', widget.config.addBtnInnerSelector, widget.addInnerRowTemplate.bind(this));
    widget.on('click', widget.config.removeBtnInnerSelector, widget.removeInnerRowTemplate.bind(this));
  }

  widget.addRowTemplateOriginClass = function () {
    let widget = this;
    widget.find(widget.config.originRowTemplateSelector).attr('data-row-index', 0);

    if (!widget.find(widget.config.originRowTemplateSelector).hasClass(widget.config.originRowTemplateId)) {
      widget.find(widget.config.originRowTemplateSelector).addClass(widget.config.originRowTemplateId)
    }
    widget.config.rowTemplateClass = (widget.config.originRowTemplateClass) ? widget.config.originRowTemplateClass : widget.config.originRowTemplateId;
  }

  widget.addInnerRowTemplateOriginClass = function () {
    let widget = this;
    widget.find(widget.config.originInnerRowTemplateSelector).attr('data-row-index', 0);

    if (!widget.find(widget.config.originInnerRowTemplateSelector).hasClass(widget.config.originInnerRowTemplateId)) {
      widget.find(widget.config.originInnerRowTemplateSelector).addClass(widget.config.originInnerRowTemplateId)
    }
    widget.config.rowInnerTemplateClass = widget.config.originInnerRowTemplateId;
  }

  widget.loadRowTemplate = function (data = null) {
    widget.config.data = data || widget.config.data;
    if (!CommonUtils.isValidArray(widget.config.data)) {
      setTimeout(function() {
        CustomScript.initCustomSelect2WithTags();
      }, 300)
      if (widget.config.tableRowWise) {
        widget.find("." + widget.config.rowTemplateClass + ":eq(0)").find(widget.config.removeBtnSelector).hide();
      }
      widget.config.addRowCallback(widget.find("." + widget.config.rowTemplateClass + ":eq(0)"));
      return false;
    }

    if(widget.config.data.length == 1) {
      if (widget.config.tableRowWise) {
        widget.find("." + widget.config.rowTemplateClass + ":eq(0)").find(widget.config.removeBtnSelector).hide();
      }
    }

    for (let i = 0; i < widget.config.data.length; i++) {
      if (i > 0) {
        widget.addRowTemplate('', true);
      } else {
        widget.find("." + widget.config.rowTemplateClass + ":eq(" + i + ")").find('.kt-heading .intakeCount').text(CommonUtils.convertToRoman(i + 1));
        setTimeout(function(){
          widget.config.addRowCallback(widget.find("." + widget.config.rowTemplateClass + ":eq(" + i + ")"));

        }, 300)
      }

      let rowTemp = widget.find("." + widget.config.rowTemplateClass + ":eq(" + i + ")");
      rowTemp.loadDataFromJSON(widget.config.data[i], widget.config.isDefaultDate);

      if (widget.config.triggerSelect2Component && widget.config.data[i]) {
        rowTemp.find('select').trigger('change');
      }

      if ($.isFunction(widget.config.rowCallback)) {
        widget.config.rowCallback(rowTemp, widget.config.data[i]);
      }
    }
  }

  widget.reloadRowTemplate = function (reqRowCount) {
    reqRowCount = parseInt(reqRowCount);
    let lastRowEl = widget.find("." + widget.config.rowTemplateClass + ":last");
    let availRowCnt = parseInt($(lastRowEl).attr('data-row-index')) + 1;

    if (availRowCnt > reqRowCount) {
      if (reqRowCount == 0) {
        $(widget.config.listBlockSelector).hide();
      } else {
        for (let i = availRowCnt; i >= reqRowCount; i--) {
          let rowEl = widget.find("." + widget.config.rowTemplateClass + ":eq(" + i + ")");
          widget.removeRowTemplate(rowEl.find('.row:first'));
        }
      }

    } else {
      for (let i = availRowCnt; i < reqRowCount; i++) {
        widget.addRowTemplate('', true);
        if (widget.config.data[i]) {
          let rowEl = widget.find("." + widget.config.rowTemplateClass + ":eq(" + i + ")");
          rowEl.loadDataFromJSON(widget.config.data[i], widget.config.isDefaultDate)

          if ($.isFunction(widget.config.rowCallback)) {
            widget.config.rowCallback(rowEl, widget.config.data[i]);
          }
        }
      }
    }
  }

  widget.loadInnerRowTemplate = function (data = null) {
    widget.config.data = data || widget.config.data;
    if (!CommonUtils.isValidArray(widget.config.data)) {
      return false;
    }

    for (let i = 0; i < widget.config.data.length; i++) {
      widget.addInnerRowTemplate('', true);
      widget.find("." + widget.config.rowInnerTemplateClass + ":eq(" + i + ")").loadDataFromJSON(widget.config.data[i], widget.config.isDefaultDate)

      if ($.isFunction(widget.config.rowCallback)) {
        widget.config.rowCallback(widget.find("." + widget.config.rowInnerTemplateClass + ":eq(" + i + ")"), widget.config.data[i]);
      }
    }
  }

  widget.hideErrorMsg = function (input, name) {
    if (!widget.config.returnErrorMsg) {
      return false;
    }

    if (widget.config.errorMsgObj[name]) {
      widget.config.errorMsgObj[name] = '';
    }

    input.parents('.form-group').find('.error_msg').hide();
    input.parents('.form-group').find('.error_msg').text('');
  }

  widget.showErrorMsg = function (input, name) {
    if (!widget.config.returnErrorMsg) {
      return false;
    }

    if (widget.config.errorMsgObj[name]) {
      input.parents('.form-group').find('.error_msg').text(widget.config.errorMsgObj[name]);
      input.parents('.form-group').find('.error_msg').show();
    }
  }

  widget.resetAllErrorMsg = function (rowElm) {
    if (!widget.config.returnErrorMsg) {
      return false;
    }

    $(rowElm).find('.error_msg').hide();
    $(rowElm).find('.error_msg').text('');
  }

  widget.validateRequiredFields = function (validateFields = []) {
    let widget = this;
    let rowElm = widget.find("." + widget.config.rowTemplateClass);
    let valid = true;
    widget.config.errorMsgObj = {};

    if (!widget.config.validationRequired) {
      return valid;
    }

    validateFields = (CommonUtils.isValidArray(validateFields)) ? validateFields : widget.config.requiredFields;

    if (CommonUtils.isValidArray(validateFields)) {
      $.each(validateFields, function (key, value) {
        $(rowElm).each(function () {
          let rowValid = false;
          let name = (value.name) ? value.name : value;
          let input = $(this).find('[name="' + name + '"]');
          rowValid = widget.checkValidation(input, value, $(this));
          let tagName = input.prop("tagName");

          if (!rowValid) {
            if (tagName == 'SELECT') {
              input.next().addClass('error_border');
            } else {
              input.addClass('error_border');
            }

            widget.showErrorMsg(input, name);;

            if (valid) {
              valid = false;
            }
          } else {
            if (tagName == 'SELECT') {
              input.next().removeClass('error_border');
            } else {
              input.removeClass('error_border');
            }

            widget.hideErrorMsg(input, name);
          }
        });
      });
    }

    return valid;
  }

  widget.resetValidation = function () {
    let widget = this;
    let rowElm = widget.find("." + widget.config.rowTemplateClass);
    if (!widget.config.validationRequired) {
      return false;
    }

    if (CommonUtils.isValidArray(widget.config.requiredFields)) {
      $.each(widget.config.requiredFields, function (key, value) {
        let name = (value.name) ? value.name : value;
        $(widget.config.listBlockSelector).on('change', '[name="' + name + '"]', function () {
          widget.validateRequiredFields([value]);
        })
      });
    }
  }

  widget.addRowTemplate = function (e, onload = false) {
    let widget = this;
    let lastRowEl = widget.find("." + widget.config.rowTemplateClass + ":last");

    if (onload == false && !widget.validateRequiredFields()) {
      return false;
    }

    let rowIndex = parseInt($(lastRowEl).attr('data-row-index'));
    let newIndex = rowIndex + 1;
    widget.checkValidRowLength(true);
    let firstRowEl = widget.find("." + widget.config.rowTemplateClass + ":first");

    if (widget.config.tableRowWise) {
      widget.find("." + widget.config.rowTemplateClass + ":eq(" + rowIndex + ")").find(widget.config.addBtnSelector).hide();
      widget.find("." + widget.config.rowTemplateClass + ":eq(" + rowIndex + ")").find(widget.config.removeBtnSelector).show();
    }

    if (firstRowEl.find('select').data('select2')) {
      firstRowEl.find('select').select2('destroy');
    }

    if (firstRowEl.is('.custom-accordion')) {
      setTimeout(function(){
        // firstRowEl.accordion("destroy"); 
      }, 100);
    }

    let rowTemplate = firstRowEl.clone();

    if(!widget.config.originRowTemplateClass) {
      rowTemplate.attr("id", widget.config.originRowTemplateId + newIndex);
    }


    rowTemplate.find('.kt-heading ' + widget.config.titleNoSelector).text(newIndex + 1);
    rowTemplate.attr("data-row-index", newIndex);

    if (widget.config.tableRowWise) {
      rowTemplate.find(widget.config.addBtnSelector).show();
    } else {
      rowTemplate.find(widget.config.removeBtnSelector).show();
    }

    $(rowTemplate).find("input,textarea,select,a,.inner_incrementer").each(function () {
      if ($(this).attr('id')) {
        $(this).attr('id', $(this).attr('id') + '_' + newIndex);
      }

      let attr = $(this).attr('name');
      if (attr) {
        if ($(this).attr('id')) {
          $(this).attr('id', attr.replace(rowIndex, newIndex));
        }

        $(this).attr('name', attr.replace(rowIndex, newIndex));
      }

      if ($(this).attr('ignore_reset_value') != 'true') {
        $(this).val('');
      }

      if ($(this).getType() == "select") {
        $(this).val('');
        // CustomScript.initCustomSelect2($(this));
      } else if ($(this).hasClass('datepicker')) {
        $(this).bootstrapDP('update')
      }

    });

    rowTemplate.find('.kt-heading .intakeCount').text(CommonUtils.convertToRoman(newIndex + 1))
    if (!rowTemplate.find('.kt-heading .intakeCount').length) {
      rowTemplate.find('.semester-heading .semCount').text(CommonUtils.convertToRoman(newIndex + 1))
    }
    rowTemplate.appendTo(widget.config.listBlockSelector);

    if (rowTemplate.find('select').length > 0) {
      CustomScript.initCustomSelect2($(firstRowEl).find("select"));
      CustomScript.initCustomSelect2($(rowTemplate).find("select"));
    }
    setTimeout(function() {
      CustomScript.initCustomSelect2WithTags();
    }, 300)

    widget.refreshTouchSpin(rowTemplate);
    widget.refreshRVMedia(rowTemplate);

    if (rowTemplate.find('.date_picker').length) {
      rowTemplate.find('.date_picker').bootstrapDP('update');
    }

    // if (firstRowEl.is('.custom-accordion')) {
    //   firstRowEl.accordion({
    //     active: 0,
    //     collapsible: true
    // });
    // }

    widget.config.addRowCallback(rowTemplate);

  }

  widget.refreshRVMedia = function (el) {
      $(el).find('.attachment-details a').html("");
      Impiger.initMediaIntegrate();
  }

  widget.refreshTouchSpin = function (rowTemplate) {
    if (rowTemplate.find('.bootstrap-touchspin-vertical-btn').length > 0) {
      let touchSpinConfig = {
        'kt_touchspin': {
          min: 0
        },
        'kt_touchspin_week': {
          max: 4
        },
        'kt_touchspin_month': {
          max: 11
        }
      }

      let touchSpinOptions = {
        buttondown_class: 'btn btn-secondary',
        buttonup_class: 'btn btn-secondary',
        verticalbuttons: true,
        verticalup: '<i class="fa fa-angle-up"></i>',
        verticaldown: '<i class="fa fa-angle-down"></i>',
        min: 0,
        mousewheel: false
      }
      $(rowTemplate.find('.kt_touchspin')).TouchSpin($.extend(touchSpinOptions, touchSpinConfig.kt_touchspin));
      $(rowTemplate.find('.kt_touchspin_week')).TouchSpin($.extend(touchSpinOptions, touchSpinConfig.kt_touchspin_week));
      $(rowTemplate.find('.kt_touchspin_month')).TouchSpin($.extend(touchSpinOptions, touchSpinConfig.kt_touchspin_month));
    }
  }

  widget.removeRowTemplate = function (e) {
    let widget = this;
    let target = e.target || e;
    let delRowEl = $(target).parents("." + widget.config.rowTemplateClass);
    delRowEl.remove();
    widget.rearrangeRowIndex();
    widget.checkValidRowLength();

    if (widget.config.tableRowWise) {
      let lastRowEl = widget.find("." + widget.config.rowTemplateClass + ":last");
      let lastRowIndex = parseInt($(lastRowEl).attr('data-row-index'));
      widget.find("." + widget.config.rowTemplateClass + ":eq(" + lastRowIndex + ")").find(widget.config.addBtnSelector).show();

      if (lastRowIndex == 0) {
        widget.find("." + widget.config.rowTemplateClass + ":eq(" + lastRowIndex + ")").find(widget.config.removeBtnSelector).hide();
      }
    }

    widget.config.deleteCallback();
  }

  // Inner Template
  widget.addInnerRowTemplate = function (e, onload = false) {
    let widget = this;
    let lastRowEl = widget.find("." + widget.config.rowInnerTemplateClass + ":last");

    if (onload == false && !widget.validateRequiredFields()) {
      return false;
    }

    let rowIndex = parseInt($(lastRowEl).attr('data-row-index'));
    let newIndex = rowIndex + 1;
    widget.checkValidRowLength(true);

    if (onload == false && $(widget.config.listInnerBlockSelector).is(':hidden')) {
      $(widget.config.listInnerBlockSelector).show();
    } else {
      let rowTemplate = widget.find(widget.config.originInnerRowTemplateSelector).clone();
      rowTemplate.attr("id", widget.config.originInnerRowTemplateId + newIndex);
      rowTemplate.find('.kt-heading ' + widget.config.titleNoSelector).text(newIndex + 1);
      rowTemplate.attr("data-row-index", newIndex);

      $(rowTemplate).find("input,textarea,select,a").each(function () {
        if ($(this).attr('id')) {
          $(this).attr('id', $(this).attr('id') + '_' + newIndex);
        }

        let attr = $(this).attr('name');
        if (attr) {
          $(this).attr('id', attr.replace(rowIndex, newIndex));
          $(this).attr('name', attr.replace(rowIndex, newIndex));
        }
        if ($(this).attr('ignore_reset_value') != 'true') {
          $(this).val('');
        }
        if ($(this).getType() == "select") {
          $(this).val('');
          $(this).select2({ placeholder: "Select" });
        } else if ($(this).hasClass('datepicker')) {
          $(this).bootstrapDP('update')
        }

      });


      rowTemplate.appendTo(widget.config.listInnerBlockSelector);

      if (rowTemplate.find('select').length > 0) {
        $(rowTemplate.find('select')).select2({ placeholder: "Select" });
      }

      if (rowTemplate.find('.kt_touchspin').length > 0) {
        $(rowTemplate.find('.kt_touchspin')).TouchSpin({
          buttondown_class: 'btn btn-secondary',
          buttonup_class: 'btn btn-secondary',
          verticalbuttons: true,
          verticalup: '<i class="fa fa-angle-up"></i>',
          verticaldown: '<i class="fa fa-angle-down"></i>',
          min: 0,
          mousewheel: false
        });
      }



      if (rowTemplate.find('.date_picker').length) {
        rowTemplate.find('.date_picker').bootstrapDP('update');
      }
      if (onload == true) {
        $(widget.config.listInnerBlockSelector).show();
      }
    }
  }

  widget.removeInnerRowTemplate = function (e) {
    let widget = this;
    let target = e.target;
    let delRowEl = $(target).parents("." + widget.config.rowInnerTemplateClass);
    if (!delRowEl.length) {
      delRowEl = widget.find("." + widget.config.rowInnerTemplateClass + ":last");
    }
    if ($(widget.config.listInnerBlockSelector).find("." + widget.config.rowInnerTemplateClass).length > 1) {
      delRowEl.remove();
    } else {
      $(widget.config.listInnerBlockSelector).hide();
    }
    widget.rearrangeRowIndex();
    widget.checkValidRowLength();

    if (widget.config.tableRowWise) {
      let lastRowEl = widget.find("." + widget.config.rowInnerTemplateClass + ":last");
      let lastRowIndex = parseInt($(lastRowEl).attr('data-row-index'));
      widget.find("." + widget.config.rowInnerTemplateClass + ":eq(" + lastRowIndex + ")").find(widget.config.addBtnSelector).show();

      if (lastRowIndex == 0) {
        widget.find("." + widget.config.rowInnerTemplateClass + ":eq(" + lastRowIndex + ")").find(widget.config.removeBtnSelector).hide();
      }
    }

    widget.config.deleteCallback();
  }

  widget.rearrangeRowIndex = function () {
    let widget = this;
    if (widget.find("." + widget.config.rowTemplateClass).length > 0) {
      let detailIndex = 0;
      widget.find("." + widget.config.rowTemplateClass).each(function () {
        $(this).attr('data-row-index', detailIndex);
        $(this).find('.kt-heading ' + widget.config.titleNoSelector).text(detailIndex + 1);
        detailIndex = (detailIndex > 0) ? detailIndex : '';
        if(!widget.config.originRowTemplateClass) {
          $(this).attr('id', widget.config.originRowTemplateId + "_" + detailIndex);
        }
        detailIndex++;
      });
    }
  }

  widget.checkValidRowLength = function (isAdd = false) {
    let widget = this;
    let lastRowEl = widget.find("." + widget.config.rowTemplateClass + ":last");
    let rowIndex = parseInt($(lastRowEl).attr('data-row-index'));
    let newIndex = rowIndex + 1;
    if (widget.config.maxRowLength) {
      let rowLength = (isAdd) ? newIndex + 1 : newIndex;
      if (widget.config.maxRowLength == rowLength) {
        widget.find(widget.config.addBtnSelector).hide();
      } else {
        widget.find(widget.config.addBtnSelector).show();
      }
    }
  }

  widget.setErrorMsg = function (valid, attrName, message) {
    if (!widget.config.returnErrorMsg) {
      return false;
    }

    if (!valid) {
      widget.config.errorMsgObj[attrName] = message;
    }
  }

  widget.checkValidation = function (input, value, rowElm) {
    let widget = this;
    let rowValid = false;
    let attrName = $(input).attr('name');

    if (value.email) {
      rowValid = (!input.val() || /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/.test(input.val()));
      widget.setErrorMsg(rowValid, attrName, 'Please enter a valid email.');
    } else if (value.url) {
      rowValid = (!input.val() || /^(http|https)?:\/\/[a-zA-Z0-9-\.]+\.[a-z]{2,4}/.test(input.val()));
      widget.setErrorMsg(rowValid, attrName, 'Please enter a valid url.');
    } else if (value.number) {
      rowValid = (!input.val() || /^[0-9]+$/.test(input.val()));
      widget.setErrorMsg(rowValid, attrName, 'Please enter a valid number.');
    } else if (value.greaterThan) {
      let toDate = input.val().replace(/-/g, '/');

      let fromDateInput = $(rowElm).find(value.greaterThan).val();
      rowValid = (value.required) ? input.val() : false;
      widget.setErrorMsg(rowValid, attrName, 'This field is required.');

      if (fromDateInput && input.val()) {
        var stDate = new Date(fromDateInput);
        stDate.setFullYear(fromDateInput.substr(6, 4), (fromDateInput.substr(3, 2) - 1), fromDateInput.substr(0, 2));
        var enDate = new Date(input.val());
        enDate.setFullYear(input.val().substr(6, 4), (input.val().substr(3, 2) - 1), input.val().substr(0, 2));
        if (!/Invalid|NaN/.test(stDate)) {
          rowValid = enDate > stDate;
        }
        else {
          rowValid = isNaN(input.val()) && isNaN(fromDateInput)
            || (Number(input.val()) > Number(fromDateInput));
        }
        widget.setErrorMsg(rowValid, attrName, 'Must be greater than ' + value.erroLabel + '.');
      }
    } else if (value.alphanumericSpecialChar) {
      if (!input.val()) {
        rowValid = false;
        widget.setErrorMsg(rowValid, attrName, 'This field is required.');
      } else {
        rowValid = /^[a-zA-Z0-9-_.,&@#�|/\s]+$/.test(input.val());
        widget.setErrorMsg(rowValid, attrName, 'Please use only letters , numbers & spaces, -, _, .,&,/,#@".');
      }
    } else {
      rowValid = input.val();
      widget.setErrorMsg(rowValid, attrName, 'This field is required.');
    }

    return rowValid;
  }

  widget.getData = function () {
    let widget = this;
    let data = [];
    let valid = true;

    widget.find("." + widget.config.rowTemplateClass).each(function () {
      let rowElm = $(this);
      let rowData = CommonUtils.getFormElementDataToJSON($(this), true);
      if (!$.isEmptyObject(rowData)) {
        if (widget.config.validationRequired && CommonUtils.isValidArray(widget.config.requiredFields)) {
          $.each(widget.config.requiredFields, function (key, value) {
            let name = (value.name) ? value.name : value;
            let input = rowElm.find('[name="' + name + '"]');

            if (!widget.checkValidation(input, value, rowElm)) {
              if (valid) {
                valid = false;
              }
            }
          });

          if (valid) {
            data.push(rowData);
          }
        } else {
          data.push(rowData);
        }
      }
    });

    return data;
  }

  widget.destroy = function () {
    let widget = this;
    widget.find('.error_msg').hide();
    widget.find('.error_msg').text('');
    widget.find('.error_border').removeClass('error_border');
    widget.find("." + widget.config.originRowTemplateId + ":not([id='" + widget.config.originRowTemplateId + "'])").remove();

    $("#" + widget.config.originRowTemplateId).find("input,textarea,select,a").each(function () {
      if ($(this).attr('ignore_reset_value') != 'true') {
        $(this).val('');
      }
    });
    $("#" + widget.config.originRowTemplateId).find(widget.config.addBtnSelector).show();
    $("#" + widget.config.originRowTemplateId).find(widget.config.removeBtnSelector).hide();
  }

  widget.init(options);
  return widget;
}
let inputRestriction = {
    defaultOptions: {
      textMaxLengthRestriction: false,
      fieldInfo: {}
    },
    handleKeyPress: function() {
      let inputRestriction = this;
      $('body').keypress(function(e) {
        if(!e.charCode) {
          return;
        }
        if (inputRestriction.options.textMaxLengthRestriction) {
          let inputElement = e.target;
          let inputFieldId = $(inputElement).attr('data-state-field-id');
          if (inputFieldId) {
            let maxLengthOfField = inputRestriction.getMaxLengthOfField(inputFieldId);
            var selectionStart = $(inputElement).prop('selectionStart');
            var selectionEnd = $(inputElement).prop('selectionEnd');
            let selectedText = $(inputElement).val().substring(selectionStart, selectionEnd);
            let printableText = $(inputElement).val() + String.fromCharCode(e.charCode);
            let textByteLenth = inputRestriction.getByteLengthOfText(printableText);
            if (selectedText) {
              textByteLenth = textByteLenth - inputRestriction.getByteLengthOfText(selectedText);
            }
            if (maxLengthOfField && maxLengthOfField < textByteLenth) {
              e.preventDefault();
              return false;
            }
          }
        }
      })
    },
    cutInUTF8: function(str, n) {
      var encoded = unescape(encodeURIComponent(str)).substr(0, n);
      while (true) {
        try {
          str = decodeURIComponent(escape(encoded));
          return str;
        } catch (e) {
          encoded = encoded.substr(0, encoded.length - 1);
        }
      }
    },
    stripNumbers: function(str) {
      let strippedOutput = str.match(/\d+/);
      if(strippedOutput==null) {
        strippedOutput = "";
      }
      return strippedOutput!=null?strippedOutput:str;
    },
    stripDecimalNumbers: function(str) {
      let strippedOutput = str.match(/[\d.]+/);
      if(strippedOutput==null) {
        strippedOutput = "";
      }
      return strippedOutput!=null?strippedOutput:str;
    },
    handleKeyPaste: function() {
      let inputRestriction = this;
      $('body').bind('input', function(e) {
        let inputElement = e.target;
        let inputValue = $(inputElement).val();
        let originalValue = inputValue;
        if($(inputElement).hasClass('allowNumberOnly')) {
          inputValue = inputRestriction.stripNumbers(inputValue);
        }
        if($(inputElement).hasClass('allowDecimalOnly')) {
          inputValue = inputRestriction.stripDecimalNumbers(inputValue);
        }
        if (inputRestriction.options.textMaxLengthRestriction) {
          let inputFieldId = $(inputElement).attr('data-state-field-id');
          if (inputFieldId) {
            let maxLengthOfField = inputRestriction.getMaxLengthOfField(inputFieldId);
            if (maxLengthOfField) {
              inputValue = inputRestriction.cutInUTF8(inputValue, maxLengthOfField);
            }
          }
        }
        if(originalValue != inputValue) {
          $(inputElement).val(inputValue).trigger('change');
        }
      });
    },
    bindGlobalKeyEvent: function() {
      let inputRestriction = this;
      inputRestriction.handleKeyPress();
      inputRestriction.handleKeyPaste();
    },
    getByteLengthOfText: function(str) {
      var s = str.length;
      for (var i = str.length - 1; i >= 0; i--) {
        var code = str.charCodeAt(i);
        if (code > 0x7f && code <= 0x7ff) s++;
        else if (code > 0x7ff && code <= 0xffff) s += 2;
        if (code >= 0xDC00 && code <= 0xDFFF) i--; //trail surrogate
      }
      return s;
    },
    initiate: function(options) {
      this.options = $.extend({}, this.defaultOptions, options);
      this.bindGlobalKeyEvent();
    },
    getMaxLengthOfField: function(fieldName) {
      let maxLength = 0;
      if (this.options.fieldInfo[fieldName]) {
        let fieldInfo = this.options.fieldInfo[fieldName];
        maxLength = fieldInfo['maxLength'];
      }
      return maxLength;
    }
  }
  
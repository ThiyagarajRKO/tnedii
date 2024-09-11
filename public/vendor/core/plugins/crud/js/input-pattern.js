(function($) {

  $.caretTo = function(el, index) {
    if (el.createTextRange) {
      var range = el.createTextRange();
      range.move("character", index);
      range.select();
    } else if (el.selectionStart != null) {
      el.focus();
      el.setSelectionRange(index, index);
    }
  };

  $.fn.getCursorPosition = function() {
    var input = this.get(0);
    if (!input) return; // No (input) element found
    if ('selectionStart' in input) {
      // Standard-compliant browsers
      return input.selectionStart;
    } else if (document.selection) {
      // IE
      input.focus();
      var sel = document.selection.createRange();
      var selLen = document.selection.createRange().text.length;
      sel.moveStart('character', -input.value.length);
      return sel.text.length - selLen;
    }
  }

  let replaceStringAt = function(stringValue) {
    var rlen = arguments[3] == null ? 1 : arguments[3];
    return stringValue.substring(0, arguments[1]) + arguments[2] + stringValue.substring(arguments[1] + rlen);
  }

  $.fn.inputPattern = function(custom_settings) {
    let defaultSettings = {
      "allowedInputPatterns": [], // set allowed input patterns in order they are expected
      "autoTypeCharacters": [],
      "ignoreKeyCodes": [],
      "defaultTypingCharacter": "",
      "initialValue": "",
      "placeHolder": ""
    }
    let inputSettings = $.extend(defaultSettings, custom_settings);
    let currentElement = this;
    // $(this).val(inputSettings['initialValue']);
    $('html').on('keydown', function(event) {
      if ($(event.target).is(currentElement)) {
        if (event.which == 8 || event.which == 46 || event.which == 86) {
          var e = $.Event('keypress');
          e.which = event.which;
          $(currentElement).trigger(e);
          event.preventDefault();
          return false;
        }
      }
    });
    $(this).keypress(function(event) {
      let typedCharCode = !event.charCode ? event.which : event.charCode;
      let currentCursorPosition = $(this).getCursorPosition();
      let inputElement = this;
      if (inputSettings['ignoreKeyCodes'].indexOf(typedCharCode) !== -1) {
        return true;
      }
      event.preventDefault();
      if (typedCharCode == 46) { //Delete
        if (currentCursorPosition < defaultSettings['allowedInputPatterns'].length) {
          let changeTextPosition = currentCursorPosition;
          let currentPositionPlaceHolderText = defaultSettings['placeHolder'][changeTextPosition];
          let newValue = replaceStringAt($(this).val(), changeTextPosition, currentPositionPlaceHolderText);
          setTimeout(function() {
            $(inputElement).val(newValue);
            $.caretTo(inputElement, currentCursorPosition + 1);
          }, 50);
        }
        return false;
      }
      if (typedCharCode == 8) { //Backspace
        if (currentCursorPosition > 0) {
          let changeTextPosition = currentCursorPosition - 1;
          let currentPositionPlaceHolderText = defaultSettings['placeHolder'][changeTextPosition];
          let newValue = replaceStringAt($(this).val(), changeTextPosition, currentPositionPlaceHolderText);
          setTimeout(function() {
            $(inputElement).val(newValue);
            $.caretTo(inputElement, currentCursorPosition - 1);
          }, 50);
        }
        return false;
      }
      let currentText = $(this).val();
      if (currentCursorPosition >= defaultSettings['allowedInputPatterns'].length) {
        return false;
      }
      let typedCharacter = String.fromCharCode(!event.charCode ? event.which : event.charCode);
      let currentPositionPattern = defaultSettings['allowedInputPatterns'][currentCursorPosition];
      let nextPositionPattern = defaultSettings['allowedInputPatterns'][currentCursorPosition + 1];
      if (new RegExp("[" + currentPositionPattern + "]").test(typedCharacter)) {
        let newValue = replaceStringAt($(this).val(), currentCursorPosition, typedCharacter);
        if ($.inArray(nextPositionPattern, defaultSettings['autoTypeCharacters']) !== -1) {
          var char = newValue[currentCursorPosition + 1];
          if (char != nextPositionPattern) { // Add autotype character with string
            newValue = newValue + nextPositionPattern;
          }
        }
        setTimeout(function() {
          $(inputElement).val(newValue);
          $.caretTo(inputElement, currentCursorPosition + 1);
        }, 50)
      } else if (new RegExp("[" + nextPositionPattern + "]").test(typedCharacter)) {
        let newValue = '';
        if ($.inArray(currentPositionPattern, defaultSettings['autoTypeCharacters']) !== -1) { // Replace the typedCharacter with corresponding position in string
          newValue = replaceStringAt($(this).val(), currentCursorPosition + 1, typedCharacter);
        } else {
          newValue = replaceStringAt($(this).val(), currentCursorPosition, defaultSettings['defaultTypingCharacter']);
          newValue = replaceStringAt(newValue, currentCursorPosition + 1, typedCharacter);
          nextPositionPattern = defaultSettings['allowedInputPatterns'][currentCursorPosition + 2];
          if ($.inArray(nextPositionPattern, defaultSettings['autoTypeCharacters']) !== -1 && typeof(newValue[currentCursorPosition + 2]) == 'undefined') { // Add autotype character with string
            newValue = newValue + nextPositionPattern;
          }
        }
        currentCursorPosition++;
        setTimeout(function() {
          $(inputElement).val(newValue);
          $.caretTo(inputElement, currentCursorPosition + 1);
        }, 50)
      }
      if (nextPositionPattern) {
        for (let i = 0; i < defaultSettings['autoTypeCharacters'].length; i++) {
          let autoTypeCharacter = defaultSettings['autoTypeCharacters'][i];
          if (new RegExp("[" + nextPositionPattern + "]").test(autoTypeCharacter)) {
            let inputElement = this;
            setTimeout(function() {
              $.caretTo(inputElement, currentCursorPosition + 2);
            }, 50)
            break;
          }
        }
      }
      return false;
    });

    $(this).keydown(function(event) {
      let typedCharCode = !event.charCode ? event.which : event.charCode;
      let currentCursorPosition = $(this).getCursorPosition();
      let inputElement = this;
      if (inputSettings['ignoreKeyCodes'].indexOf(typedCharCode) !== -1) {
        return true;
      }
      if (typedCharCode == 37) {
        if (currentCursorPosition > 0) {
          let changeTextPosition = currentCursorPosition - 1;
          setTimeout(function() {
            $.caretTo(inputElement, currentCursorPosition - 1);
          }, 50);
        }
        return false;
      }
      if (typedCharCode == 39) {
        if (currentCursorPosition >= 0) {
          let changeTextPosition = currentCursorPosition + 1;
          setTimeout(function() {
            $.caretTo(inputElement, currentCursorPosition + 1);
          }, 50);
        }
        return false;
      }
    });
  }
})(jQuery);

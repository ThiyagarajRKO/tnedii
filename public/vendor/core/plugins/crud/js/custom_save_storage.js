'use strict';

(function ($) {
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

    $.fn.loadViewDataFromJSON = function (data) {
        let $form = $(this)
        if (typeof (data) == "string" || $.isEmptyObject(data)) {
            return false;
        }

        $.each(data, function (key, value) {
            let $elem = $('[name="' + key + '"]', $form);
            $elem.text(value);
        });
    };

    $.fn.resetSaveStorage = function (pathName) {
        pathName = (pathName) ? pathName : window.location.pathname;
        pathName = pathName.replace(/\//g, "");
        let key = 'imp_ls_' + $(this).attr('id') + '_' + pathName + '_';
        localStorage.removeItem(key);
        localStorage.removeItem(key+'old');
    };

    $.fn.saveStorage = function (options) {

        if (typeof Storage !== "undefined") {
            let pathName = window.location.pathname.replace(/\//g, "");

            let form = $(this),
                key = 'imp_ls_' + $(this).attr('id') + '_' + pathName + '_',
                defaults = {
                    exclude: []
                };

            let opts = $.extend({}, defaults, options);

            let excludeInputType = function () {
                let inputType = '';

                $.each(opts.exclude, function (k, v) {
                    inputType += 'input[type=' + v + '],'
                });

                return inputType;
            };

            form.on('change keyup', ':input', function (e, wasTriggered) {
                let serializeForm = {};

                if (typeof (opts.getDataCallBack) == 'function') {
                    serializeForm = opts.getDataCallBack(form);
                } else {
                    serializeForm = form.getFormDataToJSON();
                }

                localStorage.setItem(key, JSON.stringify(serializeForm));
            });

            let initApp = function () {
                if (localStorage.getItem(key)) {
                    let data = JSON.parse(localStorage.getItem(key));
                    localStorage.setItem(key+'old', JSON.stringify(data));

                    if (typeof (opts.loadDataCallback) == 'function') {
                        opts.loadDataCallback(data);
                    } else {
                        form.loadDataFromJSON(data, true, opts.exclude);
                    }
                }
            };

            form.submit(function () {
                if(!opts.preventClear) {
                    localStorage.removeItem(key);
                    localStorage.removeItem(key+'old');
                } 
            });

            initApp();
        }
        else {
            console.error('Sorry! No web storage support.')
        }
    };
})(jQuery);

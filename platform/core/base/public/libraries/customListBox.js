$.fn.customListBox = function (options) {
    let widget = this;
    widget.config = {};
    widget.defOptions = {
        sortableOrigin: '.draggable-left',
        sortableTarget: '.draggable-right',
        nameKey: 'roles[]',
        multiple: false,
        isEnableToolbarBtns: true,
        multiNode: false
    };

    widget.init = function (options) {
        let widget = this;
        widget.config = $.extend({}, widget.defOptions, options);
        if (widget.config.isEnableToolbarBtns) {
            widget.find('.listboxToolBarBtns .btn-grp').removeClass('hidden');
        }
        widget.initSortable();
        widget.bindEvents();
    }

    widget.bindEvents = function () {
        let widget = this;
        widget.find(".draggable-left").on("sortbeforestop", function (event, ui) {
            if (widget.config.multiNode) {
                ui.item.appendTo(widget.find('.draggable-right.mainSortable'));
                let elId = ui.item.attr('id');
                $("#" + elId).find(".hrv-checkbox").each(function (index, el) {
                    $(el).attr('checked', true);
                })
            } else {
                let el = ui.item;
                $(el).find('input[type="hidden"]').attr('name', widget.config.nameKey)
            }
        });

        widget.find(".draggable-right").on("sortbeforestop", function (event, ui) {
            if (widget.config.multiNode) {
                ui.item.appendTo(widget.find('.draggable-left.mainSortable'));
                let elId = ui.item.attr('id');
                $("#" + elId).find(".hrv-checkbox").each(function (index, el) {
                    $(el).attr('checked', false);
                })
            } else {
                let el = ui.item;
                $(el).find('input[type="hidden"]').attr('name', '')
            }
        });

        widget.find('.listBoxSearchLeft').keyup(function () {
            var valThis = $(this).val();
            widget.find(".draggable-left").find('.ui-sortable-handle').each(function () {
                var text = $(this).text().toLowerCase();
                (text.indexOf(valThis.toLowerCase()) != -1) ? $(this).show() : $(this).hide();
            });
        });

        widget.find('.listBoxSearchRight').keyup(function () {
            var valThis = $(this).val();
            widget.find(".draggable-right").find('.ui-sortable-handle').each(function () {
                var text = $(this).text().toLowerCase();
                (text.indexOf(valThis.toLowerCase()) != -1) ? $(this).show() : $(this).hide();
            });
        });

        widget.find("ul li:not(#mainNode)").on("click", widget.toggleSelection);
        widget.find(".transferAllTo").on("click", widget.transferAllTo);
        widget.find(".transferTo").on("click", widget.transferTo);
        widget.find(".transferFrom").on("click", widget.transferFrom);
        widget.find(".transferAllFrom").on("click", widget.transferAllFrom);
    }

    widget.initSortable = function () {
        widget.find(widget.config.sortableOrigin + "," + widget.config.sortableTarget).sortable({
            connectWith: widget.find(".connected-sortable"),
            stack: widget.find(".connected-sortable ul"),
            forceHelperSize: true,
            forcePlaceholderSize: true,
            scroll: false,
            start: function (e, info) {
                if (!widget.config.multiNode) {
                    info.item.siblings(".selected").not(".ui-sortable-placeholder").appendTo(info.item);
                }
            },
            stop: function (e, info) {
                if (!widget.config.multiNode) {
                    info.item.after(info.item.find("li"))
                }
            }
        });
    }

    widget.toggleSelection = function (e) {
        let mainSortableCls = (widget.config.multiNode) ? ".mainSortable": "";
        if (widget.config.isEnableToolbarBtns) {
            if (!widget.config.multiple) {
                let listBox = $(this).parents('ul'+mainSortableCls+':first');
                listBox.find('li.selected').removeClass('selected');
            }

            $(this).toggleClass('selected');
        }
    }

    widget.transferTo = function (e) {
        if (widget.config.multiNode) {
            widget.find('ul.draggable-left li.selected').appendTo(widget.find('ul.draggable-right.mainSortable'));
            widget.find('ul.draggable-right li.selected .hrv-checkbox').attr('checked', true);
            setTimeout(function () {
                widget.find('ul.draggable-right li.selected').removeClass('selected');
            }, 300);
        } else {
            widget.find('ul.draggable-left li.selected').appendTo(widget.find('ul.draggable-right'));
            widget.find('ul.draggable-right li.selected').removeClass('selected');
        }
        widget.updateNameAttributeValue();
    }

    widget.transferFrom = function (e) {
        if (widget.config.multiNode) {
            widget.find('ul.draggable-right li.selected').appendTo(widget.find('ul.draggable-left.mainSortable'));
            widget.find('ul.draggable-left li.selected .hrv-checkbox').attr('checked', true);
            setTimeout(function () {
                widget.find('ul.draggable-left li.selected').removeClass('selected');
            }, 300);
        } else {
            widget.find('ul.draggable-right li.selected').appendTo(widget.find('ul.draggable-left'));
            widget.find('ul.draggable-left li.selected').removeClass('selected');
        }

        widget.resetNameAttributeValue();
    }

    widget.transferAllTo = function (e) {
        let mainSortableCls = (widget.config.multiNode) ? ".mainSortable": "";
        widget.find('ul.draggable-left'+mainSortableCls+' li.ui-sortable-handle').appendTo(widget.find('ul.draggable-right'+mainSortableCls));
        widget.find('ul.draggable-right'+mainSortableCls+' li.selected').removeClass('selected');
        widget.updateNameAttributeValue();
        widget.find('ul.draggable-right .hrv-checkbox').attr('checked', true);
    }

    widget.transferAllFrom = function (e) {
        let mainSortableCls = (widget.config.multiNode) ? ".mainSortable": "";
        widget.find('ul.draggable-right'+mainSortableCls+' li.ui-sortable-handle').appendTo(widget.find('ul.draggable-left'+mainSortableCls));
        widget.find('ul.draggable-left'+mainSortableCls+' li.selected').removeClass('selected');
        widget.resetNameAttributeValue();
        widget.find('ul.draggable-left .hrv-checkbox').attr('checked', false);
    }

    widget.updateNameAttributeValue = function (e) {
        widget.find('ul.draggable-right input[type="hidden"][name!="' + widget.config.nameKey + '"]').each(function () {
            $(this).attr('name', widget.config.nameKey);
        });
    }

    widget.resetNameAttributeValue = function (e) {
        widget.find('ul.draggable-left input[type="hidden"][name="' + widget.config.nameKey + '"]').each(function () {
            $(this).attr('name', '');
        })
    }

    widget.init(options);
    return widget;
};
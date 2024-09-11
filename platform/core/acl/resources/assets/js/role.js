class Role {
    init() {
        $('#auto-checkboxes li').tree({
            onCheck: {
                node: 'expand'
            },
            onUncheck: {
                node: 'expand'
            },
            dnd: false,
            selectable: false
        });

        $('#mainNode .checker').change(event =>  {
            let _self = $(event.currentTarget);
            let set = _self.attr('data-set');
            let checked = _self.is(':checked');
            $(set).each((index, el) => {
                if (checked) {
                    $(el).attr('checked', true);
                } else {
                    $(el).attr('checked', false);
                }
            });
        });
        /* @customized Sabari Shankar.Parthiban begin */
        $('#listBoxContainer').customListBox({
            multiNode: true
        });
        /* @customized Sabari Shankar.Parthiban end */
    }
}

$(document).ready(() => {
    new Role().init();
});

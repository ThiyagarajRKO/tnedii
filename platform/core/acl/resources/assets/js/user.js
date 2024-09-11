class User {
    init() {
        $('#auto-checkboxes li').tree({
//            onCheck: {
//                node: 'expand'
//            },
//            onUncheck: {
//                node: 'expand'
//            },
            dnd: false,
            selectable: false
        });

        $('#mainNode .checker').change(event => {
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
        //Sortable
        $('#accessListBoxContainer').customListBox({
            multiNode: true,
            nameAttr: true,
            nameKey: 'entity_ids[]'
        });

    }

    getAssignedRoleIds() {
        return JSON.stringify($("input[name='role_id[]']").map(function (idx, ele) {
            return $(ele).val();
        }).get());
    }
}



$(document).ready(() => {
    new User().init();
    let roleIdOnPageLoad = new User().getAssignedRoleIds();

    $('.nav-tabs a[data-toggle="tab"]').on('show.bs.tab', function (e) {
        let getAssignedRoleIds = new User().getAssignedRoleIds();

        if (getAssignedRoleIds != roleIdOnPageLoad) {
            CustomScript.showInfoMessage({'content' : 'Please save changes and proceed.'});
            // alert("Please save changes and proceed.");
            e.preventDefault();
            return false;
        }
    });

});

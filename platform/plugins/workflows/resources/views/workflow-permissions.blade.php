@php($i=0)
@foreach($transitions as $k => $transition)
<div class="listBoxContainer custom-accordion" id="listBoxContainer{{ $k }}">
    <h3 class="help-block">
        <span class="field-header"> {{ucfirst(Arr::get($transitionTitleMap,$k))}} </span> - &nbsp;&nbsp;&nbsp;<span class="field-mini-text">{{$transition['from_state']}}</span> <i class="fa fa-arrow-circle-right" aria-hidden="true"></i> <span class="field-mini-text">{{$transition['to_state']}}</span>
    </h3>

    <div>
        <input type="hidden" name="wf_transition_id" value="{{ $k }}">
        @include('plugins/workflows::user-specific-permission')

        <!-- @if($workflow->permission_specific_to == WORKFLOW_PERMISSION_SPECIFIC_TO_USER) -->
        <!-- @include('plugins/workflows::user-specific-permission') -->
        <!-- @else
        @include('plugins/workflows::role-specific-permission')
        @endif -->

        @if(WORKFLOW_ATTACHMENT_CONFIG)
        @include('plugins/workflows::transitions-attachment')
        @endif
    </div>
</div>
<br />
@php($i++)
@endforeach


<script>
    var specificToUser = "{{WORKFLOW_PERMISSION_SPECIFIC_TO_USER}}";
    var specificToRole = "{{WORKFLOW_PERMISSION_SPECIFIC_TO_ROLE}}";
    var availableRoles = {!!json_encode($availableRoles) !!};
    @php($i = 1)

    $(document).ready(function() {
        $(document).ajaxSend(function() {
            $("#custom-ajax-loader").show();
        });
        $(document).ajaxComplete(function() {
            $("#custom-ajax-loader").hide();
        });

        $('.searchState').keyup(function() {
            var valThis = $(this).val().toLowerCase();

            if (!valThis) {
                $('.listBoxContainer').accordion("option", "active", 1);
                return false;
            }
            $(".listBoxContainer").find('.help-block').each(function() {
                var text = $(this).text().toLowerCase();
                (text.indexOf(valThis) != -1) ? $(this).parents('.listBoxContainer').accordion("option", "active", 0): $(this).parents('.listBoxContainer').accordion("option", "active", 1);
            });
        });

        //Sortable
        @foreach($transitions as $k => $transition)
        $('#listBoxContainer{{$k}}').customListBox({
            nameKey: 'user_permissions[{{$k}}][{dynamicKey}][]',
            isDynamicKey: true,
            dynamicElm: '[name="permission_specific_to{{$k}}"]:checked',
            containerSelector: '.listBoxContainer'
        });
        @if($i == 1)
        $('#listBoxContainer{{$k}}').accordion({
            active: 0,
            collapsible: true
        });
        @else
        $('#listBoxContainer{{$k}}').accordion({
            active: 1,
            collapsible: true
        });
        @endif
        setTimeout(function(){
            $('#listBoxContainer{{$k}}').find('[name="permission_specific_to{{$k}}"][value="'+specificToRole+'"]').trigger('click');
        },300)
        @php($i++)
        @endforeach

        $(document).on('click', '[name^="permission_specific_to"]', function() {
            let parentElm = $(this).parents('.listBoxContainer:first');
            parentElm.find('.draggable-left').html('');
            let transitionId = parentElm.find('[name="wf_transition_id"]').val();
            let specificPermission = $(this).val();
            parentElm.find('.draggable-right').find('.ui-state-disabled').removeClass('ui-state-disabled');

            if (specificPermission == specificToUser) {
                let roles = getUnassignedRoleList(parentElm, transitionId);
                CustomScript.initCustomSelect2(
                    parentElm.find('[name="role"]')
                        .select2("destroy")
                        .empty()
                        .prepend('<option selected=""></option>'),
                    { data: roles }
                );
                parentElm.find('.user-specific-block').show();
                parentElm.find('.draggable-right').find('[name="user_permissions['+transitionId+']['+specificToRole+'][]"]').each(function(){
                    $(this).parents('li').addClass('ui-state-disabled')
                });
            } else {
                parentElm.find('.user-specific-block').hide();
                parentElm.find('[name="role"]').val("").trigger('change');

                let assignedList = getAssignedUerList(parentElm, transitionId, specificPermission);
                let liHtml = "";
                $(availableRoles).each(function(k, data) {
                    if ($.inArray(data.id, assignedList) === -1) {
                        liHtml += `
                    <li class="listItems ui-sortable-handle selected">
                    <input type="hidden" class="form-control" value="` + data.id + `">
                <label class="control-label" style="margin: 5px;">` + data.name + `</label>
            </li>`;
                    }
                });
                parentElm.find('.draggable-left').html(liHtml);
                parentElm.find('.draggable-right').find('[name="user_permissions['+transitionId+']['+specificToUser+'][]"]').each(function(){
                    $(this).parents('li').addClass('ui-state-disabled')
                });
            }
        })

        $(document).on('change', '[name="role"]', function() {
            let parentElm = $(this).parents('.listBoxContainer:first');
            if (!$(this).val()) {
                parentElm.find('.draggable-left').html('');
                return false;
            }
            $("#custom-ajax-loader").show();
            let requestData = {
                role_id: $(this).val()
            }
            let transitionId = parentElm.find('[name="wf_transition_id"]').val();
            $.ajax({
                url: "/admin/cruds/getusers",
                type: "POST",
                data: requestData,
                dataType: "json",
                success: (response) => {
                    if (response && $.isArray(response) && response.length > 0) {
                        let specificPermission = parentElm.find('[name="permission_specific_to"]:checked').val();
                        let assignedList = getAssignedUerList(parentElm, transitionId, specificPermission);
                        let liHtml = "";
                        $(response).each(function(k, data) {
                            if ($.inArray(data.id, assignedList) === -1) {
                                liHtml += `
                            <li class="listItems ui-sortable-handle selected">
                            <input type="hidden" class="form-control" value="` + data.id + `">
                        <label class="control-label" style="margin: 5px;">` + data.text + `</label>
                    </li>`;
                            }
                        });
                        parentElm.find('.draggable-left').html(liHtml);
                    }
                },
                error: (data) => {},
            });
        })
    });

    function getAssignedUerList(parentElm, transitionId = null, specificPermission = null) {
        let assignedList = [];
        if (!transitionId) {
            return assignedList
        }
        $(parentElm).find('.draggable-right [name="user_permissions[' + transitionId + '][' + specificPermission + '][]"]').each(function() {
            assignedList.push(parseInt($(this).val()));
        });

        return assignedList;
    }

    function getUnassignedRoleList(parentElm, transitionId = null) {
        let unAssignedRoles = [];
        if (!transitionId) {
            return unAssignedRoles
        }
        
        let assignedList = [];
        $(parentElm).find('.draggable-right [name="user_permissions[' + transitionId + '][' + specificToRole + '][]"]').each(function() {
            assignedList.push(parseInt($(this).val()));
        });

        $.map(availableRoles, function(row) {
            let id = parseInt(row.id);
            if($.inArray(id, assignedList) === -1) {
                unAssignedRoles.push({
                    id: id,
                    text: row.name
                });
            }
        });

        return unAssignedRoles;
    }

    function createHashMap(input, hashKey) {
        if (typeof input != 'undefined', Array.isArray(input)) {
            let result = input.reduce(function (map, obj) {
                map[obj[hashKey]] = obj;
                return map;
            }, {});
            return result;
        }
        return [];
    }
</script>
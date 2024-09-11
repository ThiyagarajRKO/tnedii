$(document).ready(function () {
    $(document).on('change', '#module_controller', function () {
        $.ajax({
            url: '/admin/cruds/combotablefield',
            type: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {table : $(this).val(), isHashMap: true },
            dataType: 'json',
            success: res => {
                CustomScript.initCustomSelect2($('#module_property').select2('destroy').empty().prepend('<option selected=""></option>'), { data: res });
            },
            complete: () => {
            },
            error: data => {
            }
        });

    })

    $('#workflow-config-form').submit(function (e) {
            var isValid = validateTransitionDetails();
            if (!isValid) {
                e.preventDefault();
                return false;
            }
    });
})

function validateTransitionDetails() {
    let transList = [];
    let stateList = [];
    let valid = true;
    $('.is-invalid').removeClass('is-invalid');
    $('invalid-feedback').html('');

    $(document).find('.workflow-transition').each(function() {
        let transInput = $(this).find('[name$="[name]"]');
        let transName = transInput.val();

        if($.inArray(transName, transList) !== -1) {
            if(transInput.next().hasClass('invalid-feedback')) {
                setTimeout(function() {
                    transInput.addClass('is-invalid');
                    transInput.next().html('The transition name must be a unique value.');
                    transInput.next().show();
                },100)
            }
            return valid = false;
        }

        transList.push(transName);
        let fromStateInput = $(this).find('[name$="[from_state]"]');
        let toStateInput = $(this).find('[name$="[to_state]"]');
        let stateInput = fromStateInput.val()+toStateInput.val();

        if($.inArray(stateInput, stateList) !== -1) {
            if(toStateInput.next().hasClass('invalid-feedback')) {
                setTimeout(function() {
                    toStateInput.addClass('is-invalid');
                    toStateInput.next().html('The same transitions already exists.');
                    toStateInput.next().show();
                },100)
            }
            return valid = false;
        }
        stateList.push(stateInput);
    })
    return valid;
}
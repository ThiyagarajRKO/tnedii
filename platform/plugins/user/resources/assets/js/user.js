"use strict"

let userUtils = {
    frontEndForm: $('form[action$="postdata"]').length,
    formEl: ($('.main-form').length) ? $('.main-form').parents('form:first') : $('form[action$="postdata"]'),
    formAction: ($('.main-form').length) ? $('.main-form').parents('form:first').attr('action') : $('form[action$="postdata"]').attr('action'),
    init: function () {
        var table = ($('#plugins-user-table').length) ? $('#plugins-user-table').DataTable() : null;
        $(document).on('click', 'a[href^="users/viewdetail"],a[href*="users/edit"]', function (e) {
            e.preventDefault();
            let trEl = $(this).parents('tr:first').prev('tr');
            let data = table.row(trEl).data();
            let hrf = $(this).attr('href');
            if(data){
                let roleId = data.role_id || "";
                let urlEncode = btoa(roleId.replace(",", "|"));
                hrf += (hrf.indexOf("?") > 0) ? "&" : "?";
                location.href = hrf + "restricted_roleid=" + urlEncode;
            }else{
                location.href = hrf
            }
        })

        if ($('[name="is_login_needed"]:checked').length <= 0) {
            $('[name="is_login_needed"][value="1"]').prop('checked', 'checked');

        }

        $('.viewForm .saveAsCopy').attr('disabled', 'disabled');
        $('#nationality').trigger('change');
        $('#if_refugee').trigger('change');
        if(!table) {
            userUtils.initGoogleMap();
        }

        $(document).on('change', '[name*="is_current_job"]',function () {
            let parent = $(this).parents('.grouppedLayout:first');
            if ($(this).prop("checked")) {
                parent.find('[name*="end_date"]').val("");
                parent.find('[name*="end_date"]').attr('disabled', 'disabled');
                return;
            }
            parent.find('[name*="end_date"]').removeAttr('disabled');
        });
        let currentLocation=window.location.href;
        if(!currentLocation.includes('edit-profile')){
            $(document).find('label[for*="curriculum_vitae"]').parent().hide();
            $(document).find('input[name*="curriculum_vitae"]').attr('disabled',true);
        }
        this.validateCurriculumVitae();
        if (userUtils.frontEndForm) {
            $(document).on('blur', '#identity_number,#passport_number,#card_number', function () {
                if ($(this).prev().find('.required-field').length) {
                    userUtils.getUserBasedOnIdentificationNumber($(this));
                }
            })
        }
        $(document).on('click','#changeIdentification',function(){
            $(this).parents('label').next().attr('disabled',false);
            $(this).remove();
        })
    },

    initGoogleMap: function () {
        formData = formData || {};
        let presentLat = null;
        let presentLng = null;
        let permanentLat = null;
        let permanentLng = null;
        let nextKinLat = null;
        let nextKinLng = null;
        if(!$.isEmptyObject(formData)) {
            let userAddress = formData.user_addresses || {};
            let nextKinDetails = formData.next_kin_details || {};
            let coordinates = [];
            if(userAddress.present_coordinates) {
                coordinates = userAddress.present_coordinates.split('|');
                presentLat = coordinates[0];
                presentLng = coordinates[1];
            }

            if(userAddress.permanent_coordinates) {
                coordinates = userAddress.permanent_coordinates.split('|');
                permanentLat = coordinates[0];
                permanentLng = coordinates[1];
            }
            if(nextKinDetails.coordinates){
                coordinates = nextKinDetails.coordinates.split('|');
                nextKinLat = coordinates[0];
                nextKinLng = coordinates[1];
            }
        }
        let googleMap = {
            displayError: false,
            viewOnlyMode: true,
            enableAddressSearch: true,
            addressPrefix: 'present',
            submodule: 'user_addresses',
            lat: presentLat,
            lng: presentLng
        }
        userUtils.presentLocationMap = $('.present_address_block').customGoogleMap(googleMap);
        googleMap.addressPrefix = 'permanent';
        googleMap.lat = permanentLat;
        googleMap.lng = permanentLng;
        userUtils.presentLocationMap = $('.permanent_address_block').customGoogleMap(googleMap);
        googleMap.addressPrefix = '';
        googleMap.lat = permanentLat;
        googleMap.lng = permanentLng;
        userUtils.presentLocationMap = $('#wizard-step-p-2').customGoogleMap(googleMap);
    },
    validateCurriculumVitae: function () {
        let valid = true;
        $('#wizard-step-p-3 .subFormRepeater > .form-group').each(function () {
            $(this).find('.customError').remove();

            $(this).find('[name^="experience_infos"]+ .attachment-details').each(function () {
                let targetElm = $(this);
                let elm = $(targetElm).find('a');
                let inpElm = $(targetElm).prev();

                if (!elm.length) {
                    $(targetElm).append('<a></a>');
                    elm = $(targetElm).find('a');
                }
                var filePath = (targetElm.text()).trim();
                var allowedExtensions = /(\.pdf|\.txt|\.docx|\.doc)$/i;
                if (filePath && !inpElm.attr('disabled') && !allowedExtensions.exec(filePath)) {
                    $('<span class="invalid-feedback customError" style="display: inline;">The curriculum vitae field must be a "pdf,doc,docx,txt".</span>').insertAfter($(targetElm).next());
                    valid = false;
                }
            });
        });

        return valid;
    },
    getUserBasedOnIdentificationNumber: function(el){
           let identityNumber = $(el).val();
           let nameAttr = $(el).attr('name');
           let label = $(el).prev().text();
           let nationality = $('#nationality').val();
           if(!identityNumber){
               return false;
           }
           let requestData = {
                'key': nameAttr,
                'value': identityNumber,
            };

            $.ajax({
                url: '/cruds/get_user_based_on_nin',
                type: "POST",
                headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || $('[name="_token"]').val()
                    },
                data: requestData,
                dataType: 'json',
                success: (response) => {
                    if(response && !response.error){
                        let studentDetails = response;
                       userUtils.formEl.loadDataFromJSON(studentDetails);
                       let updateAction = userUtils.formAction.replace('postdata','updatedata/'+response.id);
                        userUtils.formEl.attr('action',updateAction);
                        userUtils.formEl.find('#email').attr('disabled',true);
                        userUtils.formEl.find('#username').attr('disabled',true);
                        $(el).prev().find('.required-field').append(' <a id="changeIdentification">Change '+ label.replace('*','')+'</a>')
                        $(el).attr('disabled',true);
                        setTimeout(function(){
                            if($(el).hasClass('is-invalid')){
                                $(document).find('#'+nameAttr+'-error').remove();
                                $(el).removeClass('is-invalid');
                            }
                        },1000)
                        
                    }else{
                        if(response && response.error){
                            Impiger.showError(response.message);
                            return false;
                        }
                    }
                },
                error: (data) => {
                    userUtils.formEl.trigger('reset');
                    userUtils.formEl.attr('action',userUtils.formAction);
                    $(el).val(identityNumber);
                    $('#nationality').val(nationality).trigger('change');
                    userUtils.formEl.find('#email').attr('disabled',false);
                    userUtils.formEl.find('#username').attr('disabled',false);
                },
            });
    },
}

$(document).ready(function () {
    userUtils.init();
});



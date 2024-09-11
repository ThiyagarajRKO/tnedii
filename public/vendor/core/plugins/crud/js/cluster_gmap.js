var map;
// 0.347596|32.58252
function initMap() {
    // The map, centered on Central Park
    const center = { lat: 0.347596, lng: 32.58252 };
    const options = { zoom: 7, scaleControl: true, center: center };
    map = new google.maps.Map(
        document.getElementById('map'), options);
        var i = 0;

    $.map(institutions, function (data) {
        let coordinates = data.coordinates || "";
        let country = data.country_name || "";
        let district = data.district_name || "";
        let instituteName = data.name || "";
        var url = window.location.href;
        var arr = url.split("/");
        let address = instituteName + "<br>" + district + "<br> " + country + "<br><a style='color: #337ab7;' target='_blank' href='"+arr[0]+"//"+data.domain_url+"'>View Website</a>";

        var infoWindow = new google.maps.InfoWindow({
            content: address
        });

        let image = (data.image) ? "<img width='100' alt='"+instituteName+"' height='50' src='/storage/"+data.image+"'></img><br>" : "";
        address = image + address;
        if (coordinates) {
            coordinates = coordinates.split('|');
            let lat = parseFloat($.trim(coordinates[0]));
            let lng = parseFloat($.trim(coordinates[1]));
            let marker = new google.maps.Marker({
                position: { lat: lat, lng: lng }, map: map,
                infoWindow: infoWindow
            });
            google.maps.event.addListener(marker, 'click', (function(marker) {
                return function() {
                    infoWindow.setContent(address);
                    infoWindow.open(map, marker);
                }
            })(marker, i));
            // infoWindow.open(map, marker);
        }
        i++;
    });
}

function showAjaxLoading () {
    $('#ajaxLoader').css('display', 'block');
}

function hideAjaxLoading() {
    $('#ajaxLoader').css('display', 'none');
}

$(document).ready(function () {

    $('.filter-cluster-googlemap-form').validate({
        ignore: ":hidden",
        errorElement: 'span', //default input error message container
        errorClass: 'invalid-feedback', // default input error message class
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length ||
                element.prop('type') === 'checkbox' || element.prop('type') === 'radio') {
                error.insertAfter(element.parent());
            } else {
                error.insertAfter(element);
            }
        },
        highlight: function (element) {
            $(element).closest('.form-control').removeClass('is-valid').addClass('is-invalid');
        },
        unhighlight: function (element) {
            $(element).closest('.form-control').removeClass('is-invalid').addClass('is-valid');
        },

        success: function (element) {
            $(element).closest('.form-control').removeClass('is-invalid').addClass('is-valid');
        },
        focusInvalid: false, // do not focus the last invalid input
        rules: {
            // institute_type: { required: true },
            // institute: {required: true }
        },

        submitHandler: function(form) {
            let requestData = {
                type: $(form).find('[name="institute_type"]').val(),
                institute: $(form).find('[name="institute"]').val()
            }
            $.ajax({
                url: '/cruds/get_institutions',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || $('[name="_token"]').val()
                },
                data: requestData,
                dataType: 'json',
                beforeSend: () => {
                    showAjaxLoading();
                    // _self.showAjaxLoading();
                },
                success: response => {
                    if (response.error) {
                        // crudUtils.notifyMessageError(data.message);
                    }
                    institutions = response;
                    initMap();
                },
                complete: () => {
                    hideAjaxLoading();
                },
                error: data => {
                }
            });
        }
    });
})

google.maps.event.addDomListener(window, 'load', initMap);
"use strict";

$.fn.customGoogleMap = function (options) {
    let widget = this; widget.config = {};
    widget.defOptions = {
        map: '',
        lat: defaultLat,
        lng: defaultLng,
        markers: [],
        marker: {},
        displayError: true,
        showInfoWindow: false,
        enableAddressSearch: false,
        viewOnlyMode: false,
        mapDiv: widget.find('.g-map').data('map-cavas-div'),
    };

    widget.init = function (options) {
        let widget = this;
        widget.config = $.extend({}, widget.defOptions, options);
        widget.config.addressPrefix = (widget.config.addressPrefix) ? widget.config.addressPrefix : widget.data('address-prefix');
        widget.initMap();
        widget.bindEvents();
    };

    widget.bindEvents = function () {
        widget.on('change', "fieldset select,[name*='same_as_present']", function (e, wasTriggered) {
           if(!wasTriggered) {
                setTimeout(function () {
                    widget.findGeoCoordinates(null, null, widget.getAddressText());
                }, 100)
            }
        });
    }

    widget.initMap = function () {
        let widget = this;
        if (widget.config.lat) {
            widget.config.lat = parseFloat(widget.config.lat);
        }

        if (widget.config.lng) {
            widget.config.lng = parseFloat(widget.config.lng);
        }

        widget.config.map = new GMaps({
            div: widget.config.mapDiv,
            lat: widget.config.lat,
            lng: widget.config.lng,
            zoom: 7
        });

        if (widget.config.enableAddressSearch) {
            widget.findGeoCoordinates(widget.config.lat, widget.config.lng, widget.config.address);
        } else {
            if (lng && lng) {
                widget.findGeoCoordinates(widget.config.lat, widget.config.lng);
            }
        }
        if (!widget.config.viewOnlyMode) {
            widget.config.map.addListener('click', function (event) {
                widget.clearmarkers();
                widget.findGeoCoordinates(event.latLng.lat(), event.latLng.lng());
                widget.setLatLngVal(event.latLng.lat(), event.latLng.lng());
            });
        }
    };

    widget.findGeoCoordinates = function (lat, lng, address = null) {
        let widget = this;
        widget.clearmarkers();
        let geocodeConfig = {
            callback: function (results, status) {
                if (status == 'OK') {
                    var latlng = results[0].geometry.location;
                    widget.config.map.setCenter(latlng.lat(), latlng.lng());
                    widget.config.map.setZoom(8);
                    widget.addCustomMarker(results[0]['formatted_address'], latlng.lat(), latlng.lng());
                    widget.setLatLngVal(latlng.lat(), latlng.lng());
                } else {
                    if (!widget.config.displayError) {
                        return false;
                    }

                    CommonUtils.notifyMessageError({
                        'title': "Info",
                        'content': 'Geocoder failed due to: ' + status
                    });
                }
            }
        };

        if (widget.config.enableAddressSearch) {
            if (address) {
                geocodeConfig.address = address;
            } else {
                widget.setLatLngVal(widget.config.lat, widget.config.lng);
                let latlng = { lat: parseFloat(lat), lng: parseFloat(lng) };
                geocodeConfig.location = latlng;
            }
        } else {
            let latlng = { lat: parseFloat(lat), lng: parseFloat(lng) };
            geocodeConfig.location = latlng;
        }

        GMaps.geocode(geocodeConfig);
    };

    widget.setLatLngVal = function (lat, lng) {
        let addressPrefix = widget.config.addressPrefix || '';
        let elemName = "coordinates";
        let submodule = widget.config.submodule || '';
        elemName = (submodule) ? submodule +"[coordinates]" : "coordinates";
        if(addressPrefix) {
            elemName = (submodule) ? submodule +"["+ addressPrefix + "_"+ "coordinates]" : addressPrefix + "_"+ "coordinates";
            widget.find('[name="coordinates"]').attr('name', elemName);
        }
        widget.find('[name="coordinates"]').attr('name', elemName);

        if (!widget.find('[name="'+elemName+'"]').length) {
            return false;
        }
        let coordinates = (lat && lng) ? lat + " | " + lng : "";
        widget.find('[name="'+elemName+'"]').val(coordinates);
    }

    widget.addCustomMarker = function (content, lat, lng) {
        let widget = this;
        //        content = (content) ? content + '<br/>' : '';
        // Create infoWindow
        var infoWindow = new google.maps.InfoWindow({
            content: content
        });

        let marker = widget.config.map.addMarker({
            lat: lat,
            lng: lng,
            title: content,
            infoWindow: infoWindow,
            draggable: (!widget.config.viewOnlyMode) ? true : false
        });

        widget.config.marker = marker;
        if (widget.config.showInfoWindow) {
            infoWindow.open(widget.config.map, marker);
        }
        widget.config.markers = [];
        widget.config.markers.push(marker);

        if (!widget.config.viewOnlyMode) {
            google.maps.event.addListener(widget.config.marker, 'dragend', function (event) {
                widget.findGeoCoordinates(this.getPosition().lat(), this.getPosition().lng());
                $('[name="institute_lat"]').val(this.getPosition().lat());
                $('[name="institute_lng"]').val(this.getPosition().lng());
            });
        }
    };

    widget.clearmarkers = function () {
        let widget = this;
        if (!CommonUtils.isValidArray(widget.config.markers)) {
            return false;
        }

        for (var i = 0; i < widget.config.markers.length; i++) {
            widget.config.markers[i].setMap(null);
        }
    }

    widget.getAddressText = function () {
        let address = [];
        let addressPrefix = widget.config.addressPrefix || '';
        addressPrefix = (addressPrefix) ? addressPrefix + '_' : '';
        let elementSuffix = widget.config.elmSuffix || '';
        elementSuffix = (elementSuffix) ? '_' + elementSuffix : '';
        let districtElm = widget.find('[name*="' + addressPrefix + 'district' + elementSuffix + '"]');
        let countyElm = widget.find('[name*="' + addressPrefix + 'county' + elementSuffix + '"]');
        let subCountyElm = widget.find('[name*="' + addressPrefix + 'sub_county' + elementSuffix + '"]');
        let parishElm = widget.find('[name*="' + addressPrefix + 'parish' + elementSuffix + '"]');
        let villageElm = widget.find('[name*="' + addressPrefix + 'village' + elementSuffix + '"]');
        let village = villageElm.val();
        let parish = parishElm.val();

        if (village) {
            address.push(villageElm.find('option:selected').text());
            address.push(countyElm.find('option:selected').text());
            address.push(districtElm.find('option:selected').text());
        } else if (parish) {
            address.push(parishElm.find('option:selected').text());
            address.push(countyElm.find('option:selected').text());
            address.push(districtElm.find('option:selected').text());
        } else {
            let subCounty = (subCountyElm.val()) ? subCountyElm.find('option:selected').text() : '';
            let county = (countyElm.val()) ? countyElm.find('option:selected').text() : '';
            let district = (districtElm.val()) ? districtElm.find('option:selected').text() : '';
            address.push(subCounty);
            address.push(county);
            address.push(district);
        }

        let filteredAddress = address.filter(function (v) { return v !== '' });
        let countryElm = widget.find('[name*="' + addressPrefix + 'country' + elementSuffix + '"]');
        let country = (countryElm.val()) ? countryElm.find('option:selected').text() : '';
        filteredAddress.push(country);
        console.log(widget.config.mapDiv);
        if (!CommonUtils.isValidArray(filteredAddress)) {
            widget.config.map = new GMaps({
                div: widget.config.mapDiv,
                lat: widget.config.lat,
                lng: widget.config.lng,
                zoom: 7
            });
        }
        return filteredAddress.join();
    };

    widget.setCoordinates = function (lat, lng) {
        let widget = this;
        widget.clearmarkers();
        let geocodeConfig = {
            callback: function (results, status) {
                if (status == 'OK') {
                    var latlng = results[0].geometry.location;
                    widget.config.map.setCenter(latlng.lat(), latlng.lng());
                    widget.config.map.setZoom(8);
                    widget.addCustomMarker(results[0]['formatted_address'], latlng.lat(), latlng.lng());
                    widget.setLatLngVal(latlng.lat(), latlng.lng());
                } else {
                    if (!widget.config.displayError) {
                        return false;
                    }

                    CommonUtils.notifyMessageError({
                        'title': "Info",
                        'content': 'Geocoder failed due to: ' + status
                    });
                }
            }
        };

        let latlng = { lat: parseFloat(lat), lng: parseFloat(lng) };
        geocodeConfig.location = latlng;
        widget.setLatLngVal(parseFloat(lat), parseFloat(lat));
        GMaps.geocode(geocodeConfig);
    };

    widget.init(options);
    return widget;
};
"use strict";
var urlData = window.location.href.split("/");
$(document).ready(function () {
    setTimeout(() => {
        menteeUtils.init();
    }, 500);
});

let menteeUtils = {
    init: function () {
        // $('#entrepreneur_id').select2("destroy").empty().prepend('<option selected=""></option>');
        this.searchEntrepreneurs();
        // create
        if(urlData.length > 1 && (urlData[urlData.length - 2] == 'edit' || urlData[urlData.length - 1] == 'create')) {
            this.loadDefaultList();
        }
    },
   
    searchEntrepreneurs : function() {
        var urlData = window.location.href.split("/");
        
            // $('#entrepreneur_id').select2({
            $('SELECT[name="entrepreneur_id"]').select2({
                // minimumInputLength: 2,
                dataType: 'json',
                delay: 250,
                ajax: {
                    url: "/get-entrepreneurs-list-by-search",
                    data: function (params) {
                        var query = {
                            param: params.term,
                        }
                        return query;
                    },
                    processResults: function (data, params) {
                        // console.log("processResults -> params", params);
                        return {
                            results: data
                        };
                    }
                }
            });
    },
    loadDefaultList: function() {
        var requestData = {};
        // var urlData = window.location.href.split("/");
        var apiUrl = "/get-entrepreneurs-list-by-search";
        if(urlData.length > 1 && urlData[urlData.length - 2] == 'edit' && formData.entrepreneur_id) {
            requestData.id = formData.entrepreneur_id;
            apiUrl = "/get-entrepreneur-by-id";
        }
        $.ajax({
            url: apiUrl,
            // url: "/get-entrepreneurs-list-by-search",
            type:"GET",
            data: requestData,
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") || $('[name="_token"]').val(),
            },
            dataType: "json",
            success: (response) => {
                console.log("loadDefaultList -> response", response);
                // var entrepreneurSelect = $('#entrepreneur_id');
                var entrepreneurSelect = $('SELECT[name="entrepreneur_id"]');
                if(urlData.length > 1 && urlData[urlData.length - 2] == 'edit') {
                    var option = new Option(response[0].text, response[0].id, true, true);
                    entrepreneurSelect.append(option).trigger('change');
                    // manually trigger the `select2:select` event
                    entrepreneurSelect.trigger({
                        type: 'select2:select',
                        params: {
                            data: response
                        }
                    });
                    $('.invalid-feedback').hide();
                } 
            }
        });        
    }
};


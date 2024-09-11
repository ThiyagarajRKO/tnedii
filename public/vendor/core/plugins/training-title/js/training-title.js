
"use strict";

var urlData = window.location.href.split("/");
let trainingUtils = {
    formEl: $("#impiger-training-title-forms-training-title-form"),
    
    init: function () {
        this.checkAlreadySubscribedToEvent({'id': urlData[urlData.length -1]});
        this.disablePastDate();
        this.bindEvent();
        
    },
    bindEvent: function () {
        $(document).on('change', '#fee_paid', function (e) {
            console.log('onchange triggered');
            trainingUtils.disableTrainingFee(e);           
        });   
    },
    addButton: function() {
        // let params = (new URL(document.location)).searchParams;
        // let name = params.get("id");
        var urlData = window.location.href.split("/");
        var training_id = urlData[urlData.length - 1];
       
        var training_fee_amount = $('#fee_amount').text();
        var fee_paid = $('#fee_paid').text();
        console.log("training_fee_amount");
        console.log(training_fee_amount);
        let payment_url = "/razorpay-payment-view?amount="+training_fee_amount+"&id="+training_id;
        let payment_button = '<div class="widget meta-boxes form-actions form-actions-reset form-actions-default action-horizontal mt-10">';
        payment_button += '<div class="widget-body form-actions-fixed-bottom">';
        payment_button += '<div class="btn-set mt-0 text-right">'; 
        if(training_fee_amount && fee_paid == 'Paid') {
            payment_button += '<a href="'+payment_url+'" class="btn btn-icon btn-sm btn-success" data-toggle="tooltip" data-original-title="Payment Gateway"><i class="fa fa-money"></i> Subscribe - '+training_fee_amount+' INR </a>';
        } else if(fee_paid == 'Free') {
            payment_url = "/admin/training-titles/subscribe-to-event/"+training_id;
            payment_button += '<a href="'+payment_url+'" class="btn btn-icon btn-sm btn-success" data-toggle="tooltip" data-original-title="Payment Gateway"><i class="fa fa-money"></i> Apply Now </a>';
        }                         
        
        payment_button += '</div></div></div>';
        setTimeout(() => {
            if(trainingUtils.formEl.hasClass('viewForm')){
                // $(document).find('#main').prepend("<a class='btn btn-info' id='apply_event' href='"+payment_url+"'><i class='fa fa-money'></i> Subscribe </button>");
                $(document).find('form').prepend(payment_button);
            }
        }, 1000);
        
    },
    disablePastDate: function () {
        if (jQuery().bootstrapDP) {
            console.log("disablePastDate");
            setTimeout(() => {
                console.log("disablePastDate from setTimeout");
                let element1 = $("#training_start_date");
                let element2 = $("#training_end_date");                
                let format = "yyyy-mm-dd";
                var start = new Date();
                var training_id = urlData[urlData.length - 1];
                if(urlData.length > 1 && urlData[urlData.length - 2] != 'edit') {
                    $(element1).bootstrapDP('setStartDate',start);
                    $(element2).bootstrapDP('setStartDate',start);
                    $(element1).bootstrapDP('format', format);
                    $(element1).bootstrapDP('update');
                    $(element2).bootstrapDP('update');
                } 
                if(urlData.length > 1 && urlData[urlData.length - 2] == 'viewdetail') {
                    $('.datepicker').hide();
                }
                
                if(urlData.length > 1 && urlData[urlData.length - 2] == 'edit') {
                    $('#fee_paid').trigger('change');
                }
            },1000 * 3);
        }
    },
    disableTrainingFee: function(e) {
        let target = e.target;
        console.log("disableTrainingFee");
        console.log($(target).val());
        if($(target).val() == 1) {
            $('.trainingfee').hide();
            $('#fee_amount').attr('disabled', 'disabled');
        } else {
            $('#fee_amount').removeAttr('disabled');
            $('.trainingfee').show();
        }
    },
    checkAlreadySubscribedToEvent: function(params) {
        // /check-already-subscribed-to-event/
        $.ajax({
            url: "/check-already-subscribed-to-event/"+params.id,
            type: "GET",
            // data: params,
            dataType: "json",
            success: (response) => {
                if (response.length == 0 && !response.error) {                 
                    console.log("getTrainingBudget -> response");
                    console.log(response);
                    trainingUtils.addButton();
                }
            },
            error: (error) => {
                trainingUtils.addButton();
            }
        });
    }
    
}

$(document).ready(function () {
    trainingUtils.init();
    $('#fee_amount').closest('div.col-md-4').addClass('trainingfee');
    
});
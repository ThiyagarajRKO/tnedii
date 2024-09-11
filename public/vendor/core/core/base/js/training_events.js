var disableSpecificDates = [];
jQuery(document).ready(function () {
    getPrograms({});
});
let element = $(document).find("#datepicker");
if (jQuery().bootstrapDP) {
    $(document).find(element).bootstrapDP('destroy');
    setTimeout(function () {
        $(document).find(element).bootstrapDP('destroy');
        let format = $(document).find(element).data("date-format");
        if (!format) {
            format = "yyyy-mm-dd";
        }
        
        $(document).find(element).bootstrapDP({
            maxDate: 0,
            changeMonth: true,
            // changeYear: true,
            inline: true,
            dateFormat: format,
            startDate: new Date(),
            beforeShowDay: renderCalendarCallback
        });
        
    },3000);

    $('#datepicker').bind('changeDate', onDateChange);
    $('#datepicker').bind('changeMonth', onMonthChange);
}

function renderCalendarCallback(date) {
    let month = '0' + (date.getMonth() + 1);
    month = month.slice(-2);
    dmy = date.getDate() + "-" + (month) + "-" + date.getFullYear();
    if (disableSpecificDates.indexOf(dmy) != -1) {
        return {classes: 'highlighted-cal-dates'};
    }
    else {
        return true;                    
    }  
}

function onDateChange(event) {
    var date = event.date;
    // var tsd = date.getDate() + "-" + (date.getMonth() + 1) + "-" + date.getFullYear();
    var tsd = date.getFullYear() + "-" + (date.getMonth() + 1) + "-" + date.getDate();
    getPrograms({'tsd': tsd},date);
 }

 function onMonthChange(event) {
    // console.log("onMonthChange ball back", event);
    // var date = event.date;
    var date = getFirstDayOfMonth(
        event.date.getFullYear(),
        event.date.getMonth(),
      );
    var tsd = date.getFullYear() + "-" + (date.getMonth() + 1) + "-" + date.getDate();

    getPrograms({'tsd': tsd}, date);
 }


 function getPrograms(params, datePc = null) {
    // console.log("getPrograms call back", params);
    disableSpecificDates = [];
    $.ajax({
        url: "/get-annual-action-plan",
        type: "GET",
        data: params,
        dataType: "json",
        success: (response) => {
            var htmlTag = '';
            let dataLen = response.length;
            if (response && dataLen > 0) {
                response.forEach(function(value, key){

                    if (disableSpecificDates.indexOf(value.start) == -1) {
                        disableSpecificDates.push(value.start);
                    }
                    
                    htmlTag += generateTemplate(value);

                  /*  if(value.start && dataLen == (key + 1)) {
                        let dateStr = value.start.split("-");
                        $(document).find(element).bootstrapDP( "update", new Date(dateStr[2],dateStr[1], dateStr[0]) );
                    } */
                });
                $(document).find('.training-wrapper').css('overflow-y', 'auto').css('overflow-x', 'hidden');
                $(document).find('.training-wrapper')
            } else {
                // Impiger.showError(response.message);
                $(document).find('.training-wrapper').css('overflow', 'unset');
                htmlTag = '<div class="col-md-12 pt-50 pb-50 text-center">';
                htmlTag += '<p class="custom-alert__message text-center"> Training program is not available for the future date or selected criteria </p>';
                htmlTag += '</div>';
            }
            $(document).find('#training_item_list').html(htmlTag);
            if(datePc) {
                $(document).find(element).bootstrapDP('update', datePc);
            }
        }
    });
 }

 function generateTemplate(data) {
    var html = '<div class="col-md-6 col-lg-4">';
    html += '<div class="rounded edii_workshop">';
    html += '<div class="edii_workshop_info">';
    html += '<div class="edii_workshop_date rounded">';
    html += '<p class="month">'+ data.month +'</p>';
    html += '<p class="date">'+ data.date +'</p>';
    html += '</div>';
    html += '<div class="edii_workshop_desc">';
    html += '<div><h5><b>'+ (data.title ? data.title : '--') +'</b></h5></div>';
    html += '<div class="readmore">';
    html += '<a style="width:100%; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" href="/training?id='+data.id+'" title="'+ data.venue +'">Venue: '+ data.venue +'</a>';
    html += '</div>';
    html += '</div>';
    html += '</div></div></div>';
    return html;    
 }

 function getFirstDayOfMonth(year, month) {
    return new Date(year, month, '01');    
  }
 
 
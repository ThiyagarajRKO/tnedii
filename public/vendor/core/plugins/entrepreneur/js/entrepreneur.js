$(document).ready(() => {

    // $('#student_type_id').addClass('student');
    // document.getElementById('div-03');
    $('#student_type_id').closest('div.col-md-4').addClass('student');

    $('#student_school_name').closest('div.col-md-4').addClass('schoolstudent');
    $('#student_standard_name').closest('div.col-md-4').addClass('schoolstudent');

    $('#hub_institution_id').closest('div.col-md-4').addClass('collegestudent spokestudent');
    $('#student_college_name').closest('div.col-md-4').addClass('collegestudent');
    $('#student_course_name').closest('div.col-md-4').addClass('collegestudent');
    $('#student_year').closest('div.col-md-4').addClass('collegestudent');


    $('#qualification_id').closest('div.col-md-4').addClass('others');
    $('#entrepreneurial_category_id').closest('div.col-md-4').addClass('others');

    $('#spoke_registration_id').closest('div.col-md-4').addClass('spokestudent');

    $('.student').hide();
    $('.schoolstudent').hide();
    $('.collegestudent').hide();
    $('.others').hide();
    $('.spokestudent').hide();

    // $('#candidate_type_id').val('');
    // $('#student_type_id').val('');

    $('#candidate_type_id').on('change', function(){
        let string = $(this).val();
        // console.log(string);
        let selectedText = $("#candidate_type_id option:selected").text();
        if(selectedText == 'Student'){
            $('#student_type_id').removeAttr('disabled');
            $('#student_type_id').attr('required');
            // $('#student_type_id').val('');
            showStudentType();
            hideOthers();
            hideSpokeStudent();
            enableStudentTypeIdFields();
            disableOthersFields();
            disableStudentFields();
            disableCollegeFields();
            disableSpokeStudentFields();
        } else if(selectedText == 'SpokeStudent'){ 
            hideOthers();
            hideStudentType();
            hideStudent();
            hideCollege();
            
            showSpokeStudent();
            // disableSpokeStudentFields();
            disableStudentTypeIdFields();
            disableCollegeFields();
            disableOthersFields();
            disableStudentFields();
            enableSpokeStudentFields();
        } else {
            showOthers();
            hideStudentType();
            hideStudent();
            hideCollege();
            hideSpokeStudent();
        
            enableOthersFields();
            disableStudentTypeIdFields();
            disableStudentFields();
            disableCollegeFields();
            disableSpokeStudentFields();
        }
        // console.log(selectedText);
    });

    $('#student_type_id').on('change', function(){
        let selectedText1 = $("#student_type_id option:selected").text();
        if(selectedText1 == 'School'){
            
            enableStudentFields();
            disableCollegeFields();
            
            hideCollege();
            showStudent();

        } else if(selectedText1 == 'College'){

            enableCollegeFields();
            disableStudentFields();

            showCollege();
            hideStudent();

        } else {
            
            hideCollege();
            hideStudent();

            disableStudentTypeIdFields();
            disableStudentFields();
            disableCollegeFields();
            disableOthersFields();

        }
        // console.log(selectedText1);
    });

    var urlData = window.location.href.split("/");
    setTimeout(() => {
        if(urlData.length > 1 && urlData[urlData.length - 2] == 'edit') {
            $('#candidate_type_id').trigger('change');
            let candidateSelectedText = $("#candidate_type_id option:selected").text();
            if(candidateSelectedText == 'Student') {
                console.log('I am from edit if');
                $('#student_type_id').trigger('change');
                let studentTypeSelectedText1 = $("#student_type_id option:selected").text();
                console.log(studentTypeSelectedText1);
                if(studentTypeSelectedText1 == 'School'){

                }
            }            
        }
    }, 1500);
    
    
});

function showStudentType() {
    // $('#student_type_id').val('');
    $('.student').show();
}

function hideStudentType() {
    $('.student').hide();
}

function enableStudentTypeIdFields() {
    $('#student_type_id').removeAttr('disabled');
}

function disableStudentTypeIdFields() {
    $('#student_type_id').attr('disabled', 'disabled');
}

function showStudent() {
    $('.schoolstudent').show();
}

function hideStudent() {
    $('.schoolstudent').hide();
}

function enableStudentFields() {
    $('#student_school_name').removeAttr('disabled');
    $('#student_standard_name').removeAttr('disabled');
}

function disableStudentFields() {
    $('#student_school_name').attr('disabled', 'disabled');
    $('#student_standard_name').attr('disabled', 'disabled');
}


function showSpokeStudent() {
    // $('#spoke_registration_id').val('');
    $('.spokestudent').show();
}

function hideSpokeStudent() {
    $('.spokestudent').hide();
}

function enableSpokeStudentFields() {
    $('#hub_institution_id').removeAttr('disabled');
    $('#spoke_registration_id').removeAttr('disabled');
}

function disableSpokeStudentFields() {
    $('#hub_institution_id').attr('disabled', 'disabled');
    $('#spoke_registration_id').attr('disabled', 'disabled');
}

function showCollege() {
    $('.collegestudent').show();
}

function hideCollege() {
    $('.collegestudent').hide();
}

function enableCollegeFields() {
    $('#hub_institution_id').removeAttr('disabled');
    $('#student_college_name').removeAttr('disabled');
    $('#student_course_name').removeAttr('disabled');
    $('#student_year').removeAttr('disabled');
}

function disableCollegeFields() {
    $('#hub_institution_id').attr('disabled', 'disabled');
    $('#student_college_name').attr('disabled', 'disabled');
    $('#student_course_name').attr('disabled', 'disabled');
    $('#student_year').attr('disabled', 'disabled');
}

function showOthers() {
    $('.others').show();
}

function hideOthers() {
    $('.others').hide();
}

function enableOthersFields() {
    $('#qualification_id').removeAttr('disabled');
    $('#entrepreneurial_category_id').removeAttr('disabled');
}

function disableOthersFields() {
    $('#qualification_id').attr('disabled', 'disabled');
    $('#entrepreneurial_category_id').attr('disabled', 'disabled');
}



/*
$('#hub_institution_id').on('change', function(e){
    $('#spoke_registration_id').val('');
    let selectedText = $("#candidate_type_id option:selected").text();
    if(selectedText == 'SpokeStudent'){ 
        getSpokeCollegeByHub($(this).val());
    } else {
        e.preventDefault();
    }
});


function getSpokeCollegeByHub(params) {
    $.ajax({
        url: "/get-spoke-list/"+params,
        type: "GET",
        // headers: {
        //     "X-CSRF-TOKEN":
        //         $('meta[name="csrf-token"]').attr("content") ||
        //         $('[name="_token"]').val(),
        // },
        // data: requestData,
        dataType: "json",
        success: (response) => {
            if (response && !response.error) {
                let spokeList = response;
                // userUtils.formEl.loadDataFromJSON(studentDetails);
                if(spokeList.lenght > 0) {
                    $('#spoke_registration_id option').remove();
                    // $.each(spokeList, function (i, item) {
                    //     $('#mySelect').append($('<option>', { 
                    //         value: item.value,
                    //         text : item.text 
                    //     }));
                    // });
                }
                // console.log("getSpokeCollegeByHub -> response");
                // console.log(response);
            
                setTimeout(function () {
                    
                }, 1000);
            } else {
                if (response && response.error) {
                    Impiger.showError(response.message);
                    return false;
                }
            }
        },
        error: (data) => {
            // userUtils.formEl.trigger("reset");
            // userUtils.formEl.attr("action", userUtils.formAction);
            // $(el).val(identityNumber);
            // $("#nationality").val(nationality).trigger("change");
            // userUtils.formEl.find("#email").attr("disabled", false);
            // userUtils.formEl.find("#username").attr("disabled", false);
        },
    });
}
*/



$(document).ready(() => {
    $('#email').on('blur', function(e){
        getEntrepreneur({'email': $(this).val()});
    });

    $('#mobile').on('blur', function(e){
        getEntrepreneur({'mobile': $(this).val()});
        
    });

    // $('.certificate').on('click', function(e){
    //     let data = $(this).prop('data-section');
    //     console.log(data);
    //     getEntrepreneur();
        
    // });

    $(document).on('click', '.certificate', function () {
        // your function here
        // console.log("certificate");
        let data = $(this).attr('data-section');
        // console.log(JSON.parse(data));
        //return generateCertificate(JSON.parse(data));
        $('#certificateTemplate').modal('show');
        $("#clicked_button_data").val(data);
    });
    $(document).on('click', '.regenerate-certificate', function () {
        let data = $(this).attr('data-section');
        //data = JSON.parse(data);
        //data.action = "regenerate";
        //return generateCertificate(data);
        $('#certificateTemplate').modal('show');
        $("#clicked_button_data").val(data);
    });
    $("#certificateTemplate").on("hidden.bs.modal", function () {
        // put your default event here
        $("#clicked_button_data").val("");
    });
    $(".submit_choosen_templete").click(function() {
        let data = $("#clicked_button_data").val();
        data = JSON.parse(data);
        data.certificate_template = 1;
        if($("#certificate_template").val() != "")
        {
            data.certificate_template = $("#certificate_template").val();
        }
        //console.log(data);
        return generateCertificate(data);
    });

});



function generateCertificate(params) {
    let reqUrl = "/admin/trainees/generate-certificate";
    if(params.action == "regenerate") {
        reqUrl = "/admin/trainees/regenerate-certificate";
    }
    $.ajax({
        url: reqUrl,
        type: "POST",
        data: params,
        dataType: "json",
        success: (response) => {
            console.log("response");
            console.log(response);
            if (response && response.data && !response.error) {
                var link = document.createElement('a');
                link.href = response.data.certificate_path;
                link.href = '/admin/trainees/download-certificate/'+response.data.id;
                link.target = "_blank";
                link.download = response.data.file_name;
                link.dispatchEvent(new MouseEvent('click'));
            } else {
                Impiger.showError(response.message);
            }
        }
    });
}


function getEntrepreneur(params) {
    $('#password').removeAttr('disabled');
    $('.password').show();
    $.ajax({
        url: "/get-entrepreneur",
        type: "GET",
        data: params,
        dataType: "json",
        success: (response) => {
            if (response && !response.error) {
                let entrepreneur = response;

                if(entrepreneur) {

                    $('#password').closest('div.col-md-4').addClass('password');
                    $('#password').attr('disabled', 'disabled');
                    $('.password').hide();
                    if(entrepreneur.prefix_id) {
                        $('#prefix_id option[value='+ entrepreneur.prefix_id +']').attr('selected', 'selected');
                        $('#prefix_id').change();
                    }

                    if(entrepreneur.care_of) {
                        $('#care_of option[value='+ entrepreneur.care_of +']').attr('selected', 'selected');
                        $('#care_of').change();
                    }

                    if(entrepreneur.district_id) {
                        $('#district_id option[value='+ entrepreneur.district_id +']').attr('selected', 'selected');
                        $('#district_id').change();
                    }

                    if(entrepreneur.community) {
                        $('#community option[value='+ entrepreneur.community +']').attr('selected', 'selected');
                        $('#community').change();
                    }

                    if(entrepreneur.gender_id) {
                        $('#gender_id option[value='+ entrepreneur.gender_id +']').attr('selected', 'selected');
                        $('#gender_id').change();
                    }
                    
                    if(entrepreneur.physically_challenged || entrepreneur.physically_challenged == '0') {
                        $('#physically_challenged option[value='+ entrepreneur.physically_challenged +']').attr('selected', 'selected');
                        $('#physically_challenged').change();
                    }

                    if(entrepreneur.religion_id) {
                        $('#religion_id option[value='+ entrepreneur.religion_id +']').attr('selected', 'selected');
                        $('#religion_id').change();
                    }

                    if(entrepreneur.candidate_type_id) {
                        $('#candidate_type_id option[value='+ entrepreneur.candidate_type_id +']').attr('selected', 'selected');
                        $('#candidate_type_id').change();
                    }

                    if(entrepreneur.student_type_id) {
                        $('#student_type_id option[value='+ entrepreneur.student_type_id +']').attr('selected', 'selected');
                        $('#student_type_id').change();
                    }

                    if(entrepreneur.hub_institution_id) {
                        $('#hub_institution_id option[value='+ entrepreneur.hub_institution_id +']').attr('selected', 'selected');
                        $('#hub_institution_id').change();
                    }

                    if(entrepreneur.spoke_registration_id) {
                        $('#spoke_registration_id option[value='+ entrepreneur.spoke_registration_id +']').attr('selected', 'selected');
                        $('#spoke_registration_id').change();
                    }

                    if(entrepreneur.qualification_id) {
                        $('#qualification_id option[value='+ entrepreneur.qualification_id +']').attr('selected', 'selected');
                        $('#qualification_id').change();
                    }

                    if(entrepreneur.entrepreneurial_category_id) {
                        $('#entrepreneurial_category_id option[value='+ entrepreneur.entrepreneurial_category_id +']').attr('selected', 'selected');
                        $('#entrepreneurial_category_id').change();
                    }

                    if(entrepreneur.id) {
                        $("input[name='entrepreneur_id']").val(entrepreneur.id);
                    }

                    if(entrepreneur.user_id) {
                        $("input[name='user_id']").val(entrepreneur.user_id);
                    }
                    
                    if(entrepreneur.email) {
                        $('#email').val(entrepreneur.email);
                    }

                    if(entrepreneur.name) {
                        $('#name').val(entrepreneur.name);
                    }

                    if(entrepreneur.dob) {
                        $('#dob').val(entrepreneur.dob);
                    }

                    if(entrepreneur.aadhaar_no) {
                        $('#aadhaar_no').val(entrepreneur.aadhaar_no);
                    }

                    if(entrepreneur.father_name) {
                        $('#father_name').val(entrepreneur.father_name);
                    }

                    if(entrepreneur.mobile) {
                        $('#mobile').val(entrepreneur.mobile);
                    }
                    
                    if(entrepreneur.address) {
                        $('#address').val(entrepreneur.address);
                    }

                    if(entrepreneur.pincode) {
                        $('#pincode').val(entrepreneur.pincode);
                    }

                    if(entrepreneur.student_year) {
                        $('#student_year').val(entrepreneur.student_year);
                    }

                    if(entrepreneur.student_college_name) {
                        $('#student_college_name').val(entrepreneur.student_college_name);
                    }

                    if(entrepreneur.student_course_name) {
                        $('#student_course_name').val(entrepreneur.student_course_name);
                    }

                    if(entrepreneur.student_school_name) {
                        $('#student_school_name').val(entrepreneur.student_school_name);
                    }

                    if(entrepreneur.student_standard_name) {
                        $('#student_standard_name').val(entrepreneur.student_standard_name);
                    }

                    if(entrepreneur.activity_name) {
                        $('#activity_name').val(entrepreneur.activity_name);
                    }
                    
                }
                // console.log("getEntrepreneur -> response");
                // console.log(response);
            
                
            }
        }
    });
}
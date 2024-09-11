$(document).ready(() => {
    $('#training_title_id').on('change', function(){
        let params = {'training_title_id': $(this).val(), 'get_budget': 1};
        getTrainingBudget(params);
    });
});

function getTrainingBudget(params) {
    
    $.ajax({
        url: "/get-annual-action-plan",
        type: "GET",
        data: params,
        dataType: "json",
        success: (response) => {
            if (response && !response.error) {
                let program = response;
                if(program.length > 0) {
                    if(program[0].budget_per_program) {
                        $('#budget_approved').val(program[0].budget_per_program);
                        $('#budget_approved').attr('readonly', true);
                    } else {
                        $('#budget_approved').attr('readonly', false);
                    }                 
                } else {
                    $('#budget_approved').attr('readonly', false);
                }
                console.log("getTrainingBudget -> response");
                console.log(response);
            }
        }
    });
}
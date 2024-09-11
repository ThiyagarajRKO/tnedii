$(document).ready(() => {


    $('#budget_per_program').on('blur', function(){        
        // console.log(string);
        let no_of_batches = $('#no_of_batches').val();
        let budget_per_program = $(this).val();
        let total_budget = no_of_batches * budget_per_program;
        if(budget_per_program && no_of_batches) {
            $('#total_budget').val(total_budget);
        }
        
    });

    $('#no_of_batches').on('blur', function(){        
        // console.log(string);
        let no_of_batches = $(this).val();
        let budget_per_program = $('#budget_per_program').val();
        let total_budget = no_of_batches * budget_per_program;
        if(budget_per_program && no_of_batches) {
            $('#total_budget').val(total_budget);
        }
        
    });
    


    
});
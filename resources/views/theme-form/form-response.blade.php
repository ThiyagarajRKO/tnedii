<div class='alert alert-success'>
    {{ucfirst($form)}} Form Submitted Successfully
    
</div>
<button class='btn btn-primary' id="backBtn">Back</button>

<?php
/* Customized by Haritha Murugavel Start */
Theme::asset()
->usePath(false)
->add('theme-custom-css', asset('vendor/theme/css/theme_common_style.css'), [], ['style'], '1.0.0');
/* Customized by Haritha Murugavel End */
 Theme::asset()
    ->container('footer')    
    ->add('theme-form-response-js', asset('vendor/core/plugins/crud/js/crud_utils.js'), ['jquery'], [], '1.0.0');
?>
<div id="messageModal"></div>

@if(!str_contains(\Request::getPathInfo(),'admin'))
<script type="text/JavaScript" src="https://cdn.jsdelivr.net/npm/lodash@4.17.20/lodash.min.js"></script>
<script type='text/javascript' src="/vendor/core/core/base/js/repeater-field.js"></script>
@endif
<script type='text/javascript' src="https://cdn.jsdelivr.net/npm/bs-custom-file-input/dist/bs-custom-file-input.js"></script>
<script type='text/javascript' src="/vendor/core/plugins/crud/js/input-pattern.js"></script>
<script type='text/javascript' src="/vendor/core/core/base/js/custom_script.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>  
@if(str_contains(\Request::getPathInfo(),'admin'))
<script type='text/javascript' src="/vendor/core/plugins/crud/js/custom_encryption.js"></script>
@endif
<script>
    let defaultCountryId = <?php echo (setting('default_country')) ? setting('default_country'): ""; ?>;
//    var defaultLat = <?php echo (env('DEFAULT_LAT',"")) ? env('DEFAULT_LAT',"") : ""; ?>;
//    var defaultLng = <?php echo (env('DEFAULT_LNG',"")) ? env('DEFAULT_LNG',"") : ""; ?>;
</script>

<style>
.form-group ul.dropdown-menu {
    max-height: 300px!important;
    overflow: auto;
}

.phpdebugbar {
    display: none !important;
}
</style>
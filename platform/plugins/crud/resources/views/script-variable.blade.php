<div id="messageModal"></div>

<script type='text/javascript' src="https://cdn.jsdelivr.net/npm/bs-custom-file-input/dist/bs-custom-file-input.js"></script>
<script type='text/javascript' src="/vendor/core/plugins/crud/js/input-pattern.js"></script>
<script type='text/javascript' src="/vendor/core/core/base/js/custom_script.js"></script>
@if(str_contains(\Request::getPathInfo(),'admin'))
<script type='text/javascript' src="/vendor/core/plugins/crud/js/custom_encryption.js"></script>
@endif
<script>
    let defaultCountryId = <?php echo (setting('default_country')) ? setting('default_country'): ""; ?>;
    var defaultLat = <?php echo (env('DEFAULT_LAT',"")) ? env('DEFAULT_LAT',"") : ""; ?>;
    var defaultLng = <?php echo (env('DEFAULT_LNG',"")) ? env('DEFAULT_LNG',"") : ""; ?>;
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
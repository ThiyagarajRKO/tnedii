@php
$institutions = get_institutions(10);
$institutionList = $institutions->toArray();
$institutionList = json_encode(\Arr::get($institutionList, 'data'));
@endphp
<script>
    var institutions = <?php echo $institutionList; ?>;
</script>
@php

Theme::asset()
    ->usePath(false)
    ->add('theme-form2-css', asset('vendor/core/core/base/libraries/select2/css/select2.min.css'), [], [], '1.0.0')
    ->add('theme-form3-css', asset('vendor/core/core/base/libraries/select2/css/select2-bootstrap.min.css'), [], [], '1.0.0')
    ->add('theme-form4-css', asset('vendor/core/core/base/libraries/bootstrap-datepicker/css/bootstrap-datepicker3.min.css'), [], [], '1.0.0')
    ->add('theme-form5-css', asset('vendor/core/plugins/crud/css/module_custom_styles.css'), [], [], '1.0.0');

Theme::asset()
    ->container('footer')
    ->usePath(false)
    ->add('theme-form1-js', asset('vendor/core/core/js-validation/js/js-validation.js'), ['jquery'], [], '1.0.0')
    ->add('theme-form4-js', asset('vendor/core/core/base/libraries/select2/js/select2.min.js'), ['jquery'], [], '1.0.0')
    ->add('gmap2-js', asset('vendor/core/plugins/crud/js/cluster_gmap.js'), ['jquery'], [], '1.0.0');

Theme::asset()
->container('header')
->usePath(false)
->add('gmapheader-js', asset('https://maps.googleapis.com/maps/api/js?key='.env("GOOGLE_MAP_API_KEY","")), ['jquery'], []);
@endphp
<div id="ajaxLoader">
    <div class="loading">
    </div>
</div>

<div class="widget meta-boxes form-actions form-actions-default action-horizontal" style="margin-left:10px;margin-right:10px;border: 1px solid #ccc;">
    <div class="widget-title" style="border-bottom: 1px solid #ccc;">
        <h4><b style="color: #1f64a0">Filters</b></h4>
    </div>
    <div class="widget-body">
    <form method="GET" action="" accept-charset="UTF-8" class="filter-cluster-googlemap-form" novalidate="novalidate">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <div class="container mt-20">
        <div class="form-group col-md-4">
            <label for="institute_type" class="control-label" aria-required="true">Institute Type</label>
            <div class="form-group ui-select-wrapper">
                <select name="institute_type" class="ui-select select-full ui-select institute_type">
                    <option value="0">All Institute Type</option>
                    @foreach(\Impiger\MasterDetail\Models\MasterDetail::where(['attribute' => 'institute_type'])->pluck('name','id')->toArray() as $columnKey => $column)
                        <option value="{{ $columnKey }}" >{{ $column }}</option>
                    @endforeach    
                </select>
            </div>
        </div>

        <div class="form-group col-md-4">
            <label for="institute" class="control-label" aria-required="true">Institute Name</label>
            <input class="form-control" name="institute" type="text" id="institute">
        </div>

        <div class="form-group col-md-4">
            <div class="btn-container" style="margin-top:1.6em;">
                <button type="submit" class="btn btn-primary btn-apply">Apply</button>
            </div>
        </div>
     </div>

    </form>
    </div>
</div>
<div id="map" style="height: 500px; width: 98.5%;margin-left:10px;">
</div>
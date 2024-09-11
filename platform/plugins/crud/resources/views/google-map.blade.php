@php $time = rand();
$user = Auth::id();

if(!$user) {
    Theme::asset()
                    ->container('footer')
                    ->usePath(false)
                    
                    ->add('gmap2-js', asset('vendor/core/plugins/crud/js/gmaps.js'), ['jquery'], [], '1.0.0')
                    ->add('gmap3-js', asset('vendor/core/plugins/crud/js/custom_google_map.js'), ['jquery'], [], '1.0.0');

                    Theme::asset()
                    ->container('header')
                    ->usePath(false)
                    ->add('gmapheader-js', asset('https://maps.googleapis.com/maps/api/js?key='.env("GOOGLE_MAP_API_KEY","")), ['jquery'], []);

                } else {
Assets::addScriptsDirectly([
'vendor/core/core/base/js/common_utils.js',
'vendor/core/plugins/crud/js/gmaps.js'
,'vendor/core/plugins/crud/js/custom_google_map.js'
])
->addScriptsDirectlyToHeader([
'https://maps.googleapis.com/maps/api/js?key='.env("GOOGLE_MAP_API_KEY","")
]);
}
@endphp
</div>
<div class="row gmap-template">
    <div class="col-lg-12 col-xl-12">
        <div class="geoCoordinatesLabel">
            <div class="form-group">
                <label>Geo Coordinates</label>
                <input readonly="" type="text" class="form-control" name="coordinates" placeholder="Geo Coordinates">
            </div>
        </div>
        <div class="widget-content gmap">
            <div id="loadingMap" class="loading-image" style="display: none;"></div>
            <div data-map-cavas-div="#gmap-canvas{{$time}}" id="gmap-canvas{{$time}}" class="g-map"></div>
        </div>
    </div>
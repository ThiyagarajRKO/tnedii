<div class="page-footer fixed_bottom">
    <div class="page-footer-inner">
        <div class="row">
            <div class="col-md-8">
                {!! clean(trans('core/base::layouts.copyright', ['year' => now()->format('Y'), 'company' => setting('admin_title', config('core.base.general.base_name')), 'version' => get_cms_version()])) !!}
                 {{-- @Cutomized Vijayaragavan.Ambalam - Start --}}
<!--                | Powered by <a href="{{ config('core.base.general.powered_url') }}" target="_blank"><img src={{ url(config('core.base.general.powered_logo')) }}></a>-->
                {{-- @Cutomized Vijayaragavan.Ambalam - End --}}
            </div>
            <div class="col-md-4 text-right">
                @if (defined('LARAVEL_START')) {{ trans('core/base::layouts.page_loaded_time') }} {{ round((microtime(true) - LARAVEL_START), 2) }}s @endif
            </div>
        </div>
    </div>
    <div class="scroll-to-top">
        <i class="icon-arrow-up-circle"></i>
    </div>
</div>
 {{-- @Cutomized Ramesh Esakki - Start --}}
 <?php 
    $pwdCriteria = \Session::get('criteria');
    if ($pwdCriteria && isset($pwdCriteria->auto_logout) && $pwdCriteria->auto_logout && is_plugin_active('password-criteria')) {
        echo "<script>var idleSessionCheckConfig = ".setting('session_idle_check_time').";</script>";
        echo "<script type='text/javascript' src='/vendor/core/plugins/password-criteria/js/session_idle_time_check.js'></script>";
    }
?>


{{-- @Cutomized Ramesh Esakki - End --}}
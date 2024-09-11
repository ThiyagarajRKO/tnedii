@php
$languages = Language::getActiveLanguage(['lang_id', 'lang_name', 'lang_code', 'lang_flag']);
@endphp

@if (count($languages) > 1)
<span class="admin-list-language-chooser">
    <span>{{ trans('plugins/language::language.translations') }}: </span>
    @foreach ($languages as $language)
    @if ($language->lang_code !== Language::getCurrentAdminLocaleCode())
    <span>
        {!! language_flag($language->lang_flag, $language->lang_name) !!}
        {{-- Customized by Vijayaragavan.Ambalam Start--}}
        @if (is_plugin_active('multidomain'))
        <a
            href="{{ route($route, $language->lang_code == Language::getDefaultLocaleCode() ? ['ref_domain' => Multidomain::getCurrentAdminDomainId()] : ['ref_lang' => $language->lang_code, 'ref_domain' => Multidomain::getCurrentAdminDomainId()]) }}">{{ $language->lang_name }}</a>
        @else
        <a
            href="{{ route($route, $language->lang_code == Language::getDefaultLocaleCode() ? [] : ['ref_lang' => $language->lang_code]) }}">{{ $language->lang_name }}</a>
        @endif
        {{-- Customized by Vijayaragavan.Ambalam End--}}
    </span>&nbsp;
    @endif
    @endforeach
    <input type="hidden" name="ref_lang" value="{{ request()->input('ref_lang') }}">
</span>
@endif
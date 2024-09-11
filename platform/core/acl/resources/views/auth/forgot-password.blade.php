@extends('core/acl::auth.master')

@section('content')
{{-- @Customized  Sabari Shankar Parthiban - Start --}}
<div class="login-logo" style="text-align:center;">
    <!-- <img src="/storage/emircom_logo.png" alt="logo" > -->
    <!--<img src="/storage/editn_logo.png" alt="logo" >-->
</div>
<br>
    <p>{{ trans('core/acl::auth.forgot_password.title') }}</p>
    {!! Form::open(['route' => 'access.password.email', 'class' => 'forget-form']) !!}
        <p>{!! clean(trans('core/acl::auth.forgot_password.message')) !!}</p>
    <br>
        <div class="form-group" id="emailGroup">
            <label>{{ trans('core/acl::auth.login.email') }}</label>
            {!! Form::text('email', old('email'), ['class' => 'form-control', 'placeholder' => trans('core/acl::auth.login.email')]) !!}
        </div>
        {{-- @Customized  Haritha Murugavel - Start --}}
		        {!! do_action(RENDER_CAPTCHA_FIELD,'advanced')!!}
        {{-- @Customized  Haritha Murugavel - End --}}

        <button type="submit" class="btn btn-block login-button">
            <span class="signin">{{ trans('core/acl::auth.forgot_password.submit') }}</span>
        </button>
        <div class="clearfix"></div>

        <br>
        <p><a class="lost-pass-link" href="{{ route('access.login') }}">{{ trans('core/acl::auth.back_to_login') }}</a></p>
    {!! Form::close() !!}
@stop
@push('footer')
    <script>
        var email = document.querySelector('[name="email"]');
        email.focus();
        document.getElementById('emailGroup').classList.add('focused');

        // Focus events for email and password fields
        email.addEventListener('focusin', function(){
            document.getElementById('emailGroup').classList.add('focused');
        });
        email.addEventListener('focusout', function(){
            document.getElementById('emailGroup').classList.remove('focused');
        });
        {{-- @Customized  Haritha Murugavel - Start --}}
		$('#refresh-captcha').click(function () {
        $.ajax({
            type: 'GET',
            url: '/refresh-captcha',
            success: function (data) {
                $(".customcaptcha span").html(data.customcaptcha);
            }
        });
    });
    {{-- @Customized  Haritha Murugavel - End --}}
    </script>
@endpush


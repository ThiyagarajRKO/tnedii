@extends('core/acl::auth.master')
@section('content')
     {{-- @Customized  Haritha Murugavel - Start --}}
	<div class="login-logo" style="text-align:center;">
		<!-- <img src="/storage/emircom_logo.png" alt="logo" > -->
<!--		<img src="/storage/editn_logo.png" alt="logo" >-->
	</div>
<br>
{{-- @Customized  Haritha Murugavel - End --}}
<h5><b>{{ trans('core/acl::auth.sign_in_below') }}</b></h5>
{!! Form::open(['route' => 'access.login', 'class' => 'login-form']) !!}
<label class="loginlabel">{{ trans('core/acl::auth.login.username') }}</label>
<div class="form-group" id="emailGroup">

    {!! Form::text('username', request()->input('email', old('username', app()->environment('demo') ? config('core.base.general.demo.account.username', 'impiger') : null)), ['class' => 'form-control username', 'placeholder' => trans('core/acl::auth.login.username')]) !!}
    <i class="fa fa-user position-absolute"></i>
</div>
<label class="loginlabel">{{ trans('core/acl::auth.login.password') }}</label>
<div class="form-group" id="passwordGroup">
    {!! Form::input('password', 'password', request()->input('email') ? null : (app()->environment('demo') ? config('core.base.general.demo.account.password', '159357') : null), ['class' => 'form-control password', 'placeholder' => trans('core/acl::auth.login.password')]) !!}
    <i class="fa fa-lock fa-lg position-absolute font-14"></i>
</div>
<p><div class="forgetpassword loginlabel"><a class="lost-pass-link" href="{{env('APP_URL')}}/admin/password/reset" title="Forgot Password?">Forgot Password?</a></div></p>
<div>
</div>
{{-- @Customized  Haritha Murugavel - Start --}}
{!! do_action(RENDER_CAPTCHA_FIELD,'advanced')!!}
{{-- @Customized  Haritha Murugavel - End --}}

<button type="submit" class="btn btn-block login-button w-100">
    <span class="signin">{{ trans('core/acl::auth.login.login') }}</span>
</button>
<div class="clearfix"></div>



{!! apply_filters(BASE_FILTER_AFTER_LOGIN_OR_REGISTER_FORM, null, \Impiger\ACL\Models\User::class) !!}

{!! Form::close() !!}
@stop
@push('footer')
<script>
    var username = document.querySelector('[name="username"]');
    var password = document.querySelector('[name="password"]');
    username.focus();
    document.getElementById('emailGroup').classList.add('focused');

    // Focus events for email and password fields
    username.addEventListener('focusin', function() {
        document.getElementById('emailGroup').classList.add('focused');
    });
    username.addEventListener('focusout', function() {
        document.getElementById('emailGroup').classList.remove('focused');
    });

    password.addEventListener('focusin', function() {
        document.getElementById('passwordGroup').classList.add('focused');
    });
    password.addEventListener('focusout', function() {
        document.getElementById('passwordGroup').classList.remove('focused');
    }); {
        {
            // --@Customized Haritha Murugavel - Start--
        }
    }
    $('#refresh-captcha').click(function() {
        $.ajax({
            type: 'GET',
            url: '/refresh-captcha',
            success: function(data) {
                $(".customcaptcha span").html(data.customcaptcha);
            }
        });
    }); {
        {
            // --@Customized Haritha Murugavel - End--
        }
    }
    
</script>
@endpush
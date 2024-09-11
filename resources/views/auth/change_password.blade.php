@extends('core/acl::auth.master')
@section('content')
     {{-- @Customized  Haritha Murugavel - Start --}}
	<div class="login-logo" style="text-align:center;">
		<img src="/storage/emircom_logo.png" alt="logo" >
	</div>
<br>
    <p>{{ trans('core/acl::auth.reset.new_password') }}</p>
    {!! Form::open(['route' => ['users.change-password',$user_id], 'class' => 'login-form','id'=>'password-form']) !!}

    <div class="form-group has-feedback{{ $errors->has('password') ? ' has-error' : '' }}" id="passwordGroup">
        <label>{{ trans('core/acl::auth.reset.new_password') }}</label>
        {!! Form::password('password', ['class' => 'form-control', 'id'=>'password','placeholder' => trans('core/acl::auth.reset.new_password')]) !!}
    </div>

    <div class="form-group has-feedback{{ $errors->has('password_confirmation') ? ' has-error' : '' }}" id="passwordConfirmationGroup">
        <label>{{ trans('core/acl::auth.password_confirmation') }}</label>
        {!! Form::password('password_confirmation', ['class' => 'form-control', 'placeholder' => trans('core/acl::auth.reset.password_confirmation')]) !!}
    </div>

    <button type="submit" class="btn btn-block login-button w-100">
        <input type="hidden" name="force_change" value="1">
        <span class="signin">{{ trans('core/acl::auth.forgot_password.submit') }}</span>
    </button>
    <div class="clearfix"></div>
    {{-- @Customized  Sabari Shankar - Start --}}
    <br>
        @if(is_plugin_active('password-criteria'))@php $criteria = session()->get('criteria'); @endphp
        <p class="criteria">{!! clean(trans('core/acl::auth.forgot_password.criteria', ['min_length' =>$criteria->min_length, 'upper_case' =>$criteria->alphabet_count ,'numeric' =>$criteria->number_min_count , 'spl_char' =>$criteria->special_char_count,'history'=>$criteria->reuse_after_x_times ])) !!}</p>
        @endif
        {{-- @Customized  Sabari Shankar - End --}}
    {!! Form::close() !!}
@stop

@push('footer')
    <script>
        var password = document.querySelector('[name="password"]');
        var passwordConfirmation = document.querySelector('[name="password_confirmation"]');
        password.focus();

        password.addEventListener('focusin', function(){
            document.getElementById('passwordGroup').classList.add('focused');
        });
        password.addEventListener('focusout', function(){
            document.getElementById('passwordGroup').classList.remove('focused');
        });

        passwordConfirmation.addEventListener('focusin', function(){
            document.getElementById('passwordConfirmationGroup').classList.add('focused');
        });
        passwordConfirmation.addEventListener('focusout', function(){
            document.getElementById('passwordConfirmationGroup').classList.remove('focused');
        });
        ImpigerVariables.authorized = 1;
    </script>
@endpush

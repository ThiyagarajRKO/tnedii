@extends('core/base::layouts.master')
@section('content')
{!! Form::open(['id'=>"pwd_criteria_form"]) !!}
<input type='hidden' class="form-control" name="id">
<div class="wrapper-content pd-all-20">

    <div class="row">
        <label for="note" class="col-form-label col-lg-2 col-sm-12">
            <div class="form-label-title mt-5">Password Length</div>
        </label>
        <div class="form-group col-lg-2 col-md-12 col-sm-12"></div>
        <div class="col-lg-4 col-md-12 col-sm-12">
            <label class="col-form-label required">Minimum Length</label>
            <input  type="text" class="kt_touchspin form-control bootstrap-touchspin-vertical-btn" value="" name="min_length" id="min_length">
        </div>
        <div class="form-group col-lg-4 col-md-12 col-sm-12">
            <label class="col-form-label  required">Maximum Length</label>
            <input type="text" class="kt_touchspin form-control bootstrap-touchspin-vertical-btn" value="" name="max_length">
        </div>
    </div>
    <div class="row">
        <label for="note" class="col-form-label col-lg-2 col-md-9">
            <div class="form-label-title mt-5">Alphabet</div>
        </label>
        <div class="col-lg-2 col-md-3 col-sm-12 mt-5 text-right">
            <input data-switch="true" type="checkbox" name="has_alphabet" >
        </div>
        <div class="form-group col-lg-4 col-md-12 col-sm-12">
            <label class="col-form-label">Minimum Length</label>
            <input  type="text" class="kt_touchspin form-control bootstrap-touchspin-vertical-btn" value="" name="alphabet_count">
        </div>
        <div class="form-group col-lg-4 col-md-12 col-sm-12">
            <label class="col-form-label">Alphabet Type</label>
            <div class="ui-select-wrapper form-group">
                <select class="form-control select-full ui-select" name="alphabet_type" multiple >
                    <option value="1">Upper Case</option>
                    <option value="2">Lower Case</option>
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <label for="note" class="col-form-label col-lg-2 col-md-9">
            <div class="form-label-title mt-5">Numbers</div>
        </label>
        <div class="col-lg-2 col-md-3 col-sm-12 mt-5 text-right">
            <input data-switch="true" type="checkbox" name="has_number">
        </div>
        <div class="form-group col-lg-4 col-md-12 col-sm-12">
            <label class="col-form-label">Minimum Length</label>
            <input type="text" class="kt_touchspin form-control bootstrap-touchspin-vertical-btn" value="{{$data['min_number_count']}}" name="number_min_count">
        </div>
    </div>
    <div class="row">
        <label for="note" class="col-form-label col-lg-2 col-md-9">
            <div class="form-label-title mt-5">Special Characters</div>
        </label>
        <div class="col-lg-2 col-md-3 col-sm-12 mt-5 text-right">
            <input data-switch="true" type="checkbox" name="has_special_char" >
        </div>
        <div class="form-group col-lg-4 col-md-12 col-sm-12">
            <label class="col-form-label">Minimum Length</label>
            <input type="text" class="kt_touchspin form-control bootstrap-touchspin-vertical-btn" value="" name="special_char_count">
        </div>
        <div class="form-group col-lg-4 col-md-12 col-sm-12">
            <label class="col-form-label">Allowed Characters</label>
            <div class="ui-select-wrapper form-group">
                <select class="form-control select-full ui-select" name="allowed_spec_char">
                    <option value="">Select Allowed Characters</option>
                    @foreach($data['allowed_special_char'] as $k => $v)
                    <option value="{{$k}}" >{{$v}}</option>
                    @endforeach

                </select>
                <svg class="svg-next-icon svg-next-icon-size-16">
                <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#select-chevron"></use>
                </svg>   
            </div>
        </div>
    </div>
    <div class="row">
        <label for="note" class="col-form-label col-lg-2 col-md-9">
            <div class="form-label-title mt-5">Password Expiry Days</div>
        </label>
        <div class="col-lg-2 col-md-3 col-sm-12 mt-5 text-right">
            <input data-switch="true" type="checkbox" name="has_pwd_expiry" >
        </div>
        <div class="form-group col-lg-4 col-md-12 col-sm-12">
            <label class="col-form-label">No. of days</label>
            <input type="text" class="kt_touchspin form-control bootstrap-touchspin-vertical-btn" value="" name="validity_period">
        </div>
    </div>
    <div class="row">
        <label for="note" class="col-form-label col-lg-2 col-md-9">
            <div class="form-label-title mt-4">Reuse Old Password After This Many Password Changes</div>
        </label>
        <div class="form-group col-lg-2 col-md-3 col-sm-12 mt-5 text-right">
            <input data-switch="true" type="checkbox" name="reuse_pwd" >
        </div>
        <div class="form-group col-lg-4 col-md-12 col-sm-12">
            <label class="col-form-label">Select No</label>
            <input type="text" class="kt_touchspin form-control bootstrap-touchspin-vertical-btn" value="" name="reuse_after_x_times" >
        </div>
    </div>
    <div class="row">
        <label for="note" class="col-form-label col-lg-2 col-md-9">
            <div class="form-label-title mt-5">Auto Lock</div>
        </label>
        <div class="form-group col-lg-2 col-md-3 col-sm-12 mt-5 text-right">
            <input data-switch="true" type="checkbox" id="auto_lock" name="auto_lock" >
        </div>
        <div class="form-group col-lg-4 col-md-12 col-sm-12">
            <label class="col-form-label">Select No. of. Times</label>
            <input type="text" class="kt_touchspin form-control bootstrap-touchspin-vertical-btn" value="" name="invalid_attempt_allowed_time" >
        </div>
    </div>
    <div class="row">
        <label for="note" class="col-form-label col-lg-2 col-md-9">
            <div class="form-label-title mt-5">Auto Unlock</div>
        </label>
        <div class="form-group col-lg-2 col-md-3 col-sm-12 mt-5 text-right">
            <input data-switch="true" type="checkbox" name="auto_unlock" id="auto_unlock">
        </div>
        <div class="form-group col-lg-4 col-md-12 col-sm-12">
            <label class="col-form-label">Select Timing</label>
            <div class="ui-select-wrapper form-group">
                <select class="form-control select-full ui-select" name="unlock_format" id="unlock_format">
                    <option></option>
                    <option value="Hour">Hours</option>
                    <option value="Minute">Minutes</option>
                </select>
                <svg class="svg-next-icon svg-next-icon-size-16">
                <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#select-chevron"></use>
                </svg>
            </div>
        </div>
        <div class="form-group col-lg-4 col-md-12 col-sm-12">
            <label class="col-form-label">Select Duration</label>
            <div class="ui-select-wrapper form-group">
                <select class="form-control select-full ui-select" name="unlock_time" id="unlock_time">
                    <option></option>
                </select>
                <svg class="svg-next-icon svg-next-icon-size-16">
                <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#select-chevron"></use>
                </svg>
            </div>
        </div>

    </div>
    <div class="row">
        <label for="note" class="col-form-label col-lg-2 col-md-9">
            <div class="form-label-title mt-5">Auto Logout</div>
        </label>
        <div class="form-group col-lg-2 col-md-3 col-sm-12 mt-5 text-right">
            <input data-switch="true" type="checkbox" name="auto_logout" id="auto_logout">
        </div>
        <div class="form-group col-lg-4 col-md-12 col-sm-12">
            <label class="col-form-label">Select Timing</label>
            <div class="ui-select-wrapper form-group">
                <select class="form-control select-full ui-select" name="logout_format" id="logout_format">
                    <option></option>
                    <option value="Hour">Hours</option>
                    <option value="Minute">Minutes</option>
                </select>
                <svg class="svg-next-icon svg-next-icon-size-16">
                <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#select-chevron"></use>
                </svg>
            </div>
        </div>
        <div class="form-group col-lg-4 col-md-12 col-sm-12">
            <label class="col-form-label">Select Duration</label>
            <div class="ui-select-wrapper form-group">
                <select class="form-control select-full ui-select" name="logout_time" id="logout_time">
                    <option></option>
                </select>
                <svg class="svg-next-icon svg-next-icon-size-16">
                <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#select-chevron"></use>
                </svg>
            </div>

        </div>
        <div class="max-width-1200">
            <div class="flexbox-annotated-section" style="border: none">
                <div class="flexbox-annotated-section-annotation">
                    &nbsp;
                </div>
                <div class="flexbox-annotated-section-content">
                    <button class="btn btn-info" type="submit">{{ trans('core/setting::setting.save_settings') }}</button>
                </div>
            </div>
        </div>
    </div>
    {!! Form::close() !!}

    @endsection
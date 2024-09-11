<?php
$type = (Arr::get($options, 'type')) ? Arr::get($options, 'type') : "text";
$textAlign = (Arr::get($options, 'text-align')) ? ' text-'.Arr::get($options, 'text-align') : ' text-left';
$textAlign = ($type == 'number') ? ' text-right' : $textAlign;
?>

<div class="form-group">
    @if ($type == 'text' || $type == 'number')
        <input type="{{$type}}" class="inline-input{{$textAlign}} input-sm" name="{{$field}}[]" value="{{ Arr::get($options, 'value') }}" <?php if(Arr::get($options, 'readOnly') == 1) { echo "readOnly";} ?>/>
    @elseif ($type == 'text_date')
        <input type="date" class="inline-input{{$textAlign}}"  name="{{$field}}[]" value="{{ Arr::get($options, 'value') }}">
    @elseif ($type == 'text_datetime')
    <input type="datetime" class="inline-input{{$textAlign}}"  name="{{$field}}[]" value="{{ Arr::get($options, 'value') }}">
    @elseif ($type == 'textarea')
        <textarea class="inline-input"  name="{{$field}}[]" value="{{ Arr::get($options, 'value') }}"> {{ Arr::get($options, 'value') }} </textarea>
    @elseif ($type == 'checkbox')   
        <div class="checkbox checkbox-primary table-checkbox">
            <input type="checkbox" class="custom-checkboxes checkbox-{{$field}}" name="{{$field}}[]" value="{{ Arr::get($options, 'value') }}"/>
        </div>
    @elseif ($type == 'radio')   
    <div class="checkbox checkbox-primary table-checkbox">
        <input type="radio" class="custom-radio radio-{{$field}}" name="{{Arr::get($options, 'key')}}[]" data-key="{{Arr::get($options, 'key')}}" <?php if(Arr::get($options, 'value') == 1) { echo "checked";} ?>  value="{{ Arr::get($options, 'value') }}"/>
    </div>
    @elseif ($type == 'select')   
        <select class="form-control form-control-sm" name="{{$field}}[]">
            <option>Select</option>
            @if(Arr::has($options, 'choices')) 
                @foreach(Arr::get($options, 'choices') as $key => $val)
                    <option value="{{ $key }}" 
                    @if($key == Arr::get($options, 'value') ) selected @endif>{{ $val }} </option>
                @endforeach
            @endif
        </select>
    @elseif ($type == 'hidden')
    <input type="hidden"  class="inline-input{{$textAlign}}" name="{{$field}}[]" value="{{ Arr::get($options, 'value') }}" readonly="" style="border:none;width:30px"/>
    @endif
</div>

<div class="form-group multi-file-upload-container col-md-4 {{ $options['wrapper']['class'] }}">
    @if($options['label_show'])
    <label for="{{ $name }}" class="control-label ">{{ $options['label'] }}</label>
    @endif
    
    {!! Form::hidden($name, $options['value'], ['id' => $name, 'class' => 'form-control', 'data-sub_folder' => 'knowledge-partner']) !!}
    <div class="list-photos-gallery">
        <div class="row">
            
        </div>
    </div>
    <div class="clearfix"></div>
</div>

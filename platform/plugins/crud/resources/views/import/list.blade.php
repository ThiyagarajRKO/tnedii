@extends('core/table::table')
@section('main-table')
    {!! Form::open(['url' => route($uploadRoute) , 'class' => 'bulk-upload','enctype'=>"multipart/form-data" ] ) !!}
<!--        <input type="file"  class="hidden" id="import_json">-->
        @parent
        @include("plugins/crud::import.import-modal")
    {!! Form::close() !!}
    @include("plugins/crud::import.import-response-modal")
    @include("plugins/crud::import.certificate-modal")
    
@stop

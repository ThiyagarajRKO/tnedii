@if(Session::has('modal'))
<div class="modal fade" data-keyboard="false" data-backdrop="static" tabindex="-1"
     role="dialog" id="errorResponse" aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog ">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><i class="til_img"></i><strong>{{trans('plugins/crud::crud.bulk_upload_error')}}</strong></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="{{ trans('core/media::media.close') }}"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <label>{{trans('core/base::notices.bulk_upload_error_message')}} </label> 
                <a href="{{asset('storage/'.Session::get('modal')['filePath'])}}" download>{{Session::get('modal')['fileName']}}</a>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-info" data-dismiss="modal" aria-label="{{ trans('core/media::media.close') }}">{{ trans('core/media::media.close') }}</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<script>
    $("#errorResponse").modal();
</script>
@endif
<script>
    let templatePath = "{{asset('storage/bulk_upload_templates')}}";
    let templateName = "{{$template}}";
</script>

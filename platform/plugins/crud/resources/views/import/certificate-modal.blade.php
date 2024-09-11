@if($template == 'trainee')
<div class="modal fade" data-keyboard="false" data-backdrop="static" tabindex="-1"
     role="dialog" id="certificateTemplate" aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog ">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><i class="til_img"></i><strong>{{trans('plugins/crud::crud.choose_templete')}}</strong></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="{{ trans('core/media::media.close') }}"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
               <div class="form-group">
					<select name="certificate_template" id="certificate_template">
						<option value="1">Template Old</option>
						<option value="2">Template New</option>
					</select>
					<input type="hidden" name="clicked_button_data" id="clicked_button_data" value="">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-info" data-dismiss="modal" aria-label="{{ trans('core/media::media.close') }}">{{ trans('core/media::media.close') }}</button>				
                <button type="button" class="btn btn-primary submit_choosen_templete">{{ trans('plugins/crud::crud.submit') }}</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
@endif
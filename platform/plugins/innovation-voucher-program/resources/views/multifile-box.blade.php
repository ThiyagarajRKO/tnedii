<div class="form-group multi-file-upload-container col-md-8">
    <label class="control-label">Attachments</label>
{!! Form::hidden('attachments', null, ['id' => 'attachments', 'class' => 'form-control']) !!}
    <div class="list-photos-gallery">
        <div class="row" id="list-photos-items">
            
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="form-group image-box-actions">
        <a href="#" class="btn_select_file">Choose Files</a>&nbsp;
        <a href="#" class="text-danger reset-gallery   ">Reset</a>
    </div>
</div>

<div  class="modal fade delete-file-item" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h4 class="modal-title"><i class="til_img"></i><strong>Confirm</strong></h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>

            <div class="modal-body with-padding">
                <p>Are you sure want to delete/remove the file?</p>
            </div>

            <div class="modal-footer">
                <button class="float-left btn btn-danger" id="delete-gallery-item" href="#">delete</button>
                <button class="float-right btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
<!-- end Modal -->

@php $notes = config('importConfigs.notes', []); @endphp
<div class="modal fade" data-keyboard="false" data-backdrop="static" tabindex="-1"
     role="dialog" id="bulkUpload" aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog ">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><i class="til_img"></i><strong>{{trans('plugins/crud::crud.bulk_upload')}}</strong></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="{{ trans('core/media::media.close') }}"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="form-group">    
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 ">
                            <div class="help-block">
                                <div class="text-info">Notes:</div>
                                <ul class="p-0">
                                    @if(Arr::has($notes,'default')) 
                                        @foreach($notes['default'] as $note)
                                            <li>{{$note}}</li>
                                        @endforeach
                                    @endif
                                    @if(Arr::has($notes,$template))
                                    @foreach($notes[$template] as $note)
                                        <li>{{$note}}</li>
                                    @endforeach
                                    @endif 
                                    
                                </ul>
                            </div>
                        </div>
                    </div>                
                   @if(file_exists(public_path('storage/bulk_upload_templates/xls/'.$template . '.xls')))
                    <div class="col-md-12 form-group">
                        <a href='{{asset('storage/bulk_upload_templates')}}/xls/{{$template}}.xls'>
                            <div class="input-group">
                                <input type='text' value='{{$template}}.xls' class='form-control' readonly>
                                <span class="input-group-prepend">
                                    <button class="btn default" type="button">
                                        <i class="fa fa-download"></i>
                                    </button>
                                </span>
                            </div>
                        </a>
                    </div>
                  @endif
                </div>

                <div class="form-group">
                    <div class="custom-file">
                        <input type="file" name="file" class="custom-file-input @error('file') is-invalid @enderror" id="chooseFile">
                        <label class="custom-file-label" for="chooseFile">Select file</label>
                        @error('file')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-info" data-dismiss="modal" aria-label="{{ trans('core/media::media.close') }}">{{ trans('core/media::media.close') }}</button>
                <button type="submit" class="btn btn-primary">{{ trans('core/media::media.upload') }}</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
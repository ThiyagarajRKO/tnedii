@php $optoutOption= getOptoutOptions($studentId);

@endphp
<div class="modal fade" id="optOutModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <form id='optOutForm'>
            <input type="hidden" name="id" id="id" value="{{$studentId}}">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Student Opt Out</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <div class="modal-body">
					<div class="alert alert-danger" id="error_msg" role="alert" style="display:none">
					</div>

					<div class="form-group">
						<label>Opt Out Option<span class="required_label"></span>:
						</label>
						<div class="kt-radio-inline">
							@foreach( $optoutOption as $option)
							
								<label class="kt-radio">
									<input type="radio" name="optOutType" value="{{$option['id']}}"> {{$option['name']}} 
									<span>
									</span>
								</label>
								<br>	
							
@endforeach
							
						
													</div>
					</div>

          <div class="form-group mt-2">
              <label for="description" class="form-control-label">Reason<span class="required_label"></span>:</label>
							<textarea maxlength="500" class="form-control" rows="3" cols="100" name="reason"></textarea>
					</div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-success">Save</button>
            </div>
            </form>
        </div>

</div>
</div>




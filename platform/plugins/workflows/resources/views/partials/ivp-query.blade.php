<div class="form-group">
    
    <label for="query" class="control-label  required">{{$trans['name']}}</label>
    <input type="hidden" name="custom_input[0][field]" value="{{$trans['to_state']}}">
    <input type="hidden" name="custom_input[0][validation]" value="required">
    <textarea rows="4" data-field_index="1" required="required" name="custom_input[req][{{$trans['to_state']}}]" cols="50" id="query" class="form-control" aria-required="true"></textarea>

</div>
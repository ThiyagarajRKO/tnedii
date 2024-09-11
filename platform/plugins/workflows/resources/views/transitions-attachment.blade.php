<br/>
@php 
$pdf=true;$csv=$xls=false;$content="";
if(!empty($configs[$i])){
$pdf = ($configs[$i][0]['attachment_type'] == 'pdf') ? true : false;
$csv = ($configs[$i][0]['attachment_type'] == 'csv') ? true : false;
$xls = ($configs[$i][0]['attachment_type'] == 'xls') ? true : false;
$content = ($configs[$i][0]['attachment_content']) ?:"";
}
@endphp

<div class="subFormRepeater" id="repeater_{{$i}}" data-key='{{$k}}'>
    <div class="form-group" >
        <label for="configs[attachment_type][0]" class="control-label">Attachment Type &nbsp;   :  &nbsp;</label>

        <label class="checkbox-inline">
            {!! Form::radio("configs[0][$k][attachment_type]", 'pdf',$pdf) !!} PDF
        </label>&nbsp;
        <label class="checkbox-inline">
            {!! Form::radio("configs[0][$k][attachment_type]", 'csv',$csv) !!} CSV
        </label>&nbsp;
        <label class="checkbox-inline">
            {!! Form::radio("configs[0][$k][attachment_type]", 'xls',$xls) !!} Excel
        </label>
        <br/>
        <!--<label for="configs[attachment_content]" class="control-label">Attachment Template &nbsp;   :  &nbsp;</label>-->
        <div class='width-45'>
            <select class='select-full' name='attachment_field[0]'>
                <option value=''> Select</option>
                @foreach ($moduleFields as $key => $field)
                <option value='{{$field}}'> {{$field}}</option>        
                @endforeach
            </select>
        </div>
        <textarea class="form-control mt-10" rows="4" name="configs[0][{{$k}}][attachment_content]" cols="20">{{$content}}</textarea>

    </div>
    @php 
    $pdf=true;$csv=$xls=false;$content="";
    if(!empty($configs[$i]) && count($configs[$i]) > 1){
    foreach($configs[$i] as $key =>$value){
    if($key!=0){
    $pdf = ($configs[$i][$key]['attachment_type'] == 'pdf') ? true : false;
    $csv = ($configs[$i][$key]['attachment_type'] == 'csv') ? true : false;
    $xls = ($configs[$i][$key]['attachment_type'] == 'xls') ? true : false;
    $content = ($configs[$i][$key]['attachment_content']) ?:"";
    }
    }
    @endphp
    <div class="form-group" name='attachment[{{$k}}]'>
        <label for="configs[attachment_type][{{$key}}]" class="control-label">Attachment Type &nbsp;   :  &nbsp;</label>

        <label class="checkbox-inline">
            {!! Form::radio("configs[$key][$k][attachment_type]", 'pdf',$pdf) !!} PDF
        </label>&nbsp;
        <label class="checkbox-inline">
            {!! Form::radio("configs[$key][$k][attachment_type]", 'csv',$csv) !!} CSV
        </label>&nbsp;
        <label class="checkbox-inline">
            {!! Form::radio("configs[$key][$k][attachment_type]", 'xls',$xls) !!} Excel
        </label>
        <br/>
        <!--<label for="configs[attachment_content]" class="control-label">Attachment Template &nbsp;   :  &nbsp;</label>-->
        <div class='width-45'>
            <select class='select-full' name='attachment_field[{{$key}}]'>
                <option value=''> Select</option>
                @foreach ($moduleFields as $k => $field)
                <option value='{{$field}}'> {{$field}}</option>        
                @endforeach
            </select>
        </div>
        <textarea class="form-control mt-10" rows="4" name="configs[{{$key}}][{{$k}}][attachment_content]" cols="20">{{$content}}</textarea>
    </div>
    @php } @endphp
</div>
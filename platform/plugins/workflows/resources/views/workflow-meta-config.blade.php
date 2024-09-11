<div class="max-width-1200">
    <div class="flexbox-annotated-section">
        <div class="flexbox-annotated-section-annotation">

            <div class="annotated-section-title">
                <legend class="grouppedLegend">Meta configuration</legend>
            </div>

        </div>

        <div class="flexbox-annotated-section-content">
            <div class="subFormRepeater ">
                @if($metaData->count())
                @foreach($metaData as $key => $row)
                    <div class="form-group">
                    <div class="col-md-12 grouppedLayout workflow_meta_data">
                        <fieldset>
                            <div class="row">
                                <input type='hidden' name='workflow_meta_data[{{$key}}][id]' value='{{$row->id}}'>
                                <div class="form-group col-md-4">
                                    <label for="workflow_meta_data[{{$key}}][transition_name]" class="control-label  ">State Group Name</label>
                                    <input class="form-control" data-field_index="3" name="workflow_meta_data[{{$key}}][transition_name]" type="text" id="workflow_meta_data[{{$key}}][transition_name]" value='{{$row->transition_name}}'>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="workflow_meta_data_data[{{$key}}][meta_data][]" class="control-label  ">State Group</label>
                                    <select class="select-full ui-select ui-select select2-hidden-accessible" data-field_index="31" multiple="" id="workflow_meta_data[{{$key}}][meta_data]" name="workflow_meta_data[{{$key}}][meta_data][]" tabindex="-1" aria-hidden="true">
                                        @foreach($workflowStates as $key => $value)
                                        <option value='{{$value}}' @if(in_array($value,$row->meta_data)) selected @endif>{{ucfirst($value)}}</option>
                                        @endforeach
                                        
                                    </select>    
                                </div>
                               
                                <span class="layoutDisplayType" data-display_type="vertical"></span> 

                            </div></fieldset></div>
                </div>
                @endforeach
                @else
                <div class="form-group">
                    <div class="col-md-12 grouppedLayout workflow_meta_data">
                        <fieldset>
                            <div class="row">                                
                                <div class="form-group col-md-4">
                                    <label for="workflow_meta_data[0][transition_name]" class="control-label  ">State Group Name</label>
                                    <input class="form-control" data-field_index="3" name="workflow_meta_data[0][transition_name]" type="text" id="workflow_meta_data[0][transition_name]">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="workflow_meta_data_data[0][meta_data][]" class="control-label  ">State Group</label>
                                    <select class="select-full ui-select ui-select select2-hidden-accessible" data-field_index="31" multiple="" id="workflow_meta_data[0][meta_data]" name="workflow_meta_data[0][meta_data][]" tabindex="-1" aria-hidden="true">
                                        @foreach($workflowStates as $key => $value)
                                        <option value='{{$value}}'>{{ucfirst($value)}}</option>
                                        @endforeach
                                    </select>    
                                </div>
                               
                                <span class="layoutDisplayType" data-display_type="vertical"></span> 

                            </div></fieldset></div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
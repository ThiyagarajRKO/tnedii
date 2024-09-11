<script>
responOptData('<?php echo $f['option']['opt_type'];?>');
responeFormType('<?php echo $f['type'];?>')	;	
$(document).ready(function(){
	$(".select2").select2({ width:"98%"});	
			
	$("#lookup_table").jCombo("{{ URL::to('admin/cruds/combotable') }}" , {
		selected_value : "<?php echo $f['option']['lookup_table'];?>" ,
		initial_text : ' Select Table',
		
	});

	$("#lookup_key").jCombo("{{ URL::to('admin/cruds/combotablefield') }}?table=",
	{ selected_value : "<?php echo $f['option']['lookup_key'];?>", parent: "#lookup_table", initial_text : ' Primary Key' });

	<?php $lv = explode("|", $f['option']['lookup_value']); ?>

	
	
		$("#lookup_value1").jCombo("{{ URL::to('admin/cruds/combotablefield') }}?table=",
		{ selected_value : "<?php echo (isset($lv[0]) ? $lv[0] : '');?>", parent: "#lookup_table",   initial_text : ' Display Text'}); 
		
		$("#lookup_value2").jCombo("{{ URL::to('admin/cruds/combotablefield') }}?table=",
		{ selected_value : "<?php echo (isset($lv[1]) ? $lv[1] : '');?>", parent: "#lookup_table",   initial_text : ' Display Text'}); 
		
		$("#lookup_value3").jCombo("{{ URL::to('admin/cruds/combotablefield') }}?table=",
		{ selected_value : "<?php echo (isset($lv[2]) ? $lv[2] : '');?>", parent: "#lookup_table",   initial_text : ' Display Text'}); 		
	
	$('a.addC').relCopy({});		

	
		
	});
	

	function responOptData( val) {
		//alert(val);
		if(val =='external') {
			$('#custom-value').attr('disabled','disabled');
			$('.ext').removeAttr('disabled','disabled');
			$('.database').show();$('.datalist').hide();
			$('.specific-entity').hide();
			
		} else if(val =='entity'){
			$('#custom-value').attr('disabled','disabled');
			$('.ext').attr('disabled','disabled');
			$('.specific-entity').show();
			$('.database').hide();$('.datalist').hide();
		} else if(val =='sameAsAbove'){
			$('#custom-value').attr('disabled','disabled');
			$('.ext').attr('disabled','disabled');
			$('.database').hide();$('.datalist').hide();
			$('.specific-entity').hide();
		}  else if(val =='customFunction'){
			$('#custom-value').attr('disabled','disabled');
			$('.ext').attr('disabled','disabled');
			$('.database').hide();$('.datalist').hide();
			$('.specific-entity').hide();
			$('.custom-function').show();
		} else {
			$('#custom-value').removeAttr('disabled');
			$('.ext').attr('disabled','disabled');	
			$('.database').hide();$('.datalist').show();
			$('.specific-entity').hide();
                        $('.custom-function').hide();
		}	
	}		
	
	function responeFormType(val)
	{
		$('#repeater_data').removeAttr('required');
		if(val =='select' || val =='radio' || val =='checkbox') {
			$('.sameAsAbove').hide();
			if(val == 'radio' || val =='checkbox')
			{
				$('.dbasevalue').hide()
				$('.entsevalue').hide()
				if(val =='checkbox') {
					$('.sameAsAbove').show();
				}
			} else {
				$('.dbasevalue').show()
				$('.entsevalue').show()
			}

			$('.dataOpt').removeAttr('disabled','disabled');
			$('.ext').removeAttr('disabled','disabled');	
			$('#custom-value').removeAttr('disabled','disabled');
			$('.standart-form').show(); $('.file-upl').hide();
			$('.database').hide();
			$('.datalist').hide();
			$('.specific-entity').hide();
			$('.file-upl').hide(); 
			$('.custom-function').hide(); 
			let selectedVal = $('[name="opt_type"]:checked').val();
			$('.datalist').hide();
			if(selectedVal == 'external') {
				$('.database').show();
			}  else if(selectedVal =='entity'){
				$('.specific-entity').show();
			}   else if(selectedVal =='datalist'){
				$('.datalist').show();
			}  
			  else if(selectedVal =='customFunction'){
				$('.custom-function').show();
			}  
			$('.repeaterConfig').hide(); 
		
		} else if( val == 'file') {
			$('.standart-form').hide(); 
			$('.repeaterConfig').hide(); 
			$('.file-upl').show();	
			
		} else if( val == 'repeater') {
			$('.standart-form').hide(); 
			$('#repeater_data').prop('required', true);
			$('.repeaterConfig').show();	
			
		} else if(val == 'textarea_editor'){
                        $('.standart-form').hide(); 
			$('.repeaterConfig').hide();
                        $('.editorConfig').show()
                }else {

			$('.ext').attr('disabled','disabled');	
			$('#custom-value').attr('disabled','disabled');
			$('.dataOpt').attr('disabled','disabled');
			$('.standart-form').hide(); 
			$('.database').hide();$('.datalist').hide();$('.file-upl').hide(); 
			$('.repeaterConfig').hide(); 
			$('.specific-entity').hide();
			$('.custom-function').hide();
                        
		}
	
	}	
</script>

 {!! Form::open(array('url'=>'admin/cruds/saveformfield/'.$module_name, 'class'=>'form-horizontal')) !!}
<input type="hidden" name="alias" value="<?php echo $f['alias'];?>" />
<input type="hidden" name="field" value="<?php echo $f['field'];?>" />	
<input type="hidden" name="label" value="<?php echo $f['label'];?>" />	
<input type="hidden" name="form_group" value="<?php echo $f['form_group'];?>" />	
<input type="hidden" name="sortlist" value="<?php echo $f['sortlist'];?>" />
<input type="hidden" name="view" value="<?php echo $f['view'];?>" />
<input type="hidden" name="search" value="<?php echo $f['search'];?>" />
<input type="hidden" name="required" value="<?php echo $f['required'];?>" />
<input type="hidden" name="limited" value="<?php echo (isset($f['limited']) ? $f['limited'] : '');?>" />

<input type="hidden" name="dbType" value="<?php echo $f['dbType'];?>" />
<input type="hidden" name="isNullable" value="<?php echo $f['isNullable'];?>" />
<input type="hidden" name="defaultVal" value="<?php echo $f['defaultVal'];?>" />
<input type="hidden" name="casting" value="<?php echo Arr::get($f, 'casting');?>" />
<div class="" style="padding:30px;">			
			
  <div class="form-group row">
    <label for="ipt" class=" control-label col-md-4">Form Type </label>
	<div class="col-md-8">

		<select name="type" id="type" onchange="responeFormType(this.value)" class="form-control form-control-sm">
		<?php foreach($field_type_opt as $val=>$item) { ?>
			<option  value="<?php echo $val;?>"
			<?php if($val == $f['type']) echo 'selected="selected"';?>
			> <?php echo $item;?></option>
		<?php } ?> 
		</select>
	  
	 </div> 
  </div>  

  <div class="form-group row">
    <label for="ipt" class=" control-label col-md-4">Casting </label>
	<div class="col-md-8">
		<input name="casting" type="text" id="casting" class="form-control form-control-sm" value="<?php echo Arr::get($f, 'casting');?>"/>
	 </div> 
  </div>
  
  <div class="form-group row standart-form"  style="display:none;">
    <label for="ipt" class=" control-label col-md-4">Data Type </label>
	<div class="col-md-8">
		<span class=" cstvalue" >
			<input type="radio" name="opt_type"  onclick="responOptData(this.value)"
			<?php if($f['option']['opt_type'] =='datalist') echo 'checked';?>
			 class="dataOpt" value="datalist" id="opt-datalist" /><label for="opt-datalist"> Custom Value </label>  
		</span>	
		<span class=" dbasevalue">
			<input type="radio" name="opt_type" onclick="responOptData(this.value)"
			<?php if($f['option']['opt_type'] =='external') echo 'checked';?>
			   class="dataOpt"  value="external" id="opt-external" /><label for="opt-external"> Database </label>
		</span>	
		<span class=" entsevalue">
			<input type="radio" name="opt_type" onclick="responOptData(this.value)"
			<?php if($f['option']['opt_type'] =='entity') echo 'checked';?>
			   class="dataOpt"  value="entity" id="opt-entity" /><label for="opt-entity"> Entity </label>
		</span>	
		<span class="sameAsAbove">
			<input type="radio" name="opt_type" onclick="responOptData(this.value)"
			<?php if($f['option']['opt_type'] =='sameAsAbove') echo 'checked';?>
			   class="dataOpt"  value="sameAsAbove" id="same-as-above" /><label for="same-as-above"> Same As Above </label>
		</span>	
                <span class="functionsevalue">
			<input type="radio" name="opt_type" onclick="responOptData(this.value)"
			<?php if($f['option']['opt_type'] =='customFunction') echo 'checked';?>
			   class="dataOpt"  value="customFunction" id="opt-function" /><label for="opt-function"> Custom Function </label>
		</span>
	  
	 </div> 
  </div> 
  
  <div class="form-group row standart-form specific-entity"  style="display:none;">
  <label for="ipt" class=" control-label col-md-4">Specific Entity Type </label>
	<div class="col-md-8">
		<select name="specific_entity_type" id="specific_entity_type"  class="form-control form-control-sm">
			<option  value="">Select</option>
			<?php 
			foreach($entity_modules as $val=>$entity) { ?>
				<option  value="<?php echo $entity->module_name;?>"
				<?php if($entity->module_name == Arr::get($f, 'option.specific_entity_type')) echo 'selected="selected"';?>
				> <?php echo $entity->module_name;?></option>
			<?php } ?> 
		</select>
	 </div> 
  </div>
    
  <div class="form-group row standart-form custom-function"  style="display:none;">
  <label for="ipt" class=" control-label col-md-4">Custom Function </label>
	<div class="col-md-8">
            <input type="text" name="custom_function" class="form-control form-control-sm col-xs-5" placeholder="Function Name" 
                   value="<?php if(Arr::get($f, 'option.custom_function')) echo $f['option']['custom_function'];?>"/>
	 </div> 
  <label for="ipt" class=" control-label col-md-4">Dependant Table </label>
	<div class="col-md-8">
		<input type="text" name="dependant_table" class="form-control form-control-sm col-xs-5" placeholder="Dependant Table|key" 
                       value="<?php if(Arr::get($f, 'option.dependant_table')) echo $f['option']['dependant_table'];?>"/>
	 </div> 
  </div>

  <div class="form-group row standart-form datalist"  style="display:none;">
    <label for="ipt" class=" control-label col-md-4">Custom Value </label>
	<div class="col-md-8 ">
		<div class="">
		<?php $opt = explode("|",$f['option']['lookup_query']); ?>
		<?php if(count($opt) <= 0) {?>
		<label class="clonedInput clone row" >
			
			<div class="col-xs-4">
			  <input type="text" name="custom_field_val[]" class="form-control form-control-sm col-xs-5"  placeholder="Value"  />
			</div>  
			<div class="col-xs-4">
			 <input type="text" name="custom_field_display[]" class="form-control form-control-sm col-xs-5" placeholder="Display Name" />
			</div> 		
			
		</label>
		<?php } else { 
			for($i=0; $i<count($opt);$i++) { $row =  explode(":",$opt[$i]); ?>
			<div class="clonedInput clone row" >
				<div class="col-xs-4">
				<input type="text" name="custom_field_val[]"  class="form-control form-control-sm col-xs-5" style="width:100px;" 
				placeholder="Value"  value="<?php if(isset($row[0])) echo $row[0];?>" />
				</div>
					<div class="col-xs-4">
				<input type="text" name="custom_field_display[]" class="form-control form-control-sm col-xs-5" style="width:100px;"
				placeholder="Display Name"  value="<?php if(isset($row[1])) echo $row[1];?>"/>
				</div>
				
				<a onclick="$(this).parent().fadeIn(function(){ $(this).remove() }); return false" href="#" class="remove btn btn-sm color-red">-</a>			
			</div>			
			<?php } ?>
		<?php } ?>
		<a href="javascript:void(0);" class="addC btn btn-sm color-green " rel=".clone">+</a>	
		</div>
		
		
	 </div> 
	 
  </div>    

  
  
  <div class="form-group row standart-form database" style="display:none;">
    <label for="ipt" class=" control-label col-md-4">DataBase Select</label>
	<div class="col-md-8">
		<label class="col-md-12">
			<label> Database Name : </label>
		<select name="lookup_table" id="lookup_table"  class="ext form-control form-control-sm" style="width:100%;">
			<option value=""> -- Select Table -- </option>
		<?php
			foreach($tables as $row) 
			{
				?> <option value="<?php echo $row;?>" <?php if($row == $f['option']['lookup_table']) 
					echo 'selected="selected"';?>><?php echo $row;?></option>';			 			
		<?php } ?>
		</select>	
		</label>
		<label class="col-md-12">
			<label> Primary Key / Relation Key  </label>
			<select name="lookup_key" id="lookup_key"  class="ext form-control form-control-sm" style="width:100%;"></select>
		</label>
		
			<div class="col-md-12">
				<label> Display #1 : </label>
				<select name="lookup_value[]"  class="ext form-control form-control-sm " id="lookup_value1"  
				style="  "></select> 
			</div>	

			<div class="col-md-12">
				<label> Display #2 : </label>
				<select name="lookup_value[]"  class="ext form-control form-control-sm" id="lookup_value2"  
				style="  "></select>

			</div>

			<div class="col-md-12">
				<label> Display #3 : </label>
				<select name="lookup_value[]"  class="ext form-control form-control-sm" id="lookup_value3"  
			style=" "></select> 
			</div>

			<div class="col-md-12">
				<label> SQL WHERE CONDITIONAL : </label>
				<textarea name="where_cndn" rows="2" id="where_cndn" class="form-control form-control-sm" placeholder="SQL Where Statement"><?php echo $f['option']['where_cndn']; ?></textarea>
			</div>
			
		


		<label class="col-md-12">
		<input type="checkbox" name="is_dependency" class="ext" value="1" <?php if($f['option']['is_dependency'] ==1) echo 'checked' ;?> /> Parent Filter  </label>
		<label class="col-md-12">				
		<input name="lookup_dependency_key" type="text" class="ext form-control form-control-sm" id="lookup_key" style=" border-bottom: solid 1px #ddd;"  
		value="<?php echo $f['option']['lookup_dependency_key'];?>"	placeholder='Lookup Filter Key' />
		</label>		
		<label class="col-md-12" >
		
			<input type="checkbox" name="select_multiple"  value="1" <?php if(isset($f['option']['select_multiple']) && $f['option']['select_multiple'] =='1') echo 'checked="checked"';?> class="filled-in" id="Allow_Multiple" />
			<label for="Allow_Multiple">Allow Multiple</label>

		</label>			
 
	 </div> 
  </div>  

  <div class="form-group row standart-form file-upl"  style="display:none;">
    <label for="ipt" class=" control-label col-md-4"> Upload File </code></label>
	<div class="col-md-8">
		<input name="path_to_upload" type="text" id="path_to_upload" class="form-control form-control-sm" value="<?php echo $f['option']['path_to_upload'];?>"/>
		<div class=""> 
			<input type="radio" name="upload_type" value="file" id="opt-file"
			<?php if($f['option']['upload_type'] =='file') echo 'checked="checked"';?>
			/>  
			<label for="opt-file" > File </label> 
		</div>
		<div class=""> 
			<input type="radio" name="upload_type" value="image"  id="opt-image"
			<?php if($f['option']['upload_type'] =='image') echo 'checked="checked"';?>
			 />  
			<label for="opt-image" > Image / Picture </label>  
		</div>
		
		<div class="imgResize form-inline">
			<h6> Resize Image to ? : </h6> 
			 
			<input name="resize_width" type="text" id="resize_width" class="form-control form-control-sm"  placeholder="Width"
			value="<?php if(isset($f['option']['resize_width'])) echo $f['option']['resize_width'];?>"
			 />
			<input name="resize_height" type="text" id="resize_height" class="form-control form-control-sm" placeholder="Height"
			value="<?php if(isset($f['option']['resize_height'])) echo $f['option']['resize_height'];?>" />
		</div>

		<div class="" >
			<input type="checkbox" name="image_multiple"  value="1" <?php if(isset($f['option']['image_multiple']) && $f['option']['image_multiple'] =='1') echo 'checked="checked"';?> class="minimal-green" id="opt-multiple" /> <label for="opt-multiple" > Allow Multiple </label>
		</div>		
				
	 </div> 
  </div>   

  <div class="form-group row repeaterConfig" style="display:none;">
  	<label for="repeater_data" class="control-label col-md-4"> Repeater Configuration<code>*</code>
	  <span><a href="https://docs.botble.com/cms/5.16/form-builder#repeater-fields" target="_blank"> Reference URL</a></span></label>
	<div class="col-md-8">
		<textarea name="repeater_data" rows="10" id="repeater_data" class="tab_behave required form-control form-control-sm" required="true" placeholder="Enter Repeater Configuration" >{{Arr::get($f, 'repeater_data')}}</textarea>
	</div>
  </div>   
      <?php 
	$hide = (Arr::get($f, 'editor_config_buttons') == 1) ? true : false;
	$show = (Arr::get($f, 'editor_config_buttons') == 0) ? true : false;
	?>
  <div class="form-group row editorConfig" style="display:none;">
    
  	<label class=" control-label col-md-4"> Hide Buttons </label>
        <div class="col-md-8">
            <label class="checkbox-inline">
                {!! Form::radio("editor_config_buttons", 1, $hide) !!} True
            </label>
            <label class="checkbox-inline">
                {!! Form::radio("editor_config_buttons", 0, $show) !!} False
            </label>
        </div>
  </div>   
   

  <div class="form-group row" style="display:none;">
    <label for="ipt" class=" control-label col-md-4">Input Format ( Masking ) </label>
	<div class="col-md-8">
		<select name="format" class="form-control form-control-sm">
			<?php $array = array(
				'text' 			=> 'None',
				'phone'			=> 'Int Phone ',
				'currency'	 	=> 'USD Currency',
				'percent'		=> 'Percent'
			);
			foreach($array as $val=>$item) {?>
				<option value="<?php echo $val;?>"><?php echo $item;?></option>
			<?php } ?>
		</select>	  
	 </div> 
  </div>  
<fieldset>
	<legend> <a href="javascript:ajax" onclick="$( '.addhtml' ).toggle()"> <small> More Option  </small> </a></legend>
	<div class="addhtml" style="display: none">  
  <div class="form-group row" >
    <label for="ipt" class=" control-label col-md-4">Tooltip </label>
	<div class="col-md-8">
		<input name="tooltip" type="text" id="tooltip" class="form-control form-control-sm" value="<?php echo $f['option']['tooltip'];?>"/>  
	 </div> 
  </div>  
  <div class="form-group row" style="display:none;">
    <label for="ipt" class=" control-label col-md-4">Additional Class </label>
	<div class="col-md-8">
		<input name="extend_class" type="text" id="extend_class" class="form-control form-control-sm" value="<?php echo $f['option']['extend_class'];?>"/>
	 </div> 
  </div>   

  <div class="form-group row">
    <label for="ipt" class=" control-label col-md-4">Wrapper Grid Class </label>
	<div class="col-md-8">
		<input name="wrapper_grid_cls" type="text" id="wrapper_grid_cls" class="form-control form-control-sm" value="<?php echo Arr::get($f, 'option.wrapper_grid_cls');?>"/>
	 </div> 
  </div>
  <div class="form-group row">
    <label for="ipt" class=" control-label col-md-4">Generate Custom Code </label>
	<div class="col-md-8">
		<input name="generate_custom_code" type="text" id="generate_custom_code" class="form-control form-control-sm" value="<?php echo Arr::get($f, 'option.generate_custom_code');?>"/>
	 </div> 
  </div>
  
  <div class="form-group row " >
    <label for="ipt" class=" control-label col-md-4"> Custom <b>Prefix & Suffix </b> </label>
	<div class="col-md-8">
		  <input name="prefix" type="text" id="prefix" class="form-control form-control-sm" style="width: 30% !important; float: left; margin-right: 5px; " placeholder="Prefix"
		  value="<?php if(isset($f['option']['prefix'])) echo $f['option']['prefix'];?>" />
		  <input name="sufix" type="text" id="sufix" class="form-control form-control-sm" style="width: 30% !important;  float: left; " placeholder="Suffix"
		  value="<?php if(isset($f['option']['sufix'])) echo $f['option']['sufix'];?>" />
	 </div> 
  </div>  
  <div class="form-group row">
    <label for="ipt" class=" control-label col-md-4">Default Value </label>
	<div class="col-md-8">
		<input name="default_value" type="text" id="default_value" class="form-control form-control-sm" value="<?php echo Arr::get($f, 'option.default_value');?>"/>
	 </div> 
  </div>          

	

  <div class="form-group row " >
    <label for="ipt" class=" control-label col-md-4">Html Attribute</label>
	<div class="col-md-8">
		<textarea name="attribute" id="attribute" class="form-control form-control-sm" placeholder="style='width:50%'"><?php echo $f['option']['attribute'];?></textarea>
	 </div> 
  </div>   	    
		
	<div class="form-group row">
		<label class=" control-label col-md-4"> Readonly  </label>
		<div class="col-md-8">
			<label class="checkbox-inline">
			{!! Form::checkbox("readonly", 1, Arr::get($f, 'option.readonly')) !!} 
			</label>
		</div>
	</div>	    
	<div class="form-group row">
		<label class=" control-label col-md-4"> Disabled  </label>
		<div class="col-md-8">
			<label class="checkbox-inline">
			{!! Form::checkbox("disabled[create]", 1, Arr::get($f, 'option.disabled.create')) !!} Create
			</label>
			<label class="checkbox-inline">
			{!! Form::checkbox("disabled[edit]", 1, Arr::get($f, 'option.disabled.edit')) !!} Edit
			</label>
			<label class="checkbox-inline">
			{!! Form::checkbox("disabled[edit-profile]", 1, Arr::get($f, 'option.disabled.edit-profile')) !!} Edit Profile
			</label>
		</div>
	</div>	    
		
	<div class="form-group row">
		<label class=" control-label col-md-4"> Hide  </label>
		<div class="col-md-8">
			<label class="checkbox-inline">
			{!! Form::checkbox("hidden[view]", 1, Arr::get($f, 'option.hidden.view')) !!} View
			</label>
			<label class="checkbox-inline">
			{!! Form::checkbox("hidden[create]", 1, Arr::get($f, 'option.hidden.create')) !!} Create
			</label>
			<label class="checkbox-inline">
			{!! Form::checkbox("hidden[edit]", 1, Arr::get($f, 'option.hidden.edit')) !!} Edit
			</label>
			<label class="checkbox-inline">
			{!! Form::checkbox("hidden[front_end]", 1, Arr::get($f, 'option.hidden.front_end')) !!} Front End
			</label>
			<label class="checkbox-inline">
			{!! Form::checkbox("hidden[back_end]", 1, Arr::get($f, 'option.hidden.back_end')) !!} Back End
			</label>
		</div>
	</div>

	<div class="form-group row">
		<label for="validation_msg" class="control-label col-md-4"> Validation Message </label>
		<div class="col-md-8">
			<textarea name="validation_msg" id="validation_msg" class="form-control form-control-sm" placeholder="Enter Validation Message" >{{Arr::get($f, 'option.validation_msg')}}</textarea>
		</div>
	</div> 
	<?php 
	$restrictToChecked = (Arr::get($f, 'option.field_access_type') == 1) ? true : false;
	$accessToChecked = (Arr::get($f, 'option.field_access_type') == 2) ? true : false;
	?>
	<div class="form-group row">
		<label class=" control-label col-md-4"> Field Access Type </label>
		<div class="col-md-8">
			<label class="checkbox-inline">
			{!! Form::radio("field_access_type", 1, $restrictToChecked) !!} Restrict To
			</label>
			<label class="checkbox-inline">
			{!! Form::radio("field_access_type", 2, $accessToChecked) !!} Access To
			</label>
		</div>
	</div>

	<div class="form-group row " >
	<label for="ipt" class=" control-label col-md-4"> Restrict to </label>
	<div class="col-md-8">
		<select class="js-data-example-ajax form-control" name="restricted_role_id[]" multiple="multiple">
		<?php
			$restrictedRoleID = Arr::get($f, 'option.restricted_role_id');
			$restrictedRoleID = (Arr::get($restrictedRoleID, 0)) ? $restrictedRoleID : [];
			foreach($roles as $role) 
			{
				?> <option value="<?php echo $role->id;?>" 
				<?php if(in_array($role->id, $restrictedRoleID)) 
					echo 'selected="selected"';?>><?php echo $role->name;?></option>';			 			
		<?php } 
		?>
	</select>
	</div>
	</div>

	<div class="form-group row" >
		<label for="restrict_based_on" class=" control-label col-md-4"> Restriction based on this field? </label>
		<div class="col-md-8">
			<input type="checkbox" name="restrict_based_on"  value="1" <?php if(isset($f['option']['restrict_based_on']) && $f['option']['restrict_based_on'] =='1') echo 'checked="checked"';?> class="minimal-green" id="restrict_based_on" />
		</div>
	</div>	

  </div>
</fieldset>  
  
  <div class="form-group row">
    <label for="ipt" class=" control-label col-md-4"></label>
	<div class="col-md-8">
				<button type="submit" class="btn btn-primary"> Save Changes </button>
		<input type="hidden" name="id" value="<?php echo $id;?>" />
	 </div> 
  </div> 
    
	</div>    
 {!! Form::close() !!}
<style type="text/css">
.checkbox { display: block;}
.radio input, .checkbox input { left: 5px; margin-top: 5px;}
.clone {
	
}
</style>

<script>
    $(document).ready(function() {
		$('.js-data-example-ajax').select2({
			placeholder: "Select Role",
			width: '100%',
		});
    });
</script>

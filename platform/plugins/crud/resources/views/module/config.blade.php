@extends('core/base::layouts.master')
@section('content')
<div class="page-header"><h2>  {{ $pageTitle }} <small>Configuration</small> </h2></div>

	 
		@include('plugins/crud::module.tab',array('active'=>'config','type'=> $type))
		
	<div class="row ">
			

	<div class="col-md-6">
	{!! Form::open(array('url'=>'admin/cruds/saveconfig/'.$module_name, 'class'=>'form-horizontal ','id'=>'configA' , 'parsley-validate'=>'','novalidate'=>' ')) !!}
	<input  type='hidden' name='id' id='id'  value='{{ $row->id }}'   />
  	<fieldset>
		<legend> Module Info </legend>	
  		<div class=" form-group row">
    		<label for="ipt" class=" control-label col-md-4">Name / Title </label>
			<div class="col-md-8">
				<div class="input-group input-group-sm" style="margin:1px 0 !important;">
				<input  readonly="1"  type='text' name='module_title' id='module_title' class="form-control  form-control-sm " required="true" value='{{ $row->module_title }}'  />
					
					<span class="input-group-addon xlick bg-default btn-sm" >EN</span>
				
			</div> 		
			@if($config->lang =='true')
			  <?php $lang = langOption();
			   if(Arr::get($sximoconfig, 'cnf_multilang') ==1 && ($lang && count($lang) > 0)) {
				foreach($lang as $l) { if($l['folder'] !='en') {
			   ?>
			   <div class="input-group input-group-sm mb-1" >			   	
					 <input name="language_title[<?php echo $l['folder'];?>]" type="text"   class="form-control " placeholder="Label for <?php echo $l['name'];?>"
					 value="<?php echo (isset($module_lang['title'][$l['folder']]) ? $module_lang['title'][$l['folder']] : '');?>" />
						 
						<span class="input-group-addon xlick bg-default btn-sm" ><?php echo strtoupper($l['folder']);?></span>
					
			   </div> 
	 		
 			 <?php } } }?>	  
 			  @endif
			 </div> 
			
  		</div>   
  		<div class=" form-group row">
    		<label for="ipt" class=" control-label col-md-4">Alias Name / Title </label>
			<div class="col-md-8">
				<div class="input-group input-group-sm" style="margin:1px 0 !important;">
				<input  type='text' name='module_alias' id='module_alias' class="form-control  form-control-sm " value='{{ $row->module_alias }}'  />
					
					<span class="input-group-addon xlick bg-default btn-sm" >EN</span>
				
			</div> 		
			@if($config->lang =='true')
			  <?php $lang = langOption();
			   if(Arr::get($sximoconfig, 'cnf_multilang') ==1 && ($lang && count($lang) > 0)) {
				foreach($lang as $l) { if($l['folder'] !='en') {
			   ?>
			   <div class="input-group input-group-sm mb-1" >			   	
					 <input name="language_title[<?php echo $l['folder'];?>]" type="text"   class="form-control " placeholder="Label for <?php echo $l['name'];?>"
					 value="<?php echo (isset($module_lang['title'][$l['folder']]) ? $module_lang['title'][$l['folder']] : '');?>" />
						 
						<span class="input-group-addon xlick bg-default btn-sm" ><?php echo strtoupper($l['folder']);?></span>
					
			   </div> 
	 		
 			 <?php } } }?>	  
 			  @endif
			 </div> 
			
  		</div>   

		<div class=" form-group row">
			<label for="ipt" class=" control-label col-md-4">Module Note</label>
			<div class="col-md-8">
				<div class="input-group input-group-sm" style="margin:1px 0 !important;">
				<input  type='text' name='module_note' id='module_note'  value='{{ $row->module_note }}' class="form-control form-control-sm "  />
				<span class="input-group-addon xlick bg-default btn-sm" >EN</span>
			</div> 
			@if($config->lang =='true')	
		  <?php $lang = langOption();
		   if(Arr::get($sximoconfig, 'cnf_multilang') ==1) {
			foreach($lang as $l) { if($l['folder'] !='en') {
		   ?>
		   <div class="input-group input-group-sm" style="margin:1px 0 !important;">
			 <input name="language_note[<?php echo $l['folder'];?>]" type="text"   class="form-control " placeholder="Note for <?php echo $l['name'];?>"
			 value="<?php echo (isset($module_lang['note'][$l['folder']]) ? $module_lang['note'][$l['folder']] : '');?>" />
			 <span class="input-group-addon xlick bg-default btn-sm" ><?php echo strtoupper($l['folder']);?></span>
		   </div> 
			 
		  <?php } } }?>	
		  	 @endif	

			 </div> 
		 </div>   
		
	  <div class=" form-group row">
		<label for="ipt" class=" control-label col-md-4">Class Controller </label>
		<div class="col-md-8">
		<input  type='text' name='module_name' id='module_name' readonly="1"  class="form-control form-control-sm" required value='{{ $row->module_name }}'  />
		 </div> 
	  </div>  
  
	   <div class=" form-group row">
		<label for="ipt" class=" control-label col-md-4">Table Master</label>
		<div class="col-md-8">
		<input  type='text' name='module_db' id='module_db' readonly="1"  class="form-control form-control-sm" required value='{{ $row->module_db}}'  />
		  
		 </div> 
	  </div>  
  
	  <div class=" form-group row">
		<label for="ipt" class=" control-label col-md-4">Insert Table Before Master</label>
		<div class="col-md-8">
			<select class="form-control  form-control-sm" name="module_before_insert">
			<option value="">-- Select --</option>
			@foreach($tableList as $k => $t)
				<option value="{{ $k }}"
				@if(isset($row->module_before_insert) && $row->module_before_insert == $k) selected="selected" @endif 
				>{{ $t }}</option>
			@endforeach
			</select>
		</div> 
	 </div>  
  
	<div class=" form-group row">
		<label class=" control-label col-md-4"> Insert User Before Module</label>
		<div class="col-md-8">
			{!! Form::checkbox('insert_user_before', 1, isset($row->insert_user_before) ? $row->insert_user_before : false) !!}
		</div>
	</div>  
  
	  <div class=" form-group row" style="display:none;" >
		<label for="ipt" class=" control-label col-md-4">Author </label>
		<div class="col-md-8">
		<input  type='text' name='module_author' id='module_author' class="form-control form-control-sm"  readonly="1"  value='{{ $row->module_author }}'  />
		 </div> 
	  </div>  

		<div class=" form-group row">
			<label for="ipt" class=" control-label col-md-4"> ShortCode </label>
			<div class="col-md-8 " >
				<b>Form Shortcode : </b><code><br />
				{!! Form::checkbox('is_shortcode_form', 1, isset($row->is_shortcode_form) ? $row->is_shortcode_form : false) !!}
				<?php echo "[".$row->module_name."-form-sc][/".$row->module_name."-form-sc]"; ?></code><br />
				<b>List Shortcode : </b><code><br />
				{!! Form::checkbox('is_shortcode_table', 1, isset($row->is_shortcode_table) ? $row->is_shortcode_table : false) !!}
				<?php echo "[".$row->module_name."-list-sc][/".$row->module_name."-list-sc]"; ?></code><br />
			</div> 
		</div>  
		<div class="form-group row">
			<label class=" control-label col-md-4"> Shortcode Options </label>
			<div class="col-md-8">
				<textarea name="shortcode_options" rows="2" id="shortcode_options" class="form-control form-control-sm"   placeholder="" >{{$shortcode_options}}</textarea>
			</div>
		</div>  
  
		<div class="form-group row">
			<label class=" control-label col-md-4"> Is Entity  </label>
			<div class="col-md-8">
				{!! Form::checkbox('is_entity', 1, $row->is_entity) !!}
			</div>
		</div>	  
		<div class="form-group row">
			<label class=" control-label col-md-4"> Is BulkUpload  </label>
			<div class="col-md-8">
				{!! Form::checkbox('is_bulkupload', 1, (isset($row->is_bulkupload) ? $row->is_bulkupload : 0)) !!}
			</div>
		</div>	  
		<div class="form-group row">
			<label class=" control-label col-md-4"> Is MultiLingual  </label>
			<div class="col-md-8">
				{!! Form::checkbox('is_multi_lingual', 1, (isset($row->is_multi_lingual) ? $row->is_multi_lingual : 0)) !!}
			</div>
		</div>	    
		<div class="form-group row">
			<label class=" control-label col-md-4"> Is Customized  </label>
			<div class="col-md-8">
				{!! Form::checkbox('is_customized', 1, (isset($row->is_customized) ? $row->is_customized : 0)) !!}
			</div>
		</div>	    
		
		<div class="form-group row">
			<label class=" control-label col-md-4"> Module Action  </label>
			<div class="col-md-8">
				<label class="checkbox-inline">
				{!! Form::checkbox("module_actions[create]", 1, Arr::get($module_actions, 'create')) !!} Create
				</label>
				<label class="checkbox-inline">
				{!! Form::checkbox("module_actions[edit]", 1, Arr::get($module_actions, 'edit')) !!} Edit
				</label>
				<label class="checkbox-inline">
				{!! Form::checkbox("module_actions[destroy]", 1, Arr::get($module_actions, 'destroy')) !!} Delete
				</label>
				<label class="checkbox-inline">
				{!! Form::checkbox("module_actions[export]", 1, Arr::get($module_actions, 'export')) !!} Export
				</label>
				<label class="checkbox-inline">
				{!! Form::checkbox("module_actions[print]", 1, Arr::get($module_actions, 'print')) !!} Print
				</label>
				<label class="checkbox-inline">
				{!! Form::checkbox("module_actions[inline_edit]", 1, Arr::get($module_actions, 'inline_edit')) !!} Inline-Edit
				</label>
				<label class="checkbox-inline">
				{!! Form::checkbox("module_actions[enable_disable]", 1, Arr::get($module_actions, 'enable_disable')) !!} Enable/Disable
				</label>
				<label class="checkbox-inline">
				{!! Form::checkbox("module_actions[hide_operations]", 1, Arr::get($module_actions, 'hide_operations')) !!} Hide Operations
				</label>
				<label class="checkbox-inline">
				{!! Form::checkbox("module_actions[subscribe]", 1, Arr::get($module_actions, 'subscribe')) !!} Subscribe
				</label>
				<label class="checkbox-inline">
				{!! Form::checkbox("module_actions[is_master]", 1, Arr::get($module_actions, 'is_master')) !!} Master Permission
				</label>
			</div>
		</div>

		
		<div class="form-group row">
			<label class=" control-label col-md-4"> Module Action Meta </label>
			<div class="col-md-8">
				<textarea name="module_action_meta" rows="2" id="module_action_meta" class="form-control form-control-sm"   placeholder="" >{{$module_action_meta}}</textarea>
			</div>
		</div>
		<div class="form-group row">
			<label class=" control-label col-md-4"> Dependent Module </label>
			<div class="col-md-8">
				<textarea name="dependent_module" rows="2" id="dependent_module" class="form-control form-control-sm"   placeholder="" >{{$dependent_module}}</textarea>
			</div>
		</div>
	 
		<div class=" form-group row">
			<label for="ipt" class=" control-label col-md-4"></label>
			<div class="col-md-8">
			<button type="submit" name="submit" class="btn btn-primary btn-sm"> Update Module </button>
			 </div> 
		</div> 

	</fieldset>
  	{!! Form::close() !!}
	
  
	</div>


	 @if($config->advance =='true') 
 <div class="col-sm-6 col-md-6"> 

 @if($type !='report' && $type !='generic')
  {!! Form::open(array('url'=>'admin/cruds/savesetting/'.$module_name, 'class'=>'form-horizontal  ' ,'id'=>'configB')) !!}
  <input  type='text' name='id' id='id'  value='{{ $row->id }}'  style="display:none; " />
  	<fieldset>
		<legend> Module Setting </legend>

		  <div class=" form-group row">
			<label for="ipt" class=" control-label col-md-4"> Grid Table Type </label>
			<div class="col-md-8">			

				<select class="form-control form-control-sm" name="module_type">
					<?php if($row->module_type  =='addon') $row->module_type ='native'; ?>
					@foreach($cruds as $crud)
						<option value="{{ $crud->type }}" 
						@if($crud->type == $row->module_type ) selected @endif
						>{{ $crud->name }} </option>
					@endforeach
				</select>	
				
			 </div> 
		  </div> 


	
	  <div class=" form-group row">
		<label for="ipt" class=" control-label col-md-4"> Default Order  </label>
		<div class="col-md-8">
			<select class="form-control  form-control-sm" name="orderby" style="width: 50%">
			@if($tables)
				@foreach($tables as $t)
					<option value="{{ $t['field'] }}"
					@if($setting['orderby'] ==$t['field']) selected="selected" @endif 
					>{{ $t['label'] }}</option>
				@endforeach
			@endif
			</select>
			<select class="form-control  form-control-sm" name="ordertype" style="width: 50%">
				<option value="asc" @if($setting['ordertype'] =='asc') selected="selected" @endif > Ascending </option>
				<option value="desc" @if($setting['ordertype'] =='desc') selected="selected" @endif > Descending </option>
			</select>
			
		 </div> 
	  </div> 
	  
	  <div class=" form-group row">
		<label for="ipt" class=" control-label col-md-4"> Display Rows </label>
		<div class="col-md-8">
			<select class="form-control  form-control-sm" name="perpage" style="width: 50%">
				<?php $pages = array('10','20','30','50');
				foreach($pages as $page) {
				?>
				<option value="<?php echo $page;?>"  @if($setting['perpage'] ==$page) selected="selected" @endif > <?php echo $page;?> </option>
				<?php } ?>
			</select>	
			
		 </div> 
	  </div>   
		<div class="form-group row">
			<label class=" control-label col-md-4"> View Details In </label>
			<div class="col-md-8">
				<select class="form-control  form-control-sm" name="view_details_type" style="width: 50%">
					<?php $displayType = array(0 => 'Page', 1 => 'Pop-up');
					foreach($displayType as $k => $type) {
					?>
					<option value="<?php echo $k;?>"  @if(Arr::get($setting, 'view_details_type') ==$k) selected="selected" @endif > <?php echo $type;?> </option>
					<?php } ?>
				</select>
			</div>
		</div>

		<div class="form-group row">
			<label class=" control-label col-md-4"> Hide Menu </label>
			<div class="col-md-8">
				{!! Form::checkbox('hide_menu', 1, (Arr::get($setting, 'hide_menu')) ? true : false) !!}
			</div>
		</div>

		<div class="form-group row">
          <label for="ipt" class=" control-label col-md-4">Special Notes</label>
          <div class="col-md-8">
		  	<textarea name="special_notes" rows="2" id="special_notes" class="form-control form-control-sm"   placeholder="Enter Special Notes" >{{Arr::get($setting, 'special_notes')}}</textarea>
		  </div>
		</div> 
                @if(setting('enable_captcha') && is_plugin_active('captcha'))
                <div class="form-group row">
                    <label class=" control-label col-md-4"> Hide Captcha </label>
                    <div class="col-md-8">
                        <label class="checkbox-inline">
			{!! Form::checkbox("is_captcha[front_end]", 1, Arr::get($setting, 'is_captcha.front_end')) !!} Front End
			</label>
			<label class="checkbox-inline">
			{!! Form::checkbox("is_captcha[back_end]", 1, Arr::get($setting, 'is_captcha.back_end')) !!} Back End
			</label>
                        
                    </div>
                </div>
                @endif
                {{-- Get Action box column type --}}
                <div class="form-group row">
                    <label class=" control-label col-md-4"> Action Box </label>
                    <div class="col-md-8">
                        <div class='row'>
                            <div class="col-md-4">
                                <input type="radio" name="action_box" 
                                <?php if (Arr::get($setting, 'action_box') == 'right') echo 'checked'; ?>
                                       value="right" id="opt-right" /><label for="opt-right"> Right </label>
                            </div>
                            <div class="col-md-4">
                                <input type="radio" name="action_box" 
                                <?php if (Arr::get($setting, 'action_box') == 'bottom') echo 'checked'; ?>
                                       value="bottom"  id="opt-bottom"/><label for="opt-bottom"> Bottom </label>  
                            </div>
                        </div>
                    </div>
                </div>
                {{-- Get Menu order and Menu Icon start --}}
                <div class="form-group row">
                    <label class=" control-label col-md-4"> Menu Priority</label>
                    <div class="col-md-8">
                        <select class="form-control  form-control-sm" name="menu_priority" style="width: 50%">
                            @if($row->parent_id == 0)		
                            <?php
                            for ($i = $menu_priorities[0]; $i <= count($menu_priorities)+1; $i++) {
                                ?>
                                <option value="<?php echo $i; ?>"  @if(Arr::get($setting, 'menu_priority') ==$i) selected="selected" @endif > <?php echo $i; ?> </option>
                            <?php } ?>
                            @else
                            <?php
							if($child_menus) {
								for ($i = 1; $i <=count($child_menus)+1; $i++) {
                           
                                ?>
                                <option value="<?php echo $i; ?>"  @if(Arr::get($setting, 'menu_priority') ==$i) selected="selected" @endif > <?php echo $i; ?> </option>
                            <?php } }?>
                            @endif
                        </select>
                    </div>
                </div>
                @if($row->parent_id == 0)
                <div class="form-group row">
                    <label class=" control-label col-md-4"> Menu Icon</label>
                    <div class="col-md-8">
                        <input type="text" name="menu_icon" class="form-control form-control-sm"                        
                               value="{{Arr::get($setting, 'menu_icon')}}" id="menu_icon" placeholder="fa fa-list"/>
                    </div>
                </div>
                @endif
                {{--  Get Menu order and Menu Icon end  --}}
                {{--  need custom js start  --}}
                <div class="form-group row">
                    <label class=" control-label col-md-4"> Is Domain Mapping? </label>
                    <div class="col-md-8">
                        {!! Form::checkbox('domain_mapping', 1, (Arr::get($setting, 'domain_mapping')) ? true : false) !!}
                    </div>
                </div>
                <div class="form-group row">
                    <label class=" control-label col-md-4"> Use Custom JS? </label>
                    <div class="col-md-8">
                        {!! Form::checkbox('custom_js', 1, (Arr::get($setting, 'custom_js')) ? true : false) !!}
                    </div>
                </div>
                {{--  need custom js end  --}}

				<div class="form-group row">
                    <label class=" control-label col-md-4"> Use Gallery Image? </label>
                    <div class="col-md-8">
                        {!! Form::checkbox('is_gallery_image', 1, (Arr::get($setting, 'is_gallery_image')) ? true : false) !!}
                    </div>
                </div>
                <div class="form-group row">
                    <label class=" control-label col-md-4"> Use Custom Action? </label>
                    <div class="col-md-8">
                        {!! Form::checkbox('is_custom_action', 1, (Arr::get($setting, 'is_custom_action')) ? true : false) !!}
                    </div>
                </div>
				
				<div class="form-group row">
					<label class=" control-label col-md-4"> Is Subscription Dependent Module? </label>
					<div class="col-md-8">
					{!! Form::checkbox('is_subscription_related_module', 1, (Arr::get($setting, 'is_subscription_related_module')) ? true : false) !!}
					</div>
				</div>

                <div class="form-group row">
                    <label class=" control-label col-md-4"> Hide On Module Action </label>
                    <div class="col-md-8">
                        <textarea name="hide_module_actions" rows="2" id="hide_module_actions" class="form-control form-control-sm"   placeholder="" >{{Arr::get($setting, 'hide_module_actions')}}</textarea>
                    </div>
                </div>
                <div class="form-group row">
                    <label class=" control-label col-md-4"> Revision History </label>
                    <div class="col-md-8">
                        <textarea name="revision_history" rows="4" id="revision_history" class="form-control form-control-sm"   placeholder="" >{{Arr::get($setting, 'revision_history')}}</textarea>
                    </div>
                </div>
	</fieldset>	
	 @if($config->setting->method =='true') 
  	<fieldset>
	<legend> Form & View Setting </legend>
		<p> <i>You can switch this setting and applied to current module without have to rebuild </i></p>

		  <div class=" form-group row">
			<label for="ipt" class=" control-label col-md-4"> Form Method </label>
			<div class="col-md-8">
				
				<input type="radio" value="native" name="form-method" class="filled-in" id="n-p" 
				 @if($setting['form-method'] == 'native') checked="checked" @endif 
				 /> 
				 <label for="n-p"> New Page </label>
				
				<input type="radio" value="modal" name="form-method"  class="filled-in" id="n-m" 
				 @if($setting['form-method'] == 'modal') checked="checked" @endif 			
				/> 
				<label for="n-m">	Modal  </label>							
			 </div> 
		  </div> 

		  <div class=" form-group row">
			<label for="ipt" class=" control-label col-md-4"> View  Method </label>
			<div class="col-md-8">
				
				<input type="radio" value="native" name="view-method" class="filled-in" id="v-n" 
				 @if($setting['view-method'] == 'native') checked="checked" @endif 
				 /> 
				 <label for="v-n">	New Page  </label>		
				
				<input type="radio" value="modal" name="view-method" class="filled-in" id="v-m"   
				 @if($setting['view-method'] == 'modal') checked="checked" @endif 			
				/> 
				 <label for="v-m">	Modal  </label>	   
				
				<input type="radio" value="expand" name="view-method"  class="filled-in" id="v-e" 
				 @if($setting['view-method'] == 'expand') checked="checked" @endif 			
				/>  
				 <label for="v-e">	Expand Grid    </label>	  

			 </div> 
		  </div> 		  

		  <div class=" form-group row" >
			<label for="ipt" class=" control-label col-md-4"> Inline add / edit row </label>
			<div class="col-md-8">
				
				<input type="checkbox" value="true" name="inline" class="filled-in" id="new-inline" 
				@if($setting['inline'] == 'true') checked="checked" @endif 	
				 /> 
				 <label for="new-inline"> Yes  Allowed </label>
										
			 </div> 
		  </div> 		  

		  
		   <p class="alert alert-warning"> <strong> Important ! </strong> this setting only work with module where have <strong>Adavance </strong> Option</p>
	</fieldset>


	@endif

			  <div class=" form-group row">
			<label for="ipt" class=" control-label col-md-4"></label>
			<div class="col-md-8">
			<button type="submit" name="submit" class="btn btn-primary btn-sm"> Update Setting </button>
			 </div> 
		  </div> 

	{!! Form::close() !!}
	@endif
	
  </div>
  	@endif
</div>

<div class="modal" id="sximo-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
    <div class="modal-dialog  " role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel1" style="color:#fff;font-size: 20px;">New message</h4>
                <button type="button" class="btn_remove_image" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times"></i></span></button>
                <!--<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">x</span></button>-->
            </div>
            <div class="modal-body" id="sximo-modal-content">
                
            </div>
           
        </div>
    </div>
</div>
<script type="text/javascript">
	$(document).ready(function(){

		<?php echo sjForm('configA'); ?>
		<?php echo sjForm('configB', true); ?>

	})
</script>	

@stop
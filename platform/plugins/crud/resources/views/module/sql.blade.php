@extends('core/base::layouts.master')

@section('content')
<div class="page-header"><h2>  {{ $pageTitle }} <small>Configuration</small> </h2></div>
 


          @include('plugins/crud::module.tab',array('active'=>'sql','type'=>  $type ))

         
          {!! Form::open(array('url'=>'admin/cruds/savesql/'.$module_name, 'class'=>'form-vertical  ' ,'id'=>'SQL' , 'parsley-validate'=>'','novalidate'=>' ')) !!}
          <div class="infobox infobox-success ">
          <button type="button" class="close" data-dismiss="alert"> x </button>  
          <p> <strong>Tips !</strong> U can use query builder tool such <a href="http://code.google.com/p/sqlyog/downloads/list" target="_blank">SQL YOG </a> , PHP MyAdmin , Maestro etc to build your query statment and preview the result , <br /> then copy the syntac here </p> 
          </div>  


          <div class="form-group">
          <label for="ipt" class=" control-label">SQL SELECT & JOIN</label>
          <textarea name="sql_select" rows="5" id="sql_select" class="tab_behave form-control form-control-sm"  placeholder="SQL Select & Join Statement" >{{ $sql_select }}</textarea>
          </div>  

          <div class="form-group">
          <label for="ipt" class=" control-label">SQL WHERE CONDITIONAL</label>
          <textarea name="sql_where" rows="2" id="sql_where" class="form-control form-control-sm" placeholder="SQL Where Statement" >{{ $sql_where }}</textarea>
          </div> 

          <div class="infobox infobox-danger ">
          <p> <strong>Warning !</strong> Please make sure SQL where not empty , for prevent error when user attempt submit  <strong>SEARCH</strong>   </p>  
          </div>  
            


          <div class="form-group">
          <label for="ipt" class=" control-label">SQL GROUP</label>
          <textarea name="sql_group" rows="2" id="sql_group" class="form-control form-control-sm"   placeholder="SQL Grouping Statement" >{{ $sql_group }}</textarea>

          </div> 
          <div class="form-group">
          <label for="ipt" class=" control-label"></label>
          <button type="submit" class="btn btn-primary btn-sm"> Save SQL </button>
          </div>  

          <input type="hidden" name="id" value="{{ $row->id }}" />
          <input type="hidden" name="module_name" value="{{ $row->module_name }}" />

          {!! Form::close() !!}

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

    <?php echo sjForm('SQL'); ?>

  })
</script> 
@stop
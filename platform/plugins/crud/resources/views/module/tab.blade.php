<?php
   $modulesOpt = \DB::table('cruds')->where('module_type','!=','core')->orderby('module_title','asc')->get();
   $selected = Request::segment(4);
?>                        

<div class="toolbar-nav">
  <div class="row">
    <div class="col-md-8">
      <div class="btn-group">
           
          
          @if(isset($type) && ( $type !='blank' or $type !='core'))
          <a href="javascript://ajax" onclick="SximoModal('{{ URL::to('admin/cruds/build/'.$module_name)}}','Rebuild Module ')" class="btn  btn-sm btn-warning">  Rebuild </a>
          <a href="javascript:;"  onclick="SximoConfirmDelete('{{ url('admin/cruds/destroy/'.$row->id)}}')" class="btn  btn-sm btn-danger"> Delete </a>
          @endif

          <a href="{{ url('admin/'. $module_name )}}s" class="btn  btn-sm btn-primary" target="_blank">  Preview  </a>
          <a href="{{ url('admin/cruds')}}" class="btn  btn-sm btn-info">  Close </a>

      </div>
    </div>
    <div class="col-md-4">
      <div class="input-group">
<!--          <div class="input-group-prepend">
            <button type="button" class="btn btn-default btn-sm " > Jump To</button>
          </div>-->
            <select class="form-control form-control-sm" onchange="jumpTo(this.value)">
              @foreach($modulesOpt as $mo)
              <option value="{{ $mo->module_name }}" @if($selected == $mo->module_name) selected @endif >{{ $mo->module_title }} ( {{ $mo->module_name }}) </option>
              @endforeach
            </select>
          
        </div>    
    </div>
  </div>  

</div>
<div class="p-2 mb-5">
<ul class="nav nav-tabs">
  <li class="nav-item " ><a class="nav-link  @if($active == 'config') active @endif" href="{{ URL::to('admin/cruds/config/'.$module_name)}}"> Info</a></li>
 
  @if(isset($type) && $type =='blank')

  @else
       <li class="nav-item active" >
        <a href="{{ URL::to('admin/cruds/sql/'.$module_name)}}" class="nav-link  @if($active == 'sql') active @endif"> SQL</a></li>
        <li class="nav-item" >
        <a href="{{ URL::to('admin/cruds/table/'.$module_name)}}" class="nav-link  @if($active == 'table') active @endif"> Table</a></li>
        <li class="nav-item"  >
        <a href="{{ URL::to('admin/cruds/form/'.$module_name)}}" class="nav-link @if($active == 'form' or $active == 'subform') active @endif"> Form</a></li>
        <li class="nav-item"  >
        <a href="{{ URL::to('admin/cruds/sub/'.$module_name)}}" class="nav-link @if($active == 'sub'  ) active @endif"> Master Detail</a></li>
        <li class="nav-item"  >
        <a href="{{ URL::to('admin/cruds/scheduler/'.$module_name)}}" class="nav-link @if($active == 'scheduler'  ) active @endif"> Scheduler</a></li>
        <li class="nav-item"  >
        <a href="{{ URL::to('admin/cruds/emailconfig/'.$module_name)}}" class="nav-link @if($active == 'email'  ) active @endif"> Email</a></li>
        <li class="nav-item"  >
        <a href="{{ URL::to('admin/cruds/stats/'.$module_name)}}" class="nav-link @if($active == 'stats'  ) active @endif"> Stats</a></li>
        <li class="nav-item"  >
        <a href="{{ URL::to('admin/cruds/reports/'.$module_name)}}" class="nav-link @if($active == 'reports'  ) active @endif"> Reports</a></li>
        @endif
<!--        <li class="nav-item" >
        <a href="{{ URL::to('admin/cruds/permission/'.$module_name)}}" class="nav-link  @if($active == 'permission') active @endif"> Permission</a></li>
        @if($type !='core' )
        <li class="nav-item" >
        <a href="{{ URL::to('admin/cruds/source/'.$module_name)}}" class="nav-link  @if($active == 'source') active @endif"> Codes </a></li>
    @endif-->

  </li>

  
  
</ul>
</div>
<script type="text/javascript">
  function jumpTo( val ) {
      window.location.href='<?php echo url("admin/cruds/".Request::segment(3)) ?>/'+ val
  }
</script>
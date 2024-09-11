@extends('core/base::layouts.master')
@section('content')
<div class="page-titles">
  <h2> Crud Generator  <small>  Manage your module applications </small> </h2>
</div>

<div class="toolbar-nav">
    <div class="row">
        <div class="col-md-6">
             
                <div class="btn-group">
                    <a  href="{{ url('admin/cruds/create')}}" onclick="SximoModal(this.href ,'Create Module'); return false" class="btn  btn-sm btn-primary">  Create Module </a>
                    <a href="{{ url('cruds/package')}}" class="btn disabled btn-sm btn-success post_url"> Backup Module </a>
<!--                    <a href="{{ url('sximo/tables')}}" class="btn  btn-danger btn-sm">  Database Table </a>-->
                    <!-- <a href="{{ url('sximo/server/repository')}}" class="btn btn-warning  btn-sm">  Repositories</a> -->




                </div>
        </div>
<!--        <div class="col-md-6 text-right">
                <a href="javascript:;" class="btn  btn-sm btn-info" onclick="$('.unziped').toggle()"><i class="fa fa-upload"></i> Install Module </a>
        </div>-->
    </div>            
</div>




                
                <div class="col-md-4  offset-md-4">
                <div class="p-sm m-b unziped" style="display:none; padding: 5px 5px 30px ; margin-bottom:10px;">
                   
                        {!! Form::open(array('url'=>'crud/install/', 'class'=>'breadcrumb-search','files' => true , 'parsley-validate'=>'','novalidate'=>' ')) !!}
                        <b>Select File <small>( Module zip installer ) </small></b><br />
                        <div class="fileUpload btn " > 
                            <span>  <i class="fa fa-copy"></i>  </span>
                            <div class="title">  Upload Zip Installer </div>
                            <input type="file" name="installer" class="upload"    accept=".zip,.rar,.7zip"      />
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm " name="submit" ><i class=" fa fa-cloud-upload "></i> Install Now  </button>

                        
                        </form>
                    
                </div>  
                </div>      
           
               

                    <ul class="nav nav-tabs" style="margin-bottom:10px;">
                        <li  class="nav-item"  ><a class="nav-link @if($type =='addon') active @endif" href="{{ url('admin/cruds')}}"> {{ Lang::get('core.tab_installed') }}  </a></li>
                        <li class="nav-item disabled"><a class="nav-link @if($type =='core') active @endif"> {{ Lang::get('core.tab_core') }}</a></li>
                    </ul>     

                    @if($type =='core')

                     <div class="infobox infobox-info ">
                      <button type="button" class="close" data-dismiss="alert"> x </button>  
                      <p>   Do not <b>Rebuild</b> or Change any Core Module </p>    
                    </div>  
                     
                    @endif

        

    <div class="card">
    <div class="card-body">
        {!! Form::open(array('url'=>'crud/package#', 'class'=>'form-horizontal' ,'ID' =>'SximoTable' )) !!}

        @if(count($rowData) >=1) 
        <table class="table table-hover table-striped ">
           <thead>
            <tr>
                <th class="thead">Action</th>                 
                <th class="thead"><input type="checkbox" class="checkall minimal-green" /></th>
                <th class="thead">Module</th>
                <th class="thead">Type</th>
                
                <th class="thead">Controller</th>
                <th class="thead">Database</th>
                <th class="thead">PRI</th>
                <th class="thead">Created</th>

            </tr>
            </thead>
         <tbody>
        @foreach ($rowData as $row)
            <tr>        
                <td>
                <div class="btn-group ">
                <button class="btn  btn-sm dropdown-toggle" data-toggle="dropdown"> Action </span>
                </button>
                    <ul  class="dropdown-menu icons-right " style="z-index: 999999">
                        @if($type != 'core')
                        <li class="nav-item"><a class="nav-link" href="{{ url('admin/'.$row->module_name)}}s"> {{ Lang::get('core.btn_view') }} Module </a></li>
                        @endif
                        @if(isset($row->is_customized) && $row->is_customized!=1)
                        <li class="nav-item"><a class="nav-link" href="{{ url('admin/cruds/config/'.$row->module_name)}}"> {{ Lang::get('core.btn_edit') }}</a></li> 
                        @endif
                        @if($type != 'core')
                        <li class="nav-item disabled"><a class="nav-link disabled" href="javascript://ajax" onclick="SximoConfirmDelete('{{ url('admin/cruds/destroy/'.$row->id)}}')"> {{ Lang::get('core.btn_remove') }}</a></li>
                        <li class="divider"></li>
                        @if(isset($row->is_customized) && $row->is_customized!=1)
                        <li class="nav-item"><a class="nav-link"  onclick="SximoModal('{{ url('admin/cruds/build/'.$row->module_name)}}','Rebuild Module ')" href="javascript:;"> Rebuild All Codes</a></li>
                        @endif
                        @endif
                    </ul>
                </div>                  
                </td>
                <td>
                 
                <input type="checkbox" class="ids minimal-green" name="id[]" value="{{ $row->id }}"  id="val-{{ $row->id }}"/> 
                <label for="val-{{ $row->id }}"></label>
            </td>
                <td>{{ $row->module_title }} </td>
                <td>{{ $row->module_type }} </td>
                <td>{{ $row->module_name }} </td>

                <td>{{ $row->module_db }} </td>
                <td>{{ $row->module_db_key }} </td>
                <td>{{ date("F d ,Y ", strtotime($row->module_created)) }} </td>
            </tr>
        @endforeach 
        </tbody>        
        </table>
        {!! Form::close() !!}
      
        @else

        <p class="text-center" style="padding:50px 0;">{{ Lang::get('core.norecord') }} 
        <br /><br />
        <a href="{{ url('admin/cruds/create')}}" onclick="SximoModal(this.href ,'Create Module'); return false" class="btn btn-primary btn-sm "> {{ Lang::get('core.fr_createmodule') }} </a>
         </p>   
        @endif
    </div>
</div>
<div class="modal" id="sximo-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
    <div class="modal-dialog  " role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel1" style="color:#fff;font-size: 20px;">New message</h4>
                <button type="button" class="btn_remove_image" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times"></i></span></button>
                <!--<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>-->
            </div>
            <div class="modal-body" id="sximo-modal-content">
                
            </div>
           
        </div>
    </div>
</div>

  <script language='javascript' >
  jQuery(document).ready(function($){
    $('.post_url').click(function(e){
      e.preventDefault();
      if( ( $('.ids',$('#SximoTable')).is(':checked') )==false ){
        alert( "Nothing selected");
//        alert( $(this).attr('data-title') + " not selected");
        return false;
      }
      $('#SximoTable').attr({'action' : $(this).attr('href') }).submit();
    });


  })
  </script>        
@stop
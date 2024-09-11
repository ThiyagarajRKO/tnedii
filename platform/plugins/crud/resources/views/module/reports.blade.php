@extends('core/base::layouts.master')

@section('content')
<div class="page-header">
  <h2> {{ $pageTitle }} <small>Configuration</small> </h2>
</div>



@include('plugins/crud::module.tab',array('active'=>'reports','type'=> $type ))


{!! Form::open(array('url'=>'admin/cruds/savereports/'.$module_name, 'class'=>'form-horizontal ' ,'id'=>'fReports')) !!}

<input type='text' name='id' id='id' value='{{ $row->id }}' style="display:none;" />
<input type='hidden' name='slug' id='slug' value='{{ Arr::get($reportsData, "slug") }}'  />

<div class="form-group">
  <label for="ipt" class=" control-label col-md-4"> Reports Title <code>*</code></label>
  <div class="col-md-8">
    {!! Form::text('title', Arr::get($reportsData, 'title'),array('class'=>'form-control form-control-sm', 'placeholder'=>'' ,'required'=>'true')) !!}
    <i class="text-danger"> Important ! , <small> Do not use white space </small></i>
  </div>
</div>

<div class="form-group">
  <label class="control-label col-md-4"> Is Shortcode? </label>
  <div class="col-md-8">
    {!! Form::checkbox('is_shortcode', 1, Arr::get($reportsData, 'is_shortcode')) !!}
  </div>
</div>




<div class="form-group">
  <label for="ipt" class=" control-label  col-md-4">Field</label>
  <div class="col-md-8">
    <textarea name="field" rows="5" id="field" class="tab_behave form-control form-control-sm" placeholder="">{{Arr::get($reportsData, 'field')}}</textarea>
  </div>
</div>

<div class="form-group">
  <label for="ipt" class=" control-label  col-md-4">Select Fields</label>
  <div class="col-md-8">
    <textarea name="sel_fields" rows="5" id="sel_fields" class="tab_behave form-control form-control-sm" placeholder="">{{Arr::get($reportsData, 'sel_fields')}} 
    </textarea>
  </div>
</div>

<div class="form-group">
  <label for="ipt" class=" control-label  col-md-4">Query</label>
  <div class="col-md-8">
    <textarea name="sql_query" rows="5" id="sql_query" class="tab_behave form-control form-control-sm" placeholder="">{{Arr::get($reportsData, 'sql_query')}} 
    </textarea>
  </div>
</div>





<div class="form-group">
  <label for="ipt" class=" control-label col-md-4"></label>
  <div class="col-md-8">
    <button name="submit" type="submit" class="btn btn-primary btn-sm"> <?php if (Arr::get($reportsData, 'slug')) {
                                                                          echo "Update";
                                                                        } else {
                                                                          echo "Save";
                                                                        } ?> Reports </button>
    <?php if (Arr::get($reportsData, 'slug')) {
      echo '<a name="submit" href="/admin/cruds/reports/' . $row->module_name . '" class="btn btn-primary btn-sm">Reset</a>';
    }
    ?>
  </div>
</div>

{!! Form::close() !!}


<div class="table-responsive" style="margin-bottom:40px;">

  <table class="table table-striped">
    <thead class="no-border">
      <tr>
        <th>Title</th>
        <th>Slug</th>
        <th data-hide="phone">Action</th>
      </tr>
    </thead>
    <tbody class="no-border-x no-border-y">
      @foreach($reports as $rows)
      <tr>
        <td><?php echo $rows['title']; ?></td>
        <td><?php echo $rows['slug']; ?></td>
        <td><a href="{{'/admin/cruds/reports/'.$row->module_name.'/?mod='.Arr::get($rows, 'slug').'&parent_mod='.Arr::get($rows, 'parent_stats_id')}}" class="btn "><i class="fa fa-edit"></i> </a>
          <a href="javascript:void(0)" onclick="SximoConfirmDelete('{{ URL::to('admin/cruds/removestats?id='.$row->id.'&mod='.Arr::get($rows, 'slug').'&parent_mod='.Arr::get($rows, 'parent_stats_id')) }}');" class="btn "><i class="fa fa-trash"></i> </a>
        </td>

      </tr>
      @endforeach


    </tbody>

  </table>

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
<script>
  $(document).ready(function() {
   <?php echo sjForm('SQL'); ?>
  });
</script>

<script type="text/javascript">
  $(document).ready(function() {

    $(document).on('change', '[name="is_sub_stats"]', function() {
      if ($('[name="is_sub_stats"]').is(':checked')) {
        $('#parent_stats_id').prop('required', true);
        $('.parentStatsBlock').show();
        $('.widgetTypeBlock').hide();
      } else {
        $('.parentStatsBlock').hide();
        $('#parent_stats_id').removeAttr('required');
        $('.widgetTypeBlock').show();
      }
    });

    $('[name="is_sub_stats"]').trigger('change');

    <?php echo sjForm('fStats', true, true); ?>
    setTimeout(function() {
      $('.js-data-example-ajax').select2({
        placeholder: "Select Role",
        width: '100%',
      });
    }, 500)

  })
</script>
@stop
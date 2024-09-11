{!! Form::open(array('url' => 'admin/cruds/create/', 'class' => 'form-horizontal validated', 'id' => 'CREATECRUD', 'parsley-validate' => '')) !!}
<div class="row">
	<div class="col-md-12">



		<div class="form-group row">
			<label class="col-sm-3 text-right"></label>
			<div class="col-sm-9">

				<ul class="parsley-error-list">
					@foreach($errors->all() as $error)
						<li>{{ $error }}</li>
					@endforeach
				</ul>

			</div>
		</div>

		<div class="form-group row has-feedback">
			<label class="col-sm-3 text-right"> {{ Lang::get('core.fr_modtitle') }} </label>
			<div class="col-sm-9">
				{!! Form::text('module_title', null, array('class' => 'form-control form-control-sm', 'placeholder' => 'Title Name', 'required' => 'true')) !!}
			</div>
		</div>

		<div class="form-group row ">
			<label class="col-sm-3 text-right"> {{ Lang::get('core.fr_modnote') }} </label>
			<div class="col-sm-9">
				{!! Form::text('module_note', null, array('class' => 'form-control form-control-sm', 'placeholder' => 'Short description module')) !!}

			</div>
		</div>

		<div class="form-group row " style="display:none;">
			<label class="col-sm-3 text-right"> Template : </label>
			<div class="col-sm-9" style="font-size: 11px;">
				<div class="demo-radio-button">
					<input name="module_template" type="hidden" id="basic_checkbox_native" value="native"
						checked="checked">
					@foreach($cruds as $crud)


								<!--				<input name="module_template"  type="radio" id="basic_checkbox_{{ $crud->type }}" value="{{ $crud->type }}"   />
							<label for="basic_checkbox_{{ $crud->type }}" > {{ $crud->name }}  				
								<br /><small > {{ $crud->note }} </small>   
							</label> 
						<br /><br />-->
					@endforeach
				</div>



			</div>
		</div>


		<div class="form-group row ">
			<label class="col-sm-3 text-right">Class Controller </label>
			<div class="col-sm-9">
				{!! Form::text('module_name', null, array('class' => 'form-control form-control-sm', 'placeholder' => 'Class Controller / Module Name', 'required' => 'true')) !!}

			</div>
		</div>


		<div class="form-group row">
			<label class="col-sm-3 text-right"> {{ Lang::get('core.fr_modtable') }} </label>
			<div class="col-sm-9">
				{!! Form::select(
	'module_db',
	$tables,
	'',
	array('class' => 'form-control form-control-sm', 'required' => 'true')
) 
		!!}

			</div>
		</div>

		<div class="form-group row">
			<label class="col-sm-3 text-right"> Is Submodule </label>
			<div class="col-sm-9">
				{!! Form::checkbox('is_submodule') !!}
			</div>
		</div>

		<div class="form-group parentModuleBlock row" style="display:none;">
			<label class="col-sm-3 text-right"> Parent Module </label>
			<div class="col-sm-9">
				{!! Form::select(
	'parent_id',
	$parentModules,
	'',
	array('class' => 'form-control form-control-sm')
)
		!!}
			</div>
		</div>

		<div class="form-group row">
			<label class="col-sm-3 text-right"> Is Entity </label>
			<div class="col-sm-9">
				{!! Form::checkbox('is_entity') !!}
			</div>
		</div>
		<div class="form-group row">
			<label class="col-sm-3 text-right"> Is BulkUpload </label>
			<div class="col-sm-9">
				{!! Form::checkbox('is_bulkupload') !!}
			</div>
		</div>
		<div class="form-group row">
			<label class="col-sm-3 text-right"> Is MultiLingual </label>
			<div class="col-sm-9">
				{!! Form::checkbox('is_multi_lingual') !!}
			</div>
		</div>

		<div class="form-group row " style="display:none;">
			<label class="col-sm-3 text-right">Author </label>
			<div class="col-sm-9">
				{!! Form::text('module_author', null, array('class' => 'form-control form-control-sm', 'placeholder' => 'Author')) !!}

			</div>
		</div>



		<div class="form-group row switchSql">
			<label class="col-sm-3 text-right"> </label>
			<div class="col-sm-9">
				<label class="">
					<input type="radio" name="creation" value="auto" id="auto" checked="checked"
						class="minimal-green" />
					<label for="auto">{{ Lang::get('core.fr_modautosql') }} </label>
				</label>
				<label class="">
					<input type="radio" name="creation" value="manual" id="manual" />
					<label for="manual">{{ Lang::get('core.fr_modmanualsql') }}</label>
				</label>
			</div>
		</div>

		<div class="form-group row manualsql">
			<label class="col-sm-3 text-right"> </label>
			<div class="col-sm-9">
				{!! Form::textarea('sql_select', null, array('class' => 'form-control', 'placeholder' => 'SQL Select & Join Statement', 'rows' => '3', 'id' => 'sql_select')) !!}

			</div>
		</div>

		<div class="form-group row manualsql">
			<label class="col-sm-3 text-right"> </label>
			<div class="col-sm-9">
				{!! Form::textarea('sql_where', null, array('class' => 'form-control', 'placeholder' => 'SQL Where Statement', 'rows' => '2', 'id' => 'sql_where')) !!}
			</div>
		</div>

		<div class="form-group row manualsql">
			<label class="col-sm-3 text-right"> </label>
			<div class="col-sm-9">
				{!! Form::textarea('sql_group', null, array('class' => 'form-control', 'placeholder' => 'SQL Grouping Statement', 'rows' => '2')) !!}
			</div>
		</div>


		<div class="form-group row">
			<label class="col-sm-3 text-right">&nbsp;</label>
			<div class="col-sm-9">
				<button type="submit" class="btn btn-primary btn-sm  " style="text-transform: uppercase;">
					{{ Lang::get('core.sb_submit') }} AND GENERATE MODULE </button>

			</div>

		</div>
	</div>

</div>
{!! Form::close() !!}

</div>



<script type="text/javascript">
	$(document).ready(function () {
		<?php echo saveFormConfigDetails('CREATECRUD'); ?>

		$('[name="is_submodule"]').change(function () {
			if (this.checked) {
				$('.parentModuleBlock').show();
				$('[name="parent_id"]').attr("required", "true");
			} else {
				$('.parentModuleBlock').hide();
				$('[name="parent_id"]').removeAttr("required");
			}
		});

		$('.manualsql').hide();
		$('.switchSql input:radio').on('click', function () {
			val = $(this).val();
			if (val == 'manual') {
				$('.manualsql').show();
				$('#sql_select').attr("required", "true");
				$('#sql_where').attr("required", "true");

			} else {
				$('.manualsql').hide();
				$('#sql_select').removeAttr("required");
				$('#sql_where').removeAttr("required");

			}

		});

	});

</script>
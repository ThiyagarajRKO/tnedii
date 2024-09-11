<div class="form-group col-12">
    <div class="form-actions">
        <div class="btn-set text-center">
            {{-- @Cutomized Ramesh.Esakki -> Added Back Button--}}
            <a href="javascript:window.history.back()" name="submit" value="back" class="btn btn-info backBtn">
                <i class="fa fa-arrow-left"></i> Back
            </a>
            {{-- @Cutomized Ramesh.Esakki -> Added Back Button--}}
            <button type="submit" name="submit" value="submit" class="btn btn-success">
                <i class="fa fa-check-circle"></i> {{ trans('core/acl::users.update') }}
            </button>
        </div>
    </div>
</div>

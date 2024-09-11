<div class="tabbable-custom">
                <ul class="nav nav-tabs workflow-tabs">
                    <li class="nav-item">
                        <a href="#permissions" class="nav-link active" data-toggle="tab">Permission </a>
                    </li>
                    <li class="nav-item">
                        <a href="#mail" class="nav-link" data-toggle="tab">Mail Content </a>
                    </li>
                    <li class="nav-item">
                        <a href="#meta" class="nav-link" data-toggle="tab">Meta Data </a>
                    </li>
                    
                </ul>
                <div class="tab-content">
                    <div class="row">
                        <div class="col-md-6">
                            <input type="hidden" name="workflows_id" value="{{$workflow->id}}"/>
                        </div>
                    </div>
                    <div class="tab-pane active" id="permissions">
                        @include("plugins/workflows::workflow-permissions")
                        <div class="clearfix"></div>
                    </div>
                    <div class="tab-pane " id="mail">
                        @include("plugins/workflows::workflow-mail-config")
                        <div class="clearfix"></div>
                    </div>
                    <div class="tab-pane " id="meta">
                        @include("plugins/workflows::workflow-meta-config")
                        <div class="clearfix"></div>
                    </div>
                    
                </div>
            </div>

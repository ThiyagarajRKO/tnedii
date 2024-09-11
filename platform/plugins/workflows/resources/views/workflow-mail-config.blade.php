<div class="max-width-1200">
        <div class="flexbox-annotated-section">
            <div class="flexbox-annotated-section-annotation">
                
                <div class="annotated-section-title">
                    <legend class="grouppedLegend">{{ trans('core/setting::setting.email.title') }}</legend>
                </div>
                <div class="row">
                <div class="annotated-section-description pd-all-20 p-none-t  col-md-6">
                    <p class="color-note">
                        {!! clean(trans('core/setting::setting.email.description')) !!}
                    </p>
                    <div class="available-variable">
                        @foreach($mailConfig as $moduleKey => $moduleVariable)
                            <p><span class="text-danger field-title">{{ $moduleKey }}</span>: {{ trans($moduleVariable) }}</p>
                        @endforeach
                    </div>
                </div>
                @if(!empty($moduleFields))    
                <div class="annotated-section-description pd-all-20 p-none-t col-md-6">
                    <p class="color-note">
                        {!! clean(trans('core/setting::setting.email.description')) !!}
                    </p>
                    <div class="available-variable">
                        <p><span class="text-danger">{{implode(", ",$moduleFields) }}</span></p>
                    </div>
                </div>
                @endif
            </div>
            </div>

            <div class="flexbox-annotated-section-content">
                <div class="wrapper-content pd-all-20 email-template-edit-wrap">
                 
                        @if ($emailSubject)
                        <div class="form-group">
                            <label class="text-title-field"
                                   for="email_subject">
                                {{ trans('core/setting::setting.email.subject') }}
                            </label>
                            
                            <input data-counter="300" type="text" class="next-input"
                                   name="email_subject"
                                   id="email_subject"
                                   value="{{ $emailSubject }}">
                        </div>
                    @endif
             
                    <div class="form-group">
                        
                        <label class="text-title-field"
                               for="email_content">{{ trans('core/setting::setting.email.content') }}</label>
                        <textarea id="mail-template-editor" name="email_content" class="form-control" style="overflow-y:scroll!important; height: 200px;">{{ $emailContent }}</textarea>
                    </div>
                </div>
            </div>

        </div>

    </div>
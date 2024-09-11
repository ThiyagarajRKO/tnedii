@php

$states = \DB::table('states')->select('state_name', 'state_id')->where("country_id", "=", "107")->orderBy('state_name', 'asc')->pluck('state_name', 'state_id')->toArray();
$sectors_core_competencies = ['Agriculture', 'Arts/Humanities/Social sciences', 'Biotechnology', 'Commerce', 'Engineering & Technology', 'E-commerce', 'Food Technology', 'Health', 'Leather Technology', 'Marketing', 'Medical/Health', 'Medical Devices', 'Management', 'Water and Sanitation', 'other' => 'Other (Please Specify)'];
$knowledge_partner_and_innovatorrelationship = ['Subsidiary', 'Related party entity', 'Student', 'Innovator'];
//echo "<pre>";
//print_r($sectors_core_competencies);
//echo "</pre>";

@endphp
<div class="">
	<div id="" class="row justify-content-center">
		<div id="" class="">
		    {!! Form::open(['route' => 'public.send.knowledge-partner', 'method' => 'POST', 'class' => 'knowledge-partner-form', 'id' => 'knowledge-partner-form', 'enctype' => 'multipart/form-data']) !!}
				{{ csrf_field() }}  
    			<div class="main_form_content wizard clearfix">
    			    <div class="steps clearfix">
                    	<ul role="tablist" class="top_number_tab">
                    		<li class="first current" data-step="1"><a id="jquery-steps-t-0" href="#jquery-steps-h-0"><span class="number">1.</span> Basic Information</a></li>
                    		<li class="disabled" data-step="2"><a id="jquery-steps-t-1" href="#jquery-steps-h-1"><span class="number">2.</span> Sectors</a></li>
                    		<li class="disabled" data-step="3"><a id="jquery-steps-t-2" href="#jquery-steps-h-2"><span class="number">3.</span> Research &amp; Development</a></li>
                    		<li class="disabled" data-step="4"><a id="jquery-steps-t-3" href="#jquery-steps-h-3"><span class="number">4.</span> Facilities</a></li>
                    		<li class="disabled" data-step="5"><a id="jquery-steps-t-4" href="#jquery-steps-h-4"><span class="number">5.</span> Technical support </a></li>
                    		<li class="disabled" data-step="6"><a id="jquery-steps-t-5" href="#jquery-steps-h-5"><span class="number">6.</span> VI IPR related Registrations </a></li>
                    		<li class="disabled last" data-step="7"><a id="jquery-steps-t-6" href="#jquery-steps-h-6"><span class="number">7.</span> VII Financial Support</a></li>
                    	</ul>
                    </div>
                    <div class="actions clearfix">
        				<section class="each_step_section active_step" data-step="1">
        					<h2><strong>Basic Information of the Institution/Incubation Centre</strong></h2>
    						<div class="row">
    							<div class="form-group col-md-12">
    								<label for="name_of_the_institution"><strong>{{ trans('plugins/knowledge-partner::knowledge-partner.tables.name_of_the_institution') }}</strong></label>
    								{!! Form::text('name_of_the_institution', old('name_of_the_institution') ? old('name_of_the_institution') : '', ['class' => 'form-control form-control-lg', 'id'=>'name_of_the_institution', 'tabindex' => 1, 'placeholder' => trans('plugins/knowledge-partner::knowledge-partner.tables.name_of_the_institution')]) !!}
    							</div>
    							<div class="form-group col-md-12">
    								<label for="office_address"><strong>{{ trans('plugins/knowledge-partner::knowledge-partner.tables.office_address') }}</strong></label>
    								{!! Form::textarea('office_address', old('office_address') ? old('office_address') : '', ['class' => 'form-control form-control-lg', 'id'=>'office_address', 'tabindex' => 2, 'rows' => 5, 'placeholder' => trans('plugins/knowledge-partner::knowledge-partner.tables.office_address')]) !!}
    							</div>
    							<div class="form-group col-md-6">
    								<label for="state_id"><strong>{{ trans('plugins/knowledge-partner::knowledge-partner.tables.state') }}</strong></label>
    								{!! Form::select('state_id', $states, old('state') ? old('state') : '', ['class' => 'form-control form-control-lg', 'id'=>'office_address', 'tabindex' => 3, 'placeholder' => trans('plugins/knowledge-partner::knowledge-partner.tables.state')]) !!}
    							</div>
    							<div class="form-group col-md-6">
    								<label for="district"><strong>{{ trans('plugins/knowledge-partner::knowledge-partner.tables.district') }}</strong></label>
    								{!! Form::text('district', old('district') ? old('district') : '', ['class' => 'form-control form-control-lg', 'id'=>'district', 'tabindex' => 4, 'placeholder' => trans('plugins/knowledge-partner::knowledge-partner.tables.district')]) !!}
    							</div>
    							<div class="form-group col-md-6">
    								<label for="pin_code"><strong>{{ trans('plugins/knowledge-partner::knowledge-partner.tables.pin_code') }}</strong></label>
    								{!! Form::text('pin_code', old('pin_code') ? old('pin_code') : '', ['class' => 'form-control form-control-lg', 'id'=>'pin_code', 'tabindex' => 5, 'placeholder' => trans('plugins/knowledge-partner::knowledge-partner.tables.pin_code')]) !!}
    							</div>
    							<div class="form-group col-md-6">
    								<label for="institution_registered_under_or_accredited_by"><strong>{{ trans('plugins/knowledge-partner::knowledge-partner.tables.institution_registered_under_or_accredited_by') }}</strong></label>
    								{!! Form::text('institution_registered_under_or_accredited_by', old('institution_registered_under_or_accredited_by') ? old('institution_registered_under_or_accredited_by') : '', ['class' => 'form-control form-control-lg', 'id'=>'institution_registered_under_or_accredited_by', 'tabindex' => 6, 'placeholder' => trans('plugins/knowledge-partner::knowledge-partner.tables.institution_registered_under_or_accredited_by')]) !!}
    							</div>
    							<div class="form-group col-md-6">
    								<label for="year_of_establishment_or_inception"><strong>{{ trans('plugins/knowledge-partner::knowledge-partner.tables.year_of_establishment_or_inception') }}</strong></label>
    								{!! Form::text('year_of_establishment_or_inception', old('year_of_establishment_or_inception') ? old('year_of_establishment_or_inception') : '', ['class' => 'form-control form-control-lg', 'id'=>'year_of_establishment_or_inception', 'tabindex' => 7, 'min' => 1600, 'placeholder' => trans('plugins/knowledge-partner::knowledge-partner.tables.year_of_establishment_or_inception')]) !!}
    							</div>
    							<div class="form-group col-md-6">
    								<label for="upload_proof_of_registration"><strong>{{ trans('plugins/knowledge-partner::knowledge-partner.tables.upload_proof_of_registration') }}</strong><small> (Multiple file can be chosen) (Please upload only jpg or pdf format)</small></label>
    								{!! Form::file('upload_proof_of_registration_upload[]', ['class' => 'form-control form-control-lg', 'id'=>'upload_proof_of_registration', 'tabindex' => 8, 'accept' => '.pdf,.jpg,.jpeg', 'multiple' => true, 'placeholder' => trans('plugins/knowledge-partner::knowledge-partner.tables.upload_proof_of_registration')]) !!}
    							</div>
    							<div class="form-group col-md-4">
    								<label for="gst_no"><strong>{{ trans('plugins/knowledge-partner::knowledge-partner.tables.gst_no') }}</strong></label>
    								{!! Form::text('gst_no', old('gst_no') ? old('gst_no') : '', ['class' => 'form-control form-control-lg', 'id'=>'gst_no', 'tabindex' => 9, 'placeholder' => trans('plugins/knowledge-partner::knowledge-partner.tables.gst_no')]) !!}
    							</div>
    							<div class="form-group col-md-4">
    								<label for="pan"><strong>{{ trans('plugins/knowledge-partner::knowledge-partner.tables.pan') }}</strong></label>
    								{!! Form::text('pan', old('pan') ? old('pan') : '', ['class' => 'form-control form-control-lg', 'id'=>'pan', 'tabindex' => 10, 'placeholder' => trans('plugins/knowledge-partner::knowledge-partner.tables.pan')]) !!}
    							</div>
    							<div class="form-group col-md-4">
    								<label for="tin"><strong>{{ trans('plugins/knowledge-partner::knowledge-partner.tables.tin') }}</strong></label>
    								{!! Form::text('tin', old('tin') ? old('tin') : '', ['class' => 'form-control form-control-lg', 'id'=>'tin', 'tabindex' => 11, 'placeholder' => trans('plugins/knowledge-partner::knowledge-partner.tables.tin')]) !!}
    							</div>
    							<div class="form-group col-md-12">
    								<label for="board_founders_director"><strong>{{ trans('plugins/knowledge-partner::knowledge-partner.tables.board_founders_director') }}</strong></label>
    								{!! Form::textarea('board_founders_director', old('board_founders_director') ? old('board_founders_director') : '', ['class' => 'form-control form-control-lg', 'id'=>'board_founders_director', 'tabindex' => 12, 'rows' => 3, 'placeholder' => trans('plugins/knowledge-partner::knowledge-partner.tables.board_founders_director')]) !!}
    							</div>
    							<div class="form-group col-md-6">
    								<label for="years_of_experience_in_incubation"><strong>{{ trans('plugins/knowledge-partner::knowledge-partner.tables.years_of_experience_in_incubation') }}</strong></label>
    								{!! Form::text('years_of_experience_in_incubation', old('years_of_experience_in_incubation') ? old('years_of_experience_in_incubation') : '', ['class' => 'form-control form-control-lg', 'id'=>'years_of_experience_in_incubation', 'tabindex' => 13, 'min' => 0, 'placeholder' => trans('plugins/knowledge-partner::knowledge-partner.tables.years_of_experience_in_incubation')]) !!}
    							</div>
    							<div class="form-group col-md-6">
    								<label for="ceo"><strong>{{ trans('plugins/knowledge-partner::knowledge-partner.tables.ceo') }}</strong></label>
    								{!! Form::text('ceo', old('ceo') ? old('ceo') : '', ['class' => 'form-control form-control-lg', 'id'=>'ceo', 'tabindex' => 14, 'placeholder' => trans('plugins/knowledge-partner::knowledge-partner.tables.ceo')]) !!}
    							</div>
    							<div class="form-group col-md-6">
    								<label for="lead_scientist_or_incubation_manager"><strong>{{ trans('plugins/knowledge-partner::knowledge-partner.tables.lead_scientist_or_incubation_manager') }}</strong></label>
    								{!! Form::text('lead_scientist_or_incubation_manager', old('lead_scientist_or_incubation_manager') ? old('lead_scientist_or_incubation_manager') : '', ['class' => 'form-control form-control-lg', 'id'=>'lead_scientist_or_incubation_manager', 'tabindex' => 15, 'placeholder' => trans('plugins/knowledge-partner::knowledge-partner.tables.lead_scientist_or_incubation_manager')]) !!}
    							</div>
    							<div class="form-group col-md-6">
    								<label for="years_of_experience_in_r_and_d"><strong>{{ trans('plugins/knowledge-partner::knowledge-partner.tables.years_of_experience_in_r_and_d') }}</strong></label>
    								{!! Form::text('years_of_experience_in_r_and_d', old('years_of_experience_in_r_and_d') ? old('years_of_experience_in_r_and_d') : '', ['class' => 'form-control form-control-lg', 'id'=>'years_of_experience_in_r_and_d', 'tabindex' => 16, 'min' => 0, 'placeholder' => trans('plugins/knowledge-partner::knowledge-partner.tables.years_of_experience_in_r_and_d')]) !!}
    							</div>
    							<div class="form-group col-md-6">
    								<label for="sources_of_financial_support"><strong>{{ trans('plugins/knowledge-partner::knowledge-partner.tables.sources_of_financial_support') }}</strong></label>
    								{!! Form::text('sources_of_financial_support', old('sources_of_financial_support') ? old('sources_of_financial_support') : '', ['class' => 'form-control form-control-lg', 'id'=>'sources_of_financial_support', 'tabindex' => 17, 'placeholder' => trans('plugins/knowledge-partner::knowledge-partner.tables.sources_of_financial_support')]) !!}
    							</div>
    							<div class="form-group col-md-12">
    								<label for="key_recognition_award_received_by_institute"><strong>{{ trans('plugins/knowledge-partner::knowledge-partner.tables.key_recognition_award_received_by_institute') }} (If any)</strong><small> (Multiple file can be chosen) (Please upload only jpg or pdf format)</small></label>
    								{!! Form::file('key_recognition_award_received_by_institute_upload[]', ['class' => 'form-control form-control-lg', 'id'=>'key_recognition_award_received_by_institute', 'tabindex' => 18, 'accept' => '.pdf,.jpg,.jpeg', 'multiple' => true, 'placeholder' => trans('plugins/knowledge-partner::knowledge-partner.tables.key_recognition_award_received_by_institute')]) !!}
    							</div>
    							<div class="form-group col-md-12">
    								<label for="certifications"><strong>{{ trans('plugins/knowledge-partner::knowledge-partner.tables.certifications') }} (If any)</strong><small> (Multiple file can be chosen) (Please upload only jpg or pdf format)</small></label>
    								{!! Form::file('certifications_upload[]', ['class' => 'form-control form-control-lg', 'id'=>'certifications', 'tabindex' => 19, 'accept' => '.pdf,.jpg,.jpeg', 'multiple' => true, 'placeholder' => trans('plugins/knowledge-partner::knowledge-partner.tables.certifications')]) !!}
    							</div>
    							<!-- <div class="col-lg-12 mb-3">
    								<div class="each-field-box">
    									<label for=""><strong>Key recognition Award Received by Institute (If any Attach Proof)	<small>(Please upload only jpg or pdf format)</small></strong></label>
    									<div class="row">
    									<div class="form-group video_container">
                                            <div class="col-sm-11">
                                              <input type="file" class="knowledge-partner-form-input" name="award-received-by-institute[]">
                                            </div>
                                            <div class="col-sm-1">
                                              <button class="btn btn-success video-add">
                                                <span class="fa fa-plus"></span>
                                              </button>
                                            </div>
                                          </div>
                                        </div>
    								</div>
    							</div>
    							<div class="col-lg-12 mb-3">
    								<div class="each-field-box">
    									<label for=""><strong>Certifications (If any) <small>(Please upload only jpg or pdf format)</small></strong></label>
    									<div class="row">
    										<div class="form-group video_container">
                                                <div class="col-sm-11">
                                                  <input type="file" class="knowledge-partner-form-input" name="certifications[]">
                                                </div>
                                                <div class="col-sm-1">
                                                  <button class="btn btn-success video-add">
                                                    <span class="fa fa-plus"></span>
                                                  </button>
                                                </div>
                                            </div>
                                        </div>
    								</div>
    							</div> -->
    							<div class="form-group col-md-6">
    								<label for="contact_person"><strong>{{ trans('plugins/knowledge-partner::knowledge-partner.tables.contact_person') }}</strong></label>
    								{!! Form::text('contact_person', old('contact_person') ? old('contact_person') : '', ['class' => 'form-control form-control-lg', 'id'=>'contact_person', 'tabindex' => 20, 'placeholder' => trans('plugins/knowledge-partner::knowledge-partner.tables.contact_person')]) !!}
    							</div>
    							<div class="form-group col-md-6">
    								<label for="mobile_number"><strong>{{ trans('plugins/knowledge-partner::knowledge-partner.tables.mobile_number') }}</strong></label>
    								{!! Form::text('mobile_number', old('mobile_number') ? old('mobile_number') : '', ['class' => 'form-control form-control-lg', 'id'=>'mobile_number', 'tabindex' => 21, 'placeholder' => trans('plugins/knowledge-partner::knowledge-partner.tables.mobile_number')]) !!}
    							</div>
    							<div class="form-group col-md-6">
    								<label for="email_address"><strong>{{ trans('plugins/knowledge-partner::knowledge-partner.tables.email_address') }}</strong></label>
    								{!! Form::text('email_address', old('email_address') ? old('email_address') : '', ['class' => 'form-control form-control-lg', 'id'=>'email_address', 'tabindex' => 22, 'placeholder' => trans('plugins/knowledge-partner::knowledge-partner.tables.email_address')]) !!}
    							</div>
    							<div class="form-group col-md-6">
    								<label for="website"><strong>{{ trans('plugins/knowledge-partner::knowledge-partner.tables.website') }}</strong></label>
    								{!! Form::text('website', old('website') ? old('website') : '', ['class' => 'form-control form-control-lg', 'id'=>'website', 'tabindex' => 23, 'placeholder' => trans('plugins/knowledge-partner::knowledge-partner.tables.website')]) !!}
    							</div>
    						</div>
        				</section>
        				<section class="each_step_section" data-step="2">
        					<h2><strong>{{ trans('plugins/knowledge-partner::knowledge-partner.tables.sectors_core_competencies') }}: (Please tick the appropriate response)</strong></h2>
    						<div class="row">
    						    @php
    						        $tab_index = 23; 
    						    @endphp
    						    @foreach($sectors_core_competencies as $key => $val)
    						        @php
    						            $tab_index++;
    						        @endphp
        							<div class="col-lg-6">
        								<div class="each-field-box d-flex align-items-baseline">
        								    {!! Form::checkbox('sectors_core_competencies[]', ($key === "other" ? $key : $val), null, ['class' => '' . $key, 'id'=>'sectors_core_competencies_'.$key, 'tabindex' => $tab_index]) !!} 
    										<label for="sectors_core_competencies_{{ $key }}"><strong>{{ $val }}</strong></label>
        								</div>
        							</div>
    						    @endforeach
    						    @php
    						        $tab_index++; 
    						    @endphp
    							<div class="form-group col-md-12 other-open" style="display:none;">
    								{!! Form::textarea('sectors_core_competencies_other', null, ['class' => 'form-control form-control-lg', 'id'=>'sectors_core_competencies_other_input', 'rows' => 3, 'tabindex' => $tab_index, 'placeholder' => 'Please Specify']) !!}
    							</div>
    						</div>
        				</section>
        				<section class="each_step_section" data-step="3">
        					<h2><strong>{{ trans('plugins/knowledge-partner::knowledge-partner.tables.research_development') }}</strong></h2>
    						<div class="row">
    							<div class="col-lg-12">
    								<div class="each-field-box d-flex gap-30 mb-4">
    									<label for=""><strong>{{ trans('plugins/knowledge-partner::knowledge-partner.tables.have_testing_lab_facilities') }}</strong></label>
    									<div class="each-field-box d-flex align-items-baseline">
            						        @php
            						            $tab_index++;
            						        @endphp
    									    {{Form::radio('have_testing_lab_facilities', 'Yes', ['id' => 'have_testing_lab_facilities_yes', 'tabindex' => $tab_index])}}
                                            <label class="" for="have_testing_lab_facilities_yes"><strong>Yes</strong></label>
    									</div>
    									<div class="each-field-box d-flex align-items-baseline">
            						        @php
            						            $tab_index++;
            						        @endphp
    										{{Form::radio('have_testing_lab_facilities', 'No', ['id' => 'have_testing_lab_facilities_no', 'tabindex' => $tab_index])}}
                                            <label class="" for="have_testing_lab_facilities_no"><strong>No</strong></label>
    									</div>
    								</div>
    							</div>
    							<div class="col-lg-6 mb-3 lab-facilities-extra" style="display:none">
    								<div class="each-field-box">
    									<div class="sub-form-field-area">
    										<div class="each-field-box d-flex align-items-baseline gap-30 mb-3" style="margin-bottom:20px;">
    											<label for="" class="mb-0 w-50"><strong>Type of Lab </strong></label>
    											<div class="lab-choose-wrapper d-flex gap-30 w-50">
    												<div class="each-field-box d-flex align-items-baseline">
                        						        @php
                        						            $tab_index++;
                        						        @endphp
    													{{Form::radio('type_of_lab', 'Dry', ['id' => 'type_of_lab_dry', 'tabindex' => $tab_index])}}
                                                        <label class="" for="type_of_lab_dry"><strong>Dry</strong></label>
    												</div>
    												<div class="each-field-box d-flex align-items-baseline">
                        						        @php
                        						            $tab_index++;
                        						        @endphp
    													{{Form::radio('type_of_lab', 'Wet', ['id' => 'type_of_lab_wet', 'tabindex' => $tab_index])}}
                                                        <label class="" for="type_of_lab_wet"><strong>Wet</strong></label>
    												</div>
    												<div class="each-field-box d-flex align-items-baseline">
                        						        @php
                        						            $tab_index++;
                        						        @endphp
    													{{Form::radio('type_of_lab', 'Both', ['id' => 'type_of_lab_both', 'tabindex' => $tab_index])}}
                                                        <label class="" for="type_of_lab_both"><strong>Both</strong></label>
    												</div>
    											</div>
    										</div>
    									</div>
    								</div>
    							</div>
    							<div class="form-group col-md-6 lab-facilities-extra" style="display:none">
    						        @php
    						            $tab_index++;
    						        @endphp
    								<label for="area_in_sqft"><strong>{{ trans('plugins/knowledge-partner::knowledge-partner.tables.area_in_sqft') }}</strong></label>
    								{!! Form::text('area_in_sqft', old('area_in_sqft') ? old('area_in_sqft') : '', ['class' => 'form-control form-control-lg', 'id'=>'area_in_sqft', 'tabindex' => $tab_index, 'min' => 1, 'placeholder' => trans('plugins/knowledge-partner::knowledge-partner.tables.area_in_sqft')]) !!}
    							</div>
    							<div class="form-group col-md-12 lab-facilities-extra" style="display:none">
    						        @php
    						            $tab_index++;
    						        @endphp
    								<label for="equipments"><strong>{{ trans('plugins/knowledge-partner::knowledge-partner.tables.equipments') }} (Enclose as Annexure)</strong><small> (Multiple file can be chosen) (Please upload only jpg or pdf format)</small></label>
    								{!! Form::file('equipments_upload[]', ['class' => 'form-control form-control-lg', 'id'=>'equipments', 'tabindex' => $tab_index, 'accept' => '.pdf,.jpg,.jpeg', 'multiple' => true, 'placeholder' => trans('plugins/knowledge-partner::knowledge-partner.tables.equipments')]) !!}
    							</div>
    							<div class="form-group col-md-12">
    						        @php
    						            $tab_index++;
    						        @endphp
    								<label for="lab_facility"><strong>{{ trans('plugins/knowledge-partner::knowledge-partner.tables.lab_facility') }}</strong> <small>(Max 500 Characteristics )</small></label>
    								{!! Form::textarea('lab_facility', old('lab_facility') ? old('lab_facility') : '', ['class' => 'form-control form-control-lg', 'id'=>'lab_facility', 'tabindex' => $tab_index, 'rows' => 5, 'maxlength' => 500, 'placeholder' => trans('plugins/knowledge-partner::knowledge-partner.tables.lab_facility')]) !!}
    							</div>
    							<div class="form-group col-md-12">
    						        @php
    						            $tab_index++;
    						        @endphp
    								<label for="service_charge"><strong>{{ trans('plugins/knowledge-partner::knowledge-partner.tables.service_charge') }}</strong></label>
    								{!! Form::text('service_charge', old('service_charge') ? old('service_charge') : '', ['class' => 'form-control form-control-lg', 'id'=>'service_charge', 'tabindex' => $tab_index, 'min' => 1, 'placeholder' => trans('plugins/knowledge-partner::knowledge-partner.tables.service_charge')]) !!}
    							</div>
    							<div class="form-group col-md-12">
    						        @php
    						            $tab_index++;
    						        @endphp
    								<label for="concessions"><strong>{{ trans('plugins/knowledge-partner::knowledge-partner.tables.concessions') }}</strong> <small>(Max 500 Characteristics )</small></label>
    								{!! Form::textarea('concessions', old('concessions') ? old('concessions') : '', ['class' => 'form-control form-control-lg', 'id'=>'concessions', 'tabindex' => $tab_index, 'rows' => 5, 'maxlength' => 500, 'placeholder' => trans('plugins/knowledge-partner::knowledge-partner.tables.concessions')]) !!}
    							</div>
    						</div>
        				</section>
        				<section class="each_step_section" data-step="4">
        					<h2><strong>{{ trans('plugins/knowledge-partner::knowledge-partner.tables.facilities_available') }}</strong></h2>
    						<div class="row">
    							<div class="form-group col-md-12">
    						        @php
    						            $tab_index++;
    						        @endphp
    								<label for="number_of_awareness_training_programs"><strong>{{ trans('plugins/knowledge-partner::knowledge-partner.tables.number_of_awareness_training_programs') }}</strong></label>
    								{!! Form::text('number_of_awareness_training_programs', old('number_of_awareness_training_programs') ? old('number_of_awareness_training_programs') : '', ['class' => 'form-control form-control-lg', 'id'=>'number_of_awareness_training_programs', 'tabindex' => $tab_index, 'min' => 1, 'placeholder' => trans('plugins/knowledge-partner::knowledge-partner.tables.number_of_awareness_training_programs')]) !!}
    							</div>
    							<div class="form-group col-md-12">
    						        @php
    						            $tab_index++;
    						        @endphp
    								<label for="number_of_boot_camp_ideation_etc"><strong>{{ trans('plugins/knowledge-partner::knowledge-partner.tables.number_of_boot_camp_ideation_etc') }}</strong></label>
    								{!! Form::text('number_of_boot_camp_ideation_etc', old('number_of_boot_camp_ideation_etc') ? old('number_of_boot_camp_ideation_etc') : '', ['class' => 'form-control form-control-lg', 'id'=>'number_of_boot_camp_ideation_etc', 'tabindex' => $tab_index, 'min' => 1, 'placeholder' => trans('plugins/knowledge-partner::knowledge-partner.tables.number_of_boot_camp_ideation_etc')]) !!}
    							</div>
    							<div class="form-group col-md-12">
    								@php
    						            $tab_index++;
    						        @endphp
    								<label for="number_of_boot_camp_ideation_etc_files"><strong>{{ trans('plugins/knowledge-partner::knowledge-partner.tables.number_of_boot_camp_ideation_etc') }} Documents (Enclose as Annexure)</strong><small> (Multiple file can be chosen) (Please upload only jpg or pdf format)</small></label>
    								{!! Form::file('number_of_boot_camp_ideation_etc_files_upload[]', ['class' => 'form-control form-control-lg', 'id'=>'number_of_boot_camp_ideation_etc_files', 'tabindex' => $tab_index, 'accept' => '.pdf,.jpg,.jpeg', 'multiple' => true, 'placeholder' => trans('plugins/knowledge-partner::knowledge-partner.tables.number_of_boot_camp_ideation_etc') .  'Documents']) !!}
    							</div>
    							<div class="col-lg-12">
    								<div class="each-field-box d-flex gap-30 mb-4">
    									<label for=""><strong>{{ trans('plugins/knowledge-partner::knowledge-partner.tables.have_you_accelerated_startups') }}</strong></label>
    									<div class="each-field-box d-flex align-items-baseline">
            						        @php
            						            $tab_index++;
            						        @endphp
    									    {{Form::radio('have_you_accelerated_startups', 'Yes', ['id' => 'have_you_accelerated_startups_yes', 'tabindex' => $tab_index])}}
                                            <label class="" for="have_you_accelerated_startups_yes"><strong>Yes</strong></label>
    									</div>
    									<div class="each-field-box d-flex align-items-baseline">
            						        @php
            						            $tab_index++;
            						        @endphp
    										{{Form::radio('have_you_accelerated_startups', 'No', ['id' => 'have_you_accelerated_startups_no', 'tabindex' => $tab_index])}}
                                            <label class="" for="have_you_accelerated_startups_no"><strong>No</strong></label>
    									</div>
    								</div>
    							</div>
    							<div class="form-group col-md-12 three-fiscal-years-box" style="display:none">
    						        @php
    						            $tab_index++;
    						        @endphp
    								<label for="total_number_of_startups_supported"><strong>{{ trans('plugins/knowledge-partner::knowledge-partner.tables.total_number_of_startups_supported') }}</strong></label>
    								{!! Form::text('total_number_of_startups_supported', old('total_number_of_startups_supported') ? old('total_number_of_startups_supported') : '', ['class' => 'form-control form-control-lg', 'id'=>'total_number_of_startups_supported', 'tabindex' => $tab_index, 'min' => 1, 'placeholder' => trans('plugins/knowledge-partner::knowledge-partner.tables.total_number_of_startups_supported')]) !!}
    							</div>
    							<div class="form-group col-md-12">
    								@php
    						            $tab_index++;
    						        @endphp
    								<label for="ivp_applications_and_sanctions"><strong>{{ trans('plugins/knowledge-partner::knowledge-partner.tables.ivp_applications_and_sanctions') }} (Enclose as Annexure)</strong><small> (Multiple file can be chosen) (Please upload only jpg or pdf format)</small></label>
    								{!! Form::file('ivp_applications_and_sanctions_upload[]', ['class' => 'form-control form-control-lg', 'id'=>'ivp_applications_and_sanctions', 'tabindex' => $tab_index, 'accept' => '.pdf,.jpg,.jpeg', 'multiple' => true, 'placeholder' => trans('plugins/knowledge-partner::knowledge-partner.tables.ivp_applications_and_sanctions') .  'Documents']) !!}
    							</div>
    						</div>
        				</section>
        				<section class="each_step_section" data-step="5">
        					<h2><strong>{{ trans('plugins/knowledge-partner::knowledge-partner.tables.technical_support_and_mentorship') }}:</strong></h2>
    						<div class="row">
    							<div class="form-group col-md-12">
    						        @php
    						            $tab_index++;
    						        @endphp
    								<label for="total_no_of_mentors_available"><strong>{{ trans('plugins/knowledge-partner::knowledge-partner.tables.total_no_of_mentors_available') }}</strong></label>
    								{!! Form::text('total_no_of_mentors_available', old('total_no_of_mentors_available') ? old('total_no_of_mentors_available') : '', ['class' => 'form-control form-control-lg', 'id'=>'total_no_of_mentors_available', 'tabindex' => $tab_index, 'min' => 0, 'placeholder' => trans('plugins/knowledge-partner::knowledge-partner.tables.total_no_of_mentors_available')]) !!}
    							</div>
    						</div>
    						<div class="main_mentor_row" style="display: none;">
    						</div>
        						
        				</section>
        				<section class="each_step_section" data-step="6">
        					<h2><strong>{{ trans('plugins/knowledge-partner::knowledge-partner.tables.ipr_related_registrations') }}</strong></h2>
    						<div class="row">
    							<div class="form-group col-md-12">
    						        @php
    						            $tab_index++;
    						        @endphp
    								<label for="number_of_technologies_commercialized"><strong>{{ trans('plugins/knowledge-partner::knowledge-partner.tables.number_of_technologies_commercialized') }}</strong> <small>(Give Short Description)</small></label>
    								{!! Form::textarea('number_of_technologies_commercialized', old('number_of_technologies_commercialized') ? old('number_of_technologies_commercialized') : '', ['class' => 'form-control form-control-lg', 'id'=>'number_of_technologies_commercialized', 'tabindex' => $tab_index, 'rows' => 5, 'placeholder' => trans('plugins/knowledge-partner::knowledge-partner.tables.number_of_technologies_commercialized')]) !!}
    							</div>
    							<div class="form-group col-md-12">
    						        @php
    						            $tab_index++;
    						        @endphp
    								<label for="number_of_indian_or_wipo_compliant_patents_received"><strong>{{ trans('plugins/knowledge-partner::knowledge-partner.tables.number_of_indian_or_wipo_compliant_patents_received') }}</strong> <small>(Give Short Description)</small></label>
    								{!! Form::textarea('number_of_indian_or_wipo_compliant_patents_received', old('number_of_indian_or_wipo_compliant_patents_received') ? old('number_of_indian_or_wipo_compliant_patents_received') : '', ['class' => 'form-control form-control-lg', 'id'=>'number_of_indian_or_wipo_compliant_patents_received', 'tabindex' => $tab_index, 'rows' => 5, 'placeholder' => trans('plugins/knowledge-partner::knowledge-partner.tables.number_of_indian_or_wipo_compliant_patents_received')]) !!}
    							</div>
    						</div>
        				</section>
        				<section class="each_step_section" data-step="7">
        					<!-- <h2><strong>VII {{ trans('plugins/knowledge-partner::knowledge-partner.tables.financial_support_received_for_innovators') }} <small>(Enclose as Annexure format)</small></strong></h2> -->
    						<div class="row">
    							<div class="form-group col-md-12">
    								@php
    						            $tab_index++;
    						        @endphp
    								<label for="financial_support_received_for_innovators"><strong>{{ trans('plugins/knowledge-partner::knowledge-partner.tables.financial_support_received_for_innovators') }} (Enclose as Annexure)</strong><small> (Multiple file can be chosen) (Please upload only jpg or pdf format)</small></label>
    								{!! Form::file('financial_support_received_for_innovators_upload[]', ['class' => 'form-control form-control-lg', 'id'=>'financial_support_received_for_innovators', 'tabindex' => $tab_index, 'accept' => '.pdf,.jpg,.jpeg', 'multiple' => true, 'placeholder' => trans('plugins/knowledge-partner::knowledge-partner.tables.financial_support_received_for_innovators')]) !!}
    							</div>
    							<div class="form-group col-md-12">
    								@php
    						            $tab_index++;
    						        @endphp
    								<label for="your_financial_status"><strong>{{ trans('plugins/knowledge-partner::knowledge-partner.tables.your_financial_status') }} (Attach last 3 years balance sheet)</strong><small> (Multiple file can be chosen) (Please upload only jpg or pdf format)</small></label>
    								{!! Form::file('your_financial_status_upload[]', ['class' => 'form-control form-control-lg', 'id'=>'your_financial_status', 'tabindex' => $tab_index, 'accept' => '.pdf,.jpg,.jpeg', 'multiple' => true, 'placeholder' => trans('plugins/knowledge-partner::knowledge-partner.tables.your_financial_status')]) !!}
    							</div>
                        		<div class="form-group col-md-6">
                        	        @php
                        	            $tab_index++;
                        	        @endphp
                        			<label for="land_and_buildings_on_date"><strong>{{ trans('plugins/knowledge-partner::knowledge-partner.tables.land_and_buildings_on_date') }}</strong></label>
                        			{!! Form::date('land_and_buildings_on_date', old('land_and_buildings_on_date') ?: '', ['class' => 'form-control form-control-lg', 'tabindex' => $tab_index, 'placeholder' => trans('plugins/knowledge-partner::knowledge-partner.tables.land_and_buildings_on_date')]) !!}
                        		</div>
    							<div class="form-group col-md-12">
    								@php
    						            $tab_index++;
    						        @endphp
    								<label for="land_and_buildings_as_on_date"><strong>{{ trans('plugins/knowledge-partner::knowledge-partner.tables.land_and_buildings_as_on_date') }} </strong><small> (Enclosed as Annexure V) (Multiple file can be chosen) (Please upload only jpg or pdf format)</small></label>
    								{!! Form::file('land_and_buildings_as_on_date_upload[]', ['class' => 'form-control form-control-lg', 'id'=>'land_and_buildings_as_on_date', 'tabindex' => $tab_index, 'accept' => '.pdf,.jpg,.jpeg', 'multiple' => true, 'placeholder' => trans('plugins/knowledge-partner::knowledge-partner.tables.land_and_buildings_as_on_date')]) !!}
    							</div>
    							<div class="col-lg-12">
    							    <div class="each-field-box">
    							        <label for=""><strong>X {{ trans('plugins/knowledge-partner::knowledge-partner.tables.knowledge_partner_and_innovatorrelationship') }}</strong></label>
    								    <div class="row">
                						    @foreach($knowledge_partner_and_innovatorrelationship as $key => $val)
                						        @php
                						            $tab_index++;
                						        @endphp
                    							<div class="col-lg-6">
                    								<div class="each-field-box d-flex align-items-baseline">
                    								    {!! Form::checkbox('knowledge_partner_and_innovatorrelationship[]', $val, null, ['class' => '', 'id' => 'knowledge_partner_and_innovatorrelationship_'.$key, 'tabindex' => $tab_index]) !!} 
                										<label for="knowledge_partner_and_innovatorrelationship_{{ $key }}"><strong>{{ $val }}</strong></label>
                    								</div>
                    							</div>
                						    @endforeach
        								</div>
    								</div>
    							</div>
    						</div>
        				</section>
        			</div>
    				<div class="actions clearfix">
                    	<ul role="menu">
                    		<li class="disabled"><a href="#previous">Previous</a></li>
                    		<li><a href="#next" data-current_step="1">Next</a></li>
                    		<li style="display: none;"><a href="#finish">Finish</a></li>
                    	</ul>
                    </div>
    			</div>
			{!! Form::close() !!}
		</div>
	</div>
</div>
<div class="knowledge-partner-form-group">
    <div class="knowledge-partner-message knowledge-partner-success-message" style="display: none"></div>
    <div class="knowledge-partner-message knowledge-partner-error-message" style="display: none"></div>
</div>
<div class="reference_mentor_row" style="display: none;">
    <div class="each_mentor_row row" style="border: 1px solid #ccc;margin: 5px 0;padding: 5px;">
		<div class="form-group col-md-6">
	        @php
	            $tab_index++;
	        @endphp
			<label for=""><strong>{{ trans('plugins/knowledge-partner::knowledge-partner.tables.mentor_name') }}</strong></label>
			{!! Form::text('mentor_details[mentor_name][]', null, ['class' => 'form-control form-control-lg', 'tabindex' => $tab_index, 'placeholder' => trans('plugins/knowledge-partner::knowledge-partner.tables.mentor_name')]) !!}
		</div>
		<div class="form-group col-md-6">
	        @php
	            $tab_index++;
	        @endphp
			<label for=""><strong>{{ trans('plugins/knowledge-partner::knowledge-partner.tables.mentor_qualification') }}</strong></label>
			{!! Form::text('mentor_details[mentor_qualification][]', null, ['class' => 'form-control form-control-lg', 'tabindex' => $tab_index, 'placeholder' => trans('plugins/knowledge-partner::knowledge-partner.tables.mentor_qualification')]) !!}
		</div>
		<div class="form-group col-md-6">
	        @php
	            $tab_index++;
	        @endphp
			<label for=""><strong>{{ trans('plugins/knowledge-partner::knowledge-partner.tables.mentor_designation') }}</strong></label>
			{!! Form::text('mentor_details[mentor_designation][]', null, ['class' => 'form-control form-control-lg', 'tabindex' => $tab_index, 'placeholder' => trans('plugins/knowledge-partner::knowledge-partner.tables.mentor_designation')]) !!}
		</div>
		<div class="form-group col-md-6">
	        @php
	            $tab_index++;
	        @endphp
			<label for=""><strong>{{ trans('plugins/knowledge-partner::knowledge-partner.tables.mentor_date_of_joining_your_organization') }}</strong></label>
			{!! Form::date('mentor_details[mentor_date_of_joining_your_organization][]', null, ['class' => 'form-control form-control-lg', 'tabindex' => $tab_index, 'placeholder' => trans('plugins/knowledge-partner::knowledge-partner.tables.mentor_date_of_joining_your_organization')]) !!}
		</div>
		<div class="form-group col-md-6">
	        @php
	            $tab_index++;
	        @endphp
			<label for=""><strong>{{ trans('plugins/knowledge-partner::knowledge-partner.tables.mentor_number_of_years_experience') }}</strong></label>
			{!! Form::text('mentor_details[mentor_number_of_years_experience][]', null, ['class' => 'form-control form-control-lg', 'tabindex' => $tab_index, 'min' => 0, 'placeholder' => trans('plugins/knowledge-partner::knowledge-partner.tables.mentor_number_of_years_experience')]) !!}
		</div>
		<div class="form-group col-md-6">
	        @php
	            $tab_index++;
	        @endphp
			<label for=""><strong>{{ trans('plugins/knowledge-partner::knowledge-partner.tables.mentor_how_many_innovators_has_guided_so_far') }}</strong></label>
			{!! Form::text('mentor_details[mentor_how_many_innovators_has_guided_so_far][]', null, ['class' => 'form-control form-control-lg', 'tabindex' => $tab_index, 'min' => 0, 'placeholder' => trans('plugins/knowledge-partner::knowledge-partner.tables.mentor_how_many_innovators_has_guided_so_far')]) !!}
		</div>
	</div>
</div>
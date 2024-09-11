@extends('core/base::layouts.master')

@section('content')

    <div class="user-profile row">
    {{-- @Cutomized Ramesh.Esakki -> Added if condition--}}
    @if ($hideUserProfile != "true" && $canChangePassword!="true")
        <div class="col-md-3 col-sm-5 crop-avatar">
            <!-- Profile links -->
            <div class="block">
                <div class="block mt-element-card mt-card-round mt-element-overlay">
                    <div class="thumbnail">
                        <div class="thumb">
                            <div class="profile-userpic mt-card-item">
                                <div class="avatar-view mt-card-avatar mt-overlay-1">
                                    <img src="{{ $user->avatar_url }}" class="img-fluid" alt="avatar">
                                    @if ($canChangeProfile )
                                        <div class="mt-overlay">
                                            <ul class="mt-info">
                                                <li>
                                                    <a class="btn default btn-outline" href="javascript:;">
                                                        <i class="icon-note"></i>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    @endif
                                </div>
                                <div class="mt-card-content">
                                    <h3 class="mt-card-name">@if ($canChangeProfile && isset($user->imp_user_id))<a href="/admin/users/viewdetail/{{ $user->imp_user_id }}">{{ $user->name }}</a> @else {{$user->name}} @endif</h3>
                                </div>
                                @if ($canChangeProfile && isset($user->imp_user_id))
                                <a href="/admin/users/edit-profile/{{ $user->imp_user_id }}?change_profile={{$user->id}}"><button type="button" class="btn btn-primary mb-3 ml-1rem"><i class="fa fa-edit"></i> Profile</button></a>
                                @elseif ($canChangeProfile && isset($user->imp_student_id))
                                <div class="btn-group">
                                    <a href="/admin/students/edit-profile/{{ $user->imp_student_id }}?change_profile={{$user->id}}"><button type="button" class="btn btn-primary mb-3 ml-1rem"><i class="fa fa-edit"></i> Profile</button></a>
                                    <a href="/admin/students/viewdetail/{{ $user->imp_student_id }}?change_profile={{$user->id}}"><button type="button" class="btn btn-primary mb-3 ml-1rem"><i class="fa fa-eye"></i> Profile</button></a>
                                </div>                                
                                <div class="btn-group">
                                     @if(checkAcademicStatus($user->imp_student_id))
                                       <button type="button" class="btn btn-danger mb-3 ml-1rem mr-1rem" data-target='#optOutModal' data-toggle="modal">OptOut</button>
                                         @if(checkRequestReJoin($user->imp_student_id,getAttributeOptionId([GRADUATE_ATTRIBUTE_SLUG,TERMINATED_ATTRIBUTE_SLUG])))
                                        <button type="button"class="btn btn-danger mb-3 ml-1rem mr-1rem" id="reJoin" data-student_id="{{$user->imp_student_id}}">ReJoin</button>
                                         @endif
                                     @else
                                        <button type="button" class="btn btn-warning mb-3 ml-1rem mr-2rem" data-target='#requestStatusModal' data-toggle="modal">Change Request</button>
                                     @endif  
                                        </div>
                                        @elseif ($canChangeProfile && isset($user->alumni_id))
                                        <a href="/admin/alumnis/edit-profile/{{ $user->alumni_id }}?change_profile={{$user->id}}"><button type="button" class="btn btn-primary mb-3 ml-1rem">Edit Profile</button></a>
                                        @endif
                                </div>
                        </div>
                    </div>
                </div>
        @endif
        </div>
            <!-- /profile links -->

            @if ($canChangeProfile && !$canChangePassword)
                <div class="modal fade" id="avatar-modal" tabindex="-1" role="dialog" aria-labelledby="avatar-modal-label"
                     aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <form class="avatar-form" method="post" action="{{ route('users.profile.image', $user->id) }}" enctype="multipart/form-data">
                                <div class="modal-header">
                                    <h4 class="modal-title" id="avatar-modal-label"><i class="til_img"></i><strong>{{ trans('core/acl::users.change_profile_image') }}</strong></h4>
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                                        <span aria-hidden="true">×</span>
                                    </button>
                                </div>
                                <div class="modal-body">

                                    <div class="avatar-body">

                                        <!-- Upload image and data -->
                                        <div class="avatar-upload">
                                            <input class="avatar-src" name="avatar_src" type="hidden">
                                            <input class="avatar-data" name="avatar_data" type="hidden">
                                            <input type="hidden" name="user_id" value="{{ $user->id }}"/>
                                            {!! Form::token() !!}
                                            <label for="avatarInput">{{ trans('core/acl::users.new_image') }}</label>
                                            <input class="avatar-input" id="avatarInput" name="avatar_file" type="file">
                                        </div>

                                        <div class="loading" tabindex="-1" role="img" aria-label="{{ trans('core/acl::users.loading') }}"></div>

                                        <!-- Crop and preview -->
                                        <div class="row">
                                            <div class="col-md-9">
                                                <div class="avatar-wrapper"></div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="avatar-preview preview-lg"></div>
                                                <div class="avatar-preview preview-md"></div>
                                                <div class="avatar-preview preview-sm"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button class="btn btn-secondary" type="button" data-dismiss="modal">{{ trans('core/acl::users.close') }}</button>
                                    <button class="btn btn-primary avatar-save" type="submit">{{ trans('core/acl::users.save') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div><!-- /.modal -->
            @endif

        </div>
        {{-- @Cutomized Ramesh.Esakki --}}
        <div class="<?php echo ($hideUserProfile == "true") ? 'col-md-12 col-sm-10': 'col-md-9 col-sm-7';?>">
            <div class="profile-content">
                <div class="tabbable-custom">
                    <ul class="nav nav-tabs">
                    {{-- @Cutomized Ramesh.Esakki -> Added if condition--}}
                    @if ($hideUserProfile != "true")
						@if(isset($user->imp_user_id) && isset($user->imp_student_id) && isset($user->alumni_id))
                        <li class="nav-item">
                            <a href="#tab_1_1" class="nav-link active" data-toggle="tab" aria-expanded="true">{{ trans('core/acl::users.info.title') }}</a>
                        </li>
						@endif
                        @if ($canChangeProfile)
                            <li class="nav-item">
                                <a href="#tab_1_3" class="nav-link @if(isset($user->imp_user_id) || isset($user->imp_student_id) || isset($user->alumni_id)) active @endif" data-toggle="tab" aria-expanded="false">{{ trans('core/acl::users.change_password') }}</a>
                            </li>
                        @endif                    
                    @endif
                        {{-- @Cutomized Sabari Shankar.Parthiban begin --}}
                         @if ($canChangePassword)
                            <li class="nav-item">
                                <a href="#tab_1_3" class="nav-link active" data-toggle="tab" aria-expanded="false">{{ trans('core/acl::users.change_password') }}</a>
                            </li>
                        @endif
                        @if(Auth::id() != $id && $canChangePassword!="true")
                        <li class="nav-item">
                            <a href="#tab_1_4" class="nav-link" data-toggle="tab" aria-expanded="true">{{ trans('core/acl::users.role') }}</a>
                        </li>                        
                        @if(setting('user_level_permission'))
                        <li class="nav-item">
                            <a href="#tab_1_5" class="nav-link" data-toggle="tab" aria-expanded="true">{{ trans('core/acl::permissions.permissions') }}</a>
                        </li>
                        @endif
                        @if(isset($entities) && !empty($entities))
                        <li class="nav-item">
                            <a href="#tab_1_6" class="nav-link" data-toggle="tab" aria-expanded="true">{{ trans('core/acl::users.data_level_security') }}</a>
                        </li>
                        @endif
                        @endif
                        {{-- @Cutomized Sabari Shankar.Parthiban end --}}
                        {!! apply_filters(ACL_FILTER_PROFILE_FORM_TABS, null) !!}
                    </ul>
                    <div class="tab-content">
                    <!-- PERSONAL INFO TAB -->
                    @if ($hideUserProfile != "true")
						@if(isset($user->imp_user_id) && isset($user->imp_student_id) && isset($user->alumni_id))
                        <div class="tab-pane active" id="tab_1_1">
                        {!! $form !!}
                    </div>
					@endif
                    @elseif($canChangePassword != "true")
                        <span class="role-container-loader"><i class="fa fa-spinner"></i></span>
                    @endif
                    <!-- END PERSONAL INFO TAB -->
                    <!-- CHANGE PASSWORD TAB -->
                    @if ($canChangeProfile || $canChangePassword)
                        <div class="tab-pane @if(isset($user->imp_user_id) || isset($user->imp_student_id) || isset($user->alumni_id)) active @endif" id="tab_1_3">
                            {!! $passwordForm !!}
                        </div>
                    @endif
                    <!-- END CHANGE PASSWORD TAB -->
                    {{-- @Cutomized Sabari Shankar.Parthiban begin Role TAB --}}
					@if(Auth::id() != $id && $canChangePassword!="true")
                        <div class="tab-pane" id="tab_1_4">
                            @include("core/acl::users.profile.role-page")
                        </div>
                    {{-- Role TAB End --}}                    
                    {{-- User level permission TAB begin --}}
                        <div class="tab-pane" id="tab_1_5">
                            <form method="POST" action="{{route('users.permission-mapping', $id)}}" accept-charset="UTF-8" id="userPermissionMapping">
                                 @csrf
                                @include('core/acl::roles.permissions-lists', compact('active', 'flags', 'children'))
                                <div class="form-group col-12">
                                    @include("core/acl::users.profile.actions")
                                </div>
                            </form>                            
                        </div>
                    {{-- @Cutomized Sabari Shankar.Parthiban END User level permission TAB --}}
                    {{-- Data level security TAB begin --}}
                        <div class="tab-pane" id="tab_1_6">
                            @include("core/acl::users.profile.access-page")
                        </div>
						@endif
                    {{-- @Cutomized Sabari Shankar.Parthiban END Data level security TAB --}}
                    {!! apply_filters(ACL_FILTER_PROFILE_FORM_TAB_CONTENTS, null) !!}
                </div>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
    @if(isset($user->imp_student_id))
    @include("core/acl::users.profile.optout-status",['studentId'=>$user->imp_student_id])
    @include("plugins/student::status-request",['studentId'=>$user->imp_student_id])
    @endif
@stop

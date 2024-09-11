<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
/*  @customized Sabari Shankar.Parthiban */
use Illuminate\Support\Facades\Auth;
use DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Utils\CrudHelper;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Arr;

class HookServiceProvider extends ServiceProvider
{
    protected $applyDataLevelSecurity = false;
    public function boot()
    {
        add_filter(BASE_FILTER_AFTER_SETTING_CONTENT, [$this, 'addSettings'], 300);

        add_filter(BASE_FILTER_TABLE_QUERY, [$this, 'applyEnableCondition'], 150, 3);
        add_filter(BASE_FILTER_TABLE_QUERY, [$this, 'applyDeletedAtCondition'], 157, 3);
        add_filter(BASE_FILTER_TABLE_QUERY, [$this, 'applyVisibleByUserOnlyCondition'], 158, 3);
        add_filter(BASE_FILTER_TABLE_QUERY, [$this, 'filterFinancialYear'], 159, 3);
        add_filter(ADD_CUSTOM_ACTION, [$this, 'renderCustomAction'], 159, 3);
        add_filter(BASE_FILTER_TABLE_QUERY, [$this, 'filterEntityBased'], 150, 4);
        if (function_exists('shortcode')) {
            add_shortcode('website-stats-sc', "Website Stats", "", [CrudHelper::class, 'renderWebsiteStats']);
            add_shortcode('view-training-details-sc', "View Training Details", "", [CrudHelper::class, 'renderTrainingDetail']);
        }
    }

    /**
     * @param null $data
     * @return string
     * @throws \Throwable
     */
    public function addSettings($data = null)
    {
        return $data . view('settings.dls_setting')->render();
    }

    /**
     * @return array
     * @customized Sabari Shankar.Parthiban
     */
    public function applyEnableCondition($query, $model, array $selectedColumns = [])
    {
        $user = Auth::user();
        $this->applyDataLevelSecurity = ($user && $user->applyDataLevelSecurity());
        if (!empty($model) && !in_array(get_class($model), config('general.unsupported_enable_disable_models', []))) {
            $pluginName = Str::snake(class_basename($model), '-');
            $table = $model->getTable();
            $permission = (\Illuminate\Support\Facades\Route::has($pluginName . '.create')) ? $pluginName . '.enable_disable' : Str::plural($pluginName) . '.enable_disable';
            if (isFillableField($model, 'is_enabled') && Auth::user() && Auth::id() && !Auth::user()->hasPermission($permission)) {
                $query = $query->where($table . '.is_enabled', IS_ENABLED);
            }
            if (isFillableField($model, 'is_enabled') && !Auth::user()) {
                $query = $query->where($table . '.is_enabled', IS_ENABLED);
            }
        }
        return $query;
    }

    public function filterFinancialYear($query, $model, array $selectedColumns = [], $list = true)
    {
        $cls = get_class($model);
        if (!empty($model) && in_array($cls, config('general.financial_year_supported_modules', []))) {
            if($cls == "Impiger\Entrepreneur\Models\Trainee" && $list) {
                return $query;
            }

            $table = $model->getTable();
            if (request()->has('filter_columns') && (in_array($table . '.financial_year_id', request()->get('filter_columns')) || in_array('financial_year_id', request()->get('filter_columns')))) {
                return $query;
            }

            if(!joinTableExists($query, 'financial_year', 'FY')) {
                $query = $query->leftJoin('financial_year as FY', 'FY.id', '=', $table . '.financial_year_id');
            }
            $query = $query->where('FY.is_running', 1)->whereNull('FY.deleted_at');
        }
        return $query;
    }

    /**
     * @return array
     * @customized Sabari Shankar.Parthiban
     */
    public function applyDeletedAtCondition($query, $model, array $selectedColumns = [])
    {
        if (!empty($model)) {
            $table = $model->getTable();
            if (method_exists($model, 'getDeletedAtColumn')) {
                $query = $query->whereNull($table . '.deleted_at');
            }
        }
        return $query;
    }

    /**
     * @return array
     * @customized Ubaidur.Rahman
     */
    public function applyVisibleByUserOnlyCondition($query, $model, array $selectedColumns = [])
    {
        if (!empty($model)) {
            $table = $model->getTable();
            // \Log::info($table);
            // if (method_exists($model, 'getDeletedAtColumn')) {
            //     $query = $query->whereNull($table . '.deleted_at');
            // }
            if (Auth::id()) {
                $user = Auth::user();
                $userRoles = ($user) ? $user->roles : [];
                $roleSlugs = ($user) ? $userRoles->pluck('slug')->toArray() : [];
                if($table == 'training_title' && !$user->admins()) {
                    $query = $query->whereRaw('DATE('.$table . '.training_start_date) >= CURDATE()');
                }

                if($table == 'trainees' && !$user->admins()) {
                    $candidate = array(
                        'user_id' => Auth::id(), 
                    );
                    $entrepreneur = DB::table('entrepreneurs')->where($candidate)->first();
                    if($entrepreneur) {
                        $query = $query->where($table . '.entrepreneur_id', $entrepreneur->id);
                    }
                    
                }

                /*
                if($userRoles && $roleSlugs && $table == 'entrepreneurs' && !$user->admins()) {
                    
                    $referenceIds = array();
                    if($user->user_permission) {
                        $userPermissions = $user->user_permission;
                        foreach ($user->user_permission as $key => $permission) {
                            $referenceIds[] = $permission->reference_id;
                        }
                        \Log::info("referenceIds ". json_encode($referenceIds));
                    }
                    
                    if(in_array(HUB_ROLE_SLUG,$roleSlugs)) {
                        \Log::info("role slugs has ". HUB_ROLE_SLUG);
                        if(count($referenceIds) > 0) {
                            $query = $query->where($table . '.hub_institution_id', $referenceIds);
                        }
                    }
                    if(in_array(SPOKE_ROLE_SLUG,$roleSlugs)) {
                        \Log::info("role slugs has ".SPOKE_ROLE_SLUG);
                        if(count($referenceIds) > 0) {
                            $query = $query->where($table . '.spoke_registration_id', $referenceIds);
                        }
                        
                    }
                }
                */

                if($userRoles && $roleSlugs && $table == 'mentees' && !$user->admins()) {
                    if(in_array(MENTOR_ROLE_SLUG,$roleSlugs)) {
                        $candidate = array(
                            'user_id' => Auth::id(), 
                        );
                        $mentor = DB::table('mentors')->where($candidate)->first();
                        if($mentor) {
                            $query = $query->where($table . '.mentor_id', $mentor->id);
                        }
                    }
                }
            }
        }
        return $query;
    }

    public function renderCustomAction($action = null, $model, $data = [])
    {
        if (!$model) {
            return $action;
        }

        if (Auth::id()) {
            $user = Auth::user();
            $userRoles = ($user) ? $user->roles : [];
            $roleSlugs = ($user) ? $userRoles->pluck('slug')->toArray() : [];
            if (!empty($model) && in_array(get_class($model), config('general.dls_supported_models', []))) {
                $nav = config('general.navigation_supported_models');
                $qryStr = '';
                if($nav && isset($nav[get_class($model)])) {
                    $qryStr = '&navback='.$nav[get_class($model)];
                }
                if (is_plugin_active('user')  && $data->user_id!=$user->id && ($user->super_user || in_array(SUPERADMIN_ROLE_SLUG,$user->roles()->get()->pluck('slug')->toArray()))) {
                    $href = 'system/users/profile/'. $data->user_id . "?user_navigation=" . $data->id.($qryStr ? $qryStr : '');
                    // $action = "<a href='system/users/profile/" . $data->user_id . "?user_navigation=" . $data->id . "'  class='btn btn-icon btn-sm btn-primary' data-toggle='tooltip' data-original-title='DLS' ><i class='fa fa-sitemap'></i></a>";
                    $action = "<a href='".$href."'  class='btn btn-icon btn-sm btn-primary' data-toggle='tooltip' data-original-title='DLS' ><i class='fa fa-sitemap'></i></a>";
                }
            }
            if (!empty($model) && in_array(get_class($model), config('general.user_supported_models', []))) {
                $pluginName = get_plugin_name($model);
                if (is_plugin_active('user')  && $user->admins() && $data->user_id!=$user->id && ($user->hasPermission($pluginName.'.create') || $user->hasPermission($pluginName.'.edit'))) {
                    $action.= "<a href='/admin/system/users/profile/" . $data->user_id . "?action=reset-password'  class='btn btn-icon btn-sm btn-primary' data-toggle='tooltip' data-original-title='Reset Password' ><i class='fa fa-lock'></i></a>";
                }
            }
            
            if (!empty($model) && in_array(get_class($model), config('general.certificate_supported_models', []))) {
                $qString = "&training_id=".$data->training_title_id."&entrepreneur_id=".$data->entrepreneur_id;
                $d = array('training_title_id' => $data->training_title_id, 'entrepreneur_id' => $data->entrepreneur_id);
                if ($data->certificate_status == 1) {
                    $d['id'] = $data->id;
                    $d['action'] = 'download';
                    $action.= "<a href='/admin/trainees/download-certificate/".$data->id."' id='certificate".$data->entrepreneur_id."' class='btn btn-icon btn-sm btn-primary download-certificate' data-toggle='tooltip' data-section='".json_encode($d)."' data-original-title='Download Certificate' download='".$data->centificate_path."' ><i class='fa fa-download'></i></a>";
                    $d['action'] = 'regenerate';
                    $action.= "<a href='javascript:;' id='regenerate_certificate".$data->entrepreneur_id."' class='btn btn-icon btn-sm btn-primary regenerate-certificate' data-toggle='tooltip' data-section='".json_encode($d)."' data-original-title='Re-Generate Certificate' ><i class='fa fa-book'></i></a>";
                }
                if ($data->certificate_status == 0) {
                    $d['action'] = 'generate';
                    $action.= "<a href='javascript:;' id='certificate".$data->entrepreneur_id."' class='btn btn-icon btn-sm btn-primary certificate' data-toggle='tooltip' data-section='".json_encode($d)."' data-original-title='Generate Certificate' ><i class='fa fa-book'></i></a>";
                }
            }
            //Razorpay Payment Gateway
            if (!empty($model) && in_array(get_class($model), config('general.payment_gateway_supported_modue', []))) {
                $trainee = '';
                if(!$user->admins() && in_array('candidate', $roleSlugs)) {
                    // \Log::info("I am not admin user");
                    $candidate = array('user_id' => Auth::id());
                    $entrepreneur = DB::table('entrepreneurs')->where($candidate)->first();
                    if($entrepreneur) {
                        // \Log::info("I am a entrepreneur");
                        $trainee = DB::table('trainees')->where(['entrepreneur_id' => $entrepreneur->id, 'training_title_id' => $data->id])->first();
                        // \Log::info($trainee);                       
                    }

                    if (isset($data->fee_paid) && $data->fee_paid == APPLY_EVENT_FEE_FLAG['paid'] && !$trainee) {
                        $action.= "<a href='/razorpay-payment-view?amount=".$data->fee_amount."&id=".$data->id."'  class='btn btn-icon btn-sm btn-primary' data-toggle='tooltip' data-original-title='Payment Gateway' >Subscribe - " . $data->fee_amount . "</a>";
                    } else if (isset($data->fee_paid) && $data->fee_paid == APPLY_EVENT_FEE_FLAG['free'] && !$trainee) {
                        $action.= "<a href='/admin/training-titles/subscribe-to-event/".$data->id."'  class='btn btn-icon btn-sm btn-primary' data-toggle='tooltip' data-original-title='Apply Event' >Apply Now</a>";
                    } else if($trainee){
                        $action.= "<a href='javascript:;' class='btn btn-success'>Subscribed</a>";
                    }
                }
            }

            if (!empty($model) && get_class($model) == 'Impiger\MsmeCandidateDetails\Models\MsmeCandidateDetails') {
                if(strtotime($data->enroll_to_date) < strtotime(date('Y-m-d'))) {
                    $d = array('training_title_id' => $data->training_title_id, 'entrepreneur_id' => $data->entrepreneur_id);
                    if ($data->certificate_status == 1) {
                        $d['id'] = $data->trainee_id;
                        $d['action'] = 'download';
                        $action.= "<a href='/admin/trainees/download-certificate/".$data->trainee_id."' id='certificate".$data->entrepreneur_id."' class='btn btn-icon btn-sm btn-primary download-certificate' data-toggle='tooltip' data-section='".json_encode($d)."' data-original-title='Download Certificate' download='".$data->centificate_path."' ><i class='fa fa-download'></i></a>";
                        $action.= "<a href='javascript:;' id='regenerate_certificate".$data->entrepreneur_id."' class='btn btn-icon btn-sm btn-primary regenerate-certificate' data-toggle='tooltip' data-section='".json_encode($d)."' data-original-title='Re-Generate Certificate' ><i class='fa fa-book'></i></a>";
                    }
                    if ($data->certificate_status == 0) {
                        $d['action'] = 'generate';
                        $action.= "<a href='javascript:;' id='certificate".$data->entrepreneur_id."' class='btn btn-icon btn-sm btn-primary certificate' data-toggle='tooltip' data-section='".json_encode($d)."' data-original-title='Generate Certificate' ><i class='fa fa-book'></i></a>";
                    }
                }
            }
            
            

        }
        return $action;
    }
    
    /* Customized By Sabari Shankar Parthiban Start 
     * Add filter based on the entity                 
     */

    public function filterEntityBased($query, $model, array $selectedColumns = [], $isTableFilter = true ) {
        $user = \Auth::user();
        if ($user && $user->applyDataLevelSecurity() && $model ) {
            $table = $model->getTable();
            $entities = getAppEntitiesFromSession(true);
            $userentity = getUserEntitiesFromSession();
            $entityRelationkeys = config('general.entity_relation_key', []);
            foreach ($userentity as $key => $value) {
                    $entityTable = Arr::get($entities, $key);
                    $relationKey = Arr::get($entityRelationkeys, $entityTable);
                    if (isFillableField($model, $relationKey)) {
                        $query = $query->whereIn($table . '.' . $relationKey, $value);
                    }
                }
        }
        return $query;
    }
}

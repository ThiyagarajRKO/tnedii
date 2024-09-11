<?php

namespace Impiger\Crud\Providers;

use Impiger\Dashboard\Supports\DashboardWidgetInstance;
use Impiger\Shortcode\Compilers\Shortcode;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use Throwable;
/*  @customized Sabari Shankar.Parthiban */
use Illuminate\Support\Facades\Auth;
use DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Utils\CrudHelper;
use App\Models\Crud;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Arr;

class HookServiceProvider extends ServiceProvider
{
    protected $applyDataLevelSecurity = false;

    public function boot()
    {
//        add_filter(BASE_FILTER_TABLE_QUERY, [$this, 'applyEnableCondition'], 150, 3);
//        add_filter(BASE_FILTER_TABLE_QUERY, [$this, 'applyDeletedAtCondition'], 157, 3);
//        add_filter(ADD_CUSTOM_ACTION, [$this, 'renderCustomAction'], 159, 3);
        add_filter(RECENT_TRAINING, [$this, 'renderRecentTraining'], 160, 2);
    }
    /**
     * @return array
     * @customized Sabari Shankar.Parthiban
     */
    public function applyEnableCondition($query, $model, array $selectedColumns = [])
    {
        $user = Auth::user();
        $this->applyDataLevelSecurity = ($user && $user->applyDataLevelSecurity());
        if (!empty($model) && !in_array(get_class($model), config('plugins.crud.general.unsupported_enable_disable_models', []))) {
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
    
    public function renderCustomAction($action = null, $model, $data = [])
    {
        if (!$model) {
            return $action;
        }
        
        if (Auth::id()) {
            $user = Auth::user();
            $userRoles = ($user) ? $user->roles : [];
            $roleSlugs = ($user) ? $userRoles->pluck('slug')->toArray() : [];          
            if (!empty($model) && in_array(get_class($model), config('plugins.crud.general.dls_supported_models', []))) {
                if (is_plugin_active('user')  && $data->user_id!=$user->id && ($user->hasPermission('user.create') || $user->hasPermission('user.edit'))) {
                    $action = "<a href='system/users/profile/" . $data->user_id . "?user_navigation=" . $data->id . "'  class='btn btn-icon btn-sm btn-primary' data-toggle='tooltip' data-original-title='DLS' ><i class='fa fa-sitemap'></i></a>";
                }
            }
            if (!empty($model) && in_array(get_class($model), config('plugins.crud.general.user_supported_models', []))) {
                $pluginName = get_plugin_name($model);
                if (is_plugin_active('user')  && $user->admins() && $data->user_id!=$user->id && ($user->hasPermission($pluginName.'.create') || $user->hasPermission($pluginName.'.edit'))) {
                    $action.= "<a href='/admin/system/users/profile/" . $data->user_id . "?action=reset-password'  class='btn btn-icon btn-sm btn-primary' data-toggle='tooltip' data-original-title='Reset Password' ><i class='fa fa-lock'></i></a>";
                }
            }
            
        }
        return $action;
    }

    public function renderRecentTraining($shortcode) {
        return render_recent_training_title($shortcode->limit ? $shortcode->limit : 6);
    }

    /**
     * Customized by Vijayaragavan.Ambalam *
     * @param Shortcode $shortcode
     * @return string
     */
    public function render($shortcode)
    {
        return render_training_title($shortcode->limit ? $shortcode->limit : 6);
    }
}



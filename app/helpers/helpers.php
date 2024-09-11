<?php

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Impiger\TrainingTitle\Repositories\Interfaces\TrainingTitleInterface;

function getIdfromValue($tableName, $cond)
{
    if (!$tableName && $cond) {
        return null;
    }
    $res = \DB::table($tableName)->where($cond)->whereNull('deleted_at')->first();
    if (!empty($res)) {
        return $res->id;
    }
}

function getValueFromId($tableName, $cond,$field = 'name')
{
    if (!$tableName && $cond) {
        return null;
    }
    $res = \DB::table($tableName)->where($cond)->whereNull('deleted_at')->first();
    if (!empty($res)) {
        return $res->$field;
    }
}


function getEmailIdsfromRoles($roleSlug, $data = null) {
    if (!$roleSlug && !is_int($roleSlug)) {
        \Log::warning(" Id is not a integer t fetch the user emails ");
        return null;
    }
    $query = \DB::table('roles')->where('slug', $roleSlug)
            ->leftjoin('role_users', 'roles.id', '=', 'role_users.role_id')
            ->leftjoin('users', 'users.id', '=', 'role_users.user_id');

    $model = "Impiger\ACL\Models\UserPermission";


    if ($model) {
        $model = new $model();
        $query = apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, ['users.*'], false);
    }

    $emails = $query->select('users.email as email',DB::raw('concat(coalesce(users.first_name,"")," ",coalesce(users.last_name,"")) as username'))->pluck('email','username')->toArray();
    if (!empty($emails)) {
        return $emails;
    }
    \Log::warning(" User emails are not found ");
}

function getEmailIdsfromUsers($userId)
{
    if (!$userId && !is_int($userId)) {
        \Log::warning(" Id is not a integer t fetch the user emails ");
        return null;
    }
    $emails = \DB::table('users')->select('users.email as email',DB::raw('concat(coalesce(first_name,"")," ",coalesce(last_name,"")) as username'))->where('id', $userId)->first()
        ;
    if (!empty($emails)) {
        return $emails;
    }
    \Log::warning(" User emails are not found ");
}

function getAttributeOptionId($slug)
{
    if (!$slug) {
        return null;
    }
    $attributeOptionId = NULL;
    $query = DB::table('attribute_options');
            if(is_array($slug)){
                $attributeOption = $query->whereIn('slug', $slug)->get();
            }else{
                $attributeOption = $query->where('slug', $slug)->first();
            }
    if (!empty($attributeOption)) {
        if(!isset($attributeOption->id) && $attributeOption->count() >0){
            foreach($attributeOption as $row){
                $attributeOptionId[] = $row->id;
            }
        }else{
            $attributeOptionId = $attributeOption->id;
        }

    }
    return $attributeOptionId;
}



/* Customized by Sabari Shankar .Parthiban start*/
if (!function_exists('get_common_condition')) {

    /**
     * @return string
     */
    function get_common_condition($tableName)
    {
        $rawConditions = "";
        if (!$tableName) {
            return $rawConditions;
        }
        if ($tableName != 'financial_year' && \Illuminate\Support\Facades\Schema::hasColumn($tableName, 'is_Enabled')) {
            $rawConditions = " " . $tableName . ".is_enabled =" . IS_ENABLED;
        }
        if (\Illuminate\Support\Facades\Schema::hasColumn($tableName, 'deleted_at')) {
            $rawConditions .= (!empty($rawConditions)) ? " And" : "";
            $rawConditions .= " " . $tableName . ".deleted_at IS NULL";
        }

        return $rawConditions;
    }
}

if (!function_exists('get_plugin_name')) {

    /**
     * @return string
     */
    function get_plugin_name($model)
    {
        $pluginName = Str::snake(class_basename($model), '-');
        return (\Illuminate\Support\Facades\Route::has($pluginName . '.create')) ? $pluginName : Str::plural($pluginName);
    }
}
/* Customized by Sabari Shankar .Parthiban end*/

if (!function_exists('customcaptcha_validation')) {
    function customcaptcha_validation()
    {
        $captcha_validation = [];
        if (HIDE_CUSTOM_CAPTCHA) {
            return $captcha_validation;
        }
        if (setting('enable_custom_captcha')) {
            $captcha_validation = [
                'customcaptcha' => 'required|customcaptcha',
            ];
        } elseif (setting('enable_captcha') && is_plugin_active('captcha')) {
            $captcha_validation = [
                'g-recaptcha-response' => 'required|captcha',

            ];
        } else {
            $captcha_validation = [];
        }
        return $captcha_validation;
    }
}
if (!function_exists('customcaptcha')) {
    /**
     * @param string $config
     * @return array|ImageManager|mixed
     * @throws Exception
     */
    function customcaptcha(string $config = 'default')
    {
        return app('customcaptcha')->create($config);
    }
}

if (!function_exists('customcaptcha_src')) {
    /**
     * @param string $config
     * @return string
     */
    function customcaptcha_src(string $config = 'default'): string
    {
        return app('customcaptcha')->src($config);
    }
}

if (!function_exists('customcaptcha_img')) {

    /**
     * @param string $config
     * @return string
     */
    function customcaptcha_img(string $config = 'default'): string
    {
        return app('customcaptcha')->img($config);
    }
}

if (!function_exists('customcaptcha_check')) {
    /**
     * @param string $value
     * @return bool
     */
    function customcaptcha_check(string $value): bool
    {
        return app('customcaptcha')->check($value);
    }
}

if (!function_exists('customcaptcha_api_check')) {
    /**
     * @param string $value
     * @param string $key
     * @param string $config
     * @return bool
     */
    function customcaptcha_api_check(string $value, string $key, string $config = 'default'): bool
   {
        return app('customcaptcha')->check_api($value, $key, $config);
    }
}

function generateRandomPassword($length, $alphabet, $totLen)
{
    $pass = array(); //remember to declare $pass as an array
    for ($i = 0; $i < $length; $i++) {
        $n = rand(0, $totLen-1);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); //turn the array into a string
}

function checkAtleastOneValueExist($inputArray, $excludeColumns = [])
{
    $output = false;
    if ($inputArray) {
        foreach ($inputArray as $key => $input) {
            if ($input && !in_array($key, $excludeColumns)) {
                return true;
            }
        }
    }

    return $output;
}

function joinTableExists($query, $table, $alias = NULL) {
    $tableExists = false;
    $queryBuilder = (get_class($query) != "Illuminate\Database\Query\Builder") ? $query->getQuery() :$query ;
    if (isset($queryBuilder->joins)) {
        foreach ($queryBuilder->joins as $join) {
            if (!$alias && ($join->table == $table  || Str::startsWith($join->table, $table." "))) {
                return $tableExists = true;
            } elseif($alias && ($join->table == $table. " ".$alias || $join->table == $table. " AS ".$alias)){
                return $tableExists = true;
            }
        }
    }

    return $tableExists;
}

function cndnExists($query, $field, $value, $type = 'Basic') {
    $tableExists = false;
    $queryBuilder = (get_class($query) != "Illuminate\Database\Query\Builder") ? $query->getQuery() :$query ;
    if (isset($queryBuilder->wheres)) {
        foreach ($queryBuilder->wheres as $cndn) {
            if ($cndn['type'] == $type && $cndn['column'] == $field &&
            $cndn['value'] == $value) {
                return $tableExists = true;
            }
        }
    }

    return $tableExists;
}

if (!function_exists('getInBetweenDates')) {
    /*
     * getInBetweenDates
     * param $startDate,$endDate
     * return array of dates
     */
    function getInBetweenDates($startDate, $endDate, $months = null, $years = null)
    {
        if (!$startDate && !$endDate) {
            return [];
        }
        $dates = [];
        $period = \Carbon\CarbonPeriod::create($startDate, $endDate);
        if ($months) {
            $interval = $months . ' month';
            $period = \Carbon\CarbonPeriod::create($startDate, $interval, $endDate);
        }
        if ($years) {
            $interval = $years . ' year';
            $period = \Carbon\CarbonPeriod::create($startDate, $interval, $endDate);
        }

        // Iterate over the period
        foreach ($period as $date) {
            if ($months) {
                $dates[] = $date->format('m');
            } elseif ($years) {
                $dates[] = $date->format('Y');
            } else {
                $dates[] = $date->format('Y-m-d');
            }
        }
        return $dates;
    }
}

if (!function_exists('getEntityId')) {
    /*
     * getentityId
     * param $moduleName
     * return id
     */
    function getEntityId($moduleName,$field = 'module_name') {
        if (!$moduleName) {
            return null;
        }
        $entityId = "";
        $entity = DB::table('cruds')->select('id', 'module_name')->where(['is_entity' => 1, $field => $moduleName])->first();
        if($entity){
            $entityId = $entity->id;
        }
        return $entityId;
    }

}
if (!function_exists('getModuleDetails')) {
    /*
     * getentityId
     * param $moduleName
     * return id
     */
    function getModuleDetails($moduleTable,$field = 'module_name') {
        if (!$moduleTable) {
            return null;
        }
        $moduleField = "";
        $module = DB::table('cruds')->select('id', $field)->where(['module_db' => $moduleTable])->first();
        if($module){
            $moduleField = $module->$field;
        }
        return $moduleField;
    }

}
/**
 * @param int $number
 * @return string
 */
if (!function_exists('numberToRomanRepresentation')) {
    function numberToRomanRepresentation($number)
    {
        $map = array('M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400, 'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40, 'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1);
        $returnValue = '';
        while ($number > 0) {
            foreach ($map as $roman => $int) {
                if ($number >= $int) {
                    $number -= $int;
                    $returnValue .= $roman;
                    break;
                }
            }
        }
        return $returnValue;
    }
}

/**
 * @param string
 * @return string
 */
if (!function_exists('base64DecodeQueryParams')) {
    function base64DecodeQueryParams($str)
    {
        $qryStr = explode("&", base64_decode($str));
        $params = [];

        foreach ($qryStr as $param) {
            list($k, $v) = explode("=", $param);
            $params[$k] = $v;
        }
        return $params;
    }
}

/**
 * @param string
 * @return string
 */
if (!function_exists('isValidArray')) {
    function isValidArray($input)
    {
        if($input && is_array($input) && count($input) > 0) {
            return $input;
        }

        return [];
    }

}


/**
 * @param string
 * @return integer
 */
if (!function_exists('getImpId')) {
    function getImpId($table,$userId = null)
    {
        $impId = Null;
        if(!$table){
            return $impId;
        }
        if(!\Illuminate\Support\Facades\Schema::hasTable($table)){
            return $impId;
        }
        $userId = ($userId) ? : Auth::id();
        $impiger = DB::table($table)->where('user_id',$userId)->first();
        if($impiger){
            $impId = $impiger->id;
        }
        return $impId;
    }

}
/**
 * @param string
 * @return integer
 */
if (!function_exists('getUserId')) {
    function getUserId($table,$id)
    {
        $userId = Null;
        if(!$table && !$id){
            return $userId;
        }
        if(!\Illuminate\Support\Facades\Schema::hasTable($table)){
            return $userId;
        }

        $impiger = DB::table($table)->where('id',$id)->first();
        if($impiger){
            $userId = $impiger->user_id;
        }
        return $userId;
    }

}

/**
 * @param string
 * @return integer
 */
if (!function_exists('getRoleIdFromSlug')) {
    function getRoleIdFromSlug($slug)
    {
        $roleId = Null;
        if(!$slug){
            return $roleId;
        }
        $roles = Impiger\ACL\Models\Role::where('slug',$slug)->first();
        if($roles){
            $roleId = $roles->id;
        }
        return $roleId;
    }

}

if (!function_exists('filterByArrayValue')) {
    function filterByArrayValue($inputArray, $filterKey, $filterValue, $returnAll = false) {
        $output = false;
        $filtered = Arr::where($inputArray, function ($value, $key) use($filterKey, $filterValue) {
            return ($value[$filterKey] == $filterValue);
        });

        if(count($filtered) > 0) {
            $value = array_values($filtered);
            $output = ($returnAll) ? $value : $value[0];
        }

        return $output;
    }
}

if (!function_exists('reArrangeDashboardWidgets')) {
    function reArrangeDashboardWidgets($widgets, $widgetData) {
        $availableOrder = [];
        $i = 0;
        foreach($widgets as $widget) {
            if(\Arr::has($widgetData,$widget->name)) {
                $widgetSetting = $widget ? $widget->settings->first() : null;
                if (!empty($widgetSetting) && !array_key_exists($widgetSetting->order, $widgetData)) {
                    $widgetData[$widgetSetting->order] = $widgetData[$widget->name];
                    $availableOrder[] = $widgetSetting->order;
                } else {
                    $widgetData['-'.$i] = $widgetData[$widget->name];
                    $i++;
                }
                unset($widgetData[$widget->name]);
            }
        }
        return $widgetData;
    }
}

if (!function_exists('getRequestFilters')) {
    function getRequestFilters($removeAlias = false, $withOperator = false) {
        $requestFilters = [];
        $request = request();
        if ($request->input('filter_columns')) {
            foreach ($request->input('filter_columns', []) as $key => $item) {
                $columnKey = ($removeAlias) ? Str::afterLast($item, ".") : $item;
                if($withOperator) {
                    $requestFilters[] = [
                        'column'   => $columnKey,
                        'operator' => $request->input('filter_operators.' . $key),
                        'value'    => $request->input('filter_values.' . $key),
                    ];
                } else {
                    $requestFilters[$columnKey] = $request->input('filter_values.' . $key);
                }
            }
        }
        return $requestFilters;
    }
}


if (!function_exists('getAppEntitiesFromSession')) {
    function getAppEntitiesFromSession($hasModuleDB = false)
    {
        if($hasModuleDB) {
            if(!session()->has('app_entities_with_table'))  {
                $entities =  \App\Models\Crud::where('is_entity', 1)->select(['id', 'module_db'])->pluck('module_db', 'id')->toArray();
                session()->put('app_entities_with_table', $entities);
            }

            return session()->get('app_entities_with_table');
        }
        if(!session()->has('app_entities'))  {
            $entities =  \App\Models\Crud::where('is_entity', 1)->select(['id', 'module_name'])->pluck('id', 'module_name')->toArray();
            session()->put('app_entities', $entities);
        }

        return session()->get('app_entities');
    }
}

if (!function_exists('getUserEntitiesFromSession')) {
    function getUserEntitiesFromSession()
    {
        if(!session()->has('user_entity'))  {
            $userEntity =  \Auth::user()->userEntity();
            session()->put('user_entity', $userEntity);
            return $userEntity;
        }

        return session()->get('user_entity');
    }
}



if (!function_exists('getCurrencyCode')) {
    function getCurrencyCode() {
	    if(!session()->has('currency')) {
	    $currency = DB::table('countries')->where('id', setting('default_country'))->get()->pluck('currency_code')->first();
	    session()->put('currency', $currency);
	    return $currency;
	    }
	    return session()->get('currency');
	}
}
    function getUserDomainUrl($userId) {
        $domainIds=[];
        if(is_plugin_active('multidomains')){
            $domainIds=app(\Impiger\Multidomain\Multidomain::class)->getDomainIdsByUserLogin($userId);
        }
         if(!empty($domainIds) && count($domainIds)==1)
         {
            $domain = DB::table("multidomains")->where('id',$domainIds)->first();
            if (env('APP_ENV')!== 'local') {
                    return 'https://'.$domain->name;
            }
             return 'http://'.$domain->name;
         }
         else{
            return env('APP_URL');
         }
    }

    function inArrayAny($needles, $haystack) {
        return !empty(array_intersect($needles, $haystack));
     }

     if (!function_exists('getAllowedDashboardWidgets')) {
     function getAllowedDashboardWidgets()
     {
         if(!session()->has('allowed_dash_widgets'))  {
            $allowedWidgets = \Impiger\Dashboard\Models\DashboardWidgetSetting::select([DB::raw('REPLACE(REPLACE(W.name, "pie-", ""), "bar-","") AS name')])
            ->leftJoin('dashboard_widgets AS W', 'W.id', '=', 'widget_id')
            ->where(['user_id' => Auth::id(), 'dashboard_widget_settings.status' => 1])->pluck('name')->toArray();
             session()->put('allowed_dash_widgets', $allowedWidgets);
             return $allowedWidgets;
         }

         return session()->get('allowed_dash_widgets');
     }
 }

 function getLinks($url, $text) {
   return ($url) ? \Html::link($url, $text,['target' => '_blank']): $text;
 }

 function isFillableField($model, $field) {
    if(!$model || !$field) {
        return false;
    }

    $fillable = $model->getFillable();
    return isValidArray($fillable) ?  in_array($field, $fillable) : false;
 }

 function isVendorRequest($userId) {
    if(!$userId) {
        return true;
    }
    $coreUser = \Impiger\ACL\Models\User::where('id',$userId)->first();
    $isRequest = ($coreUser && $coreUser->last_login) ? false : true;
    return $isRequest;
 }

 function getVendorIdbyLogin(){
     $vendor = Impiger\VendorRequest\Models\VendorRequest::where('user_id',\Auth::id())->first();
     if($vendor){
         return $vendor->id;
     }
     return null;
 }

 function isVendorUser(){
     $user = \Auth::user();
     if($user && in_array(getRoleIdFromSlug(VENDOR_ROLE_SLUG),$user->role_ids)){
         return true;
     }
     return false;
 }

 function getVendorUser($vendorRequest){
    $user =  \Impiger\ACL\Models\User::find($vendorRequest->user_id);

        if (!$user) {
            \Log::info("User id updated in vendors table. But his details not exist.");
            return $response
                            ->setError()
                            ->setMessage("User Profile not exist!");
        }
        $user->domain_href = getUserDomainUrl($user->id);
        $user->temp_password = $vendorRequest->temp_password;
        //$user->email = (isset($vendorRequest->contact_email) && $vendorRequest->contact_email) ? $vendorRequest->contact_email : $vendorRequest->email_id;
        $user->contact_email = $vendorRequest->contact_email;
        return $user;
 }

 function userEmailTemplateVariables(){
     $variables = [
         'first_name' => 'First name',
         'last_name' => 'Last name',
         'username' => 'Username',
         'email' => 'Email',
         'temp_password' => 'Password',
         'domain_href' => 'Site Url',
     ];
     return $variables;
 }
 
 function getMSMETrainingId($scheme){
    $trainingId = ""; 
    if(!$scheme){
        return $trainingId;
    }
    $trainingTitle = app(\Impiger\TrainingTitle\Models\TrainingTitle::class)->where('name','LIKE','%'.$scheme.'%')
                        ->whereDate('training_end_date','>=',date('Y-m-d'))->orderBy('id','DESC')->first();
    if($trainingTitle){
        $trainingId=$trainingTitle->id;
    }
    return $trainingId;
 }
 
 
 if (!function_exists('get_post_by_category_name')) {
    /**
     * @param string $categoryName
     * @param int $paginate
     * @param int $limit
     * @param string $order_by
     * @return \Illuminate\Support\Collection
     */

    // app(Impiger\Blog\Repositories\Interfaces\PostInterface);
    function get_post_by_category_name($category_name = "Default", $paginate = 0, $limit = 0, $order_by = 'ASC')
    {
        $data = app(\Impiger\Blog\Models\Post::class)
            ->where('posts.status', \Impiger\Base\Enums\BaseStatusEnum::PUBLISHED)
            ->join('post_categories', 'post_categories.post_id', '=', 'posts.id')
            ->join('categories', 'post_categories.category_id', '=', 'categories.id')
            ->where('categories.name', $category_name)
            ->select('posts.*')
            ->orderBy('posts.created_at', $order_by);

        if ($paginate != 0) {
            $posts = app(\Impiger\Blog\Repositories\Interfaces\PostInterface::class)->applyBeforeExecuteQuery($data)->paginate($paginate);
        } else if ($limit != 0) {
            $posts = app(\Impiger\Blog\Repositories\Interfaces\PostInterface::class)->applyBeforeExecuteQuery($data)->limit($limit)->get();
        } else {
            $posts = app(\Impiger\Blog\Repositories\Interfaces\PostInterface::class)->applyBeforeExecuteQuery($data)->get();
        }

        // dd($posts);
        return $posts;
    }
 }

 if (!function_exists('get_post_by_category_our_services')) {
    /**
     * @param int $categoryId
     * @param int $paginate
     * @param int $limit
     * @return \Illuminate\Support\Collection
     */

    // app(Impiger\Blog\Repositories\Interfaces\PostInterface);
    function get_post_by_category_our_services($category = null)
    {
        $category = strtolower($category);
        $order_by = 'desc';
        if($category == 'our-services' || $category == 'our services') {
            $order_by = 'asc';
        }
        $paginate = 12; $limit = 0;
        $categoryData = get_category_by_name('Our Services');
        $data = app(\Impiger\Blog\Models\Post::class)
            ->where('posts.status', \Impiger\Base\Enums\BaseStatusEnum::PUBLISHED)
            ->join('post_categories', 'post_categories.post_id', '=', 'posts.id')
            ->join('categories', 'post_categories.category_id', '=', 'categories.id')
            ->whereIn('post_categories.category_id', [$categoryData->id])
            ->select('posts.*')
            ->distinct()
            ->with('slugable')
            ->orderBy('posts.created_at', $order_by);

        if ($paginate != 0) {
            $posts = app(\Impiger\Blog\Repositories\Interfaces\PostInterface::class)->applyBeforeExecuteQuery($data)->paginate($paginate);
        } else {
            $posts = app(\Impiger\Blog\Repositories\Interfaces\PostInterface::class)->applyBeforeExecuteQuery($data)->limit($limit)->get();
        }

        $our_services_ui_slug = OUR_SERVICES_UI_SLUG;

        if($posts) {
            foreach ($posts as $post) {
                if(array_key_exists($post->slugable->key, $our_services_ui_slug)) {
                    // $post->css = (Object) $our_services_ui_slug[$post->slugable->key];
                    $post->css = $our_services_ui_slug[$post->slugable->key];
                }
            }
            
        }

        // dd($posts);
        return $posts;
    }
 }
 if (!function_exists('get_category_by_name')) {
    function get_category_by_name($name) {
        $data = app(\Impiger\Blog\Models\Category::class)
        ->with('slugable')->where([
            'categories.name'     => $name,
            'categories.status' => \Impiger\Base\Enums\BaseStatusEnum::PUBLISHED,
        ]);

        return app(\Impiger\Blog\Repositories\Interfaces\CategoryInterface::class)->applyBeforeExecuteQuery($data, true)->first();
    }
 }

 if (!function_exists('get_training_view')) {
    function get_training_view($id) {
        return App\Utils\CrudHelper::trainingview($id);
    }
 }
 
 if (!function_exists('getExcludeCategoryId')) {
    function getExcludeCategoryId() {
        $categoryIds = [];
        $slugs = DB::table('slugs')->whereIn('key',EXCLUDE_CATEGORY_SLUGS)->where('reference_type','Impiger\Blog\Models\Category')->get();
        
        if($slugs){
            $categoryIds = $slugs->pluck('reference_id');
        }
        return $categoryIds;
    }
 }
 if (!function_exists('getCategoryId')) {
    function getCategoryId($slug) {
        $categoryId = [];
        $slugs = DB::table('slugs')->where('key',$slug)->where('reference_type','Impiger\Blog\Models\Category')->first();
        
        if($slugs){
            $categoryId = $slugs->reference_id;
        }
        return $categoryId;
    }
 }
 
 if (!function_exists('getMobileNumbers')) {
    function getMobileNumbers() {
        $mobileLink = "";
        $mobileNumbers = explode("/",theme_option('website'));
        if(is_array($mobileNumbers)){
            $count = count($mobileNumbers);
            foreach($mobileNumbers as $key=> $number){
                $concatnumber = (strlen($number) == 2) ? substr($mobileNumbers[$key-1], 0, -2) . $number : $number;
                 $mobileLink.= '<a href="tel:'.$concatnumber.'">'.$number.'</a>';
                 if($key <($count-1)){
                     $mobileLink.="/ ";
                 }
            }
        }else{
            $mobileLink = '<a href="tel:'.$mobileNumbers.'">'.$mobileNumbers.'</a>';
        }
        return $mobileLink;
    }
 }
 if (!function_exists('getEmailIds')) {
    function getEmailIds() {
        $emailLink = "";
        $emailIds = explode("|",theme_option('contact_email'));
        if(is_array($emailIds)){
            $count = count($emailIds);
            foreach($emailIds as $key=> $email){                
                 $emailLink.= '<a href="mailto:'.$email.'">'.$email.'</a>';
                 if($key <($count-1)){
                     $emailLink.="| ";
                 }
            }
        }else{
            $emailLink = '<a href="mailto:'.$emailIds.'">'.$emailIds.'</a>';
        }
        return $emailLink;
    }
 }

  /* Customized by Ubaidur.Rahman Start */
if (!function_exists('get_trainings')) {
    /**
     * @param int $limit
     * @param array $with
     * @return Collection
     */
    function get_trainings(int $limit = 8)
    {
        return app(TrainingTitleInterface::class)->getTrainingTitleListGalleryView($limit);
    }
}

if (!function_exists('render_trainings')) {
    /**
     * @param int $limit
     * @return string
     */
    function render_trainings(int $limit)
    {
        // Gallery::registerAssets();
        $trainings = get_trainings($limit);
        return view('training-title.training', compact('trainings','limit'));
    }
}
/* Customized by Ubaidur.Rahman End */
 
 



if (!function_exists('createHashMapArray')) {
    function createHashMapArray($inputArr, $hashKey, $single = false)
    {
        $output = [];
        if (!isValidArray($inputArr)) {
            return $output;
        }

        if ($single) {
            foreach ($inputArr as $k => $val) {
                $output[Arr::get($val, $hashKey)] = $val;
            }

            return $output;
        }
        foreach ($inputArr as $k => $val) {
            $output[Arr::get($val, $hashKey)][] = $val;
        }

        return $output;
    }
}
if (!function_exists('getDistrictCode')) {
    function getDistrictCode($districtId)
    {
        $code ="";
        if (!$districtId) {
            return $code;
        }
        $district = Impiger\MasterDetail\Models\District::where('id',$districtId)->first();
        if($district){
            $code = $district->code;
        }
        return $code;
    }
}
if (!function_exists('getAcronym')) {
    function getAcronym($value)
    {
        $words = explode(" ", $value);
        $acronym = "";

        foreach ($words as $w) {
          if(!in_array(strtolower($w),EXCLUDE_ABBREVATION_WORD)){
              $acronym .= mb_substr($w, 0, 1);
          }  
          
        }
        return $acronym;
    }
}
if (!function_exists('getCandidateId')) {
    function getCandidateId($userId)
    {
       $candidateId = null;
       $candidate = Impiger\Entrepreneur\Models\Entrepreneur::where('user_id',$userId)->first();
       if($candidate){
           $candidateId = $candidate->id;
       }
        return $candidateId;
    }
}

if (!function_exists('getMsmeCandidate')) {
    function getMsmeCandidate($userId)
    {
        $isMsmeCandidate = false;
        $candidate = Impiger\Entrepreneur\Models\Entrepreneur::where('user_id',$userId)->first();
        if($candidate){
            $isMsmeCandidate = ($candidate->scheme) ? true : false;
            $candidate['msmeScheme'] = $candidate->scheme;
        }

        $candidate['isMsmeCandidate'] = $isMsmeCandidate;
        return $candidate;
    }
}
if (!function_exists('getRequestClass')) {
    /**
     * @param string $value
     * @param string $key
     * @param string $config
     * @return bool
     */
    function getRequestClass(string $model)
    {
        $str="";
        if(!$model){
            return $str;
        }
        $modelObj = new $model;
        $table = $modelObj->getTable();
        $parentModule = null;
        $cruds = Impiger\Crud\Models\Crud::where('module_db',$table)->first();
        $module = ($cruds) ? $cruds->module_name : "";
        if($cruds && $cruds->parent_id){
            $parentModule = Impiger\Crud\Models\Crud::where('id',$cruds->parent_id)->first();
            $module= ($parentModule) ? $parentModule->module_name : "";
        }
        if($module){
            $str = '\Impiger\{Module}\Http\Requests\{Name}Request';
            $search = array('{Module}', '{Name}');
            $replace = array(ucfirst(Str::camel($module)), ucfirst(Str::camel($cruds->module_name)));
            $str = str_replace($search, $replace, $str);
        }else{
            $str = str_replace('Models', 'Http\Requests', $model);
            $str.='Request';
        }       
        return $str;
    }
}



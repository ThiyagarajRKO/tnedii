<?php
namespace App\Utils;

use Assets;
use Impiger\Base\Events\BeforeEditContentEvent;
use Illuminate\Http\Request;
use Exception;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\DeletedContentEvent;
use Impiger\Base\Events\UpdatedContentEvent;
use Impiger\Base\Http\Responses\BaseHttpResponse;
use Illuminate\Validation\ValidationException;
use Arr;
use DB;
use Validator;
use Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Theme;
use Html;
use App\Models\DraftedData;
use App\Models\Crud;
use Hash;
use Illuminate\Support\Facades\Response as FacadeResponse;
use Impiger\TrainingTitle\Repositories\Interfaces\TrainingTitleInterface;

class CrudHelper{

    public static function getModuleNameUsingModuleDBField($tableName)
    {
        return DB::table('cruds')->where('module_db', $tableName)->pluck('module_name')->first();
    }

    public static function formatLookupValue($val, $arr)
    {
        $arr = explode(':', $arr);

        if (isset($arr['0']) && $arr['0'] == 1) {
            $Q = DB::select(" SELECT " . str_replace("|", ",", $arr['3']) . " FROM " . $arr['1'] . " WHERE " . $arr['2'] . " = '" . $val . "' ");
            if (count($Q) >= 1) {
                $row = $Q[0];
                $fields = explode("|", $arr['3']);
                $v = '';
                $v .= (isset($fields[0]) && $fields[0] != '' ?  $row->{$fields[0]} . ' ' : '');
                $v .= (isset($fields[1]) && $fields[1] != ''  ? $row->{$fields[1]} . ' ' : '');
                $v .= (isset($fields[2]) && $fields[2] != ''  ? $row->{$fields[2]} . ' ' : '');
                return $v;
            } else {
                return '';
            }
        } else {
            return $val;
        }
    }

    public static function getTrainerUserList()
    {
        $model = new \Impiger\User\Models\User();
        $fields = ['impiger_users.id', DB::raw('CONCAT_WS(" ",impiger_users.first_name, last_name) AS displayCusText')];
        $query = $model->select($fields)->join('role_users AS RU',function($join) {
            $join->on('impiger_users.user_id','=','RU.user_id');
        })->join('roles',function($join) {
            $join->on('roles.id','=','RU.role_id')
            ->whereIn('roles.slug', TEACHING_STAFF_ROLE_SLUG);
        });
        $query = apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, $fields, false);
        return $query->pluck("displayCusText", 'id')->toArray();

    }

    public static function getSelectBoxChoices($val, $optionType = null, $lookupTable = null, $lookupValue = null, $lookupKey = null, $whereCondn = null, $applyFinancialYearCndn = false, $groupBy = [])
    {
        $optionType = ($optionType) ? $optionType : Arr::get($val, 'option.opt_type');
        $choices = [];
        if ($optionType == 'customFunction') {
            return call_user_func(array('SELF', Arr::get($val, 'option.custom_function')));
        }
        else if ($optionType == 'datalist') {
            $lookupValues = Arr::get($val, 'option.lookup_query');
            $optionList = explode("|", $lookupValues);

            foreach ($optionList as $opt) {
                list($key, $value) = explode(":", $opt);
                $choices[$key] = $value;
            }
        } else if (in_array($optionType, ['external','entity']) || $val['field'] == 'entity_type') {
            $lookupTable = ($lookupTable) ? $lookupTable : Arr::get($val, 'option.lookup_table');
            $lookupValue = ($lookupValue) ? $lookupValue : Arr::get($val, 'option.lookup_value');
            $lookupKey = ($lookupKey) ? $lookupKey :  Arr::get($val, 'option.lookup_key');
            $whereCondn = ($whereCondn) ? $whereCondn :Arr::get($val, 'option.where_cndn');
            if(Arr::get($val, 'field') == 'entity_type') {
                $lookupTable = 'cruds';
                $lookupValue = 'module_title';
                $lookupKey = 'id';
                $whereCondn = "is_entity = 1";
            } elseif($optionType == 'entity' && Arr::get($val, 'option.specific_entity_type')) {
                $lookupTable = DB::table('cruds')->where('is_entity', 1)-> where('module_name',Arr::get($val, 'option.specific_entity_type'))->pluck('module_db')->first();
                $lookupValue = 'name';
                $lookupKey = 'id';
            } elseif($optionType == 'external' && $lookupTable == 'financial_year' && $lookupValue == 'name') {
                $lookupValue = 'session_year';
            }

            if($optionType == 'entity' && Arr::get($val,'option.specific_entity_type')){
                $specificEntity = $val['option']['specific_entity_type'];
                $entityDetail = DB::table('cruds')->where('is_entity', 1)->select(['id', 'module_db'])->where('module_name', $specificEntity)->first();
                if (!$entityDetail) {
                    return [];
                }
                 $lookupTable = $entityDetail->module_db;
                 $lookupKey = ($lookupKey) ? $lookupKey : 'id';
                 $lookupValue = 'name';
            }

            if (!$lookupTable || !$lookupKey || !$lookupValue) {
                return $choices;
            }
            $concatRequired = str_contains($lookupValue, "|");
            $fields = ($concatRequired) ? DB::raw("CONCAT_WS(' '," . str_replace('|', ',', $lookupValue) . ") AS displayCusText") : $lookupTable.".".$lookupValue . " AS displayCusText";
            
            // if ($lookupTable == 'training_program_intakes' && str_contains($lookupValue,'month')) {
            //     $fields = DB::raw('MONTHNAME(STR_TO_DATE(' . $lookupValue . ', "%m")) as displayCusText');
            // }
            $query = DB::table($lookupTable)->select($fields, $lookupTable.".".$lookupKey);
            if ($lookupTable == 'financial_years' && !$applyFinancialYearCndn) {
                $whereCondn = "";
            }
            $query = ($whereCondn) ? $query->whereRaw($whereCondn) : $query;
            if ($lookupTable == 'multidomains') {
                $query = $query->whereRaw('name !="'.env('DEFAULT_DOMAIN').'"');
            }
            if(in_array($lookupTable,['users','impiger_users'])) {
                $dependentKey = ($lookupTable == 'users') ? 'id' : 'user_id';
                $query->join('role_users', 'role_users.user_id', $lookupTable . '.'.$dependentKey)
                        ->join('roles', 'roles.id', '=', 'role_users.role_id');
            }
            $rawCondition = get_common_condition($lookupTable);
            if(!empty($rawCondition)){
                $query = $query->whereRaw($rawCondition);
            }
            $instituteSecurity = Arr::get($val, 'option.specific_entity_type') ?:$lookupTable;
            $query = SELF::applyInstituteSecurityCondition($instituteSecurity, $query, $lookupTable);
            $model = get_model_from_table($lookupTable);

            if ($model) {
                $model = new $model();
                $query = apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, [$fields, $lookupTable . "." . $lookupKey], false);
            }
            if (request()->input('filter_columns')) {
                $requestFilters = getRequestFilters(true);
                $dependency = Arr::get($val, 'option.lookup_dependency_key');

                if ($dependency) {
                    $cols = explode("|",$dependency);
                    if(isValidArray($cols) && count($cols) > 1) {
                        $depedentFilterKey = $cols[1];
                        $depedentFilterVal = Arr::get($requestFilters, $depedentFilterKey);

                        if($depedentFilterKey && $depedentFilterVal) {
                            $query = $query->where($depedentFilterKey, $depedentFilterVal);
                        }
                    }
                }

                if(in_array($lookupTable, ['departments', 'training_program'])) {
                    $query = SELF::applyTrainingProgramSecurityCondition($lookupTable, $query);
                    if($lookupTable == 'training_program') {
                        $query = (Arr::get($requestFilters, 'institute_id')) ? $query->where('institute_id', Arr::get($requestFilters, 'institute_id')) : $query;
                        $query = (Arr::get($requestFilters, 'department_id')) ? $query->where('department_id', Arr::get($requestFilters, 'department_id')) : $query;
                    }
                }
            }
            if($groupBy) {
                $query->groupBy($groupBy);
            }
            if($lookupKey == $lookupValue) {
                $lookupKey = 'displayCusText';
            }
            $query->having('displayCusText', "!=", "");
            $choices = $query->orderBy("displayCusText")->pluck("displayCusText", $lookupKey)->toArray();
        }
        // $choices = (isValidArray($choices)) ? ["" => "Select Option"] + $choices : $choices;
        return $choices;
    }

    public static function loadEntityTypes()
    {
        $user = Auth::user();
        $qry = DB::table('cruds')->where('is_entity', 1);

        if ($user && $user->applyDataLevelSecurity()) {
            $userEntity = getUserEntitiesFromSession();
            $entityIds = array_keys($userEntity);
            $qry = $qry->whereIn('id', $entityIds);
        }

        return $qry->pluck('module_title', 'id')->toArray();
    }



    public static function loadEntityDetails($filters, $column)
    {
        $filtered = $userEntity = [];
        if(Arr::get($column, 'title') == "Entity" && Arr::has($filters, 0)) {
            $filtered = Arr::first($filters, function ($value, $key) {
                return str_contains($value['column'], 'entity_type');
            });
        }
        $user = Auth::user();
        $qry = DB::table('cruds')->where('is_entity', 1);

        if ($user && $user->applyDataLevelSecurity()) {
            $userEntity = getUserEntitiesFromSession();
            $entityIds = array_keys($userEntity);
            $qry = $qry->whereIn('id', $entityIds);
        }

        if(Arr::get($filtered, 'value')) {
            $qry = $qry->where('id', Arr::get($filtered, 'value'));
        }

        $entities = $qry->select(["module_db", "id", "module_title"])->get();
        $data = [];

        foreach($entities as  $entity) {
            $table = $entity->module_db;
            $model = get_model_from_table($table);
            if ($model) {
                $query = "";
                $model = new $model();
                $fields = [DB::raw('CONCAT_WS("|",'.$table.'.id,"'.$entity->id.'") AS id'), $table.'.name'];
                $query = $model->select($fields);
                if(!empty($userEntity) && Arr::get($userEntity,$entity->id)){
                    $query = $model->whereIn('id',  Arr::get($userEntity,$entity->id));
                }
                $entityData = $query->pluck('name', 'id')->toArray();
                if(count($entityData) > 0) {
                    $data[$entity->module_title] = $entityData;
                }
            }
        }
        return $data;

    }

    public static function getSelectedEntityOptions($dependentKey, $data, $entities)
    {
        if ($dependentKey) {
            $dependentValue = Arr::get($data, "$dependentKey");
            if (!$dependentValue) {
                return false;
            }
            return $entities = $entities->where('id', $dependentValue)->first();
        }

        return false;
    }

    public static function getSelectOptionValues($optionType, $lookupQuery, $lookupTable, $lookupKey, $lookupValue, $whereCondn = null, $dependentFilterKey = null, $data = [], $dependentKey = null, $subModule = null, $specificEntity = null)
    {
        $choices = [];

        if ($optionType != 'entity' && (!$lookupTable || !$lookupKey || !$lookupValue) && $optionType != 'datalist') {
            return $choices;
        }

        if ($optionType == 'datalist') {
            $optionList = explode("|", $lookupQuery);

            foreach ($optionList as $opt) {
                list($key, $value) = explode(":", $opt);
                $choices[$key] = $value;
            }
        } else if ($optionType == 'external') {
            $concatRequired = str_contains($lookupValue, "|");
            $fields = ($concatRequired) ? DB::raw("CONCAT_WS(' '," . str_replace('|', ',', $lookupValue) . ") AS displayCusText") : $lookupTable.".".$lookupValue . " AS displayCusText";
            if (in_array($lookupTable,['training_program_intakes','training_program_intake_semester_mapping']) && str_contains($lookupValue,'month')) {
                $fields = DB::raw('MONTHNAME(STR_TO_DATE(' . $lookupValue . ', "%m")) as displayCusText');
            }
            $query = DB::table($lookupTable)->select($fields, $lookupKey);
            $dependentValue = null;

            if ($dependentKey) {
                $dependentKey = str_replace("[", ".", rtrim($dependentKey, "]"));
                $dependentKey = ($subModule && !(str_contains($subModule, "["))) ? $subModule . "." . $dependentKey : $dependentKey;
                $dependentValue = Arr::get($data, "$dependentKey");

                if (!$dependentValue) {
                    return [];
                }
            }

            if ($lookupTable == 'multidomains') {
                $query = $query->whereRaw('name !="'.env('DEFAULT_DOMAIN').'"');
            }
            if(!in_array($lookupTable, config('general.exclude_apply_filters_select_option_table', []))) {
                $model = get_model_from_table($lookupTable);
                if(!$model && $lookupTable == 'roles'){
                    $model = \Impiger\ACL\Models\Role::class;
                }
                if ($model) {
                    $model = new $model();
                    $query = apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, [$fields, $lookupTable . "." . $lookupKey], false);
                }
            }
            if (in_array($lookupTable, ['users', 'impiger_users'])) {
                $dependentKey = ($lookupTable == 'users') ? 'id' : 'user_id';
                if(!joinTableExists($query, 'role_users')) {
                    $query->join('role_users', 'role_users.user_id', $lookupTable . '.' . $dependentKey)
                            ->join('roles', 'roles.id', '=', 'role_users.role_id');
                }
            }

            $query = ($whereCondn) ? $query->whereRaw($whereCondn) : $query;
            $query = ($dependentKey && $dependentValue) ? $query->where($dependentFilterKey, $dependentValue) : $query;
            $rawCondition = get_common_condition($lookupTable);
            if(!empty($rawCondition)){
                $query = $query->whereRaw($rawCondition);
			}

            if(in_array($lookupTable, ['departments'])) {
                $query = SELF::applyTrainingProgramSecurityCondition($lookupTable, $query);
            }
            //$choices = $query->pluck("displayCusText", $lookupKey)->toArray();
            $choices = $query->orderBy("displayCusText")->pluck("displayCusText", $lookupKey)->toArray();
        } else if ($optionType == 'entity') {
            $lookupKey = ($lookupKey) ? $lookupKey : 'id';
            $user = Auth::user();
            $query = '';
            $entities = DB::table('cruds')->where('is_entity', 1)->get();
            $fields = $lookupTable.'.name as displayCusText';
            $entityDetail = "";

            if ($specificEntity) {
                $entityDetail = DB::table('cruds')->where('is_entity', 1)->select(['id', 'module_db'])->where('module_name', $specificEntity)->first();
                if (!$entityDetail) {
                    return [];
                }
            }

            if ($user && $user->applyDataLevelSecurity()) {
                $userEntity = getUserEntitiesFromSession();
                $entityIds = array_keys($userEntity);
                if ($specificEntity) {
                    $specEntIds = \Arr::get($userEntity, $entityDetail->id);
                    $specEntIds = ($specEntIds) ? $specEntIds : [];
                    $query = DB::table($entityDetail->module_db)->select($fields, $lookupKey)->whereIn($entityDetail->module_db . '.id', $specEntIds);
                    $rawConditions = get_common_condition($entityDetail->module_db);
                    if(!empty($rawConditions)){
                        $query = $query->whereRaw($rawConditions);
                    }

                    $query = SELF::applyInstituteSecurityCondition($specificEntity, $query);
                    return $choices = $query->orderBy("displayCusText")->pluck("displayCusText", $lookupKey)->toArray();
                } elseif (count($userEntity) == 1) {
                    $entities = $entities->whereIn('id', $entityIds)->first();
                } else {
                    $entities = SELF::getSelectedEntityOptions($dependentKey, $data, $entities);
                    if (empty($entities)) {
                        return [];
                    }
                }
            } else {
                if ($specificEntity) {
                    $query = DB::table($entityDetail->module_db)->select($fields, $lookupKey);
                    $rawConditions = get_common_condition($entityDetail->module_db);
                    if(!empty($rawConditions)){
                        $query = $query->whereRaw($rawConditions);
                    }

                    $query = SELF::applyInstituteSecurityCondition($specificEntity, $query, $entityDetail->module_db);
                    $choices = $query->orderBy("displayCusText")->pluck("displayCusText", $lookupKey)->toArray();
                    return $choices;
                }
                $entities = SELF::getSelectedEntityOptions($dependentKey, $data, $entities);
                if (empty($entities)) {
                    return [];
                }
            }

            if (!isset($entities->module_db)) {
                return [];
            }

            $query = DB::table($entities->module_db)->select($fields, $lookupKey);
            $model = get_model_from_table($entities->module_db);
            if ($model) {
                $model = new $model();
                $query = apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, [$fields, $entities->module_db . "." . $lookupKey], false);
            }

            $choices = $query->orderBy("displayCusText")->pluck("displayCusText", $lookupKey)->toArray();
        }
        return $choices;
    }

    public static function getRadioOptionValues($optionType, $lookupQuery)
    {
        $choices = [];

        if (!$optionType || !$lookupQuery) {
            return $choices;
        }

        if ($optionType == 'datalist') {
            $optionList = explode("|", $lookupQuery);

            foreach ($optionList as $opt) {
                list($key, $value) = explode(":", $opt);
                $option = [];
                $option[0] = $key;
                $option[1] = $value;
                $choices[] = $option;
            }
        }
        return $choices;
    }

    public static function formatEntityValue($entityType, $entityId, $model = null)
    {
        $entityValue = "";

        if (!$entityType || !$entityId) {
            return $entityValue;
        }

        $entityTable = DB::table('cruds')->select('module_db')->where('id', $entityType)->pluck('module_db')->first();

        $customFields = ENTITY_CUSTOM_FIELD;
        $field = (Arr::has($customFields,$entityTable)) ? Arr::get($customFields,$entityTable) : 'name';

        if ($entityTable) {
            $model = get_model_from_table($entityTable);
        }

        if ($model) {
            $model = new $model();
            $model = $model->where('id', $entityId)->get()->first();
            $entityValue = (isset($model->$field)) ? $model->$field : "";
        }

        return $entityValue;
    }

    public static function formatEntityTypeValue($entityType)
    {

        if (!$entityType) {
            return "";
        }

        $entityTable = DB::table('cruds')->select('module_db')->where('id', $entityType)->pluck('module_db')->first();
        return ucfirst($entityTable);
    }

    public static function formatRows($value, $formatAs, $formatValue, $row = null, $conn = null)
    {
        if ($conn) {
            $value = self::formatLookupValue($value, $conn);
        }

        preg_match('~{([^{]*)}~i', $formatValue, $match);
        if (isset($match[1])) {
            $real_value = $row->{$match[1]};
            $formatValue    = str_replace($match[0], $real_value, $formatValue);
        }

        if ($formatAs == 'image') {
            // FORMAT AS IMAGE
            $vals = '';
            $values = explode(',', $value);

            foreach ($values as $val) {
                if ($val != '') {
                    if (file_exists('.' . $formatValue . $val))
                        $vals .= '<a href="' . url($formatValue . $val) . '" target="_blank" class="previewImage"><img src="' . asset($formatValue . $val) . '" border="0" width="50" class="img-circle avatar" style="margin-right:2px;" /></a>';
                }
            }
            $value = $vals;
        } elseif ($formatAs == 'link') {
            // FORMAT AS LINK
            $value = '<a href="' .  url($formatValue . $value) . '" target=__blank>' . $value . '</a>';
        } else if ($formatAs == 'date') {
            // FORMAT AS DATE
            if ($formatValue && strtotime($formatValue)) {
                $value = date("$formatValue", strtotime($value));
            }
        } else if ($formatAs == 'file') {
            // FORMAT AS FILES DOWNLOAD
            $vals = '';
            $values = explode(',', $value);
            foreach ($values as $val) {

                if (file_exists('.' . $formatValue . $val))
                    $vals .= '<a href="' . asset($formatValue . $val) . '" target=_blank> ' . $val . ' </a><br />';
            }
            $value = $vals;
        } else if ($formatAs == 'database') {
            // Database Lookup

            if ($value != '') {
                $fields = explode("|", $formatValue);
                if (count($fields) >= 2) {
                    $field_table  =  str_replace(':', ',', $fields[2]);
                    $field_toShow =  explode(":", $fields[2]);

                    if (in_array($fields[0],['training_program_intakes','training_program_intake_semester_mapping']) && str_contains($field_table, 'month')) {
                        $field_table = DB::raw('MONTHNAME(STR_TO_DATE(' . $field_table . ', "%m")) as '.$field_table);
                    }

                    if (is_array($value) && count($value) > 0) {
                        $query = DB::table($fields[0])->select($field_table);
                        $query->whereIn($fields[1], $value);
                        $choices = $query->pluck($field_table)->toArray();
                        return ($choices && is_array($choices)) ? implode(",", $choices) : "";
                    }

                    $Q = DB::select(" SELECT " . $field_table . " FROM " . $fields[0] . " WHERE " . $fields[1] . " IN(" . $value . ") ");
                    if (count($Q) >= 1) {
                        $value = '';
                        foreach ($Q as $qv) {
                            $sub_val = '';
                            foreach ($field_toShow as $fld) {
                                $sub_val .= $qv->{$fld} . ' ';
                            }
                            $value .= $sub_val . ', ';
                        }
                        $value = substr($value, 0, strlen($value) - 2);
                    }
                    if ($fields[2] === 'fee_paid') {
                        if ($value == 1) {
                            $value = 'Free';
                        } else if ($value == 2){
                            $value = 'Paid';
                        } else {
                            $value = 'Not available';
                        }
                    }
                }
            }
        } else if ($formatAs == 'checkbox' or $formatAs == 'radio') {
            // FORMAT AS RADIO/CHECKBOX VALUES

            $values = explode(',', $formatValue);
            if (count($values) >= 1) {
                for ($i = 0; $i < count($values); $i++) {
                    $val = explode(':', $values[$i]);
                    if (trim($val[0]) == $value) $value = $val[1];
                }
            } else {

                $value = '';
            }
        } elseif ($formatAs == 'function') {

            $formaters  = $formatValue;
            $c = explode("|", $formaters);
            $values = $c[2];
            foreach ($row as $k => $i) {
                if (preg_match("/$k/", $values))
                    $values = str_replace($k, $i, $values);
            }
            if (isset($c[0]) && class_exists($c[0])) {
                $args = explode(':', $values);
                if (count($args) >= 2) {
                    $value = call_user_func(array($c[0], $c[1]), $args);
                } else {
                    $value = call_user_func(array($c[0], $c[1]), $values);
                }
            } else {
                $value = 'Class Doest Not Exists';
            }
        } else if ($formatAs == 'workflow') {
            $pathInfo = \Request::getPathInfo();
            if (is_plugin_active('workflows') && \Workflow::get($row)){
                if (str_contains($pathInfo, 'admin') && \Auth::id()) {
                $workflow = (apply_filters(APPLY_WORKFLOW_TRANSITION, get_class($row), $row->id)) ? : ucfirst($value);
                return $workflow;
            }
            $workflow=\Workflow::get($row);
            $property = ($workflow->getMetadataStore()->getMetadata('module_property'))?:'status';
            $labelClass = ($workflow->getMetadataStore()->getMetadata('label_class'))?:[];
            $statusLabel = Arr::get($labelClass,$value) ? : 'label-default';
            return \Html::tag('span', ucfirst($value), ['class' => $statusLabel.' status-label'])->toHtml();
            }else{
                return \Html::tag('span', ucfirst($value), ['class' => 'label-default status-label'])->toHtml();
            }
        } else {
        }

        return $value;
    }

    public static function createUpdateDraftedForms($request,$model){
        $input = [
            'user_id' => Auth::id(),
            'reference_type' => $model->getTable(),
            'reference_id' => $model->id,

        ];
        $draftModel = new DraftedData();
        $existingData = $draftModel->where($input)->first();
        $inputs = $request->except(['_token', 'draft']);
        foreach($inputs as $key => $value){
            if(is_array($value)){
                if(isFillableField($model, $key)){
                    $inputs[$key] = json_encode($value);
                }else if(method_exists($model,$key)){
                    $inputs[$key] = collect($value);
                }
            }
        }
        $input['request_data'] = $inputs;
        if($existingData){
            $draftModel->updateOrCreate(['id' => $existingData->id], $input);
        }else{
            return $draftModel->create($input);
        }
        $moduleName = \App\Utils\CrudHelper::getModuleNameUsingModuleDBField($model->getTable());
        event(new \Impiger\AuditLog\Events\AuditHandlerEvent(
            $moduleName,
            'drafted',
            $model->id,
            \AuditLog::getReferenceName($moduleName, $model),
            'info'
        ));
    }

    public static function getDraftedForms($model){
        $requestData = $model;
        $condn = [
            'user_id' => Auth::id(),
            'reference_type' => $model->getTable(),
            'reference_id' => $model->id,
        ];
        $draftedData = DraftedData::where($condn)->first();
        $inputs = ($draftedData) ? $draftedData->request_data : null;
        return $inputs;
    }

    public static function deleteDraftedForms($model){
        $condn = [
            'reference_type' => $model->getTable(),
            'reference_id' => $model->id,
        ];
        return DraftedData::where($condn)->forceDelete();
    }

    public static function createUpdateSubforms($request, $model, $key, $id = null,$preventDelete = null)
    {
        $subformInput = $request->only($key);
        $foreignKey = $model->$key()->getForeignKeyName();

        if ($key == 'user_addresses') {
            if (!Arr::has($subformInput[$key], 'same_as_present')) {
                $subformInput[$key]['same_as_present'] = 0;
            }
        }

        if ($subformInput && is_array($subformInput)) {
            foreach ($subformInput as $input) {
                if ($id) {
                    $model->$key()->updateOrCreate([$foreignKey => $id], $input);
                } else {
                    $model->$key()->create($input);
                }
            }
        }
    }

    public static function createUpdateSubformsHasMany($request, $model, $key, $id = false, $preventDelete = null)
    {
        $subformInput = $request->input($key);
        $ids = [];
        $localKey = $model->$key()->getLocalKeyName();
        if ($subformInput && is_array($subformInput)) {
            foreach ($subformInput as $input) {
                $recordID = Arr::get($input, $localKey);
                if(checkAtleastOneValueExist($input, ['id', $localKey])) {
                    if ($recordID) {
                        $result = $model->$key()->updateOrCreate([$localKey => $recordID], $input);
                    } else {
                        $result = $model->$key()->create($input);
                    }

                    $ids[] = $result->id;
                }
            }
        }
        $checkPreventDelete = ($preventDelete) ? self::checkPreventDelete($preventDelete) : false;
        if (!$checkPreventDelete && Arr::has($ids, '0')) {
            $model->$key()->whereNotIn($localKey, $ids)->delete();
        }
    }

    public static function createUpdateSubformAsMultiSelect($request, $model, $key, $groupKey,$preventDelete = null)
    {
        $subformInput = $request->input($key);
        $selectedData = Arr::get($subformInput, $groupKey);
        $ids = [];
        $localKey = $model->$key()->getLocalKeyName();
        $related = $key . '_multiple';

        if ($selectedData) {
            foreach ($selectedData as $value) {
                $data = [];
                $data[$localKey] = $model->$localKey;
                $data[$groupKey] = intval($value);
                $recordID = $model->$related()->where($data)->first();
                if ($recordID) {
                    $model->$related()->updateOrCreate($data, $data);
                } else {
                    $model->$related()->create($data);
                }

                $ids[] = $value;
            }
        }
        $checkPreventDelete = ($preventDelete) ? self::checkPreventDelete($preventDelete) : false;
        if (!$checkPreventDelete && Arr::has($ids, '0')) {
            $model->$related()->whereNotIn($groupKey, $ids)->forceDelete();
        }
    }

    public static function createUpdateSkillsSubforms($request, $model, $key, $id = null,$preventDelete = null)
    {
        $subformInput = $request->only($key);
        $foreignKey = $model->$key()->getForeignKeyName();

        if ($subformInput && is_array($subformInput)) {
            foreach ($subformInput as $input) {
                $input['language_details'] = json_encode($input['language_details']);
                $input['project_details'] = json_encode($input['project_details']);
                if ($id) {
                    $model->$key()->updateOrCreate([$foreignKey => $id], $input);
                } else {
                    $model->$key()->create($input);
                }
            }
        }
    }

    public static function checkPreventDelete($preventDelete){
        if ($preventDelete) {
            $actionArray = explode('|', $preventDelete);
            $pathInfo = \Request::getPathInfo();

            if (!str_contains($pathInfo, 'admin') && in_array('front_end', $actionArray)) {
                return true;
            } elseif (str_contains($pathInfo, 'admin') && in_array('back_end', $actionArray)) {
                return true;
            }
        }
    }

	public static function saveFiles($file, $request)
    {
            // $name = $file->getClientOriginalName();
            // $fileName = time()."_".$name;
            // $file->storeAs('uploads/website',$fileName);
//            $result = \RvMedia::handleUpload($file, 0, 'website');
            return \RvMedia::handleUpload($file, 0, 'website');
    }

    public static function saveRelationFileNameInDB($fileArr, $request, $model, $key, $indx = null)
    {
        $subfileKey = array_keys($fileArr);
        $subfileKey = Arr::get($subfileKey, 0);
        $subfile = Arr::pull($fileArr,$subfileKey);
        $saveFile = SELF::saveFiles($subfile, $request);
        if(Arr::get($saveFile,'data')){
            $fileName = $saveFile['data'];
            $result = (!is_null($indx)) ? $model->$key->get($indx) : $model->$key;
            if($result) {
                $result->$subfileKey =  $fileName->url;
                $result->save();
            }
        }
    }

    public static function uploadFiles($request, $model)
    {
        $files = $request->file();

        if(!$files) {
            return false;
        }

        foreach($files as $key => $file) {
            if(Arr::get($file, 0)) {
                foreach($file as $indx => $subfileArr) {
                    if(is_array($subfileArr)) {
                        SELF::saveRelationFileNameInDB($subfileArr, $request, $model, $key, $indx);
                    }
                }
            } elseif(is_array($file)){
                SELF::saveRelationFileNameInDB($file, $request, $model, $key);
            }else {
                $saveFile = SELF::saveFiles($file, $request);
                if(Arr::get($saveFile,'data')){
                    $fileName = $saveFile['data'];
                    $model->$key = $fileName->url;
                    $model->save();
                }

            }
        }

        return true;
    }

    public static function getLabelName()
    {
        $user = Auth::user();
        $userEntity = ($user) ?  getUserEntitiesFromSession() : [];
        $entityIds = array_keys($userEntity);
        $label = 'Entity';
        if (count($userEntity) == 1) {
            $entity = DB::table('cruds')->whereIn('id', $entityIds)->first();
            if (!empty($entity)) {
                $label = ucfirst(\Str::camel($entity->module_name));
            }
        }
        return $label;
    }

    public static function isEntityCheck($form)
    {
        $user = Auth::user();
        $choices = [];
        if ($user && $user->applyDataLevelSecurity()) {
            $userEntity = getUserEntitiesFromSession();
            $modifyField = '';
            $entityIds = array_keys($userEntity);
            if (count($userEntity) > 1) {
                $entities = DB::table('cruds')->whereIn('id', $entityIds)->get();
                foreach ($entities as $entity) {
                    if (!empty($entity)) {
                        $choices[$entity->id] = ucfirst(\Str::camel($entity->module_title));
                    }
                }
                return $form->modify("entity_type", "customSelect", ["label" => "Entity Type", "label_attr" => ["class" => "control-label  userEntity"], "attr" => ["class" => "select-full"], "choices"    => $choices, "empty_value" => "Select"]);
            }
        } else {
            $entities = DB::table('cruds')->where('is_entity', 1)->get();
            foreach ($entities as $entity) {
                if (!empty($entity)) {
                    $choices[$entity->id] = ucfirst(\Str::camel($entity->module_title));
                }
            }
            return $form->modify("entity_type", "customSelect", ["label" => "Entity Type", "label_attr" => ["class" => "control-label  userEntity"], "attr" => ["class" => "select-full"], "choices"    => $choices, "empty_value" => "Select"]);
        }

        return $modifyField;
    }

    public static function getEntityValue()
    {
        $user = Auth::user();
        if ($user->super_user != 1) {
            $userEntity = getUserEntitiesFromSession();
            if (!empty($userEntity)) {
                $keys = array_keys($userEntity);
                return $keys[0];
            }
        }
    }



    /**
     * @return html
     */
    public static function getCustomFields($field, $options,$model = null)
    {
        $permission = "";
        if($model){
            $pluginName = get_plugin_name($model);
            $permission = $pluginName.'.inline_edit';
        }
        if($permission && Auth::user()->hasPermission($permission)){
            if (empty($options['type'])) {
                $options['type']='hidden';
            }
            return view('core/table::partials.custom-fields', compact('field'), compact('options'))->render();
        }

        return $options['value'];
    }

    /**
     * @return array
     */
    public static function getInlineEditBtn($buttons, $permission,$table)
    {
        $tableClass = get_class($table);
        if (Auth::user() && Auth::user()->hasPermission($permission)) {
            $buttons = array_merge(["save" => [
                "link" => "javascript:void(0)",
                "text" => "<i class='fa fa-save' data-class-item='$tableClass'></i> Save"
            ]], $buttons);
        }

        return $buttons;
    }

    /**
     * @return array
     */
    public static function getSubscriptionBtn($module, $item)
    {
        $crud = DB::table('cruds')->where('module_name', $module)->select('id','module_action_meta')->first();

        $subscriptionBtn = "";
        if (!empty($crud) && $crud->module_action_meta) {
            $moduleActionMeta = $crud->module_action_meta;
            foreach ($moduleActionMeta as $key => $value) {
                foreach ($value as $row) {
                    if ((Auth::user() && Auth::user()->hasPermission($module . '.' . $key))) {
                        if (Arr::has($row,'action') && $row['action']) {
                            $icon = (Arr::has($row,'icon')) ? $row['icon'] : "fa fa-bell";
                            $action = 'row' . ucfirst($row['action']) . 'Dialog';
                            $subscriptionBtn .= "<a class='btn btn-icon btn-sm btn-primary $action' data-model_class='" . get_class($item) . "' data-toggle='tooltip' data-original-title='" . $row["title"] . "' data-section='/admin/" . Str::plural($module) . "/$action/$item->id' data-item_id='$item->id' data-subscribed_institute_ids = '$item->subscribed_institute_ids'><i class='fa fa-bell'></i></a>";
                        } else {
                            $icon = (Arr::has($row,'icon')) ? $row['icon'] : "fa fa-arrow-circle-up";
                            $subscriptionBtn .= "<a class='btn btn-icon btn-sm  btn-primary' data-toggle='tooltip' data-original-title='" . $row["title"] . "' href='/admin/" . Str::plural($row["module"]) . "?relation_key=$item->id' ><i class='$icon'></i></a>";
                        }
                    }
                }
            }
        }
        return $subscriptionBtn;
    }



    /**
     * @return array
     */
    public static function getRowActivationActionBtn($rowData, $model, $permission,$hideAction = null,$module=null, $url = null)
    {
        $action = '';
        if (Auth::user() && Auth::user()->hasPermission($permission)) {
            $btnType = ($rowData->is_enabled) ? "btn-success" : "btn-danger";
            $title = ($rowData->is_enabled) ? "Disable" : "Enable";
            $url = ($url) ? $url.$rowData->id : "/admin/cruds/row_activation/".$rowData->id;
            if (isset($rowData->is_default) && !$rowData->is_default) {
                $action = "<a class='btn btn-icon btn-sm $btnType rowActivationDialog' data-module='$module' data-toggle='tooltip' data-original-title='$title' data-section='$url' data-model='" . $model . "' data-value='$rowData->is_enabled'><i class='fa fa-power-off'></i></a>";
            }
            if(!isset($rowData->is_default)){
                $action = "<a class='btn btn-icon btn-sm $btnType rowActivationDialog' data-module='$module' data-toggle='tooltip' data-original-title='$title' data-section='$url' data-model='" . $model . "' data-value='$rowData->is_enabled'><i class='fa fa-power-off'></i></a>";
            }
            if (!empty($hideAction)) {
                $hideAction = explode(":", $hideAction);
                if (isset($rowData->{$hideAction[0]}) && $rowData->{$hideAction[0]} == $hideAction[1]) {
                    $action = "";
                }
            }
        }
        return $action;
    }




    public static function destroyUserSession($user){
        if(!$user){
            return null;
        }
        $session = \DB::table('sessions')->where('user_id', $user->id)->first();
        if ($session) {
            $user->remember_token = NULL;
            $user->save();
            \DB::table('sessions')->where('user_id', $user->id)->delete();
        }
    }
    /**
     * @return html
     */
    public static function getNameFieldLink($item, $module, $isEdit = true, $isPublic = false)
    {
        if ($isPublic) {
            $domain = null;
            if (is_plugin_active('multidomain') && isset($item->domain_id)) {
                $domain = app(\Impiger\Multidomain\Multidomain::class)->getDomainNameById($item->domain_id);
            }
            return ($domain) ? \Html::link("//" . $domain, $item->name, [], true, false) : $item->name;
        } else if (Auth::user() && !Auth::user()->hasPermission($module . '.edit') || !$isEdit) {
            return $item->name;
        }
        return \Html::link(route($module . '.edit', $item->id), $item->name);
    }

    public static function inArrayAny($needles, $haystack)
    {
        if (is_string($haystack)) {
            $haystack = array($haystack); //Convert to an array if a string is provided
        }

        return (bool) array_intersect($needles, $haystack);
    }

    public static function getFileType($type)
    {
        $pathInfo = \Request::getPathInfo();

        if (!str_contains($pathInfo, 'admin') || !Auth::user()) {
            return $type = 'file';
        } else {
            return $type;
        }

    }

    public static function getFileTypeValidationOnWebsite($type = "file")
    {
        $rule = "";
        $pathInfo = \Request::getPathInfo();

        if (!str_contains($pathInfo, 'admin') || !Auth::user()) {
            return $rule = ($type == 'image') ? 'mimes:jpg,jpeg,png,gif|max:20000' : 'mimes:jpg,jpeg,png,gif,txt,docx,zip,csv,xls,xlsx,ppt,pptx,pdf,doc|max:20000';
        }

        return $rule;
    }

    public static function checkPermission($request) {
        if ($request->has('change_profile')) {
            if($request->user()->getKey() != $request->get('change_profile')) {
                return response()->json(['error' => 'Unauthenticated.'], 401);
            }
        }
    }

    public static function isFieldVisible($restrictedRoleIds, $type = 1, $hiddenConfig = null)
    {
        $pathInfo = \Request::getPathInfo();
        if ($hiddenConfig) {
            $actionArray = explode('|', $hiddenConfig);


            if (!str_contains($pathInfo, 'admin') && in_array('front_end', $actionArray)) {
                return false;
            } elseif (str_contains($pathInfo, 'admin') && in_array('back_end', $actionArray)) {
                return false;
            } elseif(str_contains($pathInfo, 'edit') && in_array('edit', $actionArray)){
                return false;
            }elseif(str_contains($pathInfo, 'create') && in_array('create', $actionArray)){
                return false;
            }

            foreach ($actionArray as $act) {
                if (str_contains($pathInfo, $act)) {
                    return false;
                }
            }
        }

        $user = Auth::user();
        if ($user && $user->isSuperUser()) {
            return true;
        }

        if (str_contains($pathInfo, 'admin') && $restrictedRoleIds) {
            $pathInfo = \Request::query();
            $restictRoleIdUri = Arr::get($pathInfo, 'restricted_roleid');
            $loginRole = ($restictRoleIdUri) ? explode('|', base64_decode($restictRoleIdUri)) : $user->roles->pluck('id')->toArray();
            $restrictedRoleIds = explode('|', $restrictedRoleIds);

            if ($type == 2) {
                return Self::inArrayAny($restrictedRoleIds, $loginRole);
            } else {
                $intersect = array_intersect($restrictedRoleIds, $loginRole);

                if (is_array($intersect)) {
                    return (count($intersect) < count($loginRole));
                }
            }
        }

        return true;
    }

    public static function isFieldDisabled($action,$isRequest = false)
    {
        if ($action) {
            $actionArray = explode('|', $action);
            $pathInfo = \Request::getPathInfo();

            foreach ($actionArray as $act) {
                if (str_contains($pathInfo, $act)) {
                    return true;
                }
                if($isRequest && \Request::has($act)){
                    return true;
                }
            }

            return false;
        }

        return false;
    }

    public static function jsonToStringFormat($data) {

        $formattedStr = '';
        
        $formattedData = array();
        if(is_array($data) && count($data) > 0) {
            foreach ($data as $key => $dataValue) {
                // echo json_encode($dataValue);
                $item = array();
                if(is_array($dataValue) && count($dataValue) > 0) {
                    foreach ($dataValue as $value) {

                        $item[] = (is_array($value) && count($value) > 0) ? Arr::get($value, 'value') : $value;
                    }
                    $formattedData[] = implode(",",$item);
                } else {
                    $formattedData[] = $dataValue;
                }
            }
            $formattedStr = implode("\n",$formattedData);
        }

        // dd($formattedStr);
        return $formattedStr;
    }

    public static function updateCoreUser($id, $request, $userRepository, $service, $activateUserService = null)
    {
        $response = (object)[];
        $response->success = false;
        $user = $userRepository->findOrFail($id);

        $currentUser = $request->user();
        if (($currentUser->hasPermission('users.update-profile') && $currentUser->getKey() === $user->id) ||
            $currentUser->isSuperUser()
        ) {
            if ($user->email !== $request->input('email')) {
                $users = $userRepository->getModel()
                    ->where('email', $request->input('email'))
                    ->where('id', '<>', $user->id)
                    ->count();
                if ($users) {
                    $response->errorMsg = trans('core/acl::users.email_exist');
                    return $response;
                }
            }
            
            if ($request->has('username') && $user->username !== $request->input('username')) {
                $users = $userRepository->getModel()
                    ->where('username', $request->input('username'))
                    ->where('id', '<>', $user->id)
                    ->count();
                if ($users) {
                    $response->errorMsg = trans('core/acl::users.username_exist');
                    return $response;
                }
            }
        }

        $result = $service->execute($request);
        $user->fill($request->input());
        $userRepository->createOrUpdate($user);
        do_action(USER_ACTION_AFTER_UPDATE_PROFILE, USER_MODULE_SCREEN_NAME, $request, $user);

        if ($request->input('is_login_needed')) {
            $activateUserService->activate($user);
        } else if($request->has('is_login_needed')){
            app(\Impiger\ACL\Repositories\Interfaces\ActivationInterface::class)->remove($user);
        }

        $response->success = true;
        return $response;
    }

    public static function createImpigerUser($request, $model,$coreUserRepository, $activateUserService = null,$roleSlug=null,$isActivate=true)
    {
        $users=[];
        if($request->has('head')){
            $users[] = $request->input('head');
        }
        if($request->has('faculty')){
            $users[] = $request->input('faculty');
        }
        
        $currentUser = $request->user();
        if(!empty($users)){
            foreach($users as $row){
                if($row['email']){
                $randomPassword = CrudHelper::randomPassword();
                $request['email'] = $row['email'];
                $request['username'] = $row['email'];
                $request['first_name'] = $row['name'];
                $request['phone_number'] = $row['phone_number'];
                $request['designation'] = $row['designation'];                
                $coreUserExists = $coreUserRepository->getFirstBy(['email'=>$request['email']]);
                if(!$coreUserExists){
                    $request['password'] = Hash::make($randomPassword);
                    $user = $coreUserRepository->createOrUpdate($request->input());
                }else{
                    $user = $coreUserExists;
                }
                if($user){
                    $request['password'] = $randomPassword;
                    $request['user_id'] = $user->id;
                    $userExists = app(\Impiger\User\Repositories\Interfaces\UserInterface::class)->getFirstBy(['user_id'=>$user->id]);
                    if(!$userExists){
                        $impigerUser = app(\Impiger\User\Repositories\Interfaces\UserInterface::class)->createOrUpdate($request->input());
                    }   

                    if($roleSlug){
                        $role = \Impiger\ACL\Models\Role::where('slug', $roleSlug)->whereNull('deleted_at')->first();
                        $role->users()->attach($user->id);
                        if(in_array(get_class($model),config("general.user_entity_supported_models",[]))){
                            $cond = [
                                'user_id' => $user->id,
                                'reference_id' => $model->id,
                                'reference_type' => get_class($model),
                                'reference_key' => getEntityId($model->getTable(),'module_db'),
                                'role_id' => $role->id
                            ];
                            $existsEntity = \Impiger\ACL\Models\UserPermission::where($cond)->first();
                            $data = $cond;
                            $data['role_id'] = $role->id;
                            $data['role_permissions'] = $role->permissions;
                            if ($existsEntity) {
                                \Impiger\ACL\Models\UserPermission::where($cond)->update($data);
                            } else {
                                \Impiger\ACL\Models\UserPermission::create($data);
                            }
                        }
                        
                        event(new \Impiger\ACL\Events\RoleAssignmentEvent($role, $user));  
                    }



                    if ($isActivate) {
                        $activateUserService->activate($user);
                        $impigerUser = $impigerUser::where('user_id',$user->id)->first();
                        $user->domain_href = getUserDomainUrl($user->id);  
                        $user->temp_password = $impigerUser->password;
                        CrudHelper::sendEmailConfig('user','{"create":"1","edit":null,"subject":"'.USER_LOGIN_CREDENTIALS_SUBJECT.'","message": "'.get_setting_email_template_content('core', 'base', 'user').'","send_to":"based_on_fields","reciever_role":null,"reciever_field":"email|contact_email","default_reciever":null}',$user);
                    }
                }
                
            }
        }
        }

}

    public static function getMultiSelectText($table, $key, $fieldName, $name, $whereCondn = null, $model = null)
    {
        $concatRequired = str_contains($name, "|");
        $fields = ($concatRequired) ? DB::raw("CONCAT_WS(' '," . str_replace('|', ',', $name) . ") AS displayCusText") : $name . " AS displayCusText";
        $query = DB::table($table)->select($fields, $key);
        $query = ($whereCondn) ? $query->whereRaw($whereCondn) : $query;
        if (!$fieldName || !isset($model->$fieldName)) {
            return "";
        }
        $query->whereIn($key, $model->$fieldName);
        $choices = $query->pluck("displayCusText", $key)->toArray();
        return ($choices && is_array($choices)) ? implode(",", $choices) : "";
    }

    public static function formatDate($date, $format = null)
    {
        if (!$date || !strtotime($date)) {
            return "";
        }

        $format = ($format) ? $format : config('core.base.general.date_format.date');
        return \BaseHelper::formatDate($date, $format);
    }

    public static function formatDateTime($date, $format = 'j M Y H:i')
    {
        if (!$date || !strtotime($date)) {
            return "";
        }

        $format = ($format) ? $format : config('core.base.general.date_format.date_time');
        return \BaseHelper::formatTime($date, $format);
    }

    public static function getTagValues($model, $field)
    {
        $value = "";
        if (!$field) {
            return $value;
        }
        if($model && isset($model->$field)){
            $result = json_decode($model->$field);
            if (is_array($result)) {
                $value = implode(",", array_column($result, 'value'));
            } else {
                $value = $result;
            }
        }
        return $value;
    }

    public static function generateCustomCode($field, $table, $key, $prefix = NULL, $suffix = NULL)
    {
        $value = "";

        if (!$table) {
            return $value;
        }
        $pathInfo = \Request::getPathInfo();
        if (str_contains($pathInfo, 'edit')) {
            return $value = $field;
        }
        $getFieldValue = DB::table($table)->max($key);
        $incrementValue = sprintf("%02d", ($getFieldValue + 1));
        if ($prefix) {
            $value = $prefix . $incrementValue;
        } else {
            $value = $incrementValue;
        }
        return $value;
    }
    public static function getSchedulerConfig($moduleName, $schedules)
    {
        if (!$moduleName && !$schedules) {
            return false;
        }
        $crud = DB::table('cruds')->where('module_name', $moduleName)->first();
        $table = $crud->module_db;
        if ($crud->parent_id) {
            $parentModule = DB::table('cruds')->where('id', $crud->parent_id)->first();
            if (!empty($parentModule)) {
                $table = $crud->module_db;
            }
        }
        $scheduleConfig = json_decode($schedules, true);
        $whereCnd = Arr::get($scheduleConfig, 'sql_where');
        $updateFields = Arr::get($scheduleConfig, 'status_change_to');
        $updateValue = Arr::get($scheduleConfig, 'status_change_value');
        $emailSubject = Arr::get($scheduleConfig, 'notification_subject');
        $emailMessage = Arr::get($scheduleConfig, 'notification_message');
        $defaultRecievers = Arr::get($scheduleConfig, 'default_reciever');
        $defaultRecieverEmails = getEmailIdsfromRoles($defaultRecievers);
        $sendTo = Arr::get($scheduleConfig, 'send_to');
        $reciever = Arr::get($scheduleConfig, 'reciever');
        $recieverEmails = [];
        if ($sendTo == 'roles') {
            $recieverEmails = getEmailIdsfromRoles($reciever);
        } else {
            $recieverEmails = getEmailIdsfromUsers($reciever);
        }
        if (Arr::get($scheduleConfig, 'prior_notification')) {
            $priorCheck = Arr::get($scheduleConfig, 'prior_check');
            $data = DB::table($table)
                ->where($updateFields, '!=', $updateValue)
                ->whereRaw($whereCnd)
                ->get();
            if (empty($data)) {
                return false;
            }
            $priorSubject = Arr::get($scheduleConfig, 'prior_notification_subject');
            $priorMessage = Arr::get($scheduleConfig, 'prior_notification_message');
            foreach ($data as $row) {
                $replaceContent = SELF::getReplaceContent($priorMessage, $row);
                $messageContent = ($replaceContent) ?: $priorMessage;
                \EmailHandler::send($messageContent, $priorSubject, $recieverEmails);
            }
        } else {
            $data = DB::table($table)
                ->whereRaw($updateFields.' != '.$updateValue.' or '.$updateFields .' IS NULL')
                ->whereRaw($whereCnd)
                ->get();
            if (!empty($data)) {
                $ids = $data->pluck('id');
                $affectedRows = DB::table($table)
                    ->whereIn('id', $ids)
                    ->update([$updateFields => $updateValue]);
                $replaceContent = SELF::getReplaceContent($emailMessage, $data);
                $messageContent = ($replaceContent) ?: $emailMessage;
                \EmailHandler::send($messageContent, $emailSubject, $recieverEmails);
            }
        }
    }


    public static function sendCustomEmailConfig($header, $msg, $data,$emailIds)
    {
        $replaceContent = SELF::getReplaceContent($msg, $data);
        if(is_array($emailIds)&& count($emailIds)>0)
        {
            foreach($emailIds as $email)
            {
                \EmailHandler::send($replaceContent, $header, $email);
            }
        }else{
            \EmailHandler::send($replaceContent, $header, $emailIds);
          }
    }
    public static function customEmailIdReceiver($data)
    {
       $emailids = [];
        if($data) {
            foreach($data as  $row) {
                if(isset($row->email))
                {
                  $emailids[] = $row->email;
                }
            }
        }
        return $emailids;
    }

    public static function sendEmailConfig($moduleName, $emailConfigs, $data)
    {
        if (!$moduleName && !$emailConfigs) {
            return false;
        }
        $crud = DB::table('cruds')->where('module_name', $moduleName)->first();
        $table = ($crud) ? $crud->module_db:"";
        if ($crud && $crud->parent_id) {
            $parentModule = DB::table('cruds')->where('id', $crud->parent_id)->first();
            if (!empty($parentModule)) {
                $table = $crud->module_db;
            }
        }
        $emailConfigs = json_decode($emailConfigs, true);
        $emailSubject = Arr::get($emailConfigs, 'subject');
        $emailMessage = Arr::get($emailConfigs, 'message');
        $defaultRecievers = Arr::get($emailConfigs, 'default_reciever');
        $defaultRecieverEmails = getEmailIdsfromRoles($defaultRecievers,$data);
        $sendTo = Arr::get($emailConfigs, 'send_to');
        $reciever_field = Arr::get($emailConfigs, 'reciever_field');
        $reciever_role = Arr::get($emailConfigs, 'reciever_role');
        $recieverEmails = $emailIds = $username = [];$userId ="";
        $dataAttributes = $data->getAttributes();
        if ($sendTo == 'roles') {
            $recieverEmails = getEmailIdsfromRoles($reciever_role,$data);
        } else {
            if(Str::contains($reciever_field,"|")){
                $fields = explode("|",$reciever_field);
                foreach($fields as $field){
                     $multiEmails[] = (isset($data->{$field})) ? $data->{$field} : Arr::get($dataAttributes,$field);
                }
                foreach($multiEmails as $email){
                    if (preg_match("/(.+)@(.+)\.(.+)/i", $email)) {
                        $emailIds[] = $email;
                        $username[$email] = (isset($data->username)) ? $data->username:strstr($userId,'@',true);
                    }
                }

            }else{
                $userId = $data->{$reciever_field};
            }
            $recieverEmails = ($userId) ? getEmailIdsfromUsers($userId) : "";
            if (preg_match("/(.+)@(.+)\.(.+)/i", $userId)) {
                $emailIds[] = $userId;
                if(isset($data->username)) {
                    $username[$userId] = $data->username;
                } else {
                    $username[$userId] = strstr($userId,'@',true);
                }
            }
        }
        if(!empty($recieverEmails)){
            $email = (isset($recieverEmails->email)) ? $recieverEmails->email :$recieverEmails;
            if(is_array($email)){
                foreach($email as $key => $value){
                    $emailIds[] = $value;
                    $username[$value] = $key;
                }
            }else{
                $emailIds[] = $email;
                $username[$email] = (isset($recieverEmails->username))?$recieverEmails->username:strstr($email,'@',true);
            }

        }
        if(!empty($defaultRecieverEmails)){
            $emailIds = array_merge($emailIds,$defaultRecieverEmails);
        }
        
        foreach($emailIds as $emailId){
            $userName = (\Arr::get($username,$emailId))?:"";
            $replaceContent = SELF::getReplaceContent($emailMessage, $data,$userName);
            $messageContent = ($replaceContent) ?: $emailMessage;
            $args['attachments'] = (isset($data->attachments) && $data->attachments) ? url('/storage/').'/'.$data->attachments:"";
            \EmailHandler::send($messageContent, $emailSubject, $emailId,$args);
        }
    }

    public static function getReplaceContent($emailMessage, $data,$username=null)
    {
        $matches = [];
        preg_match_all('/{[a-zA-Z_]*?}/', $emailMessage, $matches);
        $strEmails = $matches[0];
        if (empty($strEmails)) {
            return $emailMessage;
        }
        $search = $strEmails;
        $replace = [];
        if($username){
            $emailMessage = str_replace('{receiver_name}', $username, $emailMessage);
        }

        if(in_array('{sender_name}',$search)){
            $emailMessage = str_replace('{sender_name}', Auth::user()->name, $emailMessage);
        }
        $pathInfo = \Request::getPathInfo();
        $action = (str_contains($pathInfo, 'edit')) ? 'Updated By' : 'Created By';
        $dataAttributes = $data->getAttributes();
        foreach ($search as $row) {
            $field = substr($row, 1, -1);
            if ($field == 'action') {
                $replace[] = $action;
            } else if ($field == 'user') {
                $replace[] = Auth::user()->name;
            } else {
                if(isset($data->$field)){
                    $value = $data->$field;
                    if(in_array($field,['created_at','updated_at'])){
                        $value = \BaseHelper::formatDate($data->$field);
                    }
                }else if(method_exists($data,'join_fields') && isset($data->join_fields()->$field)){
                    $value = $data->join_fields()->$field;
                }else if(!empty($dataAttributes) && \Arr::get($dataAttributes,$field)){
                    $value = $dataAttributes[$field];
                }else{
                    $value = "";
                }
                $replace[] = $value;
            }
        }
        return str_replace($search, $replace, $emailMessage);
    }



    public static function callSchedulerCommandClass($moduleName)
    {
        $scheduleCall = "";
        if (!$moduleName) {
            return $scheduleCall;
        }
        $crud = DB::table('cruds')->where('module_name',$moduleName)->first();
        if(empty($crud)){
            return $scheduleCall;
        }
        $conds = ['id'=>$crud->id];
        $modules = DB::table('cruds')->where($conds)->whereOr('parent_id',$crud->id)->get();
        $crudModule = ucfirst(Str::camel($moduleName));
        $name = ucfirst(Str::camel($moduleName));
        if(!empty($modules)){
            foreach($modules as $module){
               $name = ucfirst(Str::camel($module->module_name));
               $moduleConfig = CF_decode_json($module->module_config);
               if(Arr::get($moduleConfig,'scheduler')){
                    $scheduleCall.= "\$schedule->command(\Impiger\\".$crudModule."\\Commands\\".$name."SchedulerCommand::class)->dailyAt(".SCHEDULER_DAILY_TIME.");\n\t";
               }

            }
        }
        return $scheduleCall;
    }

    public static function getSchedulerCommandClass($moduleName)
    {
        $schedulerCommands = [];
        if (!$moduleName) {
            return $schedulerCommands;
        }
        $crud = DB::table('cruds')->where('module_name',$moduleName)->first();
        if(empty($crud)){
            return $schedulerCommands;
        }
        $conds = ['id'=>$crud->id];
        $modules = DB::table('cruds')->where($conds)->whereOr('parent_id',$crud->id)->get();
        $crudModule = ucfirst(Str::camel($moduleName));
        $name = ucfirst(Str::camel($moduleName));
        if(!empty($modules)){
            foreach($modules as $module){
               $name = ucfirst(Str::camel($module->module_name));
               $moduleConfig = CF_decode_json($module->module_config);
               if(Arr::get($moduleConfig,'scheduler')){
                    $schedulerCommands[] = "\Impiger\\".$crudModule."\\Commands\\".$name."SchedulerCommand";
               }
            }
        }
        return $schedulerCommands;
    }

    public static function isDependentDataExist($moduleName, $ids, $modelCls)
    {
        $dataExist = false;

        if (!$moduleName) {
            return $dataExist;
        }

        $crud = DB::table('cruds')->where('module_name', $moduleName)->select(['id', 'dependent_module','module_db'])->first();
        if($crud && $modelCls == config('general.restrict_delete_financial_years')){
            foreach ($ids as $id) {
                $query = DB::table($crud->module_db)->where(['id'=>$id,'is_running'=>1]);
                $isRunning = $query->pluck('id')->first();
                if($isRunning){
                    $msg = trans('crud.delete_financial_year_msg');
                    return $msg;
                }
            }
        }
        if ($crud && $crud->dependent_module && Arr::has($crud->dependent_module, 0)) {
            foreach ($ids as $id) {

                foreach ($crud->dependent_module as $dep) {
                    if (Arr::get($dep, 'table_name') && Schema::hasTable($dep['table_name']) && Schema::hasColumn(Arr::get($dep, 'table_name'), Arr::get($dep, 'dependent_key'))) {
                        $query = DB::table($dep['table_name'])->select('id')->where([$dep['dependent_key'] => $id]);
                        if (Arr::get($dep, 'entity_key') && Schema::hasColumn($dep['table_name'], $dep['entity_key'])) {
                            $query->where([$dep['entity_key'] => $modelCls]);
                        }

                        if (Schema::hasColumn($dep['table_name'], 'deleted_at')) {
                            $query->whereNull('deleted_at');
                        }

                        $dataExist = $query->pluck('id')->first();

                        if ($dataExist) {
                            $search = array('{module}', '{dependent_module}');
                            $replace = array(ucfirst(str_replace('-', ' ', $moduleName)), Arr::get($dep, 'dependent_module'));
                            $msg = str_replace($search, $replace, trans('core/base::notices.delete_dependent_msg'));
                            return $msg;
                        }
                    }
                }
            }
        }

        return $dataExist;
    }

    public static function isDependentDataExistCore($moduleName, $dependentModule, $ids) {
        $dataExist = false;

        foreach ($ids as $id) {

            foreach ($dependentModule as $dep) {
                if (Arr::get($dep, 'table_name') && Schema::hasTable($dep['table_name']) && Schema::hasColumn(Arr::get($dep, 'table_name'), Arr::get($dep, 'dependent_key'))) {
                    $query = DB::table($dep['table_name'])->select('id')->where([$dep['dependent_key'] => $id]);


                    if (Schema::hasColumn($dep['table_name'], 'deleted_at')) {
                        $query->whereNull('deleted_at');
                    }

                    $dataExist = $query->pluck('id')->first();

                    if ($dataExist) {
                        $search = array('{module}', '{dependent_module}');
                        $replace = array(ucfirst(str_replace('-', ' ', $moduleName)), Arr::get($dep, 'dependent_module'));
                        $msg = str_replace($search, $replace, trans('core/base::notices.delete_dependent_msg'));
                        return $msg;
                    }
                }
            }
        }

        return $dataExist;
    }


    public static function mappingDomain($request,$moduleRepository){
        if(is_plugin_active('institution') && $request->has('expiry_date')){
            $moduleRepository->is_license_expired = 0;

            $moduleRepository->save();
        }
        if(!$request->has('domain_id')){
            return false;
        }
        $multiDomainData =[];
        $multiDomainData['domain_id'] = $request->input('domain_id');
        $multiDomainData['reference_id'] = $moduleRepository->id;
        $multiDomainData['reference_type'] = get_class($moduleRepository);
        $metaDataExists = DB::table('multidomain_meta')->where(['reference_id' => $moduleRepository->id,'reference_type'=>get_class($moduleRepository)])->first();
        if(!empty($metaDataExists)){
            $multiDomainData['updated_at'] = date("Y-m-d H:i:s");
            DB::table('multidomain_meta')->where('mult_meta_id',$metaDataExists->mult_meta_id)->update($multiDomainData);
        }else{
            $multiDomainData['created_at'] = date("Y-m-d H:i:s");
            DB::table('multidomain_meta')->insert($multiDomainData);
        }
    }

    public static function getshortcodeChoises($data){
        $choices =[];
        if(!empty($data)){
            foreach($data as $item){
                $choices[$item] = $item;
            }
        }
        return $choices;
    }

    public static function getSubformData($data,$subFormKey,$hasMany=true){
        $results = [];
        if(!empty($data)){
            if($hasMany){
                foreach($data as $index => $formData){
                    foreach($formData->getOriginal() as $key => $value){
                        $results[$subFormKey.'['.$index.']['.$key.']'] = $value;
                    }
                }
            }else{
                foreach($data->getOriginal() as $key => $value){
                        $results[$subFormKey.'['.$key.']'] = $value;
                    }
            }
        }
        return $results;
    }

    public static function declareShortCode($obj, $module) {
        $result = DB::table('cruds')->select(['id','module_name','module_alias','module_config','is_shortcode_form','is_shortcode_table'])
        ->where(function($query) use($module) {
            $query->where('module_name', $module)
            ->orWhereRaw('parent_id IN (SELECT id FROM cruds WHERE module_name="'.$module.'")');
        })
        ->where(function($query) {
            $query->where('is_shortcode_form', 1)
            ->orWhere('is_shortcode_table', 1);
        })
        ->get();

        foreach($result as $row) {
            $name = ($row->module_alias) ? :$row->module_name;
            $moduleLower = strtolower($name);
            $moduleUpper = ucfirst(Str::camel($name));
            $obj->module = $row->module_name;
            $obj->parentModule = ucfirst(Str::camel($module));

            if($row->is_shortcode_form == 1) {
                $shortCodeFormSc = $moduleLower.'-form-sc';
                $obj->shortcodeMap[$shortCodeFormSc] =  [
                    'module' => $row->module_name,
                    'parent_module' => $module
                ];
                add_shortcode($shortCodeFormSc, $moduleUpper.' Form', 'Add '.$moduleUpper.' Form', [$obj, 'buildFormUsingShortcode']);
            }

            if($row->is_shortcode_table == 1) {
                $shortCodeListSc = $moduleLower.'-list-sc';
                $obj->shortcodeMap[$shortCodeListSc] =  [
                    'module' => $row->module_name,
                    'parent_module' => $module
                ];
                add_shortcode($shortCodeListSc, $moduleUpper.' Table', 'List '.$moduleUpper, [$obj, 'buildTableUsingShortcode']);
            }
        }

    }

    public static function loadTableAssets()
    {
                Theme::asset()
                    ->usePath(false)
                    ->add('crud-table0-css', asset('/vendor/core/core/base/libraries/font-awesome/css/fontawesome.min.css'), [], [], '1.0.0')
                    ->add('crud-table1-css', asset('vendor/core/core/base/libraries/datatables/media/css/dataTables.bootstrap.min.css'), [], [], '1.0.0')
                    ->add('crud-table2-css', asset('vendor/core/core/base/libraries/datatables/extensions/Buttons/css/buttons.bootstrap.min.css'), [], [], '1.0.0')
                    ->add('crud-table3-css', asset('vendor/core/core/base/libraries/datatables/extensions/Responsive/css/responsive.bootstrap.min.css'), [], [], '1.0.0')
                    ->add('crud-table4-css', asset('vendor/core/core/table/css/table.css'), [], [], '1.0.0')
                    ->add('crud-table5-css', asset('vendor/core/plugins/crud/css/module_custom_styles.css'), [], [], '1.0.0');
                Theme::asset()->container('footer')->writeScript('customScript', "var ImpigerVariables = {};
                                                    ImpigerVariables.languages = {};
                                                    ImpigerVariables.languages.tables = {export:'Export',csv:'csv',print:'print',reset:'reset',reload:'reload',excel:'excel'};");
                Theme::asset()
                    ->container('footer')
                    ->usePath(false)
                    ->add('shortcode-table1-js', asset('vendor/core/core/base/libraries/datatables/media/js/jquery.dataTables.min.js'), ['jquery'], [], '1.0.0')
                    ->add('crud-table2-js', asset('vendor/core/core/base/libraries/datatables/media/js/dataTables.bootstrap.min.js'), ['jquery'], [], '1.0.0')
                    ->add('crud-table3-js', asset('vendor/core/core/base/libraries/datatables/extensions/Buttons/js/dataTables.buttons.min.js'), ['jquery'], [], '1.0.0')
                    ->add('crud-table4-js', asset('vendor/core/core/base/libraries/datatables/extensions/Buttons/js/buttons.colVis.min.js'), ['jquery'], [], '1.0.0')
                    ->add('crud-table5-js', asset('vendor/core/core/base/libraries/datatables/extensions/Buttons/js/buttons.bootstrap.min.js'), ['jquery'], [], '1.0.0')
                    ->add('crud-table6-js', asset('vendor/core/core/base/libraries/datatables/extensions/Responsive/js/dataTables.responsive.min.js'), ['jquery'], [], '1.0.0')
                    ->add('crud-table7-js', asset('vendor/core/core/table/js/table.js'), ['jquery'], [], '1.0.0')
                    ->add('crud-table8-js', asset('vendor/core/core/table/js/filter.js'), ['jquery'], [], '1.0.0')
                    ->add('crud-table10-js', asset('vendor/core/plugins/crud/js/custom_encryption.js'), ['jquery'], [], '1.0.0');
    }

    public static function getCrudModuleSlugUsingShortcode($shortCode, $shortCodeMap) {
        $module = \Arr::get($shortCodeMap, $shortCode->getName().'.module');
        $parent = \Arr::get($shortCodeMap, $shortCode->getName().'.parent_module');
        if(!$module || !$parent) {
            return false;
        }

        $slug = [];
        $slug['moduleLower'] = strtolower($module);
        $slug['moduleUpper'] = ucfirst(Str::camel($module));
        $slug['parentModule'] = ucfirst(Str::camel($parent));
        return $slug;
    }

    public static function parseJoinQuery($joins, $innerModel) {
        if($joins) {
            $joinsArr = explode("\n", $joins);
            $joinMap = ['left' => 'leftJoin', 'inner' => 'join', 'right' => 'rightJoin', 'join' => 'join' ];

            if(Arr::has($joinsArr, 0)) {
                foreach($joinsArr as $join) {
                    list($table, $joinType, $on) = explode("|", $join);
                    $joinType = Arr::get($joinMap, $joinType);
                    $innerModel = $innerModel->$joinType($table, function($join) use($on) {
                        $onSplit = preg_split('/(=|!=|<|<=|>|>=|<>)/', $on);
                        if(Arr::has($onSplit, 1)) {
                            $operator =  Str::between($on, $onSplit[0], $onSplit[1]);
                            $join->on(trim($onSplit[0]), trim($operator), trim($onSplit[1]));
                        }
                    });
                }
            }
        }

        return $innerModel;
    }

    public static function getDashboardStatsData($repository, $fields, $joins, $cndns, $groupBy, $returnData = false, $optType = 'CNT', $workflowMeta=null) {
        $model = $repository->getModel();
        $innerModel = $model;
        $innerModel = SELF::parseJoinQuery($joins, $innerModel);
        $query = ($groupBy) ? $innerModel->groupBy($groupBy) : $innerModel;
        $query = apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, [], false);

        $query = ($cndns) ? $query->whereRaw($cndns) : $query;
        if($workflowMeta){
            $workflowMetaData = \Impiger\Workflows\Models\WorkflowMetaData::where('id',$workflowMeta)->first();
            if($workflowMetaData){
                $metaData = $workflowMetaData->meta_data;
                $workflow = $workflowMetaData->workflows;
                if(is_array($metaData)){
                    $query = $query->whereIn($workflow->module_property,$metaData);
                }else{
                    $query = $query->where($workflow->module_property,$metaData);
                }
            }
        }
        if($optType && $optType != 'CNT') {
            $fieldArr = explode(",", $fields);
            if(isValidArray($fieldArr) && count($fieldArr) == 1) {
                $fieldStr = $optType.'('.$fields.') as total_stats_cnt';
                return $query->selectRaw($fieldStr)->get()->pluck('total_stats_cnt')->first();
            }
        }
        $fieldArr = ($fields && $returnData) ? explode(",", $fields) : $repository->getTable(). ".id";
        $query = $query->select($fieldArr);

        return  ($returnData) ? $query->get() : $query->get()->count();
    }

    public static function addCrudStatsWidget($module, $widgets, $widgetSettings,$extraPermissions=null)
    {
        $user = Auth::user();
        $customView = setting('custom_stats_view');
//        if(isVendorUser()){
//            return $widgets;
//        }
        if (!$user->hasPermission($module.'.index') && ($extraPermissions && !$user->hasPermission($extraPermissions))) {
            return $widgets;
        }
        $result = DB::table('cruds')->select(['id','module_name','module_config', 'parent_id'])
        ->where('is_stats',1)
        ->where(function($query) use($module) {
            $query->where('module_name', $module)
            ->orWhereRaw('parent_id IN (SELECT id FROM cruds WHERE module_name="'.$module.'")');
        })->get();
        // $allowedWidgets = getAllowedDashboardWidgets();

        if(Arr::has($result, 0)) {
            $user = Auth::user();
            foreach($result as $row) {
                $subModuleName = ($row->parent_id) ? $row->module_name : $module;
                $moduleLower = strtolower($subModuleName);
                $permissions = (!$user->hasPermission($moduleLower.'.index') && $extraPermissions) ? $extraPermissions :$moduleLower.'.index';
                if ($user->hasPermission($permissions)) {
                $parentModuleUpper = ucfirst(Str::camel($module));
                $moduleUpper = ucfirst(Str::camel($subModuleName));
                $config = CF_decode_json($row->module_config);
                $stats = (isset($config['stats']) ? $config['stats'] : array());
                $statsMap = [];

                foreach($stats as $k => $val) {
                    $showBackend = (Arr::get($val, 'show_backend') || (Arr::has($val, 'show_backend') === false && Arr::has($val, 'show_frontend')  === false) ) ? true : false;
                        if($showBackend && $val['is_sub_stats']) {
                            if(!isset($statsMap[$val['parent_stats_id']])) {
                                $statsMap[$val['parent_stats_id']] = [];
                            }
                            $val['cnt'] = 0;
                            // if((!$allowedWidgets) || in_array($val['parent_stats_id'], $allowedWidgets)) {
                                $repository = app()->make('Impiger\\'.$parentModuleUpper.'\Repositories\Interfaces\\'.$moduleUpper.'Interface');
                                $optType = (Arr::has($val,'operation_type')) ? Arr::get($val,'operation_type') : 'CNT';
                                $val['cnt'] = SELF::getDashboardStatsData($repository, Arr::get($val,'field'), Arr::get($val,'sql_join'), Arr::get($val,'sql_cndn'),Arr::get($val,'sql_group_by'), false, $optType, Arr::get($val,'workflow_meta'));
                            // }
                            $statsMap[$val['parent_stats_id']][] = $val;
                            unset($stats[$k]);

                    }
                }

                foreach($stats as $val) {
                    $cnt = 0;
                    $showBackend = (Arr::get($val, 'show_backend') || (Arr::has($val, 'show_backend') === false && Arr::has($val, 'show_frontend')  === false) ) ? true : false;

                    if($showBackend) {

                        if(in_array('stats', Arr::get($val, 'stats_type'))) {
                            $repository = app()->make('Impiger\\'.$parentModuleUpper.'\Repositories\Interfaces\\'.$moduleUpper.'Interface');
                            $optType = (Arr::has($val,'operation_type')) ? Arr::get($val,'operation_type') : 'CNT';
                            $cnt = SELF::getDashboardStatsData($repository, Arr::get($val,'field'), Arr::get($val,'sql_join'), Arr::get($val,'sql_cndn'),Arr::get($val,'sql_group_by'), false, $optType, Arr::get($val,'workflow_meta'));
                        }
                        $color = (Arr::get($val, 'color')) ? Arr::get($val, 'color') : "#32c5d2";
                        $statsType = (Arr::get($val, 'stats_type.0')) ? Arr::get($val, 'stats_type') : [];
                        $val['icon'] = ($val['icon']) ? $val['icon'] : "";
                        $route = (Arr::get($val, 'route')) ? Arr::get($val, 'route')  : "";
                        foreach($statsType as $type) {
                            $widget = (new \App\Utils\Supports\CrudDashboardWidgetInstance)
                            ->setPermission($permissions)
                            ->setTitle($val['title'])
                            ->setKey($val['slug'])
                            ->setIcon($val['icon'])
                            ->setColor($color)
                            ->setStatsConfig($val)
                            ->setStatsDisplayType($type)
                            ->setModule($module)
                            ->setSubModule($subModuleName);
                            if($route && $type != 'stats'){
                                $widget = $widget->setRoute(route($route));
                            }


                            if($type == 'stats') {
                                $widget = $widget->setType('stats')
                                ->setStatsOrder($val['order'])
                                ->setStatsTotal($cnt);
                                if($customView){
                                    $widget = $widget->setCustomView(true)
                                              ->setRoute($route);
                                }
                            } elseif(in_array($type, ['pie', 'bar'])) {
                                $widget = $widget->setKey($type."-".$val['slug'])->setColumn('col-md-6 col-sm-6')
                                ->setStatsOrder($val['order'])
                                ->setBodyClass('scroll-table')
                                ->setTitle($val['title']." - ".Str::ucfirst($type). " Chart");
                            }elseif($type=='table'){
                                $widget = $widget->setKey($type."-".$val['slug'])->setColumn('col-md-12 col-sm-12')
                                ->setBodyClass('scroll-table');
                            }

                            if(Arr::has($statsMap, $val['slug'])) {
                                $widgets = $widget->setHasSubStats(true)
                                ->setSubStats($statsMap[$val['slug']])
                                ->init($widgets, $widgetSettings);
                            } else {
                                $widgets = $widget
                                ->init($widgets, $widgetSettings);
                            }
                        }
                    }

                }
                }
               }
            }

        return $widgets;
    }

    /**
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws Throwable
     */
    public function getDashboardWidgetContent(Request $request, BaseHttpResponse $response)
    {
        $parentModuleUpper = ucfirst(Str::camel($request->input('module')));
        $moduleUpper = ucfirst(Str::camel($request->input('subModule')));
        $data = [];
        $repository = app()->make('Impiger\\'.$parentModuleUpper.'\Repositories\Interfaces\\'.$moduleUpper.'Interface');
        $statsConfig = ($request->has('statsConfig')) ? $request->input('statsConfig') : [];
        $fields = Arr::get($statsConfig, 'field');
        $data = SELF::getDashboardStatsData($repository, $fields, Arr::get($statsConfig,'sql_join'), Arr::get($statsConfig,'sql_cndn'),Arr::get($statsConfig,'sql_group_by'), true,'CNT' ,Arr::get($statsConfig,'workflow_meta'));;
        $fields = ($fields) ? explode(",", $fields) : $fields ;
        return $response
            ->setData(view('widgets.custom-table', compact('data', 'fields'))->render());
    }

    public static function renderWebsiteStats()
    {
        $result = DB::table('cruds')->select(['id','module_name','module_config', 'parent_id'])
        ->where('is_stats',1)->get();
        $statsList = $result->pluck('module_name','id')->toArray();
        $widgets = [];

        if(Arr::has($result, 0)) {
            foreach($result as $row) {
                $module = ($row->parent_id) ? Arr::get($statsList, $row->parent_id) : $row->module_name;
                $subModuleName = ($row->parent_id) ? $row->module_name : $module;
                $moduleLower = strtolower($subModuleName);
                $parentModuleUpper = ucfirst(Str::camel($module));
                $moduleUpper = ucfirst(Str::camel($subModuleName));
                $config = CF_decode_json($row->module_config);
                $stats = (isset($config['stats']) ? $config['stats'] : array());
                $statsMap = [];

                foreach($stats as $val) {
                    $cnt = 0;
                    $val['stats_type'] = (Arr::has($val, 'stats_type.0')) ? Arr::get($val, 'stats_type') : ['stats'];
                    if(Arr::get($val, 'show_frontend') && in_array('stats', Arr::get($val, 'stats_type'))) {
                        $repository = app()->make('Impiger\\'.$parentModuleUpper.'\Repositories\Interfaces\\'.$moduleUpper.'Interface');
                        $optType = (Arr::has($val,'operation_type')) ? Arr::get($val,'operation_type') : 'CNT';
                        $cnt = SELF::getDashboardStatsData($repository, Arr::get($val,'field'), Arr::get($val,'sql_join'), Arr::get($val,'sql_cndn'),Arr::get($val,'sql_group_by'), false, $optType, Arr::get($val,'workflow_meta'));
                        $color = (Arr::get($val, 'color')) ? Arr::get($val, 'color') : "#32c5d2";
                        $val['icon'] = ($val['icon']) ? $val['icon'] : "";
                        $route = (Arr::get($val, 'route')) ? Arr::get($val, 'route')  : $moduleLower.'.index';

                        $widget = (object) [];
                        $widget->name  = $val['title'];
                        $widget->title  = $widget->name;
                        $widget->key = $val['slug'];
                        $widget->icon = $val['icon'];
                        $widget->color = $color;
                        $widget->statsConfig = $val;
                        $widget->statsDisplayType = 'stats';
                        $widget->module = $module;
                        $widget->subModule = $subModuleName;
                        $widget->type = 'stats';
                        $widget->route = null;
                        $widget->statsTotal = $cnt;
                        $widget->hasSubStats = false;
                        $widget->subStats = null;
                        $widget->cnt = $cnt;

                        if(Arr::has($statsMap, $val['slug'])) {
                            $widget->hasSubStats = true;
                            $widget->subStats = $statsMap[$val['slug']];
                        }
                        $widgets[] = (new \App\Utils\Supports\CrudDashboardWidgetInstance)->renderDashboardWidget($widget);
                    }
                }
               }
            }

        return view('widgets.website-stats-list',compact('widgets'));
    }

    public static function setSubscriptionRowColor($item, $module) {
        $user = Auth::user();
        if($user && $user->hasPermission($module.'.subscribe') && $user->applyDataLevelSecurity()) {
            $rowCls = 'alert-default';

            if(intval($item->total_subscribed) > 0) {
                $rowCls = 'alert-subscribed';
            } else if(intval($item->total_approved) > 0) {
                $rowCls = 'alert-approved';
            }
            return $rowCls;
        }

        return false;
    }

    public static function customValidationRules($rules, $model)
    {
        if (!$rules) {
            return "";
        }
        $result = [];
        $table = "";
        $rulesSplit = explode("|", $rules);

        foreach ($rulesSplit as $rule) {
            if (Str::startsWith($rule, "unique:")) {
                $slice = Str::after($rule, "unique:");
                $columns = explode(",", $slice);
                if (Arr::has($columns, 0)) {
                    $table = $columns[0];
                    array_shift($columns);
                    $result[] = \Illuminate\Validation\Rule::unique($table)
                        ->ignore($model->id)
                        ->where(function ($whr) use ($model, $columns) {
                            foreach ($columns as $column) {
                                $whr->where($column, "=", $model->$column);
                            }
                        });
                }
            } else {
                $result[] = $rule;
            }
        }
        return $result;
    }


    /**
     * {@inheritDoc}
     */
    public static function getBulkChanges($module, $isFilter = false, $isSubscribe = false): array
    {
        $row = \DB::table('cruds')->where('module_name', $module)->get()->first();
        $moduleConfig = CF_decode_json($row->module_config);
        $columns = [];
        $bulkChangesType = ['text', 'select', 'text_datetime', 'select-search', 'number', 'date', 'text_date', 'textarea', 'radio', 'hidden'];
        $gridConfig = [];$excludeColumns = ['id'];
        if($module == 'student') {
            $subForms = \DB::table('cruds')->where('parent_id', $row->id)->get();
        }

        foreach($moduleConfig['grid'] as $grid)
            $gridConfig[$grid['field']] = $grid;

            $columns = SELF::getBulkChangesColumn($moduleConfig,$gridConfig,$bulkChangesType,$excludeColumns,$isFilter);
            if($module == 'student' && $subForms){
                $excludeColumns = array_merge($excludeColumns,['created_at','updated_at']);
                foreach($subForms as $subForm){
                    $subModuleConfig = CF_decode_json($subForm->module_config);
                    if($isFilter && $subForm->module_name == 'academic-info'){
                        $subColumns = SELF::getBulkChangesColumn($subModuleConfig,$gridConfig,$bulkChangesType,$excludeColumns,$isFilter);
                        $columns =array_merge($columns,$subColumns);
                    }
                }
            }

            if($isFilter) {
                if(in_array($row->module_db, config('general.entity_filter_supported_models'))) {
                    $roles = app(\Impiger\ACL\Roles::class)->getChildRolesUsingLogin(false, true, true);
                    $roles = ($roles) ? $roles->pluck('name', 'id') : [];
                    $customColumns[$row->module_db.'.entity_type'] = [
                        'title'    => 'Entity Type',
                        'type'     => 'select',
                        'choices'  => CrudHelper::getSelectBoxChoices(['field' => 'entity_type'])
                    ];

                    $customColumns[$row->module_db.'.entity_id'] = [
                        'title'    => 'Entity',
                        'type'     => 'select',
                        "callback" => "getEntityFilter"
                    ];

                    $customColumns[$row->module_db.'.role'] = [
                        'title'    => 'Role',
                        'type'     => 'select',
                        'choices' => $roles
                    ];

                    $columns = $customColumns + $columns;
                }

                if ($isSubscribe) {
                    $user = Auth::user();
                    $subscritionModule = $module;
                    if(in_array($module, config('general.tp_subscription_module'))) {
                        $subscritionModule = 'training-program';
                    }
                    if ($user && $user->applyDataLevelSecurity() && $user->hasPermission($subscritionModule.'.subscribe')) {
                        $customColumns['my_subscribtion'] = [
                            'title'    => 'My Subscription',
                            'type'     => 'select',
                            'choices'  => [1 => 'Yes', 0 => 'No']
                        ];

                        $columns = $customColumns + $columns;
                    }
                }
            }

        return $columns;
    }

    public static function getBulkChangesColumn($moduleConfig,$gridConfig,$bulkChangesType,$excludeColumns = null,$isFilter=false){
        $columns=[];
        foreach ($moduleConfig['forms'] as $val) {
            $gConfig = (isset($gridConfig[$val['field']])) ? $gridConfig[$val['field']] : [];
            $isAllowedType = (Arr::get($gConfig, 'view') && in_array($val['type'], $bulkChangesType)) ? true : false;
            if (
                (!$isFilter && $isAllowedType && Arr::get($val, 'bulk_edit')) ||
                ($isFilter && $val['search'] && $isAllowedType)
             ) {
                $config = [
                    'title' => $val['label'],
                    'type' => $val['type'],
                    'validate' => $val['required'],
                    'meta' => $val['option']
                ];

                if($val['field']=='is_enabled')
                {
                    $val['type']='radio';
                    $val['option']["lookup_query"] = "1:Active|0:In-Active";
                    $val['option']["opt_type"] = "datalist";
                }

                if (in_array($val['type'], ['select','radio','entity']) || $val['field'] == 'entity_type') {
                   if($val['field'] == 'entity_id') {
                    $config['callback'] = 'getEntityFilter';
                   } else {
                       if(Arr::get($val, 'option.opt_type') == 'customFunction') {
                        $config['type'] = 'select';
                       }
                    $config['choices'] = CrudHelper::getSelectBoxChoices($val);
                   }
                    $config['type'] = 'select';
                } else if ($val['type'] == 'text_datetime' || $val['type'] == 'text_date') {
                    $config['type'] = 'date';
                }
                if(!empty($excludeColumns)){
                    if(!in_array($val['field'],$excludeColumns))
                            $columns[$val['alias'] . "." . $val['field']] = $config;
                }else{
                    $columns[$val['alias'] . "." . $val['field']] = $config;
                }
            }
        }

        return $columns;
    }

    public static function getEntityFilterChoices($filters, $request) {
        $column = Arr::get($filters, $request->input('key'));
        $filterData = $request->input('filterData');
        return SELF::loadEntityDetails($filterData,$column);
    }

    public static function getcaptchavalidation($rules)
    {
            if(setting('enable_custom_captcha'))
            {
                $captchaFieldTemplate = ['customcaptcha' => 'sometimes|required|customcaptcha'];

            }
            elseif (setting('enable_captcha') && is_plugin_active('captcha'))
            {
                $captchaFieldTemplate = ['g-recaptcha-response' => 'sometimes|required|captcha'];

            }
            else{
                $captchaFieldTemplate=[];
            }
        $rules=array_merge($rules,$captchaFieldTemplate);
        return $rules;
    }

    public static function filterByArrayValue($inputArray, $filterKey) {
        $output = [];
        $filtered = Arr::where($inputArray, function ($value, $key) use($filterKey) {
            return str_ends_with($value['column'], $filterKey);
        });

        if(count($filtered) > 0) {
            $value = array_values($filtered);
            $output = $value[0];
        }

        return $output;
    }

    public static function arrayInsertAfterKey($key, array &$array, $newKey, $newValue){
        if (array_key_exists($key, $array)) {
            $new = array();
            foreach ($array as $k => $value) {
              $new[$k] = $value;
              if ($k === $key) {
                $new[$newKey] = $newValue;
              }
            }
            return $new;
          }
          return FALSE;
    }

    public static function applyFilterCondition($repository, $query, string $key, string $operator, ?string $value) {
        $dbKey = $key;
        $table = $repository->getTable();
        if (strpos($key, '.') !== -1) {
            $key = Arr::last(explode('.', $key));
        } else {
            $dbKey = $table . '.' .$key;
        }

        switch ($key) {
            case 'created_at':
            case 'updated_at':
                if (!$value) {
                    break;
                }

                $value = \Carbon\Carbon::createFromFormat(config('core.base.general.date_format.mysql_date'), $value)->toDateString();
                $query = $query->whereDate($dbKey, $operator, $value);
                break;
            case 'my_subscribtion':
                if ($value == "") {
                    break;
                }

                $query = $query->havingRaw('my_subscribtion =' . $value);
                break;
            default:
            /* @Customized By Ramesh Esakki */
                if (is_null($value)) {
                    break;
                }

                if(in_array($table, config('general.entity_filter_supported_models')) ||
                (Str::contains($value, "|") || Str::endsWith('entity_type', $dbKey))) {

                    if(Schema::hasColumn($table, 'entity_type') || Schema::hasColumn($table, 'entity_id')) {
                        if(Str::contains($value, "|")) {
                            list($value, $entityId) = explode("|", $value);
                            $query = $query->where($table.".entity_type", $operator, $entityId)
                            ->where($table.".entity_id", $operator, $value);
                        } elseif(Str::endsWith('entity_type', $dbKey)) {
                            $query = $query->where($table.".entity_id", $operator, $value);
                        }
                    } else {
                        if(!joinTableExists($query, 'user_permissions', 'UP')) {
                            $query = $query->leftjoin('user_permissions AS UP', 'UP.user_id', $table . '.user_id')
                            ->where('UP.is_retired', 0);
                        }
                        if(Str::contains($value, "|")) {
                            list($value, $entityId) = explode("|", $value);
                            $query = $query->where("UP.reference_key", $operator, $entityId)
                            ->where("UP.reference_id", $operator, $value);
                        } elseif(Str::endsWith('entity_type', $dbKey)) {
                            $query = $query->where("UP.reference_key", $operator, $value);
                        }
                    }
                } else {

                    if ($operator === 'like') {
                        $query = $query->where($dbKey, $operator, '%' . $value . '%');
                        break;
                    }

                    if ($operator !== '=') {
                        $value = (float)$value;
                    }
                    $query = $query->where($dbKey, $operator, $value);
                }
        }



        return $query;
    }

    public static function applySubscriptionFilterCondition($parentCls, $query, string $key, string $operator, ?string $value) {
        switch ($key) {
            case 'my_subscribtion':
                if ($value == "") {
                    break;
                }

                return $query->havingRaw('my_subscribtion =' . $value);
        }

        return $parentCls::applyFilterCondition($query, $key, $operator, $value);
    }

    public static function randomPassword() {
        $totalLength = 6;
        $alphabetCnt = 4;
        $numbersCnt = 1;
        $splCharCnt = 0;
        $passStr = "";
        $alphabets = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        if (is_plugin_active('password-criteria')) {
            $criteria = \Impiger\PasswordCriteria\Models\PasswordCriteria::first();
            if(!empty($criteria)) {
                $totalLength = ($criteria->min_length) ? $criteria->min_length : $totalLength;
                $alphabetCnt = ($criteria->has_alphabet) ? $criteria->alphabet_count : $alphabetCnt;
                $numbersCnt = ($criteria->has_number) ? $criteria->number_min_count : $numbersCnt;
                $splCharCnt = ($criteria->has_special_char) ? $criteria->special_char_count : $splCharCnt;
            }
        } else {
            return $passStr = Str::random($totalLength);
        }

        if($alphabetCnt) {
            $passStr = generateRandomPassword(2, $alphabets, 26);
        }

        if($numbersCnt) {
            $passStr .= generateRandomPassword($numbersCnt, '1234567890', 10);
        }

        if($splCharCnt) {
            $passStr .= generateRandomPassword($splCharCnt, '!,@,#,$,%,^,&,*,(,),_,-', 12);
        }

        $extraLen = (Str::length($passStr) >= $totalLength) ? 0 : ($totalLength - Str::length($passStr));
        $passStr .= ($extraLen > 0)  ? generateRandomPassword($extraLen, $alphabets, 26) : $passStr;
        return $passStr;
    }


    public static function getParentRoles(){
        $parentUsers = [];
        $userRoles = Auth::user()->roles;
        $roleIds = $userRoles->pluck('id')->toArray();

        $query = \Impiger\ACL\Models\Role::where(function($query) use($roleIds){
            foreach($roleIds as $roleId){
                $query->orWhereJsonContains('child_roles',"$roleId");
            }
        });
        $roleids = $query->get()->pluck('id')->toArray();
        $select = [
                'users.id',
                \DB::raw("concat(coalesce(users.first_name,''),' ',coalesce(users.last_name,'')) as text")
            ];
        $model = new \Impiger\ACL\Models\User;
        $query = $model::join('role_users AS ru','ru.user_id','=','users.id')->whereIn('ru.role_id', $roleids)->where('users.id','!=',Auth::id())->select($select);

        ;
        $parentUsers = $query->get()->pluck('text','id')->toArray();
        return $parentUsers;
    }

    public static function getInstituteIdsByLogin($withRelationEntity = false) {
        $instituteIds=[];
        if(is_plugin_active('multidomain')){
            $instituteIds = app(\Impiger\Multidomain\Multidomain::class)->getInstituteByCurrentDomainId();
        }
        if($withRelationEntity) {
            $user = Auth::user();
            if(!Arr::has($instituteIds, 0) && $user && $user->applyDataLevelSecurity()) {
                $entities = getAppEntitiesFromSession(true);
                $userEntity = getUserEntitiesFromSession();
                $instituteIds = [];
                foreach($userEntity as $k => $entity) {
                    $instituteIds = $instituteIds + getInstituteIdsFromEntity(Arr::get($entities, $k), $entity);
                }
            }
        }

        return $instituteIds;
    }

    public static function getInstituteDetailsByLogin() {
        if(is_plugin_active('multidomain')){
            return app(\Impiger\Multidomain\Multidomain::class)->getInstituteDetailsByCurrentDomainId();
        }
        return [];
    }

    public static function applyInstituteSecurityCondition($specificEntity,$query, $table = "") {
        if ($specificEntity == 'institution') {
            $instituteIds = SELF::getInstituteIdsByLogin();
            if(Arr::has($instituteIds, 0)) {
                $query = ($table) ?  $query->whereIn($table.'.id', $instituteIds) : $query->whereIn('id', $instituteIds);
            }
        }

        return $query;
    }

    public static function applyTrainingProgramSecurityCondition($table,$query) {
        if (in_array($table, ['training_program','departments','subjects','syllabus', 'course_units'])) {
            $instituteIds = SELF::getInstituteIdsByLogin();
            $user = Auth::user();

            if($table == 'departments' && !joinTableExists($query, 'training_program')) {
                $query = $query->leftJoin('training_program', 'training_program.department_id', '=', $table.'.id');
            } elseif($table == 'course_units' && !joinTableExists($query, 'training_program')) {
                $query = $query->leftJoin('training_program_course_map AS TPCM', 'course_units.id', '=','TPCM.course_unit_id');
                $query = $query->leftJoin('training_program', 'training_program.id', '=','TPCM.training_program_id');
            }

            if(!joinTableExists($query, 'training_program_subscriptions')) {
                $query = $query->leftJoin('training_program_subscriptions AS TPS', 'training_program.id', '=', 'TPS.training_program_id')
                ->where(["TPS.wf_status" => APPROVED_STATE_SLUG]);
                if($user && $user->applyDataLevelSecurity() && Arr::has($instituteIds, 0)) {
                    $query = $query->whereIn('TPS.institute_id', $instituteIds);
                }
            }
        }
        return $query;
    }

    public static function getCrudEntities($groupedEntities=null,$mappedRoles=[]){
        $entities = [];
        $adminRoleId = getRoleIdFromSlug(SUPERADMIN_ROLE_SLUG);
        if (!empty($groupedEntities)) {
            $entities = Crud::whereIn("id", $groupedEntities['crud_id'])->get();
        }else if(in_array($adminRoleId,$mappedRoles)){
            $entities =[];
        }else {
            $entities = Crud::where("is_entity", 1)->get();
        }
        return $entities;
    }
    
    public static function createOrUpdateMsmeCandidate($request,$msmeCandidateDetails){
        $user = $entrepreneur = $trainee = [];
        $isNew = false;
        $coreUserData = [
            'email' => $msmeCandidateDetails->email,
            'password' => Hash::make($msmeCandidateDetails->mobile_no),
            'first_name' => $msmeCandidateDetails->candidate_name
        ];
        $coreUserExists = app(\Impiger\ACL\Repositories\Interfaces\UserInterface::class)->getFirstBy(['email'=>$coreUserData['email']]);
        if(!$coreUserExists){
            $user = app(\Impiger\ACL\Repositories\Interfaces\UserInterface::class)->createOrUpdate($coreUserData);
            $isNew = true;
        }else{
            $user = $coreUserExists;
        }
        if($user){
            if($isNew) {
                $role = \Impiger\ACL\Models\Role::where('slug', CANDIDATE_ROLE_SLUG)->whereNull('deleted_at')->first();
                $role->users()->attach($user->id);

                $divisions = \Impiger\MasterDetail\Models\Division::pluck('id')->toArray();
                $userDLS = array();
                if($divisions) {   
                    foreach ($divisions as $division) {
                        $userDLS[] = array(
                            'ref_type' => 'Impiger\MasterDetail\Models\Division', 
                            'ref_id' => $division, 
                            'ref_key' => getEntityId('divisions','module_db'),
                        );
                    }
                    
                }

                if($userDLS) {
                    foreach ($userDLS as $dls) {
                        $cond = [
                            'user_id' => $user->id,
                            'reference_id' => $dls['ref_id'],
                            'reference_type' => $dls['ref_type'],
                            'reference_key' => $dls['ref_key'],
                            'role_id' => $role->id
                        ];
                        $existsEntity = \Impiger\ACL\Models\UserPermission::where($cond)->first();
                        $data = $cond;
                        $data['role_id'] = $role->id;
                        $data['role_permissions'] = $role->permissions;
                        if ($existsEntity) {
                            \Impiger\ACL\Models\UserPermission::where($cond)->update($data);
                        } else {
                            \Impiger\ACL\Models\UserPermission::create($data);
                        }
                    }
    
                    event(new \Impiger\ACL\Events\RoleAssignmentEvent($role, $user));  
                }

                $user->domain_href = getUserDomainUrl($user->id);  
                $user->temp_password = $msmeCandidateDetails->mobile_no;
                CrudHelper::sendEmailConfig('user','{"create":"1","edit":null,"subject":"'.USER_LOGIN_CREDENTIALS_SUBJECT.'","message": "'.get_setting_email_template_content('core', 'base', 'user').'","send_to":"based_on_fields","reciever_role":null,"reciever_field":"email|contact_email","default_reciever":null}',$user);
            }
            if (!app(\Impiger\ACL\Repositories\Interfaces\ActivationInterface::class)->completed($user)) {
                app(\Impiger\ACL\Services\ActivateUserService::class)->activate($user);
            }
            $entrepreneurData = [
                'user_id' => $user->id,
                'care_of' => $msmeCandidateDetails->care_of,
                'community' => $msmeCandidateDetails->category,
                'name' => $msmeCandidateDetails->candidate_name,
                'dob' => $msmeCandidateDetails->dob,
                'gender_id' => $msmeCandidateDetails->gender,
                'mobile' => $msmeCandidateDetails->mobile_no,
                'email' => $msmeCandidateDetails->email,
                'password' => $msmeCandidateDetails->mobile_no,
                'father_name' => $msmeCandidateDetails->father_husband_name,
                'district_id' => $msmeCandidateDetails->district_id,
                'address' => $msmeCandidateDetails->address,
                'photo_path' => $msmeCandidateDetails->photo,
                'scheme' => $msmeCandidateDetails->scheme,
                'msme_candidate_detail_id' => $msmeCandidateDetails->id
            ];
            $entrepreneurExists = app(\Impiger\Entrepreneur\Repositories\Interfaces\EntrepreneurInterface::class)->getFirstBy(['user_id'=>$entrepreneurData['user_id'],'email'=>$entrepreneurData['email']]);
            if(!$entrepreneurExists){
                $entrepreneur = app(\Impiger\Entrepreneur\Repositories\Interfaces\EntrepreneurInterface::class)->createOrUpdate($entrepreneurData);
            }else{
                $entrepreneurExists->fill($entrepreneurData);
                $entrepreneur = app(\Impiger\Entrepreneur\Repositories\Interfaces\EntrepreneurInterface::class)->createOrUpdate($entrepreneurExists);
            }
            if($entrepreneur){
                $scheme = getValueFromId('attribute_options',['id'=>$msmeCandidateDetails->scheme]);
                $trainingId = getMSMETrainingId($scheme);
                $trainingTitle = app(\Impiger\TrainingTitle\Repositories\Interfaces\TrainingTitleInterface::class)->findById($trainingId);
                $traineeData = [
                    'entrepreneur_id' => $entrepreneur->id,
                    'user_id' => $user->id,
                    'training_title_id' => $trainingId,
                    'division_id' => (isset($trainingTitle->division_id)) ? $trainingTitle->division_id : NULL,
                    'financial_year_id' =>(isset($trainingTitle->financial_year_id)) ? $trainingTitle->financial_year_id : NULL,
                    'annual_action_plan_id' => (isset($trainingTitle->annual_action_plan_id)) ? $trainingTitle->annual_action_plan_id : NULL,
                ];
                $traineeExists = app(\Impiger\Entrepreneur\Repositories\Interfaces\TraineeInterface::class)->getFirstBy($traineeData);
                if(!$traineeExists){
                    $trainee = app(\Impiger\Entrepreneur\Repositories\Interfaces\TraineeInterface::class)->createOrUpdate($traineeData);
                }else{
                    $traineeExists->fill($traineeData);
                    $trainee = app(\Impiger\Entrepreneur\Repositories\Interfaces\TraineeInterface::class)->createOrUpdate($traineeExists);
                }
                if($trainee){
                    $msmeCandidateDetails->is_enrolled = 1;
                    $msmeCandidateDetails->save();                    
                }
            }
        }
    }
    
    public static function sendNewsletter($post){
        $subscribers = \App\Models\NewsLetterSubscription::where('status',1)->get();
        $message = $post->name.' NewsLetter Created <a href="'.env('APP_URL').'/'.$post->slug.'">Click Here </a>to know More';
        if($subscribers){
            foreach($subscribers as $subscriber){
                dispatch(function () use($message, $subscriber) {
                    \EmailHandler::send($message, 'EDII-TN NewsLetters', $subscriber->email_id);
                });                
            }
        }
    }
    /* Static functions end here */

    /* route functions start here */
    function getDependantDropdownOptions(Request $request, BaseHttpResponse $response)
    {
        $lookupTable = $request->input('dd_table');
        $lookupValue = $request->input('dd_lookup');
        $lookupKey = $request->input('dd_key');
        $filterKey = $request->input('dd_filterkey');
        $filterValue = $request->input('value');
        $filterEnityKey = ($request->input('dd_entitytypekey')) ? $request->input('dd_entitytypekey') : "";
        $filterEntityValue = ($request->input('dd_entitytypeValue')) ? $request->input('dd_entitytypeValue') : "";

        if (!$lookupTable || !$lookupValue || !$lookupKey || !$filterKey) {
            return $response
                ->setError(true)
                ->setMessage('Required param missing.');
        }

        $concatRequired = str_contains($lookupValue, "|");
        $fields = ($concatRequired) ? DB::raw("CONCAT_WS(' '," . str_replace('|', ',', $lookupValue) . ") AS text") : $lookupTable.'.'.$lookupValue . " AS text";
         if ($lookupTable == 'training_program_intakes' && str_contains($lookupValue,'month')) {
            $fields = DB::raw('MONTHNAME(STR_TO_DATE('.$lookupTable.'.'.$lookupValue.', "%m")) as text');
        }
        $query = DB::table($lookupTable)->select($fields, DB::raw($lookupTable . '.' . $lookupKey . " AS id"));

        $model = get_model_from_table($lookupTable);

        if ($model) {
            $model = new $model();
            $query = apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, [$fields, $lookupTable . "." . $lookupKey], false);
            $config = array(
                'institute_id' => array('cndn' => ['institute_id' => $request->input('institute_id')]),
                'department_id' => array('cndn' => ['TPS.institute_id' => $request->input('institute_id')]),
                'program_type_id' => array( 'cndn' => ['TPS.institute_id' => $request->input('institute_id')]),
            );

            if(Arr::get($config, $filterKey) && $request->input('institute_id')) {
                $cndns = Arr::get($config, $filterKey.'.cndn');
                if($lookupTable == 'training_program') {
                    if($request->input('department_id')) {
                        $query = $query->where($lookupTable.'.department_id', $request->input('department_id'));
                    }
                    if(!joinTableExists($query, 'training_program_subscriptions')) {
                        $query = $query->join('training_program_subscriptions AS TPS', function ($join) use ($lookupTable) {
                            $join->on($lookupTable . '.id', '=', 'TPS.training_program_id');
                        });
                        $query = $query->where('TPS.wf_status', APPROVED_STATE_SLUG);
                    }


                } elseif($lookupTable == 'departments') {
                    $query = SELF::applyTrainingProgramSecurityCondition($lookupTable, $query);
                }
                $query = $query->where($cndns);
            }
        } else {
            if (Schema::hasColumn($lookupTable, 'deleted_at')) {
                $query = $query->whereNull($lookupTable . '.deleted_at');
            }

            if (Schema::hasColumn($lookupTable, 'is_enabled')) {
                $query = $query->where($lookupTable . '.is_enabled', 1);
            }
        }

        $query = $query->where($filterKey, $filterValue);

        if($lookupTable == 'annual_action_plan' && $filterKey == 'division_id') {
            if($request->input('financial_year_id')) {
                $query = $query->where('financial_year_id', $request->input('financial_year_id'));
            }
        }

        if($lookupTable == 'training_title' && $filterKey == 'annual_action_plan_id') {
            if($request->input('division_id')) {
                $query = $query->where('division_id', $request->input('division_id'));
            }
            if($request->input('financial_year_id')) {
                $query = $query->where('financial_year_id', $request->input('financial_year_id'));
            }
        }

        

        if ($filterEnityKey && $filterEntityValue) {
            $query = $query->where($filterEnityKey, $filterEntityValue);
        }

        if($lookupTable == 'schedule_meeting') {
            $excludedMeetingIds = app(\Impiger\ScheduleMeeting\Models\MinutesOfMeeting::class)->getModel()->groupBy('meeting_id')->pluck('meeting_id')->toArray();
            $query =  $query->whereDate('date', '<=', \Carbon\Carbon::now())
            ->whereTime('end_time', '<', \Carbon\Carbon::now())->whereNotIn('id', $excludedMeetingIds);
        }
        if($lookupTable =='spoke_registration'){
            $query = $query->where($filterKey, $filterValue)->orderBy($lookupTable.'.'.$lookupValue);
        }

        return $query->get()->toArray();
    }

    function getEntityOptions(Request $request, BaseHttpResponse $response)
    {
        $entityId = $request->input('entity_id');
        if (!$entityId) {
            return $response
                ->setError(true)
                ->setMessage('Required param missing.');
        }
        $entity = DB::table('cruds')->where('id', $entityId)->first();
        $query = '';
        if (!empty($entity)) {
            $table = $entity->module_db;
            $fields = [$table . '.name as text', $table . '.id'];
            $query = DB::table($table)->select($fields);
            $model = get_model_from_table($table);
            if ($model) {
                $model = new $model();
                $query = apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, $fields, false);
            }
            $rawCondition = get_common_condition($table);
            if(!empty($rawCondition)){
                $query = $query->whereRaw($rawCondition);
            }
            return $query->get()->toArray();
        }
        return [];
    }

    public function formResponse() {
        $data['form'] = \Request::input('form') ?: "";
        return Theme::scope('form-response', $data, 'theme-form.form-response')->render();

    }

    public function getOptions(Request $request, BaseHttpResponse $response)
    {
        if ($request->has('table')) {
            $table = $request->input('table');
            $whereRaw = get_common_condition($table);
            $selectedColumns = [$table.".id", $table.".name as text"];
            $query = \DB::table($table)->select($selectedColumns)
                ->orderBy($table.".name", "ASC");
            if($whereRaw) {
                    $query = $query->whereRaw($whereRaw);
                }
            $query = CrudHelper::applyTrainingProgramSecurityCondition($table, $query);
            $query = CrudHelper::applyInstituteSecurityCondition($table, $query, $table);
            $model = get_model_from_table($table);
            if ($model) {
                $model = new $model();
                $query = apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, $selectedColumns, false);
            }
            return $query->get()->toArray();
        } else {
            return $response->setError(true)
                ->setMessage("Required param missing");
        }
    }

    /**
     * @param int $id
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function postSubscription($id, Request $request, BaseHttpResponse $response)
    {
        $ids = [];
        $className = $request->input('model_class');
        $pluginName = Str::snake(class_basename($className), '-');
        $localKey = str_replace("-", "_", $pluginName) . "_id";
        $subscriptionClass = $className."Subscription";
        $instituteIds = $request->input('institute_ids');
        if (Arr::has($instituteIds, '0')) {
            foreach ($instituteIds as $institute) {
                $data = [];
                $data['institute_id'] = $institute;
                $data[$localKey] = $id;
                $data['wf_status'] = TRAINING_PROGRAM_SUBSCRIPTION_SLUG;
                $ids[] = $institute;

                if($pluginName == "organization" ) {
                    $data['mou_attachment'] = $request->input('mou_attachment');
                    $data['area_of_partnership'] = $request->input('area_of_partnership');
                }

                $trainingProgramSubscription = $subscriptionClass::updateOrCreate(["institute_id" => $institute, "$localKey" => $id], $data);
                event(new CreatedContentEvent(TRAINING_PROGRAM_SUBSCRIPTION_MODULE_SCREEN_NAME, $request, $trainingProgramSubscription));
            }
        }

        return $response->setPreviousUrl(route($pluginName.'.index'))->setNextUrl(route($pluginName.'.index'))->setMessage(trans('crud.subscription_success_message'));
    }

    public function updateRowActivation($id, Request $request, BaseHttpResponse $response)
    {
        $value = $request->input('value');
        $model = $request->input('model');
        $moduleName = $request->input('module');
        if (!$model) {
            return $response
                ->setError(true)
                ->setMessage('Required param missing.');
        }
        if( in_array($model,config('general.user_enable_disable', [])))
        {
            $user = $this->updateUserEnableDisable($id,$request);
            return $response
                    ->setMessage($user);
        }

        if($moduleName){
            $dependandData = constant("DEPENDANT_MODULE_IN_".strtoupper($moduleName));
            $dataExist = CrudHelper::isDependentDataExistCore($moduleName,$dependandData, array($id));

            if($dataExist) {
                return $response
                    ->setError()
                    ->setMessage($dataExist);
            }
        }
        $modelObj = new $model;
        $rowData = $modelObj::find($id);
        $screen = class_basename($model);

        if (!empty($rowData)) {
            $rowData->is_enabled = ($value) ? 0 : 1;
            $message = ($rowData->name) ? $rowData->name . " " : $screen . " ";
            $message .= ($value) ? trans('crud.disable_success_message') : trans('crud.enable_success_message');
            if ($model == "Impiger\FinancialYear\Models\FinancialYear") {
                if (!$value) {
                    $isRunning = $modelObj::whereNotin('id', [$id])->where('is_running', 1)->first();
                    if ($isRunning) {
                        return $response
                                        ->setError(true)
                                        ->setMessage("Another Session is in active state");
                    }else{
                        $rowData->is_running = 1;
                    }
                }else{
                    $rowData->is_running = 0;
                }
            }
            if ($rowData->save()) {
                event(new UpdatedContentEvent($screen, $request, $rowData));
                return $response
                    ->setMessage($message);
            }
        }
    }

    public function updateUserEnableDisable($id, Request $request){
        $value = $request->input('value');
        $model = $request->input('model');
        $moduleName = $request->input('module');
        $modelObj = new $model;
        $rowData = $modelObj::find($id);
        $screen = class_basename($model);
        $status = ($value) ? 0 : 1;
        $user = \Impiger\ACL\Models\User::find($rowData->user_id);
        if (!empty($rowData)) {
            $rowData->is_enabled = $status;
            $message = $user->name . " ";
            $message.= ($value) ? trans('crud.disable_success_message') : trans('crud.enable_success_message');
            $userMsg=STUDENT_MSG;
            if ($rowData->save()) {

                if ($status && $user) {
                    $emailIds=$user->email;
                    $userMsg=str_replace('{user_status}','Activated',$userMsg);
                    $activateUserService= new \Impiger\ACL\Services\ActivateUserService(app(\Impiger\ACL\Repositories\Interfaces\ActivationInterface::class));
                    $activateUserService->activate($user);
                    SELF::sendCustomEmailConfig(STUDENT_SUBJECT,$userMsg,$user,$emailIds);

                } else {
                    $emailIds=$user->email;
                    $userMsg=str_replace('{user_status}','Deactivated',$userMsg);

                    app(\Impiger\ACL\Repositories\Interfaces\ActivationInterface::class)->remove($user);

                    SELF::destroyUserSession($user);
                    SELF::sendCustomEmailConfig(STUDENT_SUBJECT,$userMsg,$user,$emailIds);


                }
                event(new UpdatedContentEvent($screen, $request, $rowData));
                return $message;
            }
        }
    }

    public function forceToChangePassword($user_id, Request $request){
        if(!$user_id) {
            return false;
        }
        Assets::addScripts(['jquery-validation'])
            ->addStylesDirectly('vendor/core/core/acl/css/login.css')
            ->removeStyles([
                'select2',
                'fancybox',
                'spectrum',
                'simple-line-icons',
                'custom-scrollbar',
                'datepicker',
            ])
            ->removeScripts([
                'select2',
                'fancybox',
                'cookie',
            ]);
            Assets::addStylesDirectly('vendor/core/core/acl/css/custom-style.css');


        if (is_plugin_active('password-criteria')) {
            apply_filters(BASE_FILTER_ADD_PASSWORD_CRITERIA);
        }
        return view('auth.change_password',compact('user_id'));
    }

    static function getTableField( $table )
	{
        $columns = array();
	    foreach(\DB::select("SHOW COLUMNS FROM $table") as $column)
		    $columns[$column->Field] = $column->Field;
        return $columns;
	}

    function getUsers(Request $request, BaseHttpResponse $response)
    {
        $roleId = $request->input('role_id');
        if (!$roleId) {
            return [];
        }

        $roleId = is_array($roleId) ? $roleId : [$roleId];
        $fields = ['users.id', DB::raw('CONCAT_WS(" ",users.first_name, last_name) AS text'), 'role_id'];
        return  \Impiger\ACL\Models\User::select($fields)
        ->join('role_users AS RU', 'users.id', '=', 'RU.user_id')
        ->whereIn('role_id', $roleId)->get()->toArray();
    }

    function getAnnualActionPlan(Request $request) {
        $limit = $request->get('limit');
        
        if (is_plugin_active('training-title')) {
            // \Log::info("getTrainingTitleLists");
            \Log::info(is_plugin_active('training-title'));
            $TrainingTitle_model = app(\Impiger\TrainingTitle\Models\TrainingTitle::class)->getModel();
            
            $data = $TrainingTitle_model->select([
                'training_title.id',
                'training_title.name AS title',
                'annual_action_plan.budget_per_program',
                'training_title.venue',
                DB::Raw("DATE_FORMAT(`training_title`.`training_start_date`,'%e-%m-%Y') AS start"),
                DB::Raw("DATE_FORMAT(`training_title`.`training_start_date`,'%e-%c-%Y') AS start_js"),
                DB::Raw("DATE_FORMAT(`training_title`.`training_start_date`,'%b') AS month"),
                DB::Raw("DATE_FORMAT(`training_title`.`training_start_date`,'%d') AS date"),
                // 'training_title.training_end_date AS end',
                'training_title.created_at'
            ])       
            ->leftJoin('annual_action_plan', 'annual_action_plan.id', '=', 'training_title.annual_action_plan_id')
            ->orderBy('training_title.created_at', 'desc')
            ->limit($limit);

            if($request->get('get_budget')) {
                $data = $data->where('training_title.id', $request->get('training_title_id'));
            } 

            if($request->get('tsd')) {
                $data = $data->where('training_title.training_start_date', '>=', $request->get('tsd'));
                // $data->dd();
            } else {
                $data = $data->where('training_title.training_start_date', '>=', date('Y-m-d'));
            }
            // $data->dd();
            return $trainings = $data->get()->toArray();
            // return view('annualactionplan.list_item',compact('trainings'));
            // return $data->get();
            // if($trainings) {
            //     return $response->setData($trainings);
            // } else {
            //     return $response->setError(true)->setMessage("No data found");
            // }

        } else {
            // return $response->setError(true)->setMessage("No data found");
            return [];
        }
    }

    function viewdetail($id, Request $request) {
        if (is_plugin_active('training-title')) { 
            $training_title_id = $id | $request->get('id');
            $TrainingTitle_model = app(\Impiger\TrainingTitle\Models\TrainingTitle::class)->getModel();
            $data = $TrainingTitle_model->where('training_title.id', $id)->select([
                'training_title.id',
                'annual_action_plan.name AS title',
                'training_title.code',
                'training_title.venue',
                'training_title.email',
                'training_title.phone',
                'training_title.training_start_date AS start',
                'training_title.training_end_date AS end',
                'training_title.private_workshop',
                'training_title.fee_paid',
                'training_title.webinar_link',
                'training_title.description',
                'training_title.created_at'
            ])       
            ->leftJoin('annual_action_plan', 'annual_action_plan.id', '=', 'training_title.annual_action_plan_id');
            // return $data->get();
            $trainings = $data->get();
            return view('annualactionplan.view_detail',compact('trainings'));
        } else {
            return [];
        }
    }	

    function getEntrepreneur(Request $request){

        $EntrepreneurModel = app(\Impiger\Entrepreneur\Models\Entrepreneur::class)->getModel();
        $whereKey = '';
        if($request->get('email')) {
            $whereKey = 'email';
            $keyVal = $request->get('email');
        }

        if($request->get('mobile')) {
            $whereKey = 'mobile';
            $keyVal = $request->get('mobile');
        }

        return $EntrepreneurModel->where($whereKey, $keyVal)->first();
                
    }
    
    public function getSpokeStudentByEmail(Request $request, BaseHttpResponse $response)
    {
         if ($request->has('email')) {
            $email = $request->input('email');
            if(is_plugin_active('entrepreneur')){

                $isExisting = \DB::table('tnsi_startup')->where('team_members','like', '%'.$email.'%')->first();
                if($isExisting){
                    return $response
                        ->setError()
                        ->setMessage('You have already submitted a TNSI application');
                }

                $entrepreneur = \Impiger\Entrepreneur\Models\Entrepreneur::where('email',$email)
                                ->whereNotNull('spoke_registration_id')->first();
                if(empty($entrepreneur)) {
                    return $response->setError(true)->setMessage($email.' You are not a student in spoke colleges');
                }
               
                $studentDetails = [
                    'mobile_number' => $entrepreneur->mobile,
                    'spoke_registration_id' => CrudHelper::formatRows($entrepreneur->spoke_registration_id, 'database', 'spoke_registration|id|name', $entrepreneur, ''),
                    'name' => $entrepreneur->name,
                    'dob' => $entrepreneur->dob,
                    'aadhar_number' => $entrepreneur->aadhaar_no,
                    'district' =>CrudHelper::formatRows($entrepreneur->district_id, 'database', 'district|id|name', $entrepreneur, ''),
                ];
                return $studentDetails;
            }
        
        } else {
            return $response->setError(true)
                ->setMessage("Required param missing");
        }
    }
    public function getMSMECandidateDetails(Request $request, BaseHttpResponse $response)
    {        
       
       
        if ($request->has('canditateId') && $request->has('scheme')) {
            $appID = $request->input('canditateId');
            $scheme = $request->input('scheme');
            
            $msmeConfig = MSME_CANDIDATE_API_CONFIG;
            $postData = 'token='.$msmeConfig[$scheme]['token'].'&fid='.$appID.''; 
          
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $msmeConfig[$scheme]['url']);  
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_VERBOSE, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER,['Accept: application/form-data']) ;
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);   
            $result = curl_exec($ch);
        
            curl_close($ch);
            $candidateDetails = json_decode($result)[0];
         
            if($candidateDetails){
               
                $candidateInfo = (isset($candidateDetails->candidate)) ? $candidateDetails->candidate[0] : $candidateDetails;
               
                $candidateName = (isset($candidateInfo->applicant_name)) ? $candidateInfo->applicant_name : $candidateInfo->candidate_name;
                $fatherName = "";
                $entrepreneur = app(\Impiger\Entrepreneur\Repositories\Interfaces\EntrepreneurInterface::class)->getFirstBy(['email'=>$candidateInfo->mail_id]);
                if($entrepreneur){
                    $trainee = app(\Impiger\Entrepreneur\Repositories\Interfaces\TraineeInterface::class)->getFirstBy(['entrepreneur_id'=>$entrepreneur->id]);
                    if($trainee){
                        return $response->setError(true)
                                        ->setMessage($candidateName." you are already enrolled");
                    }
                }
                if(isset($candidateInfo->father_name)){
                    $fatherName = $candidateInfo->father_name;
                }elseif(isset($candidateInfo->father_husband_wife)){
                    $fatherName = $candidateInfo->father_husband_wife;
                }
                $msmeDetails = [
                    'candidate_name' => (isset($candidateInfo->applicant_name)) ? $candidateInfo->applicant_name : $candidateInfo->candidate_name,
                    'care_of' => getIdfromValue('attribute_options', ['name'=>$candidateInfo->care_of,'attribute'=>'care_of']),
                    'father_husband_name' => $fatherName,
                    'spouse_name' => isset($candidateInfo->spouse_name) ? $candidateInfo->spouse_name : "",
                    'gender' => getIdfromValue('attribute_options', ['name'=>$candidateInfo->gender,'attribute'=>'gender']),
                    'category' => getIdfromValue('attribute_options', ['name'=>$candidateInfo->category,'attribute'=>'community']),
                    'mobile_no' => $candidateInfo->mobile_no,
                    'email' => $candidateInfo->mail_id,
                    'qualification' => (isset($candidateInfo->qualification)) ? $candidateInfo->qualification:"",
                    'dob' => date("Y-m-d", strtotime($candidateInfo->dob))  ,
                    'address' => $candidateInfo->address,
                    'photo' => $candidateInfo->photo,
                    'district' => getIdfromValue('district', ['name'=>$candidateInfo->district]),
                ];
                return $msmeDetails;
            }
        
        } else {
            return $response->setError(true)
                ->setMessage("Required param missing");
        }
    }
    
     /**
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws Throwable
     */
    public function getReportsContent(Request $request, BaseHttpResponse $response)
    {
        $parentModuleUpper = ucfirst(Str::camel($request->input('module')));
        $moduleUpper = ucfirst(Str::camel($request->input('subModule')));
        $data = [];
        $repository = app()->make('Impiger\\'.$parentModuleUpper.'\Repositories\Interfaces\\'.$moduleUpper.'Interface');
        
        $fields = Arr::get($statsConfig, 'field');
        $data = SELF::getDashboardStatsData($repository, $fields, Arr::get($statsConfig,'sql_join'), Arr::get($statsConfig,'sql_cndn'),Arr::get($statsConfig,'sql_group_by'), true,'CNT' ,Arr::get($statsConfig,'workflow_meta'));;
        $fields = ($fields) ? explode(",", $fields) : $fields ;
        return $response
            ->setData(view('widgets.custom-table', compact('data', 'fields'))->render());
    }    

    public static function renderTrainingDetail() {

        
        $id = $_REQUEST['id'];
        if (is_plugin_active('training-title')) { 
            $TrainingTitle_model = app(\Impiger\TrainingTitle\Models\TrainingTitle::class)->getModel();

            $select = [
                'training_title.id',
                'training_title.division_id',
                'training_title.financial_year_id',
                'training_title.annual_action_plan_id',
                'annual_action_plan.name',                
                // 'annual_action_plan.training_module',
                DB::Raw('(CASE WHEN annual_action_plan.training_module = 1 THEN "Online" ELSE "Offline" END) AS training_module'),
                'training_title.code',
                'training_title.venue',
                'training_title.email',
                'training_title.phone',
                'training_title.training_start_date AS start',
                'training_title.training_end_date AS end',
                'annual_action_plan.duration',
                // 'training_title.private_workshop',
                DB::Raw('(CASE WHEN training_title.private_workshop = 1 THEN "Yes" ELSE "No" END) AS private_workshop'),
                'training_title.fee_paid',
                DB::Raw('(CASE WHEN training_title.fee_paid = 1 THEN "Free" ELSE "Paid" END) AS training_version'),
                'training_title.fee_amount',
                'training_title.webinar_link',
                'training_title.small_content',
                'training_title.description',
                'training_title.created_at'
            ];

            $data = $TrainingTitle_model->where('training_title.id', $id)->select($select)      
            ->leftJoin('annual_action_plan', 'annual_action_plan.id', '=', 'training_title.annual_action_plan_id');
            
            if (Auth::id()) {
                $user = Auth::user();
                $userRoles = ($user) ? $user->roles : [];
                $roleSlugs = ($user) ? $userRoles->pluck('slug')->toArray() : [];
                if(!$user->admins() && in_array('candidate', $roleSlugs)) {
                    $candidate = array('user_id' => Auth::id());
                    $entrepreneur = DB::table('entrepreneurs')->where($candidate)->first();
                    if($entrepreneur) {
                        $trainee = \Impiger\Entrepreneur\Models\Trainee::where(['entrepreneur_id' => $entrepreneur->id, 'training_title_id' => $id])->first();
                        if($trainee) {
                            $select[] =  'trainees.entrepreneur_id';
                            $select[] =  'trainees.id AS trainee_id';
                            $data = $data->select($select)->where('trainees.entrepreneur_id', $entrepreneur->id)->leftJoin('trainees', 'trainees.training_title_id', '=', 'training_title.id');
                        }
                        
                    }
                    
                }
            }

            // return $data->get();
            $trainings = $data->get();
            // return view('annualactionplan.view',compact('trainings'));
            // return view('annualactionplan.view')->with('amount', 500 * 100)->with('payment', 0);
            return view('annualactionplan.view_detail',compact('trainings'));
        } else {
            return [];
        }
    }

    public function subscribeToEvent(Request $request, BaseHttpResponse $response) {

        // dd(is_plugin_active('entrepreneur'));
        // if (Auth::id()) {
        //     $user = Auth::user();
        //     if (is_plugin_active('user')  && $user->admins()){
        //         return $response->setPreviousUrl(route('trainee.index'));
        //     }
        // }

        if (is_plugin_active('entrepreneur')) {
            // dd($request);
            $division_id = $request->input('division_id');
            $financial_year_id = $request->input('financial_year_id');
            $annual_action_plan_id = $request->input('annual_action_plan_id');
            $training_title_id = $request->input('training_title_id');
            // $entrepreneur_id = $request->input('entrepreneur_id');
            $user_id = $request->input('user_id');

            $entrepreneur = \Impiger\Entrepreneur\Models\Entrepreneur::where('user_id',$user_id)->first();

            if(!$entrepreneur) {
                // return $response->setError(true)->setMessage('Please login as a candidate/entrepreneur/student before subscribe!');
                // dd('test');
                throw ValidationException::withMessages([
                    'trainee' => ['Please login as a candidate/entrepreneur/student before subscribe!'],
               ]);
            }

            $request['entrepreneur_id'] = $entrepreneur->id;

            $trainee = \Impiger\Entrepreneur\Models\Trainee::where(['entrepreneur_id' => $entrepreneur->id, 'training_title_id' => $training_title_id])->first();

            if($trainee) {
                // return $response->setError(true)->setMessage('You have already subscribed!');
                throw ValidationException::withMessages([
                     'trainee' => ['You have already subscribed!'],
                ]);
            }

            $trainee = app(\Impiger\Entrepreneur\Repositories\Interfaces\TraineeInterface::class)->createOrUpdate($request->input());

            return $response
            ->setPreviousUrl(route('trainee.index'))
            ->setNextUrl(route('trainee.edit', $trainee->id))
            ->setMessage(trans('core/base::notices.create_success_message'));

        }

    }

    public static function createEntrepreneurUser($request, $coreUserRepository, $activateUserService = null, $roleSlug=null, $isActivate=true)
    {
        
        $data = $request->input();
        $coreUserExists = $coreUserRepository->getFirstBy(['email'=>$request['email']]);
        if(!$coreUserExists){
            if(isset($request['name']) && $request['name']) {
                $data['first_name'] = $request['name'];
            } else if(isset($request['first_name']) && $request['first_name']) {
                $data['first_name'] = $request['first_name'];
            }
            $data['password'] = Hash::make($request['password']);
            $user = $coreUserRepository->createOrUpdate($data);
            $newUser = true;
        }else{
            $user = $coreUserExists;
            $newUser = false;
        }

        if($user && $newUser){
            if($roleSlug){
                $role = \Impiger\ACL\Models\Role::where('slug', $roleSlug)->whereNull('deleted_at')->first();
                $role->users()->attach($user->id);
                
                $divisions = \Impiger\MasterDetail\Models\Division::pluck('id')->toArray();
                $userDLS = array();

                if(($roleSlug == CANDIDATE_ROLE_SLUG || $roleSlug == INNOVATOR_ROLE_SLUG)  && $divisions) {   
                    
                    foreach ($divisions as $division) {
                        $userDLS[] = array(
                            'ref_type' => 'Impiger\MasterDetail\Models\Division', 
                            'ref_id' => $division, 
                            'ref_key' => getEntityId('divisions','module_db'),
                        );
                    }
                }

                if($roleSlug == SPOKE_STUDENT_ROLE_SLUG) {                    
                    $userDLS[] = array(
                        'ref_type' => 'Impiger\SpokeRegistration\Models\SpokeRegistration', 
                        'ref_id' => $request->input('spoke_registration_id'), 
                        // 'ref_key' => 'spoke_registration'
                        'ref_key' => getEntityId('spoke_registration','module_db'),
                    );
                }

                if($roleSlug == VENDOR_ROLE_SLUG || $roleSlug == MENTOR_ROLE_SLUG) {                    
                    $user->permissions = $role->permissions;
                    $user = $coreUserRepository->createOrUpdate($user);
                }

                if($userDLS) {
                    foreach ($userDLS as $dls) {
                        $cond = [
                            'user_id' => $user->id,
                            'reference_id' => $dls['ref_id'],
                            'reference_type' => $dls['ref_type'],
                            'reference_key' => $dls['ref_key'],
                            'role_id' => $role->id
                        ];
                        $existsEntity = \Impiger\ACL\Models\UserPermission::where($cond)->first();
                        $data = $cond;
                        $data['role_id'] = $role->id;
                        $data['role_permissions'] = $role->permissions;
                        if ($existsEntity) {
                            \Impiger\ACL\Models\UserPermission::where($cond)->update($data);
                        } else {
                            \Impiger\ACL\Models\UserPermission::create($data);
                        }
                    }
    
                    event(new \Impiger\ACL\Events\RoleAssignmentEvent($role, $user));  
                }
            }

            if ($isActivate) {
                $activateUserService->activate($user);
                // $impigerUser = $impigerUser::where('user_id',$user->id)->first();
                $user->domain_href = getUserDomainUrl($user->id);  
                $user->temp_password = $request['password'];
                CrudHelper::sendEmailConfig('user','{"create":"1","edit":null,"subject":"'.USER_LOGIN_CREDENTIALS_SUBJECT.'","message": "'.get_setting_email_template_content('core', 'base', 'user').'","send_to":"based_on_fields","reciever_role":null,"reciever_field":"email|contact_email","default_reciever":null}',$user);
            }

        }

        return $user;
    }


    public static function createCoreUserAndAssignRoleAndPermission($request, $coreUserRepository, $activateUserService = null, $roleSlug=null, $isActivate=true)
    {
        if(isset($request['isBulkUpload']) && $request['isBulkUpload']) {
            $data = $request;
        } else {
            $data = $request->input();
        }
        
        $coreUserExists = $coreUserRepository->getFirstBy(['email'=>$request['email']]);
        if(!$coreUserExists){
            if(isset($request['name']) && $request['name']) {
                $name = explode(' ', $data['name']);
                if($name && count($name) > 1) {
                    $data['first_name'] = current($name);
                    $data['last_name'] = end($name);
                    
                } else {
                    $data['first_name'] = $data['name'];
                }

            } else if(isset($request['first_name']) && $request['first_name']) {
                $data['first_name'] = $request['first_name'];
            }

            $lastName = (Arr::has($data,'second_name')) ? $data['secod_name'] : Arr::get($data,'last_name');

            if(Arr::has($data, 'second_name') && !Arr::has($data, 'last_name')) {
                $lastName = Arr::get($data,'second_name');
            }

            if(Arr::has($data, 'last_name')) {
                $lastName = Arr::get($data,'last_name');
            }

            if(Arr::has($data, 'first_name') && Arr::has($data, 'last_name')) {
                $userName = Arr::get($data,'first_name') .Arr::get($data,'last_name');
                $data['username'] = $userName;
            }
            $data['password'] = Hash::make($request['password']);
            $user = $coreUserRepository->createOrUpdate($data);
            $newUser = true;
        }else{
            $user = $coreUserExists;
            $newUser = false;
        }

        if($user && $newUser){
            if($roleSlug){
                $role = \Impiger\ACL\Models\Role::where('slug', $roleSlug)->whereNull('deleted_at')->first();
                $role->users()->attach($user->id);
                $divisions = \Impiger\MasterDetail\Models\Division::pluck('id')->toArray();
                $userDLS = array();

                if(($roleSlug == CANDIDATE_ROLE_SLUG || $roleSlug == INNOVATOR_ROLE_SLUG)  && $divisions) {   
                    
                    foreach ($divisions as $division) {
                        $userDLS[] = array(
                            'ref_type' => 'Impiger\MasterDetail\Models\Division', 
                            'ref_id' => $division, 
                            // 'ref_key' => 'divisions'
                            'ref_key' => getEntityId('divisions','module_db'),
                        );
                    }
                    
                }

                if($roleSlug == SPOKE_STUDENT_ROLE_SLUG) { 
                    /*
                    $userDLS = [
                        ['ref_type' => 'Impiger\HubInstitution\Models\HubInstitution', 'ref_id' => $request->input('hub_institution_id'), 'ref_key' => 'hub_institutions'],
                        ['ref_type' => 'Impiger\SpokeRegistration\Models\SpokeRegistration', 'ref_id' => $request->input('spoke_registration_id'), 'ref_key' => 'spoke_registration'],
                        ['ref_type' => 'Impiger\MasterDetail\Models\Division', 'ref_id' => $division->id, 'ref_key' => 'divisions']
                    ];            
                    */
                    $userDLS[] = array(
                        'ref_type' => 'Impiger\SpokeRegistration\Models\SpokeRegistration', 
                        'ref_id' => $data['spoke_registration_id'], 
                        // 'ref_key' => 'spoke_registration'
                        'ref_key' => getEntityId('spoke_registration','module_db'),
                    );
                    
                }

                // if($roleSlug == INNOVATOR_ROLE_SLUG) {

                //  }
                

                if($userDLS) {
                    foreach ($userDLS as $dls) {
                        $cond = [
                            'user_id' => $user->id,
                            'reference_id' => $dls['ref_id'],
                            'reference_type' => $dls['ref_type'],
                            'reference_key' => $dls['ref_key'],
                            'role_id' => $role->id
                        ];
                        $existsEntity = \Impiger\ACL\Models\UserPermission::where($cond)->first();
                        $data = $cond;
                        $data['role_id'] = $role->id;
                        $data['role_permissions'] = $role->permissions;
                        if ($existsEntity) {
                            \Impiger\ACL\Models\UserPermission::where($cond)->update($data);
                        } else {
                            \Impiger\ACL\Models\UserPermission::create($data);
                        }
                    }
    
                    event(new \Impiger\ACL\Events\RoleAssignmentEvent($role, $user));  
                }
            }

            if ($isActivate) {
                $activateUserService->activate($user);
                // $impigerUser = $impigerUser::where('user_id',$user->id)->first();
                $user->domain_href = getUserDomainUrl($user->id);  
                $user->temp_password = $request['password'];
                if(!isset($request['isBulkUpload'])) {
                    CrudHelper::sendEmailConfig('user','{"create":"1","edit":null,"subject":"'.USER_LOGIN_CREDENTIALS_SUBJECT.'","message": "'.get_setting_email_template_content('core', 'base', 'user').'","send_to":"based_on_fields","reciever_role":null,"reciever_field":"email|contact_email","default_reciever":null}',$user);
                }
            }

        }

        return $user;
    }

    public function checkAlreadySubscribedToEvent($id, Request $request, BaseHttpResponse $response) {
        $condition = array('user_id' => Auth::id());
        $entrepreneur = DB::table('entrepreneurs')->where($condition)->first();
        $trainee = '';
        if($entrepreneur) {
            $condition['entrepreneur_id'] = $entrepreneur->id;
            $condition['training_title_id'] = $id;
            // \Log::info("I am from checkAlreadySubscribedToEvent");
            $trainee = DB::table('trainees')->where($condition)->get();
            return ($trainee) ? $trainee->toArray() : [];                 
        } else {
            return $response->setError(true)
                ->setMessage("Required param missing");
        }
        
    }

    function getTrainings(Request $request, BaseHttpResponse $response) {
        return app(TrainingTitleInterface::class)->getInstitutionLists(0)->get()->toArray();
    }

    public function getTrainingsListGallery(Request $request, BaseHttpResponse $response)
    {
        $limit = (int)$request->input('paginate', 8);
        $trainings = app(TrainingTitleInterface::class)->getInstitutionLists($limit);
        return $response
            ->setData(view('training-title.training-list-gallery-view', compact('trainings'))->render());
    }
    
    public function subscribe(Request $request, BaseHttpResponse $response){
        $request->validate([
            'email_id' => 'required|email:filter|unique:newsletter_subscriptions,email_id'
        ]);
        $subscriptionModel = new \App\Models\NewsLetterSubscription;
        $input = [
            'email_id' => $request->input('email_id')
        ];
        $subscriptionModel->fill($input);
        if($subscriptionModel->save()){
            $request->session()->flash('success_msg', "Newsletter Subscription Successfully");
         
            return back();
        }else{
            $request->session()->flash('error_msg', "Newsletter Subscription Failed");
            return back();
        }
    }

    public static function getActionPlanBudget() {
        $model = app(\Impiger\AnnualActionPlan\Models\AnnualActionPlan::class)->getModel();
        $select = [
            DB::raw('SUM(annual_action_plan.total_budget) AS cnt'),
            DB::raw('SUM(FD.budget_approved) AS budget_approved'),
            // DB::raw('SUM(FD.budget_approved) AS cnt'),
            DB::raw('FY.session_year AS title'),
            DB::raw('FY.session_year AS label'),
        ];

        $query = $model->select($select)
            ->join(DB::raw('financial_year FY'), function ($join) {
                $join->on('FY.id', '=', 'annual_action_plan.financial_year_id');
            })
            ->join(DB::raw('training_title_financial_details FD'), function ($join) {
                $join->on('FD.financial_year_id', '=', 'FY.id');
                $join->on('FD.annual_action_plan_id', '=', 'annual_action_plan.id');
            })->groupBy(['annual_action_plan.financial_year_id']);
            // $query->dd();
        $query = apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, $select, false);
        return $query->get()->toArray();
    }


    public static function getActionPlanTotalBudget() {
        $model = app(\Impiger\FinancialYear\Models\FinancialYear::class)->getModel();
        $select = [
            DB::raw('IFNULL(SUM(AAP.total_budget),0) AS cnt'),
            DB::raw('IFNULL(SUM(FD.budget_approved),0) AS budget_approved'),
            // DB::raw('SUM(FD.budget_approved) AS cnt'),
            DB::raw('financial_year.session_year AS title'),
            DB::raw('financial_year.session_year AS label'),
        ];

        $query = $model->select($select)
            ->leftJoin(DB::raw('annual_action_plan AAP'), function ($join) {
                $join->on('AAP.financial_year_id', '=', 'financial_year.id');
            })
            ->leftJoin(DB::raw('training_title_financial_details FD'), function ($join) {
                $join->on('FD.financial_year_id', '=', 'financial_year.id');
                $join->on('FD.annual_action_plan_id', '=', 'AAP.id');
            })->groupBy(['financial_year.id']);
            // $query->dd();
        $query = apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, $select, false);
        return $query->get()->toArray();
    }

    

    public static function getEntrepreneurListByCandidateType() {
        // EXCLUDE_CANDIDATE_TYPE_SLUG
        $candidateTypes = \Impiger\MasterDetail\Models\MasterDetail::where(['attribute' => 'candidate_type'])->whereNotIn('slug', EXCLUDE_CANDIDATE_TYPE_SLUG)->pluck('id')->toArray();
        $model = app(\Impiger\Entrepreneur\Models\Entrepreneur::class)->getModel();
        $select = [
            DB::raw('COUNT(entrepreneurs.id) AS cnt'),
            'ao.name AS title',
            DB::raw("CONCAT('#','',LEFT(MD5(RAND()), 6)) AS color")
        ];
        $query = $model->select($select)->whereIN('entrepreneurs.candidate_type_id', $candidateTypes)->join(DB::raw('attribute_options ao'), function ($join) {
            $join->on('ao.id', '=', 'entrepreneurs.candidate_type_id');
        })
        ->groupBy(['entrepreneurs.candidate_type_id']);
        $query = apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, $select, false);
        // $query->dd();
        return $query->get()->toArray();
    }

    /*
    public static function getTotalMentorMentees() {
        $model = app(\Impiger\Entrepreneur\Models\Entrepreneur::class)->getModel();
        $query = DB::select("SELECT ((SELECT COUNT(`E`.`id`) FROM `entrepreneurs` `E`
JOIN `mentors` `MR` ON `MR`.`entrepreneur_id` = `E`.`id`) + 
(SELECT COUNT(`EE`.`id`) FROM `entrepreneurs` `EE` 
JOIN `mentees` `ME` ON `ME`.`entrepreneur_id` = `EE`.`id`)) AS `cnt`");
       
        // dd($query);
    }
    */

    public static function getTotalMentor() {
        $model = app(\Impiger\Entrepreneur\Models\Entrepreneur::class)->getModel();
        $select = [
            DB::raw('COUNT(entrepreneurs.id) AS cnt'),
            DB::raw("'Mentors' AS title"),
        ];
        $query = $model->select($select)->join(DB::raw('mentors MR'), function ($join) {
            $join->on('MR.entrepreneur_id', '=', 'entrepreneurs.id');
        });
        $query = apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, $select, false);
        // $query->dd();
        return $query->get()->toArray();
    }
    public static function getTotalMentees() {
        $model = app(\Impiger\Entrepreneur\Models\Entrepreneur::class)->getModel();
        $select = [
            DB::raw("COUNT(entrepreneurs.id) AS cnt"),
            DB::raw("'Mentees' AS title"),
        ];
        $query = $model->select($select)->join(DB::raw('mentees ME'), function ($join) {
            $join->on('ME.entrepreneur_id', '=', 'entrepreneurs.id');
        });
        $query = apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, $select, false);
        // $query->dd();
        return $query->get()->toArray();
    }
    public static function getTotalVendor() {
        $model = app(\Impiger\Vendor\Models\Vendor::class)->getModel();
        $select = [
            DB::raw("COUNT(vendors.id) AS cnt"),
            DB::raw("'Vendors' AS title"),
        ];
        $query = $model->select($select);
        $query = apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, $select, false);
        // $query->dd();
        return $query->get()->toArray();
    }

    public static function getTotalTrainingPrograms() {
        $model = app(\Impiger\TrainingTitle\Models\TrainingTitle::class)->getModel();
        $select = [
            DB::raw("COUNT(training_title.id) AS cnt"),
            DB::raw("'Training Programs' AS title"),
        ];
        $query = $model->select($select)->whereRaw('fee_paid IS NOT NULL AND fee_paid != ""')->where(['FY.is_running'=>1, 'FY.is_enabled'=>1])
        ->join(DB::raw('financial_year FY'), function ($join) {
            $join->on('FY.id', '=', 'training_title.financial_year_id');
        });
        $query = apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, $select, false);
        // $query->dd();
        return $query->get()->toArray();
    }
    public static function getTotalFreeTrainingPrograms() {
        $model = app(\Impiger\TrainingTitle\Models\TrainingTitle::class)->getModel();
        $select = [
            DB::raw("COUNT(training_title.id) AS cnt"),
            DB::raw("'Free Programs' AS title"),
        ];
        $query = $model->select($select)->where('fee_paid', 1)->where(['FY.is_running'=>1, 'FY.is_enabled'=>1]) ->join(DB::raw('financial_year FY'), function ($join) {
            $join->on('FY.id', '=', 'training_title.financial_year_id');
        });
        $query = apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, $select, false);
        // $query->dd();
        return $query->get()->toArray();
    }
    public static function getTotalPaidTrainingPrograms() {
        $model = app(\Impiger\TrainingTitle\Models\TrainingTitle::class)->getModel();
        $select = [
            DB::raw("COUNT(training_title.id) AS cnt"),
            DB::raw("'Paid Programs' AS title"),
        ];
        $query = $model->select($select)->where('fee_paid', 2)->where(['FY.is_running'=>1, 'FY.is_enabled'=>1])
        ->join(DB::raw('financial_year FY'), function ($join) {
            $join->on('FY.id', '=', 'training_title.financial_year_id');
        });
        $query = apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, $select, false);
        // $query->dd();
        return $query->get()->toArray();
    }

    public static function getCertificateCompleted() {
        // Impiger\Entrepreneur\Models\Trainee
        $model = app(\Impiger\Entrepreneur\Models\Trainee::class)->getModel();
        $select = [
            DB::raw("COUNT(trainees.id) AS cnt"),
            DB::raw("'Certificate Completed' AS title"),
        ];
        $query = $model->select($select)->where('certificate_status', 1);
        $query = apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, $select, false);
        // $query->dd();
        return $query->get()->toArray();
    }

    public static function getCurrentYearTotalTrainingPrograms() {
        $model = app(\Impiger\TrainingTitle\Models\TrainingTitle::class)->getModel();
        $select = [
            DB::raw('COUNT(training_title.id) AS cnt'),
            DB::raw("'Total Programs' AS title"),
        ];

        $query = $model->select($select)->where(['FY.is_running'=>1, 'FY.is_enabled'=>1])
            ->join(DB::raw('financial_year FY'), function ($join) {
                $join->on('FY.id', '=', 'training_title.financial_year_id');
            });
            
        $query = apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, $select, false);
        // $query->dd();
        return $query->get()->toArray();
    }
    public static function getCurrentYearTotalCompletedTrainingPrograms() {
        $model = app(\Impiger\TrainingTitle\Models\TrainingTitle::class)->getModel();
        $select = [
            DB::raw('COUNT(training_title.id) AS cnt'),
            DB::raw("'Completed Programs' AS title"),
        ];

        $query = $model->select($select)->where(['FY.is_running'=>1, 'FY.is_enabled'=>1])->whereRaw('DATE(training_title.training_start_date) < CURDATE()')->whereRaw('DATE(training_title.training_end_date) <= CURDATE()')
            ->join(DB::raw('financial_year FY'), function ($join) {
                $join->on('FY.id', '=', 'training_title.financial_year_id');
            });
            
        $query = apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, $select, false);
        // $query->dd();
        return $query->get()->toArray();
    }
    public static function getCurrentYearTotalUpcomingTrainingPrograms() {
        $model = app(\Impiger\TrainingTitle\Models\TrainingTitle::class)->getModel();
        $select = [
            DB::raw('COUNT(training_title.id) AS cnt'),
            DB::raw("'Upcoming Programs' AS title"),
        ];

        $query = $model->select($select)->where(['FY.is_running' => 1, 'FY.is_enabled'=>1])->whereRaw('DATE(training_title.training_end_date) >= CURDATE()')
            ->join(DB::raw('financial_year FY'), function ($join) {
                $join->on('FY.id', '=', 'training_title.financial_year_id');
            });
            
        $query = apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, $select, false);
        // $query->dd();
        return $query->get()->toArray();
    }
    
    public function getHubsByRegion(Request $request, BaseHttpResponse $response){
        
        $regionId = $request->input('region_id');

        if (!$regionId ) {
            return $response
                ->setError(true)
                ->setMessage('Required param missing.');
        }

        $select =['hub_institutions.name as text','hub_institutions.id'];
        $query = DB::table('hub_institutions')->select($select)
                                ->join('district AS D','D.id','=','hub_institutions.district')
                                ->join('regions AS R','R.id','=','D.region_id')
                                ->where('R.id',$regionId)
                                ->orderBy('text');

        $model = get_model_from_table('hub_institutions');

        if ($model) {
            $model = new $model();
            $query = apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model,$select , false);
        }
        return $query->get()->toArray();
    }
    
    function getAcademicOptions(Request $request, BaseHttpResponse $response)
    {
        $academicOption = $request->input('academic_option');
       
        if (!$academicOption) {
            return $response
                ->setError(true)
                ->setMessage('Required param missing.');
        }

        $config = array('hub_institution_id' => array('fields' => ['spoke_registration.id', 'name AS text'], 'table' => 'spoke_registration', 'cndn' => ['spoke_registration.hub_institution_id' => $request->input('hub_institution_id')]),
                        
                    );
        
       
       
        $lookupTable = Arr::get($config, $academicOption.'.table');
        $cndns = Arr::get($config, $academicOption.'.cndn');
        $fields = (Arr::has($config, $academicOption.'.fields')) ? Arr::get($config, $academicOption.'.fields') : [$lookupTable.'.id', $lookupTable.'.name AS text'];
        $model = get_model_from_table($lookupTable);
//        dd($lookupTable,$cndns,$fields,$model);
        if ($model) {
            $model = new $model();
            $query = $model::select($fields);
            $query = apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, $fields, false);
        
        } else {
            $query = DB::table($lookupTable)->select($fields);

            if (Schema::hasColumn($lookupTable, 'deleted_at')) {
                $query = $query->whereNull($lookupTable . '.deleted_at');
            }
    
            if (Schema::hasColumn($lookupTable, 'is_enabled')) {
                $query = $query->where($lookupTable . '.is_enabled', 1);
            }
        }

      
       $query = $query->where($cndns)->orderBy('text');
        
        $query = $query->groupBy($lookupTable.'.id');
        return $query->get()->toArray();
    }
    /* route functions ends here */
    public static function customUniqueValidation($rules,$ids,$userIds = null){
        if (!$rules) {
            return "";
        }
        $result = [];
        $table = "";
        $rulesSplit = explode("|", $rules);
    
        foreach ($rulesSplit as $rule) {
       if (Str::startsWith($rule, "unique:")) {
                $slice = Str::after($rule, "unique:");
                $columns = explode(",", $slice);
                if (Arr::has($columns, 0)) {
                    $table = $columns[0];
                    array_shift($columns);//if(Str::contains($rules,'email_id') && Str::contains($slice,'users')){dd($columns);}
                    $result = \Illuminate\Validation\Rule::unique($table,$columns[0])
                        ->where(function ($whr) use ($ids,$table,$userIds) {
                            if(isValidArray($userIds) && $table == 'users'){
                                $whr->whereNotIn($table.'.id', $userIds);
                            }else{
                                if(!empty($ids)){
                                    $whr->whereNotIn($table.'.id', $ids);
                               }
                            }
                            
                        });
                }
            }else {
                $result[] = $rule;
            }
        }
        return $result;
    }

    public function getEntrepreneursListBySearch(Request $request, BaseHttpResponse $response)
    {         
        $param = $request->input('param');
        if(is_plugin_active('entrepreneur')){

            $fields = ['id','name','email',DB::raw('CONCAT(`name`," - ",`email`) AS text')];
            $entrepreneur = \Impiger\Entrepreneur\Models\Entrepreneur::select($fields)->whereRaw('id NOT IN (SELECT entrepreneur_id FROM mentors)')->whereNotNull('name');
            $search = $search1 = array();
            if ($request->has('param')) {
                $search = $entrepreneur;
                $search = $search->where('name','like', '%'.$param.'%')->orWhere('email','like', '%'.$param.'%');

            } else {
                $search = $entrepreneur->limit(50)->orderBy("id", "DESC");
            }

            $search = $search->get()->toArray();
            
            if(empty($search)) {
                return $response->setError(true)->setMessage('No data found for the param '.$param);
            }

            if($request->has('id')) {
                $search1 = \Impiger\Entrepreneur\Models\Entrepreneur::select($fields);
                $whereKey = 'id';
                $keyVal = $request->get('id');
                $search1 = $search1->where($whereKey, $keyVal)->get()->toArray();
                // \Log::info($search1);
            }

            $data = array_merge($search, $search1);
            
            // $entrepreneurDetails[] = [
            //     'id' => $entrepreneur->id,
            //     'name' => $entrepreneur->name,
            //     'doemailb' => $entrepreneur->email,
            //     'name_email' => $entrepreneur->name_email,
            // ];
            // return $entrepreneurDetails;
            return $data;
        }        
    }

    function getEntrepreneurById(Request $request){

        $entrepreneurModel = app(\Impiger\Entrepreneur\Models\Entrepreneur::class)->getModel();
        $whereKey = '';
        if($request->get('id')) {
            $whereKey = 'id';
            $keyVal = $request->get('id');
        
            $fields = ['id','name','email',DB::raw('CONCAT(`name`," - ",`email`) AS text')];

            return $entrepreneurModel->select($fields)->where($whereKey, $keyVal)->get()->toArray();
        }
                
    }
}



<?php

namespace Impiger\Crud\Imports;

use Impiger\Department\Models\Department;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithValidation;
use Excel;
use DB;
use Impiger\Language\Models\LanguageMeta;
use Language;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Utils\CrudHelper;

/**
 * Description of BulkImport
 *
 * @author sabarishankar.parthi
 */
class BulkImport implements ToModel,WithHeadingRow,WithBatchInserts,WithValidation
{
    /**
     * @var Model
     */
    protected $model;
    protected $rows=0;
    
    public function __construct($model) {
        $this->model = get_class($model);
    }

    /*
    public function model(array  $rows)
    {
      $result=[]; 
      ++$this->rows;
      if($this->isSupportedModule($this->model)){
          switch($this->model){
              case 'Impiger\Institution\Models\Institution': 
                    $data = $this->getInstitutionDetails($rows);
                    $result = $this->model::create($data);
                    break;             
              
          }
          $this->addLanguageMeta($result);
      } 
    }
    */

    public function model(array $rows) {
        $result = [];
       
        ++$this->rows;
        if ($this->isSupportedModule($this->model)) {
            $data = $this->getDetails($rows);
            if (Arr::has($data, 'id')) {
                $result = $this->model::where(['id' => $data['id']])->update($data);
            } else {
                $result = $this->model::create($data);
            }
            $this->addLanguageMeta($result);
        }
    }

    public function batchSize(): int
    {
        return 1000;
    }
    
    public function getRowCount(): int
    {
        return $this->rows;
    }
    
    /*
    public function rules(): array
    {
         if($this->isSupportedModule($this->model)){
          switch($this->model){
               case 'Impiger\Institution\Models\Institution': 
                    return $this->getInstitutionDetails();
                    break;             
          }
      }
      return [];
        
    }
    */

    public function rules(): array {
        if ($this->isSupportedModule($this->model)) {
                return $this->getDetails();
        }
        return [];
    }

    public function customValidationMessages() {
        $moduleConfig = config('plugins.crud.importConfigs.' . $this->model, []);
        return $this->getCustomValidationMessages($moduleConfig, $this->model);
    }
        
   
    protected function isSupportedModule($model){
        $modelObj = new $model;
        $table = $modelObj->getTable();
        $supportedModule=DB::table("cruds")->where("module_db",$table)->where("is_bulkupload",1)->get();
        if (count($supportedModule)>0) {
            return true;
        }
        return false;
    }
    
    protected function addLanguageMeta($reference){
        $modelObj = new $this->model;
        $table = $modelObj->getTable();
        $supportedModule=DB::table("cruds")->where("module_db",$table)->where("is_multi_lingual",1)->get();
        if (count($supportedModule)>0) {
            $currentLanguage = Language::getCurrentAdminLocaleCode();
            $originValue = null;

                if ($currentLanguage !== 'en_US') {
                    $originValue = LanguageMeta::where([
                        'reference_id'   => $reference->id,
                        'reference_type' => $this->model,
                    ])->value('lang_meta_origin');
                }

                LanguageMeta::saveMetaData($reference, $currentLanguage, $originValue);
        }
        return false;
    }
    
    /**
     * Transform a date value into a Carbon object.
     *
     * @return \Carbon\Carbon|null
     */
    public function transformDate($value, $format = 'Y-m-d') {
        try {
            return Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value));
        } catch (\ErrorException $e) {
            return Carbon::parse($value)->format($format);
        }
    }
    
    public function transformTime($value, $format = 'h:i:s') {
        try {
            return Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value));
        } catch (\ErrorException $e) {
            return Carbon::parse($value)->format($format);
        }
    }
    
    protected function getInstitutionDetails($data = []){
        $result = [];
        if(!empty($data)){
           $result =  $data;
           $result['institute_type'] = getIdfromValue("attribute_options", ['name' => $data['institute_type']]);
           $result['institute_category'] = getIdfromValue("attribute_options", ['name' => $data['institute_category']]);
           $result['registration_date'] = Carbon::parse($data['registration_date'])->format('Y-m-d');
           $result['sector_id'] = getIdfromValue("attribute_options", ['name' => $data['sector']]);
           $result['domain_id'] = getIdfromValue("multidomains", ['name' => $data['domain']]);
           $result['country_id'] = getIdfromValue("countries", ['country_name' => $data['country']]);
           $result['district'] = getIdfromValue("district", ['name' => $data['district']]);
           $result['county'] = getIdfromValue("county", ['name' => $data['county']]);
           $result['subcounty'] = getIdfromValue("subcounty", ['name' => $data['subcounty']]);
           $result['parish'] = getIdfromValue("parish", ['name' => $data['parish']]);
           $result['village'] = getIdfromValue("village", ['name' => $data['village']]);
           if(isset($data['licensed_date']) && $data['licensed_date']!="0000-00-00"){
               $renewalDate = Carbon::parse($data['licensed_date'])->addYears(2);
               $result['renewal_date'] = $renewalDate->format('Y-m-d');
           }
           if(isset($result['renewal_date']) && $result['renewal_date']!="0000-00-00"){
               $expiryDate = Carbon::parse($data['renewal_date'])->addYears(2);
               $result['expiry_date'] = $expiryDate->format('Y-m-d');
           }
           return $result;
        }else{
            $instituteRequest = new \Impiger\Institution\Http\Requests\InstitutionRequest;
            $institueRules = $instituteRequest->rules();
            $institueRules['institute_code'] = "required|unique:institutions,institute_code,";
            $institueRules['registration_no'] = "required|unique:institutions,registration_no,";
            $institueRules['institute_type'] = "required|exists:attribute_options,name";
            $institueRules['institute_category'] = "required|exists:attribute_options,name";
            $institueRules['sector'] = "required|exists:attribute_options,name";
            $institueRules['country'] = "required|exists:countries,country_name";
            $institueRules['district'] = "required|exists:district,name";
            $institueRules['county'] = "required|exists:county,name";
            $institueRules['subcounty'] = "required|exists:subcounty,name";
            $institueRules['parish'] = "required|exists:parish,name";
            $institueRules['village'] = "required|exists:village,name";
            $institueRules['accreditated'] = "required";
            Arr::forget($institueRules, ['logo','domain_id','sector_id','country_id','is_accreditated','phone_code']);
            return $institueRules;  
            
        }
    }

    public function getDetails($data = []) {
        $result = $condn = [];
        $moduleConfig = config('importConfigs.' . $this->model, []);
        $moduleObj = new $this->model;
        if ($moduleConfig) {
            if (!empty($data)) {
                if (Arr::has($data, 2) && is_array($data[2])) {
                    foreach ($data as $row) {
                        $result = $this->getRowData($this->model,$moduleConfig, $row,true);
                    }
                } else {
                    $result = $this->getRowData($this->model,$moduleConfig, $data);
                }
                return $result;
            } else {
                $validationRules = [];
                $requestClass = getRequestClass($this->model);
                if ($requestClass && class_exists($requestClass)) {
                    $request = new $requestClass;
                    $rules = $request->rules();
                    if ($rules) {
                        foreach ($moduleConfig['fields'] as $field => $config) {
                            if (Arr::has($rules, $field)) {
                                $validationRules[$config['field']] = $rules[$field];
                            }
                        }
                    }
                }
                //dd($validationRules);
                return $validationRules;
            }
        }
    }
    
    public function getRowData($model,$moduleConfig,$data,$isValidation = false){
        $result = [];
        $parent_module = Arr::get($moduleConfig, 'parent_module');
        $modelObj = ($parent_module) ? new $parent_module : new $this->model;
        $moduleObj = new $model;//dd($moduleConfig);
        if (Arr::has($moduleConfig, 'reference_keys') && Arr::has($moduleConfig, 'reference_field')) {
            $mainModule = $modelObj::where($moduleConfig['reference_keys'][0], trim($data[$moduleConfig['reference_keys'][1]]))->first();
            if (!$mainModule) {
                return [];
            }
            $result[$moduleConfig['reference_field']] = ($mainModule) ? $mainModule->id : null;
            $condn[$moduleConfig['reference_field']] = $result[$moduleConfig['reference_field']];
        }
        //echo "<pre>";
        //print_r($data);
        //print_r($moduleConfig['fields']);
        //echo "</pre>";
        //die();
        foreach ($moduleConfig['fields'] as $field => $config) {
            //echo "<br/>";
            //echo $field;
            //echo "<br/>";
            //print_r($config);
            $result[$field] = "";
            if(isset($data[$config['field']]))
            {
                if (Arr::get($config, 'dependant')) {
                    $whrCondn = [$config['dependant'][1] => $data[$config['field']]];
    				if(Arr::get($config,'whrcond') && is_array(Arr::get($config,'whrcond'))){
    				   $whrCondn = array_merge($whrCondn,$config['whrcond']); 
    				}
    				$result[$field] = getIdfromValue($config['dependant'][0], $whrCondn);
    				//echo "1";
                }else if(Arr::has($config, 'callback') && method_exists($this,$config['callback'])){
    				//echo "2";
    				$parameters = $config['parameters'];
    				$result[$field] = ($parameters) ? call_user_func_array([$this, $config['callback']],[$data[$parameters[0]],$result[$parameters[1]]]): call_user_func([$this,$config['callback']]);
    			} else if (Arr::has($config, 'type') && Arr::get($config, 'type') == 'date') {
    				//echo "3";
                    $result[$field] = ($data[$config['field']]) ? $this->transformDate($data[$config['field']]) : NULL;
                } else if (Arr::has($config, 'type') && in_array(Arr::get($config, 'type') , ['checkbox','radio'])) {
    				//echo "4";
    				$result[$field] = (in_array($data[$config['field']],['Yes','yes','Y','y'])) ? 1 : 0;
    			}else if(Arr::has($config, 'field') && Arr::get($config,'field') == 'gender'){
    				//echo "5";
    				$result[$field] = (in_array($data[$config['field']],['Male','M','male','m'])) ? 'male' : "";
    				$result[$field] = (in_array($data[$config['field']],['Female','F','female','f'])) ? 'female' : $result[$field];
    			} else {
    				//echo "6";
                    $result[$field] = $data[$config['field']];
                }
                if (Arr::get($config, 'filter')) {
                    $condn[$field] = $result[$field];
                }
            }
        }
        //echo "<pre>";
        //print_r($result);
        //print_r($isValidation);
        //echo "</pre>";
        //die();
        if (!empty($condn)) {
            $exists = $moduleObj::where($condn)->first();
            if ($exists) {
                if(Arr::has($moduleConfig, 'action') && Arr::get($moduleConfig, 'action') == 'coreUser'){
                    $result['user_id'] = $exists->user_id;
                }
                $result['id'] = $exists->id;
            }
        }
        if(!$isValidation && Arr::has($moduleConfig, 'action')){
            if(empty($result['user_id']) && Arr::get($moduleConfig, 'action') == 'coreUser'){
                $coreUser = $this->createCoreUser($result,Arr::get($moduleConfig, 'role_slug'));
                $result['user_id'] = $coreUser->id;
            }
        }
        //echo "<pre>";
        //print_r($result);
        //echo "</pre>";
        //die();
        //dd($result);
        return $result;
    }

    public function getCustomValidationMessages($moduleConfig,$model) {
        $result = $condn = [];
        $moduleObj = new $model;
        $customMessages = [];
        if ($moduleConfig) {
            $requestClass = getRequestClass($model);
            if ($requestClass && class_exists($requestClass)) {
                $request = new $requestClass;
                $messages = $request->messages();
                if ($messages) {
                    foreach ($moduleConfig['fields'] as $field => $config) {
                        foreach($messages as $key => $msg){
                            $keys = explode(".",$key);
                            if (Str::contains($keys[0], $field)) {
                                $customMessages[$config['field'].'.'.$keys[1]] = $msg;
                            }
                        }
                    }
                }
                if (Arr::has($moduleConfig, 'reference_keys')) {
                    $customMessages[$moduleConfig['reference_keys'][1].'.required'] = 'The '.$moduleConfig['reference_keys'][0].' field is required and it should be same in basic form';
                }
            }
        }
//        dd($customMessages);
        return $customMessages;
    }

    public function createCoreUser($data, $roleSlug = null)
    {
        /**
         * @var User $user
         */
        $attOption = \Impiger\MasterDetail\Models\MasterDetail::whereIn('attribute',["candidate_type"])->pluck('id','slug')->toArray();
        $coreUserRepository = app(\Impiger\ACL\Repositories\Interfaces\UserInterface::class);
        $activateUserService = app(\Impiger\ACL\Services\ActivateUserService::class);
        $candidate_type_id = Arr::get($data, 'candidate_type_id');
        // Arr::get($attOption, 'spokestudent-candidate-type');
        if($candidate_type_id && $candidate_type_id == Arr::get($attOption, 'spokestudent-candidate-type')) {
            $roleSlug = SPOKE_STUDENT_ROLE_SLUG;
        }
        
        if(isset($data['password']) && $data['password']) {
            $randomPassword = Arr::get($data,'password');
        } else {
            $randomPassword = CrudHelper::randomPassword();
            // $data['password'] = $randomPassword;
        }

        $data['isBulkUpload'] = 1;

        // ParameterBag
        // $request = new \Illuminate\Http\Request($data);
        // $request = new \Impiger\Support\Http\Requests\Request($data);


        /*
        $userName = Arr::get($data,'first_name') .Arr::get($data,'last_name');
        $user = new \Impiger\ACL\Models\User;
        $user->email = $data['email'];
        $user->username = $userName;
        $user->password = \Hash::make($randomPassword);
        // $user->first_name = $data['first_name'];

        
        if(isset($data['name']) && $data['name']) {
            // $user->first_name = $data['name'];
            $name = explode(' ', $data['name']);
            if($name && count($name) > 1) {
                $data['first_name'] = current($name);
                $data['last_name'] = end($name);
                $user->first_name = Arr::get($data,'first_name');
            } else {
                $user->first_name = $data['name'];
            }                  
        } else if(isset($data['first_name']) && $data['first_name']) {
            $user->first_name = $data['first_name'];
        }
        $lastName = (Arr::has($data,'second_name')) ? $data['secod_name'] : Arr::get($data,'last_name');
        $user->last_name = $lastName . ((Arr::get($data,'other_name')) ? " " . Arr::get($data,'other_name') : "");
        $user->save();
        $role = null;
        // activate the user
        app(\Impiger\ACL\Services\ActivateUserService::class)->activate($user);
        if($roleSlug){
            $role = \Impiger\ACL\Models\Role::where('slug', $roleSlug)->first();
        }

        if($role) {
            event(new \Impiger\ACL\Events\RoleAssignmentEvent($role, $user));
            $role->users()->attach($user->id);
        }

        $user->domain_href = getUserDomainUrl($user->id);  
        $user->temp_password = $data['password'];
        CrudHelper::sendEmailConfig('user','{"create":"1","edit":null,"subject":"'.USER_LOGIN_CREDENTIALS_SUBJECT.'","message": "'.get_setting_email_template_content('core', 'base', 'user').'","send_to":"based_on_fields","reciever_role":null,"reciever_field":"email|contact_email","default_reciever":null}',$user);
        */
        
        $user = CrudHelper::createCoreUserAndAssignRoleAndPermission($data, $coreUserRepository, $activateUserService, $roleSlug, true);
        return $user;
    }
    
}

<?php

namespace Impiger\Crud\Imports;

use Impiger\Department\Models\Department;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
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
use Maatwebsite\Excel\Validators\Failure;
use Exception;
/**
 * Description of BulkImport
 *
 * @author sabarishankar.parthi
 */
class ImportSheet extends ImportMultiSheet implements ToModel,SkipsEmptyRows, WithHeadingRow, WithBatchInserts, WithValidation {

    /**
     * @var Model
     */
    public $model;
    public $subModule; 
    public $firstRow;

    public function __construct($model, $subModule) {
        $this->model = $model;
        $this->subModule = $subModule;
    }

    public function model(array $rows) {
        $result = [];
        ++$this->rows;
        if ($this->isSupportedModule($this->model)) {
            
            $data = $this->getDetails($rows);
            
            $subModuleObj = new $this->subModule;
            

            try {
                \DB::beginTransaction();
                if (!empty($data)) {
                    
                    if (Arr::has($data, 'id')) {
                        $result = $this->subModule::where(['id' => $data['id']])->update($data);
                        
                    } else {
                        
                        $result = $this->subModule::create($data);
                    }
                    DB::commit();
                    
                    //echo "<pre>";
                    //print_r($data);
                    //print_r($result);
                    //echo "</pre>";
                    //die();
                }
            } catch(Exception $e) {
                DB::rollBack();
                $error = getCustomErrorMessage($e->errorInfo);
                //  dd($e);
                $failures[] = new Failure($this->rows, '', $error, $rows);
                
                throw new \Maatwebsite\Excel\Validators\ValidationException(
                
                    \Illuminate\Validation\ValidationException::withMessages($error),
                
                    $failures
                
                );
                return $e;

            }
        }
    }
    
    public function headingRow(): int
    {
        $this->firstRow = 1;
        if($this->model == 'Impiger\Entrepreneur\Models\Trainee')
            $this->firstRow = 2;
            
        return $this->firstRow;
    }

    public function batchSize(): int {
        return 1000;
    }

    public function rules(): array {
        if ($this->isSupportedModule($this->model)) {
                   return $this->getDetails();
        }
        return [];
    }

       /**
     * @return array
     */
    public function customValidationMessages() {
        $moduleConfig = config('importConfigs.' . $this->model, []);
        if ($moduleConfig) {
            $subModuleConfig = $moduleConfig[$this->subModule];
            return $this->getCustomValidationMessages($subModuleConfig, $this->subModule);
        }
        
    }
   
    public function getDetails($data = []) {
        $result = $condn = [];
        $moduleConfig = config('importConfigs.' . $this->model, []);
        //echo "<pre>";
        //print_r($data);
        //echo "</pre>";
        //die();
        $array_index = $this->firstRow + 1;
        $replace_array_key = ["email_id" => "email", "contact" => "mobile", "qualification" => "student_type", "hub_institution" => "hub_institution", "student_year" => "student_year", "spoke_college_name" => "spoke_college_name"];
        if ($moduleConfig) {
            $subModuleConfig = $moduleConfig[$this->subModule];
            if ($subModuleConfig) {
                if (!empty($data)) {//dd($data);
                    $subModuleObj = new $this->subModule;
                    if(Arr::has($data,$array_index) && is_array($data[$array_index])){
                        foreach($data as $key => $row){
                            foreach($replace_array_key as $current_key => $replace_key)
                            {
                                if(isset($row[$current_key]) && $row[$current_key] != "")
                                {
                                    $row[$replace_key] = $row[$current_key];
                                }
                                if(!isset($row[$replace_key]))
                                {
                                    $row[$replace_key] = "";
                                }
                            }
                            $result[$key] = $this->getRowData($this->subModule,$subModuleConfig,$row,true);
                        }                        
                    }else{
                        $result = $this->getRowData($this->subModule,$subModuleConfig,$data);
                    }
                    //echo "<pre>";
                    //print_r($result);
                    //echo "</pre>";
                    //die();
                    return $result;
                } else {
                    $validationRules = [];
                    $requestClass = getRequestClass($this->subModule);
                    if ($requestClass && class_exists($requestClass)) {
                        $request = new $requestClass;
                        $rules = $request->rules();
                        if ($rules) {
                            foreach ($subModuleConfig['fields'] as $field => $config) {
                                if(Arr::has($rules,$field)){
                                    $rule = $this->customizedValidation($rules[$field],$config);
                                    if(is_array($rule) && $field ='training_title_id' ){
                                        if(Arr::has($rule,1)){
                                            $rule = 'required';
                                        }
                                    }
                                   //$field = ($field ='training_title_id') ? "training_title" : $field;
                                    $fieldName = (is_array($rule) || (!is_array($rule) && Str::contains($rule, ['unique'])) ) ? $field : $config['field'];
                                    $validationRules[$fieldName] = $rule;
                                }                                
                            }
                            if(Arr::has($subModuleConfig,'reference_keys')){
                                $validationRules[$subModuleConfig['reference_keys'][1]] = 'required';
                            }
                        }
                    }
                    //  dd($validationRules);
                    return $validationRules;
                }
            }
        }
    }
    public function customizedValidation($rule,$config){
        $customRule=$rule;
        if(is_array($rule)){
            return $customRule;
        }
        
        if(Str::contains($rule, ['|date','|after'])){
            $customRule = (Str::contains($rule, ['required'])) ? 'required' : "";
        }else if(Arr::has($config,'required_if') && Str::contains($rule, [$config['required_if']])){
            $customRule = str_replace($config['required_if'],$config['replace_if'],$rule);
        }
        return $customRule;
    }
}

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
use Maatwebsite\Excel\Concerns\WithStartRow;
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
class ImportSheetNew extends ImportMultiSheetNew implements ToModel,SkipsEmptyRows, WithHeadingRow, WithBatchInserts, WithValidation {

    /**
     * @var Model
     */
    public $model;
    public $subModule;
    public $firstRow;
    public $user_img_arr;

    public function __construct($model, $subModule, $user_img_arr) {
        $this->model = $model;
        $this->subModule = $subModule;
        $this->user_img_arr = $user_img_arr;
    }

    public function model(array $rows) {
        $result = $trainee_data = [];
        
        $user_img_arr = $this->user_img_arr;

        //echo "<pre>";
        //print_r($rows);
        $data = $this->getDetails($rows);
        //print_r($user_img_arr);
        //print_r($data);
        try {
            DB::beginTransaction();
            if (!empty($data)) {
                $scheme = DB::table('attribute_options')->where('name', '=', $rows['candidate_type'])->whereNull('deleted_at')->first();
                //print_r($scheme);
                if (!empty($scheme)) {
                    $data['scheme'] = $scheme->id;
                }
                if(isset($rows['programmedatefrom']) && $rows['programmedatefrom'] != "" && isset($rows['programmedateto']) && $rows['programmedateto'] != "")
                {
                    $programmedatefrom = $this->transformDate($rows['programmedatefrom']);
                    $programmedateto = $this->transformDate($rows['programmedateto']);
                    $find_training = DB::table('training_title')->where('code', 'LIKE', "%".$rows['progrmme']."%")->whereDate('training_start_date', '=', $programmedatefrom)->whereDate('training_end_date', '=', $programmedateto)->first();
                }
                elseif(isset($rows['programmedate_form']) && $rows['programmedate_form'] != "" && isset($rows['programmedate_to']) && $rows['programmedate_to'] != "")
                {
                    $programmedatefrom = $this->transformDate($rows['programmedate_form']);
                    $programmedateto = $this->transformDate($rows['programmedate_to']);
                    $find_training = DB::table('training_title')->where('code', 'LIKE', "%".$rows['progrmme']."%")->whereDate('training_start_date', '=', $programmedatefrom)->whereDate('training_end_date', '=', $programmedateto)->first();
                }
                if(!isset($data['qualification_id']) || (isset($data['qualification_id']) && $data['qualification_id'] == ""))
                {
                    $find_qualification = DB::table('qualifications')->where('name', '=', 'Others')->whereNull('deleted_at')->first();
                    if (!empty($find_qualification)) {
                        $data['qualification_id'] = $find_qualification->id;
                    }
                }
                if(isset($data['mobile']) && $data['mobile'] != "" && !is_numeric($data['mobile']))
                {
                    $data['mobile'] = (isset($rows['password']) ? $rows['password'] : NULL);
                }
                if(isset($data['email']) && $data['email'] != "" && isset($data['mobile']) && $data['mobile'] != "")
                {
                    if(isset($data['email']) && $data['email'] != "" && isset($user_img_arr[$data['email']]) && $user_img_arr[$data['email']] != "")
                    {
                        $img_val = $user_img_arr[$data['email']];
                        $extension = ".png";
        				if(strpos($img_val, "image/jpeg") > -1)
        				{
        					$extension = ".jpg";
        				}
        				$user_image = preg_replace("/[^A-Za-z0-9]/", '-', strtolower($rows['sl_no']."-".$data['name']))."-".time();
        				$data['photo_path'] = $user_image.$extension;
                        //$data['excel_img'] = $user_img_arr[$data['email']];
                    }
                    if(isset($data['photo_path']) && $data['photo_path'] == '')
                    {
                        unset($data['photo_path']);
                    }
                    $find_msme = DB::table('msme_candidate_details')->where('email', '=', $data['email'])->whereNull('deleted_at')->first();
                    $msme_data = [
                        'scheme' => (isset($scheme->id) ? $scheme->id : NULL),
                        'candidate_msme_ref_id' => (isset($rows['progrmme']) ? $rows['progrmme'] : NULL),
                        'candidate_name' => (isset($data['name']) ? $data['name'] : NULL),
                        'care_of' => (isset($data['care_of']) ? $data['care_of'] : NULL),
                        'father_husband_name' => (isset($data['father_name']) ? $data['father_name'] : NULL),
                        'gender' => (isset($data['gender_id']) ? $data['gender_id'] : NULL),
                        'category' => (isset($data['community']) ? $data['community'] : NULL),
                        'mobile_no' => (isset($data['mobile']) ? $data['mobile'] : NULL),
                        'email' => (isset($data['email']) ? $data['email'] : NULL),
                        'dob' => (isset($data['dob']) ? $data['dob'] : NULL),
                        'qualification' => (isset($rows['qualification']) ? $rows['qualification'] : NULL),
                        'district_id' => (isset($data['district_id']) ? $data['district_id'] : NULL),
                        'photo' => (isset($data['photo_path']) && $data['photo_path'] != '' ? $data['photo_path'] : NULL),
                        'is_enrolled' => 1,
                    ];
                    if (!empty($find_msme)) {
                        //$save_msme = DB::table('msme_candidate_details')->where(['id' => $find_msme->id])->update($msme_data);
                        $data['msme_candidate_detail_id'] = $find_msme->id;
                    } else {
                        $save_msme = DB::table('msme_candidate_details')->insertGetId($msme_data);
                        $data['msme_candidate_detail_id'] = $save_msme;
                    }
                    //print_r($find_training);
                    if (Arr::has($data, 'id')) {
                        if(isset($data['photo_path']))
                        {
                            unset($data['photo_path']);
                        }
                        $result = $this->subModule::where(['id' => $data['id']])->update($data);
                        $result = $this->subModule::where(['id' => $data['id']])->first();
                    } else {
                        $result = $this->subModule::create($data);
                    }
                    //print_r($result);
                    if(isset($result->id))
                    {
                        if(isset($img_val) && $img_val !='' && isset($user_image) && $user_image != "" && isset($data['photo_path']) && $data['photo_path'] != "")
    					{
    						$extension_val = "png";
    						if(strpos($img_val, "image/jpeg") > -1)
    						{
    							$extension_val = "jpeg";
    						}
    						$img = str_replace('data:image/'.$extension_val.';base64,', '', $img_val);
    						$img = str_replace(' ', '+', $img);
    						$img_data = base64_decode($img);
    						$move_location_user_image = public_path('storage/'.$user_image.$extension);
    						$move_location_user_image_150_x_150 = public_path('storage/'.$user_image."-150x150".$extension);
    						$success = file_put_contents($move_location_user_image, $img_data);
    						$fileUpload = new \Illuminate\Http\UploadedFile($move_location_user_image, $user_image.$extension, 'image/'.$extension_val, null, true);
                            $image = \RvMedia::handleUpload($fileUpload, 0);
    					}
                        $trainee_data = [
                            'user_id' => $result->user_id,
                            'entrepreneur_id' => $result->id,
                        ];
                        if(isset($rows['progrmme']) && $rows['progrmme'] != "")
                        {
                            $progrmme_arr = explode("/", $rows['progrmme']);
                            $progrmme_year = end($progrmme_arr);
                            $progrmme_year_arr = explode("-", $progrmme_year);
                            if(isset($progrmme_year_arr[0]) && $progrmme_year_arr[0] != "" && isset($progrmme_year_arr[1]) && $progrmme_year_arr[1] != "")
                            {
                                $year_cond = ['session_start' => $progrmme_year_arr[0], 'session_end' => $progrmme_year_arr[1]];
                                $trainee_financial_year = DB::table('financial_year')->where('session_start', 'LIKE', '%'.$progrmme_year_arr[0])->where('session_end', 'LIKE', '%'.$progrmme_year_arr[1])->whereNull('deleted_at')->first();
                                if (!empty($trainee_financial_year)) {
                                    //$trainee_financial_year->id;
                                    //print_r($trainee_financial_year);
                                    $trainee_data['financial_year_id'] = $trainee_financial_year->id;
                                }
                            }
                        }
                        if(isset($rows['training_programme']) && $rows['training_programme'] != "")
                        {
                            $find_text = $rows['training_programme'];
                            preg_match('#\((.*?)\)#', $find_text, $match);
                            if(isset($match[1]) && $match[1] != "")
                            {
                                $find_division = DB::table('divisions')->where('name', '=', $match[1])->whereNull('deleted_at')->first();
                                if (!empty($find_division)) {
                                    //$find_division->id;
                                    //print_r($find_division);
                                    $trainee_data['division_id'] = $find_division->id;
                                }
                            }
                        }
                        if(isset($find_training) && !empty($find_training))
                        {
                            $trainee_data['training_title_id'] = $find_training->id;
                            $trainee_data['division_id'] = isset($find_training->division_id) && $find_training->division_id != "" ? $find_training->division_id : (isset($find_training->division_id) ? $find_training->division_id : NULL);
                            $trainee_data['financial_year_id'] = isset($find_training->financial_year_id) && $find_training->financial_year_id != "" ? $find_training->financial_year_id : NULL;
                            $trainee_data['annual_action_plan_id'] = isset($find_training->annual_action_plan_id) && $find_training->annual_action_plan_id != "" ? $find_training->annual_action_plan_id : NULL;
                        }
                        
                        $find_trainee = DB::table('trainees')->where($trainee_data)->whereNull('deleted_at')->first();
                        if (!empty($find_trainee)) {
                            $result['trainee_id'] = $find_trainee->id;
                        }
                        else {
                            $result_trainee = DB::table('trainees')->insertGetId($trainee_data);
                            $result['trainee_id'] = $result_trainee;
                        }
                        
                        //echo "<br/>";
                        //echo $result->id;
                        //echo "<br/>";
                        //print_r($trainee_data);
                        //print_r($data);
                        ++$this->rows;
                    }
                }
            }
            DB::commit();
        } catch(Exception $e) {
            DB::rollBack();
            if(isset($e->errorInfo)){
                $error_arr = $e->errorInfo;
            }else{
                $error_arr = $e->getMessage();
            }
            $error = getCustomErrorMessage($error_arr);
            //  dd($e);
            $failures[] = new Failure($this->rows, '', $error, $rows);
            
            throw new \Maatwebsite\Excel\Validators\ValidationException(
            
                \Illuminate\Validation\ValidationException::withMessages($error),
            
                $failures
            
            );
            return $e;

        }
        
        //print_r($result);
        //echo "</pre>";
        //die();
        
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
        return [];
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
    
    public function getDetails($data = []) {
        //echo "<pre>";
        //print_r($data);
        //echo "</pre>";
        if ($this->isSupportedModule($this->model)) {
            $result = $condn = [];
            $moduleConfig = config('importConfigs.' . $this->model, []);
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
                                if(isset($row['dob']) && $row['dob'] == "-")
                                {
                                    $row['dob'] = NULL;
                                }
                                $result[$key] = $this->getRowData($this->subModule,$subModuleConfig,$row,true);
                            }                        
                        }else{
                            foreach($replace_array_key as $current_key => $replace_key)
                            {
                                if(isset($data[$current_key]) && $data[$current_key] != "")
                                {
                                    $data[$replace_key] = $data[$current_key];
                                }
                                if(!isset($data[$replace_key]))
                                {
                                    $data[$replace_key] = "";
                                }
                            }
                            if(isset($data['dob']) && $data['dob'] == "-")
                            {
                                $data['dob'] = NULL;
                            }
                            $result = $this->getRowData($this->subModule,$subModuleConfig,$data);
                        }
                        //echo "<pre>";
                        //echo "ccc";
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
    }

}

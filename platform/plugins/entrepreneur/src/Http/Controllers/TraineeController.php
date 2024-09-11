<?php

namespace Impiger\Entrepreneur\Http\Controllers;

use Impiger\Base\Events\BeforeEditContentEvent;
use Impiger\Entrepreneur\Http\Requests\TraineeRequest;
use Impiger\Entrepreneur\Repositories\Interfaces\TraineeInterface;
use Impiger\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Exception;
use Impiger\Entrepreneur\Tables\TraineeTable;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\DeletedContentEvent;
use Impiger\Base\Events\UpdatedContentEvent;
use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\Entrepreneur\Forms\TraineeForm;
use Impiger\Base\Forms\FormBuilder;
use Assets;
use App\Utils\CrudHelper;
use Impiger\Crud\Imports\BulkImport;
use Impiger\Crud\Imports\ImportMultiSheet;
use Impiger\Crud\Imports\ImportMultiSheetNew;
use Impiger\Crud\Imports\BulkErrorExport;
use Excel;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Response as FacadeResponse;
use DB;

class TraineeController extends BaseController
{
    /**
     * @var TraineeInterface
     */
    protected $traineeRepository;

    /**
     * @param TraineeInterface $traineeRepository
     */
    public function __construct(TraineeInterface $traineeRepository)
    {
        $this->traineeRepository = $traineeRepository;

        Assets::addScriptsDirectly([
            'vendor/core/plugins/crud/js/custom_save_storage.js',
            'vendor/core/plugins/crud/js/crud_utils.js',
            'vendor/core/plugins/entrepreneur/js/entrepreneur.js',
            'vendor/core/plugins/entrepreneur/js/trainee.js?'.time()
        ])
        ->addStylesDirectly([
            'vendor/core/plugins/crud/css/module_custom_styles.css'
            
        ]);
    }

    /**
     * @param TraineeTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(TraineeTable $table)
    {
        page_title()->setTitle(trans('plugins/entrepreneur::trainee.name'));

        return $table->renderTable(['uploadRoute' => 'trainee.import','template'=>'trainee']);
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/entrepreneur::trainee.create'));

        return $formBuilder->create(TraineeForm::class)->renderForm();
    }

    /**
     * @param TraineeRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store(TraineeRequest $request, BaseHttpResponse $response, \Impiger\ACL\Repositories\Interfaces\UserInterface $coreUserRepository, \Impiger\ACL\Services\ActivateUserService $activateUserService, \Impiger\Entrepreneur\Repositories\Interfaces\EntrepreneurInterface $entrepreneurRepository)
    {
        // \Impiger\ACL\Services\CreateUserService $service
        // \Impiger\ACL\Repositories\Interfaces\UserInterface $coreUserRepository
        $roleSlug = CANDIDATE_ROLE_SLUG;
        if($request->has('candidate_type_id') && $request->has('spoke_registration_id') && $request->has('hub_institution_id')) {
            $roleSlug = SPOKE_STUDENT_ROLE_SLUG;
        }
        if(isset($request['entrepreneur_id']) && !$request['entrepreneur_id']){

            $coreUserExists = $coreUserRepository->getFirstBy(['email'=>$request['email']]);
            if(!$coreUserExists){
                $request['username'] = $request['email'];
                $user = CrudHelper::createCoreUserAndAssignRoleAndPermission($request, $coreUserRepository, $activateUserService, $roleSlug, true);
            }else{
                $user = $coreUserExists;
            }

            $request['user_id'] = $user->id;

            $entrepreneurExists = $entrepreneurRepository->getFirstBy(['email'=>$request['email']]);
            if(!$coreUserExists){
                $entrepreneur = $entrepreneurRepository->createOrUpdate($request->input());
            } else {
                $entrepreneur = $entrepreneurExists;
            }

            $request['entrepreneur_id'] = $entrepreneur->id;
        }
        $trainee = $this->traineeRepository->createOrUpdate($request->input());
        
        event(new CreatedContentEvent(TRAINEE_MODULE_SCREEN_NAME, $request, $trainee));
        
        
        return $response
            ->setPreviousUrl(route('trainee.index'))
            ->setNextUrl(route('trainee.edit', $trainee->id))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function edit($id, FormBuilder $formBuilder, Request $request, \Impiger\Entrepreneur\Repositories\Interfaces\EntrepreneurInterface $entrepreneurRepository)
    {
        
        $trainee = $this->traineeRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $trainee));

        $entrepreneur = $entrepreneurRepository->findOrFail($trainee->entrepreneur_id);
        if($entrepreneur) {
            $trainee->email = $entrepreneur->email;
            $trainee->password = $entrepreneur->password;            
            $trainee->prefix_id = $entrepreneur->prefix_id;
            $trainee->name = $entrepreneur->name;
            $trainee->care_of = $entrepreneur->care_of;
            $trainee->father_name = $entrepreneur->father_name;
            $trainee->gender_id = $entrepreneur->gender_id;
            $trainee->dob = $entrepreneur->dob;
            $trainee->aadhaar_no = $entrepreneur->aadhaar_no;
            $trainee->mobile = $entrepreneur->mobile;
            $trainee->physically_challenged = $entrepreneur->physically_challenged;
            $trainee->address = $entrepreneur->address;
            $trainee->district_id = $entrepreneur->district_id;
            $trainee->pincode = $entrepreneur->pincode;
            $trainee->religion_id = $entrepreneur->religion_id;
            $trainee->community = $entrepreneur->community;
            $trainee->candidate_type_id = $entrepreneur->candidate_type_id;
            $trainee->student_type_id = $entrepreneur->student_type_id;
            $trainee->student_school_name = $entrepreneur->student_school_name;
            $trainee->student_standard_name = $entrepreneur->student_standard_name;
            $trainee->student_college_name = $entrepreneur->student_college_name;
            $trainee->student_course_name = $entrepreneur->student_course_name;
            $trainee->hub_institution_id = $entrepreneur->hub_institution_id;
            $trainee->student_year = $entrepreneur->student_year;
            $trainee->spoke_registration_id = $entrepreneur->spoke_registration_id;
            $trainee->qualification_id = $entrepreneur->qualification_id;
            $trainee->entrepreneurial_category_id = $entrepreneur->entrepreneurial_category_id;
            $trainee->activity_name = $entrepreneur->activity_name;
            $trainee->photo_path = $entrepreneur->photo_path;
            /*
            unset($entrepreneur->id);
            unset($entrepreneur->created_at);
            unset($entrepreneur->updated_at);
            unset($entrepreneur->deleted_at);
            $entrepreneur = (Array) $entrepreneur;
            foreach ($entrepreneur['attributes'] as $key => $value) {
                $trainee->$key = $value;
            }
            $trainee->fill($entrepreneur);
            print_r($trainee);
            */

        }
        
        $name = ($trainee->name) ? ' "' . $trainee->name . '"' : "";
        page_title()->setTitle(trans('plugins/entrepreneur::trainee.edit') . $name);

        return $formBuilder->create(TraineeForm::class, ['model' => $trainee])->renderForm();
    }

    /**
     * @param int $id
     * @param Request $request
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function viewdetail($id, FormBuilder $formBuilder, Request $request)
    {
        $trainee = $this->traineeRepository->findOrFail($id);
        $name = ($trainee->name) ? ' "' . $trainee->name . '"' : "";
        page_title()->setTitle(trans('plugins/entrepreneur::trainee.view') . $name);

        return $formBuilder->create(TraineeForm::class, ['model' => $trainee, 'isView' => true])->renderForm();
    }
    

    /**
     * @param int $id
     * @param TraineeRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, TraineeRequest $request, BaseHttpResponse $response, \Impiger\ACL\Repositories\Interfaces\UserInterface $coreUserRepository, \Impiger\ACL\Services\MappingRoleService $service, \Impiger\ACL\Services\ActivateUserService $activateUserService, \Impiger\Entrepreneur\Repositories\Interfaces\EntrepreneurInterface $entrepreneurRepository)
    {
        
        $trainee = $this->traineeRepository->findOrFail($id);
        
        $trainee->fill($request->input());

        $entrepreneur = $entrepreneurRepository->findOrFail($trainee->entrepreneur_id);
        $password = $request->input('password');
        
        if($entrepreneur) {
            if($entrepreneur->password != $request->input('password')) {
                $request['password'] = \Hash::make($password);
            } else {
                unset($request['password']);
            }
            $coreuser = CrudHelper::updateCoreUser($entrepreneur->user_id, $request, $coreUserRepository, $service, $activateUserService);
            $reqInput = $request->input();
            unset($reqInput['id']);
            $request['password'] = $password;
            $entrepreneur->fill($reqInput);
            $entrepreneurRepository->createOrUpdate($entrepreneur);
            event(new UpdatedContentEvent(ENTREPRENEUR_MODULE_SCREEN_NAME, $request, $entrepreneur));
        }

        $this->traineeRepository->createOrUpdate($trainee);

        event(new UpdatedContentEvent(TRAINEE_MODULE_SCREEN_NAME, $request, $trainee));
        
        
        return $response
            ->setPreviousUrl(route('trainee.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function destroy(Request $request, $id, BaseHttpResponse $response)
    {
        try {
            $trainee = $this->traineeRepository->findOrFail($id);

            $dataExist = CrudHelper::isDependentDataExist('trainee', array($id), "Impiger\Entrepreneur\Models\Trainee");

            if($dataExist) {
                return $response
                    ->setError()
                    ->setMessage($dataExist);
            }

            $this->traineeRepository->delete($trainee);
            
            event(new DeletedContentEvent(TRAINEE_MODULE_SCREEN_NAME, $request, $trainee));

            return $response->setMessage(trans('core/base::notices.delete_success_message'));
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }

    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws Exception
     */
    public function deletes(Request $request, BaseHttpResponse $response)
    {
        $ids = $request->input('ids');
        if (empty($ids)) {
            return $response
                ->setError()
                ->setMessage(trans('core/base::notices.no_select'));
        }

        $dataExist = CrudHelper::isDependentDataExist('trainee', $ids, "Impiger\Entrepreneur\Models\Trainee");

        if($dataExist) {
            return $response
                ->setError()
                ->setMessage($dataExist);
        }

        foreach ($ids as $id) {
            $trainee = $this->traineeRepository->findOrFail($id);
            $this->traineeRepository->delete($trainee);
            
            event(new DeletedContentEvent(TRAINEE_MODULE_SCREEN_NAME, $request, $trainee));
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }

    /**
     * @param ImportCustomFieldsAction $action
     * @param Request $request
     * @return array
     * @throws Exception
     */
    public function postImport(Request $request, BaseHttpResponse $response)
    {
           try {
            $request->validate([
                'file' => "required",
            ]);
            $imageSRC = [];
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($request->file('file'));
            $worksheet = $spreadsheet->getActiveSheet();
            $worksheetArray = $worksheet->toArray();
            array_shift($worksheetArray);
    
            $worksheetArray = array_map('array_filter', $worksheetArray);
            $worksheetArray = array_filter($worksheetArray);
    
            foreach ($worksheetArray as $key => $value)
            {
                if (isset($worksheet->getDrawingCollection()[$key])) {
                    $drawing = $worksheet->getDrawingCollection()[$key];
    
                    $zipReader = fopen($drawing->getPath(), 'r');
                    $imageContents = '';
                    while (!feof($zipReader)) {
                        $imageContents .= fread($zipReader, 1024);
                    }
                    fclose($zipReader);
                    $extension = $drawing->getExtension();
                    
                    $cell_number = $drawing->getCoordinates();
                    $formatted_cell_number = preg_replace("/[^0-9]/", '', $cell_number);
                    $current_arr_key = $formatted_cell_number - 2;
                    $user_email = $worksheetArray[$current_arr_key][4];
                    $imageSRC[$user_email] = "data:image/jpeg;base64," . base64_encode($imageContents);
                }
            }
            //echo "<pre>";
            //print_r($imageSRC);
            //echo "</pre>";
            //exit;
            // $bulkUpload = new BulkImport(new \Impiger\Entrepreneur\Models\Trainee);
            $bulkUpload = new ImportMultiSheetNew(new \Impiger\Entrepreneur\Models\Trainee,[new \Impiger\Entrepreneur\Models\Entrepreneur,new \Impiger\Entrepreneur\Models\Trainee], $imageSRC);
            //echo "<pre>";
            //print_r($bulkUpload);
            //echo "</pre>";
            //exit;
            $result = Excel::import($bulkUpload, $request->file('file'));
            
            $rowCount= $bulkUpload->getRowCount();
            if($rowCount==0){
                return $response
                        ->setError(true)
                        ->setMessage('There is no data to upload. Please upload file with valid data');
            }
            \Log::info(ucfirst(Str::plural(class_basename('\Impiger\Entrepreneur\Models\Trainee')))." has been uploaded successfully");
            return $response->setMessage(trans('core/base::notices.bulk_upload_success_message'));
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errors=[];
            foreach ($failures as $failure) {
                $error = $failure->values();
                $error['row'] = $failure->row();
                $error['error'] = implode(",",$failure->errors());
                if(false!==$key = array_search($failure->row(),array_column($errors,'row'))){
                    $errors[$key]['error'].="\r\n".implode(",",$failure->errors());
                }else{
                    $errors[]=$error;
                }
            }
            \Log::warning(count($errors)." ".ucfirst(Str::plural(class_basename('\Impiger\Entrepreneur\Models\Trainee')))." has been inserted failed");
            $fileName = strtolower(class_basename('\Impiger\Entrepreneur\Models\Trainee')) . "_" . \Carbon\Carbon::now()->format('Y-m-d_H-i-s') . ".csv";
            $filePath = "bulk_upload_errors" . "/" . $fileName;
            Excel::store(new BulkErrorExport($errors), $filePath);
            $template['filePath'] = $filePath;
            $template['fileName'] = $fileName;
            $request->session()->flash('modal',$template);
            return back();
        }
    }

    function generateCertificate(Request $request, BaseHttpResponse $response) {

        // $training_title_id = 2158; 
        $training_title_id = null;
        $entrepreneur_id = null;
        $id = null;
        $action = null;
        $isMsmeCandidate = null;
        
        $choosen_template = 1;
        if($request->has('certificate_template') && $request->input('certificate_template')) {
            $choosen_template = $request->input('certificate_template'); 
        }
        
        if($request->has('training_title_id') && $request->input('training_title_id')) {
            $training_title_id = $request->input('training_title_id'); 
        }

        if($request->has('entrepreneur_id') && $request->input('entrepreneur_id')) {
            $entrepreneur_id = $request->input('entrepreneur_id'); 
        }
       
        if($request->has('action') && $request->input('action') == 'generate') { 
            
            $trainee = \Impiger\Entrepreneur\Models\Trainee::where(['entrepreneur_id' => $entrepreneur_id, 'training_title_id' => $training_title_id])->first();
            if($trainee && $trainee->certificate_status == 1) {
                $msg = 'Certificate already generated, Please check your inbox and download.'; 
                return $response->setData($trainee)
                    ->setMessage($msg);
            }

            $candidate = \Impiger\Entrepreneur\Models\Entrepreneur::where('id',$entrepreneur_id)->first();
            // dd($candidate);
            if($candidate) {
                $isMsmeCandidate = ($candidate->scheme) ? true : false;
            }

            if(!$isMsmeCandidate) {
                $attendanceModel = \Impiger\Attendance\Models\Attendance::where(['entrepreneur_id' => $entrepreneur_id, 'training_title_id' => $training_title_id])->first();
                if(!$attendanceModel){
                    return $response->setError(true)
                        ->setMessage("Attendance percentage must have 80%");
                } 
            }
            
         }

        if(is_plugin_active('training-title')){
            $traineeModel = app(\Impiger\Entrepreneur\Models\Trainee::class)->getModel();

            if($training_title_id) {
                $traineeModel = $traineeModel->where('TT.id', $training_title_id);
            } 
            if($entrepreneur_id) {
                $traineeModel = $traineeModel->where('trainees.entrepreneur_id', $entrepreneur_id);
            } 

            // dd($traineeModel);

            $data = $traineeModel
            ->where('trainees.certificate_status', 0);
            if(!$isMsmeCandidate) {
                $data->whereRaw('(A.attendance_date BETWEEN DATE(TT.training_start_date) AND DATE(TT.training_end_date))');
            }
            $data->select([
                'trainees.id',
                'trainees.training_title_id',
                'trainees.entrepreneur_id',
                DB::raw('SUM(A.present) AS present'),
                'D.name AS division_name', 
                'AP.name AS program',
                'AP.remarks AS remarks',
                'TT.training_start_date AS start',
                'TT.training_end_date AS end',
                'TT.code',
                'msme.candidate_msme_ref_id',
                'msme.created_at AS msme_start',
				'msme.enroll_start_date AS enroll_start_date',
                'msme.enroll_to_date AS enroll_to_date',
                'TT.venue',
                'E.name AS entrepreneur_name',
                'E.photo_path',
                'E.father_name AS care_name',
                'E.scheme AS scheme',
                'Dis.name AS district',
                'AO.name AS care_of',
                'AOP.name AS prefix',
                'AOPT.name AS scheme_name',
                DB::Raw("DATEDIFF(TT.training_end_date, TT.training_start_date) + 1 AS training_days"),
                DB::Raw("ROUND((SUM(A.present) / (DATEDIFF(TT.training_end_date, TT.training_start_date) + 1)) * 100) AS attendance_percentage"),
            ])       
            ->leftJoin('training_title AS TT', 'TT.id', '=', 'trainees.training_title_id')
            ->leftJoin('annual_action_plan AS AP', 'AP.id', '=', 'TT.annual_action_plan_id')
            ->leftJoin('divisions AS D', 'D.id', '=', 'TT.division_id')
            ->leftJoin('entrepreneurs AS E', 'E.id', '=', 'trainees.entrepreneur_id')
            ->leftJoin('district AS Dis', 'Dis.id', '=', 'E.district_id')
            ->leftJoin('attribute_options AS AO', 'AO.id', '=', 'E.care_of')
            ->leftJoin('attribute_options AS AOP', 'AOP.id', '=', 'E.prefix_id')
            ->leftJoin('attribute_options AS AOPT', 'AOPT.id', '=', 'E.scheme')
            ->leftJoin('msme_candidate_details AS msme', 'msme.email', '=', 'E.email')
            ->leftJoin('attendance AS A', 'A.entrepreneur_id', '=', 'trainees.entrepreneur_id')
            ->groupBy('A.entrepreneur_id');
            // ->havingRaw('attendance_percentage >= 80');
            // $data->dd();
            $trainings = $data->first();    
          
            \Log::info(json_encode($trainings));
            if(!$trainings) {
                return $response->setError(true)
                     ->setMessage("No data found for generate certificate, please contact EDi admin.");
            }

            if($trainings && $trainings->attendance_percentage < 80 && !($trainings->candidate_msme_ref_id)) {
                \Log::info("certificate % is less than 80, So returing to view with msg");
                return $response->setError(true)
                    ->setMessage("Attendance percentage must have 80%");
            }

            if(!$certifiate = $this->saveCertificate($trainings, $choosen_template)) {
                return $response->setError(true)
                     ->setMessage("Certificate not generated, please contact EDi admin.");
            }

            return $response->setData($certifiate)->setMessage("Certificate generated successfully!");
            // dd($trainings);
            // if(count($trainings) > 0) {
            //     foreach ($trainings as $key => $value) {
            //         $this->saveCertificate($value);
            //     }
            // } else {
            //     return $response->setError(true)
            //         ->setMessage("Attendance percentage must have 80%");
            // }               
        }
    }

    function regenerateCertificate(Request $request, BaseHttpResponse $response) {

        // $training_title_id = 2158; 
        $training_title_id = null;
        $entrepreneur_id = null;
        $id = null;
        $action = null;
        $isMsmeCandidate = null;
        
        $choosen_template = 1;
        if($request->has('certificate_template') && $request->input('certificate_template')) {
            $choosen_template = $request->input('certificate_template'); 
        }
        
        if($request->has('training_title_id') && $request->input('training_title_id')) {
            $training_title_id = $request->input('training_title_id'); 
        }

        if($request->has('entrepreneur_id') && $request->input('entrepreneur_id')) {
            $entrepreneur_id = $request->input('entrepreneur_id'); 
        }
              
        if(is_plugin_active('training-title')){
            $traineeModel = app(\Impiger\Entrepreneur\Models\Trainee::class)->getModel();

            if($training_title_id) {
                $traineeModel = $traineeModel->where('TT.id', $training_title_id);
            } 
            if($entrepreneur_id) {
                $traineeModel = $traineeModel->where('trainees.entrepreneur_id', $entrepreneur_id);
            } 

            $candidate = \Impiger\Entrepreneur\Models\Entrepreneur::where('id',$entrepreneur_id)->first();
            // dd($candidate->scheme);
            if($candidate) {
                $isMsmeCandidate = ($candidate->scheme) ? true : false;
            }

            // dd($traineeModel);

            $data = $traineeModel
            ->where('trainees.certificate_status',1);
            if(!$isMsmeCandidate) {
                $data->whereRaw('(A.attendance_date BETWEEN DATE(TT.training_start_date) AND DATE(TT.training_end_date))');
            }
            // ->whereRaw('(A.attendance_date BETWEEN DATE(TT.training_start_date) AND DATE(TT.training_end_date))')
            $data->select([
                'trainees.id',
                'trainees.training_title_id',
                'trainees.entrepreneur_id',
                DB::raw('SUM(A.present) AS present'),
                'D.name AS division_name', 
                'AP.name AS program',
                'AP.remarks AS remarks',
                'TT.training_start_date AS start',
                'TT.training_end_date AS end',
                'TT.code',
                'msme.candidate_msme_ref_id',
                'msme.created_at AS msme_start',
                'msme.enroll_start_date AS enroll_start_date',
                'msme.enroll_to_date AS enroll_to_date',
                'TT.venue',
                'E.name AS entrepreneur_name',
                'E.photo_path',
                'E.father_name AS care_name',
                'E.scheme AS scheme',
                'Dis.name AS district',
                'AO.name AS care_of',
                'AOP.name AS prefix',
                'AOPT.name AS scheme_name',
                DB::Raw("DATEDIFF(TT.training_end_date, TT.training_start_date) + 1 AS training_days"),
                DB::Raw("ROUND((SUM(A.present) / (DATEDIFF(TT.training_end_date, TT.training_start_date) + 1)) * 100) AS attendance_percentage"),
            ])       
            ->leftJoin('training_title AS TT', 'TT.id', '=', 'trainees.training_title_id')
            ->leftJoin('annual_action_plan AS AP', 'AP.id', '=', 'TT.annual_action_plan_id')
            ->leftJoin('divisions AS D', 'D.id', '=', 'TT.division_id')
            ->leftJoin('entrepreneurs AS E', 'E.id', '=', 'trainees.entrepreneur_id')
            ->leftJoin('district AS Dis', 'Dis.id', '=', 'E.district_id')
            ->leftJoin('attribute_options AS AO', 'AO.id', '=', 'E.care_of')
            ->leftJoin('attribute_options AS AOP', 'AOP.id', '=', 'E.prefix_id')
            ->leftJoin('attribute_options AS AOPT', 'AOPT.id', '=', 'E.scheme')
            ->leftJoin('msme_candidate_details AS msme', 'msme.email', '=', 'E.email')
            ->leftJoin('attendance AS A', 'A.entrepreneur_id', '=', 'trainees.entrepreneur_id')
            ->groupBy('A.entrepreneur_id');
            // ->havingRaw('attendance_percentage >= 80');
            // $data->dd();
            $trainings = $data->first();  
            \Log::info(json_encode($trainings));
            if(!$trainings) {
                return $response->setError(true)
                     ->setMessage("No data found for generate certificate, please contact EDi admin.");
            }

            if($trainings && $trainings->attendance_percentage < 80 && !($trainings->candidate_msme_ref_id)) {
                \Log::info("certificate % is less than 80, So returing to view with msg");
                return $response->setError(true)
                    ->setMessage("Attendance percentage must have 80%");
            }

            if(!$certifiate = $this->saveCertificate($trainings, $choosen_template)) {
                return $response->setError(true)
                     ->setMessage("Certificate not generated, please contact EDi admin.");
            }

            return $response->setData($certifiate)->setMessage("Certificate generated successfully!");
                  
        }
    }

    function saveCertificate($data, $choosen_template) {
        $format = config('core.base.general.date_format.program_date');
        $data['start'] = \Carbon\Carbon::parse($data['start'])->isoFormat($format);
        $data['end'] = \Carbon\Carbon::parse($data['end'])->isoFormat($format);
        $fileName = $data['id']."_".$data['training_title_id']."_".$data['entrepreneur_id']."_certificate";
        $fileName = md5($fileName).".pdf";
        $profile_picture = 'storage/'.$data['photo_path'];
        $data['profile_picture'] = $profile_picture;
        $pdf = app('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("isPhpEnabled", true);
        $certificate_blade = "certificate";
        if($choosen_template == 2)
        {
            $certificate_blade = "certificate_new";
        }
        $pdf->loadView('program.'.$certificate_blade, ['value' => $data])->setPaper('a4', 'landscape');
        $content = $pdf->download()->getOriginalContent();
        if($content) {
            \Storage::put('certificate/'.$fileName, $content);
            $trainee = $this->traineeRepository->findOrFail($data->id);
            if($trainee){
                $trainee->certificate_status = 1;
                $trainee->certificate_generated_at = ($trainee->certificate_generated_at) ? $trainee->certificate_generated_at : date('Y-m-d H:i:s');
                $trainee->file_name = $fileName;
                $trainee->file_path = 'certificate/'.$fileName;
                if($trainee->save()){
                    return $trainee;
                } else {
                    return false;
                }
            }
        }
        
        // return $pdf->download('certificate.pdf');
    }

    public function downloadCertificate($id){
        $trainee = $this->traineeRepository->findOrFail($id);
        $headers = array(
            'Content-Type' => 'application/pdf',
        );
        if(!$trainee){
            return;
        }
        $file = public_path()."/storage/".$trainee->file_path;
        // dd($trainee);
        
        return FacadeResponse::download($file, $trainee->file_name, $headers); 
    }
}

<?php

namespace Impiger\MsmeCandidateDetails\Http\Controllers;

use Impiger\Base\Events\BeforeEditContentEvent;
use Impiger\MsmeCandidateDetails\Http\Requests\MsmeCandidateDetailsRequest;
use Impiger\MsmeCandidateDetails\Repositories\Interfaces\MsmeCandidateDetailsInterface;
use Impiger\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Exception;
use Impiger\MsmeCandidateDetails\Tables\MsmeCandidateDetailsTable;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\DeletedContentEvent;
use Impiger\Base\Events\UpdatedContentEvent;
use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\MsmeCandidateDetails\Forms\MsmeCandidateDetailsForm;
use Impiger\Base\Forms\FormBuilder;
use Assets;
use App\Utils\CrudHelper;
use Impiger\Crud\Imports\BulkImport;
use Impiger\Crud\Imports\BulkErrorExport;
use Excel;
use Illuminate\Support\Str;

class MsmeCandidateDetailsController extends BaseController
{
    /**
     * @var MsmeCandidateDetailsInterface
     */
    protected $msmeCandidateDetailsRepository;

    /**
     * @param MsmeCandidateDetailsInterface $msmeCandidateDetailsRepository
     */
    public function __construct(MsmeCandidateDetailsInterface $msmeCandidateDetailsRepository)
    {
        $this->msmeCandidateDetailsRepository = $msmeCandidateDetailsRepository;

        Assets::addScriptsDirectly([
//            'vendor/core/plugins/crud/js/custom_save_storage.js',
            'vendor/core/plugins/crud/js/crud_utils.js'
            ,'vendor/core/plugins/msme-candidate-details/js/msme-candidate-details.js',
            'vendor/core/plugins/entrepreneur/js/trainee.js'
            
        ])
        ->addStylesDirectly([
            'vendor/core/plugins/crud/css/module_custom_styles.css'
            
        ]);
    }

    /**
     * @param MsmeCandidateDetailsTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(MsmeCandidateDetailsTable $table)
    {
        page_title()->setTitle(trans('plugins/msme-candidate-details::msme-candidate-details.name'));

        return $table->renderTable();
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/msme-candidate-details::msme-candidate-details.create'));

        return $formBuilder->create(MsmeCandidateDetailsForm::class)->renderForm();
    }

    /**
     * @param MsmeCandidateDetailsRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store(MsmeCandidateDetailsRequest $request, BaseHttpResponse $response )
    {
        $iMimeType = array('tif','tiff','webp','svg','png','jpeg','jpg','gif','bmp','avif');
        $photo = $request->input('photo');
        if($photo){
            $fileExtention = substr($photo, strrpos($photo, '.') + 1);
            if(in_array($fileExtention,$iMimeType)){
                $request['photo']=$photo;
            }else{
                $imgName = str_replace("/", "-", $request->candidate_msme_ref_id).'_img';
                $imgData = $photo;
                $encodedImg = explode(",",$imgData)[1];
                $decodedImg = base64_decode($encodedImg);
                \Illuminate\Support\Facades\Storage::put($imgName."-150x150.jpg",$decodedImg);
                $request['photo'] = $imgName.".jpg";
            }
        }
        $msmeCertificateDays = MSME_CERTIFICATE_DAYS;
        $msmeAttOption = \Impiger\MasterDetail\Models\MasterDetail::whereIn('attribute',['msme_scheme'])->pluck('name','id')->toArray();
        $certDays = $msmeCertificateDays[$msmeAttOption[$request['scheme']]]; 
        $request['enroll_start_date'] = (!$request->has('enroll_start_date')) ? date('Y-m-d') : $request->input('enroll_start_date');
        // $request['enroll_to_date'] = date('Y-m-d', strtotime($today.' + '.$certDays.' days'));
        $request['enroll_to_date'] = date_add(date_create($request['enroll_start_date']),date_interval_create_from_date_string($certDays.' days'));
        if($request->has('district')) {
            $request['district_id'] = $request->input('district');
        }
        $msmeCandidateDetails = $this->msmeCandidateDetailsRepository->createOrUpdate($request->input());
        CrudHelper::createOrUpdateMsmeCandidate($request,$msmeCandidateDetails);
        event(new CreatedContentEvent(MSME_CANDIDATE_DETAILS_MODULE_SCREEN_NAME, $request, $msmeCandidateDetails));
                
        return $response
            ->setPreviousUrl(route('msme-candidate-details.index'))
            ->setNextUrl(route('msme-candidate-details.edit', $msmeCandidateDetails->id))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function edit($id, FormBuilder $formBuilder, Request $request)
    {
        
        $msmeCandidateDetails = $this->msmeCandidateDetailsRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $msmeCandidateDetails));

        $name = ($msmeCandidateDetails->name) ? ' "' . $msmeCandidateDetails->name . '"' : "";
        page_title()->setTitle(trans('plugins/msme-candidate-details::msme-candidate-details.edit') . $name);
        $msmeCandidateDetails->district = $msmeCandidateDetails->district_id;
        return $formBuilder->create(MsmeCandidateDetailsForm::class, ['model' => $msmeCandidateDetails])->renderForm();
    }

    /**
     * @param int $id
     * @param Request $request
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function viewdetail($id, FormBuilder $formBuilder, Request $request)
    {
        $msmeCandidateDetails = $this->msmeCandidateDetailsRepository->findOrFail($id);
        $name = ($msmeCandidateDetails->name) ? ' "' . $msmeCandidateDetails->name . '"' : "";
        page_title()->setTitle(trans('plugins/msme-candidate-details::msme-candidate-details.view') . $name);

        return $formBuilder->create(MsmeCandidateDetailsForm::class, ['model' => $msmeCandidateDetails, 'isView' => true])->renderForm();
    }
    

    /**
     * @param int $id
     * @param MsmeCandidateDetailsRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, MsmeCandidateDetailsRequest $request, BaseHttpResponse $response )
    {
        
        $msmeCandidateDetails = $this->msmeCandidateDetailsRepository->findOrFail($id);
        $iMimeType = array('tif','tiff','webp','svg','png','jpeg','jpg','gif','bmp','avif');
        $photo = $request->input('photo');
        if($photo){
            $fileExtention = substr($photo, strrpos($photo, '.') + 1);
            if(in_array($fileExtention,$iMimeType)){
                $request['photo']=$photo;
            }else{
                $imgName = str_replace("/", "-", $request->candidate_msme_ref_id).'_img';
                $imgData = $photo;
                $encodedImg = explode(",",$imgData)[1];
                $decodedImg = base64_decode($encodedImg);
                \Illuminate\Support\Facades\Storage::put($imgName."-150x150.jpg",$decodedImg);
                $request['photo'] = $imgName.".jpg";
            }
        }
		$msmeCertificateDays = MSME_CERTIFICATE_DAYS;
        $msmeAttOption = \Impiger\MasterDetail\Models\MasterDetail::whereIn('attribute',['msme_scheme'])->pluck('name','id')->toArray();
        $certDays = $msmeCertificateDays[$msmeAttOption[$request['scheme']]]; 
        $request['enroll_start_date'] = (!$request->has('enroll_start_date')) ? date('Y-m-d') : $request->input('enroll_start_date');
        // $request['enroll_to_date'] = date('Y-m-d', strtotime($today.' + '.$certDays.' days'));
        $request['enroll_to_date'] = date_add(date_create($request['enroll_start_date']),date_interval_create_from_date_string($certDays.' days'));
        if($request->has('district')) {
            $request['district_id'] = $request->input('district');
        }
        
        $msmeCandidateDetails->fill($request->input());

        $this->msmeCandidateDetailsRepository->createOrUpdate($msmeCandidateDetails);
        CrudHelper::createOrUpdateMsmeCandidate($request,$msmeCandidateDetails);
        event(new UpdatedContentEvent(MSME_CANDIDATE_DETAILS_MODULE_SCREEN_NAME, $request, $msmeCandidateDetails));
        
        
        return $response
            ->setPreviousUrl(route('msme-candidate-details.index'))
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
            $msmeCandidateDetails = $this->msmeCandidateDetailsRepository->findOrFail($id);

            $dataExist = CrudHelper::isDependentDataExist('msme-candidate-details', array($id), "Impiger\MsmeCandidateDetails\Models\MsmeCandidateDetails");

            if($dataExist) {
                return $response
                    ->setError()
                    ->setMessage($dataExist);
            }

            $this->msmeCandidateDetailsRepository->delete($msmeCandidateDetails);
            
            event(new DeletedContentEvent(MSME_CANDIDATE_DETAILS_MODULE_SCREEN_NAME, $request, $msmeCandidateDetails));

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

        $dataExist = CrudHelper::isDependentDataExist('msme-candidate-details', $ids, "Impiger\MsmeCandidateDetails\Models\MsmeCandidateDetails");

        if($dataExist) {
            return $response
                ->setError()
                ->setMessage($dataExist);
        }

        foreach ($ids as $id) {
            $msmeCandidateDetails = $this->msmeCandidateDetailsRepository->findOrFail($id);
            $this->msmeCandidateDetailsRepository->delete($msmeCandidateDetails);
            
            event(new DeletedContentEvent(MSME_CANDIDATE_DETAILS_MODULE_SCREEN_NAME, $request, $msmeCandidateDetails));
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
            $bulkUpload = new BulkImport(new \Impiger\MsmeCandidateDetails\Models\MsmeCandidateDetails);
            $result = Excel::import($bulkUpload, $request->file('file'));
            $rowCount= $bulkUpload->getRowCount();
            if($rowCount==0){
                return $response
                        ->setError(true)
                        ->setMessage('There is no data to upload. Please upload file with valid data');
            }
            \Log::info(ucfirst(Str::plural(class_basename('\Impiger\MsmeCandidateDetails\Models\MsmeCandidateDetails')))." has been uploaded successfully");
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
            \Log::warning(count($errors)." ".ucfirst(Str::plural(class_basename('\Impiger\MsmeCandidateDetails\Models\MsmeCandidateDetails')))." has been inserted failed");
            $fileName = strtolower(class_basename('\Impiger\MsmeCandidateDetails\Models\MsmeCandidateDetails')) . "_" . \Carbon\Carbon::now()->format('Y-m-d_H-i-s') . ".csv";
            $filePath = "bulk_upload_errors" . "/" . $fileName;
            Excel::store(new BulkErrorExport($errors), $filePath);
            $template['filePath'] = $filePath;
            $template['fileName'] = $fileName;
            $request->session()->flash('modal',$template);
            return back();
        }
    }
}

<?php

namespace Impiger\InnovationVoucherProgram\Http\Controllers;

use Impiger\Base\Events\BeforeEditContentEvent;
use Impiger\InnovationVoucherProgram\Http\Requests\IvpCompanyDetailsRequest;
use Impiger\InnovationVoucherProgram\Repositories\Interfaces\IvpCompanyDetailsInterface;
use Impiger\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Exception;
use Impiger\InnovationVoucherProgram\Tables\IvpCompanyDetailsTable;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\DeletedContentEvent;
use Impiger\Base\Events\UpdatedContentEvent;
use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\InnovationVoucherProgram\Forms\IvpCompanyDetailsForm;
use Impiger\Base\Forms\FormBuilder;
use Assets;
use App\Utils\CrudHelper;
use Impiger\Crud\Imports\BulkImport;
use Impiger\Crud\Imports\BulkErrorExport;
use Excel;
use Illuminate\Support\Str;

class IvpCompanyDetailsController extends BaseController
{
    /**
     * @var IvpCompanyDetailsInterface
     */
    protected $ivpCompanyDetailsRepository;

    /**
     * @param IvpCompanyDetailsInterface $ivpCompanyDetailsRepository
     */
    public function __construct(IvpCompanyDetailsInterface $ivpCompanyDetailsRepository)
    {
        $this->ivpCompanyDetailsRepository = $ivpCompanyDetailsRepository;

        Assets::addScriptsDirectly([
            'vendor/core/plugins/crud/js/custom_save_storage.js',
            'vendor/core/plugins/crud/js/crud_utils.js'
            
            
        ])
        ->addStylesDirectly([
            'vendor/core/plugins/crud/css/module_custom_styles.css'
            
        ]);
    }

    /**
     * @param IvpCompanyDetailsTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(IvpCompanyDetailsTable $table)
    {
        page_title()->setTitle(trans('plugins/innovation-voucher-program::ivp-company-details.name'));

        return $table->renderTable();
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/innovation-voucher-program::ivp-company-details.create'));

        return $formBuilder->create(IvpCompanyDetailsForm::class)->renderForm();
    }

    /**
     * @param IvpCompanyDetailsRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store(IvpCompanyDetailsRequest $request, BaseHttpResponse $response )
    {
        
        
        $ivpCompanyDetails = $this->ivpCompanyDetailsRepository->createOrUpdate($request->input());
        
        event(new CreatedContentEvent(IVP_COMPANY_DETAILS_MODULE_SCREEN_NAME, $request, $ivpCompanyDetails));
        
        
        return $response
            ->setPreviousUrl(route('ivp-company-details.index'))
            ->setNextUrl(route('ivp-company-details.edit', $ivpCompanyDetails->id))
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
        
        $ivpCompanyDetails = $this->ivpCompanyDetailsRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $ivpCompanyDetails));

        $name = ($ivpCompanyDetails->name) ? ' "' . $ivpCompanyDetails->name . '"' : "";
        page_title()->setTitle(trans('plugins/innovation-voucher-program::ivp-company-details.edit') . $name);

        return $formBuilder->create(IvpCompanyDetailsForm::class, ['model' => $ivpCompanyDetails])->renderForm();
    }

    /**
     * @param int $id
     * @param Request $request
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function viewdetail($id, FormBuilder $formBuilder, Request $request)
    {
        $ivpCompanyDetails = $this->ivpCompanyDetailsRepository->findOrFail($id);
        $name = ($ivpCompanyDetails->name) ? ' "' . $ivpCompanyDetails->name . '"' : "";
        page_title()->setTitle(trans('plugins/innovation-voucher-program::ivp-company-details.view') . $name);

        return $formBuilder->create(IvpCompanyDetailsForm::class, ['model' => $ivpCompanyDetails, 'isView' => true])->renderForm();
    }
    

    /**
     * @param int $id
     * @param IvpCompanyDetailsRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, IvpCompanyDetailsRequest $request, BaseHttpResponse $response )
    {
        
        $ivpCompanyDetails = $this->ivpCompanyDetailsRepository->findOrFail($id);
        
        
        $ivpCompanyDetails->fill($request->input());

        $this->ivpCompanyDetailsRepository->createOrUpdate($ivpCompanyDetails);

        event(new UpdatedContentEvent(IVP_COMPANY_DETAILS_MODULE_SCREEN_NAME, $request, $ivpCompanyDetails));
        
        
        return $response
            ->setPreviousUrl(route('ivp-company-details.index'))
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
            $ivpCompanyDetails = $this->ivpCompanyDetailsRepository->findOrFail($id);

            $dataExist = CrudHelper::isDependentDataExist('ivp-company-details', array($id), "Impiger\InnovationVoucherProgram\Models\IvpCompanyDetails");

            if($dataExist) {
                return $response
                    ->setError()
                    ->setMessage($dataExist);
            }

            $this->ivpCompanyDetailsRepository->delete($ivpCompanyDetails);
            
            event(new DeletedContentEvent(IVP_COMPANY_DETAILS_MODULE_SCREEN_NAME, $request, $ivpCompanyDetails));

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

        $dataExist = CrudHelper::isDependentDataExist('ivp-company-details', $ids, "Impiger\InnovationVoucherProgram\Models\IvpCompanyDetails");

        if($dataExist) {
            return $response
                ->setError()
                ->setMessage($dataExist);
        }

        foreach ($ids as $id) {
            $ivpCompanyDetails = $this->ivpCompanyDetailsRepository->findOrFail($id);
            $this->ivpCompanyDetailsRepository->delete($ivpCompanyDetails);
            
            event(new DeletedContentEvent(IVP_COMPANY_DETAILS_MODULE_SCREEN_NAME, $request, $ivpCompanyDetails));
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
            $bulkUpload = new BulkImport(new \Impiger\InnovationVoucherProgram\Models\IvpCompanyDetails);
            $result = Excel::import($bulkUpload, $request->file('file'));
            $rowCount= $bulkUpload->getRowCount();
            if($rowCount==0){
                return $response
                        ->setError(true)
                        ->setMessage('There is no data to upload. Please upload file with valid data');
            }
            \Log::info(ucfirst(Str::plural(class_basename('\Impiger\InnovationVoucherProgram\Models\IvpCompanyDetails')))." has been uploaded successfully");
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
            \Log::warning(count($errors)." ".ucfirst(Str::plural(class_basename('\Impiger\InnovationVoucherProgram\Models\IvpCompanyDetails')))." has been inserted failed");
            $fileName = strtolower(class_basename('\Impiger\InnovationVoucherProgram\Models\IvpCompanyDetails')) . "_" . \Carbon\Carbon::now()->format('Y-m-d_H-i-s') . ".csv";
            $filePath = "bulk_upload_errors" . "/" . $fileName;
            Excel::store(new BulkErrorExport($errors), $filePath);
            $template['filePath'] = $filePath;
            $template['fileName'] = $fileName;
            $request->session()->flash('modal',$template);
            return back();
        }
    }
}

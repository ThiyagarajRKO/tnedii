<?php

namespace Impiger\InnovationVoucherProgram\Http\Controllers;

use Impiger\Base\Events\BeforeEditContentEvent;
use Impiger\InnovationVoucherProgram\Http\Requests\IvpKnowledgePartnerRequest;
use Impiger\InnovationVoucherProgram\Repositories\Interfaces\IvpKnowledgePartnerInterface;
use Impiger\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Exception;
use Impiger\InnovationVoucherProgram\Tables\IvpKnowledgePartnerTable;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\DeletedContentEvent;
use Impiger\Base\Events\UpdatedContentEvent;
use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\InnovationVoucherProgram\Forms\IvpKnowledgePartnerForm;
use Impiger\Base\Forms\FormBuilder;
use Assets;
use App\Utils\CrudHelper;
use Impiger\Crud\Imports\BulkImport;
use Impiger\Crud\Imports\BulkErrorExport;
use Excel;
use Illuminate\Support\Str;

class IvpKnowledgePartnerController extends BaseController
{
    /**
     * @var IvpKnowledgePartnerInterface
     */
    protected $ivpKnowledgePartnerRepository;

    /**
     * @param IvpKnowledgePartnerInterface $ivpKnowledgePartnerRepository
     */
    public function __construct(IvpKnowledgePartnerInterface $ivpKnowledgePartnerRepository)
    {
        $this->ivpKnowledgePartnerRepository = $ivpKnowledgePartnerRepository;

        Assets::addScriptsDirectly([
            'vendor/core/plugins/crud/js/custom_save_storage.js',
            'vendor/core/plugins/crud/js/crud_utils.js'
            
            
        ])
        ->addStylesDirectly([
            'vendor/core/plugins/crud/css/module_custom_styles.css'
            
        ]);
    }

    /**
     * @param IvpKnowledgePartnerTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(IvpKnowledgePartnerTable $table)
    {
        page_title()->setTitle(trans('plugins/innovation-voucher-program::ivp-knowledge-partner.name'));

        return $table->renderTable();
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/innovation-voucher-program::ivp-knowledge-partner.create'));

        return $formBuilder->create(IvpKnowledgePartnerForm::class)->renderForm();
    }

    /**
     * @param IvpKnowledgePartnerRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store(IvpKnowledgePartnerRequest $request, BaseHttpResponse $response )
    {
        
        
        $ivpKnowledgePartner = $this->ivpKnowledgePartnerRepository->createOrUpdate($request->input());
        
        event(new CreatedContentEvent(IVP_KNOWLEDGE_PARTNER_MODULE_SCREEN_NAME, $request, $ivpKnowledgePartner));
        
        
        return $response
            ->setPreviousUrl(route('ivp-knowledge-partner.index'))
            ->setNextUrl(route('ivp-knowledge-partner.edit', $ivpKnowledgePartner->id))
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
        
        $ivpKnowledgePartner = $this->ivpKnowledgePartnerRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $ivpKnowledgePartner));

        $name = ($ivpKnowledgePartner->name) ? ' "' . $ivpKnowledgePartner->name . '"' : "";
        page_title()->setTitle(trans('plugins/innovation-voucher-program::ivp-knowledge-partner.edit') . $name);

        return $formBuilder->create(IvpKnowledgePartnerForm::class, ['model' => $ivpKnowledgePartner])->renderForm();
    }

    /**
     * @param int $id
     * @param Request $request
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function viewdetail($id, FormBuilder $formBuilder, Request $request)
    {
        $ivpKnowledgePartner = $this->ivpKnowledgePartnerRepository->findOrFail($id);
        $name = ($ivpKnowledgePartner->name) ? ' "' . $ivpKnowledgePartner->name . '"' : "";
        page_title()->setTitle(trans('plugins/innovation-voucher-program::ivp-knowledge-partner.view') . $name);

        return $formBuilder->create(IvpKnowledgePartnerForm::class, ['model' => $ivpKnowledgePartner, 'isView' => true])->renderForm();
    }
    

    /**
     * @param int $id
     * @param IvpKnowledgePartnerRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, IvpKnowledgePartnerRequest $request, BaseHttpResponse $response )
    {
        
        $ivpKnowledgePartner = $this->ivpKnowledgePartnerRepository->findOrFail($id);
        
        
        $ivpKnowledgePartner->fill($request->input());

        $this->ivpKnowledgePartnerRepository->createOrUpdate($ivpKnowledgePartner);

        event(new UpdatedContentEvent(IVP_KNOWLEDGE_PARTNER_MODULE_SCREEN_NAME, $request, $ivpKnowledgePartner));
        
        
        return $response
            ->setPreviousUrl(route('ivp-knowledge-partner.index'))
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
            $ivpKnowledgePartner = $this->ivpKnowledgePartnerRepository->findOrFail($id);

            $dataExist = CrudHelper::isDependentDataExist('ivp-knowledge-partner', array($id), "Impiger\InnovationVoucherProgram\Models\IvpKnowledgePartner");

            if($dataExist) {
                return $response
                    ->setError()
                    ->setMessage($dataExist);
            }

            $this->ivpKnowledgePartnerRepository->delete($ivpKnowledgePartner);
            
            event(new DeletedContentEvent(IVP_KNOWLEDGE_PARTNER_MODULE_SCREEN_NAME, $request, $ivpKnowledgePartner));

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

        $dataExist = CrudHelper::isDependentDataExist('ivp-knowledge-partner', $ids, "Impiger\InnovationVoucherProgram\Models\IvpKnowledgePartner");

        if($dataExist) {
            return $response
                ->setError()
                ->setMessage($dataExist);
        }

        foreach ($ids as $id) {
            $ivpKnowledgePartner = $this->ivpKnowledgePartnerRepository->findOrFail($id);
            $this->ivpKnowledgePartnerRepository->delete($ivpKnowledgePartner);
            
            event(new DeletedContentEvent(IVP_KNOWLEDGE_PARTNER_MODULE_SCREEN_NAME, $request, $ivpKnowledgePartner));
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
            $bulkUpload = new BulkImport(new \Impiger\InnovationVoucherProgram\Models\IvpKnowledgePartner);
            $result = Excel::import($bulkUpload, $request->file('file'));
            $rowCount= $bulkUpload->getRowCount();
            if($rowCount==0){
                return $response
                        ->setError(true)
                        ->setMessage('There is no data to upload. Please upload file with valid data');
            }
            \Log::info(ucfirst(Str::plural(class_basename('\Impiger\InnovationVoucherProgram\Models\IvpKnowledgePartner')))." has been uploaded successfully");
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
            \Log::warning(count($errors)." ".ucfirst(Str::plural(class_basename('\Impiger\InnovationVoucherProgram\Models\IvpKnowledgePartner')))." has been inserted failed");
            $fileName = strtolower(class_basename('\Impiger\InnovationVoucherProgram\Models\IvpKnowledgePartner')) . "_" . \Carbon\Carbon::now()->format('Y-m-d_H-i-s') . ".csv";
            $filePath = "bulk_upload_errors" . "/" . $fileName;
            Excel::store(new BulkErrorExport($errors), $filePath);
            $template['filePath'] = $filePath;
            $template['fileName'] = $fileName;
            $request->session()->flash('modal',$template);
            return back();
        }
    }
}

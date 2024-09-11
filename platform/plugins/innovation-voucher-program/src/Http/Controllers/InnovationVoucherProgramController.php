<?php

namespace Impiger\InnovationVoucherProgram\Http\Controllers;

use Impiger\Base\Events\BeforeEditContentEvent;
use Impiger\InnovationVoucherProgram\Http\Requests\InnovationVoucherProgramRequest;
use Impiger\InnovationVoucherProgram\Repositories\Interfaces\InnovationVoucherProgramInterface;
use Impiger\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Exception;
use Impiger\InnovationVoucherProgram\Tables\InnovationVoucherProgramTable;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\DeletedContentEvent;
use Impiger\Base\Events\UpdatedContentEvent;
use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\InnovationVoucherProgram\Forms\InnovationVoucherProgramForm;
use Impiger\Base\Forms\FormBuilder;
use Assets;
use App\Utils\CrudHelper;
use Impiger\Crud\Imports\BulkImport;
use Impiger\Crud\Imports\BulkErrorExport;
use Excel;
use Illuminate\Support\Str;

class InnovationVoucherProgramController extends BaseController
{
    /**
     * @var InnovationVoucherProgramInterface
     */
    protected $innovationVoucherProgramRepository;

    /**
     * @param InnovationVoucherProgramInterface $innovationVoucherProgramRepository
     */
    public function __construct(InnovationVoucherProgramInterface $innovationVoucherProgramRepository)
    {
        $this->innovationVoucherProgramRepository = $innovationVoucherProgramRepository;

        Assets::addScriptsDirectly([
            'vendor/core/plugins/crud/js/custom_save_storage.js',
            'vendor/core/plugins/crud/js/crud_utils.js'
            ,'vendor/core/plugins/innovation-voucher-program/js/innovation-voucher-program.js'
            
        ])
        ->addStylesDirectly([
            'vendor/core/plugins/crud/css/module_custom_styles.css'
            
        ]);
    }

    /**
     * @param InnovationVoucherProgramTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(InnovationVoucherProgramTable $table)
    {
        page_title()->setTitle(trans('plugins/innovation-voucher-program::innovation-voucher-program.name'));

        return $table->renderTable();
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/innovation-voucher-program::innovation-voucher-program.create'));

        return $formBuilder->create(InnovationVoucherProgramForm::class)->renderForm();
    }

    /**
     * @param InnovationVoucherProgramRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store(InnovationVoucherProgramRequest $request, BaseHttpResponse $response )
    {
        $request->merge(['created_by' => \Auth::id()]);
//        dd($request->input());
        $innovationVoucherProgram = $this->innovationVoucherProgramRepository->createOrUpdate($request->input());
        CrudHelper::createUpdateSubforms($request, $innovationVoucherProgram, 'ivp_company_details',false,'');
		CrudHelper::createUpdateSubforms($request, $innovationVoucherProgram, 'ivp_knowledge_partners',false,'');
		
        event(new CreatedContentEvent(INNOVATION_VOUCHER_PROGRAM_MODULE_SCREEN_NAME, $request, $innovationVoucherProgram));
        
        
        return $response
            ->setPreviousUrl(route('innovation-voucher-program.index'))
            ->setNextUrl(route('innovation-voucher-program.edit', $innovationVoucherProgram->id))
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
        
        $innovationVoucherProgram = $this->innovationVoucherProgramRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $innovationVoucherProgram));

        $name = ($innovationVoucherProgram->name) ? ' "' . $innovationVoucherProgram->name . '"' : "";
        page_title()->setTitle(trans('plugins/innovation-voucher-program::innovation-voucher-program.edit') . $name);

        return $formBuilder->create(InnovationVoucherProgramForm::class, ['model' => $innovationVoucherProgram])->renderForm();
    }

    /**
     * @param int $id
     * @param Request $request
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function viewdetail($id, FormBuilder $formBuilder, Request $request)
    {
        $innovationVoucherProgram = $this->innovationVoucherProgramRepository->findOrFail($id);
        $name = ($innovationVoucherProgram->name) ? ' "' . $innovationVoucherProgram->name . '"' : "";
        page_title()->setTitle(trans('plugins/innovation-voucher-program::innovation-voucher-program.view') . $name);

        return $formBuilder->create(InnovationVoucherProgramForm::class, ['model' => $innovationVoucherProgram, 'isView' => true])->renderForm();
    }
    

    /**
     * @param int $id
     * @param InnovationVoucherProgramRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, InnovationVoucherProgramRequest $request, BaseHttpResponse $response )
    {
        
        $innovationVoucherProgram = $this->innovationVoucherProgramRepository->findOrFail($id);
        
        CrudHelper::createUpdateSubforms($request, $innovationVoucherProgram, 'ivp_company_details',$id,'');
		CrudHelper::createUpdateSubforms($request, $innovationVoucherProgram, 'ivp_knowledge_partners',$id,'');
		
        $innovationVoucherProgram->fill($request->input());

        $this->innovationVoucherProgramRepository->createOrUpdate($innovationVoucherProgram);

        event(new UpdatedContentEvent(INNOVATION_VOUCHER_PROGRAM_MODULE_SCREEN_NAME, $request, $innovationVoucherProgram));
        
        
        return $response
            ->setPreviousUrl(route('innovation-voucher-program.index'))
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
            $innovationVoucherProgram = $this->innovationVoucherProgramRepository->findOrFail($id);

            $dataExist = CrudHelper::isDependentDataExist('innovation-voucher-program', array($id), "Impiger\InnovationVoucherProgram\Models\InnovationVoucherProgram");

            if($dataExist) {
                return $response
                    ->setError()
                    ->setMessage($dataExist);
            }

            $this->innovationVoucherProgramRepository->delete($innovationVoucherProgram);
            
            event(new DeletedContentEvent(INNOVATION_VOUCHER_PROGRAM_MODULE_SCREEN_NAME, $request, $innovationVoucherProgram));

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

        $dataExist = CrudHelper::isDependentDataExist('innovation-voucher-program', $ids, "Impiger\InnovationVoucherProgram\Models\InnovationVoucherProgram");

        if($dataExist) {
            return $response
                ->setError()
                ->setMessage($dataExist);
        }

        foreach ($ids as $id) {
            $innovationVoucherProgram = $this->innovationVoucherProgramRepository->findOrFail($id);
            $this->innovationVoucherProgramRepository->delete($innovationVoucherProgram);
            
            event(new DeletedContentEvent(INNOVATION_VOUCHER_PROGRAM_MODULE_SCREEN_NAME, $request, $innovationVoucherProgram));
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
            $bulkUpload = new BulkImport(new \Impiger\InnovationVoucherProgram\Models\InnovationVoucherProgram);
            $result = Excel::import($bulkUpload, $request->file('file'));
            $rowCount= $bulkUpload->getRowCount();
            if($rowCount==0){
                return $response
                        ->setError(true)
                        ->setMessage('There is no data to upload. Please upload file with valid data');
            }
            \Log::info(ucfirst(Str::plural(class_basename('\Impiger\InnovationVoucherProgram\Models\InnovationVoucherProgram')))." has been uploaded successfully");
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
            \Log::warning(count($errors)." ".ucfirst(Str::plural(class_basename('\Impiger\InnovationVoucherProgram\Models\InnovationVoucherProgram')))." has been inserted failed");
            $fileName = strtolower(class_basename('\Impiger\InnovationVoucherProgram\Models\InnovationVoucherProgram')) . "_" . \Carbon\Carbon::now()->format('Y-m-d_H-i-s') . ".csv";
            $filePath = "bulk_upload_errors" . "/" . $fileName;
            Excel::store(new BulkErrorExport($errors), $filePath);
            $template['filePath'] = $filePath;
            $template['fileName'] = $fileName;
            $request->session()->flash('modal',$template);
            return back();
        }
    }
}

<?php

namespace Impiger\MasterDetail\Http\Controllers;

use Impiger\Base\Events\BeforeEditContentEvent;
use Impiger\MasterDetail\Http\Requests\MilestoneRequest;
use Impiger\MasterDetail\Repositories\Interfaces\MilestoneInterface;
use Impiger\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Exception;
use Impiger\MasterDetail\Tables\MilestoneTable;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\DeletedContentEvent;
use Impiger\Base\Events\UpdatedContentEvent;
use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\MasterDetail\Forms\MilestoneForm;
use Impiger\Base\Forms\FormBuilder;
use Assets;
use App\Utils\CrudHelper;
use Impiger\Crud\Imports\BulkImport;
use Impiger\Crud\Imports\BulkErrorExport;
use Excel;
use Illuminate\Support\Str;

class MilestoneController extends BaseController
{
    /**
     * @var MilestoneInterface
     */
    protected $milestoneRepository;

    /**
     * @param MilestoneInterface $milestoneRepository
     */
    public function __construct(MilestoneInterface $milestoneRepository)
    {
        $this->milestoneRepository = $milestoneRepository;

        Assets::addScriptsDirectly([
            'vendor/core/plugins/crud/js/custom_save_storage.js',
            'vendor/core/plugins/crud/js/crud_utils.js'
            
            
        ])
        ->addStylesDirectly([
            'vendor/core/plugins/crud/css/module_custom_styles.css'
            
        ]);
    }

    /**
     * @param MilestoneTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(MilestoneTable $table)
    {
        page_title()->setTitle(trans('plugins/master-detail::milestone.name'));

        return $table->renderTable();
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/master-detail::milestone.create'));

        return $formBuilder->create(MilestoneForm::class)->renderForm();
    }

    /**
     * @param MilestoneRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store(MilestoneRequest $request, BaseHttpResponse $response )
    {
        
        
        $milestone = $this->milestoneRepository->createOrUpdate($request->input());
        
        event(new CreatedContentEvent(MILESTONE_MODULE_SCREEN_NAME, $request, $milestone));
        
        
        return $response
            ->setPreviousUrl(route('milestone.index'))
            ->setNextUrl(route('milestone.edit', $milestone->id))
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
        
        $milestone = $this->milestoneRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $milestone));

        $name = ($milestone->name) ? ' "' . $milestone->name . '"' : "";
        page_title()->setTitle(trans('plugins/master-detail::milestone.edit') . $name);

        return $formBuilder->create(MilestoneForm::class, ['model' => $milestone])->renderForm();
    }

    /**
     * @param int $id
     * @param Request $request
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function viewdetail($id, FormBuilder $formBuilder, Request $request)
    {
        $milestone = $this->milestoneRepository->findOrFail($id);
        $name = ($milestone->name) ? ' "' . $milestone->name . '"' : "";
        page_title()->setTitle(trans('plugins/master-detail::milestone.view') . $name);

        return $formBuilder->create(MilestoneForm::class, ['model' => $milestone, 'isView' => true])->renderForm();
    }
    

    /**
     * @param int $id
     * @param MilestoneRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, MilestoneRequest $request, BaseHttpResponse $response )
    {
        
        $milestone = $this->milestoneRepository->findOrFail($id);
        
        
        $milestone->fill($request->input());

        $this->milestoneRepository->createOrUpdate($milestone);

        event(new UpdatedContentEvent(MILESTONE_MODULE_SCREEN_NAME, $request, $milestone));
        
        
        return $response
            ->setPreviousUrl(route('milestone.index'))
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
            $milestone = $this->milestoneRepository->findOrFail($id);

            $dataExist = CrudHelper::isDependentDataExist('milestone', array($id), "Impiger\MasterDetail\Models\Milestone");

            if($dataExist) {
                return $response
                    ->setError()
                    ->setMessage($dataExist);
            }

            $this->milestoneRepository->delete($milestone);
            
            event(new DeletedContentEvent(MILESTONE_MODULE_SCREEN_NAME, $request, $milestone));

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

        $dataExist = CrudHelper::isDependentDataExist('milestone', $ids, "Impiger\MasterDetail\Models\Milestone");

        if($dataExist) {
            return $response
                ->setError()
                ->setMessage($dataExist);
        }

        foreach ($ids as $id) {
            $milestone = $this->milestoneRepository->findOrFail($id);
            $this->milestoneRepository->delete($milestone);
            
            event(new DeletedContentEvent(MILESTONE_MODULE_SCREEN_NAME, $request, $milestone));
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
            $bulkUpload = new BulkImport(new \Impiger\MasterDetail\Models\Milestone);
            $result = Excel::import($bulkUpload, $request->file('file'));
            $rowCount= $bulkUpload->getRowCount();
            if($rowCount==0){
                return $response
                        ->setError(true)
                        ->setMessage('There is no data to upload. Please upload file with valid data');
            }
            \Log::info(ucfirst(Str::plural(class_basename('\Impiger\MasterDetail\Models\Milestone')))." has been uploaded successfully");
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
            \Log::warning(count($errors)." ".ucfirst(Str::plural(class_basename('\Impiger\MasterDetail\Models\Milestone')))." has been inserted failed");
            $fileName = strtolower(class_basename('\Impiger\MasterDetail\Models\Milestone')) . "_" . \Carbon\Carbon::now()->format('Y-m-d_H-i-s') . ".csv";
            $filePath = "bulk_upload_errors" . "/" . $fileName;
            Excel::store(new BulkErrorExport($errors), $filePath);
            $template['filePath'] = $filePath;
            $template['fileName'] = $fileName;
            $request->session()->flash('modal',$template);
            return back();
        }
    }
}

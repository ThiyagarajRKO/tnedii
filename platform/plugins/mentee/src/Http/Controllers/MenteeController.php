<?php

namespace Impiger\Mentee\Http\Controllers;

use Impiger\Base\Events\BeforeEditContentEvent;
use Impiger\Mentee\Http\Requests\MenteeRequest;
use Impiger\Mentee\Repositories\Interfaces\MenteeInterface;
use Impiger\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Exception;
use Impiger\Mentee\Tables\MenteeTable;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\DeletedContentEvent;
use Impiger\Base\Events\UpdatedContentEvent;
use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\Mentee\Forms\MenteeForm;
use Impiger\Base\Forms\FormBuilder;
use Assets;
use App\Utils\CrudHelper;
use Impiger\Crud\Imports\BulkImport;
use Impiger\Crud\Imports\BulkErrorExport;
use Excel;
use Illuminate\Support\Str;

class MenteeController extends BaseController
{
    /**
     * @var MenteeInterface
     */
    protected $menteeRepository;

    /**
     * @param MenteeInterface $menteeRepository
     */
    public function __construct(MenteeInterface $menteeRepository)
    {
        $this->menteeRepository = $menteeRepository;

        Assets::addScriptsDirectly([
            'vendor/core/plugins/crud/js/custom_save_storage.js',
            'vendor/core/plugins/crud/js/crud_utils.js',
            'vendor/core/plugins/mentee/js/mentee.js',
            
            
        ])
        ->addStylesDirectly([
            'vendor/core/plugins/crud/css/module_custom_styles.css'
            
        ]);
    }

    /**
     * @param MenteeTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(MenteeTable $table)
    {
        page_title()->setTitle(trans('plugins/mentee::mentee.name'));

        return $table->renderTable();
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/mentee::mentee.create'));

        return $formBuilder->create(MenteeForm::class)->renderForm();
    }

    /**
     * @param MenteeRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store(MenteeRequest $request, BaseHttpResponse $response )
    {
        
        
        $mentee = $this->menteeRepository->createOrUpdate($request->input());
        
        event(new CreatedContentEvent(MENTEE_MODULE_SCREEN_NAME, $request, $mentee));
        
        
        return $response
            ->setPreviousUrl(route('mentee.index'))
            ->setNextUrl(route('mentee.edit', $mentee->id))
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
        
        $mentee = $this->menteeRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $mentee));

        $name = ($mentee->name) ? ' "' . $mentee->name . '"' : "";
        page_title()->setTitle(trans('plugins/mentee::mentee.edit') . $name);

        return $formBuilder->create(MenteeForm::class, ['model' => $mentee])->renderForm();
    }

    /**
     * @param int $id
     * @param Request $request
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function viewdetail($id, FormBuilder $formBuilder, Request $request)
    {
        $mentee = $this->menteeRepository->findOrFail($id);
        $name = ($mentee->name) ? ' "' . $mentee->name . '"' : "";
        page_title()->setTitle(trans('plugins/mentee::mentee.view') . $name);

        return $formBuilder->create(MenteeForm::class, ['model' => $mentee, 'isView' => true])->renderForm();
    }
    

    /**
     * @param int $id
     * @param MenteeRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, MenteeRequest $request, BaseHttpResponse $response )
    {
        
        $mentee = $this->menteeRepository->findOrFail($id);
        
        
        $mentee->fill($request->input());

        $this->menteeRepository->createOrUpdate($mentee);

        event(new UpdatedContentEvent(MENTEE_MODULE_SCREEN_NAME, $request, $mentee));
        
        
        return $response
            ->setPreviousUrl(route('mentee.index'))
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
            $mentee = $this->menteeRepository->findOrFail($id);

            $dataExist = CrudHelper::isDependentDataExist('mentee', array($id), "Impiger\Mentee\Models\Mentee");

            if($dataExist) {
                return $response
                    ->setError()
                    ->setMessage($dataExist);
            }

            $this->menteeRepository->delete($mentee);
            
            event(new DeletedContentEvent(MENTEE_MODULE_SCREEN_NAME, $request, $mentee));

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

        $dataExist = CrudHelper::isDependentDataExist('mentee', $ids, "Impiger\Mentee\Models\Mentee");

        if($dataExist) {
            return $response
                ->setError()
                ->setMessage($dataExist);
        }

        foreach ($ids as $id) {
            $mentee = $this->menteeRepository->findOrFail($id);
            $this->menteeRepository->delete($mentee);
            
            event(new DeletedContentEvent(MENTEE_MODULE_SCREEN_NAME, $request, $mentee));
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
            $bulkUpload = new BulkImport(new \Impiger\Mentee\Models\Mentee);
            $result = Excel::import($bulkUpload, $request->file('file'));
            $rowCount= $bulkUpload->getRowCount();
            if($rowCount==0){
                return $response
                        ->setError(true)
                        ->setMessage('There is no data to upload. Please upload file with valid data');
            }
            \Log::info(ucfirst(Str::plural(class_basename('\Impiger\Mentee\Models\Mentee')))." has been uploaded successfully");
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
            \Log::warning(count($errors)." ".ucfirst(Str::plural(class_basename('\Impiger\Mentee\Models\Mentee')))." has been inserted failed");
            $fileName = strtolower(class_basename('\Impiger\Mentee\Models\Mentee')) . "_" . \Carbon\Carbon::now()->format('Y-m-d_H-i-s') . ".csv";
            $filePath = "bulk_upload_errors" . "/" . $fileName;
            Excel::store(new BulkErrorExport($errors), $filePath);
            $template['filePath'] = $filePath;
            $template['fileName'] = $fileName;
            $request->session()->flash('modal',$template);
            return back();
        }
    }
}

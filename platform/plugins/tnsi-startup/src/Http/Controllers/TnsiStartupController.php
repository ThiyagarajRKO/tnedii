<?php

namespace Impiger\TnsiStartup\Http\Controllers;

use Impiger\Base\Events\BeforeEditContentEvent;
use Impiger\TnsiStartup\Http\Requests\TnsiStartupRequest;
use Impiger\TnsiStartup\Repositories\Interfaces\TnsiStartupInterface;
use Impiger\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Exception;
use Impiger\TnsiStartup\Tables\TnsiStartupTable;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\DeletedContentEvent;
use Impiger\Base\Events\UpdatedContentEvent;
use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\TnsiStartup\Forms\TnsiStartupForm;
use Impiger\Base\Forms\FormBuilder;
use Assets;
use App\Utils\CrudHelper;
use Impiger\Crud\Imports\BulkImport;
use Impiger\Crud\Imports\BulkErrorExport;
use Excel;
use Illuminate\Support\Str;

class TnsiStartupController extends BaseController
{
    /**
     * @var TnsiStartupInterface
     */
    protected $tnsiStartupRepository;

    /**
     * @param TnsiStartupInterface $tnsiStartupRepository
     */
    public function __construct(TnsiStartupInterface $tnsiStartupRepository)
    {
        $this->tnsiStartupRepository = $tnsiStartupRepository;

        Assets::addScriptsDirectly([
            'vendor/core/plugins/crud/js/custom_save_storage.js',
            'vendor/core/plugins/crud/js/crud_utils.js'
            ,'vendor/core/plugins/tnsi-startup/js/tnsi-startup.js'
            
        ])
        ->addStylesDirectly([
            'vendor/core/plugins/crud/css/module_custom_styles.css'
            
        ]);
    }

    /**
     * @param TnsiStartupTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(TnsiStartupTable $table)
    {
        page_title()->setTitle(trans('plugins/tnsi-startup::tnsi-startup.name'));

        return $table->renderTable();
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/tnsi-startup::tnsi-startup.create'));

        return $formBuilder->create(TnsiStartupForm::class)->renderForm();
    }

    /**
     * @param TnsiStartupRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store(TnsiStartupRequest $request, BaseHttpResponse $response )
    {
        $request->merge(['created_by' => \Auth::id()]);
        
        $isExisting = \DB::table('tnsi_startup')->where('team_members','like', '%'.$request->team_members[0][0]['value'].'%')->first();
        if($isExisting){
            return $response
                ->setError()
                ->setMessage('You have already submitted a TNSI application');
        }
        
        $tnsiStartup = $this->tnsiStartupRepository->createOrUpdate($request->input());
        
        event(new CreatedContentEvent(TNSI_STARTUP_MODULE_SCREEN_NAME, $request, $tnsiStartup));
        
        
        return $response
            ->setPreviousUrl(route('tnsi-startup.index'))
            ->setNextUrl(route('tnsi-startup.edit', $tnsiStartup->id))
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
        
        $tnsiStartup = $this->tnsiStartupRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $tnsiStartup));

        $name = ($tnsiStartup->name) ? ' "' . $tnsiStartup->name . '"' : "";
        page_title()->setTitle(trans('plugins/tnsi-startup::tnsi-startup.edit') . $name);

        return $formBuilder->create(TnsiStartupForm::class, ['model' => $tnsiStartup])->renderForm();
    }

    /**
     * @param int $id
     * @param Request $request
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function viewdetail($id, FormBuilder $formBuilder, Request $request)
    {
        $tnsiStartup = $this->tnsiStartupRepository->findOrFail($id);
        $name = ($tnsiStartup->name) ? ' "' . $tnsiStartup->name . '"' : "";
        page_title()->setTitle(trans('plugins/tnsi-startup::tnsi-startup.view') . $name);

        return $formBuilder->create(TnsiStartupForm::class, ['model' => $tnsiStartup, 'isView' => true])->renderForm();
    }
    

    /**
     * @param int $id
     * @param TnsiStartupRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, TnsiStartupRequest $request, BaseHttpResponse $response )
    {
        
        $tnsiStartup = $this->tnsiStartupRepository->findOrFail($id);
        
        
        $tnsiStartup->fill($request->input());

        $this->tnsiStartupRepository->createOrUpdate($tnsiStartup);

        event(new UpdatedContentEvent(TNSI_STARTUP_MODULE_SCREEN_NAME, $request, $tnsiStartup));
        
        
        return $response
            ->setPreviousUrl(route('tnsi-startup.index'))
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
            $tnsiStartup = $this->tnsiStartupRepository->findOrFail($id);

            $dataExist = CrudHelper::isDependentDataExist('tnsi-startup', array($id), "Impiger\TnsiStartup\Models\TnsiStartup");

            if($dataExist) {
                return $response
                    ->setError()
                    ->setMessage($dataExist);
            }

            $this->tnsiStartupRepository->delete($tnsiStartup);
            
            event(new DeletedContentEvent(TNSI_STARTUP_MODULE_SCREEN_NAME, $request, $tnsiStartup));

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

        $dataExist = CrudHelper::isDependentDataExist('tnsi-startup', $ids, "Impiger\TnsiStartup\Models\TnsiStartup");

        if($dataExist) {
            return $response
                ->setError()
                ->setMessage($dataExist);
        }

        foreach ($ids as $id) {
            $tnsiStartup = $this->tnsiStartupRepository->findOrFail($id);
            $this->tnsiStartupRepository->delete($tnsiStartup);
            
            event(new DeletedContentEvent(TNSI_STARTUP_MODULE_SCREEN_NAME, $request, $tnsiStartup));
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
            $bulkUpload = new BulkImport(new \Impiger\TnsiStartup\Models\TnsiStartup);
            $result = Excel::import($bulkUpload, $request->file('file'));
            $rowCount= $bulkUpload->getRowCount();
            if($rowCount==0){
                return $response
                        ->setError(true)
                        ->setMessage('There is no data to upload. Please upload file with valid data');
            }
            \Log::info(ucfirst(Str::plural(class_basename('\Impiger\TnsiStartup\Models\TnsiStartup')))." has been uploaded successfully");
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
            \Log::warning(count($errors)." ".ucfirst(Str::plural(class_basename('\Impiger\TnsiStartup\Models\TnsiStartup')))." has been inserted failed");
            $fileName = strtolower(class_basename('\Impiger\TnsiStartup\Models\TnsiStartup')) . "_" . \Carbon\Carbon::now()->format('Y-m-d_H-i-s') . ".csv";
            $filePath = "bulk_upload_errors" . "/" . $fileName;
            Excel::store(new BulkErrorExport($errors), $filePath);
            $template['filePath'] = $filePath;
            $template['fileName'] = $fileName;
            $request->session()->flash('modal',$template);
            return back();
        }
    }
}

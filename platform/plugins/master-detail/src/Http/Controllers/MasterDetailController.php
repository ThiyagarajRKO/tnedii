<?php

namespace Impiger\MasterDetail\Http\Controllers;

use Impiger\Base\Events\BeforeEditContentEvent;
use Impiger\MasterDetail\Http\Requests\MasterDetailRequest;
use Impiger\MasterDetail\Repositories\Interfaces\MasterDetailInterface;
use Impiger\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Exception;
use Impiger\MasterDetail\Tables\MasterDetailTable;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\DeletedContentEvent;
use Impiger\Base\Events\UpdatedContentEvent;
use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\MasterDetail\Forms\MasterDetailForm;
use Impiger\Base\Forms\FormBuilder;
use Assets;
use App\Utils\CrudHelper;
use Impiger\Crud\Imports\BulkImport;
use Impiger\Crud\Imports\BulkErrorExport;
use Excel;
use Illuminate\Support\Str;

class MasterDetailController extends BaseController
{
    /**
     * @var MasterDetailInterface
     */
    protected $masterDetailRepository;

    /**
     * @param MasterDetailInterface $masterDetailRepository
     */
    public function __construct(MasterDetailInterface $masterDetailRepository)
    {
        $this->masterDetailRepository = $masterDetailRepository;

        Assets::addScriptsDirectly([
            'vendor/core/plugins/crud/js/custom_save_storage.js',
            'vendor/core/plugins/crud/js/crud_utils.js'
            
            
        ])
        ->addStylesDirectly([
            'vendor/core/plugins/crud/css/module_custom_styles.css'
            
        ]);
    }

    /**
     * @param MasterDetailTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(MasterDetailTable $table)
    {
        page_title()->setTitle(trans('plugins/master-detail::master-detail.name'));

        return $table->renderTable();
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/master-detail::master-detail.create'));

        return $formBuilder->create(MasterDetailForm::class)->renderForm();
    }

    /**
     * @param MasterDetailRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store(MasterDetailRequest $request, BaseHttpResponse $response )
    {
        $input =array_merge($request->input(),['slug'=>Str::slug($request->input('name'))]); 
                
        $masterDetail = $this->masterDetailRepository->createOrUpdate($input);
        
        event(new CreatedContentEvent(MASTER_DETAIL_MODULE_SCREEN_NAME, $request, $masterDetail));
        
        return $response
            ->setPreviousUrl(route('master-detail.index'))
            ->setNextUrl(route('master-detail.edit', $masterDetail->id))
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
        $masterDetail = $this->masterDetailRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $masterDetail));

        $name = ($masterDetail->name) ? ' "' . $masterDetail->name . '"' : "";
        page_title()->setTitle(trans('plugins/master-detail::master-detail.edit') . $name);

        return $formBuilder->create(MasterDetailForm::class, ['model' => $masterDetail])->renderForm();
    }

    /**
     * @param int $id
     * @param Request $request
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function viewdetail($id, FormBuilder $formBuilder, Request $request)
    {
        $masterDetail = $this->masterDetailRepository->findOrFail($id);
        $name = ($masterDetail->name) ? ' "' . $masterDetail->name . '"' : "";
        page_title()->setTitle(trans('plugins/master-detail::master-detail.view') . $name);

        return $formBuilder->create(MasterDetailForm::class, ['model' => $masterDetail, 'isView' => true])->renderForm();
    }
    

    /**
     * @param int $id
     * @param MasterDetailRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, MasterDetailRequest $request, BaseHttpResponse $response )
    {
        $masterDetail = $this->masterDetailRepository->findOrFail($id);
        
        
        $masterDetail->fill($request->input());

        $this->masterDetailRepository->createOrUpdate($masterDetail);

        event(new UpdatedContentEvent(MASTER_DETAIL_MODULE_SCREEN_NAME, $request, $masterDetail));
        
        return $response
            ->setPreviousUrl(route('master-detail.index'))
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
            $masterDetail = $this->masterDetailRepository->findOrFail($id);
        
            $dataExist = CrudHelper::isDependentDataExist('master-detail', array($id), "Impiger\MasterDetail\Models\MasterDetail");
                
            if($dataExist) {
                return $response
                    ->setError()
                    ->setMessage($dataExist);
            }

            $this->masterDetailRepository->delete($masterDetail);

            event(new DeletedContentEvent(MASTER_DETAIL_MODULE_SCREEN_NAME, $request, $masterDetail));

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
        
        $dataExist = CrudHelper::isDependentDataExist('master-detail', $ids, "Impiger\MasterDetail\Models\MasterDetail");
            
        if($dataExist) {
            return $response
                ->setError()
                ->setMessage($dataExist);
        }

        foreach ($ids as $id) {
            $masterDetail = $this->masterDetailRepository->findOrFail($id);
            $this->masterDetailRepository->delete($masterDetail);
            event(new DeletedContentEvent(MASTER_DETAIL_MODULE_SCREEN_NAME, $request, $masterDetail));
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
            $bulkUpload = new BulkImport(new \Impiger\MasterDetail\Models\MasterDetail);
            $result = Excel::import($bulkUpload, $request->file('file'));
            $rowCount= $bulkUpload->getRowCount();
            if($rowCount==0){
                return $response
                        ->setError(true)
                        ->setMessage('There is no data to upload. Please upload file with valid data');
            }
            \Log::info(ucfirst(Str::plural(class_basename('\Impiger\MasterDetail\Models\MasterDetail')))." has been uploaded successfully");
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
            \Log::warning(count($errors)." ".ucfirst(Str::plural(class_basename('\Impiger\MasterDetail\Models\MasterDetail')))." has been inserted failed");
            $fileName = strtolower(class_basename('\Impiger\MasterDetail\Models\MasterDetail')) . "_" . \Carbon\Carbon::now()->format('Y-m-d_H-i-s') . ".csv";
            $filePath = "bulk_upload_errors" . "/" . $fileName;
            Excel::store(new BulkErrorExport($errors), $filePath);
            $template['filePath'] = $filePath;
            $template['fileName'] = $fileName;
            $request->session()->flash('modal',$template);
            return back();
        }
    }
}

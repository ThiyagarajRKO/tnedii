<?php

namespace Impiger\MasterDetail\Http\Controllers;

use Impiger\Base\Events\BeforeEditContentEvent;
use Impiger\MasterDetail\Http\Requests\RegionRequest;
use Impiger\MasterDetail\Repositories\Interfaces\RegionInterface;
use Impiger\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Exception;
use Impiger\MasterDetail\Tables\RegionTable;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\DeletedContentEvent;
use Impiger\Base\Events\UpdatedContentEvent;
use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\MasterDetail\Forms\RegionForm;
use Impiger\Base\Forms\FormBuilder;
use Assets;
use App\Utils\CrudHelper;
use Impiger\Crud\Imports\BulkImport;
use Impiger\Crud\Imports\BulkErrorExport;
use Excel;
use Illuminate\Support\Str;

class RegionController extends BaseController
{
    /**
     * @var RegionInterface
     */
    protected $regionRepository;

    /**
     * @param RegionInterface $regionRepository
     */
    public function __construct(RegionInterface $regionRepository)
    {
        $this->regionRepository = $regionRepository;

        Assets::addScriptsDirectly([
            'vendor/core/plugins/crud/js/custom_save_storage.js',
            'vendor/core/plugins/crud/js/crud_utils.js'
            
            
        ])
        ->addStylesDirectly([
            'vendor/core/plugins/crud/css/module_custom_styles.css'
            
        ]);
    }

    /**
     * @param RegionTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(RegionTable $table)
    {
        page_title()->setTitle(trans('plugins/master-detail::region.name'));

        return $table->renderTable();
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/master-detail::region.create'));

        return $formBuilder->create(RegionForm::class)->renderForm();
    }

    /**
     * @param RegionRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store(RegionRequest $request, BaseHttpResponse $response )
    {
        
        
        $region = $this->regionRepository->createOrUpdate($request->input());
        
        event(new CreatedContentEvent(REGION_MODULE_SCREEN_NAME, $request, $region));
        
        
        return $response
            ->setPreviousUrl(route('region.index'))
            ->setNextUrl(route('region.edit', $region->id))
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
        
        $region = $this->regionRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $region));

        $name = ($region->name) ? ' "' . $region->name . '"' : "";
        page_title()->setTitle(trans('plugins/master-detail::region.edit') . $name);

        return $formBuilder->create(RegionForm::class, ['model' => $region])->renderForm();
    }

    /**
     * @param int $id
     * @param Request $request
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function viewdetail($id, FormBuilder $formBuilder, Request $request)
    {
        $region = $this->regionRepository->findOrFail($id);
        $name = ($region->name) ? ' "' . $region->name . '"' : "";
        page_title()->setTitle(trans('plugins/master-detail::region.view') . $name);

        return $formBuilder->create(RegionForm::class, ['model' => $region, 'isView' => true])->renderForm();
    }
    

    /**
     * @param int $id
     * @param RegionRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, RegionRequest $request, BaseHttpResponse $response )
    {
        
        $region = $this->regionRepository->findOrFail($id);
        
        
        $region->fill($request->input());

        $this->regionRepository->createOrUpdate($region);

        event(new UpdatedContentEvent(REGION_MODULE_SCREEN_NAME, $request, $region));
        
        
        return $response
            ->setPreviousUrl(route('region.index'))
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
            $region = $this->regionRepository->findOrFail($id);

            $dataExist = CrudHelper::isDependentDataExist('region', array($id), "Impiger\MasterDetail\Models\Region");

            if($dataExist) {
                return $response
                    ->setError()
                    ->setMessage($dataExist);
            }

            $this->regionRepository->delete($region);
            
            event(new DeletedContentEvent(REGION_MODULE_SCREEN_NAME, $request, $region));

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

        $dataExist = CrudHelper::isDependentDataExist('region', $ids, "Impiger\MasterDetail\Models\Region");

        if($dataExist) {
            return $response
                ->setError()
                ->setMessage($dataExist);
        }

        foreach ($ids as $id) {
            $region = $this->regionRepository->findOrFail($id);
            $this->regionRepository->delete($region);
            
            event(new DeletedContentEvent(REGION_MODULE_SCREEN_NAME, $request, $region));
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
            $bulkUpload = new BulkImport(new \Impiger\MasterDetail\Models\Region);
            $result = Excel::import($bulkUpload, $request->file('file'));
            $rowCount= $bulkUpload->getRowCount();
            if($rowCount==0){
                return $response
                        ->setError(true)
                        ->setMessage('There is no data to upload. Please upload file with valid data');
            }
            \Log::info(ucfirst(Str::plural(class_basename('\Impiger\MasterDetail\Models\Region')))." has been uploaded successfully");
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
            \Log::warning(count($errors)." ".ucfirst(Str::plural(class_basename('\Impiger\MasterDetail\Models\Region')))." has been inserted failed");
            $fileName = strtolower(class_basename('\Impiger\MasterDetail\Models\Region')) . "_" . \Carbon\Carbon::now()->format('Y-m-d_H-i-s') . ".csv";
            $filePath = "bulk_upload_errors" . "/" . $fileName;
            Excel::store(new BulkErrorExport($errors), $filePath);
            $template['filePath'] = $filePath;
            $template['fileName'] = $fileName;
            $request->session()->flash('modal',$template);
            return back();
        }
    }
}

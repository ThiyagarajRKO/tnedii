<?php

namespace Impiger\MasterDetail\Http\Controllers;

use Impiger\Base\Events\BeforeEditContentEvent;
use Impiger\MasterDetail\Http\Requests\DistrictRequest;
use Impiger\MasterDetail\Repositories\Interfaces\DistrictInterface;
use Impiger\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Exception;
use Impiger\MasterDetail\Tables\DistrictTable;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\DeletedContentEvent;
use Impiger\Base\Events\UpdatedContentEvent;
use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\MasterDetail\Forms\DistrictForm;
use Impiger\Base\Forms\FormBuilder;
use Assets;
use App\Utils\CrudHelper;
use Impiger\Crud\Imports\BulkImport;
use Impiger\Crud\Imports\BulkErrorExport;
use Excel;
use Illuminate\Support\Str;

class DistrictController extends BaseController
{
    /**
     * @var DistrictInterface
     */
    protected $districtRepository;

    /**
     * @param DistrictInterface $districtRepository
     */
    public function __construct(DistrictInterface $districtRepository)
    {
        $this->districtRepository = $districtRepository;

        Assets::addScriptsDirectly([
            'vendor/core/plugins/crud/js/custom_save_storage.js',
            'vendor/core/plugins/crud/js/crud_utils.js'
            
            
        ])
        ->addStylesDirectly([
            'vendor/core/plugins/crud/css/module_custom_styles.css'
            
        ]);
    }

    /**
     * @param DistrictTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(DistrictTable $table)
    {
        page_title()->setTitle(trans('plugins/master-detail::district.name'));

        return $table->renderTable();
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/master-detail::district.create'));

        return $formBuilder->create(DistrictForm::class)->renderForm();
    }

    /**
     * @param DistrictRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store(DistrictRequest $request, BaseHttpResponse $response )
    {
        $request->merge(['created_by' => \Auth::id()]);
        
        $district = $this->districtRepository->createOrUpdate($request->input());
        
        event(new CreatedContentEvent(DISTRICT_MODULE_SCREEN_NAME, $request, $district));
        
        
        return $response
            ->setPreviousUrl(route('district.index'))
            ->setNextUrl(route('district.edit', $district->id))
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
        
        $district = $this->districtRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $district));

        $name = ($district->name) ? ' "' . $district->name . '"' : "";
        page_title()->setTitle(trans('plugins/master-detail::district.edit') . $name);

        return $formBuilder->create(DistrictForm::class, ['model' => $district])->renderForm();
    }

    /**
     * @param int $id
     * @param Request $request
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function viewdetail($id, FormBuilder $formBuilder, Request $request)
    {
        $district = $this->districtRepository->findOrFail($id);
        $name = ($district->name) ? ' "' . $district->name . '"' : "";
        page_title()->setTitle(trans('plugins/master-detail::district.view') . $name);

        return $formBuilder->create(DistrictForm::class, ['model' => $district, 'isView' => true])->renderForm();
    }
    

    /**
     * @param int $id
     * @param DistrictRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, DistrictRequest $request, BaseHttpResponse $response )
    {
        
        $district = $this->districtRepository->findOrFail($id);
        
        
        $district->fill($request->input());

        $this->districtRepository->createOrUpdate($district);

        event(new UpdatedContentEvent(DISTRICT_MODULE_SCREEN_NAME, $request, $district));
        
        
        return $response
            ->setPreviousUrl(route('district.index'))
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
            $district = $this->districtRepository->findOrFail($id);

            $dataExist = CrudHelper::isDependentDataExist('district', array($id), "Impiger\MasterDetail\Models\District");

            if($dataExist) {
                return $response
                    ->setError()
                    ->setMessage($dataExist);
            }

            $this->districtRepository->delete($district);
            
            event(new DeletedContentEvent(DISTRICT_MODULE_SCREEN_NAME, $request, $district));

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

        $dataExist = CrudHelper::isDependentDataExist('district', $ids, "Impiger\MasterDetail\Models\District");

        if($dataExist) {
            return $response
                ->setError()
                ->setMessage($dataExist);
        }

        foreach ($ids as $id) {
            $district = $this->districtRepository->findOrFail($id);
            $this->districtRepository->delete($district);
            
            event(new DeletedContentEvent(DISTRICT_MODULE_SCREEN_NAME, $request, $district));
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
            $bulkUpload = new BulkImport(new \Impiger\MasterDetail\Models\District);
            $result = Excel::import($bulkUpload, $request->file('file'));
            $rowCount= $bulkUpload->getRowCount();
            if($rowCount==0){
                return $response
                        ->setError(true)
                        ->setMessage('There is no data to upload. Please upload file with valid data');
            }
            \Log::info(ucfirst(Str::plural(class_basename('\Impiger\MasterDetail\Models\District')))." has been uploaded successfully");
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
            \Log::warning(count($errors)." ".ucfirst(Str::plural(class_basename('\Impiger\MasterDetail\Models\District')))." has been inserted failed");
            $fileName = strtolower(class_basename('\Impiger\MasterDetail\Models\District')) . "_" . \Carbon\Carbon::now()->format('Y-m-d_H-i-s') . ".csv";
            $filePath = "bulk_upload_errors" . "/" . $fileName;
            Excel::store(new BulkErrorExport($errors), $filePath);
            $template['filePath'] = $filePath;
            $template['fileName'] = $fileName;
            $request->session()->flash('modal',$template);
            return back();
        }
    }
}

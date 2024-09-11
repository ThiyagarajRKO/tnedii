<?php

namespace Impiger\MasterDetail\Http\Controllers;

use Impiger\Base\Events\BeforeEditContentEvent;
use Impiger\MasterDetail\Http\Requests\HolidayRequest;
use Impiger\MasterDetail\Repositories\Interfaces\HolidayInterface;
use Impiger\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Exception;
use Impiger\MasterDetail\Tables\HolidayTable;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\DeletedContentEvent;
use Impiger\Base\Events\UpdatedContentEvent;
use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\MasterDetail\Forms\HolidayForm;
use Impiger\Base\Forms\FormBuilder;
use Assets;
use App\Utils\CrudHelper;
use Impiger\Crud\Imports\BulkImport;
use Impiger\Crud\Imports\BulkErrorExport;
use Excel;
use Illuminate\Support\Str;

class HolidayController extends BaseController
{
    /**
     * @var HolidayInterface
     */
    protected $holidayRepository;

    /**
     * @param HolidayInterface $holidayRepository
     */
    public function __construct(HolidayInterface $holidayRepository)
    {
        $this->holidayRepository = $holidayRepository;

        Assets::addScriptsDirectly([
            'vendor/core/plugins/crud/js/custom_save_storage.js',
            'vendor/core/plugins/crud/js/crud_utils.js'
            
            
        ])
        ->addStylesDirectly([
            'vendor/core/plugins/crud/css/module_custom_styles.css'
            
        ]);
    }

    /**
     * @param HolidayTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(HolidayTable $table)
    {
        page_title()->setTitle(trans('plugins/master-detail::holiday.name'));

        return $table->renderTable();
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/master-detail::holiday.create'));

        return $formBuilder->create(HolidayForm::class)->renderForm();
    }

    /**
     * @param HolidayRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store(HolidayRequest $request, BaseHttpResponse $response )
    {
        
        
        $holiday = $this->holidayRepository->createOrUpdate($request->input());
        
        event(new CreatedContentEvent(HOLIDAY_MODULE_SCREEN_NAME, $request, $holiday));
        
        
        return $response
            ->setPreviousUrl(route('holiday.index'))
            ->setNextUrl(route('holiday.edit', $holiday->id))
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
        
        $holiday = $this->holidayRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $holiday));

        $name = ($holiday->name) ? ' "' . $holiday->name . '"' : "";
        page_title()->setTitle(trans('plugins/master-detail::holiday.edit') . $name);

        return $formBuilder->create(HolidayForm::class, ['model' => $holiday])->renderForm();
    }

    /**
     * @param int $id
     * @param Request $request
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function viewdetail($id, FormBuilder $formBuilder, Request $request)
    {
        $holiday = $this->holidayRepository->findOrFail($id);
        $name = ($holiday->name) ? ' "' . $holiday->name . '"' : "";
        page_title()->setTitle(trans('plugins/master-detail::holiday.view') . $name);

        return $formBuilder->create(HolidayForm::class, ['model' => $holiday, 'isView' => true])->renderForm();
    }
    

    /**
     * @param int $id
     * @param HolidayRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, HolidayRequest $request, BaseHttpResponse $response )
    {
        
        $holiday = $this->holidayRepository->findOrFail($id);
        
        
        $holiday->fill($request->input());

        $this->holidayRepository->createOrUpdate($holiday);

        event(new UpdatedContentEvent(HOLIDAY_MODULE_SCREEN_NAME, $request, $holiday));
        
        
        return $response
            ->setPreviousUrl(route('holiday.index'))
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
            $holiday = $this->holidayRepository->findOrFail($id);

            $dataExist = CrudHelper::isDependentDataExist('holiday', array($id), "Impiger\MasterDetail\Models\Holiday");

            if($dataExist) {
                return $response
                    ->setError()
                    ->setMessage($dataExist);
            }

            $this->holidayRepository->delete($holiday);
            
            event(new DeletedContentEvent(HOLIDAY_MODULE_SCREEN_NAME, $request, $holiday));

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

        $dataExist = CrudHelper::isDependentDataExist('holiday', $ids, "Impiger\MasterDetail\Models\Holiday");

        if($dataExist) {
            return $response
                ->setError()
                ->setMessage($dataExist);
        }

        foreach ($ids as $id) {
            $holiday = $this->holidayRepository->findOrFail($id);
            $this->holidayRepository->delete($holiday);
            
            event(new DeletedContentEvent(HOLIDAY_MODULE_SCREEN_NAME, $request, $holiday));
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
            $bulkUpload = new BulkImport(new \Impiger\MasterDetail\Models\Holiday);
            $result = Excel::import($bulkUpload, $request->file('file'));
            $rowCount= $bulkUpload->getRowCount();
            if($rowCount==0){
                return $response
                        ->setError(true)
                        ->setMessage('There is no data to upload. Please upload file with valid data');
            }
            \Log::info(ucfirst(Str::plural(class_basename('\Impiger\MasterDetail\Models\Holiday')))." has been uploaded successfully");
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
            \Log::warning(count($errors)." ".ucfirst(Str::plural(class_basename('\Impiger\MasterDetail\Models\Holiday')))." has been inserted failed");
            $fileName = strtolower(class_basename('\Impiger\MasterDetail\Models\Holiday')) . "_" . \Carbon\Carbon::now()->format('Y-m-d_H-i-s') . ".csv";
            $filePath = "bulk_upload_errors" . "/" . $fileName;
            Excel::store(new BulkErrorExport($errors), $filePath);
            $template['filePath'] = $filePath;
            $template['fileName'] = $fileName;
            $request->session()->flash('modal',$template);
            return back();
        }
    }
}

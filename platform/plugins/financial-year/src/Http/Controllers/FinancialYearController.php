<?php

namespace Impiger\FinancialYear\Http\Controllers;

use Impiger\Base\Events\BeforeEditContentEvent;
use Impiger\FinancialYear\Http\Requests\FinancialYearRequest;
use Impiger\FinancialYear\Repositories\Interfaces\FinancialYearInterface;
use Impiger\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Exception;
use Impiger\FinancialYear\Tables\FinancialYearTable;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\DeletedContentEvent;
use Impiger\Base\Events\UpdatedContentEvent;
use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\FinancialYear\Forms\FinancialYearForm;
use Impiger\Base\Forms\FormBuilder;
use Assets;
use App\Utils\CrudHelper;
use Impiger\Crud\Imports\BulkImport;
use Impiger\Crud\Imports\BulkErrorExport;
use Excel;
use Illuminate\Support\Str;

class FinancialYearController extends BaseController
{
    /**
     * @var FinancialYearInterface
     */
    protected $financialYearRepository;

    /**
     * @param FinancialYearInterface $financialYearRepository
     */
    public function __construct(FinancialYearInterface $financialYearRepository)
    {
        $this->financialYearRepository = $financialYearRepository;

        Assets::addScriptsDirectly([
            'vendor/core/plugins/crud/js/custom_save_storage.js',
            'vendor/core/plugins/crud/js/crud_utils.js'
            
            
        ])
        ->addStylesDirectly([
            'vendor/core/plugins/crud/css/module_custom_styles.css'
            
        ]);
    }

    /**
     * @param FinancialYearTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(FinancialYearTable $table)
    {
        page_title()->setTitle(trans('plugins/financial-year::financial-year.name'));

        return $table->renderTable();
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/financial-year::financial-year.create'));

        return $formBuilder->create(FinancialYearForm::class)->renderForm();
    }

    /**
     * @param FinancialYearRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store(FinancialYearRequest $request, BaseHttpResponse $response )
    {
        
        
        $financialYear = $this->financialYearRepository->createOrUpdate($request->input());
        
        event(new CreatedContentEvent(FINANCIAL_YEAR_MODULE_SCREEN_NAME, $request, $financialYear));
        
        
        return $response
            ->setPreviousUrl(route('financial-year.index'))
            ->setNextUrl(route('financial-year.edit', $financialYear->id))
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
        
        $financialYear = $this->financialYearRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $financialYear));

        $name = ($financialYear->name) ? ' "' . $financialYear->name . '"' : "";
        page_title()->setTitle(trans('plugins/financial-year::financial-year.edit') . $name);

        return $formBuilder->create(FinancialYearForm::class, ['model' => $financialYear])->renderForm();
    }

    /**
     * @param int $id
     * @param Request $request
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function viewdetail($id, FormBuilder $formBuilder, Request $request)
    {
        $financialYear = $this->financialYearRepository->findOrFail($id);
        $name = ($financialYear->name) ? ' "' . $financialYear->name . '"' : "";
        page_title()->setTitle(trans('plugins/financial-year::financial-year.view') . $name);

        return $formBuilder->create(FinancialYearForm::class, ['model' => $financialYear, 'isView' => true])->renderForm();
    }
    

    /**
     * @param int $id
     * @param FinancialYearRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, FinancialYearRequest $request, BaseHttpResponse $response )
    {
        
        $financialYear = $this->financialYearRepository->findOrFail($id);
        
        
        $financialYear->fill($request->input());

        $this->financialYearRepository->createOrUpdate($financialYear);

        event(new UpdatedContentEvent(FINANCIAL_YEAR_MODULE_SCREEN_NAME, $request, $financialYear));
        
        
        return $response
            ->setPreviousUrl(route('financial-year.index'))
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
            $financialYear = $this->financialYearRepository->findOrFail($id);

            $dataExist = CrudHelper::isDependentDataExist('financial-year', array($id), "Impiger\FinancialYear\Models\FinancialYear");

            if($dataExist) {
                return $response
                    ->setError()
                    ->setMessage($dataExist);
            }

            $this->financialYearRepository->delete($financialYear);
            
            event(new DeletedContentEvent(FINANCIAL_YEAR_MODULE_SCREEN_NAME, $request, $financialYear));

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

        $dataExist = CrudHelper::isDependentDataExist('financial-year', $ids, "Impiger\FinancialYear\Models\FinancialYear");

        if($dataExist) {
            return $response
                ->setError()
                ->setMessage($dataExist);
        }

        foreach ($ids as $id) {
            $financialYear = $this->financialYearRepository->findOrFail($id);
            $this->financialYearRepository->delete($financialYear);
            
            event(new DeletedContentEvent(FINANCIAL_YEAR_MODULE_SCREEN_NAME, $request, $financialYear));
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
            $bulkUpload = new BulkImport(new \Impiger\FinancialYear\Models\FinancialYear);
            $result = Excel::import($bulkUpload, $request->file('file'));
            $rowCount= $bulkUpload->getRowCount();
            if($rowCount==0){
                return $response
                        ->setError(true)
                        ->setMessage('There is no data to upload. Please upload file with valid data');
            }
            \Log::info(ucfirst(Str::plural(class_basename('\Impiger\FinancialYear\Models\FinancialYear')))." has been uploaded successfully");
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
            \Log::warning(count($errors)." ".ucfirst(Str::plural(class_basename('\Impiger\FinancialYear\Models\FinancialYear')))." has been inserted failed");
            $fileName = strtolower(class_basename('\Impiger\FinancialYear\Models\FinancialYear')) . "_" . \Carbon\Carbon::now()->format('Y-m-d_H-i-s') . ".csv";
            $filePath = "bulk_upload_errors" . "/" . $fileName;
            Excel::store(new BulkErrorExport($errors), $filePath);
            $template['filePath'] = $filePath;
            $template['fileName'] = $fileName;
            $request->session()->flash('modal',$template);
            return back();
        }
    }
}

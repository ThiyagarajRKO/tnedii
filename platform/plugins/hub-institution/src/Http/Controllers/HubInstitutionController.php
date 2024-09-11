<?php

namespace Impiger\HubInstitution\Http\Controllers;

use Impiger\Base\Events\BeforeEditContentEvent;
use Impiger\HubInstitution\Http\Requests\HubInstitutionRequest;
use Impiger\HubInstitution\Repositories\Interfaces\HubInstitutionInterface;
use Impiger\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Exception;
use Impiger\HubInstitution\Tables\HubInstitutionTable;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\DeletedContentEvent;
use Impiger\Base\Events\UpdatedContentEvent;
use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\HubInstitution\Forms\HubInstitutionForm;
use Impiger\Base\Forms\FormBuilder;
use Assets;
use App\Utils\CrudHelper;
use Impiger\Crud\Imports\BulkImport;
use Impiger\Crud\Imports\BulkErrorExport;
use Excel;
use Illuminate\Support\Str;

class HubInstitutionController extends BaseController
{
    /**
     * @var HubInstitutionInterface
     */
    protected $hubInstitutionRepository;

    /**
     * @param HubInstitutionInterface $hubInstitutionRepository
     */
    public function __construct(HubInstitutionInterface $hubInstitutionRepository)
    {
        $this->hubInstitutionRepository = $hubInstitutionRepository;

        Assets::addScriptsDirectly([
            'vendor/core/plugins/crud/js/custom_save_storage.js',
            'vendor/core/plugins/crud/js/crud_utils.js'
            
            
        ])
        ->addStylesDirectly([
            'vendor/core/plugins/crud/css/module_custom_styles.css'
            
        ]);
    }

    /**
     * @param HubInstitutionTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(HubInstitutionTable $table)
    {
        page_title()->setTitle(trans('plugins/hub-institution::hub-institution.name'));

        return $table->renderTable();
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/hub-institution::hub-institution.create'));

        return $formBuilder->create(HubInstitutionForm::class)->renderForm();
    }

    /**
     * @param HubInstitutionRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store(HubInstitutionRequest $request, BaseHttpResponse $response , \Impiger\ACL\Repositories\Interfaces\UserInterface $coreUserRepository, \Impiger\ACL\Services\ActivateUserService $activateUserService)
    {
        
       
        $hubInstitution = $this->hubInstitutionRepository->createOrUpdate($request->input());
        $hubCode = getAcronym($request->input('name'))."".getDistrictCode($request->input('district')).str_pad($hubInstitution->id, 4, "0", STR_PAD_LEFT);
        $request['hub_code'] = $hubCode;
        $hubInstitution->save();
        $coreuser = CrudHelper::createImpigerUser($request,$hubInstitution,$coreUserRepository,$activateUserService,HUB_ROLE_SLUG);
        event(new CreatedContentEvent(HUB_INSTITUTION_MODULE_SCREEN_NAME, $request, $hubInstitution));
        
        
        return $response
            ->setPreviousUrl(route('hub-institution.index'))
            ->setNextUrl(route('hub-institution.edit', $hubInstitution->id))
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
        
        $hubInstitution = $this->hubInstitutionRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $hubInstitution));

        $name = ($hubInstitution->name) ? ' "' . $hubInstitution->name . '"' : "";
        page_title()->setTitle(trans('plugins/hub-institution::hub-institution.edit') . $name);

        return $formBuilder->create(HubInstitutionForm::class, ['model' => $hubInstitution])->renderForm();
    }

    /**
     * @param int $id
     * @param Request $request
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function viewdetail($id, FormBuilder $formBuilder, Request $request)
    {
        $hubInstitution = $this->hubInstitutionRepository->findOrFail($id);
        $name = ($hubInstitution->name) ? ' "' . $hubInstitution->name . '"' : "";
        page_title()->setTitle(trans('plugins/hub-institution::hub-institution.view') . $name);

        return $formBuilder->create(HubInstitutionForm::class, ['model' => $hubInstitution, 'isView' => true])->renderForm();
    }
    

    /**
     * @param int $id
     * @param HubInstitutionRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, HubInstitutionRequest $request, BaseHttpResponse $response , \Impiger\ACL\Repositories\Interfaces\UserInterface $coreUserRepository, \Impiger\ACL\Services\ActivateUserService $activateUserService)
    {
        
        $hubInstitution = $this->hubInstitutionRepository->findOrFail($id);
        $hubCode = getAcronym($request->input('name'))."".getDistrictCode($request->input('district')).str_pad($id, 4, "0", STR_PAD_LEFT);
        $request['hub_code'] = $hubCode;
        $hubInstitution->fill($request->input());

        $this->hubInstitutionRepository->createOrUpdate($hubInstitution);
        $coreuser = CrudHelper::createImpigerUser($request,$hubInstitution,$coreUserRepository,$activateUserService,HUB_ROLE_SLUG);
        event(new UpdatedContentEvent(HUB_INSTITUTION_MODULE_SCREEN_NAME, $request, $hubInstitution));
        
        
        return $response
            ->setPreviousUrl(route('hub-institution.index'))
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
            $hubInstitution = $this->hubInstitutionRepository->findOrFail($id);

            $dataExist = CrudHelper::isDependentDataExist('hub-institution', array($id), "Impiger\HubInstitution\Models\HubInstitution");

            if($dataExist) {
                return $response
                    ->setError()
                    ->setMessage($dataExist);
            }

            $this->hubInstitutionRepository->delete($hubInstitution);
            
            event(new DeletedContentEvent(HUB_INSTITUTION_MODULE_SCREEN_NAME, $request, $hubInstitution));

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

        $dataExist = CrudHelper::isDependentDataExist('hub-institution', $ids, "Impiger\HubInstitution\Models\HubInstitution");

        if($dataExist) {
            return $response
                ->setError()
                ->setMessage($dataExist);
        }

        foreach ($ids as $id) {
            $hubInstitution = $this->hubInstitutionRepository->findOrFail($id);
            $this->hubInstitutionRepository->delete($hubInstitution);
            
            event(new DeletedContentEvent(HUB_INSTITUTION_MODULE_SCREEN_NAME, $request, $hubInstitution));
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
            $bulkUpload = new BulkImport(new \Impiger\HubInstitution\Models\HubInstitution);
            $result = Excel::import($bulkUpload, $request->file('file'));
            $rowCount= $bulkUpload->getRowCount();
            if($rowCount==0){
                return $response
                        ->setError(true)
                        ->setMessage('There is no data to upload. Please upload file with valid data');
            }
            \Log::info(ucfirst(Str::plural(class_basename('\Impiger\HubInstitution\Models\HubInstitution')))." has been uploaded successfully");
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
            \Log::warning(count($errors)." ".ucfirst(Str::plural(class_basename('\Impiger\HubInstitution\Models\HubInstitution')))." has been inserted failed");
            $fileName = strtolower(class_basename('\Impiger\HubInstitution\Models\HubInstitution')) . "_" . \Carbon\Carbon::now()->format('Y-m-d_H-i-s') . ".csv";
            $filePath = "bulk_upload_errors" . "/" . $fileName;
            Excel::store(new BulkErrorExport($errors), $filePath);
            $template['filePath'] = $filePath;
            $template['fileName'] = $fileName;
            $request->session()->flash('modal',$template);
            return back();
        }
    }
}

<?php

namespace Impiger\Entrepreneur\Http\Controllers;

use Impiger\Base\Events\BeforeEditContentEvent;
use Impiger\Entrepreneur\Http\Requests\EntrepreneurRequest;
use Impiger\Entrepreneur\Repositories\Interfaces\EntrepreneurInterface;
use Impiger\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Exception;
use Impiger\Entrepreneur\Tables\EntrepreneurTable;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\DeletedContentEvent;
use Impiger\Base\Events\UpdatedContentEvent;
use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\Entrepreneur\Forms\EntrepreneurForm;
use Impiger\Base\Forms\FormBuilder;
use Assets;
use App\Utils\CrudHelper;
use Impiger\Crud\Imports\BulkImport;
use Impiger\Crud\Imports\BulkErrorExport;
use Excel;
use Illuminate\Support\Str;

class EntrepreneurController extends BaseController
{
    /**
     * @var EntrepreneurInterface
     */
    protected $entrepreneurRepository;

    /**
     * @param EntrepreneurInterface $entrepreneurRepository
     */
    public function __construct(EntrepreneurInterface $entrepreneurRepository)
    {
        $this->entrepreneurRepository = $entrepreneurRepository;

        Assets::addScriptsDirectly([
            'vendor/core/plugins/crud/js/custom_save_storage.js',
            'vendor/core/plugins/crud/js/crud_utils.js'
            ,'vendor/core/plugins/entrepreneur/js/entrepreneur.js'
            
        ])
        ->addStylesDirectly([
            'vendor/core/plugins/crud/css/module_custom_styles.css'
            
        ]);
    }

    /**
     * @param EntrepreneurTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(EntrepreneurTable $table)
    {
        page_title()->setTitle(trans('plugins/entrepreneur::entrepreneur.name'));

        return $table->renderTable(['uploadRoute' => 'entrepreneur.import','template'=>'entrepreneur']);
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/entrepreneur::entrepreneur.create'));

        return $formBuilder->create(EntrepreneurForm::class)->renderForm();
    }

    /**
     * @param EntrepreneurRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store(EntrepreneurRequest $request, BaseHttpResponse $response, \Impiger\ACL\Repositories\Interfaces\UserInterface $coreUserRepository, \Impiger\ACL\Services\ActivateUserService $activateUserService)
    {
        
        $roleSlug = CANDIDATE_ROLE_SLUG;
        if($request->has('candidate_type_id') && $request->has('spoke_registration_id') && $request->has('hub_institution_id')) {
            $roleSlug = SPOKE_STUDENT_ROLE_SLUG;
        }
        $request['username'] = $request->input('email');
        // $request['password'] = CrudHelper::randomPassword();
        // $user = $service->execute($request);
        
        $user = CrudHelper::createCoreUserAndAssignRoleAndPermission($request, $coreUserRepository, $activateUserService,$roleSlug,true);
        $request['user_id'] = $user->id;
            
        $entrepreneur = $this->entrepreneurRepository->createOrUpdate($request->input());
        
        event(new CreatedContentEvent(ENTREPRENEUR_MODULE_SCREEN_NAME, $request, $entrepreneur));
        
        
        return $response
            ->setPreviousUrl(route('entrepreneur.index'))
            ->setNextUrl(route('entrepreneur.edit', $entrepreneur->id))
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
        
            if ($request->has('change_profile')) {
                if($request->user()->getKey() != $request->get('change_profile')) {
                    return response()->json(['error' => 'Unauthenticated.'], 401);
                }
            }
        $entrepreneur = $this->entrepreneurRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $entrepreneur));

        $name = ($entrepreneur->name) ? ' "' . $entrepreneur->name . '"' : "";
        page_title()->setTitle(trans('plugins/entrepreneur::entrepreneur.edit') . $name);

        return $formBuilder->create(EntrepreneurForm::class, ['model' => $entrepreneur])->renderForm();
    }

    /**
     * @param int $id
     * @param Request $request
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function viewdetail($id, FormBuilder $formBuilder, Request $request)
    {
        $entrepreneur = $this->entrepreneurRepository->findOrFail($id);
        $name = ($entrepreneur->name) ? ' "' . $entrepreneur->name . '"' : "";
        page_title()->setTitle(trans('plugins/entrepreneur::entrepreneur.view') . $name);

        return $formBuilder->create(EntrepreneurForm::class, ['model' => $entrepreneur, 'isView' => true])->renderForm();
    }
    

    /**
     * @param int $id
     * @param EntrepreneurRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, EntrepreneurRequest $request, BaseHttpResponse $response , \Impiger\ACL\Repositories\Interfaces\UserInterface $coreUserRepository, \Impiger\ACL\Services\MappingRoleService $service, \Impiger\ACL\Services\ActivateUserService $activateUserService)
    {
        
            if ($request->has('change_profile')) {
                if($request->user()->getKey() != $request->get('change_profile')) {
                    return response()->json(['error' => 'Unauthenticated.'], 401);
                }
            }
        $entrepreneur = $this->entrepreneurRepository->findOrFail($id);
        $password = $request->input('password');
        if($entrepreneur->password != $request->input('password')) {
            $request['password'] = \Hash::make($password);
        } else {
            unset($request['password']);
        }
        $coreuser = CrudHelper::updateCoreUser($entrepreneur->user_id, $request, $coreUserRepository, $service, $activateUserService);

            if(!$coreuser->success) {
                return $response
                    ->setError()
                    ->setMessage($coreuser->errorMsg)
                    ->withInput();
            }
        $request['password'] = $password;
        $entrepreneur->fill($request->input());

        $this->entrepreneurRepository->createOrUpdate($entrepreneur);

        event(new UpdatedContentEvent(ENTREPRENEUR_MODULE_SCREEN_NAME, $request, $entrepreneur));
        
        
        return $response
            ->setPreviousUrl(route('entrepreneur.index'))
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
            $entrepreneur = $this->entrepreneurRepository->findOrFail($id);

            $dataExist = CrudHelper::isDependentDataExist('entrepreneur', array($id), "Impiger\Entrepreneur\Models\Entrepreneur");

            if($dataExist) {
                return $response
                    ->setError()
                    ->setMessage($dataExist);
            }

            $this->entrepreneurRepository->delete($entrepreneur);
            $coreUser = app(\Impiger\ACL\Repositories\Interfaces\UserInterface::class)->findOrFail($entrepreneur->user_id);
                    if($coreUser){
                       app(\Impiger\ACL\Repositories\Interfaces\UserInterface::class)->delete($coreUser);
                       CrudHelper::destroyUserSession($coreUser);
                    }
            event(new DeletedContentEvent(ENTREPRENEUR_MODULE_SCREEN_NAME, $request, $entrepreneur));

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

        $dataExist = CrudHelper::isDependentDataExist('entrepreneur', $ids, "Impiger\Entrepreneur\Models\Entrepreneur");

        if($dataExist) {
            return $response
                ->setError()
                ->setMessage($dataExist);
        }

        foreach ($ids as $id) {
            $entrepreneur = $this->entrepreneurRepository->findOrFail($id);
            $this->entrepreneurRepository->delete($entrepreneur);
            $coreUser = app(\Impiger\ACL\Repositories\Interfaces\UserInterface::class)->findOrFail($entrepreneur->user_id);
                    if($coreUser){
                       app(\Impiger\ACL\Repositories\Interfaces\UserInterface::class)->delete($coreUser);
                       CrudHelper::destroyUserSession($coreUser);
                    }
            event(new DeletedContentEvent(ENTREPRENEUR_MODULE_SCREEN_NAME, $request, $entrepreneur));
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
            $bulkUpload = new BulkImport(new \Impiger\Entrepreneur\Models\Entrepreneur);
            $result = Excel::import($bulkUpload, $request->file('file'));
            $rowCount= $bulkUpload->getRowCount();
            if($rowCount==0){
                return $response
                        ->setError(true)
                        ->setMessage('There is no data to upload. Please upload file with valid data');
            }
            \Log::info(ucfirst(Str::plural(class_basename('\Impiger\Entrepreneur\Models\Entrepreneur')))." has been uploaded successfully");
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
            \Log::warning(count($errors)." ".ucfirst(Str::plural(class_basename('\Impiger\Entrepreneur\Models\Entrepreneur')))." has been inserted failed");
            $fileName = strtolower(class_basename('\Impiger\Entrepreneur\Models\Entrepreneur')) . "_" . \Carbon\Carbon::now()->format('Y-m-d_H-i-s') . ".csv";
            $filePath = "bulk_upload_errors" . "/" . $fileName;
            Excel::store(new BulkErrorExport($errors), $filePath);
            $template['filePath'] = $filePath;
            $template['fileName'] = $fileName;
            $request->session()->flash('modal',$template);
            return back();
        }
    }
}

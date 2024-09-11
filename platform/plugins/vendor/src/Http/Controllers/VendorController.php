<?php

namespace Impiger\Vendor\Http\Controllers;

use Impiger\Base\Events\BeforeEditContentEvent;
use Impiger\Vendor\Http\Requests\VendorRequest;
use Impiger\Vendor\Repositories\Interfaces\VendorInterface;
use Impiger\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Exception;
use Impiger\Vendor\Tables\VendorTable;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\DeletedContentEvent;
use Impiger\Base\Events\UpdatedContentEvent;
use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\Vendor\Forms\VendorForm;
use Impiger\Base\Forms\FormBuilder;
use Assets;
use App\Utils\CrudHelper;
use Impiger\Crud\Imports\BulkImport;
use Impiger\Crud\Imports\BulkErrorExport;
use Excel;
use Illuminate\Support\Str;

class VendorController extends BaseController
{
    /**
     * @var VendorInterface
     */
    protected $vendorRepository;

    /**
     * @param VendorInterface $vendorRepository
     */
    public function __construct(VendorInterface $vendorRepository)
    {
        $this->vendorRepository = $vendorRepository;

        Assets::addScriptsDirectly([
            'vendor/core/plugins/crud/js/custom_save_storage.js',
            'vendor/core/plugins/crud/js/crud_utils.js'
            
            
        ])
        ->addStylesDirectly([
            'vendor/core/plugins/crud/css/module_custom_styles.css'
            
        ]);
    }

    /**
     * @param VendorTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(VendorTable $table)
    {
        page_title()->setTitle(trans('plugins/vendor::vendor.name'));

        return $table->renderTable();
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/vendor::vendor.create'));

        return $formBuilder->create(VendorForm::class)->renderForm();
    }

    /**
     * @param VendorRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store(VendorRequest $request, BaseHttpResponse $response , \Impiger\ACL\Repositories\Interfaces\UserInterface $coreUserRepository, \Impiger\ACL\Services\ActivateUserService $activateUserService)
    {

        // \Impiger\ACL\Repositories\Interfaces\UserInterface $coreUserRepository, 
        // \Impiger\Entrepreneur\Repositories\Interfaces\EntrepreneurInterface $entrepreneurRepository, 
        // \Impiger\ACL\Services\ActivateUserService $activateUserService
        $request->merge(['created_by' => \Auth::id()]);
        
            $request['username'] = $request->input('email');
            // $request['password'] = CrudHelper::randomPassword();
            // $user = $service->execute($request);
            $user = CrudHelper::createEntrepreneurUser($request, $coreUserRepository, $activateUserService,VENDOR_ROLE_SLUG,true);
            $request['user_id'] = $user->id;
            
            $vendor = $this->vendorRepository->createOrUpdate($request->input());

            event(new CreatedContentEvent(VENDOR_MODULE_SCREEN_NAME, $request, $vendor));        
        
            return $response
                ->setPreviousUrl(route('vendor.index'))
                ->setNextUrl(route('vendor.edit', $vendor->id))
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
        $vendor = $this->vendorRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $vendor));

        $name = ($vendor->name) ? ' "' . $vendor->name . '"' : "";
        page_title()->setTitle(trans('plugins/vendor::vendor.edit') . $name);

        return $formBuilder->create(VendorForm::class, ['model' => $vendor])->renderForm();
    }

    /**
     * @param int $id
     * @param Request $request
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function viewdetail($id, FormBuilder $formBuilder, Request $request)
    {
        $vendor = $this->vendorRepository->findOrFail($id);
        $name = ($vendor->name) ? ' "' . $vendor->name . '"' : "";
        page_title()->setTitle(trans('plugins/vendor::vendor.view') . $name);

        return $formBuilder->create(VendorForm::class, ['model' => $vendor, 'isView' => true])->renderForm();
    }
    

    /**
     * @param int $id
     * @param VendorRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, VendorRequest $request, BaseHttpResponse $response , \Impiger\ACL\Repositories\Interfaces\UserInterface $coreUserRepository, \Impiger\ACL\Services\MappingRoleService $service, \Impiger\ACL\Services\ActivateUserService $activateUserService)
    {
        
            if ($request->has('change_profile')) {
                if($request->user()->getKey() != $request->get('change_profile')) {
                    return response()->json(['error' => 'Unauthenticated.'], 401);
                }
            }
        $vendor = $this->vendorRepository->findOrFail($id);
        $coreuser = CrudHelper::updateCoreUser($vendor->user_id, $request, $coreUserRepository, $service, $activateUserService);
            if(!$coreuser->success) {
                return $response
                    ->setError()
                    ->setMessage($coreuser->errorMsg)
                    ->withInput();
            }
        
        $vendor->fill($request->input());

        $this->vendorRepository->createOrUpdate($vendor);

        event(new UpdatedContentEvent(VENDOR_MODULE_SCREEN_NAME, $request, $vendor));
        
        
        return $response
            ->setPreviousUrl(route('vendor.index'))
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
            $vendor = $this->vendorRepository->findOrFail($id);

            $dataExist = CrudHelper::isDependentDataExist('vendor', array($id), "Impiger\Vendor\Models\Vendor");

            if($dataExist) {
                return $response
                    ->setError()
                    ->setMessage($dataExist);
            }

            $this->vendorRepository->delete($vendor);
            $coreUser = app(\Impiger\ACL\Repositories\Interfaces\UserInterface::class)->findOrFail($vendor->user_id);
                    if($coreUser){
                       app(\Impiger\ACL\Repositories\Interfaces\UserInterface::class)->delete($coreUser);
                       CrudHelper::destroyUserSession($coreUser);
                    }
            event(new DeletedContentEvent(VENDOR_MODULE_SCREEN_NAME, $request, $vendor));

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

        $dataExist = CrudHelper::isDependentDataExist('vendor', $ids, "Impiger\Vendor\Models\Vendor");

        if($dataExist) {
            return $response
                ->setError()
                ->setMessage($dataExist);
        }

        foreach ($ids as $id) {
            $vendor = $this->vendorRepository->findOrFail($id);
            $this->vendorRepository->delete($vendor);
            $coreUser = app(\Impiger\ACL\Repositories\Interfaces\UserInterface::class)->findOrFail($vendor->user_id);
                    if($coreUser){
                       app(\Impiger\ACL\Repositories\Interfaces\UserInterface::class)->delete($coreUser);
                       CrudHelper::destroyUserSession($coreUser);
                    }
            event(new DeletedContentEvent(VENDOR_MODULE_SCREEN_NAME, $request, $vendor));
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
            $bulkUpload = new BulkImport(new \Impiger\Vendor\Models\Vendor);
            $result = Excel::import($bulkUpload, $request->file('file'));
            $rowCount= $bulkUpload->getRowCount();
            if($rowCount==0){
                return $response
                        ->setError(true)
                        ->setMessage('There is no data to upload. Please upload file with valid data');
            }
            \Log::info(ucfirst(Str::plural(class_basename('\Impiger\Vendor\Models\Vendor')))." has been uploaded successfully");
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
            \Log::warning(count($errors)." ".ucfirst(Str::plural(class_basename('\Impiger\Vendor\Models\Vendor')))." has been inserted failed");
            $fileName = strtolower(class_basename('\Impiger\Vendor\Models\Vendor')) . "_" . \Carbon\Carbon::now()->format('Y-m-d_H-i-s') . ".csv";
            $filePath = "bulk_upload_errors" . "/" . $fileName;
            Excel::store(new BulkErrorExport($errors), $filePath);
            $template['filePath'] = $filePath;
            $template['fileName'] = $fileName;
            $request->session()->flash('modal',$template);
            return back();
        }
    }
}

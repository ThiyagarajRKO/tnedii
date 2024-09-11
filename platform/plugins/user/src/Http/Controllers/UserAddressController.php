<?php

namespace Impiger\User\Http\Controllers;

use Impiger\Base\Events\BeforeEditContentEvent;
use Impiger\User\Http\Requests\UserAddressRequest;
use Impiger\User\Repositories\Interfaces\UserAddressInterface;
use Impiger\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Exception;
use Impiger\User\Tables\UserAddressTable;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\DeletedContentEvent;
use Impiger\Base\Events\UpdatedContentEvent;
use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\User\Forms\UserAddressForm;
use Impiger\Base\Forms\FormBuilder;
use Assets;
use App\Utils\CrudHelper;
use Impiger\Crud\Imports\BulkImport;
use Impiger\Crud\Imports\BulkErrorExport;
use Excel;
use Illuminate\Support\Str;

class UserAddressController extends BaseController
{
    /**
     * @var UserAddressInterface
     */
    protected $userAddressRepository;

    /**
     * @param UserAddressInterface $userAddressRepository
     */
    public function __construct(UserAddressInterface $userAddressRepository)
    {
        $this->userAddressRepository = $userAddressRepository;

        Assets::addScriptsDirectly([
            'vendor/core/plugins/crud/js/custom_save_storage.js',
            'vendor/core/plugins/crud/js/crud_utils.js'
            
            
        ])
        ->addStylesDirectly([
            'vendor/core/plugins/crud/css/module_custom_styles.css'
            
        ]);
    }

    /**
     * @param UserAddressTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(UserAddressTable $table)
    {
        page_title()->setTitle(trans('plugins/user::user-address.name'));

        return $table->renderTable();
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/user::user-address.create'));

        return $formBuilder->create(UserAddressForm::class)->renderForm();
    }

    /**
     * @param UserAddressRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store(UserAddressRequest $request, BaseHttpResponse $response )
    {
        
        
        $userAddress = $this->userAddressRepository->createOrUpdate($request->input());
        
        event(new CreatedContentEvent(USER_ADDRESS_MODULE_SCREEN_NAME, $request, $userAddress));
        
        
        return $response
            ->setPreviousUrl(route('user-address.index'))
            ->setNextUrl(route('user-address.edit', $userAddress->id))
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
        
        $userAddress = $this->userAddressRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $userAddress));

        $name = ($userAddress->name) ? ' "' . $userAddress->name . '"' : "";
        page_title()->setTitle(trans('plugins/user::user-address.edit') . $name);

        return $formBuilder->create(UserAddressForm::class, ['model' => $userAddress])->renderForm();
    }

    /**
     * @param int $id
     * @param Request $request
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function viewdetail($id, FormBuilder $formBuilder, Request $request)
    {
        $userAddress = $this->userAddressRepository->findOrFail($id);
        $name = ($userAddress->name) ? ' "' . $userAddress->name . '"' : "";
        page_title()->setTitle(trans('plugins/user::user-address.view') . $name);

        return $formBuilder->create(UserAddressForm::class, ['model' => $userAddress, 'isView' => true])->renderForm();
    }
    

    /**
     * @param int $id
     * @param UserAddressRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, UserAddressRequest $request, BaseHttpResponse $response )
    {
        
        $userAddress = $this->userAddressRepository->findOrFail($id);
        
        
        $userAddress->fill($request->input());

        $this->userAddressRepository->createOrUpdate($userAddress);

        event(new UpdatedContentEvent(USER_ADDRESS_MODULE_SCREEN_NAME, $request, $userAddress));
        
        
        return $response
            ->setPreviousUrl(route('user-address.index'))
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
            $userAddress = $this->userAddressRepository->findOrFail($id);
        
            $dataExist = CrudHelper::isDependentDataExist('user-address', array($id), "Impiger\User\Models\UserAddress");
                
            if($dataExist) {
                return $response
                    ->setError()
                    ->setMessage($dataExist);
            }

            $this->userAddressRepository->delete($userAddress);
            
            event(new DeletedContentEvent(USER_ADDRESS_MODULE_SCREEN_NAME, $request, $userAddress));

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
        
        $dataExist = CrudHelper::isDependentDataExist('user-address', $ids, "Impiger\User\Models\UserAddress");
            
        if($dataExist) {
            return $response
                ->setError()
                ->setMessage($dataExist);
        }

        foreach ($ids as $id) {
            $userAddress = $this->userAddressRepository->findOrFail($id);
            $this->userAddressRepository->delete($userAddress);
            
            event(new DeletedContentEvent(USER_ADDRESS_MODULE_SCREEN_NAME, $request, $userAddress));
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
            $bulkUpload = new BulkImport(new \Impiger\User\Models\UserAddress);
            $result = Excel::import($bulkUpload, $request->file('file'));
            $rowCount= $bulkUpload->getRowCount();
            if($rowCount==0){
                return $response
                        ->setError(true)
                        ->setMessage('There is no data to upload. Please upload file with valid data');
            }
            \Log::info(ucfirst(Str::plural(class_basename('\Impiger\User\Models\UserAddress')))." has been uploaded successfully");
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
            \Log::warning(count($errors)." ".ucfirst(Str::plural(class_basename('\Impiger\User\Models\UserAddress')))." has been inserted failed");
            $fileName = strtolower(class_basename('\Impiger\User\Models\UserAddress')) . "_" . \Carbon\Carbon::now()->format('Y-m-d_H-i-s') . ".csv";
            $filePath = "bulk_upload_errors" . "/" . $fileName;
            Excel::store(new BulkErrorExport($errors), $filePath);
            $template['filePath'] = $filePath;
            $template['fileName'] = $fileName;
            $request->session()->flash('modal',$template);
            return back();
        }
    }
}

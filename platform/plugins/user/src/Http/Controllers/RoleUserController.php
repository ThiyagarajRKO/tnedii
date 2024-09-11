<?php

namespace Impiger\User\Http\Controllers;

use Impiger\Base\Events\BeforeEditContentEvent;
use Impiger\User\Http\Requests\RoleUserRequest;
use Impiger\User\Repositories\Interfaces\RoleUserInterface;
use Impiger\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Exception;
use Impiger\User\Tables\RoleUserTable;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\DeletedContentEvent;
use Impiger\Base\Events\UpdatedContentEvent;
use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\User\Forms\RoleUserForm;
use Impiger\Base\Forms\FormBuilder;
use Assets;
use App\Utils\CrudHelper;
use Impiger\Crud\Imports\BulkImport;
use Impiger\Crud\Imports\BulkErrorExport;
use Excel;
use Illuminate\Support\Str;

class RoleUserController extends BaseController
{
    /**
     * @var RoleUserInterface
     */
    protected $roleUserRepository;

    /**
     * @param RoleUserInterface $roleUserRepository
     */
    public function __construct(RoleUserInterface $roleUserRepository)
    {
        $this->roleUserRepository = $roleUserRepository;

        Assets::addScriptsDirectly([
            'vendor/core/plugins/crud/js/custom_save_storage.js',
            'vendor/core/plugins/crud/js/crud_utils.js',
            
        ])
        ->addStylesDirectly([
            'vendor/core/plugins/crud/css/module_custom_styles.css'
        ]);
    }

    /**
     * @param RoleUserTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(RoleUserTable $table)
    {
        page_title()->setTitle(trans('plugins/user::role-user.name'));

        return $table->renderTable();
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/user::role-user.create'));

        return $formBuilder->create(RoleUserForm::class)->renderForm();
    }

    /**
     * @param RoleUserRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store(RoleUserRequest $request, BaseHttpResponse $response )
    {
        
        $roleUser = $this->roleUserRepository->createOrUpdate($request->input());
        
        event(new CreatedContentEvent(ROLE_USER_MODULE_SCREEN_NAME, $request, $roleUser));

        return $response
            ->setPreviousUrl(route('role-user.index'))
            ->setNextUrl(route('role-user.edit', $roleUser->id))
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
        $roleUser = $this->roleUserRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $roleUser));

        page_title()->setTitle(trans('plugins/user::role-user.edit') . ' "' . $roleUser->name . '"');

        return $formBuilder->create(RoleUserForm::class, ['model' => $roleUser])->renderForm();
    }

    /**
     * @param int $id
     * @param Request $request
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function viewdetail($id, FormBuilder $formBuilder, Request $request)
    {
        $roleUser = $this->roleUserRepository->findOrFail($id);

        page_title()->setTitle(trans('plugins/user::role-user.view') . ' "' . $roleUser->name . '"');

        return $formBuilder->create(RoleUserForm::class, ['model' => $roleUser, 'isView' => true])->renderForm();
    }

    /**
     * @param int $id
     * @param RoleUserRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, RoleUserRequest $request, BaseHttpResponse $response )
    {
        $roleUser = $this->roleUserRepository->findOrFail($id);
        
        
        $roleUser->fill($request->input());

        $this->roleUserRepository->createOrUpdate($roleUser);

        event(new UpdatedContentEvent(ROLE_USER_MODULE_SCREEN_NAME, $request, $roleUser));

        return $response
            ->setPreviousUrl(route('role-user.index'))
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
            $roleUser = $this->roleUserRepository->findOrFail($id);

            $this->roleUserRepository->delete($roleUser);

            event(new DeletedContentEvent(ROLE_USER_MODULE_SCREEN_NAME, $request, $roleUser));

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

        foreach ($ids as $id) {
            $roleUser = $this->roleUserRepository->findOrFail($id);
            $this->roleUserRepository->delete($roleUser);
            event(new DeletedContentEvent(ROLE_USER_MODULE_SCREEN_NAME, $request, $roleUser));
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
            $bulkUpload = new BulkImport(new \Impiger\User\Models\RoleUser);
            $result = Excel::import($bulkUpload, $request->file('file'));
            \Log::info(ucfirst(Str::plural(class_basename('\Impiger\User\Models\RoleUser')))." has been uploaded successfully");
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
            \Log::warning(count($errors)." ".ucfirst(Str::plural(class_basename('\Impiger\User\Models\RoleUser')))." has been inserted failed");
            $fileName = strtolower(class_basename('\Impiger\User\Models\RoleUser')) . "_" . \Carbon\Carbon::now()->format('Y-m-d_H-i-s') . ".csv";
            $filePath = "bulk_upload_errors" . "/" . $fileName;
            Excel::store(new BulkErrorExport($errors), $filePath);
            $template['filePath'] = $filePath;
            $template['fileName'] = $fileName;
            $request->session()->flash('modal',$template);
            return back();
        }
    }
}

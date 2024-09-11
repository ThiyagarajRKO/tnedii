<?php

namespace Impiger\BackendMenu\Http\Controllers;

use Impiger\Base\Events\BeforeEditContentEvent;
use Impiger\BackendMenu\Http\Requests\BackendMenuRequest;
use Impiger\BackendMenu\Repositories\Interfaces\BackendMenuInterface;
use Impiger\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Exception;
use Impiger\BackendMenu\Tables\BackendMenuTable;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\DeletedContentEvent;
use Impiger\Base\Events\UpdatedContentEvent;
use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\BackendMenu\Forms\BackendMenuForm;
use Impiger\Base\Forms\FormBuilder;
use Assets;
use App\Utils\CrudHelper;
use Impiger\Crud\Imports\BulkImport;
use Impiger\Crud\Imports\BulkErrorExport;
use Excel;
use Illuminate\Support\Str;
use Arr;
use Impiger\BackendMenu\Models\BackendMenu;

class BackendMenuController extends BaseController
{
    /**
     * @var BackendMenuInterface
     */
    protected $backendMenuRepository;

    /**
     * @param BackendMenuInterface $backendMenuRepository
     */
    public function __construct(BackendMenuInterface $backendMenuRepository)
    {
        $this->backendMenuRepository = $backendMenuRepository;

        Assets::addScriptsDirectly([
            'vendor/core/plugins/crud/js/custom_save_storage.js',
            'vendor/core/plugins/crud/js/crud_utils.js'
            
            
        ])
        ->addStylesDirectly([
            'vendor/core/plugins/crud/css/module_custom_styles.css'
            
        ]);
    }

    /**
     * @param BackendMenuTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(BackendMenuTable $table)
    {
        page_title()->setTitle(trans('plugins/backend-menu::backend-menu.name'));

        return $table->renderTable();
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/backend-menu::backend-menu.create'));

        return $formBuilder->create(BackendMenuForm::class)->renderForm();
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function getMenu(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/backend-menu::backend-menu.create'));

        return $formBuilder->create(BackendMenuForm::class)->renderForm();
    }

    /**
     * @param BackendMenuRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function saveMenu(BackendMenuRequest $request, BaseHttpResponse $response )
    {
        $menuNodes = json_decode($request->input('menu_nodes'), true);
        // dd($menuNodes);
        BackendMenu::query()->truncate();
        $this->saveBackendMenus($menuNodes, NULL);
        
        return $response
            ->setPreviousUrl(route('backend-menu.index'))
            ->setNextUrl(route('backend-menu.getmenu'))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function saveBackendMenus($menuNodes, $parentId = NULL) {
        if(Arr::has($menuNodes, 0)) {
            foreach ($menuNodes as $menu) {
                $row = [];
                $row['menu_id'] = isset($menu['referenceId']) ? $menu['referenceId']: $menu['referenceid'];
                $row['menu_id'] = ($row['menu_id']) ? $row['menu_id'] : Str::snake(str_replace('-', '_', $menu['title']));
                $row['name'] = html_entity_decode($menu['title']);
                $row['url'] = isset($menu['referenceType']) ? $menu['referenceType']: $menu['referencetype'];;
                $row['url'] = ($row['url'] == 'custom-link') ? NULL : $row['url'];
                $row['target'] = $menu['target'];
                $row['icon'] = Arr::has($menu, 'iconFont') ? Arr::get($menu, 'iconFont') :Arr::get($menu, 'icon') ;
                $row['priority'] = Arr::get($menu, 'position');
                $row['parent_id'] = $parentId;
                BackendMenu::insert($row);

                if (Arr::has($menu, 'children.0')) {
                    $this->saveBackendMenus($menu['children'], $row['menu_id']);
                }
            }
        }
    }

    /**
     * @param BackendMenuRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store(BackendMenuRequest $request, BaseHttpResponse $response )
    {
        
        $backendMenu = $this->backendMenuRepository->createOrUpdate($request->input());
        
        event(new CreatedContentEvent(BACKEND_MENU_MODULE_SCREEN_NAME, $request, $backendMenu));
        
        return $response
            ->setPreviousUrl(route('backend-menu.index'))
            ->setNextUrl(route('backend-menu.edit', $backendMenu->id))
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
        $backendMenu = $this->backendMenuRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $backendMenu));

        $name = ($backendMenu->name) ? ' "' . $backendMenu->name . '"' : "";
        page_title()->setTitle(trans('plugins/backend-menu::backend-menu.edit') . $name);

        return $formBuilder->create(BackendMenuForm::class, ['model' => $backendMenu])->renderForm();
    }
    

    /**
     * @param int $id
     * @param BackendMenuRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, BackendMenuRequest $request, BaseHttpResponse $response )
    {
        $backendMenu = $this->backendMenuRepository->findOrFail($id);
        
        
        $backendMenu->fill($request->input());

        $this->backendMenuRepository->createOrUpdate($backendMenu);

        event(new UpdatedContentEvent(BACKEND_MENU_MODULE_SCREEN_NAME, $request, $backendMenu));
        
        return $response
            ->setPreviousUrl(route('backend-menu.index'))
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
            $backendMenu = $this->backendMenuRepository->findOrFail($id);

            $this->backendMenuRepository->delete($backendMenu);

            event(new DeletedContentEvent(BACKEND_MENU_MODULE_SCREEN_NAME, $request, $backendMenu));

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
            $backendMenu = $this->backendMenuRepository->findOrFail($id);
            $this->backendMenuRepository->delete($backendMenu);
            event(new DeletedContentEvent(BACKEND_MENU_MODULE_SCREEN_NAME, $request, $backendMenu));
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
            $bulkUpload = new BulkImport(new \Impiger\BackendMenu\Models\BackendMenu);
            $result = Excel::import($bulkUpload, $request->file('file'));
            $rowCount= $bulkUpload->getRowCount();
            if($rowCount==0){
                return $response
                        ->setError(true)
                        ->setMessage('There is no data to upload. Please upload file with valid data');
            }
            \Log::info(ucfirst(Str::plural(class_basename('\Impiger\BackendMenu\Models\BackendMenu')))." has been uploaded successfully");
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
            \Log::warning(count($errors)." ".ucfirst(Str::plural(class_basename('\Impiger\BackendMenu\Models\BackendMenu')))." has been inserted failed");
            $fileName = strtolower(class_basename('\Impiger\BackendMenu\Models\BackendMenu')) . "_" . \Carbon\Carbon::now()->format('Y-m-d_H-i-s') . ".csv";
            $filePath = "bulk_upload_errors" . "/" . $fileName;
            Excel::store(new BulkErrorExport($errors), $filePath);
            $template['filePath'] = $filePath;
            $template['fileName'] = $fileName;
            $request->session()->flash('modal',$template);
            return back();
        }
    }
}

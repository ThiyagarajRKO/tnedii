<?php

namespace Impiger\BackendMenu\Http\Controllers;

use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\UpdatedContentEvent;
use Impiger\BackendMenu\Http\Requests\BackendMenuRequest;
use Impiger\BackendMenu\Repositories\Interfaces\BackendMenuInterface;
use Impiger\BackendMenu\Tables\BackendMenuTable;
use Exception;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Schema;
use App\Utils\CrudHelper;

class BackendMenuPublicController extends Controller
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
    }

    /**
     * @param BackendMenuTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(BackendMenuTable $table)
    {
        return $table->setOptions(['shortcode' => true])->renderTable();
    }

    /**
     * @param BackendMenuRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws \Throwable
     */
    public function postData(BackendMenuRequest $request, BaseHttpResponse $response)
    {
        try {
            $backendMenu = $this->backendMenuRepository->getModel();
            $table = $backendMenu->getTable();
            if(Schema::hasColumn($table,'is_enabled')){
                $backendMenu->fillable(array_merge($backendMenu->getFillable(),["is_enabled"]));
                $backendMenu->is_enabled = 0;
            }
            $backendMenu->fill($request->input());
            $this->backendMenuRepository->createOrUpdate($backendMenu);
            
            event(new CreatedContentEvent(BACKEND_MENU_MODULE_SCREEN_NAME, $request, $backendMenu));

            return $response
                    ->setPreviousUrl(url('/form-response?form=backendMenu'))
                    ->setMessage(trans('core/base::notices.create_success_message'));
        } catch (Exception $exception) {
            info($exception->getMessage());
            return $response
                ->setError()
                ->setMessage(trans('plugins/backendMenu::failed_msg'));
        }
    }

    /**
     * @param BackendMenuRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws \Throwable
     */
    public function updateData($id, BackendMenuRequest $request, BaseHttpResponse $response)
    {
        try {
            $backendMenu = $this->backendMenuRepository->findOrFail($id);
            
            $backendMenu->fill($request->input());
            $this->backendMenuRepository->createOrUpdate($backendMenu);
            event(new UpdatedContentEvent(BACKEND_MENU_MODULE_SCREEN_NAME, $request, $backendMenu));

            return $response->setMessage(trans('core/base::notices.update_success_message'));
        } catch (Exception $exception) {
            info($exception->getMessage());
            return $response
                ->setError()
                ->setMessage(trans('plugins/backendMenu::failed_msg'));
        }
    }
}

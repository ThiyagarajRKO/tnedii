<?php

namespace Impiger\AuditLog\Http\Controllers;

use Impiger\AuditLog\Repositories\Interfaces\AuditLogInterface;
use Impiger\AuditLog\Tables\AuditLogTable;
use Impiger\Base\Events\DeletedContentEvent;
use Impiger\Base\Http\Controllers\BaseController;
use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\Base\Traits\HasDeleteManyItemsTrait;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;
/* @Customized By Ramesh Esakki  - Start -*/
use Impiger\AuditLog\Tables\AuditLogStatsTable;
use Assets;
use Impiger\AuditLog\Models\AuditHistory;
use Impiger\AuditLog\Tables\AuditLogNotificationTable;
/* @Customized By Ramesh Esakki  - End -*/

class AuditLogController extends BaseController
{

    use HasDeleteManyItemsTrait;

    /**
     * @var AuditLogInterface
     */
    protected $auditLogRepository;

    /**
     * AuditLogController constructor.
     * @param AuditLogInterface $auditLogRepository
     */
    public function __construct(AuditLogInterface $auditLogRepository)
    {
        $this->auditLogRepository = $auditLogRepository;
    }

    /**
     * @param BaseHttpResponse $response
     * @param Request $request
     * @return BaseHttpResponse
     */
    public function getWidgetActivities(BaseHttpResponse $response, Request $request)
    {
        $limit = (int)$request->input('paginate', 10);

        $histories = $this->auditLogRepository
            ->advancedGet([
                'with'     => ['user'],
                'order_by' => ['created_at' => 'DESC'],
                'paginate' => [
                    'per_page'      => $limit,
                    'current_paged' => 1,
                ],
            ]);
        $model = $this->auditLogRepository->getModel();
        $query = $model->orderBy('created_at' , 'DESC');
        $query = apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, [], false);
        $histories = $query->paginate(); 
        return $response
            ->setData(view('plugins/audit-log::widgets.activities', compact('histories', 'limit'))->render());
    }

    /**
     * @Customized By Ramesh Esakki
     * @param AuditLogTable $dataTable
     * @return Factory|View
     * @throws Throwable
     */
    public function detail(AuditLogTable $dataTable)
    {
        page_title()->setTitle(trans('plugins/audit-log::history.name'));
        return $dataTable->renderTable();
    }

    /**
     * @Customized By Ramesh Esakki
     * @param AuditLogStatsTable $dataTable
     * @return Factory|View
     * @customized Ramesh.Esakki
     * @throws Throwable
     */
    public function index(AuditLogStatsTable $dataTable)
    {
        page_title()->setTitle(trans('plugins/audit-log::history.name'));
        return $dataTable->renderTable();
    }

    /**
     * @param Request $request
     * @param int $id
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function destroy(Request $request, $id, BaseHttpResponse $response)
    {
        try {
            $log = $this->auditLogRepository->findOrFail($id);
            $this->auditLogRepository->delete($log);

            event(new DeletedContentEvent(AUDIT_LOG_MODULE_SCREEN_NAME, $request, $log));

            return $response->setMessage(trans('core/base::notices.delete_success_message'));
        } catch (Exception $ex) {
            return $response
                ->setError()
                ->setMessage($ex->getMessage());
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
        return $this->executeDeleteItems($request, $response, $this->auditLogRepository, AUDIT_LOG_MODULE_SCREEN_NAME);
    }

    /**
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function deleteAll(BaseHttpResponse $response)
    {
        $this->auditLogRepository->getModel()->truncate();

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }
    
    /**
     * @Customized By Sabari Shankar Parthiban
     * @param AuditLogNotificationTable $dataTable
     * @return Factory|View
     * @throws Throwable
     */
    public function notification(AuditLogNotificationTable $dataTable)
    {
        page_title()->setTitle(trans('plugins/audit-log::history.name'));
        return $dataTable->renderTable();
    }
}

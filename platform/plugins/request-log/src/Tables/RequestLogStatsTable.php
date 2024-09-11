<?php

namespace Impiger\RequestLog\Tables;

use Impiger\RequestLog\Models\RequestLog;
use Illuminate\Support\Facades\Auth;
use Impiger\RequestLog\Repositories\Interfaces\RequestLogInterface;
use Impiger\Table\Abstracts\TableAbstract;
use Html;
use Illuminate\Contracts\Routing\UrlGenerator;
use Yajra\DataTables\DataTables;
use DB;

class RequestLogStatsTable extends TableAbstract
{

    /**
     * @var bool
     */
    protected $hasActions = false;

    /**
     * @var bool
     */
    protected $hasFilter = false;

    /**
     * @var array
     */
    protected $customColumns = array();

    /**
     * RequestLogStatsTable constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlGenerator
     * @param RequestLogInterface $requestLogRepository
     */
    public function __construct(
        DataTables $table,
        UrlGenerator $urlGenerator,
        RequestLogInterface $requestLogRepository
    ) {
        $this->repository = $requestLogRepository;
        $this->setOption('id', 'table-request-histories');
        parent::__construct($table, $urlGenerator);

        if (!Auth::user()->hasPermission('request-log.destroy')) {
            $this->hasOperations = false;
            $this->hasActions = false;
        }
        if (Auth::user()->hasPermission('request-log.empty')) {
            $this->hasOperations = true;
            $this->hasActions = true;
        }
        $this->customColumns = RequestLog::getHTTPStatusCode();
    }

    /**
     * {@inheritDoc}
     */
    public function ajax()
    {
        $data = $this->table
            ->eloquent($this->query())
            ->editColumn('checkbox', function ($item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('created_date', function ($item) {
                return Html::link(route('request-log.detail', 'arg1=' . $item->created_date . '&arg2=' . $item->status_code), $item->created_date);
            })
            ->editColumn('status_code_count', function ($item) {
                return Html::link(route('request-log.detail', 'arg1=' . $item->created_date . '&arg2=' . $item->status_code), $item->status_code_count);
            });

        return apply_filters(BASE_FILTER_GET_LIST_DATA, $data, $this->repository->getModel())
            ->addColumn('operations', function ($item) {
                return $this->getOperations(null, 'request-log.destroy', $item);
            })
            ->escapeColumns([])
            ->make(true);
    }

    /**
     * {@inheritDoc}
     */
    public function query()
    {
        $model = $this->repository->getModel();
        $select = ['request_logs.id', 'request_logs.status_code', 'request_logs.updated_at', DB::raw('DATE_FORMAT(updated_at, "%Y-%m-%d") as created_date'),DB::raw('COUNT(id) AS status_code_count')];
        $query = $model->groupBy(DB::raw('Date(updated_at), status_code'))->select($select);
        return $this->applyScopes(apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, $select));
    }

    /**
     * {@inheritDoc}
     */
    public function columns()
    {
        return [
            'created_date'     => [
                'name'  => 'request_logs.updated_at',
                'date' => 'created_date',
                'title' => 'Date',
                'class' => 'text-left'
            ],
            'status_code' => [
                'name'  => 'request_logs.status_code',
                'title' => trans('plugins/request-log::request-log.status_code'),
                'class' => 'text-left'
            ],
            'status_code_count' => [
                'name' => 'request_logs.status_code_count',
                'title' => trans('plugins/request-log::request-log.count'),
                'class' => 'text-left'
            ]
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function buttons()
    { /*  @customized Haritha Murugavel start */
        if (!$this->hasActions) {
            return [];
        }
      else  if (Auth::user()->hasPermission('request-log.empty')) {
            $this->hasOperations = true;
            $this->hasActions = true;
        
        return [
            'empty' => [
                'link' => route('request-log.empty'),
                'text' => Html::tag('i', '', ['class' => 'fa fa-trash'])->toHtml() . ' ' . trans('plugins/request-log::request-log.delete_all'),
            ],
          ];
        }
    }  /*  @customized Haritha Murugavel end */
    /**
     * {@inheritDoc}
     */
    public function bulkActions(): array
    {
        return $this->addDeleteAction(route('request-log.deletes'), 'request-log.destroy', parent::bulkActions());
    }
}

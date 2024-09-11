<?php

namespace Impiger\RequestLog\Tables;

use Illuminate\Support\Facades\Auth;
use Impiger\RequestLog\Repositories\Interfaces\RequestLogInterface;
use Impiger\Table\Abstracts\TableAbstract;
use Html;
use Illuminate\Contracts\Routing\UrlGenerator;
use Yajra\DataTables\DataTables;
/* @Customized By Ramesh Esakki  - Start -*/
use BaseHelper;
use Impiger\ACL\Models\User;
use DB;
/* @Customized By Ramesh Esakki  - End -*/

class RequestLogTable extends TableAbstract
{

    /**
     * @var bool
     */
    protected $hasActions = false; /* @Customized By Ramesh Esakki */

    /**
     * @var bool
     */
    protected $hasFilter = true; /* @Customized By Ramesh Esakki */

    /**
     * RequestLogTable constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlGenerator
     * @param RequestLogInterface $requestLogRepository
     */
    public function __construct(
        DataTables $table,
        UrlGenerator $urlGenerator,
        RequestLogInterface $requestLogRepository
    ) {
        parent::__construct($table, $urlGenerator);

        $this->repository = $requestLogRepository;

        if (!Auth::user()->hasPermission('request-log.destroy')) {
            $this->hasOperations = false;
            $this->hasActions = false;
        }
        if (Auth::user()->hasPermission('request-log.empty')) {
            $this->hasOperations = true;
            $this->hasActions = true;
        }
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
            /* @Customized By Ramesh Esakki  - Start -*/
            ->editColumn('user_id', function ($item) {
                $userNameLabel = '';
                if ($item->user_id) {
                    $userIds = User::select(DB::raw("CONCAT(first_name,' ',last_name) AS user_full_name"))->whereIn('id', $item->user_id)->pluck('user_full_name');
                    if ($userIds) {
                        $userNameLabel = implode(",", $userIds->toArray());
                    }
                }

                return $userNameLabel;
            })
            ->editColumn('updated_at', function ($item) {
                return BaseHelper::formatTime($item->updated_at);
            })
            /* @Customized By Ramesh Esakki  - End -*/
            ->editColumn('url', function ($item) {
                return Html::link($item->url, $item->url, ['target' => '_blank'])->toHtml();
            })
            ->addColumn('operations', function ($item) {
                return $this->getOperations(null, 'request-log.destroy', $item);
            });

        return $this->toJson($data);
    }

    /**
     * {@inheritDoc}
     */
    public function query()
    {
        $model = $this->repository->getModel();
        $select = [
            'request_logs.id',
            'request_logs.url',
            'request_logs.user_id', /* @Customized By Ramesh Esakki */
            'request_logs.status_code',
            'request_logs.count',
            'request_logs.updated_at', /* @Customized By Ramesh Esakki */
        ];

        $query = $model->select($select);
		/* @Customized By Ramesh Esakki  - Start -*/
        $arg1 = \Request::input('arg1');
        $arg2 = \Request::input('arg2');

        if ($arg1) {
            $query->whereRaw('DATE(updated_at) ="' . $arg1 . '"');
        }

        if ($arg2) {
            $query->where(array('status_code' => $arg2));
        }
    	/* @Customized By Ramesh Esakki  - End -*/

        return $this->applyScopes(apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, $select));
    }

    /**
     * {@inheritDoc}
     */
    public function columns()
    {
        return [
            'id'          => [
                'name'  => 'request_logs.id',
                'title' => trans('core/base::tables.id'),
                'width' => '20px',
            ],
            'url'         => [
                'name'  => 'request_logs.url',
                'title' => trans('core/base::tables.url'),
                'class' => 'text-left',
            ],
            /* @Customized By Ramesh Esakki  - Start -*/
            'user_id'     => [
                'name'  => 'request_logs.user_id',
                'title' => trans('plugins/request-log::request-log.user'),
                'class' => 'text-left',
            ],
            /* @Customized By Ramesh Esakki  - End -*/
            'status_code' => [
                'name'  => 'request_logs.status_code',
                'title' => trans('plugins/request-log::request-log.status_code'),
            ],
            'count'       => [
                'name'  => 'request_logs.count',
                'title' => trans('plugins/request-log::request-log.count'),
            ],
            /* @Customized By Ramesh Esakki  - Start -*/
            'updated_at' => [
                'name'  => 'request_logs.updated_at',
                'title' => trans('plugins/request-log::request-log.last_date'),
                'class' => 'text-left',
            ]
        	/* @Customized By Ramesh Esakki  - End -*/
        ];
    }

    /**
     * {@inheritDoc}
     */
   
    public function buttons()
    {/*  @customized Haritha Murugavel start */
        if (!$this->hasActions) {
            return [];
        }
else if (Auth::user()->hasPermission('request-log.empty')) {
    $this->hasOperations = true;
    $this->hasActions = true;

        return [
            'empty' => [
                'link' => route('request-log.empty'),
                'text' => Html::tag('i', '', ['class' => 'fa fa-trash'])->toHtml() . ' ' . trans('plugins/request-log::request-log.delete_all'),
            ],
          ];
        }
    }/*  @customized Haritha Murugavel end */

    /**
     * {@inheritDoc}
     */
    public function bulkActions(): array
    {
        return $this->addDeleteAction(route('request-log.deletes'), 'request-log.destroy', parent::bulkActions());
    }
	
	/* @Customized By Ramesh Esakki  - Start -*/
    /**
     * @return array
     */
    public function getFilters(): array
    {
        return [
            'request_logs.status_code'       => [
                'title'    => trans('plugins/request-log::request-log.status_code'),
                'type'     => 'select-search',
                'validate' => 'required',
                'callback' => 'getStatusCode',
            ],
            'request_logs.user_id'     => [
                'title'    => trans('plugins/request-log::request-log.user'),
                'type'     => 'select-search',
                'validate' => 'required',
                'callback' => 'getUserList',
            ],
            'request_logs.updated_at' => [
                'title' => trans('plugins/request-log::request-log.last_date'),
                'type'  => 'date',
            ],
        ];
    }

    /**
     * @return array
     */
    public function getUserList(): array
    {
        return User::select(DB::raw('CONCAT(COALESCE(first_name,"")," ",COALESCE(last_name,"")) AS user_full_name'))->whereNotIn('username', ['impiger'])->orderBy('user_full_name')->pluck("user_full_name", "id")->toArray();
    }

    /**
     * @return array
     */
    public function getStatusCode(): array
    {
        return RequestLog::orderBy('status_code', 'ASC')->pluck('request_logs.status_code', 'request_logs.status_code')->toArray();
    }
	/* @Customized By Ramesh Esakki  - End -*/
}

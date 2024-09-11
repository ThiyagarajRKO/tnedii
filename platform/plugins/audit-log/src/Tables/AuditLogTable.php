<?php

namespace Impiger\AuditLog\Tables;

use Illuminate\Support\Facades\Auth;
use Impiger\AuditLog\Repositories\Interfaces\AuditLogInterface;
use Impiger\Table\Abstracts\TableAbstract;
use Html;
use Illuminate\Contracts\Routing\UrlGenerator;
use Yajra\DataTables\DataTables;
/* @Customized By Ramesh Esakki  - Start -*/
use Illuminate\Http\Request;
use DB;
use BaseHelper;
use Impiger\ACL\Models\User;
/* @Customized By Ramesh Esakki  - End -*/

class AuditLogTable extends TableAbstract
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
     * AuditLogTable constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlGenerator
     * @param AuditLogInterface $auditLogRepository
     */
    public function __construct(DataTables $table, UrlGenerator $urlGenerator, AuditLogInterface $auditLogRepository)
    {
        parent::__construct($table, $urlGenerator);

        $this->repository = $auditLogRepository;

        if (!Auth::user()->hasPermission('audit-log.destroy')) {
            $this->hasOperations = false;
            $this->hasActions = false;
        }
        if (Auth::user()->hasPermission('audit-log.empty')) {
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
            ->editColumn('user_id', function ($history) {
                return ($history->user->id) ? '<a href="' . route('users.profile.view', $history->user->id) . '" class="d-inline-block">' . $history->user->getFullName() . '</a>' : '<span class="d-inline-block">' . trans('plugins/audit-log::history.system') . '</span>';
            })
            ->editColumn('created_at', function ($item) {
                return BaseHelper::formatTime($item->created_at);
            })
            /* @Customized By Ramesh Esakki  - End -*/
            ->editColumn('action', function ($history) {
                return view('plugins/audit-log::custom-action', compact('history'))->render(); /* @Customized By Ramesh Esakki */
            })
            ->addColumn('operations', function ($item) {
                return $this->getOperations(null, 'audit-log.destroy', $item);
            });
            
            return $this->toJson($data);
    }

    /**
     * {@inheritDoc}
     */
    public function query()
    {
        $model = $this->repository->getModel();
        $select = ['audit_histories.*'];
        $query = $model
            ->with(['user'])
            ->select($select);
		/* @Customized By Ramesh Esakki  - Start -*/
        $arg1 = \Request::input('arg1');
        $arg2 = \Request::input('arg2');

        if ($arg1) {
            $query->whereRaw('DATE(created_at) ="' . $arg1 . '"');
        }

        if ($arg2) {
            $query->where(array('type' => $arg2));
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
            'id'         => [
                'name'  => 'audit_histories.id',
                'title' => trans('core/base::tables.id'),
                'width' => '20px',
            ],
            /* @Customized By Ramesh Esakki  - Start -*/
            'type' => [
                'name'  => 'audit_histories.type',
                'title' => trans('plugins/audit-log::history.type'),
                'class' => 'text-left',
            ],
            'module'     => [
                'name'  => 'audit_histories.module',
                'title' => trans('plugins/audit-log::history.module_name'),
                'class' => 'text-left',
            ],
            'user_id'     => [
                'name'  => 'audit_histories.user_id',
                'title' => trans('plugins/audit-log::history.user'),
                'class' => 'text-left',
            ],
            /* @Customized By Ramesh Esakki  - End -*/
            'action'     => [
                'name'  => 'audit_histories.action',
                'title' => trans('plugins/audit-log::history.action'),
                'class' => 'text-left',
            ],
            /* @Customized By Ramesh Esakki  - Start -*/
            'created_at' => [
                'name'  => 'audit_histories.created_at',
                'title' => trans('core/base::tables.created_at'),
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
       else  if (Auth::user()->hasPermission('audit-log.empty')) {
            $this->hasOperations = true;
            $this->hasActions = true;
        return[ 'empty' => [
                        'link' => route('audit-log.empty'),
                        'text' => Html::tag('i', '', ['class' => 'fa fa-trash'])->toHtml() . ' ' . trans('plugins/audit-log::history.delete_all'),
                    ],
        ];
    }
    }/*  @customized Haritha Murugavel end */

    /**
     * {@inheritDoc}
     */
    public function bulkActions(): array
    {
        return $this->addDeleteAction(route('audit-log.deletes'), 'audit-log.destroy', parent::bulkActions());
    }

	/* @Customized By Ramesh Esakki  - Start -*/
    /**
     * @return array
     */
    public function getFilters(): array
    {
        return [
            'audit_histories.module'       => [
                'title'    => trans('plugins/audit-log::history.module_name'),
                'type'     => 'select-search',
                'validate' => 'required',
                'callback' => 'getAuditLogModule',
            ],
            'audit_histories.type'     => [
                'title'    => trans('plugins/audit-log::history.type'),
                'type'     => 'select-search',
                'validate' => 'required',
                'callback' => 'getAuditLogType',
            ],
            'audit_histories.user_id'     => [
                'title'    => trans('plugins/audit-log::history.user'),
                'type'     => 'select-search',
                'validate' => 'required',
                'callback' => 'getUserList',
            ],
            'audit_histories.created_at' => [
                'title' => trans('core/base::tables.created_at'),
                'type'  => 'date',
            ],
        ];
    }

    /**
     * @return array
     */
    public function getAuditLogType(): array
    {
        return $this->repository->pluck('audit_histories.type', 'audit_histories.type');
    }

    /**
     * @return array
     */
    public function getAuditLogModule(): array
    {
        return $this->repository->pluck('audit_histories.module', 'audit_histories.module');
    }

    /**
     * @return array
     */
    public function getUserList(): array
    {
        return User::select(DB::raw('CONCAT(COALESCE(first_name,"")," ",COALESCE(last_name,"")) AS user_full_name'), "id")->whereNotIn('username', ['impiger'])->pluck("user_full_name", "id")->toArray();
    }
    

	/* @Customized By Ramesh Esakki  - End -*/
}

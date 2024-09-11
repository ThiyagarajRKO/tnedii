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
use App\Utils\CrudHelper;
/* @Customized By Ramesh Esakki  - End -*/

class AuditLogNotificationTable extends TableAbstract
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
        $this->notificationHistory();
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
                return ($history->user->id) ?  $history->user->getFullName() : '<span class="d-inline-block">' . trans('plugins/audit-log::history.system') . '</span>';
            })
            ->filterColumn('user_id', function ($query, $keyword) {
                $sql = DB::raw('CONCAT_WS(" ",users.first_name,users.last_name)') . ' like ?';
                return $query->whereRaw($sql, ["%{$keyword}%"]);
            })
            ->editColumn('created_at', function ($item) {
                return BaseHelper::formatTime($item->created_at);
            })
            /* @Customized By Ramesh Esakki  - End -*/
            ->editColumn('action', function ($history) {
                return view('plugins/audit-log::custom-action', compact('history'))->render(); /* @Customized By Ramesh Esakki */
            })
            ->editColumn('reference_id', function ($item) {
                return \App\Utils\CrudHelper::formatEntityValue($item->crud_id, $item->reference_id); 
            })
            ->editColumn('status', function ($item) {
                if($item->status){
                    return Html::tag('span', 'Read', ['class' => 'label-success status-label'])
                    ->toHtml();
                }else{
                    return Html::tag('span', 'UnRead', ['class' => 'label-warning status-label'])
                    ->toHtml();
                }
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
	$date = \Carbon\Carbon::now()->subDays(NOTIFICATION_DURATION_DAYS);
        $select[] = \DB::raw('(select id from cruds where cruds.module_name =audit_histories.module) as crud_id');
        $select[] = \DB::raw('CASE when (select id from notification_history WHERE user_id = '.\Auth::id().'  and audit_histories_id = audit_histories.id and reference_id = audit_histories.reference_id) THEN 1 ELSE 0 END as status');
        $query = $model
                ->join('users','users.id','=','audit_histories.user_id')
                ->whereNotIn('audit_histories.module',NOTIFICATION_EXCLUDE_MODULES)
                ->where('audit_histories.created_at', '>=', $date->format('Y-m-d H:m:s'))
                ->whereRaw('(audit_histories.action = "created" OR audit_histories.type ="workflow")')
                //->whereRaw('not exists(select audit_histories_id from notification_history  WHERE user_id = '.\Auth::id().'  and audit_histories_id = audit_histories.id and reference_id = audit_histories.reference_id)')
                ->select($select)
//                ->orderBy('audit_histories.id','desc')
                ;	
        $query = apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, $select);
        if(isVendorUser()){
            $query=$query->whereNotIn('audit_histories.action',['drafted','updated']);
        }
        return $this->applyScopes($query);
    }

    /**
     * {@inheritDoc}
     */
    public function columns()
    {
        return [
          'module'     => [
                'name'  => 'audit_histories.module',
                'title' => trans('plugins/audit-log::history.module_name'),
                'class' => 'text-left',
            ],
          'reference_id'     => [
                'name'  => 'audit_histories.reference_id',
                'title' => trans('core/base::tables.name'),
                'class' => 'text-left',
                'searchable'=>false,
            ],
            /* @Customized By Ramesh Esakki  - Start -*/
            'type' => [
                'name'  => 'audit_histories.type',
                'title' => trans('plugins/audit-log::history.type'),
                'class' => 'text-left',
            ],
            
            'user_id'     => [
                'name'  => 'user_id',
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
            ],
            'status'    => [
                'name'  => 'status',
                'title' => trans('core/base::tables.status'),
                'width' => '100px',
                'searchable'=>false,
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
            'status' => [
                'name' => 'status',
                'title' => trans('core/base::tables.status'),
                'type'  => 'select-search',
                'choices' => [1=>'Read',0=>'UnRead']
            ],
        ];
    }
    
    public function applyFilterCondition($query, string $key, string $operator, ?string $value) {
        switch ($key) {
            case 'status':
                return $query->havingRaw('status =' . $value);
        }
        return CrudHelper::applyFilterCondition($this->repository, $query, $key, $operator, $value);
    }
    /**
     * @return array
     */
    public function getAuditLogType(): array
    {
        return ['workflow'=>'workflow','info'=>'info'];
    }

    /**
     * @return array
     */
    public function getAuditLogModule(): array
    {
        $modules = $this->repository->pluck('audit_histories.module', 'audit_histories.module');
        \Arr::forget($modules,NOTIFICATION_EXCLUDE_MODULES);
        return $modules;
    }

    /**
     * @return array
     */
    public function getUserList(): array
    {
        return User::select(DB::raw('CONCAT(COALESCE(first_name,"")," ",COALESCE(last_name,"")) AS user_full_name'), "id")->whereNotIn('username', ['impiger'])->pluck("user_full_name", "id")->toArray();
    }
    
    public function notificationHistory() {
        if ($this->request->input('arg1')) {
            $history = [
                'audit_histories_id' => $this->request->input('arg1'),                
                'user_id' => \Auth::id(),
            ];

            if ($this->request->input('arg2')) {
                $history['reference_id'] = $this->request->input('arg2');
            }
            $exists =  \DB::table('notification_history')->where($history)->orderBy('id','desc')->first();
            $history['created_at'] = date('Y-m-d H:m:s');
            if(empty($exists)){
                \DB::table('notification_history')->insert($history);
            }            
        }
    }

	/* @Customized By Ramesh Esakki  - End -*/
}

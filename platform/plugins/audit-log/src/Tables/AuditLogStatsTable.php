<?php

namespace Impiger\AuditLog\Tables;

use Impiger\AuditLog\Models\AuditHistory;
use Illuminate\Support\Facades\Auth;
use Impiger\AuditLog\Repositories\Interfaces\AuditLogInterface;
use Impiger\Table\Abstracts\TableAbstract;
use Html;
use Illuminate\Contracts\Routing\UrlGenerator;
use Yajra\DataTables\DataTables;
use DB;

class AuditLogStatsTable extends TableAbstract
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
    protected $customColumns = array('info', 'danger','primary', 'workflow');

    /**
     * AuditLogStatsTable constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlGenerator
     * @param AuditLogInterface $auditLogRepository
     */
    public function __construct(DataTables $table, UrlGenerator $urlGenerator, AuditLogInterface $auditLogRepository)
    {
        $this->repository = $auditLogRepository;
        $this->setOption('id', 'table-audit-logs');
        parent::__construct($table, $urlGenerator);

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
            ->editColumn('created_date', function ($item) {
                return Html::link(route('audit-log.detail', 'arg1=' . $item->created_date), $item->created_date);
            })
            ->editColumn('action', function ($history) {
                return view('plugins/audit-log::activity-line', compact('history'))->render();
            });

        foreach ($this->customColumns as $column) {
            $dbField = $column."_cnt";
            $data->editColumn($dbField, function ($item) use ($dbField, $column) {
                return ($item->$dbField) ? Html::link(route('audit-log.detail', 'arg1=' . $item->created_date . '&arg2=' . $column), $item->$dbField) : $item->$dbField;
            });
        }

        return apply_filters(BASE_FILTER_GET_LIST_DATA, $data, $this->repository->getModel())
            ->addColumn('operations', function ($item) {
                return $this->getOperations(null, 'audit-log.destroy', $item);
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
        $select = ['audit_histories.id', 'audit_histories.module', 'audit_histories.created_at', DB::raw('DATE_FORMAT(created_at, "%Y-%m-%d") as created_date')];

        foreach ($this->customColumns as $column) {
            $dbField = $column."_cnt";
            $select[] = DB::raw('SUM(if(type = "' . $column . '", 1, 0)) AS ' . $dbField);
        }

        $query = $model
            ->with(['user'])
            ->groupBy(DB::raw('Date(created_at)'))
            ->select($select);

        $result = $this->applyScopes(apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, $select));
        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function columns()
    {
        $columns = [
            'created_date'     => [
                'name'  => 'audit_histories.created_at',
                'date' => 'created_date',
                'title' => 'Date',
                'class' => 'text-left'
            ]
        ];

        foreach ($this->customColumns as $column) {
            $columnData = [];
            $field = $column."_cnt";
            $columnData['name'] = $columnData['data'] = $field;
            $columnData['title'] = $column;
            $columnData['class'] = 'text-left';
            $columnData['searchable'] = false;
            $columns[$column] = [];
            $columns[$column] = $columnData;
        }

        return $columns;
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

    public function getModuleSlug($key)
    {
        if ($key) {
            return $field = str_replace(" ", "_", $key) . '_cnt';
        }

        return false;
    }
}

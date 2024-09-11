<?php

namespace Impiger\Reports\Tables;

use Illuminate\Support\Facades\Auth;
use BaseHelper;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Reports\Repositories\Interfaces\ReportsInterface;
use Impiger\Table\Abstracts\TableAbstract;
use Illuminate\Contracts\Routing\UrlGenerator;
use Yajra\DataTables\DataTables;
use Html;
use Impiger\Reports\Http\Controllers\ReportsController;
use DB;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;

class ReportsTable extends TableAbstract
{

    /**
     * @var bool
     */
    protected $hasActions = true;

    /**
     * @var bool
     */
    protected $hasFilter = false;
    
    protected  $config = [];

    /**
     * ReportsTable constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlGenerator
     * @param ReportsInterface $reportsRepository
     */
    public function __construct(DataTables $table, UrlGenerator $urlGenerator, ReportsInterface $reportsRepository)
    {
        parent::__construct($table, $urlGenerator);

        $this->repository = $reportsRepository;

        if (!Auth::user()->hasAnyPermission(['reports.edit', 'reports.destroy'])) {
            $this->hasOperations = false;
            $this->hasActions = false;
        }
        $this->config = ReportsController::getReportConfigs('entrepreneur','district-wise-beneficiries');
        
    }

    /**
     * {@inheritDoc}
     */
    public function ajax()
    {
        $data = $this->table
            ->eloquent($this->query())
            ->editColumn('name', function ($item) {
                if (!Auth::user()->hasPermission('reports.edit')) {
                    return $item->name;
                }
                return Html::link(route('reports.edit', $item->id), $item->name);
            })
            ->editColumn('checkbox', function ($item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('created_at', function ($item) {
                return BaseHelper::formatDate($item->created_at);
            })
            ->editColumn('status', function ($item) {
                return $item->status->toHtml();
            })
            ->addColumn('operations', function ($item) {
                return $this->getOperations('reports.edit', 'reports.destroy', $item);
            });

        return $this->toJson($data);
    }

    /**
     * {@inheritDoc}
     */
    public function query()
    {
        $model = new $this->config['model'];        
        $select = $this->getConvertedQuery($this->config['query'],true);
        $query = $this->getConvertedQuery($this->config['query']);
        $query = $model->select($select)->join('district AS D','D.id','=','E.district_id')->join('trainees AS T','T.entrepreneur_id','=','E.id')->join('training_title AS TT','TT.id','=','T.training_title_id')->whereNull('E.deleted_AT')->groupBy('D.id');
//        dd($query);
        return $this->applyScopes(apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, $select));
    }

    /**
     * {@inheritDoc}
     */
    public function columns()
    {
        $columns  = [explode("|", $this->config['columns'])];        
        return  $columns;
    }

    /**
     * {@inheritDoc}
     */
    public function buttons()
    {
        return [];
    }

    /**
     * {@inheritDoc}
     */
    public function bulkActions(): array
    {
        return $this->addDeleteAction(route('reports.deletes'), 'reports.destroy', parent::bulkActions());
    }

    /**
     * {@inheritDoc}
     */
    public function getBulkChanges(): array
    {
        return [
            'reports.name' => [
                'title'    => trans('core/base::tables.name'),
                'type'     => 'text',
                'validate' => 'required|max:120',
            ],
            'reports.status' => [
                'title'    => trans('core/base::tables.status'),
                'type'     => 'select',
                'choices'  => BaseStatusEnum::labels(),
                'validate' => 'required|in:' . implode(',', BaseStatusEnum::values()),
            ],
            'reports.created_at' => [
                'title' => trans('core/base::tables.created_at'),
                'type'  => 'date',
            ],
        ];
    }

    /**
     * @return array
     */
    public function getFilters(): array
    {
        return $this->getBulkChanges();
    }
    
    public function getConvertedQuery($sqlQuery,$getColumns = false){
        $query = $columnStr="";
         $options = array('facade' => 'DB::');
                $converter = new \RexShijaku\SQLToLaravelBuilder\SQLToLaravelBuilder($options);
                $moduleQuery =  $converter->convert($sqlQuery);

                $qryArray = explode("->", $moduleQuery);
                if (Arr::has($qryArray, 0)) {
                    $qryLen = count($qryArray);
                    unset($qryArray[0]);
                    unset($qryArray[$qryLen - 1]);
                    $moduleQuery = implode("->", $qryArray);
                }
                $qryArray = explode("->", $moduleQuery);

                if (Arr::has($qryArray, 0)) {
                    $columnStr = str_replace("select(", "", $qryArray[0]);
                    if ($columnStr) {
                        $columnStr = substr($columnStr, 0, -1);
                        $gridColumns = stripslashes($columnStr);
                        unset($qryArray[0]);
                        $moduleQry =  implode("->", $qryArray);
                        $moduleQry = str_replace("\n", "\r\n\t\t\t", $moduleQry);
                    }
                }
                if($getColumns){
                    return explode(",", $columnStr);
                }
                $query.="\$model->select(\$select)->".$moduleQry;
                return $query;
    }
}

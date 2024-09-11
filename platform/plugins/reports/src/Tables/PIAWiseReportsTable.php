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

class PIAWiseReportsTable extends TableAbstract {

    /**
     * @var bool
     */
    protected $hasActions = true;

    /**
     * @var bool
     */
    protected $hasFilter = true;
    protected $config = [];
    protected $printPreview = 'base.print';
    
    /**
     * ReportsTable constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlGenerator
     * @param ReportsInterface $reportsRepository
     */
    public function __construct(DataTables $table, UrlGenerator $urlGenerator, ReportsInterface $reportsRepository) {
        parent::__construct($table, $urlGenerator);

        $this->repository = $reportsRepository;
        $this->hasOperations = false;

        $this->config = ReportsController::getReportConfigs('entrepreneur', 'district-wise-beneficiries');
    }

    /**
     * {@inheritDoc}
     */
    public function ajax() {
        $data = $this->table
                ->eloquent($this->query())
                ->editColumn('name', function ($item) {
                    return $item->name;
                })
                ->editColumn('checkbox', function ($item) {
                    return $this->getCheckbox($item->id);
                })
                ->editColumn('created_at', function ($item) {
                    return BaseHelper::formatDate($item->created_at);
                })
                ->addColumn('operations', function ($item) {
            return $this->getOperations('reports.edit', 'reports.destroy', $item);
        })
        ;

        return $this->toJson($data);
    }

    /**
     * {@inheritDoc}
     */
    public function query() {
        $model = new $this->config['model'];
        $select = [
            DB::raw('count(entrepreneurs.id) as no_of_candidate'),
            'TT.name as program_name',
            'V.company_name as pia'
        ];
        $query = $model->select($select)->join('trainees AS T', 'T.entrepreneur_id', '=', 'entrepreneurs.id')->join('training_title AS TT', 'TT.id', '=', 'T.training_title_id')->join('vendors AS V', 'V.id', '=', 'TT.vendor_id')
                                ->whereNull(['entrepreneurs.deleted_AT','D.deleted_at','T.deleted_at','TT.deleted_at','V.deleted_at'])->groupBy('TT.id');

        return $this->applyScopes(apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, $select));
    }

    /**
     * {@inheritDoc}
     */
    public function columns() {
        return [
            'pia' => [
                'name' => 'pia',
                'title' => 'Name of the PIA',
                'width' => '100',
                'class' => 'text-left'
            ],
            'program_name' => [
                'name' => 'program_name',
                'title' => 'Program Name',
                'width' => '100',
                'class' => 'text-left'
            ],
            'no_of_candidate' => [
                'name' => 'no_of_candidate',
                'title' => 'No Of the Candidate',
                'width' => '100',
                'class' => 'text-left'
        ]];
    }
    
    public function getDefaultButtons(): array
    {
        $defaultBtns = parent::getDefaultButtons();

        if (!$this->hasActions) {
            return $defaultBtns;
        }

        if (Auth::user()) {
            $defaultBtns = array_merge($defaultBtns, ['export','print']);
        }      

        return $defaultBtns;
    }
    
    /**
     * {@inheritDoc}
     */
    public function buttons() {
        return [];
    }

   
    /**
     * @return array
     */
    public function getFilters(): array {
       $filters= ReportsController::getFilters();
        Arr::forget($filters, ['community','district','gender','religion','candidate_type']);
        return $filters;
    }
    /**
     * @param Builder $query
     * @param string $key
     * @param string $operator
     * @param string $value
     * @return Builder
     */
    public function applyFilterCondition($query, string $key, string $operator, ?string $value)
    {
        return ReportsController::applyFilterCondition($this->repository, $query,  $key,  $operator, $value);
    }

}

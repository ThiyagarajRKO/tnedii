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

class ReligionWiseReportsTable extends TableAbstract {

    /**
     * @var bool
     */
    protected $hasActions = true;

    /**
     * @var bool
     */
    protected $hasFilter = true;
    protected $config = [];
    protected $community =[];
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
        $this->religion = \Impiger\MasterDetail\Models\MasterDetail::where(['attribute' => 'religion'])->get();
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
            'TT.name as program_name'
        ];
        foreach($this->religion as $key =>  $row){
//            $select[] = DB::Raw('(SELECT count(E_'.$key.'.religion_id) from entrepreneurs E_'.$key.' JOIN trainees AS T ON T.entrepreneur_id = E_'.$key.'.id  where  E_'.$key.'.religion_id = '.$row->id.') as `'.$row->slug.'`');
            $select[] = DB::Raw('(SUM( CASE WHEN entrepreneurs.religion_id = '.$row->id.' THEN 1 ELSE 0 END )) as `'.$row->slug.'`');
        }

        $query = $model->select($select)->join('trainees AS T', 'T.entrepreneur_id', '=', 'entrepreneurs.id')->join('training_title AS TT', 'TT.id', '=', 'T.training_title_id')->whereNull('entrepreneurs.deleted_AT')
                
                ->groupBy('TT.id')->whereNull(['entrepreneurs.deleted_at','T.deleted_at','TT.deleted_at']);

        return $this->applyScopes(apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, $select));
    }

    /**
     * {@inheritDoc}
     */
    public function columns() {
        
        $columns = [
            'program_name' => [
                'name' => 'program_name',
                'title' => 'program Name',
                'width' => '100',
                'class' => 'text-left'
            ],
            'no_of_candidate' => [
                'name' => 'no_of_candidate',
                'title' => 'No Of the Candidate',
                'width' => '100',
                'class' => 'text-left'
        ],
            
            ];
        
        foreach($this->religion as $row){
            $columns[$row->slug] =[
                'name' => $row->slug,
                'title' => $row->name,
                'width' => '30',
                'class' => 'text-left'
            ];
        }  
        return $columns;
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
        Arr::forget($filters, ['community','district','gender','candidate_type']);
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
     /**
     * @return array
     */
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


}

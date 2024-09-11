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
use App\Utils\CrudHelper;

class DistrictWiseDetailsReportsTable extends TableAbstract {

    /**
     * @var bool
     */
    protected $hasActions = true;

    /**
     * @var bool
     */
    protected $hasFilter = true;
    protected $config = [];
    protected $view = "core/table::table";
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
                ->editColumn('gender_id', function ($item) {
                    return CrudHelper::formatRows($item->gender_id, 'database', 'attribute_options|id|name', $item, '');
                    ;
                })
                ->editColumn('community', function ($item) {
                    return CrudHelper::formatRows($item->community, 'database', 'attribute_options|id|name', $item, '');
                    ;
                })
                ->editColumn('religion_id', function ($item) {
                    return CrudHelper::formatRows($item->religion_id, 'database', 'attribute_options|id|name', $item, '');
                    ;
                })
                ->editColumn('candidate_type_id', function ($item) {
                    return CrudHelper::formatRows($item->candidate_type_id, 'database', 'attribute_options|id|name', $item, '');
                    ;
                })
                ->editColumn('physically_challenged', function ($item) {
                    return CrudHelper::formatRows($item->physically_challenged, 'radio', '1:Yes,0:No,:No', $item, '');
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
            'entrepreneurs.id as id',
            'D.name as district_name',
            'entrepreneurs.name',
            'entrepreneurs.father_name',
            'entrepreneurs.gender_id',
            'entrepreneurs.community',
            'entrepreneurs.religion_id',
            'entrepreneurs.physically_challenged',
            'entrepreneurs.created_at',
            'entrepreneurs.candidate_type_id',
            'TT.name as program_name'
        ];
        $query = $model->select($select)->join('district AS D', 'D.id', '=', 'entrepreneurs.district_id')->join('trainees AS T', 'T.entrepreneur_id', '=', 'entrepreneurs.id')->join('training_title AS TT', 'TT.id', '=', 'T.training_title_id')
                        ->whereNull(['entrepreneurs.deleted_AT','D.deleted_at','T.deleted_at','TT.deleted_at']);

        return $this->applyScopes(apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, $select));
    }

    /**
     * {@inheritDoc}
     */
    public function columns() {
//        $columns  = [explode("|", $this->config['columns'])];   dd($columns);     
//        return  $columns;
        $columns =  [
            'district_name' => [
                'name' => 'district_name',
                'title' => 'District Name',
                'width' => '70',
                'class' => 'text-left'
            ],
            'name' => [
                'name' => 'name',
                'title' => 'Candidate name',
                'width' => '100',
                'class' => 'text-left'
            ],
            'father_name' => [
                'name' => 'father_name',
                'title' => 'Father name',
                'width' => '100',
                'class' => 'text-left'
            ],
            'gender_id' => [
                'name' => 'gender_id',
                'title' => 'Gender',
                'width' => '50',
                'class' => 'text-left'
            ],
            'program_name' => [
                'name' => 'program_name',
                'title' => 'program Name',
                'width' => '100',
                'class' => 'text-left'
            ],
            'created_at' => [
                'name' => 'created_at',
                'title' => 'Date',
                'width' => '50',
                'class' => 'text-left'
            ],
            'physically_challenged' => [
                'name' => 'physically_challenged',
                'title' => 'Disabled',
                'width' => '30',
                'class' => 'text-left'
            ],
            'community' => [
                'name' => 'community',
                'title' => 'Community',
                'width' => '100',
                'class' => 'text-left'
            ],
            'religion_id' => [
                'name' => 'religion_id',
                'title' => 'Religion',
                'width' => '100',
                'class' => 'text-left'
            ],
        ];
        $title = base64_decode(request()->get('title'));
        if(Str::contains($title,'Candidate')){
            $candidateColumn['candidate_type_id'] = [
                    'name' => 'candidate_type_id',
                    'title' => 'Candidate Type',
                    'width' => '100',
                    'class' => 'text-left'
            ];            
            $columns = arrayInsertAfter($columns, 'name', $candidateColumn);
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
        return ReportsController::getFilters();
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
    
    
    public function getConvertedQuery($sqlQuery, $getColumns = false) {
        $query = $columnStr = "";
        $options = array('facade' => 'DB::');
        $converter = new \RexShijaku\SQLToLaravelBuilder\SQLToLaravelBuilder($options);
        $moduleQuery = $converter->convert($sqlQuery);

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
                $moduleQry = implode("->", $qryArray);
                $moduleQry = str_replace("\n", "\r\n\t\t\t", $moduleQry);
            }
        }
        if ($getColumns) {
            return explode(",", $columnStr);
        }
        $query .= "\$model->select(\$select)->" . $moduleQry;
        return $query;
    }

}

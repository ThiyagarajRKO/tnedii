<?php

namespace Impiger\FinancialYear\Tables;

use Auth;
use BaseHelper;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\FinancialYear\Repositories\Interfaces\FinancialYearInterface;
use Impiger\Table\Abstracts\TableAbstract;
use Illuminate\Contracts\Routing\UrlGenerator;
use Yajra\DataTables\DataTables;
use Impiger\FinancialYear\Models\FinancialYear;
use Html;
use DB;
use Illuminate\Support\Arr;
use App\Utils\CrudHelper;


class FinancialYearTable extends TableAbstract
{

    /**
     * @var bool
     */
    protected $hasActions = true;

    /**
     * @var bool
     */
    protected $hasFilter = true;

    /**
     * @var string
     */
    protected $view = "core/table::table";
    protected $editPermissions = "financial-year.edit";
    protected $deletePermissions = "financial-year.destroy";
    /* @customized by Sabari Shankar.Parthiban*/
    protected $printPreview = 'base.print';
    /**
     * FinancialYearTable constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlGenerator
     * @param FinancialYearInterface $financialYearRepository
     */
    public function __construct(DataTables $table, UrlGenerator $urlGenerator, FinancialYearInterface $financialYearRepository)
    {
        $this->repository = $financialYearRepository;
        $this->setOption('id', 'plugins-financial-year-table');
        parent::__construct($table, $urlGenerator);

        
    }

    /**
     * {@inheritDoc}
     */
    public function ajax()
    {
        $data = $this->table
            ->eloquent($this->query())
            ->editColumn('name', function ($item) {
                $this->checkDefault($item);
                $isEdit =  (!empty($this->editPermissions));
                $isPublic =  $this->getOption('shortcode');

                
                return CrudHelper::getNameFieldLink($item, 'financial-year', $isEdit, $isPublic);
            })
            ->editColumn('checkbox', function ($item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('session_start', function($item) { 
				return CrudHelper::formatDate($item->session_start);
			})
			->editColumn('session_end', function($item) { 
				return CrudHelper::formatDate($item->session_end);
			})
			->editColumn('is_running', function($item) { 
				return CrudHelper::formatRows($item->is_running, 'radio', '1:Yes,0:No', $item, '');
			})
			->editColumn('is_enabled', function($item) { 
				return CrudHelper::formatRows($item->is_enabled, 'radio', '1:Active,0:In-Active', $item, '');
			})
            
            ->editColumn('created_at', function ($item) {
                return BaseHelper::formatDate($item->created_at);
            })
            ->addColumn('operations', function ($item) {
                $editPermissions = $this->editPermissions;$deletePermissions = $this->deletePermissions;
                $this->checkDefault($item);
                
                
                return $this->getOperations($this->editPermissions, $this->deletePermissions, $item, "<a  href='financial-years/viewdetail/$item->id'  class='btn btn-icon btn-sm btn-primary' data-toggle='tooltip' data-original-title='View'><i class='fa fa-eye'></i></a>".CrudHelper::getRowActivationActionBtn($item,'Impiger\FinancialYear\Models\FinancialYear', 'financial-year.enable_disable',''));
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
            'financial_year.id',
			'financial_year.session_year',
			'financial_year.session_start',
			'financial_year.session_end',
			'financial_year.description',
			'financial_year.is_running',
			'financial_year.is_enabled'
        ];

        $query = $model->select($select);

        return $this->applyScopes(apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, $select));
    }

    /**
     * {@inheritDoc}
     */
    public function columns()
    {
        return [
            'session_year' => [
			'name' => 'session_year',
			'title' => 'Financial Year',
			'width' => '100',
			'class' => 'text-left'
			],
			'session_start' => [
			'name' => 'session_start',
			'title' => 'Session Start',
			'width' => '100',
			'class' => 'text-left'
			],
			'session_end' => [
			'name' => 'session_end',
			'title' => 'Session End',
			'width' => '100',
			'class' => 'text-left'
			],
			'description' => [
			'name' => 'description',
			'title' => 'Description',
			'width' => '100',
			'class' => 'text-left'
			],
			'is_running' => [
			'name' => 'is_running',
			'title' => 'Is Running',
			'width' => '100',
			'class' => 'text-left'
			],
			'is_enabled' => [
			'name' => 'is_enabled',
			'title' => 'Status',
			'width' => '100',
			'class' => 'text-left'
			]
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function buttons()
    {
        if (!$this->hasActions) {
            return [];
        }

        $buttons = $this->addCreateButton(route('financial-year.create'), 'financial-year.create');
        
        
        return apply_filters(BASE_FILTER_TABLE_BUTTONS, $buttons, FinancialYear::class);
    }

    /**
     * {@inheritDoc}
     */
    public function bulkActions(): array
    {
        if (Auth::user() && !Auth::user()->hasPermission('financial-year.edit') ) {
                    return [];
                }
        return $this->addDeleteAction(route('financial-year.deletes'), 'financial-year.destroy', parent::bulkActions());
    }

    /**
     * {@inheritDoc}
     */
    public function getBulkChanges($isFilter = false): array
    {
        return CrudHelper::getBulkChanges('financial-year', $isFilter, );
    }

    /**
     * @return array
     */
    public function getFilters(): array
    {
        return $this->getBulkChanges(true);
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

        if (Auth::user() && Auth::user()->hasPermission('financial-year.export')) {
            $defaultBtns = array_merge($defaultBtns, ['export']);
        }

        if (Auth::user() && Auth::user()->hasPermission('financial-year.print')) {
            $defaultBtns = array_merge($defaultBtns, ['print']);
        }

        return $defaultBtns;
    }

    protected function checkDefault($item){
        if( isset($item->is_default) && $item->is_default){
            if(Auth::user() && (Auth::user()->is_admin || Auth::user()->isSuperUser())){
                $this->editPermissions = 'financial-year.edit';
            }else{
                $this->editPermissions = '';
            }
            $this->deletePermissions = '';
        }
    }

    public function setTableConfig($config): self
    {
        $this->hasActions = (isset($config->hasActions)) ? $config->hasActions : false;
        $this->hasOperations = (isset($config->hasOperations)) ? $config->hasOperations : false;
        $this->hasCheckbox = (isset($config->hasCheckbox)) ? $config->hasCheckbox : false;
        $this->pageLength = (isset($config->pageLength)) ? $config->pageLength : $this->pageLength;
        return $this;
    }

    

}

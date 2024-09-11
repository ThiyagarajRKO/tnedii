<?php

namespace Impiger\IncubationCenter\Tables;

use Auth;
use BaseHelper;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\IncubationCenter\Repositories\Interfaces\IncubationCenterInterface;
use Impiger\Table\Abstracts\TableAbstract;
use Illuminate\Contracts\Routing\UrlGenerator;
use Yajra\DataTables\DataTables;
use Impiger\IncubationCenter\Models\IncubationCenter;
use Html;
use DB;
use Illuminate\Support\Arr;
use App\Utils\CrudHelper;


class IncubationCenterTable extends TableAbstract
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
    protected $editPermissions = "incubation-center.edit";
    protected $deletePermissions = "incubation-center.destroy";
    /* @customized by Sabari Shankar.Parthiban*/
    protected $printPreview = 'base.print';
    /**
     * IncubationCenterTable constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlGenerator
     * @param IncubationCenterInterface $incubationCenterRepository
     */
    public function __construct(DataTables $table, UrlGenerator $urlGenerator, IncubationCenterInterface $incubationCenterRepository)
    {
        $this->repository = $incubationCenterRepository;
        $this->setOption('id', 'plugins-incubation-center-table');
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

                
                return CrudHelper::getNameFieldLink($item, 'incubation-center', $isEdit, $isPublic);
            })
            ->editColumn('checkbox', function ($item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('district_id', function($item) { 
				return CrudHelper::formatRows($item->district_id, 'database', 'district|id|name', $item, '');
			})
			->editColumn('establishment_date', function($item) { 
				return CrudHelper::formatDate($item->establishment_date);
			})
            
            ->editColumn('created_at', function ($item) {
                return BaseHelper::formatDate($item->created_at);
            })
            ->addColumn('operations', function ($item) {
                $editPermissions = $this->editPermissions;$deletePermissions = $this->deletePermissions;
                $this->checkDefault($item);
                
                
                return $this->getOperations($this->editPermissions, $this->deletePermissions, $item, "<a  href='incubation-centers/viewdetail/$item->id'  class='btn btn-icon btn-sm btn-primary' data-toggle='tooltip' data-original-title='View'><i class='fa fa-eye'></i></a>");
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
            'incubation_centers.*'
        ];

        $query = $model->select($select)->whereNotNull('incubation_centers.id');

        return $this->applyScopes(apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, $select));
    }

    /**
     * {@inheritDoc}
     */
    public function columns()
    {
        return [
            'district_id' => [
			'name' => 'district_id',
			'title' => 'District',
			'width' => '100',
			'class' => 'text-left'
			],
			'center_name' => [
			'name' => 'center_name',
			'title' => 'Center Name',
			'width' => '100',
			'class' => 'text-left'
			],
			'manager_name' => [
			'name' => 'manager_name',
			'title' => 'Manager Name',
			'width' => '100',
			'class' => 'text-left'
			],
			'establishment_date' => [
			'name' => 'establishment_date',
			'title' => 'Establishment Date',
			'width' => '100',
			'class' => 'text-left'
			],
			'no_of_incubatees' => [
			'name' => 'no_of_incubatees',
			'title' => 'No Of Incubatees',
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

        $buttons = $this->addCreateButton(route('incubation-center.create'), 'incubation-center.create');
        
        
        return apply_filters(BASE_FILTER_TABLE_BUTTONS, $buttons, IncubationCenter::class);
    }

    /**
     * {@inheritDoc}
     */
    public function bulkActions(): array
    {
        if (Auth::user() && !Auth::user()->hasPermission('incubation-center.edit') ) {
                    return [];
                }
        return $this->addDeleteAction(route('incubation-center.deletes'), 'incubation-center.destroy', parent::bulkActions());
    }

    /**
     * {@inheritDoc}
     */
    public function getBulkChanges($isFilter = false): array
    {
        return CrudHelper::getBulkChanges('incubation-center', $isFilter, );
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

        if (Auth::user() && Auth::user()->hasPermission('incubation-center.export')) {
            $defaultBtns = array_merge($defaultBtns, ['export']);
        }

        if (Auth::user() && Auth::user()->hasPermission('incubation-center.print')) {
            $defaultBtns = array_merge($defaultBtns, ['print']);
        }

        return $defaultBtns;
    }

    protected function checkDefault($item){
        if( isset($item->is_default) && $item->is_default){
            if(Auth::user() && (Auth::user()->is_admin || Auth::user()->isSuperUser())){
                $this->editPermissions = 'incubation-center.edit';
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

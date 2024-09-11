<?php

namespace Impiger\MasterDetail\Tables;

use Auth;
use BaseHelper;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\MasterDetail\Repositories\Interfaces\DistrictInterface;
use Impiger\Table\Abstracts\TableAbstract;
use Illuminate\Contracts\Routing\UrlGenerator;
use Yajra\DataTables\DataTables;
use Impiger\MasterDetail\Models\District;
use Html;
use DB;
use Illuminate\Support\Arr;
use App\Utils\CrudHelper;


class DistrictTable extends TableAbstract
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
    protected $editPermissions = "district.edit";
    protected $deletePermissions = "district.destroy";
    /* @customized by Sabari Shankar.Parthiban*/
    protected $printPreview = 'base.print';
    /**
     * DistrictTable constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlGenerator
     * @param DistrictInterface $districtRepository
     */
    public function __construct(DataTables $table, UrlGenerator $urlGenerator, DistrictInterface $districtRepository)
    {
        $this->repository = $districtRepository;
        $this->setOption('id', 'plugins-district-table');
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

                #{hideOnEditLink}
                return CrudHelper::getNameFieldLink($item, 'district', $isEdit, $isPublic);
            })
            ->editColumn('checkbox', function ($item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('country_id', function($item) { 
				return CrudHelper::formatRows($item->country_id, 'database', 'countries|id|country_name', $item, '');
			})
			->editColumn('region_id', function($item) { 
				return CrudHelper::formatRows($item->region_id, 'database', 'regions|id|name', $item, '');
			})
			->editColumn('created_at', function($item) { 
				return CrudHelper::formatDateTime($item->created_at);
			})
			->editColumn('is_enabled', function($item) { 
				return CrudHelper::formatRows($item->is_enabled, 'radio', '1:Active,0:In-Active,:Active', $item, '');
			})
            
            ->editColumn('created_at', function ($item) {
                return BaseHelper::formatDate($item->created_at);
            })
            ->addColumn('operations', function ($item) {
                $editPermissions = $this->editPermissions;$deletePermissions = $this->deletePermissions;
                $this->checkDefault($item);
                #{hideOnEditAction}
                #{hideOnDeleteAction}
                return $this->getOperations($this->editPermissions, $this->deletePermissions, $item, "<a data-fancybox data-type='ajax' data-src='districts/viewdetail/$item->id ' href='javascript:void(0);' class='btn btn-icon btn-sm btn-primary' data-toggle='tooltip' data-original-title='View'><i class='fa fa-eye'></i></a>".CrudHelper::getRowActivationActionBtn($item,'Impiger\MasterDetail\Models\District', 'district.enable_disable',''));
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
            'district.*'
        ];

        $query = $model->select($select)->whereNotNull('district.id');

        return $this->applyScopes(apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, $select));
    }

    /**
     * {@inheritDoc}
     */
    public function columns()
    {
        return [
            'name' => [
			'name' => 'name',
			'title' => 'District Name',
			'width' => '100',
			'class' => 'text-left'
			],
			'code' => [
			'name' => 'code',
			'title' => 'Code',
			'width' => '100',
			'class' => 'text-left'
			],
			'country_id' => [
			'name' => 'country_id',
			'title' => 'Country Name',
			'width' => '100',
			'class' => 'text-left'
			],
			'region_id' => [
			'name' => 'region_id',
			'title' => 'Region',
			'width' => '100',
			'class' => 'text-left'
			],
			'created_at' => [
			'name' => 'created_at',
			'title' => 'Created At',
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

        $buttons = $this->addCreateButton(route('district.create'), 'district.create');
        
        
        return apply_filters(BASE_FILTER_TABLE_BUTTONS, $buttons, District::class);
    }

    /**
     * {@inheritDoc}
     */
    public function bulkActions(): array
    {
        if (Auth::user() && !Auth::user()->hasPermission('district.edit') ) {
                    return [];
                }
        return $this->addDeleteAction(route('district.deletes'), 'district.destroy', parent::bulkActions());
    }

    /**
     * {@inheritDoc}
     */
    public function getBulkChanges($isFilter = false): array
    {
        return CrudHelper::getBulkChanges('district', $isFilter, );
    }

    /**
     * @return array
     */
    public function getFilters(): array
    {
        $filters = $this->getBulkChanges(true);
        
        $filters['district.region_id'] = [
            'title' => 'Region',
            'type' => 'select',
            'choices' => CrudHelper::getSelectBoxChoices(["option" => ['lookup_table' => 'regions', 'lookup_value' => 'name', 'lookup_key' => 'id']], 'external')
        ];
        return $filters;
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

        if (Auth::user() && Auth::user()->hasPermission('district.export')) {
            $defaultBtns = array_merge($defaultBtns, ['export']);
        }

        if (Auth::user() && Auth::user()->hasPermission('district.print')) {
            $defaultBtns = array_merge($defaultBtns, ['print']);
        }

        return $defaultBtns;
    }

    protected function checkDefault($item){
        if( isset($item->is_default) && $item->is_default){
            if(Auth::user() && (Auth::user()->is_admin || Auth::user()->isSuperUser())){
                $this->editPermissions = 'district.edit';
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

<?php

namespace Impiger\HubInstitution\Tables;

use Auth;
use BaseHelper;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\HubInstitution\Repositories\Interfaces\HubInstitutionInterface;
use Impiger\Table\Abstracts\TableAbstract;
use Illuminate\Contracts\Routing\UrlGenerator;
use Yajra\DataTables\DataTables;
use Impiger\HubInstitution\Models\HubInstitution;
use Html;
use DB;
use Illuminate\Support\Arr;
use App\Utils\CrudHelper;


class HubInstitutionTable extends TableAbstract
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
    protected $editPermissions = "hub-institution.edit";
    protected $deletePermissions = "hub-institution.destroy";
    /* @customized by Sabari Shankar.Parthiban*/
    protected $printPreview = 'base.print';
    /**
     * HubInstitutionTable constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlGenerator
     * @param HubInstitutionInterface $hubInstitutionRepository
     */
    public function __construct(DataTables $table, UrlGenerator $urlGenerator, HubInstitutionInterface $hubInstitutionRepository)
    {
        $this->repository = $hubInstitutionRepository;
        $this->setOption('id', 'plugins-hub-institution-table');
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

                
                return CrudHelper::getNameFieldLink($item, 'hub-institution', $isEdit, $isPublic);
            })
            ->editColumn('checkbox', function ($item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('hub_type_id', function($item) { 
				return CrudHelper::formatRows($item->hub_type_id, 'database', 'hub_types|id|hub_type', $item, '');
			})
			->editColumn('district', function($item) { 
				return CrudHelper::formatRows($item->district, 'database', 'district|id|code:name', $item, '');
			})
            
            ->editColumn('created_at', function ($item) {
                return BaseHelper::formatDate($item->created_at);
            })
            ->addColumn('operations', function ($item) {
                $editPermissions = $this->editPermissions;$deletePermissions = $this->deletePermissions;
                $this->checkDefault($item);
                
                
                return $this->getOperations($this->editPermissions, $this->deletePermissions, $item, "<a  href='hub-institutions/viewdetail/$item->id'  class='btn btn-icon btn-sm btn-primary' data-toggle='tooltip' data-original-title='View'><i class='fa fa-eye'></i></a>".CrudHelper::getRowActivationActionBtn($item,'Impiger\HubInstitution\Models\HubInstitution', 'hub-institution.enable_disable',''));
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
            'hub_institutions.id',
			'hub_institutions.hub_type_id',
			'hub_institutions.hub_code',
			'hub_institutions.name',
			'hub_institutions.pincode',
			'hub_institutions.district'
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
            'hub_type_id' => [
			'name' => 'hub_type_id',
			'title' => 'Hub Type',
			'width' => '100',
			'class' => 'text-left',
			'visible' => false
			],
			'hub_code' => [
			'name' => 'hub_code',
			'title' => 'Hub Code',
			'width' => '100',
			'class' => 'text-left'
			],
			'name' => [
			'name' => 'name',
			'title' => 'Name',
			'width' => '100',
			'class' => 'text-left'
			],
			'pincode' => [
			'name' => 'pincode',
			'title' => 'Pincode',
			'width' => '100',
			'class' => 'text-left'
			],
			'district' => [
			'name' => 'district',
			'title' => 'District',
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

        $buttons = $this->addCreateButton(route('hub-institution.create'), 'hub-institution.create');
        
        
        return apply_filters(BASE_FILTER_TABLE_BUTTONS, $buttons, HubInstitution::class);
    }

    /**
     * {@inheritDoc}
     */
    public function bulkActions(): array
    {
        if (Auth::user() && !Auth::user()->hasPermission('hub-institution.edit') ) {
                    return [];
                }
        return $this->addDeleteAction(route('hub-institution.deletes'), 'hub-institution.destroy', parent::bulkActions());
    }

    /**
     * {@inheritDoc}
     */
    public function getBulkChanges($isFilter = false): array
    {
        return CrudHelper::getBulkChanges('hub-institution', $isFilter, );
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

        if (Auth::user() && Auth::user()->hasPermission('hub-institution.export')) {
            $defaultBtns = array_merge($defaultBtns, ['export']);
        }

        if (Auth::user() && Auth::user()->hasPermission('hub-institution.print')) {
            $defaultBtns = array_merge($defaultBtns, ['print']);
        }

        return $defaultBtns;
    }

    protected function checkDefault($item){
        if( isset($item->is_default) && $item->is_default){
            if(Auth::user() && (Auth::user()->is_admin || Auth::user()->isSuperUser())){
                $this->editPermissions = 'hub-institution.edit';
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

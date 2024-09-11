<?php

namespace Impiger\Vendor\Tables;

use Auth;
use BaseHelper;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Vendor\Repositories\Interfaces\VendorInterface;
use Impiger\Table\Abstracts\TableAbstract;
use Illuminate\Contracts\Routing\UrlGenerator;
use Yajra\DataTables\DataTables;
use Impiger\Vendor\Models\Vendor;
use Html;
use DB;
use Illuminate\Support\Arr;
use App\Utils\CrudHelper;


class VendorTable extends TableAbstract
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
    protected $editPermissions = "vendor.edit";
    protected $deletePermissions = "vendor.destroy";
    /* @customized by Sabari Shankar.Parthiban*/
    protected $printPreview = 'base.print';
    /**
     * VendorTable constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlGenerator
     * @param VendorInterface $vendorRepository
     */
    public function __construct(DataTables $table, UrlGenerator $urlGenerator, VendorInterface $vendorRepository)
    {
        $this->repository = $vendorRepository;
        $this->setOption('id', 'plugins-vendor-table');
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

                
                return CrudHelper::getNameFieldLink($item, 'vendor', $isEdit, $isPublic);
            })
            ->editColumn('checkbox', function ($item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('pia_constitution_id', function($item) { 
				return CrudHelper::formatRows($item->pia_constitution_id, 'database', 'attribute_options|id|name', $item, '');
			})
			->editColumn('pia_mainactivity_id', function($item) { 
				return CrudHelper::formatRows($item->pia_mainactivity_id, 'database', 'attribute_options|id|name', $item, '');
			})
			->editColumn('district_id', function($item) { 
				return CrudHelper::formatRows($item->district_id, 'database', 'district|id|name', $item, '');
			})
            
            ->editColumn('created_at', function ($item) {
                return BaseHelper::formatDate($item->created_at);
            })
            ->addColumn('operations', function ($item) {
                $editPermissions = $this->editPermissions;$deletePermissions = $this->deletePermissions;
                $this->checkDefault($item);
                
                
                return $this->getOperations($this->editPermissions, $this->deletePermissions, $item, "<a  href='vendors/viewdetail/$item->id'  class='btn btn-icon btn-sm btn-primary' data-toggle='tooltip' data-original-title='View'><i class='fa fa-eye'></i></a>".apply_filters(ADD_CUSTOM_ACTION,'',$this->repository->getModel(),$item));
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
            'vendors.id',
			'vendors.user_id',
			'vendors.name',
			'vendors.pia_constitution_id',
			'vendors.pia_mainactivity_id',
			'vendors.address',
			'vendors.district_id',
			'vendors.pincode'
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
            'name' => [
			'name' => 'name',
			'title' => 'Name',
			'width' => '100',
			'class' => 'text-left'
			],
			'pia_constitution_id' => [
			'name' => 'pia_constitution_id',
			'title' => 'Pia Constitution',
			'width' => '100',
			'class' => 'text-left'
			],
			'pia_mainactivity_id' => [
			'name' => 'pia_mainactivity_id',
			'title' => 'Pia Main Activity',
			'width' => '100',
			'class' => 'text-left'
			],
			'address' => [
			'name' => 'address',
			'title' => 'Address',
			'width' => '100',
			'class' => 'text-left'
			],
			'district_id' => [
			'name' => 'district_id',
			'title' => 'District',
			'width' => '100',
			'class' => 'text-left'
			],
			'pincode' => [
			'name' => 'pincode',
			'title' => 'Pincode',
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

        $buttons = $this->addCreateButton(route('vendor.create'), 'vendor.create');
        
        
        return apply_filters(BASE_FILTER_TABLE_BUTTONS, $buttons, Vendor::class);
    }

    /**
     * {@inheritDoc}
     */
    public function bulkActions(): array
    {
        if (Auth::user() && !Auth::user()->hasPermission('vendor.edit') ) {
                    return [];
                }
        return $this->addDeleteAction(route('vendor.deletes'), 'vendor.destroy', parent::bulkActions());
    }

    /**
     * {@inheritDoc}
     */
    public function getBulkChanges($isFilter = false): array
    {
        return CrudHelper::getBulkChanges('vendor', $isFilter, );
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

        if (Auth::user() && Auth::user()->hasPermission('vendor.export')) {
            $defaultBtns = array_merge($defaultBtns, ['export']);
        }

        if (Auth::user() && Auth::user()->hasPermission('vendor.print')) {
            $defaultBtns = array_merge($defaultBtns, ['print']);
        }

        return $defaultBtns;
    }

    protected function checkDefault($item){
        if( isset($item->is_default) && $item->is_default){
            if(Auth::user() && (Auth::user()->is_admin || Auth::user()->isSuperUser())){
                $this->editPermissions = 'vendor.edit';
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
